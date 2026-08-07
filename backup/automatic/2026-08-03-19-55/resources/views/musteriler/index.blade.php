@extends('layouts.app')


@section('title','Müşteri Kartları | İZGİOS')



@section('content')


<div class="page-container">





<div class="page-header">


<div>


<h1>

👤 Müşteri Kartları

</h1>



<p>

Sistemde kayıtlı müşteriler.

</p>


</div>






<div class="header-actions">


<a href="{{ route('musteriler.create') }}"

class="btn-add">


+ Yeni Müşteri


</a>


</div>



</div>









{{-- ARAMA --}}



<div class="search-card">



<form method="GET"

action="{{ route('musteriler.index') }}">



<input type="text"

name="search"

value="{{ request('search') }}"

class="form-input"

placeholder="Ad Soyad veya telefon ara">





<button type="submit"

class="btn-search">


🔍 Ara


</button>



</form>


</div>









{{-- MÜŞTERİ KARTLARI --}}



<div class="customer-grid">





@foreach($musteriler as $musteri)



<div class="customer-card">





<div class="customer-header">


<h2>

👤 {{ $musteri->ad_soyad }}

</h2>


</div>







<div class="customer-info">



<div>


<span>

Telefon

</span>


<strong>

{{ $musteri->telefon ?? '-' }}

</strong>


</div>






<div>


<span>

E-Posta

</span>


<strong>

{{ $musteri->email ?? '-' }}

</strong>


</div>






<div>


<span>

Araç Sayısı

</span>


<strong>

{{ $musteri->araclar->count() ?? 0 }} Araç

</strong>


</div>



</div>
{{-- MÜŞTERİ KART BUTONLARI --}}



<div class="customer-actions">



<a href="{{ route('musteriler.show',['musteri'=>$musteri->id]) }}"

class="btn-detail">


👤 Müşteri Detay 


</a>







<a href="{{ route('musteriler.edit',['musteri'=>$musteri->id]) }}"

class="btn-edit">


✏ Düzenle


</a>







<form action="{{ route('musteriler.destroy',['musteri'=>$musteri->id]) }}"

method="POST">


@csrf

@method('DELETE')



<button type="submit"

class="btn-delete"

onclick="return confirm('Müşteri silinsin mi?')">


🗑 Sil


</button>


</form>







</div>







</div>







@endforeach





</div>









@if($musteriler->count()==0)



<div class="empty-card">


👤


<br>


Kayıtlı müşteri bulunamadı.


</div>



@endif






</div>
<style>


.page-container{

    width:100%;
    padding:25px;
    box-sizing:border-box;
    overflow-x:hidden;

}






.page-header{

    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:30px;

}





.page-header h1{

    margin:0;
    font-size:32px;
    font-weight:800;
    color:#111827;

}





.page-header p{

    color:#64748b;
    margin-top:8px;

}





.header-actions{

    display:flex;
    gap:12px;

}







.btn-add{

    height:48px;
    padding:0 25px;
    border-radius:12px;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    font-weight:700;

}








/* ARAMA */


.search-card{

    background:white;
    border-radius:20px;
    padding:25px;
    margin-bottom:30px;
    box-shadow:0 5px 20px rgba(0,0,0,.06);

}





.search-card form{

    display:flex;
    gap:15px;

}





.form-input{

    flex:1;
    height:48px;
    border:1px solid #dbe3ef;
    border-radius:12px;
    padding:0 15px;
    font-size:15px;
    box-sizing:border-box;

}







.btn-search{

    width:120px;
    border:none;
    border-radius:12px;
    background:#2563eb;
    color:white;
    font-weight:700;
    cursor:pointer;

}









/* MÜŞTERİ KARTLARI */


.customer-grid{

    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:25px;

}







.customer-card{

    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
    display:flex;
    flex-direction:column;
    gap:20px;

}







.customer-header{

    border-bottom:1px solid #e5e7eb;
    padding-bottom:15px;

}





.customer-header h2{

    margin:0;
    font-size:20px;
    font-weight:800;
    color:#111827;

}







.customer-info{

    display:flex;
    flex-direction:column;
    gap:15px;

}





.customer-info span{

    display:block;
    font-size:13px;
    color:#64748b;
    margin-bottom:5px;

}





.customer-info strong{

    font-size:16px;
    color:#111827;

}









/* BUTONLAR */


.customer-actions{

    display:flex;
    flex-direction:column;
    gap:10px;

}





.customer-actions form{

    width:100%;
    margin:0;

}







.btn-detail,
.btn-edit,
.btn-delete{


    width:100%;
    height:45px;
    border-radius:12px;
    border:none;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    text-decoration:none;
    cursor:pointer;
    box-sizing:border-box;

}







.btn-detail{

    background:#dbeafe;
    color:#2563eb;

}







.btn-edit{

    background:#fef3c7;
    color:#92400e;

}







.btn-delete{

    background:#fee2e2;
    color:#dc2626;

}








.empty-card{

    padding:40px;
    background:#f8fafc;
    border-radius:15px;
    text-align:center;
    color:#64748b;

}









/* TABLET */


@media(max-width:1100px){


.customer-grid{

    grid-template-columns:repeat(2,minmax(0,1fr));

}


}









/* TELEFON */


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





.btn-add{

    width:100%;

}





.search-card form{

    flex-direction:column;

}





.btn-search{

    width:100%;

}





.customer-grid{

    grid-template-columns:1fr;

}



}



</style>





@endsection