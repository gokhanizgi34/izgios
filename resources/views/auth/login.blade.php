<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>İZGİOS | Giriş</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css','resources/js/app.js'])


</head>



<body class="login-page">


<div class="login-wrapper">



    <!-- LOGO -->

    <div class="login-logo">


        <h1>

            <span class="logo-black">izgi</span><span class="logo-gold">OS</span>


        </h1>


        <h2>

            İZGİ OTO SERVİS

        </h2>


        <p>

            Araç Servis ve Bakım Yönetim Sistemi

        </p>


    </div>





    <!-- LOGIN KART -->


    <div class="login-card">


        <h3>

            Hoş Geldiniz

        </h3>



        <span>

            Devam etmek için giriş yapınız.

        </span>





        @if ($errors->any())

            <div style="
                background:#fee2e2;
                color:#991b1b;
                padding:12px;
                border-radius:10px;
                margin-bottom:20px;
                font-size:14px;
            ">


                @foreach ($errors->all() as $error)

                    <div>

                        {{ $error }}

                    </div>


                @endforeach


            </div>


        @endif






        <form method="POST" action="{{ route('login.post') }}">


            @csrf



            <!-- EMAIL -->

            <div class="form-group">


                <label>

                    Kullanıcı Adı veya E-Posta

                </label>



                <div class="input-box">


                    <span class="icon">

                        👤

                    </span>



                    <input

                        type="text"

                        name="login"

                        value="{{ old('login') }}"

                        placeholder="admin@izgios.com"

                        autocomplete="off"

                        autocapitalize="none"

                        spellcheck="false"

                        required

                    >



                </div>


            </div>





            <!-- ŞİFRE -->


            <div class="form-group">


                <label>

                    Şifre

                </label>



                <div class="input-box">


                    <span class="icon">

                        🔒

                    </span>



                    <input

                        id="password"

                        type="password"

                        name="password"

                        placeholder="Şifre"

                        autocomplete="current-password"

                        required

                    >



                    <button

                        type="button"

                        class="show-password"

                        id="togglePassword"

                    >

                        👁


                    </button>


                </div>


            </div>
                        <!-- SEÇENEKLER -->


            <div class="login-options">


                <label class="remember">


                    <input

                        type="checkbox"

                        name="remember"

                    >


                    <span>

                        Beni Hatırla

                    </span>


                </label>





                <a

                    href="#"

                    class="forgot-password"

                >

                    Şifremi Unuttum?


                </a>


            </div>






            <!-- GİRİŞ BUTONU -->


            <button

                type="submit"

                class="login-button"

            >

                GİRİŞ YAP


            </button>



        </form>






        <div class="version">


            İZGİOS v1.0


        </div>




    </div>




</div>








<script>


const togglePassword = document.getElementById("togglePassword");


const password = document.getElementById("password");





togglePassword.addEventListener("click", function(){



    if(password.type === "password"){



        password.type = "text";


        this.innerHTML = "🙈";



    } else {



        password.type = "password";


        this.innerHTML = "👁";



    }



});



</script>




</body>

</html>