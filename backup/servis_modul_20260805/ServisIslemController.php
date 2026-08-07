<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Servis;



class ServisIslemController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Usta İşlem Ekranı
    |--------------------------------------------------------------------------
    */


    public function show($id)
    {


        $servis = Servis::with([

            'musteri',

            'arac',

            'islemler',

            'parcalar',

            'fotograflar'

        ])

        ->findOrFail($id);



        return view(

            'servisler.islem',

            compact('servis')

        );


    }



}