<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Models\SohbetMesaji;
use App\Models\SohbetOdasi;
use App\Models\User;
use App\Notifications\FirmaSistemBildirimi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SohbetController extends Controller
{
    private const KANALLAR=['genel'=>'Genel','muhasebe'=>'Muhasebe','yedek_parca'=>'Yedek Parça','usta'=>'Usta'];

    public function index(Request $request)
    {
        $firmaId=$this->firmaId($request); $firmalar=auth()->user()->tamSistemYetkisiVarMi()?Firma::orderBy('unvan')->get():collect();
        if(!$firmaId)return view('sohbet.stable-index-v2',['oda'=>null,'odalar'=>collect(),'mesajlar'=>collect(),'firmalar'=>$firmalar,'firmaId'=>null,'personelSayisi'=>0,'etiketAdlari'=>collect(),'yazabilir'=>false]);
        $odalar=collect(self::KANALLAR)->map(fn($ad,$tip)=>SohbetOdasi::firstOrCreate(['firma_id'=>$firmaId,'tip'=>$tip],['ad'=>$ad,'olusturan_id'=>auth()->id()]));
        $istenen=(string)$request->query('kanal','genel'); $oda=$odalar->firstWhere('tip',array_key_exists($istenen,self::KANALLAR)?$istenen:'genel');
        $mesajlar=$oda->mesajlar()->with('kullanici')->latest()->limit(150)->get()->reverse()->values();
        $personelSayisi=DB::table('firma_personels')->where('firma_id',$firmaId)->where('aktif',true)->count(); $etiketAdlari=$this->firmaKullanicilari($firmaId)->map(fn(User $k)=>$k->tamAdi()); $yazabilir=$this->kanalaYazabilir($oda->tip);
        return view('sohbet.stable-index-v2',compact('oda','odalar','mesajlar','firmalar','firmaId','personelSayisi','etiketAdlari','yazabilir'));
    }

    public function odaOlustur(Request $request){abort(404);}

    public function mesajGonder(Request $request,SohbetOdasi $oda)
    {
        $firmaId=$this->firmaId($request);abort_unless((int)$oda->firma_id===$firmaId,403);abort_unless(array_key_exists($oda->tip,self::KANALLAR),404);abort_unless($this->kanalaYazabilir($oda->tip),403,'Bu kanala yalnızca ilgili rol mesaj yazabilir.');
        $veri=$request->validate(['mesaj'=>['required','string','max:4000']]);$mesaj=SohbetMesaji::create(['sohbet_odasi_id'=>$oda->id,'user_id'=>auth()->id(),'mesaj'=>$veri['mesaj']]);$this->bildirimleriGonder($oda,$mesaj);$this->etiketlenenlereEpostaGonder($oda,$mesaj);
        return redirect()->route('sohbet.index',['firma'=>$firmaId,'kanal'=>$oda->tip]);
    }

    public function mesajlarJson(Request $request,SohbetOdasi $oda)
    {
        $firmaId=$this->firmaId($request);abort_unless((int)$oda->firma_id===$firmaId&&array_key_exists($oda->tip,self::KANALLAR),403);
        return response()->json(['mesajlar'=>$oda->mesajlar()->with('kullanici')->where('id','>',$request->integer('son_id'))->oldest()->limit(80)->get()->map(fn(SohbetMesaji $m)=>['id'=>$m->id,'user_id'=>$m->user_id,'ad'=>$m->kullanici?->tamAdi()??'Kullanıcı','rol'=>$m->kullanici?->rolAdi()??'Firma kullanıcısı','mesaj'=>$m->mesaj,'tarih'=>$m->created_at->format('d.m.Y H:i')])]);
    }

    private function bildirimleriGonder(SohbetOdasi $oda,SohbetMesaji $mesaj):void
    {
        $metin=mb_strtolower($mesaj->mesaj,'UTF-8');$alicilar=$this->firmaKullanicilari($oda->firma_id)->filter(function(User $k)use($oda,$mesaj,$metin){if($k->id===$mesaj->user_id)return false;$etiket=$this->etiketlendiMi($k,$metin);return $etiket||$oda->tip==='genel'||$k->role===$oda->tip||$k->tamSistemYetkisiVarMi()||$k->isAdmin();});
        foreach($alicilar as $alici){$etiket=$this->etiketlendiMi($alici,$metin);$alici->notify(new FirmaSistemBildirimi(['tur'=>$etiket?'sohbet_etiket':'sohbet','baslik'=>$etiket?'Sohbette sizden bahsedildi':self::KANALLAR[$oda->tip].' kanalında yeni mesaj','mesaj'=>($mesaj->kullanici?->tamAdi()??'Kullanıcı').': '.mb_strimwidth($mesaj->mesaj,0,140,'…','UTF-8'),'url'=>route('sohbet.index',['firma'=>$oda->firma_id,'kanal'=>$oda->tip])]));}
    }

    private function etiketlendiMi(User $k,string $metin):bool{$ad=mb_strtolower($k->tamAdi(),'UTF-8');$eposta=mb_strtolower(strstr((string)$k->email,'@',true)?:'','UTF-8');return str_contains($metin,'@'.$ad)||($eposta!==''&&str_contains($metin,'@'.$eposta));}
    private function etiketlenenlereEpostaGonder(SohbetOdasi $oda,SohbetMesaji $mesaj):void
    {
        $metin=mb_strtolower($mesaj->mesaj,'UTF-8');$etiketlenenler=$this->firmaKullanicilari($oda->firma_id)->filter(fn(User $k)=>$k->id!==$mesaj->user_id&&filled($k->email)&&$this->etiketlendiMi($k,$metin));
        foreach($etiketlenenler as $k){try{Mail::send('emails.sohbet-etiket',['gonderen'=>$mesaj->kullanici?->tamAdi()??'Bir firma kullanıcısı','firma'=>$oda->firma?->unvan??'Firma','mesaj'=>$mesaj->mesaj,'sohbetUrl'=>route('sohbet.index',['firma'=>$oda->firma_id,'kanal'=>$oda->tip])],fn($mail)=>$mail->to($k->email)->subject('İZGİOS sohbetinde sizden bahsedildi'));}catch(\Throwable $hata){Log::warning('Sohbet etiket e-postası gönderilemedi.',['mesaj_id'=>$mesaj->id,'kullanici_id'=>$k->id,'sebep'=>$hata->getMessage()]);}}
    }

    private function firmaKullanicilari(int $firmaId){return User::where('status','aktif')->whereHas('firmaPersoneli',fn($q)=>$q->where('firma_id',$firmaId)->where('aktif',true))->get();}
    private function kanalaYazabilir(string $tip):bool{return $tip==='genel'||auth()->user()->role===$tip||auth()->user()->tamSistemYetkisiVarMi()||auth()->user()->isAdmin();}
    private function firmaId(Request $request):?int{$firmaId=session('aktif_firma_id')?:auth()->user()?->firmaPersoneli?->firma_id;if(!$firmaId&&auth()->user()?->tamSistemYetkisiVarMi()){$firmaId=$request->input('firma_id',$request->query('firma'));if(!$firmaId)return null;abort_unless(Firma::whereKey($firmaId)->exists(),404);}abort_unless($firmaId,403,'Sohbet için kullanıcıya bağlı aktif firma bulunmalıdır.');return(int)$firmaId;}
}
