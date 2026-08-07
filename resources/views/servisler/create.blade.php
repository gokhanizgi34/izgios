@extends('layouts.app')


@section('title','Yeni Servis Kaydı | İZGİOS')


@section('content')


<div class="izgios-home">



<section class="welcome-panel">

<div class="welcome-content">


<h1>
🔧 Yeni Servis Kaydı
</h1>


<p>
Müşteri ve araç bilgileri ile yeni servis oluşturun.
</p>


</div>

</section>







<section class="dashboard-box">



<div class="box-header">


<h3>

<i class="bi bi-tools"></i>

Servis Bilgileri

</h3>


</div>









<div style="
padding:35px;
">



<form method="POST"
action="{{ route('servisler.store') }}">


@csrf






<div style="
display:grid;
grid-template-columns:repeat(2,1fr);
gap:25px;
">







<div>


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Müşteri *

</label>



<select
name="musteri_id"
required
style="
width:100%;
height:48px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:0 15px;
">


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








<div>


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Araç *

</label>



<select
name="arac_id"
required
style="
width:100%;
height:48px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:0 15px;
">


<option value="">

Araç Seçiniz

</option>



@foreach($araclar as $arac)


<option value="{{ $arac->id }}">

{{ $arac->plaka }} -
{{ $arac->marka }}
{{ $arac->model }}

</option>


@endforeach



</select>



</div>







<div>


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Durum

</label>



<select
name="durum"
style="
width:100%;
height:48px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:0 15px;
">


<option value="Bekliyor">

Bekliyor

</option>


<option value="İşlemde">

İşlemde

</option>


<option value="Parça Bekleniyor">

Parça Bekleniyor

</option>


<option value="Tamamlandı">

Tamamlandı

</option>


<option value="Teslim Edildi">

Teslim Edildi

</option>



</select>



</div>








<div>


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Parça Tutarı

</label>



<input

type="number"

step="0.01"

name="parca_tutari"

value="0"

style="
width:100%;
height:48px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:0 15px;
">


</div>







<div>


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

İşçilik Tutarı

</label>



<input

type="number"

step="0.01"

name="iscilik_tutari"

value="0"

style="
width:100%;
height:48px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:0 15px;
">


</div>



</div>








<div style="margin-top:25px;">


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Şikayet

</label>


<textarea

name="sikayet"

placeholder="Müşteri şikayeti..."

style="
width:100%;
height:100px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:15px;
resize:none;
"></textarea>



</div>









<div style="margin-top:25px;">


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Yapılan İşlem

</label>


<textarea

name="yapilan_islem"

placeholder="Yapılan işlemleri yazınız..."

style="
width:100%;
height:100px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:15px;
resize:none;
"></textarea>



</div>








<div style="margin-top:25px;">


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Kullanılan Parça

</label>


<textarea

name="kullanilan_parca"

placeholder="Kullanılan parçalar..."

style="
width:100%;
height:100px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:15px;
resize:none;
"></textarea>



</div>








<div style="margin-top:25px;">


<label style="
display:block;
margin-bottom:8px;
font-weight:600;
">

Notlar

</label>


<textarea

name="notlar"

placeholder="Ek notlar..."

style="
width:100%;
height:100px;
border:1px solid #D9E2F3;
border-radius:12px;
padding:15px;
resize:none;
"></textarea>



</div>










@if($errors->any())


<div style="
margin-top:25px;
background:#fee2e2;
color:#991b1b;
padding:15px;
border-radius:12px;
">


<ul>

@foreach($errors->all() as $error)

<li>

{{ $error }}

</li>

@endforeach


</ul>


</div>


@endif







<div style="
margin-top:35px;
display:flex;
justify-content:flex-end;
gap:15px;
">





<a href="{{ route('servisler.index') }}"

style="
height:50px;
padding:0 25px;
display:flex;
align-items:center;
border-radius:12px;
background:#fff;
border:1px solid #d9e2f3;
text-decoration:none;
color:#111827;
font-weight:600;
">


<i class="bi bi-arrow-left"></i>

&nbsp;

Vazgeç


</a>







<button type="submit"

style="
height:50px;
padding:0 30px;
border:none;
border-radius:12px;
background:#2563eb;
color:white;
font-weight:600;
cursor:pointer;
">


<i class="bi bi-check-circle"></i>

&nbsp;

Servisi Kaydet


</button>





</div>






</form>



</div>



</section>




</div>



@endsection