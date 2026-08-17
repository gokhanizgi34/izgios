<?php

namespace App\Http\Controllers;

use App\Mail\IkSifreTalepMaili;
use App\Models\IkIletisimAyari;
use App\Models\SifreYenilemeTalebi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SifreYonetimController extends Controller
{
    public function talepFormu() { return view('auth.passwords.talep'); }
    public function talepOlustur(Request $request)
    {
        $veri = $request->validate(['email' => ['required', 'email']]);
        $kullanici = User::query()->where('email', mb_strtolower($veri['email'], 'UTF-8'))->first();
        if (!$kullanici) { return back()->with('success', 'E-posta sistemde kayıtlıysa İK birimine talep iletildi.'); }
        $firmaId = $kullanici->firmaPersoneli?->firma_id;
        SifreYenilemeTalebi::create(['user_id' => $kullanici->id, 'firma_id' => $firmaId, 'istek_email' => $kullanici->email, 'durum' => 'eposta_gonderildi']);
        Password::sendResetLink(['email' => $kullanici->email]);
        return back()->with('success', 'E-posta adresiniz kayıtlıysa güvenli şifre sıfırlama bağlantısı gönderildi.');
    }
    public function sifirlaFormu(Request $request, string $token) { return view('auth.passwords.sifirla', ['token' => $token, 'email' => $request->email]); }
    public function sifirla(Request $request)
    {
        $veri = $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'min:8', 'confirmed']]);
        $durum = Password::reset($veri, function (User $user, string $password) { $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save(); });
        return $durum === Password::PASSWORD_RESET ? redirect()->route('login')->with('success', 'Şifreniz güncellendi. Giriş yapabilirsiniz.') : back()->withErrors(['email' => __($durum)]);
    }
    public function kendiSifreFormu() { abort_unless(auth()->check(), 403); return view('hesap.sifre-degistir'); }
    public function kendiSifreGuncelle(Request $request)
    {
        abort_unless(auth()->check(), 403);
        $veri = $request->validate(['mevcut_sifre' => ['required'], 'password' => ['required', 'min:8', 'confirmed']]);
        if (!Hash::check($veri['mevcut_sifre'], auth()->user()->password)) { return back()->withErrors(['mevcut_sifre' => 'Mevcut şifreniz doğru değil.']); }
        auth()->user()->update(['password' => Hash::make($veri['password'])]);
        return back()->with('success', 'Şifreniz güvenle güncellendi.');
    }
    public function ikTalepleri()
    {
        $this->ikKontrol();
        $sorgu = SifreYenilemeTalebi::with('kullanici')->latest();
        if (!auth()->user()->tamSistemYetkisiVarMi()) { $sorgu->where('firma_id', $this->aktifFirmaId()); }
        return view('ayarlar.ik.sifre-talepleri', ['talepler' => $sorgu->paginate(30)]);
    }
    public function ikOnayla(SifreYenilemeTalebi $talep)
    {
        $this->ikKontrol();
        abort_unless($talep->durum === 'bekliyor', 422, 'Bu talep daha önce işleme alınmış.');
        if (!auth()->user()->tamSistemYetkisiVarMi()) { abort_unless($talep->firma_id === $this->aktifFirmaId(), 403); }
        Password::sendResetLink(['email' => $talep->kullanici->email]);
        $talep->update(['durum' => 'onaylandi', 'isleyen_id' => auth()->id(), 'onaylandi_at' => now()]);
        return back()->with('success', 'Kullanıcıya güvenli şifre oluşturma bağlantısı gönderildi.');
    }
    public function ikAyarFormu() { $this->ikKontrol(); $firmaId = $this->aktifFirmaId(); return view('ayarlar.ik.iletisim', ['ayar' => IkIletisimAyari::query()->where('firma_id', $firmaId)->first(), 'firmaId' => $firmaId]); }
    public function ikAyarKaydet(Request $request)
    {
        $this->ikKontrol(); $veri = $request->validate(['sifre_talep_email' => ['required', 'email']]); $firmaId = $this->aktifFirmaId();
        IkIletisimAyari::updateOrCreate(['firma_id' => $firmaId], ['sifre_talep_email' => $veri['sifre_talep_email'], 'guncelleyen_id' => auth()->id()]);
        return back()->with('success', 'İK şifre talep e-postası güncellendi.');
    }
    private function aktifFirmaId(): ?int { return session('aktif_firma_id') ?: auth()->user()->firmaPersoneli?->firma_id; }
    private function ikKontrol(): void { abort_unless(auth()->check() && (auth()->user()->isIk() || auth()->user()->tamSistemYetkisiVarMi()), 403); }
}
