@extends('layouts.app')
@section('title', 'Kontrol Paneli | İZGİOS')
@section('content')
@php($kullanici = auth()->user())
<section class="role-dashboard">
    <header class="role-dashboard__hero">
        <div><span>İZGİOS ÇALIŞMA ALANI</span><h1>Hoş geldin, {{ $kullanici?->name ?? 'Kullanıcı' }}</h1><p>{{ $kullanici?->rolAdi() }} görünümü · yalnızca yetkili olduğunuz firma ve iş emirleri gösterilir.</p></div>
        <div class="role-dashboard__role"><small>Aktif rol</small><strong><i class="bi bi-person-check"></i> {{ $kullanici?->rolAdi() }}</strong></div>
    </header>
    <div class="role-dashboard__stats">
        <article><i class="bi bi-car-front"></i><span>Firma araçları</span><strong>{{ $ozet['arac'] }}</strong></article>
        <article><i class="bi bi-people"></i><span>Firma müşterileri</span><strong>{{ $ozet['musteri'] }}</strong></article>
        <article><i class="bi bi-tools"></i><span>İş emirleri</span><strong>{{ $ozet['toplam_servis'] }}</strong></article>
        <article><i class="bi bi-cash-stack"></i><span>İş tutarı</span><strong>₺{{ number_format($ozet['toplam_is_tutari'], 0, ',', '.') }}</strong></article>
    </div>
    <div class="role-dashboard__content">
        <article class="role-dashboard__panel"><div class="role-dashboard__heading"><h2><i class="bi bi-lightning-charge"></i> Hızlı işlemler</h2></div><div class="role-dashboard__quick">
            <a href="{{ route('araclar.index') }}"><i class="bi bi-car-front-fill"></i><span>Araçlar</span></a>
            <a href="{{ route('servis.kabul') }}"><i class="bi bi-clipboard-check-fill"></i><span>Servis kabul</span></a>
            <a href="{{ route('servisler.index') }}"><i class="bi bi-tools"></i><span>İş emirlerim</span></a>
            <a href="{{ route('destek.index') }}"><i class="bi bi-life-preserver"></i><span>Destek</span></a>
        </div></article>
        <article class="role-dashboard__panel"><div class="role-dashboard__heading"><h2><i class="bi bi-activity"></i> İş emri durumu</h2></div><div class="role-dashboard__status"><span>Bekleyen <b>{{ $ozet['bekliyor'] }}</b></span><span>İşlemde <b>{{ $ozet['islemde'] }}</b></span><span>Teslime hazır <b>{{ $ozet['hazir'] }}</b></span></div></article>
    </div>
    <article class="role-dashboard__panel"><div class="role-dashboard__heading"><h2><i class="bi bi-clock-history"></i> Son iş emirleri</h2><a href="{{ route('servisler.index') }}">Tümünü gör</a></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Servis no</th><th>Araç</th><th>Müşteri</th><th>Durum</th></tr></thead><tbody>@forelse($sonServisler as $servis)<tr><td>{{ $servis->servis_no ?: '#'.$servis->id }}</td><td>{{ $servis->arac?->plaka ?: '-' }}</td><td>{{ $servis->musteri?->ad_soyad ?: '-' }}</td><td><span class="role-dashboard__badge">{{ $servis->durum }}</span></td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-4">Henüz size ait iş emri yok.</td></tr>@endforelse</tbody></table></div></article>
</section>
@endsection
