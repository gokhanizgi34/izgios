@extends('layouts.app')

@section('title', 'Sistem Ayarları')

@section('content')
<style>
    .settings-menu { max-width: 1180px; margin: 0 auto; }
    .settings-menu__header { margin-bottom: 26px; }
    .settings-menu__header h1 { margin: 0; color: #14213d; font-size: 28px; font-weight: 800; }
    .settings-menu__header p { margin: 8px 0 0; color: #64748b; }
    .settings-menu__grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
    .settings-menu__card { min-height: 205px; padding: 24px; background: #fff; border: 1px solid #e6ebf2; border-radius: 18px; box-shadow: 0 7px 22px rgba(15, 23, 42, .06); display: flex; flex-direction: column; }
    .settings-menu__icon { width: 48px; height: 48px; border-radius: 13px; display: grid; place-items: center; font-size: 22px; background: #dbeafe; color: #2563eb; }
    .settings-menu__card h2 { margin: 16px 0 7px; font-size: 18px; color: #1e293b; }
    .settings-menu__card p { margin: 0; color: #64748b; font-size: 14px; line-height: 1.55; }
    .settings-menu__card a { margin-top: auto; padding-top: 20px; color: #2563eb; font-weight: 800; text-decoration: none; }
    .settings-menu__card--soon { opacity: .72; }
    .settings-menu__card--soon .settings-menu__icon { background: #f1f5f9; color: #64748b; }
    .settings-menu__card--soon a { color: #64748b; cursor: default; }
    @media (max-width: 900px) { .settings-menu__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 580px) { .settings-menu__grid { grid-template-columns: 1fr; } .settings-menu__header h1 { font-size: 24px; } }
</style>

<section class="settings-menu">
    <header class="settings-menu__header">
        <h1><i class="bi bi-gear-fill"></i> Sistem Ayarları</h1>
        <p>Firma, şube, personel ve sistem tanımlarını buradan yönetin.</p>
    </header>

    <div class="settings-menu__grid">
        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-buildings"></i></div>
            <h2>Firma Yönetimi</h2>
            <p>Firma kartları, firma bilgileri ve firmalara bağlı şubelerin yönetimi.</p>
            <a href="{{ route('firma.index') }}">Firma ekranına git <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-people-fill"></i></div>
            <h2>Personel Yönetimi</h2>
            <p>Personel kaydı, görev, şube bağlantısı ve doğum günü bilgileri bu alanda yer alacak.</p>
            <a href="{{ route('kullanicilar.index') }}">Personel ekranına git <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h2>Rol ve Yetkiler</h2>
            <p>Yönetici, usta, ofis, muhasebe ve yedek parça yetkilerinin tanımları.</p>
            <a href="{{ route('ayarlar.roller') }}">Rol ve yetkileri gör <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-qr-code"></i></div>
            <h2>QR İletişim Ayarları</h2>
            <p>QR ekranındaki WhatsApp düğmesini her şubenin servis hattına bağlayın.</p>
            <a href="{{ route('ayarlar.qr.iletisim') }}">WhatsApp hattını tanımla <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-plug-fill"></i></div>
            <h2>API ve Entegrasyonlar</h2>
            <p>WhatsApp, SMS, e-posta, muhasebe, banka, GİB/e-Fatura ve yapay zekâ erişim bilgilerini firma bazında yönetin.</p>
            <a href="{{ route('ticari.api') }}">Entegrasyon ayarlarını aç <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-send-check-fill"></i></div>
            <h2>İletişim ve Hatırlatma</h2>
            <p>Randevu, servis, bakım, evrak ve özel gün mesajlarında kullanılacak kanalları ve şablonları seçin.</p>
            <a href="{{ route('ayarlar.iletisim') }}">Mesaj akışlarını düzenle <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-percent"></i></div>
            <h2>KDV Ürün Grupları</h2>
            <p>Ürün gamlarına göre kullanılacak KDV oranlarını fiş girişleri için tanımlayın.</p>
            <a href="{{ route('ayarlar.kdv.gruplari') }}">KDV oranlarını yönet <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-bell-fill"></i></div>
            <h2>Bildirim Ayarları</h2>
            <p>Servis hatırlatma, doğum günü ve bayram tebrik mesajlarının ayarları.</p>
            <a href="{{ route('ayarlar.yonetim', 'bildirim') }}">Bildirim ayarlarını aç <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-car-front-fill"></i></div>
            <h2>Servis Tanımları</h2>
            <p>İşlem türleri, periyodik bakım ve servis kabul ayarları.</p>
            <a href="{{ route('ayarlar.yonetim', 'servis') }}">Servis tanımlarını aç <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-wrench-adjustable-circle-fill"></i></div>
            <h2>Periyodik Bakım Kalemleri</h2>
            <p>Her firma için bakım kalemlerini ekleyin, yeniden adlandırın, sıralayın veya listeden çıkarın.</p>
            <a href="{{ route('ayarlar.bakim-kalemleri') }}">Bakım listesini yönet <i class="bi bi-arrow-right"></i></a>
        </article>

        <article class="settings-menu__card">
            <div class="settings-menu__icon"><i class="bi bi-database-fill-gear"></i></div>
            <h2>Sistem Bilgileri</h2>
            <p>Yedekleme, sürüm ve teknik sistem ayarları.</p>
            <a href="{{ route('ayarlar.yonetim', 'sistem') }}">Sistem bilgilerini aç <i class="bi bi-arrow-right"></i></a>
        </article>
    </div>
</section>
@endsection
