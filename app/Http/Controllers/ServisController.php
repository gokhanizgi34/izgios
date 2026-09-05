<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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


        $sorgu = Servis::with([

            'musteri',
            'arac'

        ]);

        if (auth()->user()?->isUsta()) {
            // Usta yalnız kendi üzerine aldığı işi çalışabilir. Ancak henüz
            // sahiplenilmemiş, kendi firmasına ait açık işleri de listede
            // görmelidir ki "İş emrini üzerime al" akışını başlatabilsin.
            // Önceki dar filtre, boş usta_id'li iş emirlerini tamamen
            // gizlediği için firma ustası yeni kabul edilen aracı göremiyordu.
            $sorgu->where(function ($sorgu) {
                $sorgu->where('usta_id', auth()->id())
                    ->orWhereNull('usta_id');
            });
        }

        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            $sorgu->where('firma_id', $this->aktifFirmaId());
        }

        $servisler = $sorgu->latest()->get();



        return view(
            'servisler.index-v3',
            compact('servisler')
        );


    }






    /**
     * Yeni servis formu
     */
    public function create()
    {
        return redirect()->route('servis.kabul');


    }







    /**
     * Servis kaydet
     */
    public function store(Request $request)
    {


        $veri = $this->baglantiyiDogrula($request);




        $servis = new Servis();



        $servis->musteri_id = $veri['musteri_id'];

        $servis->arac_id = $veri['arac_id'];

        $servis->firma_id = $veri['firma_id'];

        $servis->sube_id = $veri['sube_id'];


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

            ->route('servis.islem', $servis->id)

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
            'arac',
            'islemler',
            'parcalar',
            'fotograflar',
            'durumNotlari.kullanici'

        ])

        ->findOrFail($id);

        $this->servisErisiminiDogrula($servis);





        return view(
            'servisler.show-v2',
            compact('servis')
        );


    }








    /**
     * Düzenleme formu
     */
    public function edit(string $id)
    {


        $servis = Servis::findOrFail($id);
        $this->servisErisiminiDogrula($servis);



        $musteriler = Musteri::query()->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))->orderBy('ad_soyad')
            ->get();



        $araclar = Arac::query()->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))->orderBy('plaka')
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
        $this->servisErisiminiDogrula($servis);




        $veri = $this->baglantiyiDogrula($request, true);





        $servis->musteri_id =
            $veri['musteri_id'];



        $servis->arac_id =
            $veri['arac_id'];



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
        $this->servisErisiminiDogrula($servis);

        $fotografKlasoru = 'servisler/'.$servis->id;
        $servis->delete();
        Storage::disk('public')->deleteDirectory($fotografKlasoru);




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


    $musteriler = Musteri::where(function ($query) use ($arama) {
            $query->where('tc_kimlik_no', 'like', "%{$arama}%")
                ->orWhere('telefon', 'like', "%{$arama}%")
                ->orWhere('ad_soyad', 'like', "%{$arama}%");
        })
        ->when(! auth()->user()?->tamSistemYetkisiVarMi(), fn ($q) => $q->where('firma_id', $this->aktifFirmaId()))
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

    private function baglantiyiDogrula(Request $request, bool $durumZorunlu = false): array
    {
        $kurallar = [
            'musteri_id' => ['required', 'integer', 'exists:musteris,id'],
            'arac_id' => ['required', 'integer', 'exists:araclar,id'],
        ];

        if ($durumZorunlu) {
            $kurallar['durum'] = ['required', 'string', 'max:50'];
            $kurallar['sikayet'] = ['nullable', 'string', 'max:5000'];
            $kurallar['yapilan_islem'] = ['nullable', 'string'];
            $kurallar['kullanilan_parca'] = ['nullable', 'string'];
            $kurallar['parca_tutari'] = ['nullable', 'numeric', 'min:0'];
            $kurallar['iscilik_tutari'] = ['nullable', 'numeric', 'min:0'];
            $kurallar['notlar'] = ['nullable', 'string'];
        }

        $veri = $request->validate($kurallar);
        $arac = Arac::findOrFail($veri['arac_id']);
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless((int) $arac->firma_id === (int) $this->aktifFirmaId(), 403);
        }
        abort_unless((int) $arac->musteri_id === (int) $veri['musteri_id'], 422, 'Seçilen araç, seçilen müşteriye ait değil.');
        $veri['firma_id'] = $arac->firma_id ?: $this->aktifFirmaId();
        $veri['sube_id'] = $arac->sube_id ?: session('aktif_sube_id');
        return $veri;
    }

    private function aktifFirmaId(): ?int
    {
        return session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id;
    }

    private function servisErisiminiDogrula(Servis $servis): void
    {
        if (auth()->user()?->tamSistemYetkisiVarMi()) return;
        abort_unless((int) $servis->firma_id === (int) $this->aktifFirmaId(), 403);
        if (auth()->user()?->isUsta()) abort_unless((int) $servis->usta_id === (int) auth()->id(), 403);
    }
   

}
