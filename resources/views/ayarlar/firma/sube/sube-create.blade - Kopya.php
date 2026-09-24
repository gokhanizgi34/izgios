@extends('layouts.app')


@section('title','Yeni Şube')



@section('content')


<div class="dashboard-box">





<div class="page-header">



<div>


<h2>

<i class="bi bi-shop"></i>

Yeni Şube Oluştur

</h2>


<p>

{{ $firma->unvan }} firmasına yeni şube ekle

</p>


</div>





<div>


<a href="{{ route('firma.show',$firma->id) }}"
class="btn-back">


<i class="bi bi-arrow-left"></i>

Firma Kartı


</a>


</div>



</div>









<form method="POST"

action="{{ route('sube.store',$firma->id) }}">


@csrf







<div class="card">



<div class="card-title">


<i class="bi bi-shop-window"></i>

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

value="{{ old('sube_adi') }}"

placeholder="Örn: Kadosan Şube"

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

value="{{ old('telefon') }}"

placeholder="Şube telefonu">


</div>







</div>








<div class="form-group">


<label class="form-label-izgios">

Adres

</label>


<textarea

name="adres"

class="form-control-izgios"

rows="4"

placeholder="Şube adresi">{{ old('adres') }}</textarea>


</div>







</div>









<div class="actions">





<button

type="submit"

class="btn-save">


<i class="bi bi-save"></i>

Kaydet


</button>








<a href="{{ route('firma.show',$firma->id) }}"

class="btn-back">


<i class="bi bi-arrow-left"></i>

Firma Kartı


</a>








<a href="{{ route('ayarlar.index') }}"

class="btn-back">


<i class="bi bi-gear"></i>

Ayarlara Dön


</a>







</div>








</form>








</div>



@endsection