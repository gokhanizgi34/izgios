<header class="izgios-topbar">


    {{-- ================================================= --}}
    {{-- MOBİL MENÜ --}}
    {{-- ================================================= --}}


    <button class="mobile-menu-button">


        <i class="bi bi-list"></i>


    </button>





    {{-- ================================================= --}}
    {{-- İZGİOS LOGO --}}
    {{-- ================================================= --}}


    <div class="topbar-brand">


        <a href="{{ route('dashboard') }}">


            <div class="topbar-logo-text">


                <span class="top-logo-black">

                    İZGİ

                </span>


                <span class="top-logo-gold">

                    OS

                </span>


            </div>



            <div class="topbar-logo-subtitle">

                İzgi Oto Servis Yönetim Sistemi

            </div>


        </a>


    </div>







    {{-- ================================================= --}}
    {{-- ARAMA --}}
    {{-- ================================================= --}}


    <div class="topbar-search">


        <i class="bi bi-search"></i>



        <input type="text"
               placeholder="Müşteri, plaka, araç veya iş emri ara...">


    </div>
        {{-- ================================================= --}}
    {{-- SAĞ BİLGİ ALANI --}}
    {{-- ================================================= --}}


    <div class="topbar-actions">





        {{-- Bildirim --}}


        <button class="topbar-icon">


            <i class="bi bi-bell"></i>


            <span class="notification-count">

                0

            </span>


        </button>







        {{-- TARİH + SAAT --}}


        <div class="topbar-date-time">



            <div class="date-item">


                <i class="bi bi-calendar3"></i>


                <span>

                    {{ now()->format('d.m.Y') }}

                </span>


            </div>




            <div class="time-item">


                <i class="bi bi-clock"></i>


                <span id="izgios-clock">

                    {{ now()->format('H:i:s') }}

                </span>


            </div>



        </div>








        {{-- Kullanıcı --}}


        <div class="topbar-user">


            <div class="top-user-avatar">


                <i class="bi bi-person-circle"></i>


            </div>




            <div class="top-user-info">


                <strong>

                    Gökhan İzgi

                </strong>



                <span>

                    Yönetici

                </span>


            </div>


        </div>



    </div>
    </header>





{{-- ================================================= --}}
{{-- CANLI SAAT --}}
{{-- ================================================= --}}


<script>

document.addEventListener("DOMContentLoaded", function(){


    function updateIzgiosClock(){


        const clock = document.getElementById(
            "izgios-clock"
        );


        if(clock){


            const now = new Date();


            clock.innerHTML =
            now.toLocaleTimeString(
                "tr-TR",
                {
                    hour:"2-digit",
                    minute:"2-digit",
                    second:"2-digit"
                }
            );


        }


    }



    updateIzgiosClock();



    setInterval(
        updateIzgiosClock,
        1000
    );


});

</script>