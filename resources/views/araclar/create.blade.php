@extends('layouts.app')


@section('title','Yeni Araç | İZGİ OS')


@section('content')


<div class="container">


<div class="page-header">

<div>

<h1>
🚗 Yeni Araç Kaydı
</h1>

<p>
Araç dijital kimliği oluşturma
</p>

</div>


<a href="{{ route('araclar.index') }}"
class="btn-back">

← Araçlara Dön

</a>

</div>





<form action="{{ route('araclar.store') }}"
method="POST">


@csrf




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


<option value="">

Müşteri Seçiniz

</option>


@foreach($musteriler as $musteri)


<option value="{{ $musteri->id }}">

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

placeholder="34 ABC 123"

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

Önce Marka Seçiniz

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

placeholder="2024">


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

placeholder="0">


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



<option value="Benzin">

Benzin

</option>



<option value="Dizel">

Dizel

</option>



<option value="LPG">

LPG

</option>



<option value="Hibrit">

Hibrit

</option>



<option value="Elektrik">

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



<option value="Manuel">

Manuel

</option>



<option value="Otomatik">

Otomatik

</option>



<option value="Yarı Otomatik">

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

placeholder="Opsiyonel">


</div>







<div class="form-group">


<label>

Motor No

</label>


<input

type="text"

name="motor_no"

class="input"

placeholder="Opsiyonel">


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

rows="4"

placeholder="Araç ile ilgili notlar..."></textarea>



</div>









<div class="actions">


<button

type="submit"

class="btn-save">


💾 Araç Kaydet


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




if(window.aracModelleri)
{


Object.keys(window.aracModelleri)
.forEach(function(marka){


let option =
document.createElement('option');


option.value = marka;

option.textContent = marka;


markaSelect.appendChild(option);


});



}





markaSelect.addEventListener(
'change',
function(){



modelSelect.innerHTML =
'<option value="">Model Seçiniz</option>';




let modeller =
window.aracModelleri[this.value];




if(modeller)
{


modeller.forEach(function(model){


let option =
document.createElement('option');


option.value = model;

option.textContent = model;


modelSelect.appendChild(option);



});


}



});



});


</script>

<style>


.container{

padding:25px;

}



.page-header{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}



.page-header h1{

font-size:32px;

font-weight:800;

margin:0;

}




.btn-back{

background:#e2e8f0;

padding:12px 20px;

border-radius:12px;

text-decoration:none;

font-weight:700;

color:#334155;

}





.card{

background:white;

border-radius:20px;

padding:25px;

margin-bottom:20px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

}





.card-title{

font-size:20px;

font-weight:800;

margin-bottom:20px;

}





.grid{

display:grid;

grid-template-columns:repeat(2,1fr);

gap:20px;

}





.form-group{

display:flex;

flex-direction:column;

gap:8px;

}




.input,
.textarea{


width:100%;

box-sizing:border-box;

padding:14px;

border-radius:12px;

border:1px solid #dbe3ef;

font-size:15px;


}





.actions{

display:flex;

justify-content:flex-end;

}





.btn-save{


background:#2563eb;

color:white;

border:none;

padding:15px 35px;

border-radius:12px;

font-weight:800;

cursor:pointer;


}





@media(max-width:768px){


.grid{

grid-template-columns:1fr;

}



.page-header{

flex-direction:column;

align-items:flex-start;

gap:15px;

}


}



</style>


@endsection