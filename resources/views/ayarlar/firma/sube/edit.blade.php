@extends('layouts.app')


@section('title','Şube Düzenle')



@section('content')

<style>
    .dashboard-box .sube-durum-card{display:flex!important;align-items:center!important;justify-content:space-between!important;gap:18px!important;padding:20px 24px!important;border-color:#fecaca!important}.dashboard-box .sube-durum-card .card-title{margin:0!important;padding:0!important;border:0!important}.dashboard-box .sube-durum-card form{margin:0!important;min-width:180px!important}.dashboard-box .sube-durum-button{width:100%!important;height:46px!important;padding:0 17px!important;border:0!important;border-radius:12px!important;background:#fff1f2!important;color:#be123c!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;font:800 14px/1 Poppins,sans-serif!important;cursor:pointer!important}.dashboard-box .sube-durum-button:hover{background:#ffe4e6!important}.dashboard-box .sube-durum-button--activate{background:#ecfdf5!important;color:#15803d!important}.dashboard-box .sube-durum-button--activate:hover{background:#dcfce7!important}@media(max-width:640px){.dashboard-box .sube-durum-card{align-items:stretch!important;flex-direction:column!important}.dashboard-box .sube-durum-card form{min-width:0!important;width:100%!important}}
</style>


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

Vergi No

</label>


<input

type="text"

name="vergi_no"

class="form-control-izgios"

value="{{ old('vergi_no',$sube->vergi_no) }}"

placeholder="Şubeye ait vergi numarası (varsa)">


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









<div class="card sube-durum-card"
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

class="sube-durum-button {{ $sube->aktif ? '' : 'sube-durum-button--activate' }}">


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
