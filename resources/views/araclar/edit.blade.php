@extends('layouts.app')

@section('title','Araç Düzenle | İZGİ OS')

@section('content')

<div class="arac-edit-container">


<div class="arac-page-header">

<div>
<h1>🚗 Araç Düzenle</h1>
<p>Araç bilgilerini güncelleme</p>
</div>


<div class="header-actions">


<button 
type="submit"
form="arac-update-form"
class="save-btn-top">

💾 Güncelle

</button>



<a href="{{ route('araclar.show',$arac->id) }}" 
class="back-btn">

← Araç Detay

</a>


</div>

</div>



<form 
id="arac-update-form"
action="{{ route('araclar.update',$arac->id) }}" 
method="POST">

@csrf
@method('PUT')




<div class="edit-card">

<h2>👤 Müşteri Bilgisi</h2>


<label>Müşteri *</label>

<select name="musteri_id" class="form-input" required>


@foreach($musteriler as $musteri)

<option value="{{ $musteri->id }}"
@if($arac->musteri_id == $musteri->id)
selected
@endif
>

{{ $musteri->ad_soyad }}

</option>


@endforeach


</select>


</div>








<div class="edit-card">

<h2>🚗 Araç Bilgileri</h2>


<div class="form-grid">



<div>

<label>Plaka *</label>

<input 
type="text"
name="plaka"
class="form-input"
value="{{ $arac->plaka }}"
required>

</div>





<div>

<label>Marka *</label>

<select 
name="marka"
id="marka"
class="form-input"
required>


<option value="">
Marka Seçiniz
</option>


</select>

</div>







<div>

<label>Model *</label>


<select 
name="model"
id="model"
class="form-input"
required>


<option value="">
Model Seçiniz
</option>


</select>


</div>







<div>

<label>Model Yılı</label>

<input
type="number"
name="model_yili"
class="form-input"
value="{{ $arac->model_yili }}">


</div>


</div>


</div>









<div class="edit-card">

<h2>⚙ Teknik Bilgiler</h2>


<div class="form-grid">



<div>

<label>Kilometre</label>

<input

type="number"

name="kilometre"

class="form-input"

value="{{ $arac->kilometre }}">


</div>







<div>

<label>Yakıt Tipi</label>


<select name="yakit_tipi" class="form-input">


<option {{ $arac->yakit_tipi=='Benzin'?'selected':'' }}>
Benzin
</option>

<option {{ in_array($arac->yakit_tipi, ['Benzin + LPG', 'BENZİN + LPG'])?'selected':'' }}>
Benzin + LPG
</option>

<option {{ $arac->yakit_tipi=='Dizel'?'selected':'' }}>
Dizel
</option>


<option {{ $arac->yakit_tipi=='LPG'?'selected':'' }}>
LPG
</option>


<option {{ $arac->yakit_tipi=='Hibrit'?'selected':'' }}>
Hibrit
</option>


<option {{ $arac->yakit_tipi=='Elektrik'?'selected':'' }}>
Elektrik
</option>


</select>

</div>







<div>

<label>Vites</label>


<select name="vites" class="form-input">


<option {{ $arac->vites=='Manuel'?'selected':'' }}>
Manuel
</option>


<option {{ $arac->vites=='Otomatik'?'selected':'' }}>
Otomatik
</option>


<option {{ $arac->vites=='Yarı Otomatik'?'selected':'' }}>
Yarı Otomatik
</option>



</select>


</div>







<div>

<label>Şase No</label>


<input

type="text"

name="sase_no"

class="form-input"

value="{{ $arac->sase_no }}">


</div>






<div>

<label>Motor No</label>


<input

type="text"

name="motor_no"

class="form-input"

value="{{ $arac->motor_no }}">


</div>



</div>


</div>








<div class="edit-card">

<h2>📝 Notlar</h2>


<textarea

name="notlar"

class="form-input textarea"

rows="5">

{{ $arac->notlar }}

</textarea>


</div>













</form>


</div>





<script src="{{ asset('js/arac-modelleri.js') }}"></script>


<script>


document.addEventListener('DOMContentLoaded',function(){


const marka=document.getElementById('marka');

const model=document.getElementById('model');


const mevcutMarka="{{ $arac->marka }}";

const mevcutModel="{{ $arac->model }}";



if(window.aracModelleri){


Object.keys(window.aracModelleri)
.forEach(function(item){


let option=document.createElement('option');

option.value=item;

option.textContent=item;


if(item===mevcutMarka){

option.selected=true;

}


marka.appendChild(option);


});





function modelleriGetir(secilen){


model.innerHTML='<option value="">Model Seçiniz</option>';



if(window.aracModelleri[secilen]){


window.aracModelleri[secilen].forEach(function(item){


let option=document.createElement('option');


option.value=item;

option.textContent=item;



if(item===mevcutModel){

option.selected=true;

}



model.appendChild(option);



});


}



}




modelleriGetir(mevcutMarka);



marka.addEventListener('change',function(){


modelleriGetir(this.value);


});



}



});


</script>






<style>


.arac-edit-container{

padding:25px;

}



.arac-page-header{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}



.arac-page-header h1{

font-size:32px;

font-weight:800;

margin:0;

}



.back-btn{

background:#e2e8f0;

padding:12px 20px;

border-radius:12px;

text-decoration:none;

font-weight:700;

color:#334155;

}



.edit-card{

background:white;

padding:25px;

border-radius:20px;

margin-bottom:20px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

}



.edit-card h2{

font-size:20px;

margin-bottom:20px;

}



.form-grid{

display:grid;

grid-template-columns:repeat(2,1fr);

gap:20px;

}



label{

display:block;

margin-bottom:8px;

font-weight:700;

}



.form-input{

width:100%;

padding:14px;

border:1px solid #dbe3ef;

border-radius:12px;

font-size:15px;

box-sizing:border-box;

}



.textarea{

resize:none;

}



.save-area{

display:flex;

justify-content:flex-end;

}



.save-btn{

background:#2563eb;

color:white;

border:none;

padding:15px 35px;

border-radius:12px;

font-weight:800;

cursor:pointer;

}




@media(max-width:768px){


.arac-page-header{

flex-direction:column;

align-items:flex-start;

gap:15px;

}



.form-grid{

grid-template-columns:1fr;

}



.save-area{

justify-content:stretch;

}



.save-btn{

width:100%;

}



}

.header-actions{

display:flex;

gap:15px;

align-items:center;

}


.save-btn-top{

background:#2563eb;

color:white;

border:none;

padding:12px 25px;

border-radius:12px;

font-weight:800;

cursor:pointer;

font-size:15px;

}



@media(max-width:768px){


.header-actions{

width:100%;

flex-direction:column;

}


.header-actions a,
.header-actions button{

width:100%;

text-align:center;

}


}

</style>


@endsection
