@extends('layouts.app')
@section('title','Yeni Müşteri | İZGİOS')
@section('content')
<main class="container py-4"><x-servis-yeni-tasarim :adim="1" baslik="Yeni müşteri kartı" aciklama="İletişim bilgisini bir kez kaydedin; araç ve servis kayıtları bu karttan ilerler." />
@if($errors->any())<div class="alert alert-danger">Lütfen zorunlu alanları kontrol edin.</div>@endif
<form method="POST" action="{{ route('musteriler.store') }}" class="servis-sayfa-kart">@csrf
<div class="kart-baslik"><h2>Müşteri bilgileri</h2><p>Servis iletişimi için gereken temel alanlar</p></div>
<div class="servis-form-grid">
@if(auth()->user()?->tamSistemYetkisiVarMi())
<div><label>Firma <span class="text-danger">*</span></label><select class="form-select" id="firma_id" name="firma_id" required><option value="">Firma seçin</option>@foreach($firmalar as $firma)<option value="{{ $firma->id }}" @selected(old('firma_id', $firmaId) == $firma->id)>{{ $firma->gosterim_adi }}</option>@endforeach</select></div>
<div><label>Şube</label><select class="form-select" id="sube_id" name="sube_id"><option value="">Firma merkezi / şube seçilmedi</option>@foreach($subeler as $sube)<option value="{{ $sube->id }}" data-firma="{{ $sube->firma_id }}" @selected(old('sube_id') == $sube->id)>{{ $sube->sube_adi }}</option>@endforeach</select></div>
@else
<div><label>Firma</label><input class="form-control" value="{{ $firmalar->first()?->gosterim_adi }}" readonly></div>
<div><label>Çalışma şubesi</label><input class="form-control" value="{{ $subeler->firstWhere('id', auth()->user()?->firmaPersoneli?->sube_id)?->sube_adi ?? 'Firma merkezi' }}" readonly></div>
@endif
<div><label>Ad soyad <span class="text-danger">*</span></label><input class="form-control" name="ad_soyad" value="{{ old('ad_soyad') }}" required autofocus></div><div><label>Telefon <span class="text-danger">*</span></label><input class="form-control" name="telefon" inputmode="tel" value="{{ old('telefon') }}" required></div><div><label>E-posta</label><input class="form-control" type="email" name="email" value="{{ old('email') }}"></div><div><label>Doğum tarihi</label><input class="form-control" type="date" name="dogum_tarihi" value="{{ old('dogum_tarihi') }}"></div><div><label>İkinci telefon</label><input class="form-control" name="telefon2" inputmode="tel" value="{{ old('telefon2') }}"></div><div><label>T.C. kimlik no</label><input class="form-control" name="tc_kimlik_no" inputmode="numeric" value="{{ old('tc_kimlik_no') }}"></div><div class="tam"><label>Adres</label><textarea class="form-control" name="adres">{{ old('adres') }}</textarea></div><div class="tam"><label>Müşteri notu</label><textarea class="form-control" name="notlar" placeholder="Tercihler veya servis için önemli notlar">{{ old('notlar') }}</textarea></div></div>
<div class="servis-aksiyonlar"><a href="{{ route('musteriler.index') }}" class="btn btn-outline-secondary">Vazgeç</a><button class="btn btn-servis-ana">Kaydet ve araç kartına geç <i class="bi bi-arrow-right"></i></button></div></form></main>
@if(auth()->user()?->tamSistemYetkisiVarMi())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const firma = document.getElementById('firma_id');
    const sube = document.getElementById('sube_id');
    const secenekler = [...sube.options];
    function subeleriFiltrele() {
        const firmaId = firma.value;
        secenekler.forEach(option => {
            if (!option.value) return;
            option.hidden = !!firmaId && option.dataset.firma !== firmaId;
            option.disabled = !!firmaId && option.dataset.firma !== firmaId;
        });
        if (sube.selectedOptions[0]?.disabled) sube.value = '';
    }
    firma.addEventListener('change', subeleriFiltrele);
    subeleriFiltrele();
});
</script>
@endif
@endsection
