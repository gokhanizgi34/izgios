<?php

namespace Tests\Unit;

use App\Services\ServisDurumAkisi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ServisDurumAkisiTest extends TestCase
{
    public static function asamalar(): array
    {
        return [
            ['Bekliyor', 'İşlemde'],
            ['İşlemde', 'Teslime Hazır'],
            ['Teslime Hazır', 'Tamamlandı'],
            ['Tamamlandı', 'Tamamlandı'],
        ];
    }

    #[DataProvider('asamalar')]
    public function test_siradaki_asamayi_dogru_belirler(string $mevcut, string $beklenen): void
    {
        $this->assertSame($beklenen, (new ServisDurumAkisi)->sonraki($mevcut));
    }
}
