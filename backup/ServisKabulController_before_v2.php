<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Musteri;
use App\Models\Arac;
use App\Models\Servis;


class ServisKabulController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Araç Kabul Ekranı
    |--------------------------------------------------------------------------
    */

    public function create()
    {

        $musteriler = Musteri::orderBy('ad_soyad')
            ->get();


        $araclar = Arac::with('musteri')
            ->orderBy('plaka')
            ->get();



        return view(
            'servisler.kabul',
            compact(
                'musteriler',
                'araclar'
            )
        );

    }





    /*
    |--------------------------------------------------------------------------
    | Araç Kabul Kaydet
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {


        $request->validate([

            'musteri_id'=>'required',

            'arac_id'=>'required',

            'sikayet'=>'nullable',

        ]);




        $servis = new Servis();



        $servis->musteri_id =
            $request->musteri_id;



        $servis->arac_id =
            $request->arac_id;



        $servis->servis_no =
            'SRV-'.date('YmdHis');



        $servis->servis_tarihi =
            now()->format('Y-m-d');



        $servis->sikayet =
            $request->sikayet;



        $servis->giris_km =
            $request->giris_km;



        $servis->bakim_periyodu =
            $request->bakim_periyodu;



        if($request->bakim_periyodu)
        {

            $servis->sonraki_bakim_tarihi =
                now()
                ->addMonths($request->bakim_periyodu)
                ->format('Y-m-d');

        }



        $servis->durum =
            'Bekliyor';



        $servis->notlar =
            $request->notlar;



        $servis->save();





        return redirect()

            ->route(
                'servisler.show',
                $servis->id
            )

            ->with(
                'success',
                'Araç kabul işlemi tamamlandı.'
            );


    }


}