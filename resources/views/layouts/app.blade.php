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



</head>



@php($aktifTema = auth()->check() ? \Illuminate\Support\Facades\DB::table('kullanici_tercihleri')->where('user_id', auth()->id())->value('tema') : 'acik')
<body class="izgios-body {{ $aktifTema === 'koyu' ? 'tema-koyu' : '' }} {{ auth()->user()?->isUsta() ? 'role-usta' : '' }}">

<style>
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





        {{-- MOBİL MENU --}}

        <button 
            id="mobile-menu-btn"
            class="mobile-menu-btn">

            <i class="bi bi-list"></i>

        </button>







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


                <span>

                    © {{ date('Y') }}

                </span>



                <strong>

                    İZGİOS

                </strong>



                <span>

                    | İzgi Oto Servis Yönetim Sistemi

                </span>



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










{{-- LOADER --}}


<div id="izgios-loader">


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
