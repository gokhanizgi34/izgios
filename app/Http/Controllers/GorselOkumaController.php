<?php

namespace App\Http\Controllers;

use App\Services\GorselBelgeOkumaServisi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GorselOkumaController extends Controller
{
    public function oku(Request $request, GorselBelgeOkumaServisi $servis): JsonResponse
    {
        $kullanici = auth()->user();
        abort_unless($kullanici && ($kullanici->tamSistemYetkisiVarMi() || $kullanici->isAdmin() || $kullanici->isUsta() || $kullanici->isMuhasebe()), 403);
        $veri = $request->validate(['tur'=>['required','in:plaka,fis'],'gorsel'=>['required','image','mimes:jpeg,jpg,png,webp,gif','max:8192']]);
        if ($veri['tur'] === 'fis') abort_unless($kullanici->tamSistemYetkisiVarMi() || $kullanici->isAdmin() || $kullanici->isMuhasebe(), 403);
        try {
            return response()->json(['success'=>true,'data'=>$servis->oku($veri['gorsel'],$veri['tur'])]);
        } catch (\RuntimeException $hata) {
            return response()->json(['success'=>false,'message'=>$hata->getMessage()], 422);
        }
    }
}
