<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Musteri;
use App\Models\Arac;
use App\Models\Servis;
use App\Models\ServisFotograf;
use App\Services\IletisimOtomasyonServisi;

use Intervention\Image\Laravel\Facades\Image;



class ServisKabulController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Araç Kabul Ekranı
    |--------------------------------------------------------------------------
    */


    public function create(Request $request)
    {


        $araclar = Arac::with('musteri')
            ->orderBy('plaka')
            ->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))
            ->get();



        $seciliAracId = $request->integer('arac_id');

        return view(
            'servisler.kabul-v3',
            compact('araclar', 'seciliAracId')
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

            ->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))
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

            ->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))
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


        $veri = $request->validate([
            'arac_id' => ['required', 'integer', 'exists:araclar,id'],
            'musteri_id' => ['required', 'integer', 'exists:musteris,id'],
            'giris_km' => ['nullable', 'integer', 'min:0'],
            'sikayet' => ['required', 'string', 'max:5000'],
            'oncelik' => ['nullable', 'in:Normal,Acil,Bekleyen'],
            'fotograflar' => ['nullable', 'array'],
            'fotograflar.*' => ['nullable', 'image', 'max:2048'],
        ]);







        $arac = Arac::findOrFail(
            $request->arac_id
        );

        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless((int) $arac->firma_id === (int) $this->aktifFirmaId(), 403);
        }

        abort_unless((int) $arac->musteri_id === (int) $veri['musteri_id'], 422, 'Seçilen araç ile müşteri eşleşmiyor. Araç seçimini yenileyin.');







        $servis = Servis::create([


            'musteri_id'=>
            $request->musteri_id,


            'arac_id'=>
            $request->arac_id,

            'firma_id' => $arac->firma_id ?: $this->aktifFirmaId(),

            'sube_id' => $arac->sube_id ?: session('aktif_sube_id'),


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
            $request->boolean('ruhsat_aracta'),



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


            $arac->kilometre =
                $request->giris_km;


            $arac->save();


        }








        $this->fotografKaydet(

            $request,

            $servis

        );

        app(IletisimOtomasyonServisi::class)->servisKabulEdildi($servis);







        return redirect()

            ->route(
                'servis.islem',
                $servis->id
            )

            ->with(
                'success',
                'Araç servis kabul işlemi tamamlandı.'
            );



    }

    private function aktifFirmaId(): ?int
    {
        return session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id;
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
