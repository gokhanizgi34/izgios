<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Servis;
use App\Models\Musteri;
use App\Models\Arac;



class ServisController extends Controller
{


    /**
     * Servis listeleme
     */
    public function index()
    {


        $servisler = Servis::with([

            'musteri',
            'arac'

        ])

        ->latest()

        ->get();



        return view(
            'servisler.index',
            compact('servisler')
        );


    }






    /**
     * Yeni servis formu
     */
    public function create()
    {


        $musteriler = Musteri::orderBy('ad_soyad')
            ->get();



        $araclar = Arac::orderBy('plaka')
            ->get();



        return view(
            'servisler.create',
            compact(
                'musteriler',
                'araclar'
            )
        );


    }







    /**
     * Servis kaydet
     */
    public function store(Request $request)
    {


        $request->validate([


            'musteri_id' => 'required',

            'arac_id' => 'required',


        ]);




        $servis = new Servis();



        $servis->musteri_id = $request->musteri_id;

        $servis->arac_id = $request->arac_id;


        $servis->servis_no =
            'SRV-' . date('YmdHis');



        $servis->sikayet =
            $request->sikayet;



        $servis->yapilan_islem =
            $request->yapilan_islem;



        $servis->kullanilan_parca =
            $request->kullanilan_parca;



        $servis->parca_tutari =
            $request->parca_tutari ?? 0;



        $servis->iscilik_tutari =
            $request->iscilik_tutari ?? 0;



        $servis->toplam_tutar =
            $servis->parca_tutari +
            $servis->iscilik_tutari;



        $servis->durum =
            $request->durum ?? 'Bekliyor';



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
                'Servis kaydı oluşturuldu.'
            );


    }








    /**
     * Servis detay
     */
    public function show(string $id)
    {


        $servis = Servis::with([

            'musteri',
            'arac'

        ])

        ->findOrFail($id);





        return view(
            'servisler.show',
            compact('servis')
        );


    }








    /**
     * Düzenleme formu
     */
    public function edit(string $id)
    {


        $servis = Servis::findOrFail($id);



        $musteriler = Musteri::orderBy('ad_soyad')
            ->get();



        $araclar = Arac::orderBy('plaka')
            ->get();





        return view(
            'servisler.edit',
            compact(
                'servis',
                'musteriler',
                'araclar'
            )
        );


    }









    /**
     * Servis güncelle
     */
    public function update(Request $request, string $id)
    {


        $servis = Servis::findOrFail($id);




        $request->validate([


            'musteri_id' => 'required',

            'arac_id' => 'required',

            'durum' => 'required',


        ]);





        $servis->musteri_id =
            $request->musteri_id;



        $servis->arac_id =
            $request->arac_id;



        $servis->sikayet =
            $request->sikayet;



        $servis->yapilan_islem =
            $request->yapilan_islem;



        $servis->kullanilan_parca =
            $request->kullanilan_parca;



        $servis->parca_tutari =
            $request->parca_tutari ?? 0;



        $servis->iscilik_tutari =
            $request->iscilik_tutari ?? 0;



        $servis->toplam_tutar =
            $servis->parca_tutari +
            $servis->iscilik_tutari;



        $servis->durum =
            $request->durum;



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
                'Servis güncellendi.'
            );


    }









    /**
     * Servis sil
     */
    public function destroy(string $id)
    {


        $servis = Servis::findOrFail($id);



        $servis->delete();




        return redirect()

            ->route('servisler.index')

            ->with(
                'success',
                'Servis kaydı silindi.'
            );


    }

public function musteriAra(Request $request)
{

    $arama = $request->arama;


    $musteriler = Musteri::where(
            'tc_kimlik_no',
            'like',
            "%".$arama."%"
        )

        ->orWhere(
            'telefon',
            'like',
            "%".$arama."%"
        )

        ->orWhere(
            'ad_soyad',
            'like',
            "%".$arama."%"
        )

        ->limit(10)

        ->get();



    return response()->json($musteriler);

}



 

    public function durumGuncelle(Request $request,$id)
    {

        $servis = Servis::findOrFail($id);


        $servis->durum = $request->durum;


        $servis->save();


        return back()

            ->with(
                'success',
                'Servis durumu güncellendi.'
            );

    }
   

}

