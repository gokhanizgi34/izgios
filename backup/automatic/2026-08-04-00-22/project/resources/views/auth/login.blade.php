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

<body>

<div class="login-wrapper">

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

    <div class="login-card">

        <h3>

            Hoş Geldiniz

        </h3>

        <span>

            Devam etmek için giriş yapınız.

        </span>

        <form action="javascript:void(0);" method="POST">

            @csrf

            <div class="form-group">

                <label>

                    Kullanıcı Adı

                </label>

                <div class="input-box">

                    <span class="icon">

                        👤

                    </span>

                    <input
                        type="text"
                        name="username"
                        placeholder="Kullanıcı Adı"
                        autocomplete="username">

                </div>

            </div>

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
                        autocomplete="current-password">
                                            <button
                        class="show-password"
                        type="button"
                        id="togglePassword">

                        👁

                    </button>

                </div>

            </div>

            <div class="login-options">

                <label class="remember">

                    <input
                        type="checkbox"
                        name="remember">

                    <span>

                        Beni Hatırla

                    </span>

                </label>

                <a
                    href="#"
                    class="forgot-password">

                    Şifremi Unuttum?

                </a>

            </div>

            <button
                type="submit"
                class="login-button">

                GİRİŞ YAP

            </button>

        </form>

        <div class="version">

            İZGİOS v1.0

        </div>

    </div>

</div>

<script>

const togglePassword=document.getElementById("togglePassword");

const password=document.getElementById("password");

togglePassword.addEventListener("click",function(){

    if(password.type==="password"){

        password.type="text";

        this.innerHTML="🙈";

    }else{

        password.type="password";

        this.innerHTML="👁";

    }

});

</script>

</body>

</html>
