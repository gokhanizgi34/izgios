<?php

namespace App\Http\Controllers;

use App\Services\YapayZekaHataAnalizServisi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SistemHataController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Hata izleme ekranı için oturum açmalısınız.');
        }

        if (!auth()->user()->tamSistemYetkisiVarMi()) {
            abort(403, 'Bu ekran yalnızca Sistem Yöneticisi içindir.');
        }

        $hatalar = $this->hatalar();

        return view('sistem-hatalari.ozet-v3', compact('hatalar'));
    }

    public function yapayZekaTara(YapayZekaHataAnalizServisi $yapayZeka)
    {
        if (!auth()->check() || !auth()->user()->tamSistemYetkisiVarMi()) {
            abort(403, 'Bu işlem yalnızca Sistem Yöneticisi içindir.');
        }

        $hatalar = $this->hatalar()->all();
        if (empty($hatalar)) {
            return back()->with('error', 'Analiz edilecek güncel hata kaydı bulunamadı.');
        }

        $cozulen = collect($hatalar)->filter(fn (array $hata) => $this->duzeltildiMi($hata));
        foreach ($cozulen as $hata) {
            DB::table('sistem_hata_durumlari')->updateOrInsert(
                ['hata_kodu' => $hata['kod']],
                ['durum' => 'cozuldu', 'kontrol_notu' => 'Tarama sırasında kaynak kod kontrolüyle doğrulandı.', 'isleyen_id' => auth()->id(), 'cozuldu_at' => now(), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $acikHatalar = collect($hatalar)->reject(fn (array $hata) => $cozulen->contains('kod', $hata['kod']))->values()->all();
        $sonuc = empty($acikHatalar) ? ['basarili' => true, 'mesaj' => 'Tarama tamamlandı. Açık hata bulunmadı.'] : $yapayZeka->analizEt($acikHatalar);
        $mesaj = $sonuc['mesaj'] . ($cozulen->isNotEmpty() ? "\n\n{$cozulen->count()} çözülmüş hata kaydı açık listeden kaldırıldı." : '');
        return back()->with($sonuc['basarili'] ? 'ai_hata_analizi' : 'error', $mesaj);
    }

    public function cozumPlaniniOnayla()
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
        return back()->with('success', 'Çözüm planı onaylandı. Uygulama adımı için Geliştirme Merkezi üzerinden ayrıca işlem oluşturulabilir.');
    }

    private function hatalar()
    {
        return collect(preg_split('/\R(?=\[\d{4}-\d{2}-\d{2} )/', $this->sonLogKayitlari()))
            ->filter(fn (string $kayit) => preg_match('/\.(ERROR|CRITICAL|ALERT|EMERGENCY):/i', $kayit) === 1)
            ->map(function (string $kayit) {
                preg_match('/^\[([^\]]+)\].*?\.(ERROR|CRITICAL|ALERT|EMERGENCY):\s*(.*)$/si', $kayit, $eslesme);
                return $this->hataOzeti($eslesme[1] ?? 'Bilinmiyor', strtoupper($eslesme[2] ?? 'ERROR'), $eslesme[3] ?? $kayit, $kayit);
            })
            ->reverse()->take(50)->values()
            ->reject(fn (array $hata) => DB::table('sistem_hata_durumlari')->where('hata_kodu', $hata['kod'])->where('durum', 'cozuldu')->exists())
            ->values();
    }

    private function hataOzeti(string $zaman, string $seviye, string $mesaj, string $hamKayit): array
    {
        $ekranlar = [
            'KullaniciController' => ['Personel ekranı', 'Personel kaydı veya kullanıcı yetkisi işlemi'],
            'MusteriController' => ['Müşteri ekranı', 'Müşteri kaydı veya müşteri bilgisi güncelleme işlemi'],
            'FirmaYonetimController' => ['Firma yönetimi', 'Firma kartı oluşturma veya güncelleme işlemi'],
            'SubeController' => ['Şube yönetimi', 'Şube kaydı veya şube bilgisi güncelleme işlemi'],
            'ServisKabulController' => ['Servis kabul ekranı', 'Araç servis kabul işlemi'],
            'ServisController' => ['İş emirleri ekranı', 'Servis kaydı veya iş emri işlemi'],
        ];

        $ekran = 'Sistem işlemi';
        $islem = 'Uygulama akışı sırasında işlem';
        foreach ($ekranlar as $anahtar => [$ekranAdi, $islemAdi]) {
            if (str_contains($hamKayit, $anahtar)) { $ekran = $ekranAdi; $islem = $islemAdi; break; }
        }

        if (str_contains(strtolower($hamKayit), 'duplicate entry') || str_contains(strtolower($hamKayit), 'unique constraint')) {
            $islem .= ' — aynı bilgi ikinci kez kaydedilmeye çalışıldı';
        }

        $sebep = trim(preg_replace('/\s+/', ' ', strtok($mesaj, "\n") ?: $mesaj));
        $sebep = preg_replace('/^[\\\w]+(?:Exception|Error):\s*/', '', $sebep);
        $sebep = mb_strimwidth($sebep ?: 'Sistem hata ayrıntısını kaydedemedi.', 0, 360, '…', 'UTF-8');

        return ['kod' => 'HATA-' . $seviye . '-' . strtoupper(substr(sha1($hamKayit), 0, 6)), 'zaman' => $zaman, 'ekran' => $ekran, 'islem' => $islem, 'sebep' => $sebep];
    }

    private function sonLogKayitlari(): string
    {
        $logYolu = storage_path('logs/laravel.log');
        if (!File::exists($logYolu)) { return ''; }

        $dosya = fopen($logYolu, 'rb');
        fseek($dosya, max(0, File::size($logYolu) - 262144));
        $icerik = stream_get_contents($dosya) ?: '';
        fclose($dosya);
        return $icerik;
    }

    private function duzeltildiMi(array $hata): bool
    {
        $sebep = mb_strtolower($hata['sebep'], 'UTF-8');
        if (str_contains($sebep, "unknown column 'son_km'")) {
            return !str_contains(File::get(app_path('Http/Controllers/ServisKabulController.php')), 'son_km');
        }
        if (str_contains($sebep, "unknown column 'merkez_mi'")) {
            return !str_contains(File::get(app_path('Http/Controllers/QrServisController.php')), 'merkez_mi');
        }
        if (str_contains($sebep, 'unexpected token "endif"')) {
            return !str_contains(File::get(resource_path('views/servisler/islem-v2.blade.php')), '@endif@if');
        }
        return false;
    }
}
