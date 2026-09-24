<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FirmaAyarController extends Controller
{


    public function index()
    {


        $firma = [

            'unvan' => 'İzgi Oto Servis',

            'telefon' => '',

            'email' => '',

            'adres' => '',

            'vergi_no' => '',

        ];


        return view(
            'ayarlar.firma.index',
            compact('firma')
        );


    }


}