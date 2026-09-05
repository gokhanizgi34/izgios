<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>
        İZGİOS | Giriş
    </title>


    <!-- Google Font -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">


    <!-- Vite -->

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])


</head>


<body class="login-page">


<div class="login-wrapper">



    <!-- LOGO -->

    <div class="login-logo">


        <h1>

            <span class="logo-black">
                İZGİ
            </span>


            <span class="logo-gold">
                OS
            </span>


        </h1>



        <h2>
            İZGİ OTO SERVİS
        </h2>



        <p>
            Araç Servis ve Bakım Yönetim Sistemi
        </p>


    </div>





    <!-- LOGIN CARD -->

    <div class="login-card">


        <h3>
            Hoş Geldiniz
        </h3>



        <span>
            Devam etmek için giriş yapınız.
        </span>





        {{-- HATA MESAJI --}}


        @if($errors->any())

            <div class="login-error">

                {{ $errors->first() }}

            </div>


        @endif






        <form action="{{ route('login.post') }}"
              method="POST">


            @csrf





            <!-- KULLANICI -->

            <div class="form-group">


                <label>

                    Kullanıcı Adı veya E-Mail

                </label>



                <div class="input-box">


                    <span class="icon">

                        👤

                    </span>




                    <input

                        type="text"

                        name="username"

                        value="{{ old('username') }}"

                        placeholder="admin@izgios.com"

                        autocomplete="username"

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
                        <!-- LOGIN OPTIONS -->


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







            <!-- LOGIN BUTTON -->


            <button

                type="submit"

                class="login-button"

            >

                GİRİŞ YAP


            </button>




        </form>





        <!-- VERSION -->


        <div class="version">


            İZGİOS v1.0.0


        </div>




    </div>



</div>





<script>


document.addEventListener(
    "DOMContentLoaded",
    function(){



        const password =
            document.getElementById(
                "password"
            );



        const toggle =
            document.getElementById(
                "togglePassword"
            );





        if(toggle){


            toggle.addEventListener(
                "click",
                function(){


                    if(password.type === "password"){


                        password.type = "text";


                        toggle.innerHTML = "🙈";


                    }

                    else {


                        password.type = "password";


                        toggle.innerHTML = "👁";


                    }



                }
            );


        }



    }
);


</script>



</body>


</html>