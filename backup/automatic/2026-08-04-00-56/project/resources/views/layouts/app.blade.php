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



<body class="izgios-body">



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
