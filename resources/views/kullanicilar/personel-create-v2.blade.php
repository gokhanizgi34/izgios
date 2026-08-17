@extends('layouts.app')
@section('title', 'Yeni Personel')
@section('content')
<section class="container py-2"><div class="page-header"><div><h1>Yeni Personel</h1><p>Sisteme yeni personel ve rol kaydı ekleyin.</p></div></div>@if($errors->any())<div class="alert alert-danger"><strong>Personel kaydedilemedi.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('kullanicilar.store') }}">@csrf @include('kullanicilar.personel-form-v2', ['kullanici' => null])<div class="personel-actions-bar"><a class="btn btn-secondary" href="{{ route('kullanicilar.aktifler') }}"><i class="bi bi-arrow-left"></i> Personel listesine dön</a><button class="btn btn-primary" type="submit"><i class="bi bi-person-plus-fill"></i> Personeli oluştur</button></div></form></div></div></section>
@endsection
