<?php

namespace App\Http\Controllers;

use App\Models\DestekTalebi;
use App\Services\YapayZekaDestekServisi;
use Illuminate\Http\Request;

class DestekController extends Controller
{
    public function index()
    {
        $this->oturumKontrol();
        $sorgu = DestekTalebi::query()->with(['kullanici', 'firma'])->latest();

        if (!auth()->user()->tamSistemYetkisiVarMi()) {
            $sorgu->where('user_id', auth()->id());
        }

        return view('destek.stable-index', ['talepler' => $sorgu->paginate(30)]);
    }

    public function create()
    {
        $this->oturumKontrol();
        return view('destek.create');
    }

    public function store(Request $request, YapayZekaDestekServisi $yapayZeka)
    {
        $this->oturumKontrol();
        $veri = $request->validate([
            'kategori' => ['required', 'in:genel,hesap,teknik,servis,muhasebe,stok'],
            'oncelik' => ['required', 'in:normal,yuksek,acil'],
            'baslik' => ['required', 'string', 'max:180'],
            'mesaj' => ['required', 'string', 'max:5000'],
            'hata_kodu' => ['nullable', 'string', 'max:70'],
        ]);

        $firmaId = session('aktif_firma_id') ?: auth()->user()->firmaPersoneli?->firma_id;

        $talep = DestekTalebi::create(array_merge($veri, [
            'user_id' => auth()->id(), 'firma_id' => $firmaId,
            'durum' => 'acik', 'ai_durum' => 'inceleniyor',
        ]));

        $analiz = $yapayZeka->analizEt($talep);
        $talep->update(['durum' => $analiz['durum'] === 'cozum_onerildi' ? 'ai_yonlendirildi' : 'acik', 'ai_durum' => $analiz['durum'], 'ai_ozet' => $analiz['ozet'], 'ai_cozum' => $analiz['cozum']]);

        return redirect()->route('destek.index')->with('success', 'Destek talebiniz kaydedildi ve ön değerlendirmeye alındı.');
    }

    public function durumGuncelle(Request $request, DestekTalebi $talep)
    {
        $this->sistemYoneticisiKontrol();
        $talep->update($request->validate(['durum' => ['required', 'in:acik,inceleniyor,cozuldu,ai_yonlendirildi']]));
        return back()->with('success', 'Destek talebi durumu güncellendi.');
    }

    private function oturumKontrol(): void { if (!auth()->check()) { abort(403); } }
    private function sistemYoneticisiKontrol(): void { $this->oturumKontrol(); if (!auth()->user()->tamSistemYetkisiVarMi()) { abort(403); } }
}
