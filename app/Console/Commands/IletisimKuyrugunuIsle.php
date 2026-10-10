<?php

namespace App\Console\Commands;

use App\Services\FirmaIletisimGonderici;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class IletisimKuyrugunuIsle extends Command
{
    protected $signature = 'izgi:iletisim-kuyrugunu-isle {--dry-run : Gönderim yapmadan uygun kayıtları listeler}';
    protected $description = 'Planlanan müşteri iletişim bildirimlerini işler.';

    public function handle(FirmaIletisimGonderici $gonderici): int
    {
        $kayitlar = DB::table('iletisim_gonderim_loglari')
            ->whereIn('durum', ['planlandi', 'hata', 'entegrasyon_bekliyor'])
            ->whereNotNull('planlanan_at')
            ->where('planlanan_at', '<=', now())
            ->where(function ($sorgu) {
                $sorgu->whereNull('sonraki_deneme_at')
                    ->orWhere('sonraki_deneme_at', '<=', now());
            })
            ->where('deneme_sayisi', '<', 5)
            ->orderBy('id')
            ->limit(200)
            ->get();

        foreach ($kayitlar as $kayit) {
            if ($this->option('dry-run')) {
                $this->line("#{$kayit->id} {$kayit->kanal}: {$kayit->alici_maskeli}");
                continue;
            }

            try {
                $arac = $kayit->arac_id ? DB::table('araclar')->where('id', $kayit->arac_id)->first() : null;
                $konu = $this->konu($kayit->mesaj_grubu, $arac?->plaka);
                $gonderici->gonder($kayit, $konu);
                DB::table('iletisim_gonderim_loglari')->where('id', $kayit->id)->update([
                    'durum' => 'gonderildi',
                    'gonderildi_at' => now(),
                    'son_hata' => null,
                    'sonraki_deneme_at' => null,
                    'updated_at' => now(),
                ]);
            } catch (Throwable $exception) {
                report($exception);
                $denemeSayisi = ((int) $kayit->deneme_sayisi) + 1;
                $entegrasyonEksik = str_contains($exception->getMessage(), 'eksik')
                    || str_contains($exception->getMessage(), 'etkin değil');
                $beklemeDakikasi = $entegrasyonEksik ? 60 : ([1 => 1, 2 => 5, 3 => 15, 4 => 60][$denemeSayisi] ?? 180);
                DB::table('iletisim_gonderim_loglari')->where('id', $kayit->id)->update([
                    'durum' => $denemeSayisi >= 5 ? 'hata_kalici' : ($entegrasyonEksik ? 'entegrasyon_bekliyor' : 'hata'),
                    'deneme_sayisi' => $denemeSayisi,
                    'son_hata' => mb_substr($exception->getMessage(), 0, 2000),
                    'sonraki_deneme_at' => $denemeSayisi >= 5 ? null : now()->addMinutes($beklemeDakikasi),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->info($this->option('dry-run') ? 'Kuyruk kontrol edildi.' : 'İletişim kuyruğu işlendi.');
        return self::SUCCESS;
    }

    private function konu(string $grup, ?string $plaka): string
    {
        $baslik = match ($grup) {
            'randevu_olustuldu' => 'Randevunuz oluşturuldu',
            'randevu_yaklasiyor' => 'Randevu hatırlatması',
            'randevu_iptal' => 'Randevu güncellemesi',
            'servis_kabul' => 'Aracınız servise kabul edildi',
            'teklif_hazir' => 'Servis teklifiniz hazır',
            'islem_basladi' => 'Servis işleminiz başladı',
            'ek_islem' => 'Ek işlem onayı gerekiyor',
            'teslimata_hazir' => 'Aracınız teslimata hazır',
            'teslim_edildi' => 'Araç teslim bilgilendirmesi',
            'bakim_hatirlatma' => 'Bakım hatırlatması',
            'bakim_gecikme' => 'Bakım zamanınız geçti',
            'ozel_gunler' => 'Özel gün kutlaması',
            'servis_evraklari' => 'Servis evraklarınız hazır',
            default => 'Servis bilgilendirmesi',
        };

        return trim(($plaka ? "{$plaka} | " : '') . $baslik);
    }
}
