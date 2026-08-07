@extends('layouts.app')


@section('title','Servis İşlem | İZGİOS')


@section('content')


<div class="page-container">



<div class="page-header">

<h1>
<i class="bi bi-tools"></i>
Servis İşlem
</h1>


<p>
Usta çalışma ekranı
</p>


</div>





<div class="card">


<div class="card-title">

<i class="bi bi-car-front"></i>

Araç Bilgileri

<div class="card">


<div class="card-title">

<i class="bi bi-hourglass-split"></i>

Servis Durumu

</div>



<form method="POST"
action="{{ route('servis.islem.durum',$servis->id) }}">


@csrf


<select 
name="durum"
class="form-input">


<option value="Bekliyor"
@if($servis->durum=='Bekliyor')
selected
@endif
>
Bekliyor
</option>


<option value="İşlemde"
@if($servis->durum=='İşlemde')
selected
@endif
>
İşlemde
</option>


<option value="Tamamlandı"
@if($servis->durum=='Tamamlandı')
selected
@endif
>
Tamamlandı
</option>


</select>



<br>


<button class="btn-save">

Durumu Güncelle

</button>


</form>


</div>


</div>



<h3>

{{ $servis->arac->plaka }}

</h3>


<p>

{{ $servis->arac->marka }}

{{ $servis->arac->model }}

</p>



<p>

Müşteri:

<strong>

{{ $servis->musteri->ad_soyad }}

</strong>


</p>


</div>







<div class="card">


<div class="card-title">

<i class="bi bi-chat-left-text"></i>

Müşteri Şikayeti

</div>


<p>

{{ $servis->sikayet }}

</p>


</div>







<div class="card">


<div class="card-title">

<i class="bi bi-gear"></i>

Yapılan İşlemler

</div>



<button>

+ İşlem Ekle

</button>



</div>








<div class="card">


<div class="card-title">

<i class="bi bi-box"></i>

Değişen Parçalar

</div>


<button>

+ Parça Ekle

</button>


</div>







<div class="card">


<div class="card-title">

<i class="bi bi-camera"></i>

Parça Fotoğrafları

</div>



<div class="photo-grid">

Fotoğraf alanı


</div>


</div>






<div class="card">


<div class="card-title">

<i class="bi bi-pencil"></i>

Usta Notu

</div>



<textarea class="form-textarea"
rows="5"></textarea>



</div>






<button class="btn-save">

Servisi Tamamla

</button>



</div>


@endsection