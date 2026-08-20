<?php

namespace Tests\Unit;

use App\Services\BakimHatirlatmaTarihi;
use PHPUnit\Framework\TestCase;

class BakimHatirlatmaTarihiTest extends TestCase
{
    public function test_servise_giris_tarihine_secili_ayi_ekler(): void
    {
        $tarih = (new BakimHatirlatmaTarihi)->hesapla('2026-05-12 10:30:00', 3);

        $this->assertSame('2026-08-12', $tarih->toDateString());
    }

    public function test_ay_sonunda_takvim_disina_tasmaz(): void
    {
        $tarih = (new BakimHatirlatmaTarihi)->hesapla('2026-01-31', 1);

        $this->assertSame('2026-02-28', $tarih->toDateString());
    }
}
