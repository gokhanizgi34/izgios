@php($aktifFirma = auth()->user()?->firmaPersoneli?->firma ?? (session('aktif_firma_id') ? \App\Models\Firma::find(session('aktif_firma_id')) : null))

<aside class="izgios-sidebar"
       id="izgios-sidebar">
    {{-- ================================================= --}}
    {{-- USER --}}
    {{-- ================================================= --}}

    <div class="sidebar-profile">

        <div class="profile-icon">

            <i class="bi bi-person-circle"></i>

        </div>

        <div class="profile-info">

            <strong>

                {{ auth()->user()?->tamAdi() ?? 'Misafir Kullanıcı' }}

            </strong>

            <span>

                {{ auth()->user()?->rolAdi() ?? 'Giriş yapınız' }}

            </span>

        </div>

    </div>



    {{-- ================================================= --}}
    {{-- MENU --}}
    {{-- ================================================= --}}

    <nav class="sidebar-navigation">
        @if(auth()->user()?->isIk())
            <div class="sidebar-section">
                <span>İNSAN KAYNAKLARI</span>
                <a href="{{ route('ik.index') }}" class="{{ request()->routeIs('ik.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i><label>İK Kontrol Paneli</label></a>
                <a href="{{ route('ik.index',['sekme'=>'personel']) }}" class="{{ request('sekme') === 'personel' ? 'active' : '' }}"><i class="bi bi-person-badge-fill"></i><label>Personel Yönetimi</label></a>
                <a href="{{ route('ik.index',['sekme'=>'puantaj']) }}" class="{{ request('sekme') === 'puantaj' ? 'active' : '' }}"><i class="bi bi-calendar-check-fill"></i><label>Puantaj ve Mesai</label></a>
                <a href="{{ route('ik.index',['sekme'=>'ozel-gun']) }}" class="{{ request('sekme') === 'ozel-gun' ? 'active' : '' }}"><i class="bi bi-calendar-heart-fill"></i><label>Özel Gün Takvimi</label></a>
                <a href="{{ route('ik.index',['sekme'=>'ozluk']) }}" class="{{ request('sekme') === 'ozluk' ? 'active' : '' }}"><i class="bi bi-folder2-open"></i><label>Özlük ve CV</label></a>
                <a href="{{ route('ik.index',['sekme'=>'ise-alim']) }}" class="{{ request('sekme') === 'ise-alim' ? 'active' : '' }}"><i class="bi bi-person-plus-fill"></i><label>İşe Alım</label></a>
                <a href="{{ route('ik.index',['sekme'=>'egitim']) }}" class="{{ request('sekme') === 'egitim' ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i><label>Eğitim ve Gelişim</label></a>
                <a href="{{ route('ik.index',['sekme'=>'performans']) }}" class="{{ request('sekme') === 'performans' ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i><label>Performans Yönetimi</label></a>
                <a href="{{ route('ik.index',['sekme'=>'ucret']) }}" class="{{ request('sekme') === 'ucret' ? 'active' : '' }}"><i class="bi bi-cash-stack"></i><label>Ücret ve Bordro</label></a>
                <a href="{{ route('ik.sifre.talepleri') }}" class="{{ request()->routeIs('ik.sifre.*') ? 'active' : '' }}"><i class="bi bi-key-fill"></i><label>Şifre Talepleri</label></a>
            </div>
        @else

        <div class="sidebar-section">

            <span>

                GENEL

            </span>

            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-fill"></i>

                <label>

                    Kontrol Paneli

                </label>

            </a>

        </div>



        @if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin() || auth()->user()?->isUsta() || auth()->user()?->isOfis())
        <div class="sidebar-section">
            <span>SERVİS</span>
            @if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin() || auth()->user()?->isOfis() || auth()->user()?->isUsta())
            <a href="{{ route('musteriler.index') }}" class="{{ request()->routeIs('musteriler.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i><label>Müşteriler</label>
            </a>
            @endif
            <a href="{{ route('araclar.index') }}" class="{{ request()->routeIs('araclar.*') ? 'active' : '' }}">
                <i class="bi bi-car-front-fill"></i><label>Araçlar</label>
            </a>
            @if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin() || auth()->user()?->isUsta())
            <a href="{{ route('servis.kabul') }}" class="{{ request()->routeIs('servis.kabul*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i><label>Servis Kabul</label>
            </a>
            <a href="{{ route('servisler.index') }}" class="{{ request()->routeIs('servisler.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i><label>İş Emirleri</label>
            </a>
            @endif
            <a href="{{ route('operasyon.randevular') }}" class="{{ request()->routeIs('operasyon.randevular*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week-fill"></i><label>Randevu ve Ajanda</label>
            </a>
        </div>
        @endif
        {{-- ================================================= --}}
{{-- MUHASEBE --}}
{{-- ================================================= --}}

@if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin() || auth()->user()?->isMuhasebe())
<div class="sidebar-section">
    <span>MUHASEBE</span>
    <a href="{{ route('ticari.index') }}" class="{{ request()->routeIs('ticari.index') ? 'active' : '' }}">
        <i class="bi bi-calculator-fill"></i><label>Muhasebe Merkezi</label>
    </a>
    <a href="{{ route('ticari.genel-muhasebe') }}" class="{{ request()->routeIs('ticari.genel-muhasebe*') ? 'active' : '' }}">
        <i class="bi bi-journal-bookmark-fill"></i><label>Genel Muhasebe</label>
    </a>
    <a href="{{ route('ticari.cari') }}" class="{{ request()->routeIs('ticari.cari*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i><label>Cari Hesaplar</label>
    </a>
    <a href="{{ route('ticari.belgeler', ['tur' => 'teklif']) }}" class="{{ request()->routeIs('ticari.belgeler') && request()->route('tur') === 'teklif' ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text-fill"></i><label>Teklifler</label>
    </a>
    <a href="{{ route('ticari.belgeler', ['tur' => 'fatura']) }}" class="{{ request()->routeIs('ticari.belgeler') && request()->route('tur') === 'fatura' ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i><label>Faturalar</label>
    </a>
    <a href="{{ route('ticari.fisler') }}" class="{{ request()->routeIs('ticari.fisler*') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i><label>Muhasebe Fişleri</label>
    </a>
</div>
@endif



{{-- ================================================= --}}
{{-- DEPO --}}
{{-- ================================================= --}}

@if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin() || auth()->user()?->isYedekParca())
<div class="sidebar-section">

    <span>

        DEPO

    </span>


    <a href="{{ route('depo.index') }}" class="{{ request()->routeIs('depo.*') ? 'active' : '' }}">

        <i class="bi bi-box-seam-fill"></i>

        <label>

            Stok Yönetimi

        </label>

    </a>


    <a href="{{ route('depo.barkod') }}" class="{{ request()->routeIs('depo.barkod') ? 'active' : '' }}">

        <i class="bi bi-upc-scan"></i>

        <label>

            Barkod Yönetimi

        </label>

    </a>

</div>
@endif



{{-- ================================================= --}}
{{-- YÖNETİM --}}
{{-- ================================================= --}}

<div class="sidebar-section">

    <span>

        YÖNETİM

    </span>


    <a href="{{ route('sohbet.index') }}" class="{{ request()->routeIs('sohbet.*') ? 'active' : '' }}">
        <i class="bi bi-chat-square-text-fill"></i>
        <label>Firma Sohbeti</label>
    </a>
    <a href="{{ route('operasyon.b2b') }}" class="{{ request()->routeIs('operasyon.b2b*') ? 'active' : '' }}">
        <i class="bi bi-cart-check-fill"></i><label>B2B Siparişler</label>
    </a>

    @if(auth()->check())
    <a href="{{ route('hesap.sifre') }}" class="{{ request()->routeIs('hesap.sifre') ? 'active' : '' }}">
        <i class="bi bi-key-fill"></i>
        <label>Şifremi Değiştir</label>
    </a>
    @endif

 </div>

@if(auth()->user()?->raporErisimiVarMi())
<div class="sidebar-section">
    <span>RAPORLAR</span>
    <a href="{{ route('raporlar.index') }}" class="{{ request()->routeIs('raporlar.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i><label>Rapor Merkezi</label>
    </a>
</div>
@endif

@if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin())
<div class="sidebar-section">
    <span>İNSAN KAYNAKLARI</span>
    <a href="{{ route('ik.index') }}" class="{{ request()->routeIs('ik.index') && !request('sekme') ? 'active' : '' }}"><i class="bi bi-people-fill"></i><label>İK Kontrol Paneli</label></a>
    <a href="{{ route('kullanicilar.index') }}" class="{{ request()->routeIs('kullanicilar.*') ? 'active' : '' }}"><i class="bi bi-person-badge-fill"></i><label>Personel Yönetimi</label></a>
    <a href="{{ route('ik.index',['sekme'=>'puantaj']) }}" class="{{ request('sekme') === 'puantaj' ? 'active' : '' }}"><i class="bi bi-calendar-check-fill"></i><label>Puantaj ve Mesai</label></a>
    <a href="{{ route('ik.index',['sekme'=>'ozel-gun']) }}" class="{{ request('sekme') === 'ozel-gun' ? 'active' : '' }}"><i class="bi bi-calendar-heart-fill"></i><label>Özel Gün Takvimi</label></a>
    <a href="{{ route('ik.index',['sekme'=>'ozluk']) }}" class="{{ request('sekme') === 'ozluk' ? 'active' : '' }}"><i class="bi bi-folder2-open"></i><label>Özlük ve CV</label></a>
    <a href="{{ route('ik.index',['sekme'=>'ise-alim']) }}" class="{{ request('sekme') === 'ise-alim' ? 'active' : '' }}"><i class="bi bi-person-plus-fill"></i><label>İşe Alım</label></a>
    <a href="{{ route('ik.index',['sekme'=>'egitim']) }}" class="{{ request('sekme') === 'egitim' ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i><label>Eğitim ve Gelişim</label></a>
    <a href="{{ route('ik.index',['sekme'=>'performans']) }}" class="{{ request('sekme') === 'performans' ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i><label>Performans Yönetimi</label></a>
    <a href="{{ route('ik.index',['sekme'=>'ucret']) }}" class="{{ request('sekme') === 'ucret' ? 'active' : '' }}"><i class="bi bi-cash-stack"></i><label>Ücret ve Bordro</label></a>
    <a href="{{ route('ik.sifre.talepleri') }}" class="{{ request()->routeIs('ik.sifre.*') ? 'active' : '' }}"><i class="bi bi-key-fill"></i><label>Şifre Talepleri</label></a>
</div>
@endif

@if(auth()->user()?->tamSistemYetkisiVarMi())
<div class="sidebar-section">
    <span>YAPAY ZEKÂ</span>
    <a href="{{ route('sistem.hatalari') }}" class="{{ request()->routeIs('sistem.hatalari') ? 'active' : '' }}">
        <i class="bi bi-shield-exclamation"></i><label>Sistem Hataları</label>
    </a>
    <a href="{{ route('sistem.silme-kayitlari') }}" class="{{ request()->routeIs('sistem.silme-kayitlari') ? 'active' : '' }}">
        <i class="bi bi-trash3-fill"></i><label>Silme Kayıtları</label>
    </a>
    <a href="{{ route('yapayzeka.index') }}" class="{{ request()->routeIs('yapayzeka.*') ? 'active' : '' }}">
        <i class="bi bi-cpu-fill"></i><label>Yapay Zekâ Merkezi</label>
    </a>
    <a href="{{ route('gelistirme.index') }}" class="{{ request()->routeIs('gelistirme.*') ? 'active' : '' }}">
        <i class="bi bi-code-square"></i><label>Geliştirme Merkezi</label>
    </a>
</div>
@endif

<div class="sidebar-section">
    <span>SİSTEM</span>
    <a href="{{ route('destek.index') }}" class="{{ request()->routeIs('destek.*') ? 'active' : '' }}">
        <i class="bi bi-life-preserver"></i><label>Destek Merkezi</label>
    </a>
    @if(auth()->user()?->tamSistemYetkisiVarMi() || auth()->user()?->isAdmin())
    <a href="{{ route('ayarlar.iletisim') }}" class="{{ request()->routeIs('ayarlar.iletisim*') ? 'active' : '' }}">
        <i class="bi bi-send-check-fill"></i><label>İletişim Ayarları</label>
    </a>
    <a href="{{ route('ticari.api') }}" class="{{ request()->routeIs('ticari.api*') ? 'active' : '' }}">
        <i class="bi bi-plug-fill"></i><label>API ve Entegrasyonlar</label>
    </a>
    @endif
    @if(auth()->user()?->isAdmin() && $aktifFirma)
    <a href="{{ route('firma.show', $aktifFirma) }}" class="{{ request()->routeIs('firma.*', 'sube.*') ? 'active' : '' }}">
        <i class="bi bi-building-gear"></i><label>Firma ve Şube Ayarları</label>
    </a>
    @endif
    @if(auth()->user()?->tamSistemYetkisiVarMi())
    <a href="{{ route('ayarlar.index') }}" class="{{ request()->routeIs('ayarlar.*', 'firma.*', 'sube.*') ? 'active' : '' }}">
        <i class="bi bi-gear-fill"></i><label>Sistem Ayarları</label>
    </a>
    @endif
</div>
        @endif
        <div class="sidebar-section">
            <span>BİLGİ MERKEZİ</span>
            <a href="{{ route('sss.index') }}" class="{{ request()->routeIs('sss.*') ? 'active' : '' }}">
                <i class="bi bi-question-circle-fill"></i><label>Sık Sorulan Sorular</label>
            </a>
        </div>
    </nav>


    {{-- ================================================= --}}
    {{-- ALT KULLANICI ALANI --}}
    {{-- ================================================= --}}

    <div class="sidebar-footer">

        <div class="sidebar-user-mini">

            <div class="mini-avatar">

                <i class="bi bi-person-circle"></i>

            </div>

            <div class="mini-info">

                <strong>

                    {{ auth()->user()?->tamAdi() ?? 'Kullanıcı' }}

                </strong>

                <span>

                    <i class="bi bi-circle-fill"></i>

                    Çevrimiçi

                </span>

            </div>

        </div>



        <div class="sidebar-footer-menu">

            <a href="{{ route('hesap.profil') }}" class="{{ request()->routeIs('hesap.profil*') ? 'active' : '' }}">

                <i class="bi bi-person-gear"></i>

                <label>

                    Profilim

                </label>

            </a>


            <a href="{{ route('hesap.tercihler') }}" class="{{ request()->routeIs('hesap.tercihler*') ? 'active' : '' }}">

                <i class="bi bi-sliders"></i>

                <label>

                    Tercihler

                </label>

            </a>


            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sidebar-logout"><i class="bi bi-box-arrow-right"></i><label>Güvenli Çıkış</label></button>
            </form>

        </div>



        <div class="sidebar-version">

            <strong>

                İZGİOS

            </strong>

            <span>

                v1.0.0

            </span>

        </div>

    </div>
<button id="sidebar-close"
        class="sidebar-close"
        type="button"
        aria-label="Menüyü kapat">
    <span aria-hidden="true">&times;</span>
</button>
</aside>
