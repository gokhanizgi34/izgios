<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BildirimController extends Controller
{
    public function liste(Request $request): JsonResponse
    {
        abort_unless(auth()->check(), 403);
        $bildirimler = auth()->user()->notifications()->latest()->limit(12)->get();

        return response()->json([
            'okunmamis' => auth()->user()->unreadNotifications()->count(),
            'bildirimler' => $bildirimler->map(fn ($bildirim) => [
                'id' => $bildirim->id,
                'baslik' => $bildirim->data['baslik'] ?? 'Bildirim',
                'mesaj' => $bildirim->data['mesaj'] ?? '',
                'url' => $bildirim->data['url'] ?? route('dashboard'),
                'okundu' => $bildirim->read_at !== null,
                'tarih' => $bildirim->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function okundu(Request $request): RedirectResponse
    {
        abort_unless(auth()->check(), 403);
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
