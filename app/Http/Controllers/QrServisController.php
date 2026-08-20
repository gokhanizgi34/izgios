<?php

namespace App\Http\Controllers;


use App\Models\Arac;
use App\Models\Sube;



class QrServisController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | QR Servis Görüntüleme
    |--------------------------------------------------------------------------
    */


    public function show($token)
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

        $guncelKm = (int) ($arac->kilometre ?? $arac->servisler->max('giris_km') ?? 0);
        $bakimPlan = collect(range(1, 10))->map(function ($sira) use ($arac) {
            $hedefKm = $sira * 20000;
            $servis = $arac->servisler
                ->filter(fn ($kayit) => (int) ($kayit->giris_km ?? 0) >= $hedefKm)
                ->sortBy('giris_km')
                ->first();
            return ['sira' => $sira, 'km' => $hedefKm, 'yil' => $sira, 'tamam' => $servis !== null, 'servis' => $servis];
        });

        $periyodikBakimlar = $arac->servisler
            ->flatMap(fn ($servis) => $servis->islemler
                ->where('kategori', 'periyodik_bakim')
                ->map(fn ($islem) => ['servis' => $servis, 'islem' => $islem]))
            ->sortByDesc(fn ($kayit) => $kayit['servis']->servis_tarihi ?? $kayit['servis']->created_at)
            ->values();

        // QR servis sekmesi yalnızca iş emrindeki "Yapılan İşlemler"
        // kayıtlarından, bakım sekmesi ise yalnız periyodik bakım kayıtlarından beslenir.
        $servisIslemleri = $arac->servisler
            ->map(function ($servis) {
                return [
                    'servis' => $servis,
                    'islemler' => $servis->islemler
                        ->where('kategori', '!=', 'periyodik_bakim')
                        ->values(),
                ];
            })
            ->filter(fn ($kayit) => $kayit['islemler']->isNotEmpty())
            ->values();

        $sube = $arac->servisler->first()?->sube
            ?: Sube::query()->where('aktif', true)->orderBy('id')->first();
        $whatsappNo = preg_replace('/\D+/', '', (string) ($sube?->whatsapp_no ?: $sube?->telefon));
        if (str_starts_with($whatsappNo, '0')) {
            $whatsappNo = '90'.substr($whatsappNo, 1);
        }
        $whatsappUrl = strlen($whatsappNo) >= 10 ? 'https://wa.me/'.$whatsappNo : null;

        return view(

            'qr.musteri-servis-v4',

            compact(

                'arac',

                'musteri',

                'sonrakiBakim',
                'bakimPlan',
                'periyodikBakimlar',
                'servisIslemleri',
                'whatsappUrl',
                'hizliIslemYetkisi'

            )

        );


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
