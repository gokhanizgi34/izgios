@extends('layouts.app')


@section('title','İZGİOS Yönetim Paneli')


@section('content')


<div class="izgios-home">



{{-- ================================================= --}}
{{-- ADMIN HOŞ GELDİNİZ --}}
{{-- ================================================= --}}


<section class="welcome-panel">


    <div class="welcome-content">


        <h1>

            İZGİOS Yönetim Paneli

        </h1>


        <p>

            Sistem yöneticisi olarak tüm servis, kullanıcı ve sistem işlemlerini buradan yönetebilirsiniz.

        </p>


    </div>



    <div class="welcome-info">


        <div class="welcome-info-box">


            <span>

                Kullanıcı

            </span>


            <strong>

                {{ auth()->user()->name ?? 'Yönetici' }}

            </strong>


        </div>




        <div class="welcome-info-box">


            <span>

                Yetki

            </span>


            <strong>

                Sistem Yöneticisi

            </strong>


        </div>


    </div>


</section>






{{-- ================================================= --}}
{{-- ADMIN ÖZET KARTLARI --}}
{{-- ================================================= --}}



<section class="summary-grid">



    <div class="summary-card users">


        <div class="summary-icon">

            <i class="bi bi-people-fill"></i>

        </div>


        <span>

            Kullanıcılar

        </span>


        <strong>

            2

        </strong>


        <small>

            Aktif kullanıcı

        </small>


    </div>





    <div class="summary-card vehicle">


        <div class="summary-icon">

            <i class="bi bi-car-front-fill"></i>

        </div>


        <span>

            Araçlar

        </span>


        <strong>

            0

        </strong>


        <small>

            Kayıtlı araç

        </small>


    </div>





    <div class="summary-card service">


        <div class="summary-icon">

            <i class="bi bi-tools"></i>

        </div>


        <span>

            Servis Yönetimi

        </span>


        <strong>

            0

        </strong>


        <small>

            Açık iş emri

        </small>


    </div>





    <div class="summary-card finance">


        <div class="summary-icon">

            <i class="bi bi-cpu-fill"></i>

        </div>


        <span>

            Sistem Durumu

        </span>


        <strong>

            Aktif

        </strong>


        <small>

            Sistem çalışıyor

        </small>


    </div>



</section>
{{-- ================================================= --}}
{{-- SERVİS DURUMU + ADMİN İŞLEMLERİ --}}
{{-- ================================================= --}}


<section class="dashboard-columns">



    {{-- SERVİS DURUMU --}}


    <div class="dashboard-box">


        <div class="box-header">


            <h3>

                <i class="bi bi-clipboard-data"></i>

                Servis Durumu

            </h3>


        </div>




        <div class="service-status-list">



            <div class="status-item">


                <div class="status-icon blue">


                    <i class="bi bi-car-front"></i>


                </div>



                <div>


                    <strong>

                        Kabul Bekleyen

                    </strong>


                    <span>

                        0 Araç

                    </span>


                </div>



            </div>






            <div class="status-item">


                <div class="status-icon yellow">


                    <i class="bi bi-tools"></i>


                </div>



                <div>


                    <strong>

                        İşlemde

                    </strong>


                    <span>

                        0 Araç

                    </span>


                </div>



            </div>







            <div class="status-item">


                <div class="status-icon green">


                    <i class="bi bi-check-circle"></i>


                </div>



                <div>


                    <strong>

                        Teslime Hazır

                    </strong>


                    <span>

                        0 Araç

                    </span>


                </div>



            </div>



        </div>


    </div>








    {{-- ADMİN HIZLI İŞLEMLER --}}



    <div class="dashboard-box">


        <div class="box-header">


            <h3>


                <i class="bi bi-lightning-charge-fill"></i>


                Yönetim İşlemleri


            </h3>


        </div>





        <div class="quick-grid">





            <a href="#" class="quick-card">


                <i class="bi bi-person-plus-fill"></i>


                <span>

                    Kullanıcı Ekle

                </span>


            </a>







            <a href="#" class="quick-card">


                <i class="bi bi-person-gear"></i>


                <span>

                    Rol Yönetimi

                </span>


            </a>








            <a href="#" class="quick-card">


                <i class="bi bi-sliders"></i>


                <span>

                    Sistem Ayarları

                </span>


            </a>








            <a href="#" class="quick-card">


                <i class="bi bi-bar-chart-fill"></i>


                <span>

                    Raporlar

                </span>


            </a>





        </div>


    </div>




</section>
{{-- ================================================= --}}
{{-- AKTİVİTELER + SERVİS KAYITLARI --}}
{{-- ================================================= --}}


<section class="dashboard-columns">





{{-- SON SİSTEM AKTİVİTELERİ --}}



<div class="dashboard-box">


    <div class="box-header">


        <h3>


            <i class="bi bi-clock-history"></i>


            Son Sistem Aktiviteleri


        </h3>


    </div>





    <div class="activity-list">





        <div class="activity-item">


            <div class="activity-user">


                <div class="activity-avatar">


                    Gİ


                </div>



                <div class="activity-info">


                    <strong>

                        Gökhan İzgi

                    </strong>


                    <span>

                        Sisteme giriş yaptı

                    </span>


                </div>



            </div>



            <div class="activity-time">

                Şimdi

            </div>



        </div>








        <div class="activity-item">


            <div class="activity-user">


                <div class="activity-avatar">


                    AD

                </div>



                <div class="activity-info">


                    <strong>

                        Admin

                    </strong>


                    <span>

                        Sistem yönetimi aktif edildi

                    </span>


                </div>



            </div>



            <div class="activity-time">

                Bugün

            </div>



        </div>






        <div class="activity-item">


            <div class="activity-user">


                <div class="activity-avatar">


                    US

                </div>



                <div class="activity-info">


                    <strong>

                        Kullanıcı

                    </strong>


                    <span>

                        Bekleyen işlem yok

                    </span>


                </div>



            </div>



            <div class="activity-time">

                -

            </div>



        </div>





    </div>


</div>









{{-- SON SERVİS KAYITLARI --}}



<div class="dashboard-box">


    <div class="box-header">


        <h3>


            <i class="bi bi-wrench-adjustable"></i>


            Son Servis Kayıtları


        </h3>


    </div>





    <div class="table-responsive">


        <table class="service-table">



            <thead>


                <tr>


                    <th>

                        Plaka

                    </th>



                    <th>

                        Müşteri

                    </th>



                    <th>

                        Durum

                    </th>



                </tr>


            </thead>





            <tbody>



                <tr>


                    <td colspan="3"
                        class="empty-row">


                        Henüz servis kaydı bulunmuyor.


                    </td>


                </tr>



            </tbody>



        </table>


    </div>



</div>






</section>
{{-- ================================================= --}}
{{-- SİSTEM BİLGİLERİ --}}
{{-- ================================================= --}}



<section class="system-panel">



    <div class="dashboard-box">



        <div class="box-header">


            <h3>

                <i class="bi bi-cpu-fill"></i>

                Sistem Bilgileri

            </h3>


        </div>





        <div class="system-grid">





            <div class="system-card">


                <span>

                    Uygulama

                </span>


                <strong>

                    İZGİOS

                </strong>


            </div>







            <div class="system-card">


                <span>

                    Versiyon

                </span>


                <strong>

                    v1.0.0

                </strong>


            </div>








            <div class="system-card">


                <span>

                    Sunucu Durumu

                </span>


                <strong class="status-active">

                    Aktif

                </strong>


            </div>








            <div class="system-card">


                <span>

                    PHP

                </span>


                <strong>

                    {{ PHP_VERSION }}

                </strong>


            </div>







        </div>



    </div>




</section>







{{-- ================================================= --}}
{{-- LİSANS BİLGİSİ --}}
{{-- ================================================= --}}



<section class="dashboard-box license-box">



    <div class="box-header">


        <h3>


            <i class="bi bi-shield-check"></i>


            Lisans Bilgileri


        </h3>



    </div>






    <div class="license-content">



        <div>


            <span>

                Paket

            </span>


            <strong>

                İZGİOS Profesyonel

            </strong>


        </div>





        <div>


            <span>

                Durum

            </span>


            <strong class="status-active">

                Aktif

            </strong>


        </div>





        <div>


            <span>

                Kullanıcı Limiti

            </span>


            <strong>

                Sınırsız

            </strong>


        </div>



    </div>



</section>







</div>


@endsection