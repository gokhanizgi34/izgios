<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GenelMuhasebeAktarimServisi
{
    public function giderFisleriniAktar(int $firmaId): void
    {
        DB::table('muhasebe_fisleri')->where('firma_id', $firmaId)->where('yon', 'gider')->where('durum', 'onaylandi')
            ->orderBy('id')->each(fn ($fis) => $this->giderFisiniAktar($fis->id));
    }

    public function giderFisiniAktar(int $fisId): void
    {
        $fis = DB::table('muhasebe_fisleri')->where('id', $fisId)->first();
        if (! $fis || $fis->yon !== 'gider' || $fis->durum !== 'onaylandi') return;
        if (DB::table('muhasebe_yevmiye_fisleri')->where('firma_id', $fis->firma_id)->where('kaynak', 'gider_fisi')->where('kaynak_id', $fis->id)->exists()) return;

        $hesaplar = $this->hesaplariHazirla((int) $fis->firma_id);
        $satirlar = DB::table('muhasebe_fis_satirlari')->where('muhasebe_fis_id', $fis->id)->get();
        $net = (float) $satirlar->sum('kdv_haric_tutar');
        $kdv = (float) $satirlar->sum('kdv_tutari');
        $toplam = (float) $fis->tutar;
        if ($toplam <= 0) return;
        if ($net <= 0) $net = round($toplam - $kdv, 2);

        $donemId = $this->donemId((int) $fis->firma_id, $fis->fis_tarihi);
        DB::transaction(function () use ($fis, $hesaplar, $net, $kdv, $toplam, $donemId) {
            $yevmiyeId = DB::table('muhasebe_yevmiye_fisleri')->insertGetId([
                'firma_id' => $fis->firma_id,
                'muhasebe_donem_id' => $donemId,
                'fis_no' => 'ENT-GID-' . $fis->id,
                'fis_tarihi' => $fis->fis_tarihi,
                'tip' => 'entegrasyon',
                'aciklama' => 'Gider fişi aktarımı: ' . ($fis->fis_no ?: $fis->id),
                'kaynak' => 'gider_fisi',
                'kaynak_id' => $fis->id,
                'durum' => 'onaylandi',
                'olusturan_id' => $fis->olusturan_id,
                'onaylayan_id' => $fis->olusturan_id,
                'onay_tarihi' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $veri = [
                ['hesap' => $hesaplar['770'], 'borc' => $net, 'alacak' => 0, 'cari' => null, 'aciklama' => 'Gider tutarı'],
                ['hesap' => $hesaplar['191'], 'borc' => $kdv, 'alacak' => 0, 'cari' => null, 'aciklama' => 'İndirilecek KDV'],
                ['hesap' => $fis->cari_hesap_id ? $hesaplar['320'] : $hesaplar['100'], 'borc' => 0, 'alacak' => $toplam, 'cari' => $fis->cari_hesap_id, 'aciklama' => $fis->cari_hesap_id ? 'Tedarikçi borcu' : 'Kasa çıkışı'],
            ];
            foreach ($veri as $sira => $satir) {
                if ((float) $satir['borc'] === 0.0 && (float) $satir['alacak'] === 0.0) continue;
                DB::table('muhasebe_yevmiye_satirlari')->insert(['muhasebe_yevmiye_fis_id' => $yevmiyeId, 'muhasebe_hesap_plan_id' => $satir['hesap'], 'cari_hesap_id' => $satir['cari'], 'aciklama' => $satir['aciklama'], 'borc' => $satir['borc'], 'alacak' => $satir['alacak'], 'sira' => $sira + 1, 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    private function hesaplariHazirla(int $firmaId): array
    {
        $tanimlar = [['100','Kasa','varlik','borc'],['191','İndirilecek KDV','varlik','borc'],['320','Satıcılar','borc','alacak'],['770','Genel Yönetim Giderleri','gider','borc']];
        foreach ($tanimlar as [$kod, $ad, $sinif, $bakiye]) DB::table('muhasebe_hesap_planlari')->updateOrInsert(['firma_id'=>$firmaId,'kod'=>$kod],['ad'=>$ad,'sinif'=>$sinif,'normal_bakiye'=>$bakiye,'aktif'=>true,'updated_at'=>now(),'created_at'=>now()]);
        return DB::table('muhasebe_hesap_planlari')->where('firma_id', $firmaId)->whereIn('kod', ['100','191','320','770'])->pluck('id','kod')->all();
    }

    private function donemId(int $firmaId, string $tarih): int
    {
        $yil = (int) date('Y', strtotime($tarih));
        DB::table('muhasebe_donemleri')->updateOrInsert(['firma_id'=>$firmaId,'baslangic_tarihi'=>"$yil-01-01",'bitis_tarihi'=>"$yil-12-31"],['ad'=>"$yil Mali Dönemi",'durum'=>'acik','updated_at'=>now(),'created_at'=>now()]);
        return (int) DB::table('muhasebe_donemleri')->where('firma_id',$firmaId)->whereDate('baslangic_tarihi','<=',$tarih)->whereDate('bitis_tarihi','>=',$tarih)->value('id');
    }
}
