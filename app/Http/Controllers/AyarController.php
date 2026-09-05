<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Models\Sube;
use App\Services\PeriyodikBakimKalemiServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AyarController extends Controller
{
    public function index()
    {
        return view('ayarlar.menu');
    }

    public function qrIletisim()
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $subeler = Sube::with('firma')->orderBy('firma_id')->orderBy('sube_adi')->get();
        return view('ayarlar.qr-iletisim', compact('subeler'));
    }

    public function qrIletisimKaydet(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        $veri = $request->validate(['sube_id' => ['required', 'exists:subes,id'], 'whatsapp_no' => ['nullable', 'string', 'max:25']]);
        $numara = preg_replace('/\D+/', '', (string) $veri['whatsapp_no']);
        if ($numara !== '' && (strlen($numara) < 10 || strlen($numara) > 13)) {
            return back()->withErrors(['whatsapp_no' => 'Geçerli bir WhatsApp numarası girin.']);
        }
        Sube::findOrFail($veri['sube_id'])->update(['whatsapp_no' => $numara ?: null]);
        return back()->with('success', 'QR müşteri ekranı WhatsApp numarası kaydedildi.');
    }

    public function yonetimAyarlari(string $grup)
    {
        $this->sistemYoneticisiKontrol();
        $sayfalar = $this->yonetimSayfalari();
        abort_unless(isset($sayfalar[$grup]), 404);
        $sayfa = $sayfalar[$grup];
        $ayarlar = DB::table('yonetim_ayarlari')->where('grup', $grup)->pluck('deger', 'anahtar')->all();
        return view('ayarlar.yonetim-form', array_merge($sayfa, compact('grup', 'ayarlar')));
    }

    public function yonetimAyarlariKaydet(Request $request, string $grup)
    {
        $this->sistemYoneticisiKontrol();
        $sayfalar = $this->yonetimSayfalari();
        abort_unless(isset($sayfalar[$grup]), 404);
        foreach ($sayfalar[$grup]['alanlar'] as $anahtar => $alan) {
            $deger = ($alan['tip'] ?? '') === 'checkbox' ? ($request->boolean($anahtar) ? '1' : '0') : trim((string) $request->input($anahtar));
            DB::table('yonetim_ayarlari')->updateOrInsert(['grup' => $grup, 'anahtar' => $anahtar], ['deger' => $deger, 'guncelleyen_id' => auth()->id(), 'updated_at' => now(), 'created_at' => now()]);
        }
        return back()->with('success', 'Ayarlar kaydedildi.');
    }

    public function kdvGruplari()
    {
        $this->sistemYoneticisiKontrol();
        return view('ayarlar.kdv-gruplari', ['gruplar' => DB::table('kdv_urun_gruplari')->orderBy('grup_adi')->get()]);
    }

    public function kdvGrubuKaydet(Request $request)
    {
        $this->sistemYoneticisiKontrol();
        $veri = $request->validate(['grup_adi' => ['required', 'string', 'max:100', 'unique:kdv_urun_gruplari,grup_adi'], 'kdv_orani' => ['required', 'numeric', 'min:0', 'max:100']]);
        DB::table('kdv_urun_gruplari')->insert(array_merge($veri, ['aktif' => true, 'created_at' => now(), 'updated_at' => now()]));
        return back()->with('success', 'Ürün grubu KDV oranı kaydedildi.');
    }

    public function bakimKalemleri(Request $request, PeriyodikBakimKalemiServisi $bakimServisi)
    {
        $this->sistemYoneticisiKontrol();
        $firmalar = Firma::where('aktif', true)->orderBy('unvan')->get();
        $firmaId = $request->integer('firma_id') ?: $firmalar->first()?->id;
        $kalemler = $firmaId ? $bakimServisi->firmaIcin($firmaId) : [];

        return view('ayarlar.bakim-kalemleri', compact('firmalar', 'firmaId', 'kalemler'));
    }

    public function bakimKalemleriKaydet(Request $request, PeriyodikBakimKalemiServisi $bakimServisi)
    {
        $this->sistemYoneticisiKontrol();
        $veri = $request->validate([
            'firma_id' => ['required', 'exists:firmas,id'],
            'kalemler' => ['nullable', 'array'],
            'kalemler.*.kod' => ['required', 'alpha_dash', 'max:100'],
            'kalemler.*.ad' => ['required', 'string', 'max:120'],
            'kalemler.*.sira' => ['required', 'integer', 'min:1', 'max:999'],
            'kalemler.*.sil' => ['nullable', 'boolean'],
            'yeni_ad' => ['nullable', 'string', 'max:120'],
        ]);

        $kalemler = collect($veri['kalemler'] ?? [])->reject(fn (array $kalem) => (bool) ($kalem['sil'] ?? false))
            ->sortBy('sira')->values()->map(fn (array $kalem, int $sira) => ['kod'=>$kalem['kod'], 'ad'=>trim($kalem['ad']), 'sira'=>$sira + 1])->all();

        if (filled($veri['yeni_ad'] ?? null)) {
            $taban = Str::slug($veri['yeni_ad'], '_') ?: 'bakim_kalemi';
            $kod = $taban; $sayac = 2;
            while (collect($kalemler)->contains('kod', $kod)) $kod = $taban.'_'.($sayac++);
            $kalemler[] = ['kod'=>$kod, 'ad'=>trim($veri['yeni_ad']), 'sira'=>count($kalemler) + 1];
        }

        $bakimServisi->kaydet((int) $veri['firma_id'], $kalemler, auth()->id());
        return redirect()->route('ayarlar.bakim-kalemleri', ['firma_id'=>$veri['firma_id']])->with('success', 'Firmanın periyodik bakım listesi güncellendi.');
    }

    private function sistemYoneticisiKontrol(): void
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
    }

    private function yonetimSayfalari(): array
    {
        return [
            'bildirim' => ['baslik' => 'Bildirim Ayarları', 'ikon' => 'bi-bell-fill', 'aciklama' => 'Otomatik hatırlatma ve kutlama bildirimlerinin merkez ayarları.', 'alanlar' => ['servis_hatirlatma' => ['etiket' => 'Servis hatırlatmaları', 'tip' => 'checkbox', 'yardim' => 'Tarih bazlı bakım hatırlatmalarını oluştur.'], 'dogum_gunu' => ['etiket' => 'Doğum günü kutlamaları', 'tip' => 'checkbox', 'yardim' => 'Müşteri ve personel için kutlama kuyruğu oluştur.'], 'bayram_mesaji' => ['etiket' => 'Dini ve milli bayram mesajları', 'tip' => 'checkbox', 'yardim' => 'Onaylı iletişim kanallarına bayram mesajı planla.'], 'gonderim_saati' => ['etiket' => 'Planlı gönderim saati', 'tip' => 'time', 'varsayilan' => '10:00']]],
            'servis' => ['baslik' => 'Servis Tanımları', 'ikon' => 'bi-car-front-fill', 'aciklama' => 'Servis kabul ve periyodik bakım süreçlerinin ortak tanımları.', 'alanlar' => ['servis_no_on_eki' => ['etiket' => 'Servis numarası ön eki', 'varsayilan' => 'SRV'], 'varsayilan_kdv' => ['etiket' => 'Varsayılan KDV oranı', 'tip' => 'number', 'min' => '0', 'varsayilan' => '20'], 'teslim_kontrol_listesi' => ['etiket' => 'Teslim kontrol listesi', 'tip' => 'checkbox', 'yardim' => 'Teslimden önce kontrol adımlarını zorunlu tut.'], 'fotoğraf_zorunlu' => ['etiket' => 'Araç dış fotoğrafı', 'tip' => 'checkbox', 'yardim' => 'Servis kabulünde araç fotoğrafı istenmesini etkinleştir.']]],
            'sistem' => ['baslik' => 'Sistem Bilgileri', 'ikon' => 'bi-database-fill-gear', 'aciklama' => 'Sürüm, bakım modu ve teknik izleme için merkezi ayarlar.', 'alanlar' => ['bakim_modu' => ['etiket' => 'Bakım modu', 'tip' => 'checkbox', 'yardim' => 'Yalnız teknik yöneticilerin erişebileceği bakım modu.'], 'log_saklama_gun' => ['etiket' => 'Hata kayıt saklama süresi (gün)', 'tip' => 'number', 'min' => '30', 'varsayilan' => '180'], 'varsayilan_tema' => ['etiket' => 'Varsayılan tema', 'tip' => 'select', 'varsayilan' => 'acik', 'secenekler' => ['acik' => 'Açık tema', 'koyu' => 'Koyu tema']]]],
        ];
    }
}
