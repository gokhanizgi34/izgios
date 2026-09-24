@extends('layouts.app')


@section('title','Firma Yönetimi')


@section('content')


<div class="container">


    <div class="page-header">


        <div>

            <h1>
                🏢 Firma Yönetimi
            </h1>

            <p>
                Kayıtlı firma yönetimi
            </p>

        </div>


        <a href="{{ route('firma.create') }}" class="btn-new">
            + Yeni Firma
        </a>


    </div>




    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif





    <div class="firma-grid">



        @forelse($firmalar as $firma)



        <div class="firma-card">


            <div class="firma-header">


                <h2>

                    🏢 {{ $firma->unvan }}


                    @if($firma->merkez_goster)

                        <span>
                            (Merkez Şube)
                        </span>

                    @endif


                </h2>


            </div>




            <div class="firma-info">



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



                <div>

                    <span>
                        Şube Sayısı
                    </span>

                    <strong>
                        {{ $firma->subeler_count ?? 0 }}
                    </strong>

                </div>



                <div>

                    <span>
                        Personel Sayısı
                    </span>

                    <strong>
                        {{ $firma->personeller_count ?? 0 }}
                    </strong>

                </div>




                <div>

                    <span>
                        Durum
                    </span>


                    <strong>

                    @if($firma->aktif)

                        <span class="aktif">
                            Aktif
                        </span>

                    @else

                        <span class="pasif">
                            Pasif
                        </span>

                    @endif


                    </strong>


                </div>



            </div>






<div class="firma-actions">


<a href="{{ route('firma.show',$firma->id) }}"
class="btn-firma btn-detay">

<i class="bi bi-eye"></i>

Firma Kartı

</a>





<a href="{{ route('firma.edit',$firma->id) }}"
class="btn-firma btn-duzenle">

<i class="bi bi-pencil"></i>

Düzenle

</a>






<a href="{{ route('sube.index',$firma->id) }}"
class="btn-firma btn-sube">

<i class="bi bi-shop"></i>

Şubeler

</a>




</div>            



        </div>



        @empty


            <div class="empty-box">

                Henüz firma bulunmuyor.

            </div>


        @endforelse




    </div>



</div>



@endsection