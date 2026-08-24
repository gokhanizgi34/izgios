@extends('layouts.app')
@section('title','İş Emrini Düzenle | İZGİOS')
@section('content')
<main class="container py-4">
    <x-servis-yeni-tasarim :adim="4" baslik="İş emrini düzenle" aciklama="Hatalı araç, müşteri veya servis bilgilerini güvenli biçimde düzeltin." />
    @if($errors->any())<div class="alert alert-danger"><strong>Kayıt güncellenemedi.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $hata)<li>{{ $hata }}</li>@endforeach</ul></div>@endif
    <form class="servis-sayfa-kart" method="POST" action="{{ route('servisler.update',$servis) }}">@csrf @method('PUT')
        <div class="kart-baslik"><h2>{{ $servis->servis_no ?: 'İş Emri #'.$servis->id }}</h2><p>Araç kartı silinmez; yalnız bu iş emrinin bağlantıları ve bilgileri güncellenir.</p></div>
        <div class="servis-form-grid">
            <div><label for="musteri_id">Müşteri</label><select class="form-select" id="musteri_id" name="musteri_id" required>@foreach($musteriler as $musteri)<option value="{{ $musteri->id }}" @selected(old('musteri_id',$servis->musteri_id)==$musteri->id)>{{ $musteri->ad_soyad }} · {{ $musteri->telefon }}</option>@endforeach</select></div>
            <div><label for="arac_id">Araç</label><select class="form-select" id="arac_id" name="arac_id" required>@foreach($araclar as $arac)<option value="{{ $arac->id }}" data-musteri="{{ $arac->musteri_id }}" @selected(old('arac_id',$servis->arac_id)==$arac->id)>{{ $arac->plaka }} · {{ $arac->marka }} {{ $arac->model }}</option>@endforeach</select></div>
            <div class="tam"><label for="sikayet">Müşteri şikayeti / talebi</label><textarea class="form-control" id="sikayet" name="sikayet">{{ old('sikayet',$servis->sikayet) }}</textarea></div>
            <div><label for="durum">Durum</label><select class="form-select" id="durum" name="durum" required>@foreach(['Bekliyor','İşlemde','Teslime Hazır','Tamamlandı','İptal'] as $durum)<option value="{{ $durum }}" @selected(old('durum',$servis->durum)===$durum)>{{ $durum }}</option>@endforeach</select></div>
            <div><label for="iscilik_tutari">İşçilik tutarı</label><input class="form-control" id="iscilik_tutari" name="iscilik_tutari" type="number" min="0" step="0.01" value="{{ old('iscilik_tutari',$servis->iscilik_tutari) }}"></div>
            <div><label for="parca_tutari">Parça tutarı</label><input class="form-control" id="parca_tutari" name="parca_tutari" type="number" min="0" step="0.01" value="{{ old('parca_tutari',$servis->parca_tutari) }}"></div>
            <div class="tam"><label for="yapilan_islem">Yapılan işlem özeti</label><textarea class="form-control" id="yapilan_islem" name="yapilan_islem">{{ old('yapilan_islem',$servis->yapilan_islem) }}</textarea></div>
            <div class="tam"><label for="kullanilan_parca">Kullanılan parça özeti</label><textarea class="form-control" id="kullanilan_parca" name="kullanilan_parca">{{ old('kullanilan_parca',$servis->kullanilan_parca) }}</textarea></div>
            <div class="tam"><label for="notlar">Notlar</label><textarea class="form-control" id="notlar" name="notlar">{{ old('notlar',$servis->notlar) }}</textarea></div>
        </div>
        <div class="servis-aksiyonlar"><a class="btn btn-outline-secondary" href="{{ route('servisler.index') }}">Vazgeç</a><button class="btn btn-servis-ana" type="submit"><i class="bi bi-check-lg"></i> Değişiklikleri Kaydet</button></div>
    </form>
</main>
<script>document.addEventListener('DOMContentLoaded',()=>{const arac=document.getElementById('arac_id'),musteri=document.getElementById('musteri_id');arac.addEventListener('change',()=>{const id=arac.options[arac.selectedIndex]?.dataset.musteri;if(id)musteri.value=id})});</script>
@endsection
