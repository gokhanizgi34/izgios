<?php

namespace App\Services;

class ServisDurumAkisi
{
    public function sonraki(string $durum): string
    {
        return match ($durum) {
            'Bekliyor' => 'İşlemde',
            'İşlemde' => 'Teslime Hazır',
            'Teslime Hazır' => 'Tamamlandı',
            default => 'Tamamlandı',
        };
    }
}
