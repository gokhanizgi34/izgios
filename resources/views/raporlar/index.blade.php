@extends('layouts.app')
@section('title','Raporlar')
@section('content')
@php
$rol=auth()->user()->role;
$gruplar=[
 ['baslik'=>'Satışlar - Alışlar','ikon'=>'bi-cart-check','renk'=>'blue','raporlar'=>[
  ['servis','Basit Satış / Servis Raporu','Servis cirosu, işçilik, parça ve durumlar'],['satis_alis','Ürün Alış-Satış Raporu','Gelir ve gider hareketlerini karşılaştırın'],['teklifler','Teklifler','Teklif tutarları ve durumlarını izleyin'],['alti_aylik_satis','6 Aylık Satışlar','Aylık servis cirosu eğilimi'],
 ]],
 ['baslik'=>'Finansal Raporlar','ikon'=>'bi-bank','renk'=>'green','raporlar'=>[
  ['finans','Kasa - Hesap Hareketleri','Günlük gelir, gider ve net sonuç'],['cari','Hesap Bakiyeleri','Cari borç ve alacak bakiyeleri'],['kdv','KDV Raporu','Dönem hareketlerinden KDV özeti'],['masraflar','Masraflar','Dönem gider fişleri'],['gelir_gider','Gelir Gider Durumu','Gelir ve gider karşılaştırması'],['ik','Çalışanlar','Personel, bordro ve mesai özeti'],
 ]],
 ['baslik'=>'Stok Raporları','ikon'=>'bi-box-seam','renk'=>'orange','raporlar'=>[
  ['stok_detay','Ürünler / OEM Listesi','Parça, OEM, raf, stok ve fiyat'],['stok_hareketleri','Stok Hareketleri','Giriş ve çıkış hareketleri'],['depo_durumu','Depo Durumu','Stok seviyesi ve dönem hareketi'],['stok','Kritik Stoklar','Kritik eşik altındaki parçalar'],['hareketsiz_urun','Hareket Görmeyen Ürünler','Seçili dönemde hareket almayan parçalar'],
 ]],
 ['baslik'=>'Müşteri Raporları','ikon'=>'bi-people','renk'=>'purple','raporlar'=>[
  ['musteri_listesi','Müşteri Listesi','İletişim, kimlik ve kayıt tarihi'],['cari','Müşteri Cari Bakiyeleri','Borç ve alacak durumları'],['servis','Müşteri Servis Geçmişi','Araç, servis ve ciro hareketleri'],
 ]],
];
$izin=['sistem_yoneticisi'=>null,'admin'=>null,'muhasebe'=>['finans','cari','satis_alis','teklifler','alti_aylik_satis','kdv','masraflar','gelir_gider','musteri_listesi'],'ik'=>['ik'],'yedek_parca'=>['stok','stok_detay','stok_hareketleri','depo_durumu','hareketsiz_urun'],'ofis'=>['servis','cari','satis_alis','teklifler','alti_aylik_satis','musteri_listesi'],'usta'=>['servis']];
@endphp
<style>
.rh{max-width:1420px;margin:auto}.rh-head{padding:29px;border-radius:22px;background:linear-gradient(120deg,#11284d,#16776f);color:#fff}.rh-head p{margin:7px 0 0;color:#d9f8f3}.rh-summary{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin:16px 0}.rh-stat{padding:14px;border:1px solid #dce6f0;border-radius:13px;background:#fff}.rh-stat span{display:block;color:#6c7e95;font-size:11px}.rh-stat b{display:block;margin-top:4px;color:#132f56;font-size:21px}.rh-filter{display:grid;grid-template-columns:1fr 1fr auto;gap:10px;padding:16px;border:1px solid #dce6f0;border-radius:15px;background:#fff}.rh-groups{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px;margin-top:18px}.rh-group{overflow:hidden;border:1px solid #dce6f0;border-radius:17px;background:#fff}.rh-group header{display:flex;align-items:center;gap:10px;padding:17px 19px;background:#f4f8fd;color:#15375f}.rh-group header i{font-size:22px}.rh-items{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;padding:14px}.rh-report{display:flex;gap:10px;align-items:flex-start;width:100%;padding:13px;border:1px solid #dce5ef;border-radius:11px;background:#fff;text-align:left;color:#17385f}.rh-report:hover{border-color:#d9ae22;background:#fff9df}.rh-report i{color:#d5a91d}.rh-report strong,.rh-report small{display:block}.rh-report small{margin-top:3px;color:#74869b;font-size:11px;line-height:1.4}.rh-group.empty{opacity:.6}@media(max-width:1000px){.rh-summary{grid-template-columns:repeat(3,1fr)}.rh-groups{grid-template-columns:1fr}}@media(max-width:600px){.rh-summary{grid-template-columns:repeat(2,1fr)}.rh-filter,.rh-items{grid-template-columns:1fr}.rh-head{padding:22px}}
</style>
<section class="rh">
 <header class="rh-head"><h1 class="h3 mb-1"><i class="bi bi-bar-chart-fill"></i> Raporlar</h1><p>{{ $firmaAdi }} · Satış, finans, stok ve müşteri raporlarını tek merkezden oluşturun.</p></header>
 <div class="rh-summary">@foreach($kartlar as [$b,$d,$i])<article class="rh-stat"><span>{{ $b }}</span><b>{{ number_format($d,0,',','.') }}</b></article>@endforeach</div>
 <form id="reportForm" method="POST" action="{{ route('raporlar.al') }}">@csrf<input type="hidden" name="tur" id="reportType"><div class="rh-filter">@if($firmalar->isNotEmpty())<select class="form-select" name="firma_id"><option value="">Tüm firmalar</option>@foreach($firmalar as $firma)<option value="{{ $firma->id }}">{{ $firma->unvan }}</option>@endforeach</select>@else<div class="form-control bg-light">{{ $firmaAdi }}</div>@endif<input class="form-control" type="month" name="donem" value="{{ now()->format('Y-m') }}" required><div class="btn btn-warning fw-bold"><i class="bi bi-calendar3"></i> Dönem ve firma seçin</div></div>
 <div class="rh-groups">@foreach($gruplar as $grup)@php($gorunen=collect($grup['raporlar'])->filter(fn($r)=>$izin[$rol]===null||in_array($r[0],$izin[$rol]??[],true)))<article class="rh-group {{ $gorunen->isEmpty()?'empty':'' }}"><header><i class="bi {{ $grup['ikon'] }}"></i><h2 class="h5 mb-0">{{ $grup['baslik'] }}</h2></header><div class="rh-items">@forelse($gorunen as [$tur,$ad,$aciklama])<button class="rh-report" type="submit" onclick="document.getElementById('reportType').value='{{ $tur }}'"><i class="bi bi-chevron-right"></i><span><strong>{{ $ad }}</strong><small>{{ $aciklama }}</small></span></button>@empty<p class="text-muted p-2 mb-0">Bu rapor grubu rolünüze açık değil.</p>@endforelse</div></article>@endforeach</div></form>
</section>
@endsection
