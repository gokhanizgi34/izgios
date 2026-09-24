<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Services\FirmaIletisimGonderici;
use App\Services\MaasHesaplamaServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IkController extends Controller
{
    // İK verileri her işlemde aktif firma bağlantısı üzerinden filtrelenir.
    public function index(Request $request)
    {
        $this->yetki();
        $firmalar = $this->firmalar();
        $firmaId = $this->firmaId($request, $firmalar);
        $personeller = DB::table('firma_personels as fp')->join('users as u', 'u.id', '=', 'fp.user_id')->where('fp.firma_id', $firmaId)->where('fp.aktif', true)->select('u.id', 'u.name', 'u.surname', 'u.role', 'u.email', 'u.phone', 'u.dogum_tarihi', 'fp.sube_id')->orderBy('u.name')->get();
        $this->aylikBordrolariGuncelle($firmaId, $personeller->pluck('id')->all(), now());
        $ozlukler = DB::table('ik_personel_ozlukleri')->where('firma_id', $firmaId)->get()->keyBy('user_id');
        $bordrolar = DB::table('ik_bordrolar as b')->join('users as u', 'u.id', '=', 'b.user_id')->where('b.firma_id', $firmaId)->select('b.*', 'u.name', 'u.surname')->latest('donem')->limit(20)->get();
        $ozet = ['personel' => $personeller->count(), 'aktif_bordro' => $bordrolar->where('durum', 'onaylandi')->count(), 'net_toplam' => $bordrolar->where('donem', now()->startOfMonth()->toDateString())->sum('net_ucret'), 'mesai' => $bordrolar->where('donem', now()->startOfMonth()->toDateString())->sum('mesai_saati')];
        $puantajlar = DB::table('ik_puantaj_kayitlari as p')->join('users as u', 'u.id', '=', 'p.user_id')->where('p.firma_id', $firmaId)->select('p.*', 'u.name', 'u.surname')->latest('tarih')->limit(20)->get();
        $dosyalar = DB::table('ik_personel_dosyalari as d')->join('users as u', 'u.id', '=', 'd.user_id')->where('d.firma_id', $firmaId)->select('d.*', 'u.name', 'u.surname')->latest('d.id')->limit(20)->get();
        $egitimler = DB::table('ik_egitim_planlari as e')->join('users as u', 'u.id', '=', 'e.user_id')->where('e.firma_id', $firmaId)->select('e.*', 'u.name', 'u.surname')->orderBy('e.planlanan_tarih')->limit(20)->get();
        $performanslar = DB::table('ik_performans_degerlendirmeleri as p')->join('users as u', 'u.id', '=', 'p.user_id')->where('p.firma_id', $firmaId)->select('p.*', 'u.name', 'u.surname')->latest('p.donem_bitis')->limit(20)->get();
        $pozisyonlar = DB::table('ik_acik_pozisyonlar')->where('firma_id', $firmaId)->latest('id')->limit(20)->get();
        $basvurular = DB::table('ik_is_basvurulari as b')->leftJoin('ik_acik_pozisyonlar as p', 'p.id', '=', 'b.pozisyon_id')->where('b.firma_id', $firmaId)->select('b.*', 'p.pozisyon')->latest('b.id')->limit(30)->get();
        $izinler = DB::table('ik_izin_talepleri as i')->join('users as u', 'u.id', '=', 'i.user_id')->where('i.firma_id', $firmaId)->select('i.*', 'u.name', 'u.surname')->latest('i.baslangic_tarihi')->limit(20)->get();
        $aktifSekme = $request->string('sekme', 'genel')->toString();
        return view('ayarlar.ik.merkez-v2', compact('firmalar', 'firmaId', 'personeller', 'ozlukler', 'bordrolar', 'ozet', 'puantajlar', 'dosyalar', 'egitimler', 'performanslar', 'pozisyonlar', 'basvurular', 'izinler', 'aktifSekme'));
    }

    public function ozlukKaydet(Request $request)
    {
        $this->yetki(); $firmaId = $this->firmaId($request, $this->firmalar());
        if ($request->filled('islem')) {
            return $this->ikKaydiKaydet($request, $firmaId);
        }
        $v = $request->validate(['user_id' => ['required', 'integer'], 'ise_baslama_tarihi' => ['nullable', 'date'], 'unvan' => ['nullable', 'string', 'max:120'], 'brut_ucret' => ['nullable', 'numeric', 'min:0'], 'net_ucret' => ['nullable', 'numeric', 'min:0'], 'ucret_giris_turu' => ['nullable','in:brut,net'], 'saatlik_mesai_ucreti' => ['nullable', 'numeric', 'min:0'], 'calisma_baslangic' => ['nullable','date_format:H:i'], 'calisma_bitis' => ['nullable','date_format:H:i','after:calisma_baslangic'], 'gunluk_mola_dakika' => ['nullable','integer','min:0','max:300'], 'fazla_mesai_carpani' => ['nullable','numeric','min:1','max:3'], 'calisma_gunleri' => ['nullable','array'], 'calisma_gunleri.*' => ['integer','between:1,7'], 'notlar' => ['nullable', 'string', 'max:2000']]);
        abort_unless(DB::table('firma_personels')->where('firma_id', $firmaId)->where('user_id', $v['user_id'])->where('aktif', true)->exists(), 422, 'Personel seçilen firmaya bağlı değil.');
        $v = $this->ucretleriTamamla($v, $firmaId, (int) $v['user_id']);
        $v['calisma_gunleri'] = json_encode($v['calisma_gunleri'] ?? [1,2,3,4,5,6]);
        $mevcut = DB::table('ik_personel_ozlukleri')->where('firma_id',$firmaId)->where('user_id',$v['user_id'])->first();
        DB::table('ik_personel_ozlukleri')->updateOrInsert(['firma_id' => $firmaId, 'user_id' => $v['user_id']], array_merge($v, ['firma_id' => $firmaId, 'puantaj_qr_token' => $mevcut?->puantaj_qr_token ?: (string) Str::uuid(), 'puantaj_qr_yenilendi_at' => $mevcut?->puantaj_qr_yenilendi_at ?: now(), 'updated_at' => now(), 'created_at' => $mevcut?->created_at ?: now()]));
        $this->bordroHesapla($firmaId, $v['user_id'], now());
        return back()->with('success', 'Personel özlük ve ücret bilgileri kaydedildi; güncel ay bordrosu otomatik hesaplandı.');
    }

    public function bordroKaydet(Request $request)
    {
        $this->yetki(); $firmaId = $this->firmaId($request, $this->firmalar());
        $v = $request->validate(['user_id' => ['required', 'integer'], 'donem' => ['required', 'date'], 'brut_ucret' => ['required', 'numeric', 'min:0'], 'net_ucret' => ['required', 'numeric', 'min:0'], 'mesai_saati' => ['nullable', 'numeric', 'min:0'], 'mesai_tutari' => ['nullable', 'numeric', 'min:0'], 'hak_edis' => ['nullable', 'numeric', 'min:0'], 'avans' => ['nullable', 'numeric', 'min:0'], 'durum' => ['required', 'in:taslak,onaylandi,odendi'], 'aciklama' => ['nullable', 'string', 'max:2000']]);
        abort_unless(DB::table('firma_personels')->where('firma_id', $firmaId)->where('user_id', $v['user_id'])->where('aktif', true)->exists(), 422, 'Personel seçilen firmaya bağlı değil.');
        $v['donem'] = date('Y-m-01', strtotime($v['donem']));
        DB::table('ik_bordrolar')->updateOrInsert(['firma_id' => $firmaId, 'user_id' => $v['user_id'], 'donem' => $v['donem']], array_merge($v, ['firma_id' => $firmaId, 'olusturan_id' => auth()->id(), 'updated_at' => now(), 'created_at' => now()]));
        return back()->with('success', 'Bordro, mesai ve hak ediş bilgileri kaydedildi.');
    }

    private function yetki(): void { abort_unless(auth()->check() && auth()->user()->ikErisimiVarMi(), 403); }
    private function firmalar() { return auth()->user()->tamSistemYetkisiVarMi() ? Firma::where('aktif', true)->orderBy('unvan')->get() : Firma::where('id', auth()->user()->firmaPersoneli?->firma_id)->where('aktif', true)->get(); }
    private function firmaId(Request $request, $firmalar): int { $firmaId = $request->integer('firma_id') ?: $firmalar->first()?->id; abort_unless($firmaId && $firmalar->contains('id', $firmaId), 403); return $firmaId; }

    private function ikKaydiKaydet(Request $request, int $firmaId)
    {
        $islem = $request->string('islem')->toString();
        $kurallar = [
            'puantaj' => ['user_id' => ['required','integer'], 'tarih' => ['required','date'], 'giris_saati' => ['nullable','date_format:H:i'], 'cikis_saati' => ['nullable','date_format:H:i'], 'mesai_saati' => ['nullable','numeric','min:0'], 'durum' => ['required','in:calisti,izinli,raporlu,devamsiz,resmi_tatil'], 'aciklama' => ['nullable','string','max:500']],
            'ozel_gun' => ['user_id' => ['required','integer'], 'tur' => ['required','in:dogum_gunu,ise_baslama,evlilik_yildonumu,cocuk_dogum_gunu,diger'], 'baslik' => ['required','string','max:160'], 'tarih' => ['required','date'], 'hatirlatma_gun_once' => ['nullable','integer','min:0','max:60']],
            'dosya' => ['user_id' => ['required','integer'], 'kategori' => ['required','string','max:60'], 'baslik' => ['required','string','max:160'], 'dosya' => ['required','file','max:10240'], 'gecerlilik_tarihi' => ['nullable','date']],
            'egitim' => ['user_id' => ['required','integer'], 'egitim_adi' => ['required','string','max:180'], 'egitim_turu' => ['nullable','string','max:80'], 'planlanan_tarih' => ['nullable','date'], 'durum' => ['required','in:planlandi,devam_ediyor,tamamlandi,iptal'], 'notlar' => ['nullable','string','max:2000']],
            'performans' => ['user_id' => ['required','integer'], 'donem_baslangic' => ['required','date'], 'donem_bitis' => ['required','date','after_or_equal:donem_baslangic'], 'puan' => ['nullable','numeric','min:0','max:100'], 'guclu_yonler' => ['nullable','string','max:2000'], 'gelisim_alanlari' => ['nullable','string','max:2000'], 'hedefler' => ['nullable','string','max:2000']],
            'pozisyon' => ['birim' => ['required','string','max:120'], 'pozisyon' => ['required','string','max:160'], 'ihtiyac_adedi' => ['required','integer','min:1','max:999'], 'acilis_tarihi' => ['required','date'], 'son_basvuru_tarihi' => ['nullable','date','after_or_equal:acilis_tarihi'], 'durum' => ['required','in:acik,beklemede,kapali'], 'gorev_tanimi' => ['nullable','string','max:3000']],
            'basvuru' => ['pozisyon_id' => ['nullable','integer'], 'aday_adi' => ['required','string','max:160'], 'email' => ['nullable','email','max:160'], 'telefon' => ['nullable','string','max:40'], 'kaynak' => ['nullable','string','max:100'], 'asama' => ['required','in:basvuru,on_gorusme,teknik_gorusme,referans,teklif,ise_alindi,olumsuz'], 'notlar' => ['nullable','string','max:3000']],
            'izin' => ['user_id' => ['required','integer'], 'izin_turu' => ['required','string','max:80'], 'baslangic_tarihi' => ['required','date'], 'bitis_tarihi' => ['required','date','after_or_equal:baslangic_tarihi'], 'gun_sayisi' => ['required','numeric','min:0.5','max:365'], 'aciklama' => ['nullable','string','max:1500']],
        ];
        abort_unless(array_key_exists($islem, $kurallar), 422, 'Geçersiz İK işlemi.');
        $veri = $request->validate($kurallar[$islem]);
        if (isset($veri['user_id'])) {
            abort_unless(DB::table('firma_personels')->where('firma_id', $firmaId)->where('user_id', $veri['user_id'])->where('aktif', true)->exists(), 422, 'Personel seçilen firmaya bağlı değil.');
        }

        if ($islem === 'puantaj') {
            DB::table('ik_puantaj_kayitlari')->updateOrInsert(['firma_id'=>$firmaId,'user_id'=>$veri['user_id'],'tarih'=>$veri['tarih']], array_merge($veri, ['firma_id'=>$firmaId,'updated_at'=>now(),'created_at'=>now()]));
        } elseif ($islem === 'ozel_gun') {
            DB::table('ik_ozel_gunler')->insert(array_merge($veri, ['firma_id'=>$firmaId,'hatirlatma_aktif'=>true,'hatirlatma_gun_once'=>$veri['hatirlatma_gun_once'] ?? 1,'created_at'=>now(),'updated_at'=>now()]));
        } elseif ($islem === 'dosya') {
            $dosyaYolu = $request->file('dosya')->store('ik-personel-dosyalari/'.$firmaId, 'public');
            DB::table('ik_personel_dosyalari')->insert(['firma_id'=>$firmaId,'user_id'=>$veri['user_id'],'kategori'=>$veri['kategori'],'baslik'=>$veri['baslik'],'dosya_yolu'=>$dosyaYolu,'gecerlilik_tarihi'=>$veri['gecerlilik_tarihi'] ?? null,'yukleyen_id'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);
        } elseif ($islem === 'egitim') {
            DB::table('ik_egitim_planlari')->insert(array_merge($veri, ['firma_id'=>$firmaId,'created_at'=>now(),'updated_at'=>now()]));
        } elseif ($islem === 'performans') {
            DB::table('ik_performans_degerlendirmeleri')->insert(array_merge($veri, ['firma_id'=>$firmaId,'degerlendiren_id'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]));
        } elseif ($islem === 'pozisyon') {
            DB::table('ik_acik_pozisyonlar')->insert(array_merge($veri, ['firma_id'=>$firmaId,'created_at'=>now(),'updated_at'=>now()]));
        } elseif ($islem === 'basvuru') {
            if (!empty($veri['pozisyon_id'])) {
                abort_unless(DB::table('ik_acik_pozisyonlar')->where('firma_id', $firmaId)->where('id', $veri['pozisyon_id'])->exists(), 422, 'Pozisyon seçilen firmaya bağlı değil.');
            }
            DB::table('ik_is_basvurulari')->insert(array_merge($veri, ['firma_id'=>$firmaId,'created_at'=>now(),'updated_at'=>now()]));
        } else {
            DB::table('ik_izin_talepleri')->insert(array_merge($veri, ['firma_id'=>$firmaId,'created_at'=>now(),'updated_at'=>now()]));
        }

        if ($islem === 'puantaj') {
            $this->bordroHesapla($firmaId, $veri['user_id'], Carbon::parse($veri['tarih']));
        }

        if ($islem === 'izin') {
            $donem = Carbon::parse($veri['baslangic_tarihi'])->startOfMonth();
            $bitisDonemi = Carbon::parse($veri['bitis_tarihi'])->startOfMonth();
            while ($donem->lte($bitisDonemi)) {
                $this->bordroHesapla($firmaId, $veri['user_id'], $donem);
                $donem->addMonth();
            }
        }

        return back()->with('success', 'İK kaydı firma bazında kaydedildi; ilgili bordro hesabı güncellendi.');
    }

    private function aylikBordrolariGuncelle(int $firmaId, array $personelIds, $donem): void
    {
        foreach ($personelIds as $personelId) {
            $this->bordroHesapla($firmaId, $personelId, $donem);
        }
    }

    private function ucretleriTamamla(array $veri, int $firmaId, int $userId): array
    {
        $tur = $veri['ucret_giris_turu'] ?? (filled($veri['net_ucret'] ?? null) ? 'net' : 'brut');
        unset($veri['ucret_giris_turu']);
        if (!filled($veri[$tur.'_ucret'] ?? null)) return $veri;

        $oncekiMatrah = (float) DB::table('ik_bordrolar')->where('firma_id',$firmaId)->where('user_id',$userId)->whereYear('donem',now()->year)->whereDate('donem','<',now()->startOfMonth()->toDateString())->sum('gelir_vergisi_matrahi');
        $hesaplayici = app(MaasHesaplamaServisi::class);
        $hesap = $tur === 'net'
            ? $hesaplayici->nettenBrute((float)$veri['net_ucret'], $oncekiMatrah)
            : $hesaplayici->bruttenNete((float)$veri['brut_ucret'], $oncekiMatrah);
        $veri['brut_ucret'] = $hesap['brut'];
        $veri['net_ucret'] = $hesap['net'];
        return $veri;
    }

    private function bordroHesapla(int $firmaId, int $userId, $donem): void
    {
        $ayBaslangic = Carbon::parse($donem)->startOfMonth();
        $ayBitis = $ayBaslangic->copy()->endOfMonth();
        $ozluk = DB::table('ik_personel_ozlukleri')->where('firma_id', $firmaId)->where('user_id', $userId)->first();
        $mevcutBordro = DB::table('ik_bordrolar')->where('firma_id', $firmaId)->where('user_id', $userId)->where('donem', $ayBaslangic->toDateString())->first();
        $esasNet = (float) ($ozluk->net_ucret ?? 0);
        $brut = (float) ($ozluk->brut_ucret ?? 0);

        $eksikTarihler = DB::table('ik_puantaj_kayitlari')
            ->where('firma_id', $firmaId)->where('user_id', $userId)
            ->whereBetween('tarih', [$ayBaslangic->toDateString(), $ayBitis->toDateString()])
            ->whereIn('durum', ['izinli', 'raporlu', 'devamsiz'])
            ->pluck('tarih')->map(fn ($tarih) => Carbon::parse($tarih)->toDateString())->all();

        $izinler = DB::table('ik_izin_talepleri')
            ->where('firma_id', $firmaId)->where('user_id', $userId)
            ->whereNotIn('durum', ['reddedildi', 'iptal'])
            ->whereDate('baslangic_tarihi', '<=', $ayBitis->toDateString())
            ->whereDate('bitis_tarihi', '>=', $ayBaslangic->toDateString())
            ->get();
        foreach ($izinler as $izin) {
            $gun = Carbon::parse($izin->baslangic_tarihi)->startOfDay();
            $sonGun = Carbon::parse($izin->bitis_tarihi)->startOfDay();
            if ($gun->lt($ayBaslangic)) {
                $gun = $ayBaslangic->copy()->startOfDay();
            }
            if ($sonGun->gt($ayBitis)) {
                $sonGun = $ayBitis->copy()->startOfDay();
            }
            while ($gun->lte($sonGun)) {
                $eksikTarihler[] = $gun->toDateString();
                $gun->addDay();
            }
        }

        $eksikGun = min(30, count(array_unique($eksikTarihler)));
        $calisilanGun = max(0, 30 - $eksikGun);
        $mesaiSaati = (float) DB::table('ik_puantaj_kayitlari')
            ->where('firma_id', $firmaId)->where('user_id', $userId)
            ->whereBetween('tarih', [$ayBaslangic->toDateString(), $ayBitis->toDateString()])
            ->sum('mesai_saati');
        $saatlikMesai = (float) ($ozluk->saatlik_mesai_ucreti ?? 0);
        if ($saatlikMesai <= 0 && $esasNet > 0) {
            $saatlikMesai = round($esasNet / 240, 2);
        }
        $kesinti = round(($esasNet / 30) * $eksikGun, 2);
        $mesaiTutari = round($mesaiSaati * $saatlikMesai * (float) ($ozluk->fazla_mesai_carpani ?? 1.5), 2);
        $primeEsas = max(0, $brut - round(($brut / 30) * $eksikGun, 2) + $mesaiTutari);
        $sgkIsci = round($primeEsas * .14, 2);
        $issizlikIsci = round($primeEsas * .01, 2);
        $vergiMatrahi = max(0, $primeEsas - $sgkIsci - $issizlikIsci);
        $oncekiMatrah = (float) DB::table('ik_bordrolar')->where('firma_id',$firmaId)->where('user_id',$userId)->whereYear('donem',$ayBaslangic->year)->whereDate('donem','<',$ayBaslangic->toDateString())->sum('gelir_vergisi_matrahi');
        $gelirVergisi = $this->gelirVergisi($oncekiMatrah + $vergiMatrahi) - $this->gelirVergisi($oncekiMatrah);
        $damgaVergisi = round($primeEsas * .00759, 2);
        $toplamKesinti = round($sgkIsci + $issizlikIsci + $gelirVergisi + $damgaVergisi + $kesinti, 2);
        $sgkIsveren = round($primeEsas * .2175, 2);
        $issizlikIsveren = round($primeEsas * .02, 2);
        $odenecekNet = $brut > 0 ? max(0, round($primeEsas - $sgkIsci - $issizlikIsci - $gelirVergisi - $damgaVergisi, 2)) : max(0, round($esasNet - $kesinti + $mesaiTutari, 2));

        DB::table('ik_bordrolar')->updateOrInsert(
            ['firma_id' => $firmaId, 'user_id' => $userId, 'donem' => $ayBaslangic->toDateString()],
            [
                'brut_ucret' => $brut,
                'sgk_isci' => $sgkIsci,
                'issizlik_isci' => $issizlikIsci,
                'gelir_vergisi_matrahi' => $vergiMatrahi,
                'gelir_vergisi' => $gelirVergisi,
                'damga_vergisi' => $damgaVergisi,
                'sgk_isveren' => $sgkIsveren,
                'issizlik_isveren' => $issizlikIsveren,
                'toplam_kesinti' => $toplamKesinti,
                'isveren_maliyeti' => round($primeEsas + $sgkIsveren + $issizlikIsveren, 2),
                'net_ucret' => $odenecekNet,
                'esas_net_ucret' => $esasNet,
                'calisilan_gun' => $calisilanGun,
                'eksik_gun' => $eksikGun,
                'eksik_gun_kesintisi' => $kesinti,
                'odenecek_net' => $odenecekNet,
                'mesai_saati' => $mesaiSaati,
                'mesai_tutari' => $mesaiTutari,
                'hak_edis' => $mesaiTutari,
                'avans' => (float) ($mevcutBordro->avans ?? 0),
                'durum' => $mevcutBordro->durum ?? 'taslak',
                'aciklama' => 'Puantaj, eksik gün, fazla mesai ve dönemsel yasal kesinti kalemleri otomatik hesaplandı. Onay öncesi mali müşavir kontrolü gerekir.',
                'olusturan_id' => $mevcutBordro->olusturan_id ?? auth()->id(),
                'updated_at' => now(),
                'created_at' => $mevcutBordro->created_at ?? now(),
            ]
        );
    }

    public function puantajQr(Request $request, int $user)
    {
        $this->yetki(); $firmaId=$this->firmaId($request,$this->firmalar());
        $personel=DB::table('firma_personels as fp')->join('users as u','u.id','=','fp.user_id')->join('ik_personel_ozlukleri as o',function($j){$j->on('o.user_id','=','u.id')->on('o.firma_id','=','fp.firma_id');})->where('fp.firma_id',$firmaId)->where('u.id',$user)->select('u.id','u.name','u.surname','o.puantaj_qr_token')->first();
        abort_unless($personel,404);
        if (!$personel->puantaj_qr_token) { $token=(string)Str::uuid(); DB::table('ik_personel_ozlukleri')->where('firma_id',$firmaId)->where('user_id',$user)->update(['puantaj_qr_token'=>$token,'puantaj_qr_yenilendi_at'=>now()]); $personel->puantaj_qr_token=$token; }
        $qrCode=QrCode::size(360)->margin(2)->generate(route('ik.puantaj.qr.okut',$personel->puantaj_qr_token));
        return view('ayarlar.ik.puantaj-qr',compact('personel','qrCode','firmaId'));
    }

    public function qrOkut(string $token)
    {
        $personel=DB::table('ik_personel_ozlukleri as o')->join('users as u','u.id','=','o.user_id')->join('firmas as f','f.id','=','o.firma_id')->where('o.puantaj_qr_token',$token)->select('o.*','u.name','u.surname','f.unvan as firma_adi')->firstOrFail();
        return view('ayarlar.ik.puantaj-okut',compact('personel','token'));
    }

    public function qrKaydet(Request $request, string $token)
    {
        $personel=DB::table('ik_personel_ozlukleri')->where('puantaj_qr_token',$token)->firstOrFail();
        abort_if(DB::table('ik_puantaj_hareketleri')->where('firma_id',$personel->firma_id)->where('user_id',$personel->user_id)->where('created_at','>',now()->subMinute())->exists(),429,'Aynı QR bir dakika içinde tekrar kullanılamaz.');
        $bugun=now()->toDateString(); $saat=now()->format('H:i:s');
        $puantaj=DB::table('ik_puantaj_kayitlari')->where('firma_id',$personel->firma_id)->where('user_id',$personel->user_id)->where('tarih',$bugun)->first();
        $hareket=(!$puantaj || !$puantaj->giris_saati) ? 'giris' : 'cikis';
        if ($hareket==='giris') {
            DB::table('ik_puantaj_kayitlari')->updateOrInsert(['firma_id'=>$personel->firma_id,'user_id'=>$personel->user_id,'tarih'=>$bugun],['giris_saati'=>$saat,'durum'=>'calisti','kaynak'=>'qr','giris_ip'=>$request->ip(),'updated_at'=>now(),'created_at'=>$puantaj?->created_at ?: now()]);
        } else {
            abort_if($puantaj->cikis_saati,422,'Bugünkü giriş ve çıkış daha önce tamamlandı.');
            $giris=Carbon::parse($bugun.' '.$puantaj->giris_saati); $cikis=now();
            $calisma=max(0,round(($giris->diffInMinutes($cikis)-(int)$personel->gunluk_mola_dakika)/60,2));
            $plan=max(0,round((Carbon::parse($bugun.' '.$personel->calisma_baslangic)->diffInMinutes(Carbon::parse($bugun.' '.$personel->calisma_bitis))-(int)$personel->gunluk_mola_dakika)/60,2));
            DB::table('ik_puantaj_kayitlari')->where('id',$puantaj->id)->update(['cikis_saati'=>$saat,'calisma_saati'=>$calisma,'mesai_saati'=>max(0,$calisma-$plan),'cikis_ip'=>$request->ip(),'updated_at'=>now()]);
            $this->bordroHesapla((int)$personel->firma_id,(int)$personel->user_id,now());
        }
        DB::table('ik_puantaj_hareketleri')->insert(['firma_id'=>$personel->firma_id,'user_id'=>$personel->user_id,'hareket'=>$hareket,'kayit_zamani'=>now(),'kaynak'=>'qr','ip_adresi'=>$request->ip(),'cihaz'=>Str::limit((string)$request->userAgent(),500,''),'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success', $hareket==='giris' ? 'İşe giriş kaydedildi.' : 'İşten çıkış ve mesai süresi kaydedildi.');
    }

    public function bordroYazdir(Request $request, int $bordro)
    {
        $this->yetki(); $firmalar=$this->firmalar(); $firmaId=$this->firmaId($request,$firmalar);
        $kayit=DB::table('ik_bordrolar as b')->join('users as u','u.id','=','b.user_id')->join('firmas as f','f.id','=','b.firma_id')->where('b.id',$bordro)->where('b.firma_id',$firmaId)->select('b.*','u.name','u.surname','u.email','u.phone','f.unvan as firma_adi')->firstOrFail();
        return view('ayarlar.ik.bordro',compact('kayit'));
    }

    public function bordroGonder(Request $request, int $bordro, FirmaIletisimGonderici $gonderici)
    {
        abort_unless(auth()->user()?->tamSistemYetkisiVarMi(),403);
        $v=$request->validate(['kanal'=>['required','in:email,whatsapp,sms']]);
        $kayit=DB::table('ik_bordrolar as b')->join('users as u','u.id','=','b.user_id')->where('b.id',$bordro)->select('b.*','u.name','u.surname','u.email','u.phone')->firstOrFail();
        $alici=$v['kanal']==='email'?$kayit->email:$kayit->phone; abort_if(blank($alici),422,'Personelin seçilen kanal için iletişim bilgisi yok.');
        $link=route('ik.bordro.yazdir',['bordro'=>$kayit->id,'firma_id'=>$kayit->firma_id]);
        $mesaj=Carbon::parse($kayit->donem)->format('m.Y')." dönemi bordronuz hazır. Net ödeme: ₺".number_format($kayit->odenecek_net,2,',','.').". Bordro: {$link}";
        if ($v['kanal']==='whatsapp') return redirect()->away('https://wa.me/'.preg_replace('/\D+/','',$alici).'?text='.rawurlencode($mesaj));
        if ($v['kanal']==='sms') return redirect()->away('sms:'.preg_replace('/\D+/','',$alici).'?body='.rawurlencode($mesaj));
        $gonderici->gonder((object)['firma_id'=>$kayit->firma_id,'kanal'=>'email','alici'=>$alici,'mesaj'=>$mesaj],'Aylık ücret bordrosu');
        return back()->with('success','Bordro e-posta ile gönderildi.');
    }

    private function gelirVergisi(float $matrah): float
    {
        $dilimler=[[190000,.15],[400000,.20],[1500000,.27],[5300000,.35],[INF,.40]]; $vergi=0; $onceki=0;
        foreach($dilimler as [$sinir,$oran]) { $tutar=min($matrah,$sinir)-$onceki; if($tutar>0)$vergi+=$tutar*$oran; if($matrah<=$sinir)break; $onceki=$sinir; }
        return round($vergi,2);
    }
}
