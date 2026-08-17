@extends('layouts.app')
@section('title', 'Destek Merkezi')
@section('content')
<section class="container py-2">
    <div class="page-header">
        <div><h1>Destek Merkezi</h1><p>Talebinizi yazın; yapay zekâ çözüm önerisi üretir veya Sistem Yöneticisi kuyruğuna yönlendirir.</p></div>
        <a class="btn btn-primary" href="{{ route('destek.create') }}"><i class="bi bi-plus-circle"></i> Yeni Destek Talebi</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card"><div class="card-body p-4">
        @forelse($talepler as $talep)
            <article class="border-bottom py-3">
                <div class="d-flex justify-content-between gap-3"><div><h2 class="h6 mb-1">{{ $talep->baslik }}</h2><p class="text-muted small mb-2">{{ $talep->mesaj }}</p></div><small class="text-muted">{{ $talep->created_at->format('d.m.Y H:i') }}</small></div>
                <span class="badge text-bg-primary">{{ ucfirst($talep->kategori) }}</span>
                <span class="badge text-bg-secondary">{{ str_replace('_', ' ', ucfirst($talep->durum)) }}</span>
                @if($talep->ai_ozet || $talep->ai_cozum)
                    <div class="alert alert-light border mt-3 mb-0 small"><strong>AI ön değerlendirme:</strong> {{ $talep->ai_ozet }}<br>@if($talep->ai_cozum)<strong>Öneri:</strong> {{ $talep->ai_cozum }}@endif</div>
                @endif
                @if(auth()->user()->tamSistemYetkisiVarMi())
                    <form class="mt-3" method="POST" action="{{ route('destek.durum', $talep) }}">@csrf @method('PATCH')<select class="d-inline-block w-auto" name="durum"><option value="acik" @selected($talep->durum === 'acik')>Açık</option><option value="inceleniyor" @selected($talep->durum === 'inceleniyor')>İnceleniyor</option><option value="cozuldu" @selected($talep->durum === 'cozuldu')>Çözüldü</option></select><button class="btn btn-sm btn-outline-primary">Durumu Kaydet</button></form>
                @endif
            </article>
        @empty
            <p class="text-muted text-center py-5 mb-0">Henüz destek talebi bulunmuyor.</p>
        @endforelse
    </div></div>
</section>
@endsection
