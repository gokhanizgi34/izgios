<?php

namespace App\Http\Controllers;


use App\Models\Musteri;
use App\Models\Firma;
use App\Models\Sube;
use App\Services\CariAktarimServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



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
        $this->musteriKayitYetkisi();
        $firmaId = $this->aktifFirmaId();
        $firmalar = auth()->user()?->tamSistemYetkisiVarMi()
            ? Firma::where('aktif', true)->orderBy('unvan')->get()
            : Firma::where('id', $firmaId)->where('aktif', true)->get();
        $subeler = Sube::where('aktif', true)->whereIn('firma_id', $firmalar->pluck('id'))->orderBy('sube_adi')->get();

        return view('musteriler.create-v3', compact('firmalar', 'subeler', 'firmaId'));


    }









    /**
     * Müşteri kayıt
     */
    public function store(Request $request)
    {
        $this->musteriKayitYetkisi();


        $validated = $request->validate([

            'firma_id' => ['nullable', 'integer', 'exists:firmas,id'],

            'sube_id' => ['nullable', 'integer', 'exists:subes,id'],


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







        $firmaId = $this->kayitFirmaId($request, $validated['firma_id'] ?? null);
        $subeId = $this->kayitSubeId($firmaId, $validated['sube_id'] ?? null);
        $validated['firma_id'] = $firmaId;
        $validated['sube_id'] = $subeId;

        DB::transaction(function () use ($validated, &$musteri) {
            $musteri = Musteri::create($validated);
            app(CariAktarimServisi::class)->musteriKarti($musteri);
        });







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

        $firmaId = $musteri->firma_id ?: $this->aktifFirmaId();
        $firmalar = auth()->user()?->tamSistemYetkisiVarMi()
            ? Firma::where('aktif', true)->orderBy('unvan')->get()
            : Firma::where('id', $firmaId)->where('aktif', true)->get();
        $subeler = Sube::where('aktif', true)->whereIn('firma_id', $firmalar->pluck('id'))->orderBy('sube_adi')->get();

        return view(
            'musteriler.edit',
            compact('musteri', 'firmalar', 'subeler', 'firmaId')
        );


    }









    /**
     * Güncelleme
     */
    public function update(Request $request, Musteri $musteri)
    {
        $this->musteriErisiminiDogrula($musteri);

        $validated = $request->validate([

            'firma_id' => ['nullable', 'integer', 'exists:firmas,id'],

            'sube_id' => ['nullable', 'integer', 'exists:subes,id'],


            'ad_soyad' => 'required|string|max:255',


            'tc_kimlik_no' => 'nullable|string|max:11',


            'telefon' => 'required|string|max:30',


            'telefon2' => 'nullable|string|max:30',


            'email' => 'nullable|email|max:255',

            'dogum_tarihi' => 'nullable|date|before:today',


            'adres' => 'nullable|string',


            'notlar' => 'nullable|string',


        ]);

        if (auth()->user()?->tamSistemYetkisiVarMi()) {
            $validated['firma_id'] = $this->kayitFirmaId($request, $validated['firma_id'] ?? $musteri->firma_id);
            $validated['sube_id'] = $this->kayitSubeId($validated['firma_id'], $validated['sube_id'] ?? $musteri->sube_id);
        } else {
            unset($validated['firma_id'], $validated['sube_id']);
        }







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
        app(CariAktarimServisi::class)->musteriKarti($musteri->fresh());







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

    private function musteriKayitYetkisi(): void
    {
        $kullanici = auth()->user();
        abort_unless($kullanici && ($kullanici->tamSistemYetkisiVarMi() || $kullanici->isAdmin() || $kullanici->isUsta() || $kullanici->isOfis()), 403);
        if (! $kullanici->tamSistemYetkisiVarMi()) {
            abort_unless($this->aktifFirmaId(), 403, 'Müşteri oluşturmak için kullanıcının firma bağlantısı bulunmalıdır.');
        }
    }

    private function kayitFirmaId(Request $request, mixed $istenenFirmaId): int
    {
        if (auth()->user()?->tamSistemYetkisiVarMi()) {
            $firmaId = (int) ($istenenFirmaId ?: session('aktif_firma_id'));
            if (! $firmaId) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'firma_id' => 'Müşteri kaydı için firma seçmelisiniz.',
                ]);
            }
        } else {
            $firmaId = (int) auth()->user()?->firmaPersoneli?->firma_id;
        }

        abort_unless(Firma::where('id', $firmaId)->where('aktif', true)->exists(), 403, 'Geçerli ve aktif bir firma seçilmelidir.');
        return $firmaId;
    }

    private function kayitSubeId(int $firmaId, mixed $istenenSubeId): ?int
    {
        $subeId = $istenenSubeId ?: (auth()->user()?->tamSistemYetkisiVarMi() ? null : auth()->user()?->firmaPersoneli?->sube_id);
        if (! $subeId) {
            return null;
        }

        abort_unless(Sube::where('id', $subeId)->where('firma_id', $firmaId)->where('aktif', true)->exists(), 403, 'Seçilen şube firmaya ait değil.');
        return (int) $subeId;
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
