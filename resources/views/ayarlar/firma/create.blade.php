@extends('layouts.app')


@section('title','Yeni Firma')



@section('content')

<style>
    .dashboard-box .merkez-gosterim-secimi{display:flex!important;align-items:center!important;position:relative!important;min-height:30px!important;gap:0!important}
    .dashboard-box .merkez-gosterim-secimi__checkbox{appearance:none!important;-webkit-appearance:none!important;display:block!important;position:relative!important;z-index:2!important;width:44px!important;min-width:44px!important;max-width:44px!important;height:24px!important;min-height:24px!important;max-height:24px!important;margin:0!important;padding:0!important;opacity:0!important;cursor:pointer!important}
    .dashboard-box .merkez-gosterim-secimi__track{display:block!important;position:absolute!important;left:0!important;top:3px!important;width:44px!important;height:24px!important;border-radius:999px!important;background:#cbd5e1!important;pointer-events:none!important}
    .dashboard-box .merkez-gosterim-secimi__track:after{content:""!important;position:absolute!important;left:3px!important;top:3px!important;width:18px!important;height:18px!important;border-radius:50%!important;background:#fff!important;box-shadow:0 1px 3px rgba(15,23,42,.25)!important;transition:.2s!important}
    .dashboard-box .merkez-gosterim-secimi__checkbox:checked + .merkez-gosterim-secimi__track{background:#2563eb!important}.dashboard-box .merkez-gosterim-secimi__checkbox:checked + .merkez-gosterim-secimi__track:after{transform:translateX(20px)!important}.dashboard-box .merkez-gosterim-secimi__track + span{margin-left:13px!important;line-height:1.5!important}
</style>


<div class="dashboard-box">





<div class="page-header">



<div>


<h2>

<i class="bi bi-building-add"></i>

Yeni Firma Oluştur

</h2>


<p>

Sisteme yeni firma kaydı ekleyin

</p>


</div>





</div>








<form method="POST"

action="{{ route('firma.store') }}">


@csrf







<div class="card">



<div class="card-title">


<i class="bi bi-building"></i>

Firma Bilgileri


</div>







<div class="grid">





<div class="form-group">


<label class="form-label-izgios">

Firma Ünvanı

</label>


<input

type="text"

name="unvan"

class="form-control-izgios"

value="{{ old('unvan') }}"

placeholder="Örn: Gökhan Otomotiv"

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

value="{{ old('vergi_no') }}"

placeholder="Vergi numarası">


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

placeholder="Telefon">


</div>







<div class="form-group">


<label class="form-label-izgios">

E-Posta

</label>


<input

type="email"

name="email"

class="form-control-izgios"

value="{{ old('email') }}"

placeholder="E-posta">


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

placeholder="Firma adresi">{{ old('adres') }}</textarea>


</div>







</div>









<div class="card">



<div class="card-title">


<i class="bi bi-shop"></i>

Merkez Gösterim Ayarı


</div>







<div class="form-group">



<label class="form-label-izgios">


Firma isminde merkez şube göster


</label>




<div class="merkez-gosterim-secimi">



<input

type="checkbox"

class="merkez-gosterim-secimi__checkbox"

name="merkez_goster"

value="1"

@if(old('merkez_goster'))

checked

@endif

>



<span class="merkez-gosterim-secimi__track" aria-hidden="true"></span>

<span>

İşaretlenirse:

<strong>

Firma Adı (Merkez Şube)

</strong>

olarak gösterilir.

</span>



</div>



</div>







</div>









<div class="actions">





<button

type="submit"

class="btn-save">


<i class="bi bi-save"></i>

Kaydet


</button>







<a href="{{ route('firma.index') }}"

class="btn-back">


<i class="bi bi-arrow-left"></i>

Firmalara Dön


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
