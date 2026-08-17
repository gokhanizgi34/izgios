<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ZorunluOturum
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('login', 'sifremi-unuttum', 'sifre-sifirla/*', 'qr-servis/*') || auth()->check()) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Bu ekrana erişmek için giriş yapmalısınız.');
    }
}
