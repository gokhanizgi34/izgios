<?php

namespace App\Providers;

use App\Models\SilmeDenetimKaydi;
use App\Services\SilmeDenetimServisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('yonetim_ayarlari')) {
                $ayarlar = DB::table('yonetim_ayarlari')->where('grup', 'yapay_zeka')->pluck('deger', 'anahtar');
                if ($ayarlar->has('aktif') && ($ayarlar['aktif'] ?? '0') !== '1') {
                    config(['services.izgios_ai.provider'=>null, 'services.izgios_ai.key'=>null]);
                } elseif (($ayarlar['aktif'] ?? '0') === '1' && filled($ayarlar['api_anahtari'] ?? null)) {
                    config([
                        'services.izgios_ai.provider' => 'openai',
                        'services.izgios_ai.key' => Crypt::decryptString($ayarlar['api_anahtari']),
                        'services.izgios_ai.model' => $ayarlar['model'] ?? config('services.izgios_ai.model', 'gpt-5.6'),
                    ]);
                }
            }
        } catch (\Throwable $hata) {
            report($hata);
        }

        Event::listen('eloquent.deleted: *', function (string $event, array $payload): void {
            $model = $payload[0] ?? null;
            if ($model instanceof Model && ! $model instanceof SilmeDenetimKaydi) {
                app(SilmeDenetimServisi::class)->modelSilindi($model);
            }
        });
    }
}
