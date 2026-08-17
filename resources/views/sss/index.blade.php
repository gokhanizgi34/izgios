@extends('layouts.app')
@section('title','Sık Sorulan Sorular | İZGİOS')
@section('content')
<section class="faq-page"><header><span>İZGİOS BİLGİ MERKEZİ</span><h1>Sık Sorulan Sorular</h1><p>Asistan önce bu çalışma akışlarını esas alır; teknik veya yetki hatalarında sizi Destek Merkezi’ne yönlendirir.</p></header><div class="faq-page__grid">
@foreach([
['Yeni müşteri ve araç nasıl eklenir?','Müşteriler > Yeni Müşteri ile iletişim kartını kaydedin. Kaydet ve araç kartına geç ile aracı aynı müşteriye bağlayın; araç kaydı sonrası Servis Kabul açılır.'],
['Servis kabul nasıl başlatılır?','Servis Kabul ekranında plaka, QR veya kayıtlı araçtan seçin. Kilometre, müşteri şikâyeti ve kabul bilgilerini kaydedin. Kayıt iş emrine dönüşür.'],
['Usta iş emrini nasıl alır?','Usta, kendi firmasındaki araç için plaka veya QR ile iş emrini açar ve Üzerime Al seçeneğini kullanır. Usta yalnız kendi aldığı iş emirlerini görür.'],
['Bakım hatırlatması nasıl çalışır?','Hatırlatmalar kilometreye değil tarih bazlıdır. Yeni servis/bakım girildiğinde önceki hatırlatma iptal olur, usta tarafından tanımlanan yeni tarih geçerli olur.'],
['QR ekranında müşteri ne görür?','Müşteri yalnız kendi plakası için servis ve periyodik bakım geçmişini ayrı sekmelerde görür. Detayda yapılan işlem, kilometre ve tarih yer alır.'],
['Muhasebe ve depo kimler içindir?','Muhasebe; firma sahibi ve muhasebe rolüne, depo; firma sahibi ve yedek parça rolüne açıktır. Her kayıt bağlı firmayla sınırlıdır.'],
['Bir hata ile karşılaşırsam ne yapmalıyım?','Destek Merkezi > Yeni Destek Talebi üzerinden ekran adresini, yaptığınız adımı ve hata kodunu yazın. Yapay zekâ öneri üretir; çözemezse Sistem Yöneticisi kuyruğuna aktarır.'],
] as [$soru,$yanit])<details><summary>{{ $soru }} <i class="bi bi-plus-lg"></i></summary><p>{{ $yanit }}</p></details>@endforeach
</div></section>
@endsection
