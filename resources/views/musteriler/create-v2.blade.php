@extends('layouts.app')
@section('title','Yeni Müşteri | İZGİOS')
@section('content')
<section class="container py-4 servis-form">
    <div class="servis-form-hero"><div><div class="eyebrow">SERVİS AKIŞI · 1. ADIM</div><h1>Müşteri Kaydı</h1><p>Önce müşteri kartını oluşturun; kayıttan sonra araç bilgilerine geçeceksiniz.</p></div><i class="bi bi-person-plus"></i></div>
    @include('components.servis-akisi', ['aktifAdim' => 1])
    <form method="POST" action="{{ route('musteriler.store') }}" class="card corporate-form">@csrf
        <div class="card-body p-4 p-lg-5"><div class="form-section-title"><i class="bi bi-person-vcard"></i><div><h2>Müşteri bilgileri</h2><p>Zorunlu alanları doldurarak müşteri kartını başlatın.</p></div></div>
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="row g-3"><div class="col-md-6"><label>Ad Soyad <sup>*</sup></label><input name="ad_soyad" value="{{ old('ad_soyad') }}" required placeholder="Örn: Ahmet Yılmaz"></div><div class="col-md-6"><label>TC Kimlik No</label><input name="tc_kimlik_no" value="{{ old('tc_kimlik_no') }}" maxlength="11" placeholder="Varsa 11 haneli kimlik numarası"></div><div class="col-md-6"><label>Telefon <sup>*</sup></label><input name="telefon" value="{{ old('telefon') }}" required placeholder="05xx xxx xx xx"></div><div class="col-md-6"><label>Alternatif Telefon</label><input name="telefon2" value="{{ old('telefon2') }}" placeholder="Opsiyonel"></div><div class="col-md-6"><label>E-posta</label><input type="email" name="email" value="{{ old('email') }}" placeholder="ornek@eposta.com"></div><div class="col-md-6"><label>Doğum Tarihi</label><input type="date" name="dogum_tarihi" value="{{ old('dogum_tarihi') }}"></div><div class="col-12"><label>Adres</label><textarea name="adres" rows="3" placeholder="Adres bilgisi">{{ old('adres') }}</textarea></div><div class="col-12"><label>Notlar</label><textarea name="notlar" rows="3" placeholder="Müşteri ile ilgili notlar">{{ old('notlar') }}</textarea></div></div></div>
        <div class="form-footer"><a class="btn btn-light" href="{{ route('musteriler.index') }}"><i class="bi bi-arrow-left"></i> Müşterilere Dön</a><button class="btn btn-primary" type="submit">Kaydet ve Araç Kaydına Geç <i class="bi bi-arrow-right"></i></button></div>
    </form>
</section>
@include('components.servis-form-style')
@endsection
