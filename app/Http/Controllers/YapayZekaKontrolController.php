<?php

namespace App\Http\Controllers;

use App\Models\DestekTalebi;

class YapayZekaKontrolController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $veriler = [
            'anahtar_tanimli' => filled(config('services.izgios_ai.key')),
            'saglayici' => config('services.izgios_ai.provider', 'Tanımlanmadı'),
            'acik_talep' => DestekTalebi::query()->whereIn('durum', ['acik', 'inceleniyor', 'ai_yonlendirildi'])->count(),
            'teknik_kuyruk' => DestekTalebi::query()->where('ai_durum', 'sistem_yoneticisine_yonlendirildi')->latest()->take(10)->get(),
        ];
        return view('yapay-zeka.index', compact('veriler'));
    }
}
