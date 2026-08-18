<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class IletisimKuyrugunuIsle extends Command
{
    protected $signature = 'izgi:iletisim-kuyrugunu-isle {--dry-run : Gönderim yapmadan uygun kayıtları listeler}';
    protected $description = 'Planlanan müşteri iletişim bildirimlerini işler.';

    public function handle(): int
    {
        $kayitlar = DB::table('iletisim_gonderim_loglari')
            ->where('durum', 'planlandi')
            ->whereNotNull('planlanan_at')
            ->where('planlanan_at', '<=', now())
            ->orderBy('id')
            ->limit(200)
            ->get();

        foreach ($kayitlar as $kayit) {
            if ($this->option('dry-run')) {
                $this->line("#{$kayit->id} {$kayit->kanal}: {$kayit->alici_maskeli}");
                continue;
            }

            if ($kayit->kanal !== 'email') {
                DB::table('iletisim_gonderim_loglari')->where('id', $kayit->id)->update([
                    'durum' => 'entegrasyon_bekliyor',
                    'updated_at' => now(),
                ]);
                continue;
            }

            try {
                $firma = DB::table('firmas')->where('id', $kayit->firma_id)->first();
                $arac = $kayit->arac_id ? DB::table('araclar')->where('id', $kayit->arac_id)->first() : null;
                $konu = $this->konu($kayit->mesaj_grubu, $arac?->plaka);

                Mail::send('emails.iletisim-bildirimi', [
                    'firmaAdi' => $firma?->unvan ?: config('app.name', 'İZGİOS'),
                    'plaka' => $arac?->plaka,
                    'mesaj' => $kayit->mesaj,
                    'konu' => $konu,
                    'tarih' => now()->format('d.m.Y H:i'),
                ], function ($mail) use ($kayit, $konu) {
                    $mail->to($kayit->alici)->subject($konu);
                });
                DB::table('iletisim_gonderim_loglari')->where('id', $kayit->id)->update([
                    'durum' => 'gonderildi',
                    'gonderildi_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (Throwable $exception) {
                report($exception);
                DB::table('iletisim_gonderim_loglari')->where('id', $kayit->id)->update([
                    'durum' => 'hata',
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
