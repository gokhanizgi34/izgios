@extends('layouts.app')
@section('title', 'İletişim Entegrasyonları')
@section('content')
@php
    $entegrasyon = $entegrasyonlar['email'] ?? null;
    $ayar = json_decode($entegrasyon?->ayarlar ?: '{}', true) ?: [];
    $hazir = ($entegrasyon?->durum ?? '') === 'yapilandirildi';
    $whatsapp = $entegrasyonlar['whatsapp'] ?? null;
    $whatsappAyar = json_decode($whatsapp?->ayarlar ?: '{}', true) ?: [];
    $whatsappHazir = ($whatsapp?->durum ?? '') === 'yapilandirildi';
    $sms = $entegrasyonlar['sms'] ?? null;
    $smsAyar = json_decode($sms?->ayarlar ?: '{}', true) ?: [];
    $smsHazir = ($sms?->durum ?? '') === 'yapilandirildi';
@endphp
<style>
    .mail-page{max-width:1080px;margin:auto}.mail-hero{padding:25px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#102a50,#0f766e)}.mail-hero p{margin:7px 0 0;color:#dff7f0}.integration-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.mail-card{margin-top:18px;padding:22px;border:1px solid #dce6ef;border-radius:16px;background:#fff}.mail-card.email-card{grid-column:1/-1}.mail-status{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}.mail-grid{display:grid;grid-template-columns:2fr 1fr;gap:12px}.mail-card label{font-size:12px;font-weight:800;color:#39516f;margin-top:12px}.mail-card .form-control,.mail-card .form-select{margin-top:5px;text-transform:none!important}.mail-note{margin-top:16px;padding:13px 15px;border-radius:12px;background:#f3f7fb;color:#52677f;font-size:13px}.tema-koyu .mail-card{background:#17233a;border-color:#30425c}.tema-koyu .mail-card h2{color:#f2f7ff}.tema-koyu .mail-note{background:#22314a;color:#c9d7e9}@media(max-width:760px){.integration-grid{grid-template-columns:1fr}.mail-card.email-card{grid-column:auto}}@media(max-width:620px){.mail-grid{grid-template-columns:1fr}.mail-status{align-items:flex-start;flex-direction:column}}
</style>
<section class="mail-page container py-4">
    <header class="mail-hero"><h1 class="h3 mb-0"><i class="bi bi-chat-square-dots-fill me-2"></i>API ve İletişim Entegrasyonları</h1><p>Merkezi yapay zekâ, e-posta, WhatsApp ve SMS bağlantılarını tek ekrandan yönetin.</p></header>
    @if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger mt-3">{{ $errors->first() }}</div>@endif
    @if(auth()->user()->tamSistemYetkisiVarMi())
    <article class="mail-card" style="border-left:4px solid #7c3aed">
        <div class="mail-status"><div><h2 class="h5 mb-1"><i class="bi bi-stars me-1"></i>Merkezi Yapay Zekâ API</h2><small class="text-muted">Bu tek bağlantı bütün firmalar ve tüm İZGİOS asistan ekranları tarafından kullanılır.</small></div><span class="badge text-bg-{{ $openAiGlobal ? 'success' : 'secondary' }}">{{ $openAiGlobal ? 'Tanımlı' : 'Kurulum gerekli' }}</span></div>
        <form method="POST" action="{{ route('ticari.api.yapay-zeka') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
            @csrf
            <label for="ai_provider">Sağlayıcı</label><select id="ai_provider" class="form-select" name="saglayici" required><option value="openai" @selected(old('saglayici',$aiAyarlar['saglayici'] ?? config('services.izgios_ai.provider','openai'))==='openai')>OpenAI</option><option value="gemini" @selected(old('saglayici',$aiAyarlar['saglayici'] ?? '')==='gemini')>Google Gemini</option></select>
            <label for="ai_model">Model</label><input id="ai_model" class="form-control" name="model" value="{{ old('model', $aiAyarlar['model'] ?? config('services.izgios_ai.model', 'gpt-5.6')) }}" required><small class="text-muted">Gemini için örnek: gemini-2.5-flash</small>
            <label for="ai_key">Yapay zekâ API anahtarı</label><input id="ai_key" class="form-control" type="password" name="api_anahtari" autocomplete="new-password" placeholder="{{ $openAiGlobal ? 'Kayıtlı anahtarı korumak için boş bırakın' : 'API anahtarını girin' }}">
            <div class="form-check form-switch mt-3"><input type="hidden" name="aktif" value="0"><input id="ai_active" class="form-check-input" type="checkbox" name="aktif" value="1" @checked(($aiAyarlar['aktif'] ?? ($openAiGlobal ? '1' : '0')) === '1')><label class="form-check-label mt-0" for="ai_active">Tüm sistemde etkin</label></div>
            <button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-shield-lock-fill me-1"></i>Merkezi Yapay Zekâyı Kaydet</button>
        </form>
        <div class="mail-note">Firma bazında ayrıca yapay zekâ anahtarı girilmez. Anahtar şifreli tutulur ve ekranda tekrar gösterilmez.</div>
    </article>
    <article class="mail-card" style="border-left:4px solid #0f766e">
        <div class="mail-status"><div><h2 class="h5 mb-1"><i class="bi bi-envelope-check-fill me-1"></i>Sistem Yöneticisi E-posta Hesabı</h2><small class="text-muted">Destek mesajları, sistem hataları ve tüm silme denetimleri bu merkezi hesaptan gönderilir ve bildirim adresine ulaşır.</small></div><span class="badge text-bg-{{ $sistemEmailHazir ? 'success' : 'secondary' }}">{{ $sistemEmailHazir ? 'Aktif' : 'Kurulum gerekli' }}</span></div>
        <form method="POST" action="{{ route('ticari.api.sistem-email') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
            @csrf
            <label for="sys_smtp_host">SMTP sunucusu</label><input id="sys_smtp_host" class="form-control" name="smtp_host" value="{{ old('smtp_host', $sistemEmailAyarlar['smtp_host'] ?? '') }}" placeholder="mail.izgios.com" required>
            <div class="mail-grid"><div><label for="sys_smtp_port">SMTP portu</label><input id="sys_smtp_port" class="form-control" type="number" name="smtp_port" min="1" max="65535" value="{{ old('smtp_port', $sistemEmailAyarlar['smtp_port'] ?? 465) }}" required></div><div><label for="sys_sifreleme">Güvenlik</label><select id="sys_sifreleme" class="form-select" name="smtp_sifreleme"><option value="ssl" @selected(($sistemEmailAyarlar['smtp_sifreleme'] ?? 'ssl')==='ssl')>SSL</option><option value="tls" @selected(($sistemEmailAyarlar['smtp_sifreleme'] ?? '')==='tls')>TLS</option><option value="none" @selected(($sistemEmailAyarlar['smtp_sifreleme'] ?? '')==='none')>Yok</option></select></div></div>
            <label for="sys_kullanici">SMTP kullanıcı adı</label><input id="sys_kullanici" class="form-control" name="kullanici_adi" value="{{ old('kullanici_adi', $sistemEmailAyarlar['kullanici_adi'] ?? '') }}" autocomplete="username" required>
            <label for="sys_sifre">E-posta hesabı şifresi</label><input id="sys_sifre" class="form-control" type="password" name="sifre" autocomplete="new-password" placeholder="{{ filled($sistemEmailAyarlar['sifre'] ?? null) ? 'Kayıtlı şifreyi korumak için boş bırakın' : 'E-posta hesabı şifresi' }}">
            <div class="mail-grid"><div><label for="sys_gonderen">Gönderen e-posta</label><input id="sys_gonderen" class="form-control" type="email" name="gonderen" value="{{ old('gonderen', $sistemEmailAyarlar['gonderen'] ?? '') }}" placeholder="sistem@izgios.com" required></div><div><label for="sys_gonderen_adi">Gönderen adı</label><input id="sys_gonderen_adi" class="form-control" name="gonderen_adi" value="{{ old('gonderen_adi', $sistemEmailAyarlar['gonderen_adi'] ?? 'İZGİOS Sistem Yönetimi') }}"></div></div>
            <label for="sys_alici">Sistem bildirimlerinin geleceği e-posta</label><input id="sys_alici" class="form-control" type="email" name="bildirim_alicisi" value="{{ old('bildirim_alicisi', $sistemEmailAyarlar['bildirim_alicisi'] ?? '') }}" placeholder="yonetim@izgios.com" required>
            <div class="form-check form-switch mt-3"><input type="hidden" name="aktif" value="0"><input id="sys_aktif" class="form-check-input" type="checkbox" name="aktif" value="1" @checked(($sistemEmailAyarlar['aktif'] ?? '0')==='1')><label class="form-check-label mt-0" for="sys_aktif">Sistem e-postalarını etkinleştir</label></div>
            <button class="btn btn-primary w-100 mt-3"><i class="bi bi-shield-lock-fill me-1"></i>Sistem E-posta Ayarlarını Kaydet</button>
        </form>
        @if($sistemEmailHazir)<form method="POST" action="{{ route('ticari.api.sistem-email-test') }}">@csrf<button class="btn btn-outline-success w-100 mt-2"><i class="bi bi-send-check me-1"></i>Sistem E-postasını Test Et</button></form>@endif
        <div class="mail-note">Firma servis bildirimleri firma hesabından çıkmaya devam eder. Bu hesap yalnız merkezi sistem yönetimi ve denetim mesajları içindir.</div>
    </article>
    @endif
    @if(auth()->user()->tamSistemYetkisiVarMi())
        <form class="card p-3 mt-3" method="GET" action="{{ route('ticari.api') }}"><label class="form-label" for="firma_id">Firma</label><select id="firma_id" name="firma_id" class="form-select" onchange="this.form.submit()">@forelse($firmalar as $firma)<option value="{{ $firma->id }}" @selected($firmaId == $firma->id)>{{ $firma->gosterim_adi }}</option>@empty<option>Önce firma oluşturun</option>@endforelse</select></form>
    @endif
    @if($firmaId)
      <div class="integration-grid">
        <article class="mail-card email-card">
            <div class="mail-status"><div><h2 class="h5 mb-1">SMTP gönderim hesabı</h2><small class="text-muted">Yalnızca e-posta gönderimi için gereken bilgiler saklanır.</small></div><span class="badge text-bg-{{ $hazir ? 'success' : 'secondary' }}">{{ $hazir ? 'Aktif' : 'Kurulum gerekli' }}</span></div>
            <form method="POST" action="{{ route('ticari.api.kaydet') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
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
        <article class="mail-card">
            <div class="mail-status"><div><h2 class="h5 mb-1"><i class="bi bi-whatsapp text-success me-1"></i>WhatsApp</h2><small class="text-muted">Meta Cloud API veya JSON uyumlu sağlayıcı.</small></div><span class="badge text-bg-{{ $whatsappHazir ? 'success' : 'secondary' }}">{{ $whatsappHazir ? 'Aktif' : 'Kurulum gerekli' }}</span></div>
            <form method="POST" action="{{ route('ticari.api.kaydet') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
                @csrf<input type="hidden" name="firma_id" value="{{ $firmaId }}"><input type="hidden" name="saglayici" value="whatsapp">
                <label for="wa_tur">Bağlantı türü</label><select id="wa_tur" class="form-select" name="saglayici_turu"><option value="meta_cloud" @selected(($whatsappAyar['saglayici_turu'] ?? 'meta_cloud') === 'meta_cloud')>Meta WhatsApp Cloud API</option><option value="http_json" @selected(($whatsappAyar['saglayici_turu'] ?? '') === 'http_json')>Genel JSON API</option></select>
                <label for="wa_endpoint">API adresi</label><input id="wa_endpoint" class="form-control" type="url" name="endpoint" value="{{ old('endpoint', $whatsappAyar['endpoint'] ?? '') }}" placeholder="https://graph.facebook.com/vXX.X/.../messages" required>
                <label for="wa_token">Erişim anahtarı</label><input id="wa_token" class="form-control" type="password" name="api_anahtari" autocomplete="new-password" placeholder="{{ $whatsapp?->api_anahtari_sifreli ? 'Kayıtlı anahtarı korumak için boş bırakın' : 'Kalıcı erişim anahtarı' }}">
                <label for="wa_gonderen">Gönderen telefon numarası</label><input id="wa_gonderen" class="form-control" name="gonderen" inputmode="tel" value="{{ old('gonderen', $whatsappAyar['gonderen'] ?? '') }}" placeholder="905xxxxxxxxx" required>
                <button class="btn btn-success w-100 mt-3" type="submit"><i class="bi bi-check2-circle me-1"></i>WhatsApp'ı Etkinleştir</button>
            </form>
        </article>
        <article class="mail-card">
            <div class="mail-status"><div><h2 class="h5 mb-1"><i class="bi bi-chat-dots-fill text-primary me-1"></i>SMS</h2><small class="text-muted">JSON API destekli SMS sağlayıcısı.</small></div><span class="badge text-bg-{{ $smsHazir ? 'success' : 'secondary' }}">{{ $smsHazir ? 'Aktif' : 'Kurulum gerekli' }}</span></div>
            <form method="POST" action="{{ route('ticari.api.kaydet') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
                @csrf<input type="hidden" name="firma_id" value="{{ $firmaId }}"><input type="hidden" name="saglayici" value="sms">
                <label for="sms_tur">Sağlayıcı</label><select id="sms_tur" class="form-select" name="saglayici_turu"><option value="netgsm" @selected(($smsAyar['saglayici_turu'] ?? '') === 'netgsm')>Netgsm</option><option value="iletimerkezi" @selected(($smsAyar['saglayici_turu'] ?? '') === 'iletimerkezi')>İleti Merkezi</option><option value="http_json" @selected(($smsAyar['saglayici_turu'] ?? 'http_json') === 'http_json')>Genel JSON API</option></select>
                <label for="sms_endpoint">API adresi</label><input id="sms_endpoint" class="form-control" type="url" name="endpoint" value="{{ old('endpoint', $smsAyar['endpoint'] ?? '') }}" placeholder="https://api.saglayici.com/sms" required>
                <label for="sms_token">API anahtarı / şifre</label><input id="sms_token" class="form-control" type="password" name="api_anahtari" autocomplete="new-password" placeholder="{{ $sms?->api_anahtari_sifreli ? 'Kayıtlı anahtarı korumak için boş bırakın' : 'API anahtarı' }}">
                <label for="sms_gonderen">Onaylı gönderici başlığı</label><input id="sms_gonderen" class="form-control" name="gonderen" value="{{ old('gonderen', $smsAyar['gonderen'] ?? '') }}" placeholder="IZGIOS" required>
                <label for="sms_kullanici">Kullanıcı adı <span class="text-muted fw-normal">(gerekiyorsa)</span></label><input id="sms_kullanici" class="form-control" name="kullanici_adi" value="{{ old('kullanici_adi', $smsAyar['kullanici_adi'] ?? '') }}" placeholder="SMS paneli kullanıcı adı">
                <button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-check2-circle me-1"></i>SMS'i Etkinleştir</button>
            </form>
        </article>
      </div>
    @else<div class="alert alert-info mt-3">İletişim entegrasyonu için önce aktif bir firma oluşturun.</div>@endif
</section>
@endsection
