@extends('layouts.app')
@section('title', 'Şifre Değiştir')
@section('content')
<section class="container py-2"><div class="page-header"><div><h1>Şifre Değiştir</h1><p>Hesap güvenliğiniz için şifrenizi güncelleyin.</p></div></div><div class="card" style="max-width:650px"><div class="card-body p-4">@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif<form method="POST" action="{{ route('hesap.sifre.update') }}">@csrf<label class="form-label">Mevcut şifre</label><input type="password" name="mevcut_sifre" required><label class="form-label mt-3">Yeni şifre</label><input type="password" name="password" required><label class="form-label mt-3">Yeni şifre tekrar</label><input type="password" name="password_confirmation" required><button class="btn btn-primary mt-4">Şifreyi Güncelle</button></form></div></div></section>
@endsection
