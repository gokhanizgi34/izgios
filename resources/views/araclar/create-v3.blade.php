@extends('layouts.app')
@section('title','Yeni Araç | İZGİOS')
@section('content')
<main class="container py-4"><x-servis-yeni-tasarim :adim="2" baslik="Araç kartı oluştur" aciklama="Araç sahibini seçin, temel teknik bilgileri girin; sistem QR erişimini otomatik hazırlar." />
<form method="POST" action="{{ route('araclar.store') }}" class="servis-sayfa-kart">@csrf
<div class="kart-baslik"><h2>Araç ve sahip eşleştirmesi</h2><p>Servis kabulde araç bilgileri otomatik gelir.</p></div>
<div class="servis-form-grid"><div class="tam"><label>Müşteri <span class="text-danger">*</span></label><select class="form-select" name="musteri_id" required><option value="">Müşteri seçin</option>@foreach($musteriler as $musteri)<option value="{{ $musteri->id }}" @selected(old('musteri_id',$seciliMusteriId)==$musteri->id)>{{ $musteri->ad_soyad }} · {{ $musteri->telefon }}</option>@endforeach</select></div><div><label>Plaka <span class="text-danger">*</span></label><input class="form-control text-uppercase" name="plaka" value="{{ old('plaka') }}" placeholder="34 ABC 123" required></div><div><label>Kilometre</label><input class="form-control" type="number" name="kilometre" value="{{ old('kilometre') }}" inputmode="numeric" min="0"></div><div><label>Marka <span class="text-danger">*</span></label><select id="marka" class="form-select" name="marka" required><option value="">Marka seçin</option></select></div><div><label>Model <span class="text-danger">*</span></label><select id="model" class="form-select" name="model" required disabled><option value="">Önce marka seçin</option></select></div><div><label>Model yılı</label><input class="form-control" type="number" name="model_yili" value="{{ old('model_yili') }}" min="1900" max="2100" inputmode="numeric"></div><div><label>Yakıt tipi</label><select class="form-select" name="yakit_tipi"><option value="">Seçin</option><option @selected(old('yakit_tipi') === 'BENZİN')>BENZİN</option><option @selected(old('yakit_tipi') === 'BENZİN + LPG')>BENZİN + LPG</option><option @selected(old('yakit_tipi') === 'DİZEL')>DİZEL</option><option @selected(old('yakit_tipi') === 'HYBRID')>HYBRID</option><option @selected(old('yakit_tipi') === 'ELEKTRİK')>ELEKTRİK</option></select></div><div><label>Vites</label><select class="form-select" name="vites"><option value="">Seçin</option><option>MANUEL</option><option>OTOMATİK</option></select></div><div><label>Şasi no</label><input class="form-control" name="sase_no" value="{{ old('sase_no') }}"></div><div><label>Motor no</label><input class="form-control" name="motor_no" value="{{ old('motor_no') }}"></div><div class="tam"><label>Araç notu</label><textarea class="form-control" name="notlar">{{ old('notlar') }}</textarea></div></div>
<div class="servis-aksiyonlar"><a href="{{ route('musteriler.create') }}" class="btn btn-outline-secondary">Müşteri kartına dön</a><button class="btn btn-servis-ana">Araç kartını kaydet ve kabul ekranına geç <i class="bi bi-arrow-right"></i></button></div></form></main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const marka = document.getElementById('marka');
    const model = document.getElementById('model');
    const seciliMarka = @json(old('marka'));
    const seciliModel = @json(old('model'));
    const modeller = window.aracModelleri || {};
    Object.keys(modeller).sort().forEach(function (ad) { marka.add(new Option(ad, ad, false, ad === seciliMarka)); });
    function modelDoldur(markaAdi) {
        model.innerHTML = '';
        model.add(new Option(markaAdi ? 'Model seçin' : 'Önce marka seçin', ''));
        const liste = modeller[markaAdi] || [];
        liste.forEach(function (ad) { model.add(new Option(ad, ad, false, ad === seciliModel)); });
        model.disabled = !markaAdi || liste.length === 0;
    }
    marka.addEventListener('change', function () { modelDoldur(this.value); });
    if (seciliMarka) modelDoldur(seciliMarka);
});
</script>
@endsection
