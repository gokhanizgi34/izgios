<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Servis;
use App\Models\ServisIslem;
use App\Models\ServisParca;
use App\Models\ServisFotograf;
use App\Models\AracHasar;
use App\Models\ServisDurumNotu;
use App\Services\IletisimOtomasyonServisi;
use App\Services\BakimHatirlatmaTarihi;
use App\Services\ServisMuhasebeAktarimServisi;
use App\Services\ServisDurumAkisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;



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

            'fotograflar',

            'durumNotlari.kullanici'

        ])

        ->findOrFail($id);

        $this->firmaErisiminiDogrula($servis);

        $ustaUzerineAlabilir = false;
        if (auth()->user()?->isUsta()) {
            abort_unless($servis->usta_id === null || $servis->usta_id === auth()->id(), 403, 'Bu iş emri başka bir ustanın üzerindedir.');
            $ustaUzerineAlabilir = $servis->usta_id === null;
        }

        $gununIsleri = Servis::query()
            ->with(['arac:id,plaka,marka,model'])
            ->where('firma_id', $servis->firma_id)
            ->when(auth()->user()?->isUsta(), fn ($q) => $q->where('usta_id', auth()->id()))
            ->whereNotIn('durum', ['Tamamlandı'])
            ->latest('servis_tarihi')
            ->limit(6)
            ->get();



        return view(

            'servisler.islem-v9',

            ['servis' => $servis, 'hasarlar' => AracHasar::where('servis_id', $servis->id)->latest()->get(), 'ustaUzerineAlabilir' => $ustaUzerineAlabilir, 'stokParcalar' => DB::table('stok_parcalar')->where('firma_id', $servis->firma_id)->where('aktif', true)->orderBy('parca_adi')->get(), 'gununIsleri' => $gununIsleri]

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

    public function durumGuncelle(Request $request, Servis $servis, ServisDurumAkisi $akis)
    {
        $this->islemYetkisi($servis);
        $akisNotu = $request->boolean('akis_notu');
        $veri = $request->validate([
            'durum' => [$akisNotu ? 'nullable' : 'required', 'in:Bekliyor,İşlemde,Teslime Hazır,Tamamlandı'],
            'usta_notu' => [$akisNotu ? 'required' : 'nullable', 'string', 'max:5000'],
            'akis_notu' => ['nullable', 'boolean'],
        ]);
        $oncekiDurum = $servis->durum;

        if ($akisNotu) {
            if ($servis->durumNotlari()->where('durum', $oncekiDurum)->exists()) {
                throw ValidationException::withMessages(['usta_notu' => $oncekiDurum.' aşaması için zaten bir not bulunuyor. Mevcut notu düzenleyebilirsiniz.']);
            }
            $sonrakiDurum = $akis->sonraki($oncekiDurum);
            DB::transaction(function () use ($servis, $veri, $oncekiDurum, $sonrakiDurum) {
                ServisDurumNotu::create([
                    'servis_id' => $servis->id,
                    'kullanici_id' => auth()->id(),
                    'durum' => $oncekiDurum,
                    'not' => $veri['usta_notu'],
                ]);
                $servis->update(['durum' => $sonrakiDurum]);
            });
        } else {
            $servis->update(array_filter([
                'durum' => $veri['durum'],
                'usta_notu' => $veri['usta_notu'] ?? null,
            ], fn ($deger) => $deger !== null));
        }

        $guncelServis = $servis->fresh();
        app(IletisimOtomasyonServisi::class)->servisDurumuDegisti($guncelServis, $oncekiDurum, $guncelServis->durum);
        if ($guncelServis->durum === 'Tamamlandı') {
            app(ServisMuhasebeAktarimServisi::class)->aktar($guncelServis);
        }
        return back()->with('success', $akisNotu
            ? $oncekiDurum.' notu kaydedildi. Sıradaki aşama: '.$guncelServis->durum.'.'
            : 'Servis durumu güncellendi.');
    }

    public function durumNotuGuncelle(Request $request, Servis $servis, ServisDurumNotu $not)
    {
        $this->islemYetkisi($servis);
        abort_unless((int) $not->servis_id === (int) $servis->id, 404);
        $veri = $request->validate(['not' => ['required', 'string', 'max:5000']]);
        $not->update($veri);

        return back()->with('success', $not->durum.' aşaması notu güncellendi.');
    }

    public function durumNotuSil(Servis $servis, ServisDurumNotu $not)
    {
        $this->islemYetkisi($servis);
        abort_unless((int) $not->servis_id === (int) $servis->id, 404);
        $durum = $not->durum;
        $not->delete();

        return back()->with('success', $durum.' aşaması notu silindi. Servis durumu değiştirilmedi.');
    }

    public function hatirlatmaGuncelle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);

        // Çoklu firma öncesinden kalan kayıtlar için, işlem yapılan çalışma
        // alanının firma bağlantısını bir kez onar. Firma yoksa rastgele
        // atama yapılmaz; kullanıcıya açık yönlendirme verilir.
        if (! $servis->firma_id) {
            $firmaId = (int) ($request->integer('firma_id') ?: session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id);
            if (! $firmaId) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'firma_id' => 'Bu eski servis kaydı bir firmaya bağlı değil. Hatırlatma oluşturmak için önce ilgili firma çalışma alanını seçin.',
                ]);
            }

            $servis->update(['firma_id' => $firmaId]);
            $servis->refresh();
        }

        $veri = $request->validate([
            'bakim_periyodu' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $baslangic = $servis->servis_tarihi ?: $servis->created_at;
        $veri['sonraki_bakim_tarihi'] = app(BakimHatirlatmaTarihi::class)
            ->hesapla($baslangic, (int) $veri['bakim_periyodu'])
            ->toDateString();

        $servis->update($veri);
        $servis->refresh();
        app(IletisimOtomasyonServisi::class)->periyodikBakimPlanla($servis);

        return back()->with('success', 'Bakım hatırlatması '.$servis->sonraki_bakim_tarihi->format('d.m.Y').' tarihine kuruldu; 15, 7, 4, 3 ve 1 gün öncesi / 5, 10, 15 ve 20 gün sonrası iletişim planı oluşturuldu.');
    }

    public function islemEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $veri = $request->validate(['islem_adi' => ['required', 'string', 'max:255'], 'aciklama' => ['nullable', 'string', 'max:2000'], 'tutar' => ['nullable', 'numeric', 'min:0']]);
        ServisIslem::create(array_merge($veri, ['servis_id' => $servis->id, 'kategori' => 'servis', 'tutar' => $veri['tutar'] ?? 0, 'durum' => 'tamamlandi']));
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
        $veri = $request->validate(['stok_parca_id' => ['nullable', 'integer', 'exists:stok_parcalar,id'], 'parca_adi' => ['nullable', 'required_without:stok_parca_id', 'string', 'max:255'], 'adet' => ['required', 'integer', 'min:1'], 'birim_fiyat' => ['nullable', 'numeric', 'min:0'], 'aciklama' => ['nullable', 'string', 'max:1000']]);

        DB::transaction(function () use ($veri, $servis) {
            $stokParca = null;
            if (! empty($veri['stok_parca_id'])) {
                $stokParca = DB::table('stok_parcalar')->lockForUpdate()->where('id', $veri['stok_parca_id'])->where('firma_id', $servis->firma_id)->first();
                abort_unless($stokParca, 403, 'Seçilen parça bu firmaya ait değil.');
                if ($stokParca->stok_miktari < $veri['adet']) {
                    abort(422, 'Seçilen parça için yeterli stok yok.');
                }
            }

            $parcaAdi = $stokParca?->parca_adi ?: $veri['parca_adi'];
            $birimFiyat = filled($veri['birim_fiyat'] ?? null)
                ? (float) $veri['birim_fiyat']
                : (float) (($stokParca?->satis_fiyati ?: $stokParca?->alis_fiyati) ?: 0);

            ServisParca::create([
                'servis_id' => $servis->id,
                'stok_parca_id' => $stokParca?->id,
                'parca_adi' => $parcaAdi,
                'adet' => $veri['adet'],
                'birim_fiyat' => $birimFiyat,
                'toplam_fiyat' => $veri['adet'] * $birimFiyat,
                'aciklama' => $veri['aciklama'] ?? ($stokParca ? 'Stoktan kullanıldı · OEM: '.$stokParca->oem_no : null),
            ]);

            if ($stokParca) {
                DB::table('stok_parcalar')->where('id', $stokParca->id)->decrement('stok_miktari', $veri['adet'], ['updated_at' => now()]);
                DB::table('stok_hareketleri')->insert(['stok_parca_id' => $stokParca->id, 'yon' => 'cikis', 'miktar' => $veri['adet'], 'birim_alis_fiyati' => 0, 'toplam_tutar' => 0, 'referans' => $servis->servis_no ?: 'Servis #'.$servis->id, 'aciklama' => 'Servis iş emrinde kullanıldı.', 'olusturan_id' => auth()->id(), 'created_at' => now(), 'updated_at' => now()]);
            }
        });
        $this->tutarlariGuncelle($servis);
        return back()->with('success', 'Kullanılan parça eklendi.');
    }

    public function periyodikBakimEkle(Request $request, Servis $servis)
    {
        $this->islemYetkisi($servis);
        $bakimlar = [
            'motor_yagi' => 'Motor Yağı', 'yag_filtresi' => 'Yağ Filtresi',
            'hava_filtresi' => 'Hava Filtresi', 'polen_filtresi' => 'Polen Filtresi',
            'yakit_filtresi' => 'Yakıt Filtresi', 'triger_seti' => 'Triger Seti',
            'v_kayisi' => 'V Kayışı', 'gergi_rulmani' => 'Gergi ve Rulmanlar',
            'devirdaim_pompasi' => 'Devirdaim Pompası', 'sanziman_yagi' => 'Şanzıman Yağı',
            'diferansiyel_yagi' => 'Diferansiyel Yağı', 'fren_bakimi' => 'Fren Bakımı',
            'fren_hidroligi' => 'Fren Hidroliği', 'fren_disk_balata' => 'Fren Disk ve Balata',
            'lastik_bakimi' => 'Lastik Bakımı', 'rot_balans' => 'Rot ve Balans',
            'amortisor_kontrolu' => 'Amortisör Kontrolü', 'direksiyon_bakimi' => 'Direksiyon Sistemi',
            'aku_bakimi' => 'Akü Bakımı', 'sarj_sistemi' => 'Şarj Sistemi',
            'buji_bakimi' => 'Buji Bakımı', 'atesleme_sistemi' => 'Ateşleme Sistemi',
            'enjektor_temizligi' => 'Enjektör Temizliği', 'egzoz_emisyon' => 'Egzoz ve Emisyon',
            'klima_bakimi' => 'Klima Bakımı', 'klima_gazi' => 'Klima Gazı',
            'sogutma_sistemi' => 'Soğutma Sistemi', 'antifriz' => 'Antifriz Kontrolü',
            'silecek_bakimi' => 'Silecek Bakımı', 'far_ayari' => 'Far Ayarı',
            'genel_kontrol' => 'Genel Kontrol', 'iscilik' => 'İşçilik',
        ];
        $veri = $request->validate(['bakim_turu' => ['required', 'in:'.implode(',', array_keys($bakimlar))], 'bakim_durumu' => ['nullable', 'in:degistirildi,kontrol_edildi'], 'tutar' => ['nullable', 'numeric', 'min:0'], 'aciklama' => ['nullable', 'string', 'max:2000']]);
        ServisIslem::create(['servis_id' => $servis->id, 'kategori' => 'periyodik_bakim', 'islem_adi' => $bakimlar[$veri['bakim_turu']], 'tutar' => $veri['tutar'] ?? 0, 'aciklama' => $veri['aciklama'] ?? null, 'durum' => $veri['bakim_durumu'] ?? 'degistirildi']);
        $this->tutarlariGuncelle($servis);
        return back()->with('success', 'Periyodik bakım kalemi eklendi.');
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
        $veri = $request->validate([
            'foto' => ['required', 'image', 'max:10240'],
            'kategori' => ['required', 'in:ariza_tespit,islem_oncesi,islem_sirasi,islem_sonrasi,parca_eski,parca_yeni,teslim'],
            'aciklama' => ['required', 'string', 'max:500'],
        ]);
        $yol = $request->file('foto')->store('servisler/'.$servis->id, 'public');
        ServisFotograf::create(['servis_id' => $servis->id, 'kategori' => $veri['kategori'], 'dosya_yolu' => $yol, 'aciklama' => $veri['aciklama']]);
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
