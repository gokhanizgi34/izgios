@extends('layouts.app')


@section('title','Yeni Müşteri | İZGİOS')



@section('content')


<style>

.form-control-izgios {

    width:100%;

    height:48px;

    padding:0 16px;

    border:1px solid #D9E2F3;

    border-radius:12px;

    background:#FFFFFF;

    color:#111827;

    font-size:15px;

    outline:none;

    transition:.2s;

}



.form-control-izgios:focus {

    border-color:#2563EB;

    box-shadow:0 0 0 3px rgba(37,99,235,.12);

}



textarea.form-control-izgios {

    height:120px;

    padding:15px;

    resize:none;

}



.form-label-izgios {

    display:block;

    margin-bottom:8px;

    font-size:14px;

    font-weight:600;

    color:#111827;

}


</style>





<div class="izgios-home">





<section class="welcome-panel">


    <div class="welcome-content">


        <h1>

            Yeni Müşteri

        </h1>



        <p>

            Sisteme yeni müşteri kaydı oluşturun.

        </p>


    </div>


</section>







<section class="dashboard-box">



<div class="box-header">


<h3>

<i class="bi bi-person-plus-fill"></i>

Müşteri Bilgileri

</h3>


</div>







<div style="padding:35px;">



<form method="POST"
      action="{{ route('musteriler.store') }}">


@csrf






<div style="
display:grid;
grid-template-columns:repeat(2,1fr);
gap:25px;
">







<div>


<label class="form-label-izgios">

Ad Soyad <span style="color:red">*</span>

</label>


<input type="text"
name="ad_soyad"
value="{{ old('ad_soyad') }}"
placeholder="Müşteri adı soyadı"
class="form-control-izgios"
required>


</div>








<div>


<label class="form-label-izgios">

TC Kimlik No <span style="color:red">*</span>

</label>


<input type="text"
name="tc_kimlik_no"
value="{{ old('tc_kimlik_no') }}"
maxlength="11"
placeholder="11 haneli TC Kimlik No"
class="form-control-izgios"
required>


</div>





<div>


<label class="form-label-izgios">

Telefon

</label>


<input type="text"
name="telefon"
value="{{ old('telefon') }}"
placeholder="05xx xxx xx xx"
class="form-control-izgios">


</div>





<div>


<label class="form-label-izgios">

Telefon 2

</label>


<input type="text"
name="telefon2"
value="{{ old('telefon2') }}"
placeholder="Alternatif telefon"
class="form-control-izgios">


</div>



<div>


<label class="form-label-izgios">

E-posta

</label>


<input type="email"
name="email"
value="{{ old('email') }}"
placeholder="ornek@mail.com"
class="form-control-izgios">


</div>





</div>








<div style="margin-top:25px;">



<label class="form-label-izgios">

Adres

</label>



<textarea
name="adres"
placeholder="Müşteri adresi"
class="form-control-izgios">{{ old('adres') }}</textarea>



</div>








<div style="margin-top:25px;">



<label class="form-label-izgios">

Notlar

</label>



<textarea
name="notlar"
placeholder="Müşteri hakkında özel notlar"
class="form-control-izgios">{{ old('notlar') }}</textarea>



</div>









@if($errors->any())


<div style="
margin-top:25px;
padding:15px 20px;
border-radius:12px;
background:#FEE2E2;
color:#991B1B;
">


<ul style="
margin:0;
padding-left:20px;
">



@foreach($errors->all() as $error)



<li>

{{ $error }}

</li>



@endforeach



</ul>



</div>


@endif





{{-- BUTONLAR --}}


<div style="
margin-top:35px;
display:flex;
justify-content:flex-end;
gap:15px;
">





<a href="{{ route('musteriler.index') }}"
style="
height:52px;
width:160px;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
border-radius:14px;
border:1px solid #D9E2F3;
background:#FFFFFF;
color:#111827;
text-decoration:none;
font-weight:600;
font-size:15px;
">



<i class="bi bi-arrow-left"
style="
font-size:20px;
color:#2563EB;
"></i>



<span>

Vazgeç

</span>


</a>








<button type="submit"
style="
height:52px;
width:170px;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
border:none;
border-radius:14px;
background:#2563EB;
color:#FFFFFF;
font-weight:600;
font-size:15px;
cursor:pointer;
">



<i class="bi bi-check-circle-fill"
style="
font-size:20px;
"></i>



<span>

Kaydet

</span>



</button>





</div>






</form>



</div>


</section>





</div>


@endsection