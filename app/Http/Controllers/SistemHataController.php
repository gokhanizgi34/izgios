<?php

namespace App\Http\Controllers;

use App\Services\SistemHataIzlemeServisi;
use App\Services\YapayZekaHataAnalizServisi;

class SistemHataController extends Controller
{
    public function index(SistemHataIzlemeServisi $izleme)
    {
        $this->yetki(); $izleme->tara();
        return view('sistem-hatalari.ozet-v3', ['hatalar'=>$izleme->acikHatalar()]);
    }

    public function yapayZekaTara(SistemHataIzlemeServisi $izleme, YapayZekaHataAnalizServisi $yapayZeka)
    {
        $this->yetki(); $sonuc=$izleme->tara(); $hatalar=$izleme->acikHatalar()->all();
        if($hatalar===[]) return back()->with('success', "Tarama tamamlandı. {$sonuc['cozulen']} çözülmüş hata denetim kaydına aktarıldı; açık hata yok.");
        $analiz=$yapayZeka->analizEt($hatalar);
        return back()->with($analiz['basarili']?'ai_hata_analizi':'error',$analiz['mesaj']);
    }

    public function cozumPlaniniOnayla(){ $this->yetki(); return back()->with('success','Çözüm planı inceleme için onaylandı.'); }
    private function yetki(): void { abort_unless(auth()->check()&&auth()->user()->tamSistemYetkisiVarMi(),403,'Bu ekran yalnızca Sistem Yöneticisi içindir.'); }
}
