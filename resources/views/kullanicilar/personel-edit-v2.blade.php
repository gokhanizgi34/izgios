@extends('layouts.app')
@section('title','Personel Düzenle')
@section('content')
<section class="container py-2"><div class="page-header"><div><h1>Personel Düzenle</h1><p>{{ $kullanici->tamAdi() }} personel kaydını ve rolünü güncelleyin.</p></div></div><div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('kullanicilar.update', $kullanici) }}">@csrf @method('PUT') @include('kullanicilar.personel-form-v2', ['kullanici' => $kullanici])<div class="personel-actions-bar"><a class="btn btn-secondary" href="{{ $kullanici->status === 'aktif' ? route('kullanicilar.aktifler') : route('kullanicilar.pasifler') }}"><i class="bi bi-arrow-left"></i> Listeye dön</a><button class="btn btn-primary" type="submit"><i class="bi bi-save-fill"></i> Değişiklikleri kaydet</button></div></form></div></div></section>
@endsection
