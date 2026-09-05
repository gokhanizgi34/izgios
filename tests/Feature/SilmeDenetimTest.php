<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DenemeSilinecekKayit extends Model
{
    protected $table = 'deneme_silinecek_kayitlar';
    protected $guarded = [];
}

class SilmeDenetimTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('deneme_silinecek_kayitlar', function (Blueprint $t) { $t->id(); $t->string('baslik'); $t->timestamps(); });
        Schema::create('silme_denetim_kayitlari', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('firma_id')->nullable(); $t->unsignedBigInteger('kullanici_id')->nullable(); $t->string('modul'); $t->string('kayit_turu'); $t->string('kayit_id')->nullable(); $t->string('kayit_ozeti')->nullable(); $t->json('silinen_veri')->nullable(); $t->string('islemi_yapan')->nullable(); $t->string('rol')->nullable(); $t->string('ip_adresi')->nullable(); $t->text('ekran_adresi')->nullable(); $t->boolean('firma_sahibine_mail')->default(false); $t->text('mail_hatasi')->nullable(); $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('silme_denetim_kayitlari');
        Schema::dropIfExists('deneme_silinecek_kayitlar');
        parent::tearDown();
    }

    public function test_eloquent_silme_islemi_merkezi_denetim_kaydina_yazilir(): void
    {
        $kayit = DenemeSilinecekKayit::create(['baslik' => 'Silinecek kayıt']);
        $kayit->delete();

        $this->assertDatabaseHas('silme_denetim_kayitlari', [
            'kayit_turu' => 'DenemeSilinecekKayit',
            'kayit_id' => (string) $kayit->id,
            'kayit_ozeti' => 'Silinecek kayıt',
        ]);
    }
}
