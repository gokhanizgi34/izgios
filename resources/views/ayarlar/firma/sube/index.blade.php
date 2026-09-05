@extends('layouts.app')


@section('title','Şube Yönetimi')


@section('content')


<div class="dashboard-box">





{{-- HEADER --}}


<div class="page-header">



<div>


<h2>

<i class="bi bi-shop"></i>

{{ $firma->unvan }} - Şubeler

</h2>


<p>

Firma şube yönetimi

</p>


</div>





<div class="header-actions">



<a href="{{ route('firma.show',$firma->id) }}"
class="btn-card btn-detail">


<i class="bi bi-arrow-left"></i>

Firma Kartı


</a>





<a href="{{ route('ayarlar.index') }}"
class="btn-card btn-detail">


<i class="bi bi-gear"></i>

Ayarlara Dön


</a>



</div>



</div>









{{-- YENİ ŞUBE --}}


<div style="margin-bottom:25px;">


<a href="{{ route('sube.create',$firma->id) }}"
class="btn-primary-custom">


<i class="bi bi-plus-circle"></i>

Yeni Şube


</a>


</div>









{{-- ŞUBE KARTLARI --}}



<div class="card-grid">






@foreach($subeler as $sube)






<div class="service-card">







{{-- BAŞLIK --}}


<div class="card-title">



<i class="bi bi-shop"></i>



{{ $sube->sube_adi }}



</div>









<div class="card-info">






<div>


<span>

Telefon

</span>


<strong>

{{ $sube->telefon ?? '-' }}

</strong>


</div>








<div>


<span>

Adres

</span>


<strong>

{{ $sube->adres ?? '-' }}

</strong>


</div>








<div>


<span>

Personel

</span>


<strong>

{{ $sube->personeller_count ?? 0 }}

Kişi

</strong>


</div>








<div>


<span>

Durum

</span>



@if($sube->aktif)



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






<a href="{{ route('sube.edit',[
$firma->id,
$sube->id
]) }}"
class="btn-card btn-edit">


<i class="bi bi-pencil"></i>

Düzenle


</a>








<a href="{{ route('sube.show',[
$firma->id,
$sube->id
]) }}"
class="btn-card btn-detail">


<i class="bi bi-eye"></i>

Detay


</a>









<form method="POST"
action="{{ route('sube.durum',[
$firma->id,
$sube->id
]) }}">



@csrf

@method('PATCH')





<button type="submit"
class="btn-card btn-danger">





@if($sube->aktif)



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