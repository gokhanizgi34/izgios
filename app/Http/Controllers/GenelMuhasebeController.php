<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Services\GenelMuhasebeAktarimServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenelMuhasebeController extends Controller
{
    public function index(Request $request)
    {
        $this->yetki();
        $firmalar = $this->firmalar();
        $firmaId = $this->firmaId($request, $firmalar);
        $this->varsayilanTanimlariOlustur($firmaId);
        app(GenelMuhasebeAktarimServisi::class)->giderFisleriniAktar($firmaId);

        $hesaplar = DB::table('muhasebe_hesap_planlari')->where('firma_id', $firmaId)->orderBy('kod')->get();
        $donemler = DB::table('muhasebe_donemleri')->where('firma_id', $firmaId)->orderByDesc('baslangic_tarihi')->get();
        $masrafMerkezleri = DB::table('muhasebe_masraf_merkezleri')->where('firma_id', $firmaId)->where('aktif', true)->orderBy('kod')->get();
        $projeler = DB::table('muhasebe_projeler')->where('firma_id', $firmaId)->where('durum', 'acik')->orderBy('kod')->get();
        $cariler = DB::table('cari_hesaplar')->where('firma_id', $firmaId)->where('aktif', true)->orderBy('unvan')->get();
        $fisler = DB::table('muhasebe_yevmiye_fisleri')->where('firma_id', $firmaId)->latest('fis_tarihi')->latest('id')->limit(25)->get();

        $mizan = DB::table('muhasebe_yevmiye_satirlari as s')
            ->join('muhasebe_yevmiye_fisleri as f', 'f.id', '=', 's.muhasebe_yevmiye_fis_id')
            ->join('muhasebe_hesap_planlari as h', 'h.id', '=', 's.muhasebe_hesap_plan_id')
            ->where('f.firma_id', $firmaId)->where('f.durum', 'onaylandi')
            ->groupBy('h.id', 'h.kod', 'h.ad', 'h.sinif', 'h.normal_bakiye')
            ->select('h.id', 'h.kod', 'h.ad', 'h.sinif', 'h.normal_bakiye', DB::raw('SUM(s.borc) as borc_toplam'), DB::raw('SUM(s.alacak) as alacak_toplam'))
            ->orderBy('h.kod')->get();

        $toplamBorc = (float) $mizan->sum('borc_toplam');
        $toplamAlacak = (float) $mizan->sum('alacak_toplam');
        $ozet = [
            'hesap' => $hesaplar->count(),
            'yevmiye' => $fisler->count(),
            'borc' => $toplamBorc,
            'alacak' => $toplamAlacak,
            'denge' => round($toplamBorc - $toplamAlacak, 2),
            'gelir' => (float) $mizan->where('sinif', 'gelir')->sum('alacak_toplam') - (float) $mizan->where('sinif', 'gelir')->sum('borc_toplam'),
            'gider' => (float) $mizan->where('sinif', 'gider')->sum('borc_toplam') - (float) $mizan->where('sinif', 'gider')->sum('alacak_toplam'),
        ];
        $sekme = $request->string('sekme', 'genel')->toString();

        return view('ticari.genel-muhasebe-v1', compact('firmalar', 'firmaId', 'hesaplar', 'donemler', 'masrafMerkezleri', 'projeler', 'cariler', 'fisler', 'mizan', 'ozet', 'sekme'));
    }

    public function hesapKaydet(Request $request)
    {
        $firmaId = $this->firmaId($request, $this->firmalar());
        $v = $request->validate([
            'kod' => ['required', 'string', 'max:25'],
            'ad' => ['required', 'string', 'max:180'],
            'sinif' => ['required', 'in:varlik,borc,sermaye,gelir,gider'],
            'normal_bakiye' => ['required', 'in:borc,alacak'],
        ]);
        DB::table('muhasebe_hesap_planlari')->updateOrInsert(
            ['firma_id' => $firmaId, 'kod' => $v['kod']],
            [...$v, 'firma_id' => $firmaId, 'aktif' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        return back()->with('success', 'Hesap planı kaydedildi.');
    }

    public function mizanExcel(Request $request)
    {
        $this->yetki();
        $firmalar = $this->firmalar();
        $firmaId = $this->firmaId($request, $firmalar);
        $this->varsayilanTanimlariOlustur($firmaId);

        $mizan = DB::table('muhasebe_yevmiye_satirlari as s')
            ->join('muhasebe_yevmiye_fisleri as f', 'f.id', '=', 's.muhasebe_yevmiye_fis_id')
            ->join('muhasebe_hesap_planlari as h', 'h.id', '=', 's.muhasebe_hesap_plan_id')
            ->where('f.firma_id', $firmaId)->where('f.durum', 'onaylandi')
            ->groupBy('h.id', 'h.kod', 'h.ad', 'h.sinif', 'h.normal_bakiye')
            ->select('h.kod', 'h.ad', 'h.sinif', DB::raw('SUM(s.borc) as borc_toplam'), DB::raw('SUM(s.alacak) as alacak_toplam'))
            ->orderBy('h.kod')->get();

        return response()->streamDownload(function () use ($mizan) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['MİZAN RAPORU'], ';');
            fputcsv($out, ['Hesap kodu', 'Hesap adı', 'Sınıf', 'Borç', 'Alacak', 'Bakiye'], ';');
            foreach ($mizan as $satir) {
                $bakiye = (float) $satir->borc_toplam - (float) $satir->alacak_toplam;
                fputcsv($out, [$satir->kod, $satir->ad, $satir->sinif, number_format($satir->borc_toplam, 2, ',', '.'), number_format($satir->alacak_toplam, 2, ',', '.'), number_format(abs($bakiye), 2, ',', '.').' '.($bakiye < 0 ? 'Alacak' : 'Borç')], ';');
            }
            fclose($out);
        }, 'mizan-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function donemKaydet(Request $request)
    {
        $firmaId = $this->firmaId($request, $this->firmalar());
        $v = $request->validate([
            'ad' => ['required', 'string', 'max:100'],
            'baslangic_tarihi' => ['required', 'date'],
            'bitis_tarihi' => ['required', 'date', 'after_or_equal:baslangic_tarihi'],
            'durum' => ['required', 'in:acik,kilitli,kapali'],
        ]);
        DB::table('muhasebe_donemleri')->updateOrInsert(
            ['firma_id' => $firmaId, 'baslangic_tarihi' => $v['baslangic_tarihi'], 'bitis_tarihi' => $v['bitis_tarihi']],
            [...$v, 'firma_id' => $firmaId, 'updated_at' => now(), 'created_at' => now()]
        );
        return back()->with('success', 'Muhasebe dönemi kaydedildi.');
    }

    public function boyutKaydet(Request $request)
    {
        $firmaId = $this->firmaId($request, $this->firmalar());
        $v = $request->validate([
            'tur' => ['required', 'in:masraf_merkezi,proje'],
            'kod' => ['required', 'string', 'max:30'],
            'ad' => ['required', 'string', 'max:150'],
        ]);
        $tablo = $v['tur'] === 'masraf_merkezi' ? 'muhasebe_masraf_merkezleri' : 'muhasebe_projeler';
        $veri = ['firma_id' => $firmaId, 'kod' => $v['kod'], 'ad' => $v['ad'], 'updated_at' => now(), 'created_at' => now()];
        if ($tablo === 'muhasebe_masraf_merkezleri') $veri['aktif'] = true;
        else $veri['durum'] = 'acik';
        DB::table($tablo)->updateOrInsert(['firma_id' => $firmaId, 'kod' => $v['kod']], $veri);
        return back()->with('success', 'Analiz boyutu kaydedildi.');
    }

    public function yevmiyeKaydet(Request $request)
    {
        $firmaId = $this->firmaId($request, $this->firmalar());
        $v = $request->validate([
            'muhasebe_donem_id' => ['nullable', 'integer'],
            'fis_tarihi' => ['required', 'date'],
            'tip' => ['required', 'in:mahsup,tahsilat,tediye,acilis,kapanis,entegrasyon'],
            'aciklama' => ['nullable', 'string', 'max:1000'],
            'satirlar' => ['required', 'array', 'min:2'],
            'satirlar.*.hesap_id' => ['required', 'integer'],
            'satirlar.*.cari_hesap_id' => ['nullable', 'integer'],
            'satirlar.*.masraf_merkezi_id' => ['nullable', 'integer'],
            'satirlar.*.proje_id' => ['nullable', 'integer'],
            'satirlar.*.aciklama' => ['nullable', 'string', 'max:500'],
            'satirlar.*.borc' => ['nullable', 'numeric', 'min:0'],
            'satirlar.*.alacak' => ['nullable', 'numeric', 'min:0'],
        ]);
        $donem = DB::table('muhasebe_donemleri')
            ->where('firma_id', $firmaId)
            ->where('durum', 'acik')
            ->whereDate('baslangic_tarihi', '<=', $v['fis_tarihi'])
            ->whereDate('bitis_tarihi', '>=', $v['fis_tarihi'])
            ->when($v['muhasebe_donem_id'] ?? null, fn ($q, $donemId) => $q->where('id', $donemId))
            ->first();
        abort_unless($donem, 422, 'Fiş tarihi için açık ve firmaya ait muhasebe dönemi bulunamadı.');
        $v['muhasebe_donem_id'] = $donem->id;
        $hesapIds = collect($v['satirlar'])->pluck('hesap_id')->unique();
        abort_unless(DB::table('muhasebe_hesap_planlari')->where('firma_id', $firmaId)->whereIn('id', $hesapIds)->count() === $hesapIds->count(), 422, 'Fiş satırındaki hesaplardan biri seçilen firmaya ait değil.');
        foreach ($v['satirlar'] as $satir) {
            if (!empty($satir['cari_hesap_id'])) abort_unless(DB::table('cari_hesaplar')->where('firma_id', $firmaId)->where('id', $satir['cari_hesap_id'])->exists(), 422, 'Cari hesap seçilen firmaya ait değil.');
            if (!empty($satir['masraf_merkezi_id'])) abort_unless(DB::table('muhasebe_masraf_merkezleri')->where('firma_id', $firmaId)->where('id', $satir['masraf_merkezi_id'])->exists(), 422, 'Masraf merkezi seçilen firmaya ait değil.');
            if (!empty($satir['proje_id'])) abort_unless(DB::table('muhasebe_projeler')->where('firma_id', $firmaId)->where('id', $satir['proje_id'])->exists(), 422, 'Proje seçilen firmaya ait değil.');
        }
        $borc = round((float) collect($v['satirlar'])->sum(fn ($satir) => (float) ($satir['borc'] ?? 0)), 2);
        $alacak = round((float) collect($v['satirlar'])->sum(fn ($satir) => (float) ($satir['alacak'] ?? 0)), 2);
        abort_unless($borc > 0 && abs($borc - $alacak) < 0.01, 422, 'Yevmiye fişinde borç ve alacak toplamları eşit ve sıfırdan büyük olmalıdır.');

        DB::transaction(function () use ($v, $firmaId) {
            $fisNo = 'YEV-' . now()->format('YmdHis') . '-' . random_int(10, 99);
            $fisId = DB::table('muhasebe_yevmiye_fisleri')->insertGetId([
                'firma_id' => $firmaId,
                'muhasebe_donem_id' => $v['muhasebe_donem_id'] ?? null,
                'fis_no' => $fisNo,
                'fis_tarihi' => $v['fis_tarihi'],
                'tip' => $v['tip'],
                'aciklama' => $v['aciklama'] ?? null,
                'kaynak' => 'manuel',
                'durum' => 'onaylandi',
                'olusturan_id' => auth()->id(),
                'onaylayan_id' => auth()->id(),
                'onay_tarihi' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($v['satirlar'] as $sira => $satir) {
                DB::table('muhasebe_yevmiye_satirlari')->insert([
                    'muhasebe_yevmiye_fis_id' => $fisId,
                    'muhasebe_hesap_plan_id' => $satir['hesap_id'],
                    'cari_hesap_id' => $satir['cari_hesap_id'] ?? null,
                    'masraf_merkezi_id' => $satir['masraf_merkezi_id'] ?? null,
                    'proje_id' => $satir['proje_id'] ?? null,
                    'aciklama' => $satir['aciklama'] ?? null,
                    'borc' => (float) ($satir['borc'] ?? 0),
                    'alacak' => (float) ($satir['alacak'] ?? 0),
                    'sira' => $sira + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        return back()->with('success', 'Dengeli yevmiye fişi onaylanarak kaydedildi.');
    }

    private function yetki(): void
    {
        abort_unless(auth()->check() && (auth()->user()->tamSistemYetkisiVarMi() || auth()->user()->isAdmin() || auth()->user()->isMuhasebe()), 403);
    }

    private function firmalar()
    {
        $query = Firma::where('aktif', true)->orderBy('unvan');
        if (! auth()->user()->tamSistemYetkisiVarMi()) $query->whereKey(auth()->user()->firmaPersoneli?->firma_id);
        return $query->get();
    }

    private function firmaId(Request $request, $firmalar): int
    {
        $firmaId = auth()->user()->tamSistemYetkisiVarMi() ? ($request->integer('firma_id') ?: $firmalar->first()?->id) : auth()->user()->firmaPersoneli?->firma_id;
        abort_unless($firmaId && $firmalar->contains('id', $firmaId), 403, 'Firma erişimi bulunamadı.');
        return $firmaId;
    }

    private function varsayilanTanimlariOlustur(int $firmaId): void
    {
        $hesaplar = [
            ['100','Kasa','varlik','borc'],['101','Alınan Çekler','varlik','borc'],['102','Bankalar','varlik','borc'],['103','Verilen Çekler ve Ödeme Emirleri','varlik','borc'],['108','Diğer Hazır Değerler','varlik','borc'],['120','Alıcılar','varlik','borc'],['121','Alacak Senetleri','varlik','borc'],['126','Verilen Depozito ve Teminatlar','varlik','borc'],['128','Şüpheli Ticari Alacaklar','varlik','borc'],['131','Ortaklardan Alacaklar','varlik','borc'],['136','Diğer Çeşitli Alacaklar','varlik','borc'],['150','İlk Madde ve Malzeme','varlik','borc'],['151','Yarı Mamuller','varlik','borc'],['152','Mamuller','varlik','borc'],['153','Ticari Mallar','varlik','borc'],['157','Diğer Stoklar','varlik','borc'],['159','Verilen Sipariş Avansları','varlik','borc'],['180','Gelecek Aylara Ait Giderler','varlik','borc'],['191','İndirilecek KDV','varlik','borc'],['193','Peşin Ödenen Vergiler ve Fonlar','varlik','borc'],
            ['300','Banka Kredileri','borc','alacak'],['320','Satıcılar','borc','alacak'],['321','Borç Senetleri','borc','alacak'],['326','Alınan Depozito ve Teminatlar','borc','alacak'],['331','Ortaklara Borçlar','borc','alacak'],['335','Personele Borçlar','borc','alacak'],['336','Diğer Çeşitli Borçlar','borc','alacak'],['340','Alınan Sipariş Avansları','borc','alacak'],['360','Ödenecek Vergi ve Fonlar','borc','alacak'],['361','Ödenecek Sosyal Güvenlik Kesintileri','borc','alacak'],['391','Hesaplanan KDV','borc','alacak'],['397','Sayım ve Tesellüm Fazlaları','borc','alacak'],
            ['500','Sermaye','sermaye','alacak'],['540','Yasal Yedekler','sermaye','alacak'],['570','Geçmiş Yıllar Kârları','sermaye','alacak'],['580','Geçmiş Yıllar Zararları','sermaye','borc'],['590','Dönem Net Kârı','sermaye','alacak'],['591','Dönem Net Zararı','sermaye','borc'],
            ['600','Yurt İçi Satışlar','gelir','alacak'],['601','Yurt Dışı Satışlar','gelir','alacak'],['602','Diğer Gelirler','gelir','alacak'],['610','Satıştan İadeler','gelir','borc'],['611','Satış İskontoları','gelir','borc'],['621','Satılan Ticari Mallar Maliyeti','gider','borc'],['622','Satılan Hizmet Maliyeti','gider','borc'],['631','Pazarlama Satış ve Dağıtım Giderleri','gider','borc'],['632','Genel Yönetim Giderleri','gider','borc'],['642','Faiz Gelirleri','gelir','alacak'],['646','Kambiyo Kârları','gelir','alacak'],['653','Komisyon Giderleri','gider','borc'],['654','Karşılık Giderleri','gider','borc'],['656','Kambiyo Zararları','gider','borc'],['660','Kısa Vadeli Borçlanma Giderleri','gider','borc'],['689','Diğer Olağandışı Gider ve Zararlar','gider','borc'],['770','Genel Yönetim Giderleri','gider','borc'],['760','Pazarlama Satış ve Dağıtım Giderleri','gider','borc'],['780','Finansman Giderleri','gider','borc'],
        ];
        foreach ($hesaplar as [$kod, $ad, $sinif, $bakiye]) {
            DB::table('muhasebe_hesap_planlari')->updateOrInsert(['firma_id' => $firmaId, 'kod' => $kod], ['ad' => $ad, 'sinif' => $sinif, 'normal_bakiye' => $bakiye, 'aktif' => true, 'updated_at' => now(), 'created_at' => now()]);
        }
        $yil = now()->year;
        DB::table('muhasebe_donemleri')->updateOrInsert(['firma_id' => $firmaId, 'baslangic_tarihi' => "$yil-01-01", 'bitis_tarihi' => "$yil-12-31"], ['ad' => "$yil Mali Dönemi", 'durum' => 'acik', 'updated_at' => now(), 'created_at' => now()]);
    }
}
