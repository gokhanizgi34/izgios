@extends('layouts.app')


@section('title','Araç Düzenle | İZGİ OS')


@section('content')


<div class="container">



<div class="page-header">


<div>

<h1>
✏ Araç Düzenle
</h1>


<p>
Araç bilgilerini güncelleme
</p>


</div>



<a href="{{ route('araclar.show',$arac->id) }}"
class="btn-back">

← Araç Detay

</a>



</div>







<form action="{{ route('araclar.update',$arac->id) }}"
method="POST">


@csrf

@method('PUT')









<div class="card">


<div class="card-title">

👤 Müşteri Bilgisi

</div>





<label>

Müşteri *

</label>



<select

name="musteri_id"

class="input"

required>


@foreach($musteriler as $musteri)


<option

value="{{ $musteri->id }}"

{{ $arac->musteri_id == $musteri->id ? 'selected':'' }}

>

{{ $musteri->ad_soyad }}

</option>


@endforeach


</select>



</div>









<div class="card">


<div class="card-title">

🚗 Araç Bilgileri

</div>





<div class="grid">



<div class="form-group">


<label>

Plaka *

</label>


<input

type="text"

name="plaka"

class="input"

value="{{ $arac->plaka }}"

required>


</div>








<div class="form-group">


<label>

Marka *

</label>


<select

name="marka"

id="marka"

class="input"

required>



<option value="">

Marka Seçiniz

</option>


</select>


</div>









<div class="form-group">


<label>

Model *

</label>


<select

name="model"

id="model"

class="input"

required>



<option value="">

Model Seçiniz

</option>


</select>


</div>








<div class="form-group">


<label>

Model Yılı

</label>


<input

type="number"

name="model_yili"

class="input"

value="{{ $arac->model_yili }}">


</div>



</div>



</div>

<div class="card">


<div class="card-title">

⚙ Teknik Bilgiler

</div>





<div class="grid">





<div class="form-group">


<label>

Kilometre

</label>



<input

type="number"

name="kilometre"

class="input"

value="{{ $arac->kilometre }}">


</div>








<div class="form-group">


<label>

Yakıt Tipi

</label>



<select

name="yakit_tipi"

class="input">



<option value="">

Seçiniz

</option>



<option value="Benzin"

{{ $arac->yakit_tipi == 'Benzin' ? 'selected':'' }}

>

Benzin

</option>



<option value="Dizel"

{{ $arac->yakit_tipi == 'Dizel' ? 'selected':'' }}

>

Dizel

</option>



<option value="LPG"

{{ $arac->yakit_tipi == 'LPG' ? 'selected':'' }}

>

LPG

</option>



<option value="Hibrit"

{{ $arac->yakit_tipi == 'Hibrit' ? 'selected':'' }}

>

Hibrit

</option>



<option value="Elektrik"

{{ $arac->yakit_tipi == 'Elektrik' ? 'selected':'' }}

>

Elektrik

</option>



</select>


</div>









<div class="form-group">


<label>

Vites

</label>



<select

name="vites"

class="input">



<option value="">

Seçiniz

</option>



<option value="Manuel"

{{ $arac->vites == 'Manuel' ? 'selected':'' }}

>

Manuel

</option>



<option value="Otomatik"

{{ $arac->vites == 'Otomatik' ? 'selected':'' }}

>

Otomatik

</option>



<option value="Yarı Otomatik"

{{ $arac->vites == 'Yarı Otomatik' ? 'selected':'' }}

>

Yarı Otomatik

</option>



</select>


</div>








<div class="form-group">


<label>

Şase No

</label>



<input

type="text"

name="sase_no"

class="input"

value="{{ $arac->sase_no }}">



</div>









<div class="form-group">


<label>

Motor No

</label>



<input

type="text"

name="motor_no"

class="input"

value="{{ $arac->motor_no }}">



</div>






</div>



</div>









<div class="card">


<div class="card-title">

📝 Notlar

</div>




<textarea

name="notlar"

class="textarea"

rows="4">{{ $arac->notlar }}</textarea>



</div>









<div class="actions">


<button

type="submit"

class="btn-save">

💾 Güncelle

</button>



</div>





</form>


</div>

<script src="{{ asset('js/arac-modelleri.js') }}"></script>


<script>


document.addEventListener(
'DOMContentLoaded',
function(){



const markaSelect =
document.getElementById('marka');


const modelSelect =
document.getElementById('model');



const mevcutMarka =
@json($arac->marka);



const mevcutModel =
@json($arac->model);





if(window.aracModelleri)
{


Object.keys(window.aracModelleri)

.forEach(function(marka){



let option =
document.createElement('option');



option.value = marka;

option.textContent = marka;




if(marka === mevcutMarka)
{

option.selected=true;

}



markaSelect.appendChild(option);



});



}



function modelleriGetir(){



modelSelect.innerHTML =
'<option value="">Model Seçiniz</option>';



let modeller =
window.aracModelleri[markaSelect.value];




if(modeller)
{


modeller.forEach(function(model){



let option =
document.createElement('option');



option.value=model;

option.textContent=model;




if(model===mevcutModel)
{

option.selected=true;

}



modelSelect.appendChild(option);



});



}



}



markaSelect.addEventListener(
'change',
modelleriGetir
);



modelleriGetir();



});


</div>


@endsection