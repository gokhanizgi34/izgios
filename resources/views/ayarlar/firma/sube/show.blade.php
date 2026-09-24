@extends('layouts.app')


@section('title','Şube Detayı')



@section('content')



<div class="dashboard-box">





<div class="box-header">


<h3>

<i class="bi bi-shop"></i>

{{ $sube->sube_adi }}

</h3>




<div class="page-actions">



<a href="{{ route('firma.show',$firma->id) }}"

class="btn-secondary">


<i class="bi bi-arrow-left"></i>

Firma Kartı


</a>





<a href="{{ route('ayarlar.index') }}"

class="btn-secondary">


<i class="bi bi-gear"></i>

Ayarlara Dön


</a>



</div>



</div>









<div class="info-section">



<h4>

<i class="bi bi-info-circle"></i>

Şube Bilgileri

</h4>







<p>

<strong>Bağlı Firma:</strong>

{{ $firma->unvan }}

</p>





<p>

<strong>Şube Adı:</strong>

{{ $sube->sube_adi }}

</p>





<p>

<strong>Telefon:</strong>

{{ $sube->telefon ?? '-' }}

</p>






<p>

<strong>Adres:</strong>

{{ $sube->adres ?? '-' }}

</p>







<p>

<strong>Personel:</strong>

{{ $sube->personeller()->count() }} kişi

</p>








<p>

<strong>Durum:</strong>


@if($sube->aktif)


<span class="status-active">

Aktif

</span>


@else


<span class="status-passive">

Pasif

</span>


@endif


</p>





</div>









<div class="button-group">





<a href="{{ route('sube.edit',
[
'firma'=>$firma->id,
'sube'=>$sube->id
]) }}"

class="btn-primary">


<i class="bi bi-pencil"></i>

Düzenle


</a>







<form method="POST"

action="{{ route('sube.durum',
[
'firma'=>$firma->id,
'sube'=>$sube->id
]) }}">


@csrf

@method('PATCH')




<button

type="submit"

class="btn-warning">


@if($sube->aktif)

Pasif Yap

@else

Aktif Yap

@endif



</button>



</form>






</div>








<hr>







<div class="page-actions">





<a href="{{ route('firma.show',$firma->id) }}"

class="btn-secondary">


<i class="bi bi-building"></i>

Firma Kartına Dön


</a>






<a href="{{ route('ayarlar.index') }}"

class="btn-secondary">


<i class="bi bi-gear"></i>

Ayarlara Dön


</a>




</div>






</div>



@endsection