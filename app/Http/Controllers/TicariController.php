<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Services\FirmaIletisimGonderici;
use App\Services\MerkeziSistemMailGonderici;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class TicariController extends Controller
{
    private function yetki(): void { abort_unless(auth()->check() && (auth()->user()->tamSistemYetkisiVarMi() || auth()->user()->isAdmin() || auth()->user()->isMuhasebe()), 403); }
    private function firmaId(Request $request): int { $this->yetki(); $id=(int)$request->input('firma_id'); if(!auth()->user()->tamSistemYetkisiVarMi()) abort_unless($id === (int) auth()->user()->firmaPersoneli?->firma_id,403); return $id; }
    public function index(Request $request)
    {
        $firmalar = Firma::where('aktif', true)->orderBy('unvan')->get();
        $firmaId = $request->integer('firma_id') ?: $firmalar->first()?->id;
        $ozet = ['gelir' => 0, 'gider' => 0, 'cari' => 0, 'fis' => 0];
        $fisler = collect();
        if ($firmaId) {
            $ozet = [
                'gelir' => (float) DB::table('muhasebe_fisleri')->where('firma_id', $firmaId)->where('yon', 'gelir')->where('durum', 'onaylandi')->sum('tutar'),
                'gider' => (float) DB::table('muhasebe_fisleri')->where('firma_id', $firmaId)->where('yon', 'gider')->where('durum', 'onaylandi')->sum('tutar'),
                'cari' => DB::table('cari_hesaplar')->where('firma_id', $firmaId)->where('aktif', true)->count(),
                'fis' => DB::table('muhasebe_fisleri')->where('firma_id', $firmaId)->count(),
            ];
            $fisler = DB::table('muhasebe_fisleri')->where('firma_id', $firmaId)->latest('fis_tarihi')->limit(12)->get();
        }
        return view('ticari.index', compact('firmalar', 'firmaId', 'ozet', 'fisler'));
    }
    public function cariHesaplar(Request $request)
    {
        $this->yetki(); $firmalar=Firma::where('aktif',true)->orderBy('unvan')->get(); $firmaId=$this->seciliFirma($request,$firmalar);
        $cariler=DB::table('cari_hesaplar')->where('firma_id',$firmaId)->latest()->get();
        $musteriler=DB::table('musteris')->latest()->limit(100)->get();
        return view('ticari.cari-hesaplar',compact('firmalar','firmaId','cariler','musteriler'));
    }
    public function cariKaydet(Request $request)
    {
        $v=$request->validate(['firma_id'=>['required','exists:firmas,id'],'tip'=>['required','in:musteri,tedarikci,diger'],'unvan'=>['required','string','max:255'],'telefon'=>['nullable','string','max:30'],'email'=>['nullable','email','max:255'],'vergi_no'=>['nullable','string','max:20']]);$id=$this->firmaId($request);
        DB::table('cari_hesaplar')->insert(array_merge($v,['firma_id'=>$id,'bakiye'=>0,'aktif'=>true,'created_at'=>now(),'updated_at'=>now()]));return back()->with('success','Cari hesap oluşturuldu.');
    }
    public function fisler(Request $request)
    {
        $this->yetki(); $firmalar=Firma::where('aktif',true)->orderBy('unvan')->get();$firmaId=$this->seciliFirma($request,$firmalar);
        $cariler=DB::table('cari_hesaplar')->where('firma_id',$firmaId)->where('aktif',true)->orderBy('unvan')->get();$fisler=DB::table('muhasebe_fisleri')->leftJoin('muhasebe_fis_satirlari','muhasebe_fis_satirlari.muhasebe_fis_id','=','muhasebe_fisleri.id')->where('muhasebe_fisleri.firma_id',$firmaId)->select('muhasebe_fisleri.*','muhasebe_fis_satirlari.urun_adi','muhasebe_fis_satirlari.birim_fiyat','muhasebe_fis_satirlari.kdv_orani','muhasebe_fis_satirlari.kdv_dahil_tutar')->latest('fis_tarihi')->get();$kdvGruplari=DB::table('kdv_urun_gruplari')->where('aktif',true)->orderBy('grup_adi')->get();
        return view('ticari.fisler-v2',compact('firmalar','firmaId','cariler','fisler','kdvGruplari'));
    }
    public function fisKaydet(Request $request)
    {
        $v=$request->validate(['firma_id'=>['required','exists:firmas,id'],'fis_no'=>['nullable','string','max:80','unique:muhasebe_fisleri,fis_no'],'cari_hesap_id'=>['nullable','exists:cari_hesaplar,id'],'fis_tarihi'=>['required','date'],'aciklama'=>['nullable','string','max:1000'],'satirlar'=>['required','array','min:1'],'satirlar.*.urun_adi'=>['required','string','max:255'],'satirlar.*.adet'=>['nullable','numeric','gt:0'],'satirlar.*.birim'=>['nullable','in:Adet,Litre,Kilogram,Gram,Metre,Paket,Kutu,Çuval,Takım,Hizmet,Saat,Gün,Ay'],'satirlar.*.birim_fiyat'=>['required','numeric','gt:0'],'satirlar.*.kdv_orani'=>['required','numeric','min:0','max:100'],'satirlar.*.kdv_dahil_tutar'=>['required','numeric','gt:0']]);$v['yon']='gider';$v['tip']='Gider Fişi';$id=$this->firmaId($request);
        if($v['cari_hesap_id']) abort_unless(DB::table('cari_hesaplar')->where('id',$v['cari_hesap_id'])->where('firma_id',$id)->exists(),422,'Cari hesap seçilen firmaya ait değil.');
        DB::transaction(function()use($v,$id){$toplam=collect($v['satirlar'])->sum('kdv_dahil_tutar');$fisId=DB::table('muhasebe_fisleri')->insertGetId(['firma_id'=>$id,'cari_hesap_id'=>$v['cari_hesap_id'],'fis_no'=>$v['fis_no']?:'FIS-'.now()->format('YmdHis').'-'.random_int(10,99),'tip'=>$v['tip'],'fis_tarihi'=>$v['fis_tarihi'],'aciklama'=>$v['aciklama'],'tutar'=>$toplam,'yon'=>$v['yon'],'kaynak'=>'manuel','durum'=>'onaylandi','olusturan_id'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);foreach($v['satirlar'] as $satir){$dahil=(float)$satir['kdv_dahil_tutar'];$oran=(float)$satir['kdv_orani'];$haric=round($dahil/(1+$oran/100),2);DB::table('muhasebe_fis_satirlari')->insert(['muhasebe_fis_id'=>$fisId,'urun_adi'=>$satir['urun_adi'],'adet'=>$satir['adet']??1,'birim'=>$satir['birim']??'Adet','birim_fiyat'=>$satir['birim_fiyat'],'kdv_orani'=>$oran,'kdv_haric_tutar'=>$haric,'kdv_tutari'=>round($dahil-$haric,2),'kdv_dahil_tutar'=>$dahil,'created_at'=>now(),'updated_at'=>now()]);}if($v['cari_hesap_id'])DB::table('cari_hesaplar')->where('id',$v['cari_hesap_id'])->increment('bakiye',$v['yon']==='gelir'?$toplam:-$toplam);});
        return back()->with('success','Muhasebe fişi işlendi.');
    }
    private function seciliFirma(Request $request,$firmalar): ?int { $id=$request->integer('firma_id')?:$firmalar->first()?->id;if(!auth()->user()->tamSistemYetkisiVarMi())$id=auth()->user()->firmaPersoneli?->firma_id;return $id; }
    public function belgeler(Request $request,string $tur){$this->yetki();abort_unless(in_array($tur,['teklif','fatura'],true),404);$firmalar=Firma::where('aktif',true)->orderBy('unvan')->get();$firmaId=$request->integer('firma_id')?:$firmalar->first()?->id;if(!auth()->user()->tamSistemYetkisiVarMi())$firmaId=auth()->user()->firmaPersoneli?->firma_id;$kayitlar=DB::table($tur==='teklif'?'teklifler':'faturalar')->where('firma_id',$firmaId)->latest('tarih')->get();return view('ticari.belgeler',compact('tur','firmalar','firmaId','kayitlar'));}
    public function belgeKaydet(Request $request,string $tur){abort_unless(in_array($tur,['teklif','fatura'],true),404);$v=$request->validate(['firma_id'=>['required','exists:firmas,id'],'musteri_unvan'=>['required','string','max:255'],'tarih'=>['required','date'],'tutar'=>['required','numeric','min:0'],'aciklama'=>['nullable','string','max:2000']]);$id=$this->firmaId($request);$tablo=$tur==='teklif'?'teklifler':'faturalar';$no=($tur==='teklif'?'TKL':'FTR').'-'.now()->format('YmdHis');$kayit=array_merge($v,[$tur==='teklif'?'teklif_no':'fatura_no'=>$no,'durum'=>'taslak','created_at'=>now(),'updated_at'=>now()]);if($tur==='fatura'){$kayit['entegrasyon_durumu']='gonderilmedi';unset($kayit['aciklama']);}DB::table($tablo)->insert($kayit);return back()->with('success',ucfirst($tur).' kaydedildi.');}
    public function apiAyarlari(Request $request){$this->yetki();$firmalar=Firma::where('aktif',true)->orderBy('unvan')->get();$firmaId=$request->integer('firma_id')?:$firmalar->first()?->id;if(!auth()->user()->tamSistemYetkisiVarMi())$firmaId=auth()->user()->firmaPersoneli?->firma_id;$entegrasyonlar=DB::table('muhasebe_entegrasyonlari')->where('firma_id',$firmaId)->get()->keyBy('saglayici');$aiAyarlar=DB::table('yonetim_ayarlari')->where('grup','yapay_zeka')->pluck('deger','anahtar');$sistemEmailAyarlar=DB::table('yonetim_ayarlari')->where('grup','sistem_email')->pluck('deger','anahtar');$openAiGlobal=filled($aiAyarlar['api_anahtari']??null)||filled(config('services.izgios_ai.key'));$sistemEmailHazir=app(MerkeziSistemMailGonderici::class)->hazirMi();return view('ayarlar.api-ayarlari',compact('firmalar','firmaId','entegrasyonlar','openAiGlobal','aiAyarlar','sistemEmailAyarlar','sistemEmailHazir'));}

    public function merkeziEmailKaydet(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $veri = $request->validate([
            'smtp_host'=>['required','string','max:255'], 'smtp_port'=>['required','integer','between:1,65535'],
            'smtp_sifreleme'=>['required','in:ssl,tls,none'], 'kullanici_adi'=>['required','string','max:255'],
            'sifre'=>['nullable','string','max:2000'], 'gonderen'=>['required','email','max:255'],
            'gonderen_adi'=>['nullable','string','max:180'], 'bildirim_alicisi'=>['required','email','max:255'],
            'aktif'=>['nullable','boolean'],
        ]);
        $mevcutSifre=DB::table('yonetim_ayarlari')->where(['grup'=>'sistem_email','anahtar'=>'sifre'])->value('deger');
        $sifre=filled($veri['sifre']??null)?Crypt::encryptString($veri['sifre']):$mevcutSifre;
        abort_if(blank($sifre),422,'Sistem e-posta hesabı şifresini girin.');
        $ayarlar=['smtp_host'=>$veri['smtp_host'],'smtp_port'=>(string)$veri['smtp_port'],'smtp_sifreleme'=>$veri['smtp_sifreleme'],'kullanici_adi'=>$veri['kullanici_adi'],'sifre'=>$sifre,'gonderen'=>$veri['gonderen'],'gonderen_adi'=>$veri['gonderen_adi']??'İZGİOS Sistem Yönetimi','bildirim_alicisi'=>$veri['bildirim_alicisi'],'aktif'=>$request->boolean('aktif')?'1':'0'];
        foreach($ayarlar as $anahtar=>$deger) DB::table('yonetim_ayarlari')->updateOrInsert(['grup'=>'sistem_email','anahtar'=>$anahtar],['deger'=>$deger,'guncelleyen_id'=>auth()->id(),'updated_at'=>now(),'created_at'=>now()]);
        return back()->with('success','Sistem Yöneticisi e-posta entegrasyonu güncellendi.');
    }

    public function merkeziEmailTest(Request $request, MerkeziSistemMailGonderici $gonderici)
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(),403);
        try{$gonderici->bildirimGonder('İZGİOS sistem e-posta bağlantı testi','Merkezi sistem e-posta hesabı başarıyla doğrulandı. Sistem hataları, silme kayıtları ve destek bildirimleri bu adrese gönderilecektir.');}
        catch(Throwable $hata){report($hata);return back()->withErrors(['sistem_email'=>'Bağlantı kurulamadı: '.$hata->getMessage()]);}
        return back()->with('success','Sistem e-posta test mesajı gönderildi.');
    }

    public function merkeziYapayZekaKaydet(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $veri = $request->validate(['api_anahtari'=>['nullable','string','max:4000'],'model'=>['required','string','max:100'],'aktif'=>['nullable','boolean']]);
        $mevcut = DB::table('yonetim_ayarlari')->where(['grup'=>'yapay_zeka','anahtar'=>'api_anahtari'])->value('deger');
        $apiAnahtari = filled($veri['api_anahtari'] ?? null) ? Crypt::encryptString($veri['api_anahtari']) : $mevcut;
        abort_if(blank($apiAnahtari) && $request->boolean('aktif'), 422, 'Yapay zekâyı etkinleştirmek için API anahtarı girin.');
        foreach (['api_anahtari'=>$apiAnahtari,'model'=>$veri['model'],'aktif'=>$request->boolean('aktif')?'1':'0'] as $anahtar=>$deger) {
            DB::table('yonetim_ayarlari')->updateOrInsert(['grup'=>'yapay_zeka','anahtar'=>$anahtar], ['deger'=>$deger,'guncelleyen_id'=>auth()->id(),'updated_at'=>now(),'created_at'=>now()]);
        }
        return back()->with('success', 'Merkezi yapay zekâ bağlantısı tüm sistem için güncellendi.');
    }
    public function apiKaydet(Request $request)
    {
        $v = $request->validate([
            'firma_id' => ['required', 'exists:firmas,id'],
            'saglayici' => ['required', 'in:logo,parasut,efatura,gib,bankacilik,openai,whatsapp,sms,email'],
            'api_anahtari' => ['nullable', 'string', 'max:2000'],
            'endpoint' => ['nullable', 'url', 'max:500'],
            'gonderen' => ['nullable', 'string', 'max:180'],
            'kullanici_adi' => ['nullable', 'string', 'max:255'],
            'saglayici_turu' => ['nullable', 'string', 'max:60'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'between:1,65535'],
            'smtp_sifreleme' => ['nullable', 'in:ssl,tls,none'],
            'gonderen_adi' => ['nullable', 'string', 'max:180'],
        ]);

        $this->firmaId($request);
        $mevcut = DB::table('muhasebe_entegrasyonlari')->where([
            'firma_id' => $v['firma_id'],
            'saglayici' => $v['saglayici'],
        ])->first();

        $anahtar = filled($v['api_anahtari'])
            ? Crypt::encryptString($v['api_anahtari'])
            : $mevcut?->api_anahtari_sifreli;

        $ayarlar = array_filter([
            'endpoint' => $v['endpoint'] ?? null,
            'gonderen' => $v['gonderen'] ?? null,
            'kullanici_adi' => $v['kullanici_adi'] ?? null,
            'saglayici_turu' => $v['saglayici_turu'] ?? null,
            'smtp_host' => $v['smtp_host'] ?? null,
            'smtp_port' => $v['smtp_port'] ?? null,
            'smtp_sifreleme' => $v['smtp_sifreleme'] ?? null,
            'gonderen_adi' => $v['gonderen_adi'] ?? null,
        ], fn ($deger) => filled($deger));

        $tumAyarlar = array_replace(json_decode($mevcut?->ayarlar ?: '{}', true) ?: [], $ayarlar);
        $hazir = match ($v['saglayici']) {
            'email' => filled($anahtar) && filled($tumAyarlar['smtp_host'] ?? null) && filled($tumAyarlar['kullanici_adi'] ?? null) && filled($tumAyarlar['gonderen'] ?? null),
            'whatsapp', 'sms' => filled($anahtar)
                && filled($tumAyarlar['endpoint'] ?? null)
                && filled($tumAyarlar['gonderen'] ?? null),
            default => filled($anahtar),
        };

        DB::table('muhasebe_entegrasyonlari')->updateOrInsert(
            ['firma_id' => $v['firma_id'], 'saglayici' => $v['saglayici']],
            [
                'api_anahtari_sifreli' => $anahtar,
                'ayarlar' => json_encode($tumAyarlar, JSON_UNESCAPED_UNICODE),
                'aktif' => $hazir,
                'durum' => $hazir ? 'yapilandirildi' : 'yapilandirilmamis',
                'updated_at' => now(),
                'created_at' => $mevcut?->created_at ?: now(),
            ]
        );

        return back()->with('success', 'Entegrasyon bilgileri şifreli olarak kaydedildi.');
    }

    public function apiEmailTest(Request $request, FirmaIletisimGonderici $gonderici)
    {
        $request->validate(['firma_id' => ['required', 'exists:firmas,id']]);
        $firmaId = $this->firmaId($request);
        $entegrasyon = DB::table('muhasebe_entegrasyonlari')
            ->where('firma_id', $firmaId)
            ->where('saglayici', 'email')
            ->first();
        $ayarlar = json_decode($entegrasyon?->ayarlar ?: '{}', true) ?: [];

        if (! $entegrasyon?->aktif || blank($ayarlar['gonderen'] ?? null)) {
            return back()->withErrors(['email' => 'Önce gerekli SMTP bilgilerini kaydedin.']);
        }

        try {
            $gonderici->gonder((object) [
                'firma_id' => $firmaId,
                'kanal' => 'email',
                'alici' => $ayarlar['gonderen'],
                'mesaj' => 'E-posta bağlantınız başarıyla doğrulandı. Servis bildirimleri bu SMTP hesabı üzerinden gönderilecektir.',
            ], 'İZGİOS e-posta bağlantı testi');
        } catch (Throwable $exception) {
            report($exception);
            return back()->withErrors(['email' => 'SMTP bağlantısı kurulamadı: '.$exception->getMessage()]);
        }

        return back()->with('success', 'Test e-postası gönderildi. Gelen kutunuzu ve gereksiz klasörünü kontrol edin.');
    }
}
