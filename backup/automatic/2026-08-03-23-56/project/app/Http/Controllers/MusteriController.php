<?php

namespace App\Http\Controllers;


use App\Models\Musteri;
use Illuminate\Http\Request;



class MusteriController extends Controller
{


    /**
     * Müşteri kartları
     */
    public function index(Request $request)
    {


        $query = Musteri::with('araclar');



        if($request->filled('search')){


            $arama = $request->search;



            $query->where(function($q) use ($arama){


                $q->where('ad_soyad','like','%'.$arama.'%')
                  ->orWhere('telefon','like','%'.$arama.'%')
                  ->orWhere('email','like','%'.$arama.'%');


            });


        }



        $musteriler = $query
            ->latest()
            ->get();



        return view(
            'musteriler.index',
            compact('musteriler')
        );


    }









    /**
     * Yeni müşteri ekranı
     */
    public function create()
    {


        return view(
            'musteriler.create'
        );


    }









    /**
     * Müşteri kayıt
     */
    public function store(Request $request)
    {


        $validated = $request->validate([


            'ad_soyad' => 'required|string|max:255',


            'tc_kimlik_no' => 'nullable|string|max:11',


            'telefon' => 'required|string|max:30',


            'telefon2' => 'nullable|string|max:30',


            'email' => 'nullable|email|max:255',


            'adres' => 'nullable|string',


            'notlar' => 'nullable|string',


        ]);






        $validated['ad_soyad'] =
            mb_strtoupper(
                $validated['ad_soyad'],
                'UTF-8'
            );






        if(!empty($validated['adres'])){


            $validated['adres'] =
                mb_strtoupper(
                    $validated['adres'],
                    'UTF-8'
                );


        }






        if(!empty($validated['notlar'])){


            $validated['notlar'] =
                mb_strtoupper(
                    $validated['notlar'],
                    'UTF-8'
                );


        }







        Musteri::create($validated);







        return redirect()

            ->route('musteriler.index')

            ->with(
                'success',
                'Müşteri başarıyla oluşturuldu.'
            );


    }









    /**
     * Müşteri detay
     */
    public function show(Musteri $musteri)
    {


        $musteri->load([
            'araclar'
        ]);




        return view(
            'musteriler.show',
            compact('musteri')
        );


    }









    /**
     * Düzenleme
     */
    public function edit(Musteri $musteri)
    {


        return view(
            'musteriler.edit',
            compact('musteri')
        );


    }









    /**
     * Güncelleme
     */
    public function update(Request $request, Musteri $musteri)
    {


        $validated = $request->validate([


            'ad_soyad' => 'required|string|max:255',


            'tc_kimlik_no' => 'nullable|string|max:11',


            'telefon' => 'required|string|max:30',


            'telefon2' => 'nullable|string|max:30',


            'email' => 'nullable|email|max:255',


            'adres' => 'nullable|string',


            'notlar' => 'nullable|string',


        ]);







        $validated['ad_soyad'] =
            mb_strtoupper(
                $validated['ad_soyad'],
                'UTF-8'
            );








        if(!empty($validated['adres'])){


            $validated['adres'] =
                mb_strtoupper(
                    $validated['adres'],
                    'UTF-8'
                );


        }








        if(!empty($validated['notlar'])){


            $validated['notlar'] =
                mb_strtoupper(
                    $validated['notlar'],
                    'UTF-8'
                );


        }







        $musteri->update($validated);







        return redirect()

            ->route(
                'musteriler.show',
                $musteri->id
            )

            ->with(
                'success',
                'Müşteri bilgileri güncellendi.'
            );


    }









    /**
     * Müşteri sil
     */
    public function destroy(Musteri $musteri)
    {


        $musteri->delete();




        return redirect()

            ->route('musteriler.index')

            ->with(
                'success',
                'Müşteri silindi.'
            );


    }



}