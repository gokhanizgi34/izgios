@extends('layouts.app')


@section('title','Kullanıcılar')


@section('content')


<div class="page-header">


    <div>

        <h1>

            Kullanıcı Yönetimi

        </h1>


        <p>

            Sistem kullanıcılarını yönetin.

        </p>

    </div>



   @if(!auth()->check() || auth()->user()->sistemYetkilisiMi())


    <a href="{{ route('kullanicilar.create') }}"
       class="btn btn-primary">

        <i class="bi bi-person-plus-fill"></i>

        Yeni Kullanıcı

    </a>


    @endif


</div>





{{-- MESAJLAR --}}


@if(session('success'))

<div class="alert alert-success">

    {{ session('success') }}

</div>

@endif



@if(session('error'))

<div class="alert alert-danger">

    {{ session('error') }}

</div>

@endif





{{-- FİLTRE --}}


<div class="card mb-4">


<div class="card-body">


<form method="GET"
      action="{{ route('kullanicilar.index') }}">


<div class="row g-3">



<div class="col-md-4">


<label class="form-label">

Durum

</label>


<select name="status"
        class="form-select">


<option value="">

Tümü

</option>


<option value="aktif"
@if(request('status')=='aktif') selected @endif
>

Aktif

</option>


<option value="pasif"
@if(request('status')=='pasif') selected @endif
>

Pasif

</option>


</select>


</div>





<div class="col-md-4">


<label class="form-label">

Rol

</label>


<select name="role"
        class="form-select">


<option value="">

Tümü

</option>


<option value="sistem_yoneticisi">

Sistem Yöneticisi

</option>


<option value="admin">

Firma Yöneticisi

</option>


<option value="usta">

Usta

</option>


<option value="ofis">

Ofis

</option>


<option value="muhasebe">

Muhasebe

</option>


<option value="yedek_parca">

Yedek Parça

</option>


</select>


</div>





<div class="col-md-4 d-flex align-items-end">


<button class="btn btn-dark w-100">

<i class="bi bi-search"></i>

Filtrele

</button>


</div>



</div>


</form>


</div>


</div>






{{-- TABLO --}}


<div class="card">


<div class="card-body table-responsive">


<table class="table table-hover align-middle">


<thead>


<tr>

<th>ID</th>

<th>Kullanıcı</th>

<th>Telefon</th>

<th>Rol</th>

<th>Durum</th>

<th>Oluşturan</th>

<th>İşlem</th>

</tr>


</thead>


<tbody>
    <tbody>


@forelse($kullanicilar as $kullanici)


<tr>


<td>

{{ $kullanici->id }}

</td>





<td>


<strong>

{{ $kullanici->name }}

{{ $kullanici->surname }}

</strong>


<br>


<small class="text-muted">

{{ $kullanici->email }}

</small>


</td>





<td>

{{ $kullanici->phone ?? '-' }}

</td>





<td>


@switch($kullanici->role)



@case('sistem_yoneticisi')

<span class="badge bg-danger">

Sistem Yöneticisi

</span>

@break



@case('admin')

<span class="badge bg-primary">

Firma Yöneticisi

</span>

@break



@case('usta')

<span class="badge bg-success">

Usta

</span>

@break



@case('ofis')

<span class="badge bg-info">

Ofis

</span>

@break



@case('muhasebe')

<span class="badge bg-warning">

Muhasebe

</span>

@break



@case('yedek_parca')

<span class="badge bg-secondary">

Yedek Parça

</span>

@break



@endswitch


</td>





<td>


@if($kullanici->status === 'aktif')


<span class="badge bg-success">

Aktif

</span>


@else


<span class="badge bg-dark">

Pasif

</span>


@endif


</td>





<td>


@if($kullanici->olusturan)


{{ $kullanici->olusturan->name }}


@else


Sistem


@endif


</td>





<td>


<div class="d-flex gap-2">



<a href="{{ route('kullanicilar.edit',$kullanici) }}"
   class="btn btn-sm btn-outline-primary">


<i class="bi bi-pencil-square"></i>


</a>





@if($kullanici->aktifMi())


<form method="POST"
      action="{{ route('kullanicilar.pasif',$kullanici) }}">


@csrf

@method('PATCH')


<button type="submit"
        class="btn btn-sm btn-outline-danger"
        title="Pasif Yap">


<i class="bi bi-person-x"></i>


</button>


</form>



@else



<form method="POST"
      action="{{ route('kullanicilar.aktif',$kullanici) }}">


@csrf

@method('PATCH')


<button type="submit"
        class="btn btn-sm btn-outline-success"
        title="Aktif Yap">


<i class="bi bi-person-check"></i>


</button>


</form>



@endif



</div>


</td>



</tr>



@empty


<tr>


<td colspan="7"
    class="text-center">


Kayıtlı kullanıcı bulunamadı.


</td>


</tr>


@endforelse



</tbody>


</table>



<div class="mt-3">


{{ $kullanicilar->links() }}


</div>


</div>


</div>



@endsection