@extends('layouts.app')
@section('title', $baslik)
@section('content')
<style>
.settings-form{max-width:1080px;margin:0 auto}.settings-form__head{padding:28px;border-radius:20px;background:linear-gradient(120deg,#172554,#0f766e);color:#fff}.settings-form__head p{margin:8px 0 0;color:#ccfbf1}.settings-form__card{margin-top:20px;padding:26px;background:#fff;border:1px solid #e4ebf5;border-radius:18px;box-shadow:0 10px 26px rgba(15,23,42,.06)}.settings-form__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.settings-form__field{padding:17px;border:1px solid #e1e8f2;border-radius:14px;background:#fbfdff}.settings-form__field label{font-weight:800;color:#14213d;margin-bottom:7px}.settings-form__field small{display:block;color:#64748b;margin-top:7px}.settings-form__switch{display:flex;align-items:center;gap:12px;min-height:42px}.settings-form__switch input{width:21px;height:21px;accent-color:#2563eb}.settings-form__actions{display:flex;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #e5eaf1}@media(max-width:700px){.settings-form__grid{grid-template-columns:1fr}.settings-form__actions{flex-direction:column}.settings-form__actions .btn{width:100%}}
</style>
<section class="settings-form"><header class="settings-form__head"><h1><i class="bi {{ $ikon }}"></i> {{ $baslik }}</h1><p>{{ $aciklama }}</p></header>
@if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
<form class="settings-form__card" method="POST" action="{{ route('ayarlar.yonetim.kaydet', $grup) }}">@csrf
<div class="settings-form__grid">@foreach($alanlar as $anahtar => $alan)<div class="settings-form__field"><label for="{{ $anahtar }}">{{ $alan['etiket'] }}</label>
@if(($alan['tip'] ?? 'text') === 'checkbox')<label class="settings-form__switch"><input id="{{ $anahtar }}" type="checkbox" name="{{ $anahtar }}" value="1" @checked(($ayarlar[$anahtar] ?? $alan['varsayilan'] ?? '0') === '1')><span>{{ $alan['yardim'] ?? 'Aktif' }}</span></label>
@elseif(($alan['tip'] ?? '') === 'select')<select id="{{ $anahtar }}" name="{{ $anahtar }}">@foreach($alan['secenekler'] as $deger=>$metin)<option value="{{ $deger }}" @selected(($ayarlar[$anahtar] ?? $alan['varsayilan'] ?? '') === $deger)>{{ $metin }}</option>@endforeach</select>
@else<input id="{{ $anahtar }}" type="{{ $alan['tip'] ?? 'text' }}" name="{{ $anahtar }}" value="{{ old($anahtar, $ayarlar[$anahtar] ?? $alan['varsayilan'] ?? '') }}" @if(!empty($alan['min'])) min="{{ $alan['min'] }}" @endif>@if(!empty($alan['yardim']))<small>{{ $alan['yardim'] }}</small>@endif
@endif
@error($anahtar)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>@endforeach</div>
<div class="settings-form__actions"><a class="btn btn-secondary" href="{{ route('ayarlar.index') }}"><i class="bi bi-arrow-left"></i> Ayarlara Dön</a><button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Ayarları Kaydet</button></div></form></section>
@endsection
