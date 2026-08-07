@extends('layouts.app')


@section('title','Müşteri Düzenle | İZGİOS')


@section('content')


<div class="page-container">


<div class="page-header">


<div>

<h1>
👤 Müşteri Düzenle
</h1>


<p>
Müşteri bilgilerini güncelleyin.
</p>


</div>





<div class="header-actions">


<button type="submit"
form="musteriForm"
class="btn-save">

💾 Güncelle

</button>



<a href="{{ route('musteriler.show',$musteri->id) }}"
class="btn-back">

← Vazgeç

</a>


</div>


</div>









<form id="musteriForm"

action="{{ route('musteriler.update',$musteri->id) }}"

method="POST">


@csrf

@method('PUT')








<div class="form-card">


<div class="form-title">

👤 Müşteri Bilgileri

</div>






<div class="form-grid">





<div class="form-group">


<label>
Ad Soyad *
</label>


<input type="text"

name="ad_soyad"

value="{{ old('ad_soyad',$musteri->ad_soyad) }}"

class="form-input"

required>


</div>









<div class="form-group">


<label>
TC Kimlik No
</label>


<input type="text"

name="tc_kimlik_no"

maxlength="11"

value="{{ old('tc_kimlik_no',$musteri->tc_kimlik_no) }}"

class="form-input">


</div>









<div class="form-group">


<label>
Telefon *
</label>


<input type="text"

name="telefon"

value="{{ old('telefon',$musteri->telefon) }}"

class="form-input"

required>


</div>








<div class="form-group">


<label>
Telefon 2
</label>


<input type="text"

name="telefon2"

value="{{ old('telefon2',$musteri->telefon2) }}"

class="form-input">


</div>









<div class="form-group">


<label>
E-Posta
</label>


<input type="email"

name="email"

value="{{ old('email',$musteri->email) }}"

class="form-input">


</div>









<div class="form-group full">


<label>
Adres
</label>


<textarea

name="adres"

rows="4"

class="form-textarea">{{ old('adres',$musteri->adres) }}</textarea>


</div>









<div class="form-group full">


<label>
Notlar
</label>


<textarea

name="notlar"

rows="4"

class="form-textarea">{{ old('notlar',$musteri->notlar) }}</textarea>


</div>









<style>


.page-container{

padding:25px;

}




.page-header{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

gap:20px;

}





.page-header h1{

font-size:32px;

font-weight:800;

margin:0;

}





.page-header p{

color:#64748b;

}





.header-actions{

display:flex;

gap:12px;

}





.btn-save,
.btn-back{

height:48px;

padding:0 25px;

border-radius:12px;

display:flex;

align-items:center;

justify-content:center;

font-weight:700;

text-decoration:none;

cursor:pointer;

}



.btn-save{

background:#2563eb;

color:white;

border:none;

}




.btn-back{

background:#e2e8f0;

color:#334155;

}





.form-card{

background:white;

border-radius:20px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

overflow:hidden;

}





.form-title{

padding:22px 30px;

font-size:20px;

font-weight:800;

border-bottom:1px solid #e5e7eb;

}





.form-grid{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:25px;

padding:30px;

}





.form-group{

display:flex;

flex-direction:column;

gap:8px;

}





.form-group label{

font-weight:700;

color:#334155;

}





.form-input,
.form-textarea{

border:1px solid #dbe3ef;

border-radius:12px;

padding:14px;

font-size:15px;

width:100%;

box-sizing:border-box;

}





.form-textarea{

resize:vertical;

}





.full{

grid-column:1/-1;

}








.switch-box{

display:flex;

align-items:center;

gap:12px;

}





.switch input{

display:none;

}




.slider{

width:45px;

height:24px;

background:#cbd5e1;

border-radius:30px;

display:block;

position:relative;

cursor:pointer;

}





.slider:before{

content:"";

width:18px;

height:18px;

background:white;

border-radius:50%;

position:absolute;

left:3px;

top:3px;

transition:.3s;

}





.switch input:checked + .slider{

background:#2563eb;

}





.switch input:checked + .slider:before{

transform:translateX(21px);

}







@media(max-width:1100px){


.form-grid{

grid-template-columns:repeat(2,1fr);

}


}





@media(max-width:700px){


.page-container{

padding:15px;

}


.page-header{

flex-direction:column;

align-items:flex-start;

}


.header-actions{

width:100%;

}


.btn-save,
.btn-back{

flex:1;

}


.form-grid{

grid-template-columns:1fr;

padding:20px;

}


}


</style>



@endsection