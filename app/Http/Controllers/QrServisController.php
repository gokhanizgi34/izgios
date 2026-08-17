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
/*
|--------------------------------------------------------------------------
| Usta / Admin QR Menü Kontrolü
|--------------------------------------------------------------------------
*/


if(
    auth()->check()
    &&
    (
        auth()->user()->isUsta()
        ||
        auth()->user()->isAdmin()
    )
)
{


    return view(

        'qr.usta-menu',

        compact(

            'arac'

        )

    );


}

        $guncelKm = (int) ($arac->kilometre ?? $arac->servisler->max('giris_km') ?? 0);
        $bakimPlan = collect(range(1, 10))->map(function ($sira) use ($arac) {
            $hedefKm = $sira * 20000;
            $servis = $arac->servisler
                ->filter(fn ($kayit) => (int) ($kayit->giris_km ?? 0) >= $hedefKm)
                ->sortBy('giris_km')
                ->first();
            return ['sira' => $sira, 'km' => $hedefKm, 'yil' => $sira, 'tamam' => $servis !== null, 'servis' => $servis];
        });

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
                'whatsappUrl'

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
