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





<!-- PLAKA ARAMA -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-search"></i>

Plaka Ara

</div>



<div class="plaka-search">


<input 
type="text"
name="plaka"
placeholder="34 KL 1024"
>


<button type="button">

<i class="bi bi-search"></i>

</button>


</div>


<small>

Plakayı yazın, araç ve müşteri bilgileri otomatik getirilecektir.

</small>


</div>






<!-- ARAÇ MÜŞTERİ -->

<div class="sk-card">


<div class="sk-grid">



<div>


<h3>
<i class="bi bi-car-front"></i>
Araç Bilgileri
</h3>



<div class="info-row">
<span>Plaka</span>
<strong>34 KL 1024</strong>
</div>


<div class="info-row">
<span>Marka</span>
<strong>Fiat</strong>
</div>


<div class="info-row">
<span>Model</span>
<strong>Egea</strong>
</div>


<div class="info-row">
<span>Model Yılı</span>
<strong>2023</strong>
</div>


</div>





<div>


<h3>
<i class="bi bi-person"></i>
Müşteri Bilgileri
</h3>


<div class="info-row">
<span>Ad Soyad</span>
<strong>Ahmet Yılmaz</strong>
</div>


<div class="info-row">
<span>Telefon</span>
<strong>0532 123 45 67</strong>
</div>


<div class="info-row">
<span>E-posta</span>
<strong>ahmet@gmail.com</strong>
</div>



</div>



</div>


</div>








<!-- SERVİS GİRİŞ -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-clipboard"></i>

Servis Giriş Bilgileri

</div>




<div class="sk-two">


<div>


<label>
Araç Güncel KM *
</label>


<input
type="number"
name="giris_km"
>


</div>



<div>


<label>
Öncelik
</label>


<select name="oncelik">

<option>
Normal
</option>

<option>
Acil
</option>

<option>
Bekleyen
</option>


</select>



</div>


</div>






<div class="sk-two">


<div>


<label>
Müşteri Şikayeti *
</label>


<textarea
name="sikayet">
</textarea>


</div>




<div>


<label>
Usta İlk Kontrol Notu *
</label>


<textarea
name="notlar">
</textarea>


</div>


</div>




</div>






<!-- TESLİM BİLGİLERİ -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-key"></i>

Araç Teslim Bilgileri

</div>



<div class="sk-three">



<div>

<label>
Yakıt Seviyesi
</label>


<select>

<option>
1/2
</option>

</select>

</div>




<div>

<label>
Anahtar Durumu
</label>


<select>

<option>
Anahtar Teslim Alındı
</option>


</select>


</div>





<div>

<label>
RUSAT ARAÇTA MI?
</label>


<select>

<option>
Var
</option>


<option>
Yok
</option>


</select>


</div>




</div>







<label>

Araç Mevcut Durum Notu

</label>


<textarea></textarea>




</div>









<!-- FOTOĞRAF -->


<div class="sk-card">


<div class="sk-title">

<i class="bi bi-camera"></i>

Araç Dış Fotoğrafları

</div>



<div class="photo-grid">


@foreach([
'on'=>'Ön Görünüm',
'sag'=>'Sağ Görünüm',
'sol'=>'Sol Görünüm',
'arka'=>'Arka Görünüm'
] as $key=>$name)



<div class="photo-item">


<strong>
{{ $name }}
</strong>


<label>


<i class="bi bi-camera"></i>


Fotoğraf Ekle


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









<div class="sk-buttons">


<button type="reset"
class="btn-clear">

Temizle

</button>




<button class="btn-service">

<i class="bi bi-car-front"></i>

SERVİSE AL

</button>



</div>





</form>


</div>
/* ===================================
   İZGİOS ARAÇ KABUL EKRANI
=================================== */


.servis-kabul-container{

    width:100%;
    max-width:1400px;
    margin:auto;
    padding:25px;

}



.servis-header{

    margin-bottom:25px;

}


.servis-header h1{

    font-size:28px;
    font-weight:700;
    color:#111827;

}


.servis-header p{

    color:#64748b;

}






/* KARTLAR */


.sk-card{

    background:white;

    border-radius:18px;

    padding:25px;

    margin-bottom:20px;

    box-shadow:
    0 5px 20px rgba(0,0,0,.05);

    border:1px solid #e5e7eb;

}






.sk-title{

    font-size:18px;

    font-weight:700;

    color:#2563eb;

    margin-bottom:20px;

    display:flex;

    align-items:center;

    gap:10px;

}







/* PLAKA */


.plaka-search{

    display:flex;

    gap:10px;

}


.plaka-search input{


    flex:1;

    height:52px;

    border:1px solid #dbe3ef;

    border-radius:12px;

    padding:0 18px;

    font-size:18px;


}



.plaka-search button{


    width:55px;

    border:none;

    border-radius:12px;

    background:#2563eb;

    color:white;

    font-size:20px;


}








/* ARAÇ MÜŞTERİ */


.sk-grid{


    display:grid;

    grid-template-columns:1fr 1fr;

    gap:40px;


}


.sk-grid h3{


    color:#2563eb;

    font-size:18px;


}



.info-row{


    display:flex;

    justify-content:space-between;

    padding:10px 0;

    border-bottom:1px solid #eef2f7;


}


.info-row span{

    color:#64748b;

}







/* FORM */


.sk-two{


    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

    margin-bottom:20px;


}



.sk-three{


    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:20px;


}





label{

    display:block;

    font-weight:600;

    margin-bottom:8px;

    color:#1f2937;

}





input,
select,
textarea{


    width:100%;

    border:1px solid #dbe3ef;

    border-radius:12px;

    padding:12px 15px;

    font-size:15px;


}



textarea{

    min-height:120px;

    resize:none;

}






/* FOTOĞRAFLAR */


.photo-grid{


    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:20px;


}



.photo-item{


    text-align:center;

}



.photo-item strong{


    display:block;

    margin-bottom:12px;


}




.photo-item label{


    height:170px;

    border:2px dashed #cbd5e1;

    border-radius:15px;

    display:flex;

    align-items:center;

    justify-content:center;

    flex-direction:column;

    cursor:pointer;

    color:#64748b;


}


.photo-item i{


    font-size:35px;

    color:#2563eb;

    margin-bottom:10px;


}



.photo-item input{


    display:none;

}







/* BUTONLAR */


.sk-buttons{


    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-top:30px;

}




.btn-clear{


    height:50px;

    padding:0 30px;

    border-radius:12px;

    background:white;

    border:1px solid #dbe3ef;

    font-weight:600;


}



.btn-service{


    height:55px;

    padding:0 45px;

    border:none;

    border-radius:14px;

    background:#16a34a;

    color:white;

    font-size:17px;

    font-weight:700;


}






/* MOBİL */


@media(max-width:900px){


.servis-kabul-container{

padding:15px;

}




.sk-grid,
.sk-two,
.sk-three,
.photo-grid{


grid-template-columns:1fr;


}



.sk-card{


padding:18px;


}



.sk-buttons{


flex-direction:column;

gap:15px;


}



.btn-clear,
.btn-service{


width:100%;


}


}

@endsection