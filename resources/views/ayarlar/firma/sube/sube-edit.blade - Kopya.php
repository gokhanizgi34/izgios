@extends('layouts.app')


@section('title','Şube Düzenle')



@section('content')


<div class="dashboard-box">





<div class="page-header">


<div>


<h2>

<i class="bi bi-pencil-square"></i>

Şube Düzenle

</h2>


<p>

{{ $sube->sube_adi }} bilgilerini güncelleyin

</p>


</div>





<div>


<a href="{{ route('sube.show',
[
'firma'=>$firma->id,
'sube'=>$sube->id
]) }}"
class="btn-back">


<i class="bi bi-arrow-left"></i>

Şube Detayı


</a>


</div>


</div>










<form method="POST"

action="{{ route('sube.update',
[
'firma'=>$firma->id,
'sube'=>$sube->id
]) }}">


@csrf

@method('PUT')








<div class="card">



<div class="card-title">


<i class="bi bi-shop"></i>

Şube Bilgileri


</div>







<div class="grid">





<div class="form-group">


<label class="form-label-izgios">

Bağlı Firma

</label>


<input

type="text"

class="form-control-izgios"

value="{{ $firma->unvan }}"

disabled>


</div>







<div class="form-group">


<label class="form-label-izgios">

Şube Adı

</label>


<input

type="text"

name="sube_adi"

class="form-control-izgios"

value="{{ old('sube_adi',$sube->sube_adi) }}"

required>


</div>








<div class="form-group">


<label class="form-label-izgios">

Telefon

</label>


<input

type="text"

name="telefon"

class="form-control-izgios"

value="{{ old('telefon',$sube->telefon) }}">


</div>







</div>









<div class="form-group">


<label class="form-label-izgios">

Adres

</label>


<textarea

name="adres"

class="form-control-izgios"

rows="4">{{ old('adres',$sube->adres) }}</textarea>


</div>







</div>









<div class="actions">



<button

type="submit"

class="btn-save">


<i class="bi bi-save"></i>

Güncelle


</button>






<a href="{{ route('firma.show',$firma->id) }}"

class="btn-back">


<i class="bi bi-building"></i>

Firma Kartı


</a>








<a href="{{ route('ayarlar.index') }}"

class="btn-back">


<i class="bi bi-gear"></i>

Ayarlara Dön


</a>




</div>






</form>









<div class="card"
style="margin-top:25px;">



<div class="card-title">


<i class="bi bi-toggle-on"></i>

Şube Durumu


</div>







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

class="btn-danger">


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


@endsection