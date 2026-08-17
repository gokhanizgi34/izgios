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
        $this->firmaKapsami($query);



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
            'musteriler.create-v3'
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

            'dogum_tarihi' => 'nullable|date|before:today',


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







        $validated['firma_id'] = $this->aktifFirmaId();
        $validated['sube_id'] = session('aktif_sube_id') ?: auth()->user()?->firmaPersoneli?->sube_id;
        $musteri = Musteri::create($validated);







        return redirect()

            ->route('araclar.create', ['musteri_id' => $musteri->id])

            ->with(
                'success',
                'Müşteri kaydedildi. Şimdi müşteriye ait araç kartını oluşturun.'
            );


    }









    /**
     * Müşteri detay
     */
    public function show(Musteri $musteri)
    {
        $this->musteriErisiminiDogrula($musteri);

        $musteri->load([
            'araclar.servisler'
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
        $this->musteriErisiminiDogrula($musteri);

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
        $this->musteriErisiminiDogrula($musteri);

        $validated = $request->validate([


            'ad_soyad' => 'required|string|max:255',


            'tc_kimlik_no' => 'nullable|string|max:11',


            'telefon' => 'required|string|max:30',


            'telefon2' => 'nullable|string|max:30',


            'email' => 'nullable|email|max:255',

            'dogum_tarihi' => 'nullable|date|before:today',


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
        $this->musteriErisiminiDogrula($musteri);

        $musteri->delete();




        return redirect()

            ->route('musteriler.index')

            ->with(
                'success',
                'Müşteri silindi.'
            );


    }

    private function aktifFirmaId(): ?int
    {
        return auth()->user()?->tamSistemYetkisiVarMi()
            ? (session('aktif_firma_id') ?: null)
            : (session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id);
    }

    private function firmaKapsami($query): void
    {
        $firmaId = $this->aktifFirmaId();
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless($firmaId, 403, 'Kullanıcının firma bağlantısı bulunamadı.');
            $query->where('firma_id', $firmaId);
        }
    }

    private function musteriErisiminiDogrula(Musteri $musteri): void
    {
        if (auth()->user()?->tamSistemYetkisiVarMi()) return;
        abort_unless((int) $musteri->firma_id === (int) $this->aktifFirmaId(), 403);
    }



}
