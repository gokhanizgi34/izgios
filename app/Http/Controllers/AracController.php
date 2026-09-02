<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use App\Models\Arac;
use App\Models\Musteri;
use App\Services\CariAktarimServisi;


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
        $this->firmaKapsami($query);

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
            'araclar.index-v2',
            compact('araclar')
        );


    }








    /*
    |--------------------------------------------------------------------------
    | Yeni Araç Formu
    |--------------------------------------------------------------------------
    */


    public function create(Request $request)
    {


        $musteriler = Musteri::query();
        $this->firmaKapsamiMusteri($musteriler);
        $musteriler = $musteriler->orderBy(
            'ad_soyad'
        )->get();



        $seciliMusteriId = $request->integer('musteri_id');

        return view(
            'araclar.create-v3',
            compact('musteriler', 'seciliMusteriId')
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
        $musteri = Musteri::findOrFail($validated['musteri_id']);
        $this->musteriErisiminiDogrula($musteri);
        if (! $musteri->firma_id) {
            return back()->withInput()->withErrors([
                'musteri_id' => 'Bu müşteri eski bir kayıttır ve firma bağlantısı yoktur. Müşteri kartından firma bağlantısını tamamlayın.',
            ]);
        }
        $validated['firma_id'] = $musteri->firma_id;
        $validated['sube_id'] = $musteri->sube_id;
        $validated['plaka'] = $this->plakaNormalize($validated['plaka']);

        if ($this->plakaMevcutMu($validated['firma_id'], $validated['plaka'])) {
            return back()->withInput()->withErrors(['plaka' => 'Bu plaka aynı firmada zaten kayıtlıdır.']);
        }



        DB::transaction(function() use ($validated, &$arac){



            $validated['qr_token'] = Str::uuid();

            $validated['qr_created_at'] = now();



            $arac = Arac::create(
                $validated
            );


        });
        app(CariAktarimServisi::class)->musteriKarti($musteri->fresh());




        return redirect()

            ->route('servis.kabul', ['arac_id' => $arac->id])

            ->with(
                'success',
                'Araç kartı kaydedildi. Servis kabul bilgilerini tamamlayın.'
            );


    }









    /*
    |--------------------------------------------------------------------------
    | Araç Detay
    |--------------------------------------------------------------------------
    */


    public function show(Arac $arac)
    {
        $this->aracErisiminiDogrula($arac);

        $arac->load(
            ['musteri', 'servisler' => fn ($query) => $query->with('fotograflar')->latest('servis_tarihi')]
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
        $this->aracErisiminiDogrula($arac);
        $musteriler = Musteri::query();
        $this->firmaKapsamiMusteri($musteriler);
        $musteriler = $musteriler->orderBy(
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
        $this->aracErisiminiDogrula($arac);
        $validated = $this->validation($request);
        $musteri = Musteri::findOrFail($validated['musteri_id']);
        $this->musteriErisiminiDogrula($musteri);
        if (! $musteri->firma_id) {
            return back()->withInput()->withErrors([
                'musteri_id' => 'Bu müşterinin firma bağlantısı yoktur. Araç kaydedilmeden önce müşteri kartını güncelleyin.',
            ]);
        }
        $validated['firma_id'] = $musteri->firma_id;
        $validated['sube_id'] = $musteri->sube_id;
        $validated['plaka'] = $this->plakaNormalize($validated['plaka']);

        if ($this->plakaMevcutMu($validated['firma_id'], $validated['plaka'], $arac->id)) {
            return back()->withInput()->withErrors(['plaka' => 'Bu plaka aynı firmada zaten kayıtlıdır.']);
        }



        $arac->update(
            $validated
        );
        app(CariAktarimServisi::class)->musteriKarti($musteri->fresh());




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
        $this->aracErisiminiDogrula($arac);

        if ($arac->servisler()->exists()) {
            return redirect()
                ->route('araclar.index')
                ->with('error', 'Servis geçmişi bulunan araç silinemez. Yalnızca hatalı ve işlem görmemiş araç kayıtları silinebilir.');
        }

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
        $this->aracErisiminiDogrula($arac);

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

    private function plakaNormalize(string $plaka): string
    {
        return preg_replace('/[^0-9A-ZÇĞİÖŞÜ]/u', '', mb_strtoupper(trim($plaka), 'UTF-8')) ?: '';
    }

    private function plakaMevcutMu(int $firmaId, string $plaka, ?int $haricId = null): bool
    {
        return Arac::query()
            ->where('firma_id', $firmaId)
            ->when($haricId, fn ($query) => $query->whereKeyNot($haricId))
            ->pluck('plaka')
            ->contains(fn ($kayitliPlaka) => $this->plakaNormalize($kayitliPlaka) === $plaka);
    }

    private function aktifFirmaId(): ?int
    {
        return auth()->user()?->tamSistemYetkisiVarMi()
            ? (session('aktif_firma_id') ?: null)
            : (session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id);
    }

    private function firmaKapsami($query): void
    {
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            $firmaId = $this->aktifFirmaId();
            abort_unless($firmaId, 403, 'Kullanıcının firma bağlantısı bulunamadı.');
            $query->where('firma_id', $firmaId);
        }
    }

    private function firmaKapsamiMusteri($query): void
    {
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            $query->where('firma_id', $this->aktifFirmaId());
        }
    }

    private function musteriErisiminiDogrula(Musteri $musteri): void
    {
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless((int) $musteri->firma_id === (int) $this->aktifFirmaId(), 403);
        }
    }

    private function aracErisiminiDogrula(Arac $arac): void
    {
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless((int) $arac->firma_id === (int) $this->aktifFirmaId(), 403);
        }
    }



}
