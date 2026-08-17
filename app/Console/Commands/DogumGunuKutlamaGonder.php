<?php

namespace App\Console\Commands;

use App\Mail\DogumGunuKutlamaMaili;
use App\Models\DogumGunuEpostaLogu;
use App\Models\Musteri;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DogumGunuKutlamaGonder extends Command
{
    protected $signature = 'izgi:dogum-gunu-kutla';
    protected $description = 'Bugün doğum günü olan müşteri ve personele kutlama e-postası gönderir.';

    public function handle(): int
    {
        $gun = now()->format('m-d');
        $yil = (int) now()->year;
        $gonderilen = 0;

        $gonder = function ($alici, string $tip, string $hitap) use ($gun, $yil, &$gonderilen): void {
            if (!$alici->email || $alici->dogum_tarihi?->format('m-d') !== $gun) {
                return;
            }

            if (DogumGunuEpostaLogu::query()->where(['alici_tipi' => $tip, 'alici_id' => $alici->id, 'yil' => $yil])->exists()) {
                return;
            }

            $ad = $tip === 'personel' ? $alici->tamAdi() : $alici->ad_soyad;
            Mail::to($alici->email)->send(new DogumGunuKutlamaMaili($ad, $hitap));
            DogumGunuEpostaLogu::create(['alici_tipi' => $tip, 'alici_id' => $alici->id, 'yil' => $yil, 'gonderildi_at' => now()]);
            $gonderilen++;
        };

        Musteri::query()->whereNotNull('dogum_tarihi')->whereNotNull('email')->get()->each(fn ($musteri) => $gonder($musteri, 'musteri', 'müşterimiz'));
        User::query()->where('status', 'aktif')->whereNotNull('dogum_tarihi')->whereNotNull('email')->get()->each(fn ($personel) => $gonder($personel, 'personel', 'çalışma arkadaşımız'));

        $this->info("{$gonderilen} doğum günü e-postası gönderildi.");
        return self::SUCCESS;
    }
}
