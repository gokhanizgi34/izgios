<?php

namespace App\Http\Controllers;

use App\Models\GelistirmeTalebi;
use App\Models\GelistirmeMesaji;
use App\Services\YapayZekaGelistirmeServisi;
use Illuminate\Http\Request;

class GelistirmeMerkeziController extends Controller
{
    public function index()
    {
        $this->yetkiKontrol();
        return view('gelistirme.surec', ['talepler' => GelistirmeTalebi::with(['olusturan', 'onaylayan'])->latest()->paginate(30)]);
    }

    public function store(Request $request)
    {
        $this->yetkiKontrol();
        $veri = $request->validate(['baslik' => ['required', 'string', 'max:180'], 'talep' => ['required', 'string', 'max:8000']]);
        $talep = GelistirmeTalebi::create(array_merge($veri, ['olusturan_id' => auth()->id(), 'durum' => 'onay_bekliyor']));
        GelistirmeMesaji::create(['gelistirme_talebi_id' => $talep->id, 'user_id' => auth()->id(), 'gonderen_tipi' => 'sistem_yoneticisi', 'mesaj' => 'Geliştirme talebi oluşturuldu: ' . $veri['talep']]);
        return back()->with('success', 'Geliştirme talebi onay kuyruğuna eklendi.');
    }

    public function show(GelistirmeTalebi $talep)
    {
        $this->yetkiKontrol();
        $talep->load(['mesajlar.kullanici', 'olusturan', 'onaylayan']);
        return view('gelistirme.sohbet', compact('talep'));
    }

    public function mesajGonder(Request $request, GelistirmeTalebi $talep)
    {
        $this->yetkiKontrol();
        $veri = $request->validate(['mesaj' => ['required', 'string', 'max:8000']]);
        GelistirmeMesaji::create(['gelistirme_talebi_id' => $talep->id, 'user_id' => auth()->id(), 'gonderen_tipi' => 'sistem_yoneticisi', 'mesaj' => $veri['mesaj']]);
        return back();
    }

    public function yapayZekaYaniti(Request $request, GelistirmeTalebi $talep, YapayZekaGelistirmeServisi $yapayZeka)
    {
        $this->yetkiKontrol();
        $veri = $request->validate(['mesaj' => ['nullable', 'string', 'max:8000']]);
        $mesaj = trim($veri['mesaj'] ?? '') ?: 'Mevcut geliştirme talebini analiz et ve uygulama planını hazırla.';
        $yanit = $yapayZeka->yanitla($talep, $mesaj);

        GelistirmeMesaji::create([
            'gelistirme_talebi_id' => $talep->id,
            'user_id' => null,
            'gonderen_tipi' => 'yapay_zeka',
            'mesaj' => $yanit['mesaj'],
        ]);

        if ($yanit['basarili']) {
            $talep->update(['cozum_plani' => $yanit['mesaj']]);
        }

        return back()->with($yanit['basarili'] ? 'success' : 'error', $yanit['basarili'] ? 'Yapay zekâ analiz ve plan yanıtını ekledi.' : $yanit['mesaj']);
    }

    public function onayla(GelistirmeTalebi $talep)
    {
        $this->yetkiKontrol();
        abort_unless($talep->durum === 'onay_bekliyor', 422, 'Bu talep onay beklemiyor.');
        $talep->update(['durum' => 'onaylandi', 'onaylayan_id' => auth()->id(), 'onaylandi_at' => now()]);
        GelistirmeMesaji::create(['gelistirme_talebi_id' => $talep->id, 'user_id' => auth()->id(), 'gonderen_tipi' => 'sistem', 'mesaj' => 'Sistem Yöneticisi talebi onayladı. Analiz ve çözüm planı aşamasına geçilebilir.']);
        return back()->with('success', 'Talep onaylandı. Güvenli dağıtım aracısı bağlandığında uygulama kuyruğuna aktarılacaktır.');
    }

    public function durumGuncelle(Request $request, GelistirmeTalebi $talep)
    {
        $this->yetkiKontrol();
        $veri = $request->validate(['durum' => ['required', 'in:analiz_ediliyor,uygulamada,test_ediliyor,tamamlandi,rejected']]);
        $gecisler = [
            'onaylandi' => ['analiz_ediliyor'],
            'analiz_ediliyor' => ['uygulamada'],
            'uygulamada' => ['test_ediliyor'],
            'test_ediliyor' => ['tamamlandi'],
        ];
        abort_unless(in_array($veri['durum'], $gecisler[$talep->durum] ?? [], true), 422, 'Bu süreç adımı için geçiş uygun değil.');
        $talep->update(['durum' => $veri['durum'], 'uygulandi_at' => $veri['durum'] === 'tamamlandi' ? now() : $talep->uygulandi_at]);
        GelistirmeMesaji::create(['gelistirme_talebi_id' => $talep->id, 'user_id' => auth()->id(), 'gonderen_tipi' => 'sistem', 'mesaj' => 'Süreç adımı güncellendi: ' . str_replace('_', ' ', $veri['durum'])]);
        return back()->with('success', 'Geliştirme süreci güncellendi.');
    }

    private function yetkiKontrol(): void
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
    }
}
