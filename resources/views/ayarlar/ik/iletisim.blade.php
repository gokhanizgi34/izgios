@extends('layouts.app')
@section('title', 'İK İletişim Ayarı')
@section('content')
<section class="container py-2"><div class="page-header"><div><h1>İK Şifre Talep Ayarı</h1><p>Şifre yenileme taleplerinin düşeceği İK e-posta adresini belirleyin.</p></div></div><div class="card" style="max-width:720px"><div class="card-body p-4">@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif<form method="POST" action="{{ route('ik.iletisim.update') }}">@csrf @method('PUT')<label class="form-label">İK e-posta adresi</label><input type="email" name="sifre_talep_email" value="{{ old('sifre_talep_email', $ayar?->sifre_talep_email) }}" required placeholder="ik@firma.com"><p class="text-muted small mt-2">Bu adres, giriş ekranından açılan şifre yenileme taleplerini alır.</p><button class="btn btn-primary mt-3">Kaydet</button></form></div></div></section>
@endsection
