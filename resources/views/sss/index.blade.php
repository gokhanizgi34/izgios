@extends('layouts.app')

@section('title', 'Sık Sorulan Sorular | İZGİOS')

@section('content')
@php($sssKonular = app(\App\Services\DervisBilgiBankasiServisi::class)->sss())
<section class="faq-page">
    <header>
        <span>İZGİOS BİLGİ MERKEZİ</span>
        <h1>Sık Sorulan Sorular</h1>
        <p>Bu cevaplar Yapay Zekâ Asistanı ve Destek Merkezi tarafından doğrudan kullanılır. Teknik veya yetki hatalarında sizi Destek Merkezi’ne yönlendirir.</p>
    </header>

    <div class="faq-page__grid">
        @foreach($sssKonular as $konu)
            <details id="{{ $konu['id'] }}">
                <summary>{{ $konu['soru'] }} <i class="bi bi-plus-lg"></i></summary>
                <p>{{ $konu['yanit'] }}</p>
            </details>
        @endforeach
    </div>
</section>
@endsection
