@extends('layouts.app')


@section('title','Firma Düzenle')



@section('content')

<style>
    .dashboard-box .merkez-gosterim-secimi{display:flex!important;align-items:center!important;position:relative!important;min-height:30px!important;gap:0!important}
    .dashboard-box .merkez-gosterim-secimi__checkbox{appearance:none!important;-webkit-appearance:none!important;display:block!important;position:relative!important;z-index:2!important;width:44px!important;min-width:44px!important;max-width:44px!important;height:24px!important;min-height:24px!important;max-height:24px!important;margin:0!important;padding:0!important;opacity:0!important;cursor:pointer!important}
    .dashboard-box .merkez-gosterim-secimi__track{display:block!important;position:absolute!important;left:0!important;top:3px!important;width:44px!important;height:24px!important;border-radius:999px!important;background:#cbd5e1!important;pointer-events:none!important}
    .dashboard-box .merkez-gosterim-secimi__track:after{content:""!important;position:absolute!important;left:3px!important;top:3px!important;width:18px!important;height:18px!important;border-radius:50%!important;background:#fff!important;box-shadow:0 1px 3px rgba(15,23,42,.25)!important;transition:.2s!important}
    .dashboard-box .merkez-gosterim-secimi__checkbox:checked + .merkez-gosterim-secimi__track{background:#2563eb!important}.dashboard-box .merkez-gosterim-secimi__checkbox:checked + .merkez-gosterim-secimi__track:after{transform:translateX(20px)!important}.dashboard-box .merkez-gosterim-secimi__track + span{margin-left:13px!important;line-height:1.5!important}
    .dashboard-box .actions .firma-durum-form{flex:1!important;height:48px!important;margin:0!important;display:block!important}.dashboard-box .actions .firma-durum-button{width:100%!important;height:48px!important;margin:0!important;padding:0 16px!important;border:0!important;border-radius:12px!important;background:#fff1f2!important;color:#be123c!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;font:800 14px/1 Poppins,sans-serif!important;cursor:pointer!important}.dashboard-box .actions .firma-durum-button:hover{background:#ffe4e6!important}.dashboard-box .actions .firma-durum-button--activate{background:#ecfdf5!important;color:#15803d!important}.dashboard-box .actions .firma-durum-button--activate:hover{background:#dcfce7!important}
</style>


<div class="dashboard-box">






<div class="page-header">



<div>


<h2>

<i class="bi bi-pencil-square"></i>

Firma Düzenle

</h2>


<p>

{{ $firma->unvan }} bilgilerini güncelleyin

</p>


</div>






</div>









<form method="POST" enctype="multipart/form-data"

id="firma-guncelle-form"

action="{{ route('firma.update',$firma->id) }}">


@csrf

@method('PUT')









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

value="{{ old('unvan',$firma->unvan) }}"

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

value="{{ old('vergi_no',$firma->vergi_no) }}">


</div>








<div class="form-group">


<label class="form-label-izgios">

Telefon

</label>


<input

type="text"

name="telefon"

class="form-control-izgios"

value="{{ old('telefon',$firma->telefon) }}">


</div>








<div class="form-group">


<label class="form-label-izgios">

E-Posta

</label>


<input

type="email"

name="email"

class="form-control-izgios"

value="{{ old('email',$firma->email) }}">


</div>



<div class="form-group">



<label class="form-label-izgios">

Google yorum bağlantısı

</label>



<input

type="url"

name="google_yorum_linki"

class="form-control-izgios"

value="{{ old('google_yorum_linki',$firma->google_yorum_linki) }}"

placeholder="https://g.page/r/.../review">

<small class="text-muted">Araç teslim teşekkür mesajlarında müşteriye gönderilir.</small>



</div>



<div class="form-group">

<label class="form-label-izgios">Firma logosu</label>

@if ($firma->logo_yolu)
    <div class="mb-2"><img src="{{ asset('storage/'.$firma->logo_yolu) }}" alt="{{ $firma->unvan }} logosu" style="max-width:220px;max-height:88px;object-fit:contain;border:1px solid #d7e2ef;border-radius:10px;padding:8px;background:#fff"></div>
@endif

<input type="file" name="logo" class="form-control-izgios" accept="image/png,image/jpeg,image/webp">

<small class="text-muted">PNG, JPG veya WebP · en fazla 2 MB · önerilen ölçü 600 × 240 px (yatay, şeffaf PNG tercih edilir).</small>

</div>






</div>








<div class="form-group">


<label class="form-label-izgios">

Adres

</label>


<textarea

name="adres"

class="form-control-izgios"

rows="4">{{ old('adres',$firma->adres) }}</textarea>


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

@if($firma->merkez_goster)

checked

@endif

>



<span class="merkez-gosterim-secimi__track" aria-hidden="true"></span>

<span>

İşaretlenirse:

<strong>

{{ $firma->unvan }} (Merkez Şube)

</strong>

olarak gösterilir.

</span>



</div>



</div>







</div>









</form>

<div class="actions">





<button

type="submit"

form="firma-guncelle-form"

class="btn-save">


<i class="bi bi-save"></i>

Güncelle


</button>








<form method="POST"
action="{{ route('firma.durum',$firma->id) }}"
class="firma-durum-form">

@csrf
@method('PATCH')


<button
type="submit"
class="firma-durum-button {{ $firma->aktif ? '' : 'firma-durum-button--activate' }}">


@if($firma->aktif)

<i class="bi bi-pause-circle"></i>
Pasif Yap

@else

<i class="bi bi-play-circle"></i>
Aktif Yap

@endif


</button>


</form>








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







</div>


@endsection
