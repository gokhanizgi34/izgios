<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Arac;
use App\Models\AracHasar;
use App\Models\AracHasarFotografi;



class AracHasarController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Araç Hasar Listesi
    |--------------------------------------------------------------------------
    */


    public function index(Arac $arac)
    {


        $hasarlar = AracHasar::with('fotograflar')

            ->where(
                'arac_id',
                $arac->id
            )

            ->latest()

            ->get();



        return view(
            'araclar.hasar',
            compact(
                'arac',
                'hasarlar'
            )
        );


    }







    /*
    |--------------------------------------------------------------------------
    | Hasar Kaydet
    |--------------------------------------------------------------------------
    */


    public function store(Request $request, Arac $arac)
    {


        $request->validate([


            'parca_adi'=>[
                'required'
            ],


            'aciklama'=>[
                'nullable'
            ],


            'konum'=>[
                'nullable'
            ]

        ]);





        $hasar = AracHasar::create([


            'arac_id'=>$arac->id,


            'servis_id'=>$request->servis_id,


            'parca_adi'=>$request->parca_adi,


            'aciklama'=>$request->aciklama,


            'konum'=>$request->konum



        ]);





        return redirect()

        ->back()

        ->with(
            'success',
            'Hasar kaydı oluşturuldu.'
        );


    }









    /*
    |--------------------------------------------------------------------------
    | Hasar Sil
    |--------------------------------------------------------------------------
    */


    public function destroy(AracHasar $hasar)
    {


        $hasar->delete();



        return redirect()

        ->back()

        ->with(
            'success',
            'Hasar kaydı silindi.'
        );


    }



}