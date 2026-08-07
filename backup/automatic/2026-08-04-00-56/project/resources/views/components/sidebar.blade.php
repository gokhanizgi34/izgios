<aside class="izgios-sidebar"
       id="izgios-sidebar">
    {{-- ================================================= --}}
    {{-- LOGO --}}
    {{-- ================================================= --}}

    <div class="sidebar-brand">

        <a href="{{ route('dashboard') }}"
           class="sidebar-logo">

            <div class="sidebar-logo-text">

                <span class="logo-white">

                    İZGİ

                </span>

                <span class="logo-yellow">

                    OS

                </span>

            </div>

            <div class="sidebar-logo-subtitle">

                İzgi Oto Servis Yönetim Sistemi

            </div>

        </a>

    </div>



    {{-- ================================================= --}}
    {{-- USER --}}
    {{-- ================================================= --}}

    <div class="sidebar-profile">

        <div class="profile-icon">

            <i class="bi bi-person-circle"></i>

        </div>

        <div class="profile-info">

            <strong>

                Gökhan İzgi

            </strong>

            <span>

                Sistem Yöneticisi

            </span>

        </div>

    </div>



    {{-- ================================================= --}}
    {{-- MENU --}}
    {{-- ================================================= --}}

    <nav class="sidebar-navigation">

        <div class="sidebar-section">

            <span>

                GENEL

            </span>

            <a href="{{ route('dashboard') }}"
               class="active">

                <i class="bi bi-grid-fill"></i>

                <label>

                    İZGİOS

                </label>

            </a>

        </div>



        <div class="sidebar-section">

            <span>

                SERVİS

            </span>

            <a href="#">

                <i class="bi bi-people-fill"></i>

                <label>

                    Müşteriler

                </label>

            </a>

            <a href="#">

                <i class="bi bi-car-front-fill"></i>

                <label>

                    Araçlar

                </label>

            </a>

            <a href="#">

                <i class="bi bi-clipboard-check-fill"></i>

                <label>

                    Servis Kabul

                </label>

            </a>

            <a href="#">

                <i class="bi bi-tools"></i>

                <label>

                    İş Emirleri

                </label>

            </a>

        </div>
        {{-- ================================================= --}}
{{-- TİCARİ --}}
{{-- ================================================= --}}

<div class="sidebar-section">

    <span>

        TİCARİ

    </span>


    <a href="#">

        <i class="bi bi-wallet2"></i>

        <label>

            Cari Hesap

        </label>

    </a>


    <a href="#">

        <i class="bi bi-file-earmark-text-fill"></i>

        <label>

            Teklifler

        </label>

    </a>


    <a href="#">

        <i class="bi bi-receipt-cutoff"></i>

        <label>

            Faturalar

        </label>

    </a>

</div>



{{-- ================================================= --}}
{{-- DEPO --}}
{{-- ================================================= --}}

<div class="sidebar-section">

    <span>

        DEPO

    </span>


    <a href="#">

        <i class="bi bi-box-seam-fill"></i>

        <label>

            Stok Yönetimi

        </label>

    </a>


    <a href="#">

        <i class="bi bi-upc-scan"></i>

        <label>

            Barkod Yönetimi

        </label>

    </a>

</div>



{{-- ================================================= --}}
{{-- YÖNETİM --}}
{{-- ================================================= --}}

<div class="sidebar-section">

    <span>

        YÖNETİM

    </span>


    <a href="#">

        <i class="bi bi-person-badge-fill"></i>

        <label>

            Personel

        </label>

    </a>


    <a href="#">

        <i class="bi bi-bar-chart-fill"></i>

        <label>

            Raporlar

        </label>

    </a>


    <a href="#">

        <i class="bi bi-gear-fill"></i>

        <label>

            Sistem Ayarları

        </label>

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

                    Gökhan İzgi

                </strong>

                <span>

                    <i class="bi bi-circle-fill"></i>

                    Çevrimiçi

                </span>

            </div>

        </div>



        <div class="sidebar-footer-menu">

            <a href="#">

                <i class="bi bi-person-gear"></i>

                <label>

                    Profilim

                </label>

            </a>


            <a href="#">

                <i class="bi bi-sliders"></i>

                <label>

                    Tercihler

                </label>

            </a>


            <a href="#">

                <i class="bi bi-box-arrow-right"></i>

                <label>

                    Güvenli Çıkış

                </label>

            </a>

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
        class="sidebar-close">


</button>
</aside>

