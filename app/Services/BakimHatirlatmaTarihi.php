<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class BakimHatirlatmaTarihi
{
    public function hesapla(CarbonInterface|string $serviseGiris, int $ay): CarbonImmutable
    {
        return CarbonImmutable::parse($serviseGiris)
            ->addMonthsNoOverflow($ay)
            ->startOfDay();
    }
}
