@extends('layouts.app')


@section('title','İZGİOS')


@section('content')


<div class="izgios-home">



    {{-- ================================================= --}}
    {{-- HOŞ GELDİNİZ --}}
    {{-- ================================================= --}}


    <section class="welcome-panel">


        <div class="welcome-content">


            <h1>

                İZGİOS'a Hoş Geldiniz

            </h1>


            <p>

                İzgi Oto Servis Yönetim Sistemi ile servis süreçlerinizi kolayca yönetin.

            </p>


        </div>



    </section>





    {{-- ================================================= --}}
    {{-- ÖZET KARTLAR --}}
    {{-- ================================================= --}}


    <section class="summary-grid">


        <div class="summary-card">


            <div class="summary-icon blue">

                <i class="bi bi-car-front-fill"></i>

            </div>


            <div>

                <span>

                    Servisteki Araç

                </span>


                <strong>

                    0

                </strong>


            </div>


        </div>





        <div class="summary-card">


            <div class="summary-icon green">

                <i class="bi bi-check-circle-fill"></i>

            </div>


            <div>

                <span>

                    Tamamlanan İş

                </span>


                <strong>

                    0

                </strong>


            </div>


        </div>





        <div class="summary-card">


            <div class="summary-icon yellow">

                <i class="bi bi-hourglass-split"></i>

            </div>


            <div>

                <span>

                    Bekleyen İş

                </span>


                <strong>

                    0

                </strong>


            </div>


        </div>





        <div class="summary-card">


            <div class="summary-icon red">

                <i class="bi bi-wallet-fill"></i>

            </div>


            <div>

                <span>

                    Bekleyen Tahsilat

                </span>


                <strong>

                    ₺0

                </strong>


            </div>


        </div>



    </section>







    {{-- ================================================= --}}
    {{-- SERVİS DURUMU + HIZLI İŞLEMLER --}}
    {{-- ================================================= --}}


    <section class="dashboard-columns">



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

                        <i class="bi bi-check-lg"></i>

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







        <div class="dashboard-box">


            <div class="box-header">


                <h3>

                    <i class="bi bi-lightning-charge-fill"></i>

                    Hızlı İşlemler

                </h3>


            </div>




            <div class="quick-grid">



                <a href="#" class="quick-card">


                    <i class="bi bi-person-plus-fill"></i>


                    <span>

                        Yeni Müşteri

                    </span>


                </a>





                <a href="#" class="quick-card">


                    <i class="bi bi-car-front-fill"></i>


                    <span>

                        Araç Ekle

                    </span>


                </a>





                <a href="#" class="quick-card">


                    <i class="bi bi-clipboard-plus-fill"></i>


                    <span>

                        Servis Kabul

                    </span>


                </a>





                <a href="#" class="quick-card">


                    <i class="bi bi-file-earmark-text-fill"></i>


                    <span>

                        İş Emri

                    </span>


                </a>



            </div>


        </div>



    </section>








    {{-- ================================================= --}}
    {{-- SON İŞLEMLER --}}
    {{-- ================================================= --}}


    <section class="dashboard-columns">



        <div class="dashboard-box">


            <div class="box-header">


                <h3>

                    <i class="bi bi-clock-history"></i>

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







        <div class="dashboard-box">


            <div class="box-header">


                <h3>

                    <i class="bi bi-calendar-check-fill"></i>

                    Yaklaşan Bakımlar

                </h3>


            </div>



            <div class="maintenance-list">


                <div class="empty-state">


                    <i class="bi bi-car-front"></i>


                    <p>

                        Yaklaşan bakım bulunmuyor.

                    </p>


                </div>


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

                        Sunucu

                    </span>


                    <strong>

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



</div>


@endsection