@extends('layouts.app')

@section('title', 'Firmalar | İZGİOS')

@section('content')
<x-kurumsal-kart-stil />
<main class="firma-liste-sayfa">
    <header class="firma-liste-baslik">
        <div><p>YÖNETİM MERKEZİ</p><h1>🏢 Firmalar</h1><span>Firma, şube ve kullanıcı yapılarını yönetin.</span></div>
        @if(auth()->user()->tamSistemYetkisiVarMi())<a href="{{ route('firma.create') }}" class="firma-liste-ana-btn">＋ Yeni Firma</a>@endif
    </header>
    @if(session('success'))<div class="firma-mesaj firma-mesaj--ok">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="firma-mesaj firma-mesaj--hata">⚠ {{ session('error') }}</div>@endif

    <section class="izgi-card-grid">
        @forelse($firmalar as $firma)
            @php($subeSayisi = $firma->subeler_count ?? $firma->subeler()->count())
            @php($personelSayisi = $firma->personeller_count ?? $firma->personeller()->count())
            <article class="izgi-card" style="--izgi-card-actions:3">
                <div class="izgi-card__head"><span class="izgi-card__icon">🏢</span><div class="izgi-card__title-wrap"><p class="izgi-card__eyebrow">Firma kartı</p><h2 class="izgi-card__title">{{ $firma->gosterim_adi }}</h2><p class="izgi-card__subtitle">{{ $subeSayisi }} şube · {{ $personelSayisi }} personel</p></div></div>
                <div class="izgi-card__body"><div class="izgi-card__meta"><div class="izgi-card__meta-item"><span class="izgi-card__label">Vergi No</span><strong class="izgi-card__value">{{ $firma->vergi_no ?: 'Tanımlanmadı' }}</strong></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Telefon</span><strong class="izgi-card__value">{{ $firma->telefon ?: 'Tanımlanmadı' }}</strong></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Durum</span><span class="izgi-card__status {{ $firma->aktif ? '' : 'izgi-card__status--passive' }}">{{ $firma->aktif ? '● Aktif' : '● Pasif' }}</span></div><div class="izgi-card__meta-item"><span class="izgi-card__label">Merkez gösterimi</span><strong class="izgi-card__value">{{ $firma->merkez_goster ? 'Açık' : 'Kapalı' }}</strong></div></div></div>
                <footer class="izgi-card__footer"><a class="izgi-card__action izgi-card__action--soft" href="{{ route('firma.show', $firma) }}">◉ Kartı Aç</a><a class="izgi-card__action izgi-card__action--primary" href="{{ route('firma.edit', $firma) }}">✎ Düzenle</a><a class="izgi-card__action izgi-card__action--outline" href="{{ route('sube.index', $firma) }}">⌘ Şubeler</a></footer>
            </article>
        @empty
            <div class="firma-bos-durum">🏢<br><strong>Henüz kayıtlı firma bulunmuyor.</strong><br>Yeni firma oluşturarak başlayabilirsiniz.</div>
        @endforelse
    </section>
    @if(method_exists($firmalar,'links'))<div class="firma-sayfalama">{{ $firmalar->links() }}</div>@endif
</main>
<style>
.firma-liste-sayfa{padding:28px;max-width:1500px;margin:0 auto}.firma-liste-baslik{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:22px}.firma-liste-baslik p{margin:0 0 5px;font-size:12px;font-weight:850;letter-spacing:.08em;color:#d4a80f}.firma-liste-baslik h1{margin:0;color:#102b52;font-size:31px}.firma-liste-baslik span{display:block;margin-top:7px;color:#687c9a}.firma-liste-ana-btn{min-height:46px;padding:0 20px;border-radius:13px;background:#e3b82e;color:#fff;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:850}.firma-mesaj{padding:13px 16px;margin-bottom:18px;border-radius:13px;font-weight:750}.firma-mesaj--ok{background:#eaf8ef;color:#15723b}.firma-mesaj--hata{background:#fff0ef;color:#bf3d33}.firma-bos-durum{grid-column:1/-1;border:1px dashed #bfd0e5;border-radius:20px;background:#fff;padding:45px;text-align:center;line-height:1.8;color:#647894}.firma-sayfalama{margin-top:22px}@media(max-width:640px){.firma-liste-sayfa{padding:16px}.firma-liste-baslik{align-items:stretch;flex-direction:column}.firma-liste-ana-btn{width:100%}}
</style>
@endsection
