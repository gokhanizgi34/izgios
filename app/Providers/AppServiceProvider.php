<?php

namespace App\Providers;

use App\Models\SilmeDenetimKaydi;
use App\Services\SilmeDenetimServisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
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
        Event::listen('eloquent.deleted: *', function (string $event, array $payload): void {
            $model = $payload[0] ?? null;
            if ($model instanceof Model && ! $model instanceof SilmeDenetimKaydi) {
                app(SilmeDenetimServisi::class)->modelSilindi($model);
            }
        });
    }
}
