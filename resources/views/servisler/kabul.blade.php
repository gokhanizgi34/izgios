@extends('layouts.app')


@section('title','Araç Kabul | İZGİOS')


@section('content')


<div class="servis-kabul-container">



<div class="servis-header">

<h1>
<i class="bi bi-car-front"></i>
Araç Kabul
</h1>


<p>
Servise giriş yapan aracın kabul işlemi
</p>


</div>






<form action="{{ route('servis.kabul.store') }}"

method="POST"

enctype="multipart/form-data">


@csrf






<input type="hidden"
name="arac_id"
id="arac_id">



<input type="hidden"
name="musteri_id"
id="musteri_id">







<!-- =================================================
 ARAÇ BULMA
================================================== -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-search"></i>

Araç Bulma

</div>







<div class="arama-satiri">


<input

type="text"

id="plaka"

placeholder="Plaka yazınız. Örn: 34 ABC 123"

autocomplete="off"

>




<button

type="button"

id="plakaAra"

class="arama-btn"

>

<i class="bi bi-search"></i>

</button>



</div>







<div class="arama-butons">



<button

type="button"

id="qrOku"

class="qr-btn"

>

<i class="bi bi-qr-code"></i>

QR Oku

</button>







<button

type="button"

id="kameraOku"

class="kamera-btn"

>

<i class="bi bi-camera"></i>

Kamera ile Plaka Oku

</button>




</div>








<div class="form-group">


<label>

Kayıtlı Araç Seç

</label>




<select

id="aracSelect"

class="form-control"

>


<option value="">

Araç seçiniz

</option>




@foreach($araclar as $arac)


<option

value="{{ $arac->id }}"

data-plaka="{{ $arac->plaka }}"

data-marka="{{ $arac->marka }}"

data-model="{{ $arac->model }}"

data-yil="{{ $arac->model_yili }}"

data-sasi="{{ $arac->sasi_no }}"

data-musteri="{{ $arac->musteri?->ad_soyad }}"

data-telefon="{{ $arac->musteri?->telefon }}"

data-email="{{ $arac->musteri?->email }}"

data-musteriid="{{ $arac->musteri_id }}"


>


{{ $arac->plaka }}

-

{{ $arac->marka }}

{{ $arac->model }}


</option>



@endforeach




</select>


</div>







<div id="aramaSonuclari"></div>



</div>









<!-- =================================================
 ARAÇ VE MÜŞTERİ BİLGİLERİ
================================================== -->



<div class="sk-card">



<div class="bilgi-grid">







<div>


<h3>

<i class="bi bi-car-front"></i>

Araç Bilgileri

</h3>



<div class="info-row">

<span>Plaka</span>

<strong id="bilgiPlaka">-</strong>

</div>



<div class="info-row">

<span>Marka</span>

<strong id="bilgiMarka">-</strong>

</div>




<div class="info-row">

<span>Model</span>

<strong id="bilgiModel">-</strong>

</div>




<div class="info-row">

<span>Model Yılı</span>

<strong id="bilgiYil">-</strong>

</div>




<div class="info-row">

<span>Şasi No</span>

<strong id="bilgiSasi">-</strong>

</div>



</div>









<div>



<h3>

<i class="bi bi-person"></i>

Müşteri Bilgileri

</h3>




<div class="info-row">

<span>Ad Soyad</span>

<strong id="bilgiMusteri">-</strong>

</div>



<div class="info-row">

<span>Telefon</span>

<strong id="bilgiTelefon">-</strong>

</div>




<div class="info-row">

<span>E-posta</span>

<strong id="bilgiEmail">-</strong>

</div>



</div>







</div>


</div>
<!-- =================================================
 SERVİS GİRİŞ BİLGİLERİ
================================================== -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-clipboard-check"></i>

Servis Giriş Bilgileri

</div>





<div class="form-grid">



<div>


<label>
Araç Güncel KM *
</label>


<input

type="number"

name="giris_km"

placeholder="Örn: 125000"

>


</div>





<div>


<label>
Öncelik
</label>



<select name="oncelik">


<option value="Normal">
Normal
</option>


<option value="Acil">
Acil
</option>


<option value="Bekleyen">
Bekleyen
</option>


</select>



</div>



</div>







<div class="form-grid">



<div>


<label>
Müşteri Şikayeti *
</label>


<textarea

name="sikayet"

placeholder="Müşteri şikayeti..."

></textarea>



</div>







<div>


<label>
Usta İlk Kontrol Notu
</label>


<textarea

name="usta_notu"

placeholder="Kontrol notları..."

></textarea>



</div>




</div>



</div>









<!-- =================================================
 ARAÇ TESLİM BİLGİLERİ
================================================== -->


<div class="sk-card">



<div class="sk-title">

<i class="bi bi-key"></i>

Araç Teslim Bilgileri

</div>






<div class="form-grid-3">





<div>


<label>

Yakıt Seviyesi

</label>



<select name="yakit_seviyesi">


<option value="1/4">
1/4
</option>


<option value="1/3">
1/3
</option>


<option value="1/2">
1/2
</option>


<option value="1/1">
1/1
</option>


</select>


</div>







<div>


<label>

Anahtar Durumu

</label>



<select name="anahtar_durumu">


<option value="Teslim Alındı">

Anahtar Teslim Alındı

</option>



<option value="Yok">

Yok

</option>



</select>


</div>







<div>


<label>

RUSAT ARAÇTA MI?

</label>




<select name="ruhsat_aracta">


<option value="Var">

Var

</option>



<option value="Yok">

Yok

</option>



</select>



</div>





</div>








<label>

Araç Mevcut Durum Notu

</label>




<textarea

name="arac_durum_notu"

placeholder="Araç mevcut durum bilgisi..."

></textarea>




</div>









<!-- =================================================
 FOTOĞRAFLAR
================================================== -->



<div class="sk-card">


<div class="sk-title">

<i class="bi bi-camera"></i>

Araç Dış Fotoğrafları

</div>







<div class="foto-grid">



@foreach([

'on'=>'Ön Görünüm',

'sag'=>'Sağ Görünüm',

'sol'=>'Sol Görünüm',

'arka'=>'Arka Görünüm'

] as $key=>$title)



<div class="foto-kutu">


<strong>

{{ $title }}

</strong>




<label>


<i class="bi bi-camera-fill"></i>


Kamera Aç



<input

type="file"

accept="image/*"

capture="environment"

name="fotograflar[{{$key}}]"


>



</label>



</div>



@endforeach




</div>




</div>









<!-- =================================================
 BUTONLAR
================================================== -->


<div class="form-buttons">


<button

type="reset"

class="btn-temizle"

>

Temizle

</button>





<button

type="submit"

class="btn-servis"

>


<i class="bi bi-car-front-fill"></i>


SERVİSE AL


</button>



</div>







</form>


</div>







<style>


.servis-kabul-container{

max-width:1400px;

margin:auto;

padding:25px;

}



.servis-header h1{

font-size:30px;

font-weight:700;

}



.sk-card{

background:white;

border:1px solid #e5e7eb;

border-radius:18px;

padding:25px;

margin-bottom:20px;

box-shadow:0 5px 20px rgba(0,0,0,.05);

}



.sk-title{

font-size:20px;

font-weight:700;

color:#2563eb;

margin-bottom:20px;

}



.arama-satiri{

display:flex;

gap:10px;

}



.arama-satiri input{

flex:1;

height:52px;

border-radius:12px;

border:1px solid #ddd;

padding:0 15px;

}



.arama-btn{

width:60px;

background:#2563eb;

color:white;

border:0;

border-radius:12px;

}



.arama-butons{

display:flex;

gap:15px;

margin-top:15px;

}



.qr-btn,
.kamera-btn{

padding:12px 20px;

border:0;

border-radius:10px;

color:white;

background:#475569;

}



.form-control,
input,
select,
textarea{

width:100%;

border-radius:12px;

border:1px solid #ddd;

padding:12px;

}



textarea{

min-height:120px;

}



.bilgi-grid{

display:grid;

grid-template-columns:1fr 1fr;

gap:40px;

}



.form-grid{

display:grid;

grid-template-columns:1fr 1fr;

gap:20px;

margin-bottom:20px;

}



.form-grid-3{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:20px;

}



.info-row{

display:flex;

justify-content:space-between;

padding:10px 0;

border-bottom:1px solid #eee;

}



.foto-grid{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:20px;

}



.foto-kutu label{

height:160px;

border:2px dashed #ccc;

border-radius:15px;

display:flex;

flex-direction:column;

justify-content:center;

align-items:center;

cursor:pointer;

}



.foto-kutu input{

display:none;

}



.foto-kutu i{

font-size:35px;

color:#2563eb;

}



.form-buttons{

display:flex;

justify-content:space-between;

margin-top:30px;

}



.btn-temizle,
.btn-servis{

height:55px;

padding:0 40px;

border-radius:12px;

font-weight:700;

}



.btn-servis{

background:#16a34a;

color:white;

border:0;

}



.btn-temizle{

background:white;

border:1px solid #ddd;

}





@media(max-width:900px){


.bilgi-grid,
.form-grid,
.form-grid-3,
.foto-grid{

grid-template-columns:1fr;

}



.arama-butons{

flex-direction:column;

}



.form-buttons{

flex-direction:column;

gap:15px;

}



.btn-temizle,
.btn-servis{

width:100%;

}



}



</style>
<script>


/*
|--------------------------------------------------------------------------
| ARAÇ SEÇME
|--------------------------------------------------------------------------
*/


function aracBilgileriniDoldur(arac)
{


document
.getElementById('arac_id')
.value =
arac.id;



document
.getElementById('musteri_id')
.value =
arac.musteri_id ?? '';




document
.getElementById('bilgiPlaka')
.innerText =
arac.plaka ?? '-';




document
.getElementById('bilgiMarka')
.innerText =
arac.marka ?? '-';




document
.getElementById('bilgiModel')
.innerText =
arac.model ?? '-';




document
.getElementById('bilgiYil')
.innerText =
arac.model_yili ?? '-';




document
.getElementById('bilgiSasi')
.innerText =
arac.sasi_no ?? '-';






if(arac.musteri)
{


document
.getElementById('bilgiMusteri')
.innerText =
arac.musteri.ad_soyad ?? '-';



document
.getElementById('bilgiTelefon')
.innerText =
arac.musteri.telefon ?? '-';



document
.getElementById('bilgiEmail')
.innerText =
arac.musteri.email ?? '-';


}



}









/*
|--------------------------------------------------------------------------
| SELECT ARAÇ
|--------------------------------------------------------------------------
*/


document
.getElementById('aracSelect')
.addEventListener(
'change',
function()
{


let option =
this.options[this.selectedIndex];



if(!option.value)
{
return;
}



let arac = {


id:option.value,


plaka:option.dataset.plaka,


marka:option.dataset.marka,


model:option.dataset.model,


model_yili:option.dataset.yil,


sasi_no:option.dataset.sasi,


musteri_id:option.dataset.musteriid,



musteri:{


ad_soyad:
option.dataset.musteri,


telefon:
option.dataset.telefon,


email:
option.dataset.email



}


};



aracBilgileriniDoldur(arac);



});









/*
|--------------------------------------------------------------------------
| PLAKA ARAMA
|--------------------------------------------------------------------------
*/


document
.getElementById('plakaAra')
.addEventListener(
'click',
function()
{


let plaka =
document
.getElementById('plaka')
.value;




if(!plaka)
{

alert(
'Plaka giriniz'
);

return;

}




fetch(

'/servis-kabul/arac-bul?plaka='
+
encodeURIComponent(plaka)

)


.then(
response =>
response.json()
)


.then(
data =>
{



let alan =
document
.getElementById('aramaSonuclari');



alan.innerHTML='';





if(data.length===0)
{


alan.innerHTML =

'<div class="sk-card">Araç bulunamadı.</div>';

return;


}







data.forEach(
function(arac)
{



alan.innerHTML += `


<div style="
padding:15px;
margin-top:10px;
border:1px solid #ddd;
border-radius:12px;
">


<strong>
${arac.plaka}
</strong>

<br>


${arac.marka ?? ''}
${arac.model ?? ''}


<br>


${arac.musteri ? arac.musteri.ad_soyad : ''}


<br><br>


<button

type="button"

onclick='aracSec(${JSON.stringify(arac)})'

>

Seç

</button>



</div>


`;



});





});



});








/*
|--------------------------------------------------------------------------
| ARAMA SONUCUNDAN ARAÇ SEÇ
|--------------------------------------------------------------------------
*/


function aracSec(arac)
{


aracBilgileriniDoldur(arac);



document
.getElementById('aramaSonuclari')
.innerHTML='';



}










/*
|--------------------------------------------------------------------------
| QR OKUMA
|--------------------------------------------------------------------------
*/


document
.getElementById('qrOku')
.addEventListener(
'click',
function()
{


alert(
'QR kamera modülü sonraki aşamada bağlanacak.'
);


});









/*
|--------------------------------------------------------------------------
| PLAKA KAMERA
|--------------------------------------------------------------------------
*/


document
.getElementById('kameraOku')
.addEventListener(
'click',
function()
{


alert(
'Plaka OCR kamera modülü sonraki aşamada bağlanacak.'
);


});



</script>



@endsection