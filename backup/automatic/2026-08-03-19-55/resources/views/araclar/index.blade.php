@extends('layouts.app')


@section('title','Araçlar | İZGİ OS')


@section('content')


<div class="container">


<div class="page-header">


<div>

<h1>
🚗 Araçlar
</h1>


<p>
Kayıtlı araç yönetimi
</p>


</div>




<a href="{{ route('araclar.create') }}"
class="btn-new">

+ Yeni Araç

</a>



</div>








@if(session('success'))

<div class="alert-success">

{{ session('success') }}

</div>

@endif







<div class="search-box">


<form method="GET"
action="{{ route('araclar.index') }}">



<input

type="text"

name="plaka"

class="input"

placeholder="Plaka, marka veya model ara..."

value="{{ request('plaka') }}">





<button class="btn-search">

🔍 Ara

</button>



</form>


</div>









<div class="vehicle-grid">



@forelse($araclar as $arac)



<div class="vehicle-card">



<div class="vehicle-header">



<div>


<h2>

{{ $arac->plaka }}

</h2>


<p>

{{ $arac->marka }}

{{ $arac->model }}

</p>


</div>



</div>









<div class="vehicle-info">



<div>

<span>

Müşteri

</span>


<strong>

{{ $arac->musteri->ad_soyad ?? '-' }}

</strong>


</div>





<div>

<span>

Kilometre

</span>


<strong>

{{ number_format($arac->kilometre ?? 0,0,',','.') }}

KM

</strong>


</div>





<div>

<span>

Yakıt

</span>


<strong>

{{ $arac->yakit_tipi ?? '-' }}

</strong>


</div>





<div>

<span>

Vites

</span>


<strong>

{{ $arac->vites ?? '-' }}

</strong>


</div>




</div>

<div class="card-actions">


<a href="{{ route('araclar.show',$arac->id) }}"
class="btn-detail">

🚗 Araç Detay

</a>





<a href="{{ route('araclar.edit',$arac->id) }}"
class="btn-edit">

✏ Düzenle

</a>





<a href="{{ route('araclar.qr',$arac->id) }}"
class="btn-qr">

▣ QR Yazdır

</a>







<form action="{{ route('araclar.destroy',$arac->id) }}"
method="POST">


@csrf

@method('DELETE')



<button

type="submit"

class="btn-delete"

onclick="return confirm('Bu araç silinsin mi?')">


🗑 Sil


</button>



</form>



</div>





</div>



@empty


<div class="empty">

Kayıtlı araç bulunamadı.

</div>


@endforelse



</div>



</div>

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




.btn-new{

background:#2563eb;

color:white;

padding:12px 22px;

border-radius:12px;

font-weight:800;

text-decoration:none;

}





.alert-success{

background:#dcfce7;

color:#166534;

padding:15px;

border-radius:12px;

margin-bottom:20px;

}



.search-box form{

display:flex;

gap:12px;

margin-bottom:25px;

width:100%;

}


.search-box .input{

flex:1;

height:48px;

padding:0 18px;

border:1px solid #dbe3ef;

border-radius:14px;

background:white;

font-size:16px;

color:#1e293b;

outline:none;

box-sizing:border-box;

}


.search-box .input:focus{

border-color:#2563eb;

box-shadow:0 0 0 3px rgba(37,99,235,.15);

}



.btn-search{

background:#334155;

color:white;

border:none;

padding:12px 25px;

border-radius:12px;

font-weight:700;

}




.vehicle-grid{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:20px;

}





.vehicle-card{

background:white;

border-radius:20px;

padding:25px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

}





.vehicle-header h2{

font-size:30px;

margin:0;

}




.vehicle-header p{

font-size:18px;

font-weight:700;

color:#64748b;

}





.vehicle-info{

display:grid;

grid-template-columns:1fr 1fr;

gap:15px;

margin-top:20px;

}





.vehicle-info span{

display:block;

color:#64748b;

font-size:13px;

}




.vehicle-info strong{

font-size:15px;

}





.card-actions{


display:flex;

gap:10px;

margin-top:25px;

width:100%;


}





.card-actions > a,

.card-actions > form{


flex:1;

display:flex;


}






.card-actions a,

.card-actions button{


width:100%;

height:42px;

display:flex;

align-items:center;

justify-content:center;

border:none;

border-radius:12px;

font-weight:800;

font-size:13px;

cursor:pointer;

text-decoration:none;

box-sizing:border-box;

}





.btn-detail{

background:#dbeafe;

color:#1d4ed8;

}





.btn-edit{

background:#fef3c7;

color:#92400e;

}





.btn-qr{

background:#ede9fe;

color:#6d28d9;

}





.btn-delete{

background:#fee2e2;

color:#b91c1c;

}





.empty{

padding:40px;

background:white;

border-radius:20px;

text-align:center;

}






@media(max-width:1200px){


.vehicle-grid{

grid-template-columns:repeat(2,1fr);

}


}






@media(max-width:768px){


.page-header{

flex-direction:column;

align-items:flex-start;

gap:15px;

}



.vehicle-grid{

grid-template-columns:1fr;

}



.card-actions{


display:grid;

grid-template-columns:repeat(2,1fr);


}



.card-actions > a,

.card-actions > form{

width:100%;

}



}



@media(max-width:420px){


.card-actions{


grid-template-columns:1fr;


}


}



<style>


.container{
...
}


/* bütün mevcut CSS'ler */


@media(max-width:420px){

.card-actions{

grid-template-columns:1fr;

}

}





</style>



@endsection