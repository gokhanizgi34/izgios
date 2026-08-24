<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Models\SilmeDenetimKaydi;
use Illuminate\Http\Request;

class SilmeDenetimController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $kayitlar = SilmeDenetimKaydi::query()
            ->when($request->filled('firma_id'), fn ($q) => $q->where('firma_id', $request->integer('firma_id')))
            ->when($request->filled('modul'), fn ($q) => $q->where('modul', $request->string('modul')))
            ->when($request->filled('arama'), function ($q) use ($request) {
                $arama = '%'.$request->string('arama')->toString().'%';
                $q->where(fn ($alt) => $alt->where('kayit_ozeti', 'like', $arama)->orWhere('islemi_yapan', 'like', $arama)->orWhere('kayit_turu', 'like', $arama));
            })->latest()->paginate(40)->withQueryString();
        $firmalar = Firma::orderBy('unvan')->get(['id','unvan']);
        $moduller = SilmeDenetimKaydi::query()->select('modul')->distinct()->orderBy('modul')->pluck('modul');
        return view('sistem.silme-kayitlari', compact('kayitlar','firmalar','moduller'));
    }
}
