@extends('layouts.app')
@section('title', 'Firma Sohbeti')
@section('content')
<section class="container py-2">
    <div class="page-header"><div><h1>Firma Sohbeti</h1><p>Genel, birim ve grup odalarında firma içi iletişim.</p></div></div>
    <div class="row g-3">
        <aside class="col-lg-4"><div class="card"><div class="card-body p-3">
            @if(auth()->user()->tamSistemYetkisiVarMi())
                <form method="GET" action="{{ route('sohbet.index') }}" class="mb-3"><label class="form-label">Yönetilecek firma</label><select name="firma" onchange="this.form.submit()"><option value="">Firma seçiniz</option>@foreach($firmalar as $firma)<option value="{{ $firma->id }}" @selected($firmaId === $firma->id)>{{ $firma->unvan }}</option>@endforeach</select></form>
            @endif
            <h2 class="h6">Sohbet Odaları</h2>
            @forelse($odalar as $item)<a class="d-block p-2 mb-1 rounded text-decoration-none {{ $oda?->id === $item->id ? 'bg-primary text-white' : 'bg-light text-dark' }}" href="{{ route('sohbet.index', ['firma' => $firmaId, 'oda' => $item->id]) }}">{{ $item->ad }}<small class="d-block">{{ ucfirst($item->tip) }}</small></a>@empty<p class="text-muted small">{{ $firmaId ? 'Henüz sohbet odası yok.' : 'Önce firma seçiniz.' }}</p>@endforelse
            @if($firmaId)<hr><form method="POST" action="{{ route('sohbet.oda.store') }}">@csrf<input type="hidden" name="firma_id" value="{{ $firmaId }}"><label class="form-label">Yeni oda</label><input name="ad" required placeholder="Örn. Servis Birimi"><select class="mt-2" name="tip"><option value="genel">Genel sohbet</option><option value="birim">Birim</option><option value="ozel">Özel grup</option></select><button class="btn btn-sm btn-primary w-100 mt-2">Oda Oluştur</button></form>@endif
        </div></div></aside>
        <main class="col-lg-8"><div class="card h-100"><div class="card-body p-4 d-flex flex-column">
            @if($oda)
                <h2 class="h5">{{ $oda->ad }}</h2><p class="text-muted small">{{ ucfirst($oda->tip) }} sohbet odası</p><hr>
                <div class="flex-grow-1" style="min-height:360px">@forelse($mesajlar as $mesaj)<div class="mb-3 {{ $mesaj->user_id === auth()->id() ? 'text-end' : '' }}"><strong class="small d-block">{{ $mesaj->kullanici?->tamAdi() }} · {{ $mesaj->kullanici?->rolAdi() }}</strong><span class="d-inline-block p-2 rounded bg-light text-start">{{ $mesaj->mesaj }}</span><small class="d-block text-muted">{{ $mesaj->created_at->format('d.m.Y H:i') }}</small></div>@empty<p class="text-muted text-center py-5">Bu odada henüz mesaj yok.</p>@endforelse</div>
                <form method="POST" action="{{ route('sohbet.mesaj.store', $oda) }}">@csrf<input type="hidden" name="firma_id" value="{{ $firmaId }}"><textarea name="mesaj" rows="3" required placeholder="Mesajınızı yazın..."></textarea><button class="btn btn-primary mt-2"><i class="bi bi-send"></i> Gönder</button></form>
            @else
                <div class="text-center text-muted py-5">Soldan bir sohbet odası seçin veya yeni oda oluşturun.</div>
            @endif
        </div></div></main>
    </div>
</section>
@endsection
