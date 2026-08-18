@extends('layouts.app')
@section('title','Araç Yönetimi')
@section('content')
<x-kurumsal-kart-stil />
<section class="arac-liste-sayfa">
    <header class="arac-liste-baslik"><div><p>ARAÇ YÖNETİMİ</p><h1>🚘 Araç Kartları</h1><span>Plaka, müşteri, kilometre ve servis geçmişi aynı kart yapısında.</span></div><a class="arac-ana-btn" href="{{ route('araclar.create') }}">＋ Yeni Araç</a></header>
    @if(session('success'))<div class="arac-mesaj">✓ {{ session('success') }}</div>@endif
    <div class="arac-arama"><form method="GET"><input name="plaka" value="{{ request('plaka') }}" placeholder="Plaka, marka, model veya müşteri ara"><button class="arac-ana-btn">⌕ Ara</button></form></div>
    <div class="izgi-card-grid">
        @forelse($araclar as $arac)
        <article class="izgi-card" style="--izgi-card-actions:2">
            <div class="izgi-card__head"><span class="izgi-card__icon">🚘</span><div class="izgi-card__title-wrap"><p class="izgi-card__eyebrow">Araç kartı</p><h2 class="izgi-card__title">{{ $arac->plaka }}</h2><p class="izgi-card__subtitle">{{ trim(($arac->marka ?? '').' '.($arac->model ?? '')) ?: 'Marka / model girilmedi' }} · {{ $arac->model_yili ?: '-' }}</p></div><span class="arac-qr">⌘</span></div>
            <div class="izgi-card__body"><strong class="arac-sahip">♙ {{ $arac->musteri?->ad_soyad ?: 'Müşteri bilgisi yok' }}</strong><div class="izgi-card__meta"><div class="izgi-card__meta-item"><span class="izgi-card__label">Kilometre</span><strong class="izgi-card__value">{{ number_format($arac->kilometre ?? 0,0,',','.') }} KM</strong></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Yakıt</span><strong class="izgi-card__value">{{ $arac->yakit_tipi ?: '-' }}</strong></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Vites</span><strong class="izgi-card__value">{{ $arac->vites ?: '-' }}</strong></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Şasi no</span><strong class="izgi-card__value">{{ $arac->sase_no ? 'Kayıtlı' : '-' }}</strong></div></div></div>
            <footer class="izgi-card__footer"><a class="izgi-card__action izgi-card__action--outline" href="{{ route('araclar.show',$arac) }}">◉ Kartı Aç</a><a class="izgi-card__action izgi-card__action--primary" href="{{ route('servis.kabul',['arac_id'=>$arac->id]) }}">⌕ Servise Al</a></footer>
        </article>
        @empty <div class="arac-bos">Araç bulunamadı.</div> @endforelse
    </div>
</section>
<style>.arac-liste-sayfa{max-width:1450px;margin:auto;padding:28px}.arac-liste-baslik{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:22px}.arac-liste-baslik p{margin:0 0 5px;color:#d4a80f;font-size:12px;font-weight:850;letter-spacing:.08em}.arac-liste-baslik h1{margin:0;color:#102b52;font-size:31px}.arac-liste-baslik span{display:block;margin-top:7px;color:#687c9a}.arac-ana-btn{min-height:46px;padding:0 19px;border:0;border-radius:13px;background:#e3b82e;color:#fff;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:850;cursor:pointer}.arac-arama{padding:16px;background:#fff;border:1px solid #dce5f1;border-radius:18px;margin-bottom:22px}.arac-arama form{display:flex;gap:10px}.arac-arama input{flex:1;min-height:46px;border:1px solid #cedaec;border-radius:12px;padding:0 14px;font:inherit}.arac-qr{font-size:23px;color:#1d5cff}.arac-sahip{display:block;margin-bottom:14px;color:#102b52}.arac-bos{grid-column:1/-1;padding:48px;text-align:center;border:1px dashed #bfd0e5;border-radius:20px;background:#fff;color:#647894}.arac-mesaj{background:#eaf8ef;color:#15723b;padding:13px;border-radius:12px;margin-bottom:15px}@media(max-width:640px){.arac-liste-sayfa{padding:16px}.arac-liste-baslik{align-items:stretch;flex-direction:column}.arac-ana-btn{width:100%}.arac-arama form{flex-direction:column}}</style>
@endsection
