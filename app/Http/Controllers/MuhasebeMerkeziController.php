<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Services\SilmeDenetimServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MuhasebeMerkeziController extends Controller
{
    private function yetki(): void
    {
        abort_unless(auth()->check() && (auth()->user()->tamSistemYetkisiVarMi() || auth()->user()->isAdmin() || auth()->user()->isMuhasebe()), 403);
    }

    private function firmaId(Request $request): ?int
    {
        $this->yetki();
        $firmalar = Firma::where('aktif', true)->orderBy('unvan')->get();
        $id = $request->integer('firma_id') ?: $firmalar->first()?->id;
        if (! auth()->user()->tamSistemYetkisiVarMi()) $id = auth()->user()->firmaPersoneli?->firma_id;
        return $id;
    }

    private function firmalar()
    {
        $query = Firma::where('aktif', true);
        if (auth()->check() && ! auth()->user()->tamSistemYetkisiVarMi()) {
            $query->whereKey(auth()->user()->firmaPersoneli?->firma_id);
        }
        return $query->orderBy('unvan')->get();
    }

    public function index(Request $request)
    {
        $firmalar = $this->firmalar(); $firmaId = $this->firmaId($request);
        $fis = DB::table('muhasebe_fisleri')->where('firma_id', $firmaId);
        $ozet = [
            'gelir' => (float) (clone $fis)->where('yon','gelir')->where('durum','onaylandi')->sum('tutar'),
            'gider' => (float) (clone $fis)->where('yon','gider')->where('durum','onaylandi')->sum('tutar'),
            'cari' => DB::table('cari_hesaplar')->where('firma_id',$firmaId)->where('aktif',true)->count(),
            'fis' => (clone $fis)->count(),
        ];
        $fisler = (clone $fis)->latest('fis_tarihi')->limit(10)->get();
        $belgeOzet = [
            'teklif' => DB::table('teklifler')->where('firma_id',$firmaId)->whereIn('durum',['taslak','gonderildi'])->count(),
            'fatura' => DB::table('faturalar')->where('firma_id',$firmaId)->whereIn('durum',['taslak','beklemede'])->count(),
            'vadesiGecmis' => DB::table('faturalar')->where('firma_id',$firmaId)->whereNotNull('vade_tarihi')->whereDate('vade_tarihi','<',today())->whereNotIn('durum',['odendi','iptal'])->count(),
        ];
        return view('ticari.index-v3', compact('firmalar','firmaId','ozet','fisler','belgeOzet'));
    }

    public function cariler(Request $request)
    {
        $firmalar=$this->firmalar(); $firmaId=$this->firmaId($request); $arama=trim((string)$request->input('ara')); $tip=$request->input('tip');
        $q=DB::table('cari_hesaplar')->where('firma_id',$firmaId);
        if ($arama !== '') $q->where(fn($x)=>$x->where('unvan','like',"%{$arama}%")->orWhere('plaka','like',"%{$arama}%")->orWhere('vergi_no','like',"%{$arama}%")->orWhere('telefon','like',"%{$arama}%"));
        if (in_array($tip,['musteri','tedarikci','diger'],true)) $q->where('tip',$tip);
        if ($request->has('aktif')) $q->where('aktif',$request->boolean('aktif'));
        $cariler=$q->orderByDesc('aktif')->orderBy('unvan')->get();
        $cariOzet=['borc'=>(float)$cariler->filter(fn($x)=>(float)$x->bakiye<0)->sum(fn($x)=>abs((float)$x->bakiye)),'alacak'=>(float)$cariler->filter(fn($x)=>(float)$x->bakiye>0)->sum('bakiye'),'aktif'=>$cariler->where('aktif',true)->count()];
        return view('ticari.cari-hesaplar-v3',compact('firmalar','firmaId','cariler','arama','tip','cariOzet'));
    }

    public function cariKaydet(Request $request)
    {
        $id=$this->firmaId($request); $v=$request->validate(['tip'=>['required','in:musteri,tedarikci,diger'],'unvan'=>['required','string','max:255'],'plaka'=>['nullable','string','max:20'],'telefon'=>['nullable','string','max:30'],'email'=>['nullable','email','max:255'],'vergi_no'=>['nullable','string','max:20']]);
        DB::table('cari_hesaplar')->insert([...$v,'firma_id'=>$id,'bakiye'=>0,'aktif'=>true,'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success','Cari kart oluşturuldu.');
    }

    private function cariDogrula(Request $request): array
    {
        return $request->validate(['tip'=>['required','in:musteri,tedarikci,diger'],'unvan'=>['required','string','max:255'],'plaka'=>['nullable','string','max:20'],'telefon'=>['nullable','string','max:30'],'email'=>['nullable','email','max:255'],'vergi_no'=>['nullable','string','max:20'],'aktif'=>['nullable','boolean']]);
    }

    public function cariGuncelle(Request $request, int $cari)
    {
        $firmaId=$this->firmaId($request); $kayit=DB::table('cari_hesaplar')->whereKey($cari)->where('firma_id',$firmaId)->first(); abort_unless($kayit,404);
        $v=$this->cariDogrula($request); $v['aktif']=$request->boolean('aktif'); $v['updated_at']=now();
        DB::table('cari_hesaplar')->whereKey($cari)->update($v);
        return back()->with('success','Cari kart güncellendi.');
    }

    public function cariSil(Request $request, int $cari)
    {
        $firmaId=$this->firmaId($request); $kayit=DB::table('cari_hesaplar')->whereKey($cari)->where('firma_id',$firmaId)->first(); abort_unless($kayit,404);
        app(SilmeDenetimServisi::class)->tabloKaydiSilindi('Cari Hesap',$kayit,$firmaId);
        DB::table('cari_hesaplar')->whereKey($cari)->delete();
        return back()->with('success','Cari kart silindi. Bağlı kayıtlarda cari bağlantısı korunarak boşaltıldı.');
    }

    public function fisler(Request $request)
    {
        $firmalar=$this->firmalar(); $firmaId=$this->firmaId($request); $yon=$request->input('yon'); $durum=$request->input('durum');
        $cariler=DB::table('cari_hesaplar')->where('firma_id',$firmaId)->where('aktif',true)->orderBy('unvan')->get();
        $fisler=DB::table('muhasebe_fisleri')
            ->leftJoin('cari_hesaplar', 'cari_hesaplar.id', '=', 'muhasebe_fisleri.cari_hesap_id')
            ->where('muhasebe_fisleri.firma_id',$firmaId)
            ->select('muhasebe_fisleri.*', 'cari_hesaplar.unvan as cari_unvan')
            ->when(in_array($yon,['gelir','gider'],true),fn($q)=>$q->where('muhasebe_fisleri.yon',$yon))->when($durum,fn($q)=>$q->where('muhasebe_fisleri.durum',$durum))->latest('muhasebe_fisleri.fis_tarihi')
            ->get();
        $fisSatirlari=DB::table('muhasebe_fis_satirlari')->whereIn('muhasebe_fis_id',$fisler->pluck('id'))->orderBy('id')->get()->groupBy('muhasebe_fis_id');
        $kdvGruplari=DB::table('kdv_urun_gruplari')->where('aktif',true)->orderBy('grup_adi')->get();
        return view('ticari.fisler-v3',compact('firmalar','firmaId','cariler','fisler','fisSatirlari','kdvGruplari','yon','durum'));
    }

    public function fisGuncelle(Request $request, int $fis)
    {
        $firmaId=$this->firmaId($request); $kayit=DB::table('muhasebe_fisleri')->whereKey($fis)->where('firma_id',$firmaId)->first(); abort_unless($kayit,404);
        $v=$request->validate(['fis_no'=>['required','string','max:80','unique:muhasebe_fisleri,fis_no,'.$fis],'cari_hesap_id'=>['nullable','integer'],'fis_tarihi'=>['required','date'],'aciklama'=>['nullable','string','max:1000'],'tutar'=>['required','numeric','gt:0']]);
        if(!empty($v['cari_hesap_id'])) abort_unless(DB::table('cari_hesaplar')->whereKey($v['cari_hesap_id'])->where('firma_id',$firmaId)->exists(),422);
        DB::transaction(function()use($kayit,$v,$fis){if($kayit->cari_hesap_id)DB::table('cari_hesaplar')->whereKey($kayit->cari_hesap_id)->increment('bakiye',$kayit->yon==='gelir'?-(float)$kayit->tutar:(float)$kayit->tutar);DB::table('muhasebe_fisleri')->whereKey($fis)->update([...$v,'updated_at'=>now()]);if($v['cari_hesap_id']??null)DB::table('cari_hesaplar')->whereKey($v['cari_hesap_id'])->increment('bakiye',$kayit->yon==='gelir'?(float)$v['tutar']:-(float)$v['tutar']);});
        return back()->with('success','Muhasebe fişi güncellendi.');
    }

    public function fisSil(Request $request, int $fis)
    {
        $firmaId=$this->firmaId($request); $kayit=DB::table('muhasebe_fisleri')->whereKey($fis)->where('firma_id',$firmaId)->first(); abort_unless($kayit,404);
        app(SilmeDenetimServisi::class)->tabloKaydiSilindi('Muhasebe Fişi',$kayit,$firmaId);
        DB::transaction(function()use($kayit,$fis){if($kayit->cari_hesap_id)DB::table('cari_hesaplar')->whereKey($kayit->cari_hesap_id)->increment('bakiye',$kayit->yon==='gelir'?-(float)$kayit->tutar:(float)$kayit->tutar);DB::table('muhasebe_fis_satirlari')->where('muhasebe_fis_id',$fis)->delete();DB::table('muhasebe_fisleri')->whereKey($fis)->delete();});
        return back()->with('success','Muhasebe fişi silindi ve cari bakiyesi düzeltildi.');
    }

    public function belgeler(Request $request, string $tur)
    {
        $this->yetki(); abort_unless(in_array($tur,['teklif','fatura'],true),404); $firmalar=$this->firmalar(); $firmaId=$this->firmaId($request); $arama=trim((string)$request->input('ara')); $durum=$request->input('durum'); $tablo=$tur==='teklif'?'teklifler':'faturalar';
        $q=DB::table($tablo)->leftJoin('cari_hesaplar','cari_hesaplar.id','=',$tablo.'.cari_hesap_id')->where($tablo.'.firma_id',$firmaId)->select($tablo.'.*','cari_hesaplar.plaka as cari_plaka'); if($arama!=='')$q->where(fn($x)=>$x->where($tablo.'.musteri_unvan','like',"%{$arama}%")->orWhere('cari_hesaplar.plaka','like',"%{$arama}%")->orWhere($tur==='teklif'?'teklif_no':'fatura_no','like',"%{$arama}%")); if($durum==='vadesi_gecmis'&&$tur==='fatura')$q->whereDate('vade_tarihi','<',today())->whereNotIn($tablo.'.durum',['odendi','iptal']);elseif($durum)$q->where($tablo.'.durum',$durum);
        $kayitlar=$q->latest('tarih')->get(); $cariler=DB::table('cari_hesaplar')->where('firma_id',$firmaId)->where('aktif',true)->orderBy('unvan')->get();
        return view('ticari.belgeler-v3',compact('tur','firmalar','firmaId','kayitlar','cariler','arama','durum'));
    }

    public function belgeKaydet(Request $request, string $tur)
    {
        abort_unless(in_array($tur,['teklif','fatura'],true),404); $id=$this->firmaId($request);
        $v=$request->validate(['cari_hesap_id'=>['nullable','exists:cari_hesaplar,id'],'musteri_unvan'=>['required','string','max:255'],'tarih'=>['required','date'],'gecerlilik_tarihi'=>['nullable','date'],'vade_tarihi'=>['nullable','date'],'para_birimi'=>['required','in:TRY,USD,EUR'],'notlar'=>['nullable','string','max:2000'],'satirlar'=>['required','array','min:1'],'satirlar.*.urun_hizmet_adi'=>['required','string','max:255'],'satirlar.*.miktar'=>['required','numeric','gt:0'],'satirlar.*.birim'=>['nullable','string','max:30'],'satirlar.*.birim_fiyat'=>['required','numeric','min:0'],'satirlar.*.iskonto_orani'=>['nullable','numeric','min:0','max:100'],'satirlar.*.kdv_orani'=>['required','numeric','in:1,5,8,10,20']]);
        if (($v['cari_hesap_id'] ?? null) && !DB::table('cari_hesaplar')->where('id',$v['cari_hesap_id'])->where('firma_id',$id)->exists()) abort(422,'Cari hesap seçilen firmaya ait değil.');
        $satirlar=collect($v['satirlar'])->map(function($s){$net=round((float)$s['miktar']*(float)$s['birim_fiyat']*(1-((float)($s['iskonto_orani']??0)/100)),2);$kdv=round($net*((float)$s['kdv_orani']/100),2);return [...$s,'net'=>$net,'kdv'=>$kdv,'dahil'=>round($net+$kdv,2)];}); $tablo=$tur==='teklif'?'teklifler':'faturalar'; $no=($tur==='teklif'?'TKL':'FTR').'-'.now()->format('YmdHis').'-'.random_int(10,99);
        DB::transaction(function()use($v,$id,$tur,$tablo,$no,$satirlar){$data=['firma_id'=>$id,'cari_hesap_id'=>$v['cari_hesap_id']??null,$tur==='teklif'?'teklif_no':'fatura_no'=>$no,'musteri_unvan'=>$v['musteri_unvan'],'tarih'=>$v['tarih'],'tutar'=>$satirlar->sum('dahil'),'ara_toplam'=>$satirlar->sum('net'),'kdv_toplam'=>$satirlar->sum('kdv'),'iskonto_toplam'=>0,'para_birimi'=>$v['para_birimi'],'durum'=>'taslak','notlar'=>$v['notlar']??null,'created_at'=>now(),'updated_at'=>now()]; if($tur==='teklif'){$data['gecerlilik_tarihi']=$v['gecerlilik_tarihi']??null;$data['aciklama']=$v['notlar']??null;}else{$data['vade_tarihi']=$v['vade_tarihi']??null;$data['entegrasyon_durumu']='gonderilmedi';}$belgeId=DB::table($tablo)->insertGetId($data);foreach($satirlar as $s)DB::table('ticari_belge_satirlari')->insert(['belge_turu'=>$tur,'belge_id'=>$belgeId,'urun_hizmet_adi'=>$s['urun_hizmet_adi'],'miktar'=>$s['miktar'],'birim'=>$s['birim']??'Adet','birim_fiyat'=>$s['birim_fiyat'],'iskonto_orani'=>$s['iskonto_orani']??0,'kdv_orani'=>$s['kdv_orani'],'kdv_haric_tutar'=>$s['net'],'kdv_tutari'=>$s['kdv'],'kdv_dahil_tutar'=>$s['dahil'],'created_at'=>now(),'updated_at'=>now()]);});
        return back()->with('success',($tur==='teklif'?'Teklif':'Fatura').' taslak olarak oluşturuldu.');
    }

    public function belgeGuncelle(Request $request, string $tur, int $belge)
    {
        abort_unless(in_array($tur,['teklif','fatura'],true),404); $firmaId=$this->firmaId($request); $tablo=$tur==='teklif'?'teklifler':'faturalar';
        abort_unless(DB::table($tablo)->whereKey($belge)->where('firma_id',$firmaId)->exists(),404);
        $v=$request->validate(['musteri_unvan'=>['required','string','max:255'],'tarih'=>['required','date'],'vade_tarihi'=>['nullable','date'],'durum'=>['required','string','max:30'],'notlar'=>['nullable','string','max:2000']]);
        if($tur==='teklif')unset($v['vade_tarihi']); DB::table($tablo)->whereKey($belge)->update([...$v,'updated_at'=>now()]);
        return back()->with('success',ucfirst($tur).' güncellendi.');
    }

    public function belgeSil(Request $request, string $tur, int $belge)
    {
        abort_unless(in_array($tur,['teklif','fatura'],true),404); $firmaId=$this->firmaId($request); $tablo=$tur==='teklif'?'teklifler':'faturalar'; $kayit=DB::table($tablo)->whereKey($belge)->where('firma_id',$firmaId)->first(); abort_unless($kayit,404);
        app(SilmeDenetimServisi::class)->tabloKaydiSilindi($tur==='fatura'?'Fatura':'Teklif',$kayit,$firmaId);
        DB::transaction(function()use($tur,$tablo,$belge){DB::table('ticari_belge_satirlari')->where(['belge_turu'=>$tur,'belge_id'=>$belge])->delete();DB::table($tablo)->whereKey($belge)->delete();});
        return back()->with('success',ucfirst($tur).' silindi.');
    }
}
