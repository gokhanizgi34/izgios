<?php

namespace Tests\Feature;

use App\Http\Controllers\AracController;
use App\Models\AracQrKodu;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AracQrAtamaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('araclar', function (Blueprint $table) {
            $table->id();
            $table->uuid('qr_token')->nullable()->unique();
            $table->timestamp('qr_created_at')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_09_24_120000_create_arac_qr_havuzu_table.php'))->up();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('arac_qr_havuzu');
        Schema::dropIfExists('araclar');
        parent::tearDown();
    }

    private function label(array $attributes = []): AracQrKodu
    {
        return AracQrKodu::create(array_merge([
            'token' => (string) Str::uuid(), 'parti_kodu' => (string) Str::uuid(),
            'sira_no' => 1, 'durum' => 'bos',
        ], $attributes));
    }

    private function reserve(string $token): AracQrKodu
    {
        return (new \ReflectionMethod(AracController::class, 'bosQrKodunuKilitle'))
            ->invoke(new AracController, $token);
    }

    public function test_ikinci_arac_ayni_etiketi_kullanamaz(): void
    {
        $label = $this->label();
        DB::transaction(function () use ($label) {
            $reserved = $this->reserve($label->token);
            $id = DB::table('araclar')->insertGetId(['qr_token' => $reserved->token]);
            $reserved->update(['durum' => 'atanmis', 'arac_id' => $id]);
        });
        try {
            DB::transaction(fn () => $this->reserve($label->token));
            $this->fail('A used QR must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('qr_havuz_token', $exception->errors());
            $this->assertDatabaseCount('araclar', 1);
        }
    }

    public function test_havuz_bos_gorunse_de_aracta_kullanilan_token_reddedilir(): void
    {
        $label = $this->label();
        DB::table('araclar')->insert(['qr_token' => $label->token]);
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => $this->reserve($label->token));
    }

    public function test_iptal_edilen_etiket_tekrar_atanamaz(): void
    {
        $label = $this->label(['durum' => 'iptal']);
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => $this->reserve($label->token));
    }

    public function test_taninmayan_etiket_reddedilir(): void
    {
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => $this->reserve((string) Str::uuid()));
    }

    public function test_atama_islemi_basarisizsa_etiket_bos_kalir(): void
    {
        $label = $this->label();
        try {
            DB::transaction(function () use ($label) {
                $this->reserve($label->token)->update(['durum' => 'atanmis', 'arac_id' => 10]);
                throw new \RuntimeException('Simulated save failure');
            });
        } catch (\RuntimeException) {
            $this->assertDatabaseHas('arac_qr_havuzu', ['token' => $label->token, 'durum' => 'bos', 'arac_id' => null]);
        }
    }
}
