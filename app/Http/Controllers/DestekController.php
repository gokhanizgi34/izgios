<?php

namespace App\Http\Controllers;

use App\Models\DestekMesaji;
use App\Models\DestekTalebi;
use App\Models\User;
use App\Services\YapayZekaDestekServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DestekController extends Controller
{
    public function index()
    {
        $this->oturumKontrol();
        $sorgu = DestekTalebi::query()->with(['kullanici', 'firma', 'mesajlar.kullanici'])->latest();

        if (!auth()->user()->tamSistemYetkisiVarMi()) {
            $sorgu->where('user_id', auth()->id());
        }

        return view('destek.stable-index', ['talepler' => $sorgu->paginate(30)]);
    }

    public function create()
    {
        $this->oturumKontrol();
        return view('destek.create');
    }

    public function store(Request $request, YapayZekaDestekServisi $yapayZeka)
    {
        $this->oturumKontrol();
        $veri = $request->validate([
            'kategori' => ['required', 'in:genel,hesap,teknik,servis,muhasebe,stok'],
            'oncelik' => ['required', 'in:normal,yuksek,acil'],
            'baslik' => ['required', 'string', 'max:180'],
            'mesaj' => ['required', 'string', 'max:5000'],
            'hata_kodu' => ['nullable', 'string', 'max:70'],
        ]);

        $firmaId = session('aktif_firma_id') ?: auth()->user()->firmaPersoneli?->firma_id;

        $talep = DestekTalebi::create(array_merge($veri, [
            'user_id' => auth()->id(), 'firma_id' => $firmaId,
            'durum' => 'acik', 'ai_durum' => 'inceleniyor',
        ]));

        $analiz = $yapayZeka->analizEt($talep);
        $talep->update([
            'durum' => $analiz['durum'] === 'cozum_onerildi' ? 'ai_yonlendirildi' : 'acik',
            'ai_durum' => $analiz['durum'],
            'ai_ozet' => $analiz['ozet'],
            'ai_cozum' => $analiz['cozum'],
            'son_yanit_at' => now(),
        ]);

        DestekMesaji::create([
            'destek_talebi_id' => $talep->id,
            'user_id' => auth()->id(),
            'gonderen_tipi' => 'kullanici',
            'mesaj' => $talep->mesaj,
        ]);
        DestekMesaji::create([
            'destek_talebi_id' => $talep->id,
            'gonderen_tipi' => 'dervis',
            'mesaj' => $analiz['cozum'],
        ]);
        $this->yoneticiyeEpostaGonder($talep, true);

        return redirect()->route('destek.index')->with('success', 'Destek talebiniz kaydedildi. Derviş ön değerlendirmesini yaptı ve Sistem Yöneticisine e-posta bildirimi gönderildi.');
    }

    public function durumGuncelle(Request $request, DestekTalebi $talep)
    {
        $this->sistemYoneticisiKontrol();
        $talep->update($request->validate(['durum' => ['required', 'in:acik,inceleniyor,cozuldu,ai_yonlendirildi']]));
        return back()->with('success', 'Destek talebi durumu güncellendi.');
    }

    public function geriBildirim(Request $request, DestekTalebi $talep)
    {
        $this->oturumKontrol();
        abort_unless($talep->user_id === auth()->id() || auth()->user()->tamSistemYetkisiVarMi(), 403);

        $veri = $request->validate(['sonuc' => ['required', 'in:cozuldu,cozulemedi']]);
        $cozuldu = $veri['sonuc'] === 'cozuldu';

        $talep->update([
            'kullanici_geri_bildirimi' => $veri['sonuc'],
            'durum' => $cozuldu ? 'cozuldu' : 'inceleniyor',
            'ai_durum' => $cozuldu ? 'kullanici_onayladi' : 'sistem_yoneticisine_yonlendirildi',
            'zaman_asimi_at' => null,
        ]);

        return back()->with('success', $cozuldu
            ? 'Geri bildiriminiz kaydedildi. Talep çözüldü olarak kapatıldı.'
            : 'Talep Sistem Yöneticisi inceleme kuyruğuna aktarıldı. Derviş çözüm planını kayıt üzerinde tutar.');
    }

    public function mesajGonder(Request $request, DestekTalebi $talep, YapayZekaDestekServisi $yapayZeka)
    {
        $this->oturumKontrol();
        abort_unless($talep->user_id === auth()->id() || auth()->user()->tamSistemYetkisiVarMi(), 403);

        $veri = $request->validate(['mesaj' => ['required', 'string', 'max:3000']]);
        $yonetici = auth()->user()->tamSistemYetkisiVarMi();

        DestekMesaji::create([
            'destek_talebi_id' => $talep->id,
            'user_id' => auth()->id(),
            'gonderen_tipi' => $yonetici ? 'sistem_yoneticisi' : 'kullanici',
            'mesaj' => $veri['mesaj'],
        ]);

        if ($yonetici) {
            $talep->update(['durum' => 'inceleniyor', 'son_yanit_at' => now()]);
            return back()->with('success', 'Yanıtınız talep konuşmasına eklendi.');
        }

        $dervisYaniti = $yapayZeka->yanitlaMesaj($talep, $veri['mesaj']);
        DestekMesaji::create([
            'destek_talebi_id' => $talep->id,
            'gonderen_tipi' => 'dervis',
            'mesaj' => $dervisYaniti,
        ]);
        $talep->update(['durum' => 'inceleniyor', 'son_yanit_at' => now()]);
        $this->yoneticiyeEpostaGonder($talep, false, $veri['mesaj']);

        return back()->with('success', 'Mesajınız kaydedildi. Derviş yanıtladı ve Sistem Yöneticisine e-posta bildirimi gönderildi.');
    }

    private function yoneticiyeEpostaGonder(DestekTalebi $talep, bool $yeniTalep, ?string $yeniMesaj = null): void
    {
        $adresler = User::query()->where('role', 'sistem_yoneticisi')->whereNotNull('email')->pluck('email')->filter()->values()->all();
        if ($adresler === []) {
            $adresler = array_filter([config('mail.from.address')]);
        }

        try {
            Mail::send('emails.destek-talebi', [
                'talep' => $talep->loadMissing('kullanici'),
                'destekUrl' => route('destek.index'),
                'yeniMesaj' => $yeniMesaj,
            ], function ($mail) use ($adresler, $talep, $yeniTalep) {
                $mail->to($adresler)->subject(($yeniTalep ? 'Yeni destek talebi: ' : 'Destek talebi güncellendi: ') . $talep->baslik);
            });
        } catch (\Throwable $hata) {
            Log::warning('Destek yöneticisi e-posta bildirimi gönderilemedi.', ['talep_id' => $talep->id, 'sebep' => $hata->getMessage()]);
        }
    }

    private function oturumKontrol(): void { if (!auth()->check()) { abort(403); } }
    private function sistemYoneticisiKontrol(): void { $this->oturumKontrol(); if (!auth()->user()->tamSistemYetkisiVarMi()) { abort(403); } }
}
