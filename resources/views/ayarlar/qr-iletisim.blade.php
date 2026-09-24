@extends('layouts.app')

@section('title', 'QR İletişim Ayarları')

@section('content')
<div class="container" style="max-width:860px">
    <div class="page-header"><div><h1><i class="bi bi-qr-code"></i> QR İletişim Ayarları</h1><p>QR okutan müşteri, aracın en son servis gördüğü şubenin WhatsApp hattına yönlendirilir.</p></div><a href="{{ route('ayarlar.index') }}" class="btn-firma btn-detay">Ayarlara Dön</a></div>
    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    <div class="firma-card" style="margin-top:18px"><form method="POST" action="{{ route('ayarlar.qr.iletisim.kaydet') }}">@csrf
        <div class="firma-info" style="display:grid;grid-template-columns:1fr 1fr;gap:18px"><div style="grid-column:1/-1"><label for="sube_id">Servis şubesi</label><select class="form-control" id="sube_id" name="sube_id" required>@foreach($subeler as $sube)<option value="{{ $sube->id }}">{{ $sube->firma->unvan }} — {{ $sube->sube_adi }}</option>@endforeach</select></div><div style="grid-column:1/-1"><label for="whatsapp_no">WhatsApp numarası</label><input class="form-control" id="whatsapp_no" name="whatsapp_no" inputmode="tel" placeholder="Örn: 905374916936"><small>Ülke koduyla yazın. Boş bırakırsanız şubenin telefon numarası kullanılır.</small></div></div>
        <div class="firma-actions" style="margin-top:22px"><button class="btn-firma btn-duzenle" type="submit"><i class="bi bi-whatsapp"></i> WhatsApp Numarasını Kaydet</button></div>
    </form></div>
    <div class="firma-card" style="margin-top:18px"><h2>Tanımlı servis hatları</h2><div class="firma-info">@forelse($subeler as $sube)<div><span>{{ $sube->firma->unvan }} · {{ $sube->sube_adi }}</span><strong>{{ $sube->whatsapp_no ?: ($sube->telefon ?: 'Numara tanımlı değil') }}</strong></div>@empty<div>Şube kaydı bulunmuyor.</div>@endforelse</div></div>
</div>
@endsection
