@extends('layouts.app')
@section('title','Araç QR | İZGİOS')
@section('content')
<main class="qr-page">
    <header class="qr-header no-print"><h1>Araç Dijital Kimliği</h1><p>60 × 40 mm servis etiketi</p></header>
    <section class="qr-sticker" aria-label="{{ $arac->plaka }} araç QR etiketi">
        <div class="qr-box">{!! $qrCode !!}</div>
        <div class="qr-label-info">
            <strong>İZGİ<span>OS</span></strong>
            <b>{{ $arac->plaka }}</b>
            <small>{{ trim(($arac->marka ?? '').' '.($arac->model ?? '')) }}</small>
            <em>ARAÇ DİJİTAL KİMLİĞİ</em>
        </div>
    </section>
    <div class="qr-actions no-print"><button type="button" onclick="window.print()">Yazdır</button><a href="{{ route('araclar.show',$arac) }}">Araç Detay</a></div>
</main>
<style>
.qr-page{max-width:640px;margin:auto;padding:30px;text-align:center}.qr-header h1{margin:0;color:#132e52}.qr-header p{margin:6px 0 20px;color:#6b7d94}.qr-sticker{display:grid;grid-template-columns:34mm 1fr;align-items:center;width:60mm;height:40mm;margin:25px auto;padding:3mm;overflow:hidden;border:1px dashed #9aa8ba;background:#fff;color:#142b4e}.qr-box{display:flex;align-items:center;justify-content:center}.qr-box svg{width:31mm;height:31mm}.qr-label-info{text-align:left;min-width:0}.qr-label-info strong,.qr-label-info b,.qr-label-info small,.qr-label-info em{display:block}.qr-label-info strong{font-size:16px}.qr-label-info strong span{color:#d4aa20}.qr-label-info b{margin-top:8px;font-size:17px;white-space:nowrap}.qr-label-info small{margin-top:3px;color:#52657d;font-size:8px;line-height:1.3}.qr-label-info em{margin-top:9px;color:#64748b;font-size:6px;font-style:normal;font-weight:800;letter-spacing:.08em}.qr-actions{display:flex;justify-content:center;gap:12px}.qr-actions button,.qr-actions a{padding:12px 24px;border:0;border-radius:10px;text-decoration:none;font:inherit;font-weight:800;cursor:pointer}.qr-actions button{background:#ddb436;color:#fff}.qr-actions a{background:#e6edf5;color:#173659}@media print{@page{size:60mm 40mm;margin:0}html,body{width:60mm!important;height:40mm!important;margin:0!important;padding:0!important;background:#fff!important}body *{visibility:hidden}.qr-sticker,.qr-sticker *{visibility:visible}.qr-sticker{position:absolute;inset:0;width:60mm;height:40mm;margin:0;padding:3mm;border:0}.no-print{display:none!important}}
</style>
@endsection
