@extends('layouts.app')


@section('title','Müşteri Detay | İZGİOS')


@section('content')


<div class="page-container">


<div class="page-header">


<div>

<h1>
👤 Müşteri Detay
</h1>


<p>
Müşteri bilgileri ve araç geçmişi
</p>


</div>






<div class="actions">

<a href="{{ route('araclar.create', ['musteri_id' => $musteri->id]) }}"
class="btn-service">

<i class="bi bi-car-front-fill"></i> Araç Ekle

</a>


<a href="{{ route('musteriler.edit',$musteri->id) }}"
class="btn-edit">

✏ Düzenle

</a>





<form action="{{ route('musteriler.destroy',$musteri->id) }}"
method="POST"
onsubmit="return confirm('Müşteri silinsin mi?')">


@csrf

@method('DELETE')


<button class="btn-delete">

🗑 Sil

</button>


</form>


<a href="{{ route('musteriler.index') }}"
class="btn-back">

← Müşterilere Dön

</a>



</div>


</div>









<div class="card">


<div class="card-title">

👤 {{ $musteri->ad_soyad }}

</div>





<div class="info-grid">



<div>

<span>
Telefon
</span>

<strong>
{{ $musteri->telefon }}
</strong>

</div>





<div>

<span>
Telefon 2
</span>

<strong>
{{ $musteri->telefon2 ?: '-' }}
</strong>

</div>





<div>

<span>
E-Posta
</span>

<strong>
{{ $musteri->email ?: '-' }}
</strong>

</div>





<div>

<span>
TC Kimlik No
</span>

<strong>
{{ $musteri->tc_kimlik_no ?: '-' }}
</strong>

</div>





<div class="full">

<span>
Adres
</span>

<strong>
{{ $musteri->adres ?: '-' }}
</strong>

</div>





<div class="full">

<span>
Notlar
</span>

<strong>
{{ $musteri->notlar ?: '-' }}
</strong>





</div>



</div>









<div class="card">


<div class="card-title">

🚗 Araçlar

</div>



@if($musteri->araclar->count())


<div class="vehicle-grid">


@foreach($musteri->araclar as $arac)



<div class="vehicle-card">


<h3>

🚗 {{ $arac->plaka }}

</h3>



<p>

{{ $arac->marka }}

{{ $arac->model }}

</p>



<a href="{{ route('araclar.show',$arac->id) }}"

class="vehicle-button">

Araç Detay

</a>

<a href="{{ route('servis.kabul', ['arac_id' => $arac->id]) }}" class="vehicle-button vehicle-service">

Servis Kabule Al

</a>



</div>



@endforeach


</div>



@else


<div class="empty">

Henüz kayıtlı araç bulunmuyor.

</div>


@endif



</div>







</div>








<style>


.page-container{

padding:25px;

}





.page-header{

display:flex;

justify-content:space-between;

align-items:center;

gap:20px;

margin-bottom:25px;

}





.page-header h1{

font-size:32px;

font-weight:800;

margin:0;

}





.page-header p{

color:#64748b;

}





.actions{

display:flex;

gap:10px;

flex-wrap:wrap;

}





.btn-edit,
.btn-delete,
.btn-back{

height:45px;

padding:0 22px;

border-radius:12px;

display:flex;

align-items:center;

justify-content:center;

font-weight:700;

border:none;

text-decoration:none;

cursor:pointer;

}





.btn-edit{

background:#facc15;

color:#713f12;

}

.btn-service{background:#2563eb;color:#fff;}





.btn-delete{

background:#fecaca;

color:#b91c1c;

}





.btn-back{

background:#e2e8f0;

color:#334155;

}





.card{

background:white;

border-radius:20px;

padding:25px;

margin-bottom:25px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

}





.card-title{

font-size:22px;

font-weight:800;

border-bottom:1px solid #e5e7eb;

padding-bottom:15px;

margin-bottom:20px;

}





.info-grid{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:25px;

}





.info-grid span{

display:block;

color:#64748b;

font-size:14px;

margin-bottom:5px;

}





.info-grid strong{

font-size:17px;

}





.full{

grid-column:1/-1;

}





.active{

color:#15803d;

}





.passive{

color:#dc2626;

}





.vehicle-grid{

display:grid;

grid-template-columns:repeat(3,1fr);

gap:20px;

}





.vehicle-card{

background:#f8fafc;

border-radius:15px;

padding:20px;

}





.vehicle-button{

display:block;

background:#dbeafe;

color:#1d4ed8;

text-align:center;

padding:12px;

border-radius:10px;

text-decoration:none;

font-weight:700;

}

.vehicle-service{margin-top:10px;background:#dcfce7;color:#166534;}





.empty{

padding:25px;

background:#f8fafc;

border-radius:15px;

}





@media(max-width:900px){


.info-grid,
.vehicle-grid{

grid-template-columns:1fr;

}


.page-header{

flex-direction:column;

align-items:flex-start;

}


.actions{

width:100%;

}


}





</style>



@endsection
