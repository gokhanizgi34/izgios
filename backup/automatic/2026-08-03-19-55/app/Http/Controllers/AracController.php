<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use App\Models\Arac;
use App\Models\Musteri;


use SimpleSoftwareIO\QrCode\Facades\QrCode;



class AracController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Araç Listesi
    |--------------------------------------------------------------------------
    */


    public function index(Request $request)
    {


        $query = Arac::with('musteri');

if($request->filled('plaka'))
{

    $arama = $request->plaka;


    $query->where(function($q) use ($arama){


        $q->where('plaka','like','%'.$arama.'%')

        ->orWhere('marka','like','%'.$arama.'%')

        ->orWhere('model','like','%'.$arama.'%')

        ->orWhereHas('musteri', function($m) use ($arama){

            $m->where('ad_soyad','like','%'.$arama.'%')
              ->orWhere('telefon','like','%'.$arama.'%')
              ->orWhere('telefon2','like','%'.$arama.'%');

        });


    });

}




        $araclar = $query
            ->latest()
            ->get();



        return view(
            'araclar.index',
            compact('araclar')
        );


    }








    /*
    |--------------------------------------------------------------------------
    | Yeni Araç Formu
    |--------------------------------------------------------------------------
    */


    public function create()
    {


        $musteriler = Musteri::orderBy(
            'ad_soyad'
        )->get();



        return view(
            'araclar.create',
            compact('musteriler')
        );


    }









    /*
    |--------------------------------------------------------------------------
    | Araç Kaydet
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {


        $validated = $this->validation($request);



        DB::transaction(function() use ($validated){



            $validated['qr_token'] = Str::uuid();

            $validated['qr_created_at'] = now();



            Arac::create(
                $validated
            );


        });




        return redirect()

            ->route('araclar.index')

            ->with(
                'success',
                'Araç başarıyla kaydedildi.'
            );


    }









    /*
    |--------------------------------------------------------------------------
    | Araç Detay
    |--------------------------------------------------------------------------
    */


    public function show(Arac $arac)
    {


        $arac->load(
            'musteri'
        );



        return view(
            'araclar.show',
            compact('arac')
        );


    }









    /*
    |--------------------------------------------------------------------------
    | Düzenleme
    |--------------------------------------------------------------------------
    */


    public function edit(Arac $arac)
    {


        $musteriler = Musteri::orderBy(
            'ad_soyad'
        )->get();



        return view(
            'araclar.edit',
            compact(
                'arac',
                'musteriler'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | Güncelleme
    |--------------------------------------------------------------------------
    */


    public function update(Request $request, Arac $arac)
    {


        $validated = $this->validation($request);



        $arac->update(
            $validated
        );




        return redirect()

            ->route(
                'araclar.show',
                $arac->id
            )

            ->with(
                'success',
                'Araç bilgileri güncellendi.'
            );


    }









    /*
    |--------------------------------------------------------------------------
    | Sil
    |--------------------------------------------------------------------------
    */


    public function destroy(Arac $arac)
    {


        $arac->delete();



        return redirect()

            ->route(
                'araclar.index'
            )

            ->with(
                'success',
                'Araç silindi.'
            );


    }









    /*
    |--------------------------------------------------------------------------
    | QR Yazdır
    |--------------------------------------------------------------------------
    */


    public function qr(Arac $arac)
    {


        if(!$arac->qr_token)
        {


            $arac->update([

                'qr_token'=>Str::uuid(),

                'qr_created_at'=>now()

            ]);


        }




        $qrData = route(

            'araclar.qr.show',

            $arac->qr_token

        );





        $qrCode = QrCode::size(300)

            ->margin(2)

            ->generate($qrData);





        return view(

            'araclar.qr',

            compact(

                'arac',

                'qrCode'

            )

        );


    }









    /*
    |--------------------------------------------------------------------------
    | QR Okutma
    |--------------------------------------------------------------------------
    */


    public function qrShow($token)
    {


        $arac = Arac::where(

            'qr_token',

            $token

        )

        ->with('musteri')

        ->firstOrFail();





        return view(

            'araclar.qr-show',

            compact('arac')

        );


    }









    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */


    private function validation(Request $request)
    {


        return $request->validate([



            'musteri_id'=>[

                'required',

                'exists:musteris,id'

            ],




            'plaka'=>[

                'required',

                'string',

                'max:20'

            ],




            'marka'=>[

                'required',

                'string',

                'max:100'

            ],




            'model'=>[

                'required',

                'string',

                'max:100'

            ],




            'model_yili'=>[

                'nullable',

                'integer',

                'min:1900',

                'max:2100'

            ],




            'kilometre'=>[

                'nullable',

                'integer',

                'min:0'

            ],




            'sase_no'=>[

                'nullable',

                'string',

                'max:100'

            ],




            'motor_no'=>[

                'nullable',

                'string',

                'max:100'

            ],




            'yakit_tipi'=>[

                'nullable',

                'string',

                'max:50'

            ],




            'vites'=>[

                'nullable',

                'string',

                'max:50'

            ],




            'notlar'=>[

                'nullable',

                'string'

            ],



        ]);


    }



}