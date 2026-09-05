<?php

namespace App\Services;

class MaasHesaplamaServisi
{
    public function bruttenNete(float $brut, float $oncekiMatrah = 0, ?int $ay = null): array
    {
        $ay ??= (int) date('n');
        $brut = max(0, round($brut, 2));
        $sgk = round($brut * .14, 2); $issizlik = round($brut * .01, 2);
        $matrah = max(0, $brut - $sgk - $issizlik);
        $hesaplananVergi = $this->gelirVergisi($oncekiMatrah + $matrah) - $this->gelirVergisi($oncekiMatrah);
        $asgariMatrah = 33030 * .85;
        $asgariOncekiMatrah = $asgariMatrah * max(0, $ay - 1);
        $asgariIstisna = $this->gelirVergisi($asgariOncekiMatrah + $asgariMatrah) - $this->gelirVergisi($asgariOncekiMatrah);
        $gelirVergisi = round(max(0, $hesaplananVergi - $asgariIstisna), 2);
        $damgaVergisi = round(max(0, $brut - 33030) * .00759, 2);
        $net = max(0, round($brut - $sgk - $issizlik - $gelirVergisi - $damgaVergisi, 2));
        return compact('brut', 'net', 'sgk', 'issizlik', 'matrah', 'gelirVergisi', 'damgaVergisi');
    }

    public function nettenBrute(float $hedefNet, float $oncekiMatrah = 0, ?int $ay = null): array
    {
        $hedefNet = max(0, round($hedefNet, 2));
        if ($hedefNet === 0.0) return $this->bruttenNete(0, $oncekiMatrah, $ay);
        $alt = $hedefNet; $ust = max($hedefNet * 2.5, $hedefNet + 1000);
        while ($this->bruttenNete($ust, $oncekiMatrah, $ay)['net'] < $hedefNet) $ust *= 1.5;
        for ($i = 0; $i < 60; $i++) { $orta = ($alt + $ust) / 2; if ($this->bruttenNete($orta, $oncekiMatrah, $ay)['net'] < $hedefNet) $alt = $orta; else $ust = $orta; }
        $sonuc = $this->bruttenNete(round($ust, 2), $oncekiMatrah, $ay); $sonuc['net'] = $hedefNet;
        return $sonuc;
    }

    private function gelirVergisi(float $matrah): float
    {
        $dilimler = [[190000,.15],[400000,.20],[1500000,.27],[5300000,.35],[INF,.40]]; $vergi = 0; $onceki = 0;
        foreach ($dilimler as [$sinir, $oran]) { $tutar = min($matrah, $sinir) - $onceki; if ($tutar > 0) $vergi += $tutar * $oran; if ($matrah <= $sinir) break; $onceki = $sinir; }
        return round($vergi, 2);
    }
}
