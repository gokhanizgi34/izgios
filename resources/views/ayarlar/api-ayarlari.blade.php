@extends('layouts.app')
@section('title', 'E-posta Entegrasyonu')
@section('content')
@php
    $entegrasyon = $entegrasyonlar['email'] ?? null;
    $ayar = json_decode($entegrasyon?->ayarlar ?: '{}', true) ?: [];
    $hazir = ($entegrasyon?->durum ?? '') === 'yapilandirildi';
@endphp
<style>
    .mail-page{max-width:820px;margin:auto}.mail-hero{padding:25px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#102a50,#0f766e)}.mail-hero p{margin:7px 0 0;color:#dff7f0}.mail-card{margin-top:18px;padding:22px;border:1px solid #dce6ef;border-radius:16px;background:#fff}.mail-status{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}.mail-grid{display:grid;grid-template-columns:2fr 1fr;gap:12px}.mail-card label{font-size:12px;font-weight:800;color:#39516f;margin-top:12px}.mail-card .form-control,.mail-card .form-select{margin-top:5px;text-transform:none!important}.mail-note{margin-top:16px;padding:13px 15px;border-radius:12px;background:#f3f7fb;color:#52677f;font-size:13px}.tema-koyu .mail-card{background:#17233a;border-color:#30425c}.tema-koyu .mail-card h2{color:#f2f7ff}.tema-koyu .mail-note{background:#22314a;color:#c9d7e9}@media(max-width:620px){.mail-grid{grid-template-columns:1fr}.mail-status{align-items:flex-start;flex-direction:column}}
</style>
<section class="mail-page container py-4">
    <header class="mail-hero"><h1 class="h3 mb-0"><i class="bi bi-envelope-check-fill me-2"></i>E-posta Entegrasyonu</h1><p>Servis kabulü, işlem durumu ve bakım bildirimlerini firmanızın SMTP hesabından gönderin.</p></header>
    @if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger mt-3">{{ $errors->first() }}</div>@endif
    @if(auth()->user()->tamSistemYetkisiVarMi())
        <form class="card p-3 mt-3" method="GET" action="{{ route('ticari.api') }}"><label class="form-label" for="firma_id">Firma</label><select id="firma_id" name="firma_id" class="form-select" onchange="this.form.submit()">@forelse($firmalar as $firma)<option value="{{ $firma->id }}" @selected($firmaId == $firma->id)>{{ $firma->gosterim_adi }}</option>@empty<option>Önce firma oluşturun</option>@endforelse</select></form>
    @endif
    @if($firmaId)
        <article class="mail-card">
            <div class="mail-status"><div><h2 class="h5 mb-1">SMTP gönderim hesabı</h2><small class="text-muted">Yalnızca e-posta gönderimi için gereken bilgiler saklanır.</small></div><span class="badge text-bg-{{ $hazir ? 'success' : 'secondary' }}">{{ $hazir ? 'Aktif' : 'Kurulum gerekli' }}</span></div>
            <form method="POST" action="{{ route('ticari.api.kaydet') }}" autocapitalize="none" spellcheck="false">
                @csrf
                <input type="hidden" name="firma_id" value="{{ $firmaId }}"><input type="hidden" name="saglayici" value="email">
                <label for="email_smtp_host">SMTP sunucusu</label><input id="email_smtp_host" class="form-control" name="smtp_host" value="{{ old('smtp_host', $ayar['smtp_host'] ?? '') }}" placeholder="mail.firmaniz.com" required>
                <div class="mail-grid"><div><label for="email_smtp_port">SMTP portu</label><input id="email_smtp_port" class="form-control" type="number" name="smtp_port" min="1" max="65535" value="{{ old('smtp_port', $ayar['smtp_port'] ?? 465) }}" required></div><div><label for="email_sifreleme">Güvenlik</label><select id="email_sifreleme" class="form-select" name="smtp_sifreleme"><option value="ssl" @selected(($ayar['smtp_sifreleme'] ?? 'ssl') === 'ssl')>SSL</option><option value="tls" @selected(($ayar['smtp_sifreleme'] ?? '') === 'tls')>TLS</option><option value="none" @selected(($ayar['smtp_sifreleme'] ?? '') === 'none')>Yok</option></select></div></div>
                <label for="email_kullanici">SMTP kullanıcı adı</label><input id="email_kullanici" class="form-control" name="kullanici_adi" value="{{ old('kullanici_adi', $ayar['kullanici_adi'] ?? '') }}" autocomplete="username" placeholder="servis@firmaniz.com" required>
                <label for="email_anahtar">E-posta hesabı şifresi</label><input id="email_anahtar" class="form-control" type="password" name="api_anahtari" autocomplete="new-password" placeholder="{{ $entegrasyon?->api_anahtari_sifreli ? 'Kayıtlı şifreyi değiştirmemek için boş bırakın' : 'E-posta hesabı şifresi' }}">
                <label for="email_gonderen">Gönderen e-posta adresi</label><input id="email_gonderen" class="form-control" type="email" name="gonderen" value="{{ old('gonderen', $ayar['gonderen'] ?? '') }}" placeholder="servis@firmaniz.com" required>
                <label for="email_gonderen_adi">Gönderen adı</label><input id="email_gonderen_adi" class="form-control" name="gonderen_adi" value="{{ old('gonderen_adi', $ayar['gonderen_adi'] ?? '') }}" placeholder="Firma veya servis adı">
                <button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-shield-lock-fill me-1"></i>Ayarları Kaydet ve Etkinleştir</button>
            </form>
            @if($hazir)<form method="POST" action="{{ route('ticari.api.email-test') }}">@csrf<input type="hidden" name="firma_id" value="{{ $firmaId }}"><button class="btn btn-outline-success w-100 mt-2" type="submit"><i class="bi bi-send-check me-1"></i>Bağlantıyı Test Et</button></form>@endif
            <div class="mail-note"><i class="bi bi-info-circle me-1"></i>SSL çoğunlukla 465, TLS çoğunlukla 587 portunu kullanır. Test e-postası gönderen adresinin kendi gelen kutusuna gönderilir.</div>
        </article>
    @else<div class="alert alert-info mt-3">E-posta ayarı için önce aktif bir firma oluşturun.</div>@endif
</section>
@endsection
