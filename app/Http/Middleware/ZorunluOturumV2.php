<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ZorunluOturumV2
{
    public function handle(Request $request, Closure $next): Response
    {
        $herkeseAcik = $request->is(
            'login',
            'demo',
            'sifremi-unuttum',
            'sifre-sifirla/*',
            'arac/*',
            'qr-servis/*'
        );

        if ($herkeseAcik || ! auth()->check()) {
            if ($herkeseAcik) {
                return $next($request);
            }

            return redirect()->route('login')
                ->with('error', 'Bu ekrana erişmek için giriş yapmalısınız.');
        }

        // İnsan Kaynakları kullanıcıları yalnız kendi firma kapsamındaki İK ve hesap alanlarında çalışır.
        if (auth()->user()->isIk() && ! $request->is(
            'ayarlar/ik',
            'ayarlar/ik/*',
            'kullanicilar',
            'kullanicilar/*',
            'hesabim/*',
            'logout'
        )) {
            return redirect()->route('ik.index')
                ->with('error', 'İnsan Kaynakları rolü yalnız İK çalışma alanına erişebilir.');
        }

        if (auth()->check()) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
