@extends('layouts.app')

@section('title', 'Firma Yönetimi')

@section('content')
<style>
    .firma-list-page { max-width: 1280px; margin: 0 auto; }
    .firma-list-page__header { display:flex; justify-content:space-between; align-items:flex-start; gap:18px; margin-bottom:24px; }
    .firma-list-page__header h1 { margin:0; color:#13203a; font-size:28px; font-weight:800; }
    .firma-list-page__header h1 i { color:#2563eb; margin-right:7px; }
    .firma-list-page__header p { margin:7px 0 0; color:#64748b; font-size:14px; }
    .firma-list-page__actions { display:flex; gap:10px; flex-wrap:wrap; }
    .firma-list-page__button { min-height:42px; padding:10px 14px; display:inline-flex; justify-content:center; align-items:center; gap:7px; border-radius:10px; text-decoration:none; font-size:13px; font-weight:800; }
    .firma-list-page__button--settings { background:#e2e8f0; color:#334155; }
    .firma-list-page__button--new { background:#2563eb; color:#fff; }
    .firma-list-page__grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; }
    .firma-list-card { background:#fff; border:1px solid #e6ecf3; border-radius:19px; padding:21px; box-shadow:0 7px 23px rgba(15,23,42,.06); display:flex; flex-direction:column; }
    .firma-list-card__title { display:flex; align-items:center; gap:9px; padding-bottom:16px; margin-bottom:17px; border-bottom:1px solid #edf1f5; font-size:19px; font-weight:800; color:#14213d; }
    .firma-list-card__title i { color:#2563eb; }
    .firma-list-card__info { margin:0; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:17px 14px; }
    .firma-list-card__info div { min-width:0; }
    .firma-list-card__info dt { margin:0 0 4px; color:#718096; font-size:12px; font-weight:650; }
    .firma-list-card__info dd { margin:0; color:#1e293b; font-size:14px; font-weight:800; overflow-wrap:anywhere; }
    .firma-list-card__status { padding:3px 8px; border-radius:999px; font-size:12px; }
    .firma-list-card__status--active { color:#15803d; background:#dcfce7; }
    .firma-list-card__status--passive { color:#b91c1c; background:#fee2e2; }
    .firma-list-card__actions { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; margin-top:22px; }
    .firma-list-card__actions a { min-height:39px; padding:8px 6px; border-radius:9px; display:flex; justify-content:center; align-items:center; gap:5px; text-decoration:none; white-space:nowrap; font-size:12px; font-weight:800; }
    .firma-list-card__show { color:#1d4ed8; background:#dbeafe; }
    .firma-list-card__edit { color:#92400e; background:#fef3c7; }
    .firma-list-card__branches { color:#6d28d9; background:#ede9fe; }
    .firma-list-page__empty { grid-column:1/-1; min-height:170px; border:1px dashed #cbd5e1; border-radius:18px; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:8px; color:#64748b; }
    .firma-list-page__empty i { font-size:30px; color:#94a3b8; }
    @media(max-width:1100px){ .firma-list-page__grid{grid-template-columns:repeat(2,minmax(0,1fr));} }
    @media(max-width:680px){ .firma-list-page__header{flex-direction:column;} .firma-list-page__actions{width:100%;} .firma-list-page__button{flex:1;} .firma-list-page__grid{grid-template-columns:1fr;} }
    @media(max-width:410px){ .firma-list-card__actions{grid-template-columns:1fr;} }
</style>

<section class="firma-list-page">
    <header class="firma-list-page__header">
        <div>
            <h1><i class="bi bi-buildings"></i> Firma Yönetimi</h1>
            <p>Kayıtlı firmaları, şubelerini ve firma bilgilerini yönetin.</p>
        </div>
        <div class="firma-list-page__actions">
            <a href="{{ route('ayarlar.index') }}" class="firma-list-page__button firma-list-page__button--settings"><i class="bi bi-gear"></i> Ayarlara Dön</a>
            <a href="{{ route('firma.create') }}" class="firma-list-page__button firma-list-page__button--new"><i class="bi bi-plus-lg"></i> Yeni Firma</a>
        </div>
    </header>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="firma-list-page__grid">
        @forelse($firmalar as $firma)
            <article class="firma-list-card">
                <div class="firma-list-card__title"><i class="bi bi-building"></i><span>{{ $firma->unvan }}{{ $firma->merkez_goster ? ' (Merkez Şube)' : '' }}</span></div>
                <dl class="firma-list-card__info">
                    <div><dt>Vergi No</dt><dd>{{ $firma->vergi_no ?: '-' }}</dd></div>
                    <div><dt>Telefon</dt><dd>{{ $firma->telefon ?: '-' }}</dd></div>
                    <div><dt>Şube Sayısı</dt><dd>{{ $firma->subeler_count }}</dd></div>
                    <div><dt>Personel Sayısı</dt><dd>{{ $firma->personeller_count }}</dd></div>
                    <div><dt>Durum</dt><dd><span class="firma-list-card__status {{ $firma->aktif ? 'firma-list-card__status--active' : 'firma-list-card__status--passive' }}">{{ $firma->aktif ? 'Aktif' : 'Pasif' }}</span></dd></div>
                </dl>
                <div class="firma-list-card__actions">
                    <a href="{{ route('firma.show', $firma) }}" class="firma-list-card__show"><i class="bi bi-eye"></i> Firma Kartı</a>
                    <a href="{{ route('firma.edit', $firma) }}" class="firma-list-card__edit"><i class="bi bi-pencil"></i> Düzenle</a>
                    <a href="{{ route('sube.index', $firma) }}" class="firma-list-card__branches"><i class="bi bi-diagram-3"></i> Şubeler</a>
                </div>
            </article>
        @empty
            <div class="firma-list-page__empty"><i class="bi bi-building-add"></i><strong>Henüz firma kaydı bulunmuyor.</strong><span>İlk firma kartını oluşturarak başlayabilirsiniz.</span></div>
        @endforelse
    </div>
</section>
@endsection
