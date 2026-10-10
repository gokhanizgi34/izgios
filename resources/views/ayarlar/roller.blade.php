@extends('layouts.app')

@section('title', 'Rol ve Yetkiler')

@section('content')
<section class="container py-2">
    <div class="page-header"><div><h1><i class="bi bi-shield-lock-fill"></i> Rol ve Yetkiler</h1><p>İZGİOS erişim şablonları ve sorumluluk alanları.</p></div><a class="btn btn-secondary" href="{{ route('ayarlar.index') }}">Ayarlara Dön</a></div>
    <div class="row g-3">
        @foreach ([
            ['Sistem Yöneticisi', 'Tüm firmalar, loglar, sistem ayarları ve tam yönetim yetkisi.'],
            ['Firma Sahibi', 'Yalnız kendi firma/şube servis, mali, stok ve rapor verileri.'],
            ['Usta', 'Servis işleri, plaka/QR ile iş alma ve araç durum takibi.'],
            ['Muhasebe', 'Cari, fatura, tahsilat, mali hareketler ve raporlar.'],
            ['Ofis', 'Müşteri, araç ve ilgili raporlar.'],
            ['Yedek Parça', 'Stok, raf, giriş-çıkış, satış ve depo raporları.'],
            ['İnsan Kaynakları', 'Aktif/pasif personel, özlük ve operasyonel rol yönetimi.'],
        ] as [$rol, $aciklama])
            <div class="col-md-6 col-xl-4"><article class="card h-100"><div class="card-body"><h2 class="h5">{{ $rol }}</h2><p class="mb-0 text-muted">{{ $aciklama }}</p></div></article></div>
        @endforeach
    </div>
</section>
@endsection
