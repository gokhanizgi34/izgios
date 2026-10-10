<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\FirmaPersonel;



class LoginController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Login ekranı
    |--------------------------------------------------------------------------
    */


    public function showLogin()
    {

        return view('auth.login-portal');

    }


    public function demo()
    {
        return view('auth.demo-portal');
    }





    /*
    |--------------------------------------------------------------------------
    | Login işlemi
    |--------------------------------------------------------------------------
    */


    public function login(Request $request)
    {


        $credentials = $request->validate([


            'login' => [

                'required',

                'string',

            ],


            'password' => [

                'required',

                'string',

            ],


        ]);





        /*
        |--------------------------------------------------------------------------
        | Giriş verisi temizleme
        |--------------------------------------------------------------------------
        */


        $login = trim($credentials['login']);


        $login = mb_strtolower(

            $login,

            'UTF-8'

        );





        /*
        |--------------------------------------------------------------------------
        | Kullanıcı bul
        |--------------------------------------------------------------------------
        */


        $kullanici = User::whereRaw(

            'LOWER(email) = ?',

            [

                $login

            ]

        )->first();





        if (!$kullanici) {


            return back()

                ->withErrors([

                    'login' => 'Kullanıcı bulunamadı.'

                ])

                ->withInput();


        }





        /*
        |--------------------------------------------------------------------------
        | Kullanıcı aktif mi?
        |--------------------------------------------------------------------------
        */


        if ($kullanici->status !== 'aktif') {


            return back()

                ->withErrors([

                    'login' => 'Bu kullanıcı aktif değil.'

                ])

                ->withInput();


        }





        /*
        |--------------------------------------------------------------------------
        | Şifre kontrolü
        |--------------------------------------------------------------------------
        */


        if (!Hash::check(

            $credentials['password'],

            $kullanici->password

        )) {


            return back()

                ->withErrors([

                    'login' => 'Şifre hatalı.'

                ])

                ->withInput();


        }





        /*
        |--------------------------------------------------------------------------
        | Oturum aç
        |--------------------------------------------------------------------------
        */


        $mobilOturumKorunacak = $kullanici->mobilOturumKorunurMu();
        if ($mobilOturumKorunacak) {
            Auth::guard()->setRememberDuration(180);
        }

        Auth::login(

            $kullanici,

            $mobilOturumKorunacak || $request->filled('remember')

        );





        $request->session()->regenerate();

        $firmaBaglantisi = FirmaPersonel::query()
            ->where('user_id', $kullanici->id)
            ->where('aktif', true)
            ->first();

        $request->session()->put('aktif_firma_id', $firmaBaglantisi?->firma_id);
        $request->session()->put('aktif_sube_id', $firmaBaglantisi?->sube_id);





        /*
        |--------------------------------------------------------------------------
        | Dashboard yönlendirme
        |--------------------------------------------------------------------------
        */


        return redirect()

            ->route('dashboard');


    }








    /*
    |--------------------------------------------------------------------------
    | Çıkış işlemi
    |--------------------------------------------------------------------------
    */


    public function logout(Request $request)
    {


        Auth::logout();



        $request->session()->invalidate();



        $request->session()->regenerateToken();

        $request->session()->forget(['aktif_firma_id', 'aktif_sube_id']);



        return redirect()

            ->route('login');


    }



}
