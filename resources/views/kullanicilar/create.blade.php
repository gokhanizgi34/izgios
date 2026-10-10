@extends('layouts.app')


@section('title','Yeni Personel')


@section('content')


<div class="page-header">


    <div>

        <h1>

            Yeni Personel Oluştur

        </h1>


        <p>

            Sisteme yeni kullanıcı/personel ekleyin.

        </p>

    </div>


</div>





<div class="card">


<div class="card-body">



<form method="POST"
      action="{{ route('kullanicilar.store') }}">


@csrf





<div class="row g-3">



{{-- AD --}}

<div class="col-md-6">


<label class="form-label">

Ad

</label>


<input type="text"
       name="name"
       value="{{ old('name') }}"
       class="form-control @error('name') is-invalid @enderror">


@error('name')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>





{{-- SOYAD --}}

<div class="col-md-6">


<label class="form-label">

Soyad

</label>


<input type="text"
       name="surname"
       value="{{ old('surname') }}"
       class="form-control @error('surname') is-invalid @enderror">


@error('surname')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>





{{-- KULLANICI ADI --}}

<div class="col-md-6">


<label class="form-label">

Kullanıcı Adı

</label>


<input type="text"
       name="username"
       value="{{ old('username') }}"
       class="form-control @error('username') is-invalid @enderror">


@error('username')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>





{{-- TELEFON --}}

<div class="col-md-6">


<label class="form-label">

Telefon

</label>


<input type="text"
       name="phone"
       value="{{ old('phone') }}"
       class="form-control">


</div>





{{-- TC --}}

<div class="col-md-6">


<label class="form-label">

TC Kimlik No

</label>


<input type="text"
       name="tc_no"
       maxlength="11"
       value="{{ old('tc_no') }}"
       class="form-control">


</div>





{{-- EMAIL --}}

<div class="col-md-6">


<label class="form-label">

E-Mail

</label>


<input type="email"
       name="email"
       value="{{ old('email') }}"
       class="form-control @error('email') is-invalid @enderror">


@error('email')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>

    {{-- ŞİFRE --}}

<div class="col-md-6">


<label class="form-label">

Şifre

</label>


<input type="password"
       name="password"
       class="form-control @error('password') is-invalid @enderror">


@error('password')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>





{{-- ŞİFRE TEKRAR --}}

<div class="col-md-6">


<label class="form-label">

Şifre Tekrar

</label>


<input type="password"
       name="password_confirmation"
       class="form-control">


</div>








{{-- ROL --}}

<div class="col-md-6">


<label class="form-label">

Personel Rolü

</label>


<select name="role"
        class="form-select @error('role') is-invalid @enderror">


<option value="">
Rol Seçiniz
</option>


<option value="admin"
@if(old('role')=='admin')
selected
@endif
>
Firma Yöneticisi
</option>



<option value="usta"
@if(old('role')=='usta')
selected
@endif
>
Usta
</option>



<option value="ofis"
@if(old('role')=='ofis')
selected
@endif
>
Ofis
</option>



<option value="muhasebe"
@if(old('role')=='muhasebe')
selected
@endif
>
Muhasebe
</option>



<option value="yedek_parca"
@if(old('role')=='yedek_parca')
selected
@endif
>
Yedek Parça
</option>


</select>



@error('role')

<div class="invalid-feedback">

{{ $message }}

</div>

@enderror


</div>







{{-- BİLGİ ALANI --}}


<div class="col-12">


<div class="alert alert-info">


<strong>
Yetki Bilgisi:
</strong>


<br>


Seçilen role göre kullanıcının sistem içerisindeki erişimleri otomatik belirlenir.


</div>


</div>







{{-- BUTONLAR --}}


<div class="col-12">


<button type="submit"
        class="btn btn-primary">


<i class="bi bi-person-plus"></i>


Personel Oluştur


</button>




<a href="{{ route('kullanicilar.index') }}"
   class="btn btn-secondary">


İptal


</a>


</div>





</div>


</form>


</div>


</div>



@endsection