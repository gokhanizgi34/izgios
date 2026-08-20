<?php

namespace Tests\Feature;

use App\Models\Arac;
use App\Models\Firma;
use App\Models\Musteri;
use App\Models\Servis;
use App\Models\ServisIslem;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AracDijitalKimlikTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('firmas', function (Blueprint $table) {
            $table->id(); $table->string('unvan'); $table->timestamps();
        });
        Schema::create('subes', function (Blueprint $table) {
            $table->id(); $table->boolean('aktif')->default(true); $table->string('telefon')->nullable(); $table->string('whatsapp_no')->nullable(); $table->timestamps();
        });
        Schema::create('musteris', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('firma_id')->nullable(); $table->unsignedBigInteger('sube_id')->nullable(); $table->string('ad_soyad'); $table->string('telefon')->nullable(); $table->timestamps();
        });
        Schema::create('araclar', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('musteri_id'); $table->unsignedBigInteger('firma_id')->nullable(); $table->unsignedBigInteger('sube_id')->nullable(); $table->string('plaka'); $table->string('marka'); $table->string('model'); $table->string('model_yili')->nullable(); $table->integer('kilometre')->nullable(); $table->uuid('qr_token')->nullable(); $table->timestamp('qr_created_at')->nullable(); $table->timestamps();
        });
        Schema::create('servisler', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('musteri_id'); $table->unsignedBigInteger('arac_id'); $table->unsignedBigInteger('firma_id')->nullable(); $table->unsignedBigInteger('sube_id')->nullable(); $table->string('servis_no'); $table->dateTime('servis_tarihi')->nullable(); $table->integer('giris_km')->nullable(); $table->date('sonraki_bakim_tarihi')->nullable(); $table->timestamps();
        });
        Schema::create('servis_islemleri', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('servis_id'); $table->string('kategori')->default('servis'); $table->string('islem_adi'); $table->text('aciklama')->nullable(); $table->decimal('tutar')->default(0); $table->string('durum')->default('tamamlandi'); $table->timestamps();
        });
        Schema::create('servis_fotograflari', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('servis_id'); $table->timestamps();
        });
        Schema::create('servis_parcalar', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('servis_id'); $table->timestamps();
        });
        Schema::create('muhasebe_entegrasyonlari', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('firma_id'); $table->string('saglayici'); $table->boolean('aktif')->default(false); $table->string('durum')->default('yapilandirilmamis'); $table->text('ayarlar')->nullable(); $table->text('api_anahtari_sifreli')->nullable(); $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        foreach (['muhasebe_entegrasyonlari','servis_parcalar','servis_fotograflari','servis_islemleri','servisler','araclar','musteris','subes','firmas'] as $table) {
            Schema::dropIfExists($table);
        }
        parent::tearDown();
    }

    public function test_dijital_kimlik_giris_yapmadan_gorulebilir_ve_kayitlari_ayirir(): void
    {
        $firma = Firma::create(['unvan' => 'Test Servisi']);
        $musteri = Musteri::create([
            'firma_id' => $firma->id,
            'ad_soyad' => 'Ahmet Yılmaz',
            'telefon' => '05320000000',
        ]);
        $arac = Arac::create([
            'firma_id' => $firma->id,
            'musteri_id' => $musteri->id,
            'plaka' => '34 TEST 123',
            'marka' => 'Fiat',
            'model' => 'Egea',
            'model_yili' => 2024,
            'kilometre' => 20000,
        ]);
        $servis = Servis::create([
            'firma_id' => $firma->id,
            'musteri_id' => $musteri->id,
            'arac_id' => $arac->id,
            'servis_no' => 'SRV-TEST-1',
            'servis_tarihi' => now(),
            'giris_km' => 20000,
        ]);
        ServisIslem::create(['servis_id' => $servis->id, 'kategori' => 'servis', 'islem_adi' => 'Rot ayarı']);
        ServisIslem::create(['servis_id' => $servis->id, 'kategori' => 'servis', 'islem_adi' => 'Balans ayarı']);
        ServisIslem::create(['servis_id' => $servis->id, 'kategori' => 'periyodik_bakim', 'islem_adi' => 'Motor Yağı']);
        ServisIslem::create(['servis_id' => $servis->id, 'kategori' => 'periyodik_bakim', 'islem_adi' => 'Yağ Filtresi']);
        \DB::table('muhasebe_entegrasyonlari')->insert([
            'firma_id' => $firma->id,
            'saglayici' => 'whatsapp',
            'aktif' => true,
            'durum' => 'yapilandirildi',
            'ayarlar' => json_encode(['gonderen' => '905320000000']),
            'api_anahtari_sifreli' => Crypt::encryptString('test-token'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('araclar.qr.show', $arac->qr_token))
            ->assertOk()
            ->assertSee('Araç Dijital Kimliği')
            ->assertSee('Rot ayarı')
            ->assertSee('Balans ayarı')
            ->assertSee('2 işlem')
            ->assertDontSee('Motor Yağı');

        $this->get(route('araclar.qr.show', [$arac->qr_token, 'ekran' => 'bakim']))
            ->assertOk()
            ->assertSee('Motor Yağı')
            ->assertSee('Yağ Filtresi')
            ->assertSee('2 bakım işlemi')
            ->assertDontSee('Rot ayarı')
            ->assertDontSee('Sırada')
            ->assertSee('https://wa.me/905320000000', false);
    }
}
