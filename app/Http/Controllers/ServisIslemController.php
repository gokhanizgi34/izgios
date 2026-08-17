<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Servis;
use App\Models\ServisIslem;
use App\Models\ServisParca;
use App\Models\ServisFotograf;
use App\Models\AracHasar;



class ServisIslemController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Usta İşlem Ekranı
    |--------------------------------------------------------------------------
    */


    public function show($id)
    {


        $servis = Servis::with([

            'musteri',

            'arac',

            'islemler',

            'parcalar',

            'fotograflar'

        ])

        ->findOrFail($id);

        $this->firmaErisiminiDogrula($servis);

        $ustaUzerineAlabilir = false;
        if (auth()->user()?->isUsta()) {
            abort_unless($servis->usta_id === null || $servis->usta_id === auth()->id(), 403, 'Bu iş emri başka bir ustanın üzerindedir.');
            $ustaUzerineAlabilir = $servis->usta_id === null;
        }



        return view(

            'servisler.islem-v9',

            ['servis' => $servis, 'hasarlar' => AracHasar::where('servis_id', $servis->id)->latest()->get(), 'ustaUzerineAlabilir' => $ustaUzerineAlabilir]

        );


    }

    public function uzerineAl(Servis $servis)
    {
        abort_unless(auth()->check() && auth()->user()->isUsta(), 403);
        $this->firmaErisiminiDogrula($servis);
        abort_unless($servis->usta_id === null || $servis->usta_id === auth()->id(), 403, 'Bu iş emri başka bir ustanın üzerindedir.');
        if ($servis->usta_id === null) {
            $servis->update(['usta_id' => auth()->id(), 'durum' => $servis->durum === 'Bekliyor' ? 'İşlemde' : $servis->durum]);
        }
        return redirect()->route('servis.islem', $servis)->with('success', 'İş emri üzerinize alındı.');
    }

    public function durumGuncelle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['durum' => ['required', 'in:Bekliyor,İşlemde,Teslime Hazır,Tamamlandı'], 'usta_notu' => ['nullable', 'string', 'max:5000']]);
        $servis->update(array_filter($veri, fn ($deger) => $deger !== null));
        return back()->with('success', 'Servis durumu güncellendi.');
    }

    public function hatirlatmaGuncelle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);

        $veri = $request->validate([
            'bakim_periyodu' => ['nullable', 'integer', 'min:1', 'max:120'],
            'sonraki_bakim_tarihi' => ['nullable', 'date'],
        ]);

        if (! empty($veri['bakim_periyodu']) && empty($veri['sonraki_bakim_tarihi'])) {
            $baslangic = $servis->servis_tarihi ?: now();
            $veri['sonraki_bakim_tarihi'] = $baslangic->copy()
                ->addMonths((int) $veri['bakim_periyodu'])
                ->toDateString();
        }

        $servis->update($veri);

        return back()->with('success', 'Periyodik bakım hatırlatması kaydedildi.');
    }

    public function islemEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['islem_adi' => ['required', 'string', 'max:255'], 'aciklama' => ['nullable', 'string', 'max:2000'], 'tutar' => ['nullable', 'numeric', 'min:0']]);
        ServisIslem::create(array_merge($veri, ['servis_id' => $servis->id, 'tutar' => $veri['tutar'] ?? 0, 'durum' => 'tamamlandi']));
        $this->tutarlariGuncelle($servis);
        return back()->with('success', 'Servis işlemi eklendi.');
    }

    public function islemGuncelle(Request $request, Servis $servis, ServisIslem $islem)
    {
        $this->islemYetkisi($servis);
        abort_unless((int) $islem->servis_id === (int) $servis->id, 404);

        $veri = $request->validate([
            'islem_adi' => ['required', 'string', 'max:255'],
            'aciklama' => ['nullable', 'string', 'max:2000'],
            'tutar' => ['nullable', 'numeric', 'min:0'],
        ]);

        $islem->update(array_merge($veri, ['tutar' => $veri['tutar'] ?? 0]));
        $this->tutarlariGuncelle($servis);

        return back()->with('success', 'Servis işlemi güncellendi.');
    }

    public function islemSil(Servis $servis, ServisIslem $islem)
    {
        $this->islemYetkisi($servis);
        abort_unless((int) $islem->servis_id === (int) $servis->id, 404);

        $islem->delete();
        $this->tutarlariGuncelle($servis);

        return back()->with('success', 'Hatalı servis işlemi silindi.');
    }

    public function parcaEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['parca_adi' => ['required', 'string', 'max:255'], 'adet' => ['required', 'integer', 'min:1'], 'birim_fiyat' => ['nullable', 'numeric', 'min:0'], 'aciklama' => ['nullable', 'string', 'max:1000']]);
        $birimFiyat = (float) ($veri['birim_fiyat'] ?? 0);
        ServisParca::create(array_merge($veri, ['servis_id' => $servis->id, 'birim_fiyat' => $birimFiyat, 'toplam_fiyat' => $veri['adet'] * $birimFiyat]));
        $this->tutarlariGuncelle($servis);
        return back()->with('success', 'Kullanılan parça eklendi.');
    }

    public function hasarEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['parca_adi' => ['required', 'string', 'max:255'], 'konum' => ['nullable', 'string', 'max:100'], 'aciklama' => ['nullable', 'string', 'max:2000']]);
        AracHasar::create(array_merge($veri, ['arac_id' => $servis->arac_id, 'servis_id' => $servis->id]));
        return back()->with('success', 'Hasar kaydedildi.');
    }

    public function fotografEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['foto' => ['required', 'image', 'max:10240'], 'kategori' => ['nullable', 'string', 'max:50'], 'aciklama' => ['nullable', 'string', 'max:500']]);
        $yol = $request->file('foto')->store('servisler/'.$servis->id, 'public');
        ServisFotograf::create(['servis_id' => $servis->id, 'kategori' => $veri['kategori'] ?? 'islem', 'dosya_yolu' => $yol, 'aciklama' => $veri['aciklama'] ?? 'Servis işlem fotoğrafı']);
        return back()->with('success', 'Servis fotoğrafı eklendi.');
    }

    private function tutarlariGuncelle(Servis $servis): void
    {
        $iscilik = (float) $servis->islemler()->sum('tutar');
        $parca = (float) $servis->parcalar()->sum('toplam_fiyat');
        $servis->update(['iscilik_tutari' => $iscilik, 'parca_tutari' => $parca, 'toplam_tutar' => $iscilik + $parca]);
    }

    private function islemYetkisi(Servis $servis): void
    {
        $this->firmaErisiminiDogrula($servis);
        if (auth()->user()?->isUsta()) {
            abort_unless($servis->usta_id === auth()->id(), 403, 'Önce bu iş emrini üzerinize almanız gerekir.');
        }
    }

    private function firmaErisiminiDogrula(Servis $servis): void
    {
        if (auth()->user()?->tamSistemYetkisiVarMi()) return;
        $firmaId = session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id;
        abort_unless($firmaId && (int) $servis->firma_id === (int) $firmaId, 403, 'Bu iş emri bağlı olduğunuz firmaya ait değil.');
    }



}
