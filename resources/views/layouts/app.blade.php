<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>
        @yield('title','İZGİOS')
    </title>


    {{-- ICON --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    {{-- FONT --}}
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">



    {{-- VITE CSS --}}
    @vite([
        'resources/css/app.css'
    ])

    @stack('styles')

    @if(auth()->user()?->mobilOturumKorunurMu())
    <script>
    (() => {
        const anahtar = @json('izgios-mobil-son-ekran-'.auth()->id());
        const ucSaat = 3 * 60 * 60 * 1000;
        const mevcut = window.location.pathname + window.location.search;
        const isEmriEkrani = /^\/servisler\/\d+\/islem(?:\?|$)/.test(mevcut);

        if (isEmriEkrani) {
            localStorage.setItem(anahtar, JSON.stringify({ adres: mevcut, zaman: Date.now() }));
        } else if (window.location.pathname === @json(route('dashboard', [], false))) {
            try {
                const son = JSON.parse(localStorage.getItem(anahtar) || 'null');
                if (son?.adres && Date.now() - Number(son.zaman) <= ucSaat && /^\/servisler\/\d+\/islem(?:\?|$)/.test(son.adres)) {
                    window.location.replace(son.adres);
                }
            } catch (_) {
                localStorage.removeItem(anahtar);
            }
        }
    })();
    </script>
    @endif



</head>



@php($aktifTema = auth()->check() ? \Illuminate\Support\Facades\DB::table('kullanici_tercihleri')->where('user_id', auth()->id())->value('tema') : 'acik')
<body class="izgios-body {{ $aktifTema === 'koyu' ? 'tema-koyu' : '' }} {{ auth()->user()?->isUsta() ? 'role-usta' : '' }}">

<style>
.kurumsal-kart,.vehicle-head,.firma-detail-card,.firma-detail__card,.firma-list-card,.sube-detail__card,.page-container>.card,.container>.card{border:1px solid #d9e3ef!important;border-radius:18px!important;background:#fff!important;box-shadow:0 10px 28px rgba(16,42,86,.06)!important;overflow:hidden}.kurumsal-kart__head,.vehicle-head,.firma-detail-header,.firma-detail__card h2,.card-title{padding:18px 22px!important;border-bottom:1px solid #e5ebf3!important;background:linear-gradient(135deg,#fbfdff,#f2f6fb)!important;color:#142b4e!important;font-weight:800}.kurumsal-kart__grid,.info-grid,.firma-detail-grid,.firma-detail__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0!important}.kurumsal-kart__grid>div,.info-grid>div,.firma-detail-grid>div,.firma-detail__grid>div{padding:15px 18px;border-right:1px solid #edf1f6;border-bottom:1px solid #edf1f6}.kurumsal-kart__grid span,.info-grid span,.firma-detail-grid span,.firma-detail__grid dt{display:block;color:#72839a!important;font-size:.72rem!important;font-weight:800!important;text-transform:uppercase;letter-spacing:.04em}.kurumsal-kart__grid strong,.info-grid strong,.firma-detail-grid strong,.firma-detail__grid dd{display:block;margin-top:5px;color:#152f54!important;font-size:.95rem}.tema-koyu .kurumsal-kart,.tema-koyu .vehicle-head,.tema-koyu .firma-detail-card,.tema-koyu .firma-detail__card,.tema-koyu .firma-list-card,.tema-koyu .sube-detail__card,.tema-koyu .page-container>.card,.tema-koyu .container>.card{background:#111c2e!important;border-color:#263652!important}.tema-koyu .kurumsal-kart__head,.tema-koyu .vehicle-head,.tema-koyu .firma-detail-header,.tema-koyu .firma-detail__card h2,.tema-koyu .card-title{background:#17233a!important;border-color:#31415b!important;color:#f8fafc!important}.tema-koyu .kurumsal-kart__grid>div,.tema-koyu .info-grid>div,.tema-koyu .firma-detail-grid>div,.tema-koyu .firma-detail__grid>div{border-color:#263652}.tema-koyu .kurumsal-kart__grid strong,.tema-koyu .info-grid strong,.tema-koyu .firma-detail-grid strong,.tema-koyu .firma-detail__grid dd{color:#eef4ff!important}@media(max-width:760px){.kurumsal-kart__grid,.info-grid,.firma-detail-grid,.firma-detail__grid{grid-template-columns:1fr 1fr}.kurumsal-kart__grid>div,.info-grid>div,.firma-detail-grid>div,.firma-detail__grid>div{border-right:0}}
.tema-koyu{background:#0b1220!important;color:#e5edf7}.tema-koyu .izgios-main,.tema-koyu .izgios-content{background:#0b1220!important}
.tema-koyu .card,.tema-koyu .firma-card,.tema-koyu .rbox,.tema-koyu .work-card,.tema-koyu .report-panel,.tema-koyu .module-band+*,.tema-koyu .dashboard-box,.tema-koyu .summary-card,.tema-koyu .warehouse .wh-card,.tema-koyu .warehouse .wh-form,.tema-koyu .warehouse .wh-list,.tema-koyu .barcode-tools,.tema-koyu .barcode-list,.tema-koyu .hr-stat,.tema-koyu .hr-panel,.tema-koyu .hr-table,.tema-koyu .report-card{background:#111c2e!important;color:#e5edf7!important;border-color:#263652!important}
.tema-koyu input,.tema-koyu select,.tema-koyu textarea{background:#0b1525!important;color:#e5edf7!important;border-color:#354563!important}.tema-koyu option{background:#0b1525;color:#e5edf7}
.tema-koyu .page-header h1,.tema-koyu h1,.tema-koyu h2,.tema-koyu h3,.tema-koyu h4,.tema-koyu strong,.tema-koyu .dashboard-box-header h3,.tema-koyu .box-header h3{color:#f8fafc!important}.tema-koyu .text-muted,.tema-koyu small,.tema-koyu p,.tema-koyu .dashboard-box-header span{color:#aebed2!important}
.tema-koyu .service-table,.tema-koyu .service-table thead tr,.tema-koyu .service-table tbody tr,.tema-koyu .table,.tema-koyu .table tr,.tema-koyu .table td,.tema-koyu .table th{background:#111c2e!important;color:#e5edf7!important;border-color:#263652!important}.tema-koyu .service-table tbody tr:hover,.tema-koyu .table-hover tbody tr:hover{background:#17263e!important}.tema-koyu .service-table th,.tema-koyu .table th{color:#aebed2!important}.tema-koyu .service-table td,.tema-koyu .table td{color:#e5edf7!important}
.tema-koyu .list-row,.tema-koyu hr{border-color:#263652!important}.tema-koyu .alert-info{background:#122a4d!important;color:#dbeafe!important;border-color:#2563eb!important}.tema-koyu .izgios-footer{background:#10192a!important;border-color:#263652!important}
</style>



<div class="izgios-layout">



    {{-- MOBİL OVERLAY --}}

    <div class="sidebar-overlay"
         id="sidebar-overlay">

    </div>





    {{-- SIDEBAR --}}

    @include('components.sidebar')







    {{-- ANA ALAN --}}

    <div class="izgios-main">





        {{-- TOPBAR --}}

        @include('components.topbar')







        {{-- İÇERİK --}}

        <main class="izgios-content">

            @include('components.module-band')

            @yield('content')

        </main>







        {{-- FOOTER --}}


        <footer class="izgios-footer">


            <div class="footer-left">


                <span>© {{ date('Y') }}</span>

                <span><strong>İZGİOS</strong> · İzgi Oto Servis Yönetim Sistemi</span>



            </div>







            <div class="footer-right">


                <span>

                    Versiyon

                </span>



                <strong>

                    1.0.0

                </strong>



            </div>



        </footer>

        @include('components.genel-asistan')






    </div>





</div>








{{-- MODAL --}}


<div id="izgios-modal"
     class="izgios-modal">


    <div class="izgios-modal-box">


        <button type="button"
                class="modal-close"
                id="modal-close">

        </button>




        <h3 id="modal-title"></h3>



        <div id="modal-body"></div>



    </div>



</div>










{{-- Sayfa yükleyicisi kaldırıldı; sürekli görünen işlevsiz animasyondu. --}}


<div id="izgios-loader" hidden>


    <div class="loader-circle"></div>


</div>









{{-- TOAST --}}


<div id="izgios-toast-container"></div>









{{-- JAVASCRIPT --}}


@vite([
    'resources/js/app.js'
])





</body>


</html>
