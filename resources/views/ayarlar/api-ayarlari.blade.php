@extends('layouts.app')

@section('title', 'API ve Entegrasyon Ayarları')

@section('content')
@php
    $entegrasyonKartlari = [
        'whatsapp' => ['WhatsApp Business', 'bi-whatsapp', 'Müşteri süreç mesajları, servis evrakı ve bakım hatırlatmalarını WhatsApp Business sağlayıcısı üzerinden gönderin.', 'API erişim anahtarı', 'Telefon numarası / gönderici', 'https://graph.facebook.com/...'],
        'sms' => ['SMS Sağlayıcısı', 'bi-chat-dots-fill', 'Randevu, servis durumu ve özel gün mesajları için SMS sağlayıcınızı bağlayın.', 'API anahtarı / şifre', 'Başlık / gönderici adı', 'https://api.sms-saglayici.com/...'],
        'email' => ['E-posta (SMTP/API)', 'bi-envelope-fill', 'Servis evrakları ve e-posta bildirimleri için firma gönderen hesabını tanımlayın.', 'SMTP şifresi / API anahtarı', 'Gönderen e-posta adresi', 'https://api.resend.com/...'],
        'logo' => ['Logo Muhasebe', 'bi-calculator-fill', 'Muhasebe fişi ve cari aktarımı için sağlayıcı bağlantısı.', 'API anahtarı / erişim anahtarı', 'Firma kodu / kullanıcı', null],
        'parasut' => ['Paraşüt', 'bi-receipt-cutoff', 'Cari, fatura ve muhasebe aktarımı için sağlayıcı bağlantısı.', 'API anahtarı / erişim anahtarı', 'Firma kodu / kullanıcı', null],
        'bankacilik' => ['Banka Entegrasyonu', 'bi-bank2', 'Hesap hareketlerini güvenli banka sağlayıcısından içeri alma hazırlığı.', 'API anahtarı / erişim anahtarı', 'Hesap / müşteri numarası', null],
        'efatura' => ['E-Fatura Sağlayıcısı', 'bi-file-earmark-text-fill', 'Özel entegratör üzerinden e-Fatura/e-Arşiv gönderim bağlantısı.', 'API anahtarı / erişim anahtarı', 'Etiket / kullanıcı', null],
        'gib' => ['GİB / e-Fatura', 'bi-building-check', 'GİB uyumlu entegratör erişim bilgileri. Gerçek belge kesimi sağlayıcı sözleşmesi ve canlı API yetkisiyle etkinleşir.', 'API anahtarı / erişim anahtarı', 'Etiket / kullanıcı', null],
        'openai' => ['OpenAI Yapay Zekâ', 'bi-cpu-fill', 'Yalnız izin verilen maskelenmiş destek ve analiz verileri için.', 'API anahtarı', 'Proje / kullanım notu', null],
    ];
@endphp

<style>
    .api-page{max-width:1240px;margin:auto}.api-hero{padding:26px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#102a50,#0f766e)}.api-hero p{margin:7px 0 0;color:#dff7f0;max-width:850px}.api-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:18px}.api-card{padding:20px;border:1px solid #dce6ef;border-radius:16px;background:#fff}.api-card__head{display:flex;gap:11px;align-items:flex-start}.api-card__icon{display:grid;place-items:center;width:40px;height:40px;flex:0 0 40px;border-radius:12px;background:#fff4c8;color:#946d00;font-size:19px}.api-card h2{font-size:17px;margin:0}.api-card p{color:#64748b;font-size:13px;min-height:38px;margin:8px 0 15px}.api-card label{font-size:12px;font-weight:800;color:#39516f}.api-card small{color:#74849a;font-size:11px}.api-card .form-control{margin-top:5px}.api-card .btn{margin-top:13px}.api-help{margin-top:18px;padding:16px 18px;border:1px solid #d6e4f1;border-radius:14px;background:#f8fbff;color:#3e5877;font-size:13px}.tema-koyu .api-card{background:#17233a;border-color:#30425c}.tema-koyu .api-card h2{color:#f2f7ff}.tema-koyu .api-card p,.tema-koyu .api-card small{color:#b9c8db}.tema-koyu .api-help{background:#17233a;border-color:#30425c;color:#c9d7e9}@media(max-width:760px){.api-grid{grid-template-columns:1fr}}
    .api-page input,.api-page textarea{text-transform:none!important}
</style>

<section class="api-page container py-4">
    <header class="api-hero">
        <h1 class="h3 mb-0"><i class="bi bi-plug-fill me-2"></i>API ve İletişim Entegrasyonları</h1>
        <p>Her firma kendi WhatsApp, SMS, e-posta ve diğer servis bağlantılarını burada yönetir. Gizli anahtarlar şifrelenerek saklanır; ekranda tekrar açık gösterilmez.</p>
    </header>

    @if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger mt-3">{{ $errors->first() }}</div>@endif

    @if(auth()->user()->tamSistemYetkisiVarMi())
        <form class="card p-3 mt-3" method="GET" action="{{ route('ticari.api') }}">
            <label class="form-label" for="firma_id">Firma</label>
            <select id="firma_id" name="firma_id" class="form-select" onchange="this.form.submit()">
                @forelse($firmalar as $firma)<option value="{{ $firma->id }}" @selected($firmaId == $firma->id)>{{ $firma->gosterim_adi }}</option>@empty<option>Önce firma oluşturun</option>@endforelse
            </select>
        </form>
    @endif

    @if($firmaId)
        <div class="api-grid">
            @foreach($entegrasyonKartlari as $kod => [$ad,$ikon,$aciklama,$anahtarEtiketi,$gonderenEtiketi,$endpointOrnek])
                @php
                    $entegrasyon = $entegrasyonlar[$kod] ?? null;
                    $ayar = json_decode($entegrasyon?->ayarlar ?: '{}', true) ?: [];
                    $hazir = ($entegrasyon?->durum ?? '') === 'yapilandirildi' || ($kod === 'openai' && $openAiGlobal);
                @endphp
                <article class="api-card">
                    <div class="api-card__head">
                        <span class="api-card__icon"><i class="bi {{ $ikon }}"></i></span>
                        <div class="flex-grow-1"><div class="d-flex justify-content-between gap-2"><h2>{{ $ad }}</h2><span class="badge text-bg-{{ $hazir ? 'success' : 'secondary' }}">{{ $hazir ? 'Yapılandırıldı' : 'Yapılandırılmadı' }}</span></div><p>{{ $aciklama }}</p></div>
                    </div>
                    <form method="POST" action="{{ route('ticari.api.kaydet') }}" autocapitalize="none" spellcheck="false" data-preserve-case>
                        @csrf
                        <input type="hidden" name="firma_id" value="{{ $firmaId }}">
                        <input type="hidden" name="saglayici" value="{{ $kod }}">
                        @if(in_array($kod, ['whatsapp','sms'], true))
                            <label for="{{ $kod }}_tur">Sağlayıcı / bağlantı tipi</label>
                            <select id="{{ $kod }}_tur" class="form-select" name="saglayici_turu">
                                @foreach(($kod === 'whatsapp' ? ['meta_cloud'=>'Meta WhatsApp Cloud API','http_json'=>'Genel JSON API'] : ['netgsm'=>'Netgsm','iletimerkezi'=>'İleti Merkezi','http_json'=>'Genel JSON API']) as $deger=>$metin)
                                    <option value="{{ $deger }}" @selected(($ayar['saglayici_turu'] ?? '') === $deger)>{{ $metin }}</option>
                                @endforeach
                            </select>
                        @endif
                        <label for="{{ $kod }}_anahtar">{{ $anahtarEtiketi }}</label>
                        <input id="{{ $kod }}_anahtar" class="form-control" type="password" name="api_anahtari" autocomplete="new-password" placeholder="{{ $entegrasyon?->api_anahtari_sifreli ? '•••••••• kayıtlı anahtar korunur' : $anahtarEtiketi }}">
                        <label class="mt-2" for="{{ $kod }}_gonderen">{{ $gonderenEtiketi }}</label>
                        <input id="{{ $kod }}_gonderen" class="form-control" name="gonderen" value="{{ old('gonderen', $ayar['gonderen'] ?? '') }}" placeholder="{{ $gonderenEtiketi }}">
                        <label class="mt-2" for="{{ $kod }}_kullanici">Kullanıcı / firma kodu <span class="text-muted fw-normal">(isteğe bağlı)</span></label>
                        <input id="{{ $kod }}_kullanici" class="form-control" name="kullanici_adi" value="{{ old('kullanici_adi', $ayar['kullanici_adi'] ?? '') }}" placeholder="Kullanıcı adı veya firma kodu">
                        @if($kod === 'email')
                            <label class="mt-2" for="email_smtp_host">SMTP sunucusu</label>
                            <input id="email_smtp_host" class="form-control" name="smtp_host" value="{{ old('smtp_host', $ayar['smtp_host'] ?? '') }}" placeholder="mail.firmaniz.com">
                            <div class="row g-2 mt-1"><div class="col-6"><label for="email_smtp_port">SMTP portu</label><input id="email_smtp_port" class="form-control" type="number" name="smtp_port" value="{{ old('smtp_port', $ayar['smtp_port'] ?? 465) }}"></div><div class="col-6"><label for="email_sifreleme">Güvenlik</label><select id="email_sifreleme" class="form-select" name="smtp_sifreleme"><option value="ssl" @selected(($ayar['smtp_sifreleme'] ?? 'ssl') === 'ssl')>SSL (genelde 465)</option><option value="tls" @selected(($ayar['smtp_sifreleme'] ?? '') === 'tls')>TLS (genelde 587)</option><option value="none" @selected(($ayar['smtp_sifreleme'] ?? '') === 'none')>Yok</option></select></div></div>
                            <label class="mt-2" for="email_gonderen_adi">Gönderen adı</label>
                            <input id="email_gonderen_adi" class="form-control" name="gonderen_adi" value="{{ old('gonderen_adi', $ayar['gonderen_adi'] ?? '') }}" placeholder="Firma / servis adı">
                            <small class="d-block mt-2">Görseldeki IHS örneği için: kullanıcı adı ve gönderen adresi <b>servis@izgiotoservis.com</b>, SMTP sunucusu <b>mail.izgiotoservis.com</b>, port <b>465</b>, güvenlik <b>SSL</b>. Şifre alanına e-posta hesabının şifresi yazılır.</small>
                        @endif
                        @if($endpointOrnek)
                            <label class="mt-2" for="{{ $kod }}_endpoint">API uç noktası <span class="text-muted fw-normal">(isteğe bağlı)</span></label>
                            <input id="{{ $kod }}_endpoint" class="form-control" type="url" name="endpoint" value="{{ old('endpoint', $ayar['endpoint'] ?? '') }}" placeholder="{{ $endpointOrnek }}">
                        @endif
                        @if($kod === 'whatsapp')<small class="d-block mt-2">Telefon numarasını ülke koduyla (ör. 905...) girin. Bu numara QR ekranındaki iletişim düğmesinde de kullanılır. Cloud API gönderimi için API anahtarı ve uç noktası ayrıca gereklidir.</small>@else<small class="d-block mt-2">Sağlayıcı hesabı, onaylı gönderici ve canlı erişim bilgileri olmadan gerçek gönderim başlatılmaz.</small>@endif
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-shield-lock-fill me-1"></i>Bağlantı Bilgilerini Kaydet</button>
                    </form>
                </article>
            @endforeach
        </div>
        <aside class="api-help"><strong><i class="bi bi-info-circle me-1"></i>İletişim akışları:</strong> Kanal seçimi ve mesaj şablonları <a href="{{ route('ayarlar.iletisim', ['firma_id' => $firmaId]) }}">İletişim ve Hatırlatma Merkezi</a> ekranından yapılır. Bu ekran ise sağlayıcı erişim bilgilerini saklar.</aside>
    @else
        <div class="alert alert-info mt-3">Entegrasyon ayarı için önce aktif bir firma oluşturun.</div>
    @endif
</section>
@endsection
