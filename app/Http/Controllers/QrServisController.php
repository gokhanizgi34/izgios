<?php

namespace App\Http\Controllers;


use App\Models\Arac;
use App\Models\ServisFotograf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\MusteriIletisimIzinServisi;



class QrServisController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | QR Servis Görüntüleme
    |--------------------------------------------------------------------------
    */


    public function show(Request $request, $token)
    {



        $arac = Arac::with([


            'musteri',


            'servisler' => function($query){

                $query->orderBy(
                    'created_at',
                    'desc'
                );

            },


            'servisler.fotograflar',


            'servisler.islemler',


            'servisler.parcalar'

            ,'servisler.sube'


        ])

        ->where(
            'qr_token',
            $token
        )

        ->firstOrFail();





        /*
        |--------------------------------------------------------------------------
        | Müşteri Bilgileri
        |--------------------------------------------------------------------------
        */


        $musteri = null;



        if($arac->musteri)
        {


            $musteri = [


                'ad_soyad' => $this->isimMaskele(

                    $arac->musteri->ad_soyad

                ),



                'telefon' => $this->telefonMaskele(

                    $arac->musteri->telefon

                ),



            ];


        }






        /*
        |--------------------------------------------------------------------------
        | Bir Sonraki Bakım
        |--------------------------------------------------------------------------
        */


        $sonrakiBakim = $arac
            ->servisler
            ->whereNotNull(
                'sonraki_bakim_tarihi'
            )
            ->sortBy(
                'sonraki_bakim_tarihi'
            )
            ->first();



        /*
        |--------------------------------------------------------------------------
        | QR Sayfası
        |--------------------------------------------------------------------------
        */
        $kullanici = auth()->user();
        $aktifFirmaId = session('aktif_firma_id') ?: $kullanici?->firmaPersoneli?->firma_id;
        $hizliIslemYetkisi = $kullanici
            && ($kullanici->isUsta() || $kullanici->isAdmin())
            && $aktifFirmaId
            && (int) $aktifFirmaId === (int) $arac->firma_id;
        $detayYetkisi = $hizliIslemYetkisi || $request->session()->get('qr_detay_'.$arac->qr_token) === true;

        $periyodikBakimlar = $arac->servisler
            ->flatMap(fn ($servis) => $servis->islemler
                ->where('kategori', 'periyodik_bakim')
                ->map(fn ($islem) => ['servis' => $servis, 'islem' => $islem]))
            ->groupBy(fn ($kayit) => (int) ($kayit['servis']->giris_km ?? 0))
            ->map(function ($kayitlar, $km) {
                $servisler = $kayitlar->pluck('servis')->unique('id');
                return [
                    'km' => (int) $km,
                    'tarih' => $servisler->max(fn ($servis) => $servis->servis_tarihi ?? $servis->created_at),
                    'islemler' => $kayitlar->pluck('islem')->values(),
                ];
            })
            ->sortByDesc('km')
            ->values();

        // QR servis sekmesi yalnızca iş emrindeki "Yapılan İşlemler"
        // kayıtlarından, bakım sekmesi ise yalnız periyodik bakım kayıtlarından beslenir.
        $servisIslemleri = $arac->servisler
            ->flatMap(fn ($servis) => $servis->islemler
                ->where('kategori', '!=', 'periyodik_bakim')
                ->map(fn ($islem) => ['servis' => $servis, 'islem' => $islem]))
            ->groupBy(fn ($kayit) => (int) ($kayit['servis']->giris_km ?? 0))
            ->map(function ($kayitlar, $km) {
                $servisler = $kayitlar->pluck('servis')->unique('id');
                return [
                    'km' => (int) $km,
                    'tarih' => $servisler->max(fn ($servis) => $servis->servis_tarihi ?? $servis->created_at),
                    'servis_nolari' => $servisler->pluck('servis_no')->filter()->unique()->implode(' · '),
                    'islemler' => $kayitlar->pluck('islem')->values(),
                ];
            })
            ->sortByDesc('km')
            ->values();

        $sonServis = $arac->servisler->first();
        $firmaId = (int) ($sonServis?->firma_id ?: $arac->firma_id);
        $whatsappEntegrasyonu = DB::table('muhasebe_entegrasyonlari')
            ->where('firma_id', $firmaId)
            ->where('saglayici', 'whatsapp')
            ->where('aktif', true)
            ->first();
        $whatsappAyarlari = json_decode($whatsappEntegrasyonu?->ayarlar ?: '{}', true) ?: [];
        $firmaTelefonu = DB::table('firmas')->where('id', $firmaId)->value('telefon');
        $whatsappNo = preg_replace('/\D+/', '', (string) ($sonServis?->sube?->whatsapp_no ?: ($whatsappAyarlari['gonderen'] ?? $firmaTelefonu)));
        if (str_starts_with($whatsappNo, '0')) {
            $whatsappNo = '90'.substr($whatsappNo, 1);
        }
        $whatsappMesaj = "Merhaba, {$arac->plaka} plakalı aracımla ilgili servis kaydı hakkında bilgi almak istiyorum.";
        $whatsappUrl = strlen($whatsappNo) >= 10
            ? 'https://wa.me/'.$whatsappNo.'?text='.rawurlencode($whatsappMesaj)
            : null;

        $fotoGruplari = $arac->servisler->map(function ($servis) {
            return [
                'km' => (int) ($servis->giris_km ?? 0),
                'tarih' => $servis->servis_tarihi ?? $servis->created_at,
                'servis_no' => $servis->servis_no,
                'servis' => $servis->fotograflar->where('kategori', '!=', 'bakim')->values(),
                'bakim' => $servis->fotograflar->where('kategori', 'bakim')->values(),
            ];
        })->filter(fn ($grup) => $grup['servis']->isNotEmpty() || $grup['bakim']->isNotEmpty())->values();

        $iletisimIzni = app(MusteriIletisimIzinServisi::class)->izinKaydi($firmaId, $arac->musteri_id);

        return view(

            'qr.musteri-servis-v4',

            compact(

                'arac',

                'musteri',

                'sonrakiBakim',
                'periyodikBakimlar',
                'servisIslemleri',
                'whatsappUrl',
                'hizliIslemYetkisi',
                'detayYetkisi',
                'fotoGruplari'
                ,'iletisimIzni'

            )

        );


    }

    public function iletisimIzniKaydet(Request $request, string $token, MusteriIletisimIzinServisi $izinServisi)
    {
        $request->validate([
            'servis_iletisim_izni' => ['accepted'],
            'ticari_iletisim_izni' => ['nullable', 'boolean'],
        ], [
            'servis_iletisim_izni.accepted' => 'Servis ekranına devam etmek için servis iletişimi izni gereklidir.',
        ]);
        $arac = Arac::with('musteri')->where('qr_token', $token)->firstOrFail();
        $izinServisi->kaydet($request, $arac, $request->boolean('servis_iletisim_izni'), $request->boolean('ticari_iletisim_izni'));

        return redirect()->route('qr.servis.show', ['token' => $token, 'ekran' => $request->input('ekran', 'servis')])
            ->with('izin_basarili', 'İletişim tercihleriniz tarih ve saat bilgisiyle kaydedildi.');
    }

    public function acikRizaMetni(string $token, string $tur)
    {
        $arac = Arac::where('qr_token', $token)->firstOrFail();
        $ozet = $tur === 'ticari' ? MusteriIletisimIzinServisi::TICARI_METIN : MusteriIletisimIzinServisi::SERVIS_METNI;
        $metin = $tur === 'ticari' ? MusteriIletisimIzinServisi::TICARI_HUKUKI_METIN : MusteriIletisimIzinServisi::SERVIS_HUKUKI_METIN;
        $baslik = $tur === 'ticari' ? 'Ticari İletişim Açık Rıza Metni' : 'Servis İletişimi Açık Rıza Metni';

        return view('qr.acik-riza', compact('arac', 'baslik', 'ozet', 'metin'));
    }

    public function sifreDogrula(Request $request, string $token, MusteriIletisimIzinServisi $izinServisi)
    {
        $arac = Arac::where('qr_token', $token)->firstOrFail();
        $kullanici = auth()->user();
        $aktifFirmaId = session('aktif_firma_id') ?: $kullanici?->firmaPersoneli?->firma_id;
        $personelYetkili = $kullanici
            && ($kullanici->isUsta() || $kullanici->isAdmin())
            && $aktifFirmaId
            && (int) $aktifFirmaId === (int) $arac->firma_id;
        if (! $personelYetkili && ! $izinServisi->izinliMi($arac->firma_id, $arac->musteri_id, 'servis')) {
            return back()->withErrors(['servis_iletisim_izni' => 'Servis ekranına devam etmek için önce servis iletişimi izni verilmelidir.']);
        }
        $veri = $request->validate(['sifre' => ['required', 'string', 'max:12']]);
        $beklenen = mb_substr(preg_replace('/[^A-Z0-9]/u', '', mb_strtoupper($arac->plaka)), -4);
        $girilen = preg_replace('/[^A-Z0-9]/u', '', mb_strtoupper($veri['sifre']));
        if (! hash_equals($beklenen, $girilen)) {
            return back()->withErrors(['sifre' => 'Şifre hatalı.']);
        }
        $request->session()->put('qr_detay_'.$arac->qr_token, true);
        return redirect()->to(route('qr.servis.show', ['token' => $token, 'ekran' => $request->input('ekran', 'servis')]).'#kayitlar');
    }

    public function fotograf(Request $request, string $token, ServisFotograf $fotograf)
    {
        $arac = Arac::where('qr_token', $token)->firstOrFail();
        $kullanici = auth()->user();
        $personelYetkili = $kullanici && ($kullanici->tamSistemYetkisiVarMi() || (int) $kullanici->firmaPersoneli?->firma_id === (int) $arac->firma_id);
        abort_unless($request->session()->get('qr_detay_'.$arac->qr_token) === true || $personelYetkili, 403);
        abort_unless($arac->servisler()->whereKey($fotograf->servis_id)->exists(), 404);
        abort_unless(Storage::disk('public')->exists($fotograf->dosya_yolu), 404);
        return response()->file(Storage::disk('public')->path($fotograf->dosya_yolu));
    }






    /*
    |--------------------------------------------------------------------------
    | İsim Maskeleme
    |--------------------------------------------------------------------------
    */


    private function isimMaskele($isim)
    {


        if(!$isim)
        {

            return null;

        }




        $parcalar = explode(

            ' ',

            trim($isim)

        );




        return collect($parcalar)

            ->map(function($kelime){


                return mb_substr(

                    $kelime,

                    0,

                    1

                )

                .

                str_repeat(

                    '*',

                    max(

                        mb_strlen($kelime)-1,

                        1

                    )

                );


            })

            ->implode(' ');



    }







    /*
    |--------------------------------------------------------------------------
    | Telefon Maskeleme
    |--------------------------------------------------------------------------
    */


    private function telefonMaskele($telefon)
    {


        if(!$telefon)
        {

            return null;

        }




        return substr(

            $telefon,

            0,

            4

        )

        .

        ' *** ** '

        .

        substr(

            $telefon,

            -2

        );


    }





}
