<?php

namespace Tests\Feature;

use App\Services\MerkeziSistemMailGonderici;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MerkeziSistemMailGondericiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('yonetim_ayarlari', function (Blueprint $table) {
            $table->id();
            $table->string('grup');
            $table->string('anahtar');
            $table->text('deger')->nullable();
            $table->unsignedBigInteger('guncelleyen_id')->nullable();
            $table->timestamps();
            $table->unique(['grup', 'anahtar']);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('yonetim_ayarlari');
        parent::tearDown();
    }

    public function test_merkezi_email_yalniz_aktif_ve_tum_alanlar_tanimliyken_hazirdir(): void
    {
        $ayarlar = [
            'aktif'=>'1', 'smtp_host'=>'mail.izgios.test', 'smtp_port'=>'465',
            'kullanici_adi'=>'sistem@izgios.test', 'sifre'=>Crypt::encryptString('secret'),
            'gonderen'=>'sistem@izgios.test', 'bildirim_alicisi'=>'yonetim@izgios.test',
        ];
        foreach ($ayarlar as $anahtar=>$deger) DB::table('yonetim_ayarlari')->insert(['grup'=>'sistem_email','anahtar'=>$anahtar,'deger'=>$deger,'created_at'=>now(),'updated_at'=>now()]);

        $this->assertTrue(app(MerkeziSistemMailGonderici::class)->hazirMi());
        DB::table('yonetim_ayarlari')->where(['grup'=>'sistem_email','anahtar'=>'aktif'])->update(['deger'=>'0']);
        $this->assertFalse(app(MerkeziSistemMailGonderici::class)->hazirMi());
    }
}
