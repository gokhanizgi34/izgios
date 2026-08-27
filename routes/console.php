<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



/*
|--------------------------------------------------------------------------
| İZGİ OS Otomatik Backup
|--------------------------------------------------------------------------
*/

Schedule::command('izgi:backup')
    ->hourly();

Schedule::command('izgi:dogum-gunu-kutla')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('izgi:iletisim-kuyrugunu-isle')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('izgi:destek-zaman-asimini-kapat', function () {
    $adet = \App\Models\DestekTalebi::query()
        ->where('durum', 'ai_yonlendirildi')
        ->whereNull('kullanici_geri_bildirimi')
        ->whereNotNull('son_yanit_at')
        ->where('son_yanit_at', '<=', now()->subMinutes(30))
        ->update([
            'durum' => 'zaman_asimiyla_kapatildi',
            'ai_durum' => 'zaman_asimiyla_kapatildi',
            'zaman_asimi_at' => now(),
            'updated_at' => now(),
        ]);

    $this->info("{$adet} destek talebi zaman aşımıyla kapatıldı.");
})->purpose('Derviş çözüm önerisine 30 dakika yanıt gelmeyen destek taleplerini kapatır.');

Schedule::command('izgi:destek-zaman-asimini-kapat')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Artisan::command('izgi:sistem-hatalarini-tara', function () {
    $sonuc = app(\App\Services\SistemHataIzlemeServisi::class)->tara();
    $this->info("Açık hata: {$sonuc['acik']}; çözülen ve denetime aktarılan: {$sonuc['cozulen']}.");
})->purpose('Uygulama hatalarını günceller ve çözülenleri silme denetimine aktarır.');

Schedule::command('izgi:sistem-hatalarini-tara')->everyTenMinutes()->withoutOverlapping();
