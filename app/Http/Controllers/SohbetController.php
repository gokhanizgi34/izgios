<?php

namespace App\Http\Controllers;

use App\Models\SohbetMesaji;
use App\Models\SohbetOdasi;
use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SohbetController extends Controller
{
    public function index(Request $request)
    {
        $firmaId = $this->firmaId($request);
        $firmalar = auth()->user()->tamSistemYetkisiVarMi() ? Firma::query()->orderBy('unvan')->get() : collect();

        if (!$firmaId) {
            return view('sohbet.stable-index-v2', ['oda' => null, 'mesajlar' => collect(), 'firmalar' => $firmalar, 'firmaId' => null, 'personelSayisi' => 0]);
        }

        $oda = SohbetOdasi::firstOrCreate(
            ['firma_id' => $firmaId, 'tip' => 'genel'],
            ['ad' => 'Genel Sohbet', 'olusturan_id' => auth()->id()]
        );
        $mesajlar = $oda->mesajlar()->with('kullanici')->oldest()->limit(150)->get();
        $personelSayisi = DB::table('firma_personels')->where('firma_id', $firmaId)->where('aktif', true)->count();

        return view('sohbet.stable-index-v2', compact('oda', 'mesajlar', 'firmalar', 'firmaId', 'personelSayisi'));
    }

    public function odaOlustur(Request $request)
    {
        abort(404);
    }

    public function mesajGonder(Request $request, SohbetOdasi $oda)
    {
        $firmaId = $this->firmaId($request);
        abort_unless($oda->firma_id === $firmaId, 403);
        abort_unless($oda->tip === 'genel', 404);
        $veri = $request->validate(['mesaj' => ['required', 'string', 'max:4000']]);
        SohbetMesaji::create(['sohbet_odasi_id' => $oda->id, 'user_id' => auth()->id(), 'mesaj' => $veri['mesaj']]);
        return redirect()->route('sohbet.index', ['firma' => $firmaId]);
    }

    private function firmaId(Request $request): ?int
    {
        abort_unless(auth()->check(), 403);
        $firmaId = session('aktif_firma_id') ?: auth()->user()->firmaPersoneli?->firma_id;
        if (!$firmaId && auth()->user()->tamSistemYetkisiVarMi()) {
            $firmaId = $request->input('firma_id', $request->query('firma'));
            if (!$firmaId) { return null; }
            abort_unless(Firma::query()->whereKey($firmaId)->exists(), 404);
        }
        abort_unless($firmaId, 403, 'Sohbet için kullanıcıya bağlı aktif firma bulunmalıdır.');
        return (int) $firmaId;
    }
}
