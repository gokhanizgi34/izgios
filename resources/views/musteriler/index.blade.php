@extends('layouts.app')

@section('title', 'Müşteri Kartları | İZGİOS')

@section('content')
<x-kurumsal-kart-stil />

<main class="kart-listesi-sayfa">
    <header class="kart-listesi-baslik">
        <div>
            <p class="kart-listesi-eyebrow">MÜŞTERİ YÖNETİMİ</p>
            <h1>👤 Müşteri Kartları</h1>
            <p>Müşteri iletişimi, araç bağlantıları ve servis geçmişi tek kart düzeninde.</p>
        </div>
        <a href="{{ route('musteriler.create') }}" class="izgi-liste-ana-btn">＋ Yeni Müşteri</a>
    </header>

    <section class="kart-arama-paneli" aria-label="Müşteri ara">
        <form method="GET" action="{{ route('musteriler.index') }}">
            <input type="search" name="search" value="{{ request('search') }}" class="form-input" placeholder="Ad soyad veya telefon ile ara">
            <button type="submit" class="izgi-liste-ana-btn">⌕ Ara</button>
        </form>
    </section>

    <section class="izgi-card-grid">
        @forelse($musteriler as $musteri)
            <article class="izgi-card" style="--izgi-card-actions:3">
                <div class="izgi-card__head">
                    <span class="izgi-card__icon">👤</span>
                    <div class="izgi-card__title-wrap">
                        <p class="izgi-card__eyebrow">Müşteri kartı</p>
                        <h2 class="izgi-card__title">{{ $musteri->ad_soyad }}</h2>
                        <p class="izgi-card__subtitle">{{ $musteri->araclar->count() }} kayıtlı araç</p>
                    </div>
                </div>
                <div class="izgi-card__body">
                    <div class="izgi-card__meta izgi-card__meta--single">
                        <div class="izgi-card__meta-item"><span class="izgi-card__label">Telefon</span><strong class="izgi-card__value">{{ $musteri->telefon ?: 'Tanımlanmadı' }}</strong></div>
                        <div class="izgi-card__meta-item"><span class="izgi-card__label">E-posta</span><strong class="izgi-card__value">{{ $musteri->email ?: 'Tanımlanmadı' }}</strong></div>
                    </div>
                </div>
                <footer class="izgi-card__footer">
                    <a class="izgi-card__action izgi-card__action--soft" href="{{ route('musteriler.show', $musteri) }}">◉ Detay</a>
                    <a class="izgi-card__action izgi-card__action--primary" href="{{ route('musteriler.edit', $musteri) }}">✎ Düzenle</a>
                    <form action="{{ route('musteriler.destroy', $musteri) }}" method="POST">@csrf @method('DELETE')<button class="izgi-card__action izgi-card__action--danger" onclick="return confirm('Müşteri silinsin mi?')">⌫ Sil</button></form>
                </footer>
            </article>
        @empty
            <div class="kart-bos-durum">👤<br><strong>Kayıtlı müşteri bulunamadı.</strong><br><span>Yeni müşteri ekleyerek başlayabilirsiniz.</span></div>
        @endforelse
    </section>
</main>

<style>
.kart-listesi-sayfa{width:100%;padding:28px;box-sizing:border-box}.kart-listesi-baslik{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin:0 0 22px}.kart-listesi-eyebrow{margin:0 0 5px;color:#d4a80f;font-size:12px;font-weight:850;letter-spacing:.07em}.kart-listesi-baslik h1{margin:0;color:#102b52;font-size:30px}.kart-listesi-baslik p:not(.kart-listesi-eyebrow){margin:7px 0 0;color:#687c9a}.izgi-liste-ana-btn{min-height:46px;padding:0 19px;border:0;border-radius:13px;background:#e3b82e;color:#fff!important;display:inline-flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;font-weight:850;cursor:pointer;white-space:nowrap}.kart-arama-paneli{padding:16px;margin-bottom:22px;border:1px solid #dce5f1;border-radius:18px;background:#fff;box-shadow:0 7px 20px rgba(15,35,69,.05)}.kart-arama-paneli form{display:flex;gap:10px}.kart-arama-paneli .form-input{flex:1;min-height:46px;border:1px solid #cedaec;border-radius:12px;padding:0 14px;font:inherit}.kart-bos-durum{grid-column:1/-1;padding:48px 20px;text-align:center;color:#637795;background:#fff;border:1px dashed #bfd0e5;border-radius:20px;line-height:1.8}@media(max-width:640px){.kart-listesi-sayfa{padding:16px}.kart-listesi-baslik{align-items:stretch;flex-direction:column}.izgi-liste-ana-btn{width:100%}.kart-arama-paneli form{flex-direction:column}}
</style>
@endsection
