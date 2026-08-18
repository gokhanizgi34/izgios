<?php

namespace App\Services;

use App\Models\Servis;
use Illuminate\Support\Facades\DB;

class ServisMuhasebeAktarimServisi
{
    public function aktar(Servis $servis): void
    {
        if (DB::table('muhasebe_fisleri')->where('kaynak', 'servis')->where('kaynak_id', $servis->id)->exists()) {
            return;
        }

        $servis->loadMissing(['musteri.araclar', 'arac', 'islemler', 'parcalar']);
        if (! $servis->musteri) {
            return;
        }

        $cariId = app(CariAktarimServisi::class)->musteriKarti($servis->musteri);
        DB::table('cari_hesaplar')->where('id', $cariId)->update(['plaka' => $servis->arac?->plaka, 'updated_at' => now()]);

        $satirlar = collect();
        foreach ($servis->islemler as $islem) {
            $satirlar->push(['urun_adi' => $islem->islem_adi, 'adet' => 1, 'birim' => 'Hizmet', 'net' => (float) $islem->tutar]);
        }
        foreach ($servis->parcalar as $parca) {
            $satirlar->push(['urun_adi' => $parca->parca_adi, 'adet' => (float) $parca->adet, 'birim' => 'Adet', 'net' => (float) $parca->toplam_fiyat]);
        }
        if ($satirlar->isEmpty()) {
            $satirlar->push(['urun_adi' => 'Servis hizmeti', 'adet' => 1, 'birim' => 'Hizmet', 'net' => (float) $servis->toplam_tutar]);
        }

        DB::transaction(function () use ($servis, $cariId, $satirlar) {
            $toplam = $satirlar->sum(fn ($satir) => round($satir['net'] * 1.20, 2));
            $fisId = DB::table('muhasebe_fisleri')->insertGetId([
                'firma_id' => $servis->firma_id,
                'cari_hesap_id' => $cariId,
                'fis_no' => 'SRV-'.str_pad((string) $servis->id, 7, '0', STR_PAD_LEFT),
                'tip' => 'Servis Gelir Fişi',
                'fis_tarihi' => optional($servis->servis_tarihi)->toDateString() ?: now()->toDateString(),
                'aciklama' => ($servis->arac?->plaka ?: 'Araç').' servis işlemleri',
                'tutar' => $toplam,
                'yon' => 'gelir',
                'kaynak' => 'servis',
                'kaynak_id' => $servis->id,
                'durum' => 'onaylandi',
                'olusturan_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($satirlar as $satir) {
                $net = round($satir['net'], 2);
                $kdv = round($net * .20, 2);
                DB::table('muhasebe_fis_satirlari')->insert([
                    'muhasebe_fis_id' => $fisId,
                    'urun_adi' => $satir['urun_adi'],
                    'adet' => $satir['adet'],
                    'birim' => $satir['birim'],
                    'birim_fiyat' => $satir['adet'] > 0 ? round($net / $satir['adet'], 2) : $net,
                    'kdv_orani' => 20,
                    'kdv_haric_tutar' => $net,
                    'kdv_tutari' => $kdv,
                    'kdv_dahil_tutar' => $net + $kdv,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('cari_hesaplar')->where('id', $cariId)->increment('bakiye', $toplam);

            if (! DB::table('faturalar')->where('servis_id', $servis->id)->exists()) {
                $faturaId = DB::table('faturalar')->insertGetId([
                    'firma_id' => $servis->firma_id,
                    'cari_hesap_id' => $cariId,
                    'servis_id' => $servis->id,
                    'fatura_no' => 'FTR-'.now()->format('YmdHis').'-'.$servis->id,
                    'musteri_unvan' => $servis->musteri?->ad_soyad ?: 'Müşteri',
                    'tarih' => optional($servis->servis_tarihi)->toDateString() ?: now()->toDateString(),
                    'vade_tarihi' => now()->addDays(30)->toDateString(),
                    'ara_toplam' => $satirlar->sum('net'),
                    'kdv_toplam' => $satirlar->sum(fn ($satir) => round($satir['net'] * .20, 2)),
                    'iskonto_toplam' => 0,
                    'tutar' => $toplam,
                    'para_birimi' => 'TRY',
                    'durum' => 'taslak',
                    'entegrasyon_durumu' => 'gonderilmedi',
                    'notlar' => 'Tamamlanan '.($servis->servis_no ?: 'servis').' iş emrinden otomatik oluşturuldu.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                foreach ($satirlar as $satir) {
                    $net = round($satir['net'], 2);
                    $kdv = round($net * .20, 2);
                    DB::table('ticari_belge_satirlari')->insert([
                        'belge_turu' => 'fatura',
                        'belge_id' => $faturaId,
                        'urun_hizmet_adi' => $satir['urun_adi'],
                        'miktar' => $satir['adet'],
                        'birim' => $satir['birim'],
                        'birim_fiyat' => $satir['adet'] > 0 ? round($net / $satir['adet'], 2) : $net,
                        'iskonto_orani' => 0,
                        'kdv_orani' => 20,
                        'kdv_haric_tutar' => $net,
                        'kdv_tutari' => $kdv,
                        'kdv_dahil_tutar' => $net + $kdv,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}
