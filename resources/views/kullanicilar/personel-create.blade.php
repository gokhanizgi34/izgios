@extends('layouts.app')
@section('title', 'Yeni Personel')
@section('content')
<style>.personel-actions-bar{display:flex;justify-content:flex-end;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid #e8edf4}.personel-actions-bar .btn{min-width:175px;padding:11px 15px}@media(max-width:600px){.personel-actions-bar{flex-direction:column}.personel-actions-bar .btn{width:100%}}</style>
<section class="container py-2"><div class="page-header"><div><h1>Yeni Personel</h1><p>Sisteme yeni personel ve rol kaydı ekleyin.</p></div></div><div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('kullanicilar.store') }}">@csrf @include('kullanicilar.personel-form', ['kullanici' => null])<div class="personel-actions-bar"><a class="btn btn-secondary" href="{{ route('kullanicilar.aktifler') }}"><i class="bi bi-arrow-left"></i> Personel Listesine Dön</a><button class="btn btn-primary" type="submit"><i class="bi bi-person-plus-fill"></i> Personeli Oluştur</button></div></form></div></div></section>
@endsection
