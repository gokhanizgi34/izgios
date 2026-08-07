<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Musteri;
use App\Models\Arac;
use App\Models\Servis;
use App\Models\ServisFotograf;

use Intervention\Image\Laravel\Facades\Image;



class ServisKabulController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Araç Kabul Ekranı
    |--------------------------------------------------------------------------
    */


    public function create()
    {


        $araclar = Arac::with('musteri')
            ->orderBy('plaka')
            ->get();



        return view(
            'servisler.kabul',
            compact('araclar')
        );


    }





    /*
    |--------------------------------------------------------------------------
    | Plaka / Araç Arama
    |--------------------------------------------------------------------------
    */


    public function aracBul(Request $request)
    {


        $plaka = $request->plaka;



        if(!$plaka)
        {

            return response()->json([]);

        }



        $araclar = Arac::with('musteri')

            ->where(
                'plaka',
                'like',
                '%'.$plaka.'%'
            )

            ->limit(20)

            ->get();




        return response()->json($araclar);



    }







    /*
    |--------------------------------------------------------------------------
    | QR Kod ile Araç Bul
    |--------------------------------------------------------------------------
    */


    public function qrBul(Request $request)
    {


        $token = $request->token;



        $arac = Arac::with('musteri')

            ->where(
                'qr_token',
                $token
            )

            ->first();





        if(!$arac)
        {


            return response()->json([

                'success'=>false

            ]);


        }





        return response()->json([

            'success'=>true,

            'arac'=>$arac


        ]);



    }







    /*
    |--------------------------------------------------------------------------
    | Servis Kaydet
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {


        $request->validate([


            'arac_id'=>'required',

            'musteri_id'=>'required',


        ]);







        $arac = Arac::findOrFail(
            $request->arac_id
        );







        $servis = Servis::create([


            'musteri_id'=>
            $request->musteri_id,


            'arac_id'=>
            $request->arac_id,


            'servis_no'=>
            'SRV-'.date('YmdHis'),


            'servis_tarihi'=>
            now(),



            'giris_km'=>
            $request->giris_km,



            'sikayet'=>
            $request->sikayet,



            'usta_notu'=>
            $request->usta_notu,



            'oncelik'=>
            $request->oncelik
            ??
            'Normal',



            'yakit_seviyesi'=>
            $request->yakit_seviyesi,



            'anahtar_durumu'=>
            $request->anahtar_durumu,



            'ruhsat_aracta'=>
            $request->ruhsat_aracta,



            'notlar'=>
            $request->arac_durum_notu,



            'durum'=>
            'Bekliyor',



        ]);








        /*
        |
        | Araç KM güncelle
        |
        */


        if($request->giris_km)
        {


            $arac->son_km =
                $request->giris_km;


            $arac->save();


        }








        $this->fotografKaydet(

            $request,

            $servis

        );







        return redirect()

            ->route(
                'servisler.show',
                $servis->id
            )

            ->with(
                'success',
                'Araç servis kabul işlemi tamamlandı.'
            );



    }









    /*
    |--------------------------------------------------------------------------
    | Fotoğraf Kaydet
    |--------------------------------------------------------------------------
    */


    private function fotografKaydet(
        Request $request,
        Servis $servis
    )
    {



        if(
            !$request->hasFile('fotograflar')
        )
        {

            return;

        }







        foreach(
            $request->file('fotograflar')
            as $kategori=>$foto
        )
        {


            if(!$foto)
            {

                continue;

            }





            $klasor =
            'servisler/'.$servis->id;





            Storage::disk('public')
                ->makeDirectory($klasor);






            $dosya =
            uniqid()
            .'.webp';






            $yol =
            $klasor.'/'.$dosya;







            Image::read($foto)

                ->toWebp(80)

                ->save(
                    storage_path(
                        'app/public/'.$yol
                    )
                );







            ServisFotograf::create([


                'servis_id'=>
                $servis->id,


                'kategori'=>
                $kategori,


                'dosya_yolu'=>
                $yol,


                'aciklama'=>
                'Araç kabul fotoğrafı'


            ]);



        }




    }





}