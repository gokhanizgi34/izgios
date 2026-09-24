@extends('layouts.app')
@section('title', 'Araç QR Havuzu | İZGİOS')
@section('content')
<main class="container py-4">
    <section class="servis-sayfa-kart">
        <div class="kart-baslik d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <h1><i class="bi bi-qr-code"></i> Araç QR Havuzu</h1>
                <p>Araca atanmadan önce dışarıda bastırılacak fiziksel QR etiketlerini 100’lük partiler halinde üretin.</p>
            </div>
            <form method="POST" action="{{ route('arac-qr-havuzu.uret') }}" onsubmit="return confirm('100 yeni ve benzersiz QR üretilecek. Devam edilsin mi?')">
                @csrf
                <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> 100 Adet QR Üret</button>
            </form>
        </div>

        <div class="alert alert-info mt-3">
            Üretilen QR’ları PDF olarak kaydedip matbaada bastırabilirsiniz. Boş etiket araç kaydında kamerayla okutulduğunda yalnız o araca atanır.
        </div>

        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Parti</th><th>Üretim tarihi</th><th>Toplam</th><th>Boş</th><th>Atanmış / iptal</th><th>İşlem</th></tr></thead>
                <tbody>
                @forelse($partiler as $parti)
                    <tr>
                        <td><code>{{ \Illuminate\Support\Str::limit($parti->parti_kodu, 13, '') }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($parti->olusturulma_at)->format('d.m.Y H:i') }}</td>
                        <td>{{ $parti->toplam }}</td>
                        <td><strong class="text-success">{{ $parti->bos }}</strong></td>
                        <td>{{ $parti->toplam - $parti->bos }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('arac-qr-havuzu.yazdir', $parti->parti_kodu) }}"><i class="bi bi-printer"></i> Aç / PDF Kaydet</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">Henüz QR partisi üretilmedi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
