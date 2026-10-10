@extends('layouts.app')


@section('title','Firma Yönetimi')


@section('content')


<div class="dashboard-box">


    {{-- HEADER --}}
    <div class="page-header">


        <div>

            <h2>
                <i class="bi bi-building"></i>
                Firma Yönetimi
            </h2>

            <p>
                Kayıtlı firma yönetimi
            </p>

        </div>



        <a href="{{ route('firma.create') }}"
           class="btn-primary-custom">

            <i class="bi bi-plus-circle"></i>

            Yeni Firma

        </a>


    </div>





    {{-- FİRMA KARTLARI --}}


    <div class="card-grid">



        @foreach($firmalar as $firma)



        <div class="service-card">



            {{-- BAŞLIK --}}

            <div class="card-title">


                <i class="bi bi-building"></i>


                {{ $firma->unvan }}


            </div>





            <div class="card-info">



                <div>

                    <span>
                        Vergi No
                    </span>

                    <strong>
                        {{ $firma->vergi_no ?? '-' }}
                    </strong>

                </div>





                <div>

                    <span>
                        Telefon
                    </span>


                    <strong>
                        {{ $firma->telefon ?? '-' }}
                    </strong>

                </div>





                <div class="info-row">


                    <span>
                        Şube Sayısı
                    </span>


                    <strong>
                        
                        {{ $firma->subeler_count ?? 0 }}

                    </strong>


                </div>





                <div class="info-row">


                    <span>
                        Personel Sayısı
                    </span>


                    <strong>

                        {{ $firma->personeller_count ?? 0 }}

                    </strong>


                </div>





                <div class="info-row">


                    <span>
                        Durum
                    </span>



                    @if($firma->aktif)


                        <strong class="status-active">

                            Aktif

                        </strong>


                    @else


                        <strong class="status-passive">

                            Pasif

                        </strong>


                    @endif



                </div>



            </div>






            {{-- BUTONLAR --}}


            <div class="card-buttons">



                <a href="{{ route('firma.show',$firma->id) }}"
                   class="btn-card btn-detail">


                    <i class="bi bi-eye"></i>

                    Firma Kartı


                </a>






                <a href="{{ route('firma.edit',$firma->id) }}"
                   class="btn-card btn-edit">


                    <i class="bi bi-pencil"></i>

                    Düzenle


                </a>







                <form method="POST"
                      action="{{ route('firma.durum',$firma->id) }}">


                    @csrf

                    @method('PATCH')



                    <button type="submit"
                            class="btn-card btn-danger">



                        @if($firma->aktif)

                            <i class="bi bi-pause-circle"></i>

                            Pasif Yap


                        @else


                            <i class="bi bi-play-circle"></i>

                            Aktif Yap


                        @endif



                    </button>



                </form>




            </div>





        </div>



        @endforeach



    </div>




</div>



@endsection