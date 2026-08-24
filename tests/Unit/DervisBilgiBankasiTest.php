<?php

namespace Tests\Unit;

use App\Services\DervisBilgiBankasiServisi;
use PHPUnit\Framework\TestCase;

class DervisBilgiBankasiTest extends TestCase
{
    public function test_sss_cevabi_genel_modul_eslesmesinden_once_kullanilir(): void
    {
        $servis = new DervisBilgiBankasiServisi();
        $cevap = $servis->eslestir('Araç sil işlemi nasıl yapılır?');

        $this->assertSame('Bilgi Merkezi', $cevap['modul']);
        $this->assertStringContainsString('plaka', $cevap['ozet']);
    }

    public function test_merkezi_yapay_zeka_sss_kaydi_baglami_besler(): void
    {
        $baglam = (new DervisBilgiBankasiServisi())->asistanBaglami();
        $this->assertStringContainsString('tek OpenAI anahtarı', $baglam);
        $this->assertStringContainsString('her 10 dakikada', $baglam);
    }
}
