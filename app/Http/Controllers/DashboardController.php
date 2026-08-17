<?php

namespace App\Http\Controllers;

use App\Models\Arac;
use App\Models\Firma;
use App\Models\Musteri;
use App\Models\Servis;
use App\Models\Sube;
use App\Models\User;
use App\Models\DestekTalebi;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * Dashboard Ana Sayfası
     */
    public function index()
    {
        $kullanici = auth()->user();
        $firmaId = $kullanici?->tamSistemYetkisiVarMi() ? null : (session('aktif_firma_id') ?: $kullanici?->firmaPersoneli?->firma_id);
        abort_if(! $kullanici?->tamSistemYetkisiVarMi() && ! $firmaId, 403, 'Kullanıcı firma bağlantısı bulunamadı.');
        $musteriler = Musteri::query()->when($firmaId, fn ($q) => $q->where('firma_id', $firmaId));
        $araclar = Arac::query()->when($firmaId, fn ($q) => $q->where('firma_id', $firmaId));
        $servisler = Servis::query()->when($firmaId, fn ($q) => $q->where('firma_id', $firmaId));
        if ($kullanici?->isUsta()) $servisler->where('usta_id', $kullanici->id);

        $ozet = [
            'aktif_kullanici' => User::query()->when($firmaId, fn ($q) => $q->whereHas('firmaPersoneli', fn ($p) => $p->where('firma_id', $firmaId)))->where('status', 'aktif')->count(),
            'personel' => User::query()->when($firmaId, fn ($q) => $q->whereHas('firmaPersoneli', fn ($p) => $p->where('firma_id', $firmaId)))->count(),
            'musteri' => (clone $musteriler)->count(),
            'arac' => (clone $araclar)->count(),
            'firma' => Firma::query()->count(),
            'sube' => Sube::query()->count(),
            'bekliyor' => (clone $servisler)->where('durum', 'Bekliyor')->count(),
            'islemde' => (clone $servisler)->whereIn('durum', ['İşlemde', 'Islemde'])->count(),
            'hazir' => (clone $servisler)->whereIn('durum', ['Teslime Hazır', 'Teslime Hazir'])->count(),
            'toplam_servis' => (clone $servisler)->count(),
            'toplam_is_tutari' => (float) (clone $servisler)->sum('toplam_tutar'),
            'aktif_firma' => Firma::query()->where('aktif', true)->count(),
            'aktif_sube' => Sube::query()->where('aktif', true)->count(),
            'hata_sayisi' => $this->guncelHataSayisi(),
        ];

        $sonServisler = (clone $servisler)
            ->with(['arac', 'musteri'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $sistemVerileri = [
            'bugun_servis' => (clone $servisler)->whereDate('created_at', today())->count(),
            'ay_servis' => (clone $servisler)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count(),
            'ay_tutar' => (float) (clone $servisler)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('toplam_tutar'),
            'pasif_personel' => User::query()->where('status', 'pasif')->count(),
            'dogum_gunu' => User::query()->whereMonth('dogum_tarihi', now()->month)->whereDay('dogum_tarihi', now()->day)->count()
                + Musteri::query()->whereMonth('dogum_tarihi', now()->month)->whereDay('dogum_tarihi', now()->day)->count(),
            'acik_destek' => DestekTalebi::query()->whereIn('durum', ['acik', 'inceleniyor', 'ai_yonlendirildi'])->count(),
            'acil_destek' => DestekTalebi::query()->where('oncelik', 'acil')->whereNotIn('durum', ['cozuldu'])->count(),
            'son_kullanicilar' => User::query()->latest()->limit(6)->get(),
            'son_firmalar' => Firma::query()->latest()->limit(5)->get(),
            'son_destekler' => DestekTalebi::query()->with('kullanici')->latest()->limit(6)->get(),
            'mail_yapilandirildi' => config('mail.default') !== 'log',
            'ai_yapilandirildi' => filled(config('services.izgios_ai.key')),
        ];

        if (auth()->user()?->tamSistemYetkisiVarMi()) {
            return view('dashboard.system-admin', compact('ozet', 'sonServisler', 'sistemVerileri'));
        }

        return view('dashboard.role', compact('ozet', 'sonServisler'));
    }

    private function guncelHataSayisi(): int
    {
        $logYolu = storage_path('logs/laravel.log');
        if (!File::exists($logYolu)) { return 0; }

        $dosya = fopen($logYolu, 'rb');
        fseek($dosya, max(0, File::size($logYolu) - 262144));
        $icerik = stream_get_contents($dosya) ?: '';
        fclose($dosya);

        return preg_match_all('/\.(ERROR|CRITICAL|ALERT|EMERGENCY):/i', $icerik);
    }
}
