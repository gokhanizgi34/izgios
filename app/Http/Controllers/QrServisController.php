<?php

namespace App\Http\Controllers;


use App\Models\Arac;



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

        return view(

            'qr.servis',

            compact(

                'arac',

                'musteri',

                'sonrakiBakim'

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