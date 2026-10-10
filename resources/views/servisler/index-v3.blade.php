@extends('layouts.app')
@section('title','İş Emirleri | İZGİOS')
@section('content')
<main class="container py-4">
    <x-servis-yeni-tasarim :adim="4" baslik="İş emirleri" aciklama="Açık işleri takip edin, doğru iş emrini açın ve servisi adım adım tamamlayın." />
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <section class="servis-sayfa-kart">
        <div class="kart-baslik d-flex flex-wrap justify-content-between gap-3 align-items-center">
            <div><h2>Aktif servis kayıtları</h2><p>{{ $servisler->count() }} iş emri görüntüleniyor</p></div>
            <a class="btn btn-servis-ana" href="{{ route('servis.kabul') }}"><i class="bi bi-plus-lg"></i> Yeni servis kabul</a>
        </div>
        <div class="table-responsive is-emri-table-wrap">
            <table class="table align-middle mb-0 yeni-is-emri-liste">
                <thead><tr><th>İş emri</th><th>Araç / müşteri</th><th>Kabul</th><th>Durum</th><th class="text-end">Tutar</th><th class="text-end">İşlemler</th></tr></thead>
                <tbody>
                @forelse($servisler as $servis)
                    <tr>
                        <td data-label="İş emri"><strong>{{ $servis->servis_no }}</strong><small>{{ $servis->oncelik ?: 'Normal' }} öncelik</small></td>
                        <td data-label="Araç / müşteri"><strong>{{ $servis->arac?->plaka ?: '-' }}</strong><small>{{ $servis->musteri?->ad_soyad ?: '-' }}</small></td>
                        <td data-label="Kabul">{{ optional($servis->servis_tarihi)->format('d.m.Y H:i') ?: '-' }}</td>
                        <td data-label="Durum"><span class="yeni-durum">{{ $servis->durum }}</span></td>
                        <td data-label="Tutar" class="text-end fw-bold">₺ {{ number_format($servis->toplam_tutar ?? 0,2,',','.') }}</td>
                        <td data-label="İşlemler" class="is-emri-action-cell"><div class="is-emri-actions">
                            <a class="btn btn-sm btn-servis-ana" href="{{ route('servis.islem',$servis) }}">Aç</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('servisler.edit',$servis) }}"><i class="bi bi-pencil-square"></i> Düzenle</a>
                            <form method="POST" action="{{ route('servisler.destroy',$servis) }}" onsubmit="return confirm('Bu hatalı iş emri kalıcı olarak silinsin mi? Araç kartı silinmez; iş emrine bağlı işlemler, parçalar ve fotoğraflar silinir.');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash3"></i> Sil</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr class="is-emri-empty"><td colspan="6" class="text-center text-muted py-5">Henüz iş emri yok. Servis kabulden ilk kaydı başlatın.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
<style>
.yeni-is-emri-liste th{background:#f4f7fb;color:#60748f;text-transform:uppercase;font-size:.73rem;letter-spacing:.05em;padding:15px}.yeni-is-emri-liste td{padding:16px;color:#263c5b}.yeni-is-emri-liste small{display:block;color:#72849c;margin-top:3px}.yeni-durum{display:inline-flex;background:#edf3fb;border-radius:999px;padding:6px 10px;color:#365677;font-size:.78rem;font-weight:700}.is-emri-actions{display:flex;justify-content:flex-end;gap:6px;white-space:nowrap}.is-emri-actions form{margin:0}.tema-koyu .yeni-is-emri-liste th{background:#20304b}.tema-koyu .yeni-is-emri-liste td{color:#edf4ff}
@media(max-width:900px){.is-emri-table-wrap{overflow:visible}.yeni-is-emri-liste,.yeni-is-emri-liste tbody{display:block;width:100%}.yeni-is-emri-liste thead{display:none}.yeni-is-emri-liste tbody{padding:14px;display:grid;gap:14px}.yeni-is-emri-liste tr{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);border:1px solid #dce5f0;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 5px 16px rgba(16,42,86,.05)}.yeni-is-emri-liste td{display:block;min-width:0;padding:13px 14px!important;border:0!important;text-align:left!important;overflow-wrap:anywhere}.yeni-is-emri-liste td::before{content:attr(data-label);display:block;margin-bottom:5px;color:#71839b;font-size:.68rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase}.yeni-is-emri-liste .is-emri-action-cell{grid-column:1/-1;padding-top:8px!important;border-top:1px solid #e7edf5!important}.is-emri-actions{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;white-space:normal}.is-emri-actions form,.is-emri-actions .btn{width:100%;min-width:0;min-height:42px;display:flex;align-items:center;justify-content:center;gap:5px}.yeni-is-emri-liste .is-emri-empty{display:block}.yeni-is-emri-liste .is-emri-empty td::before{display:none}.tema-koyu .yeni-is-emri-liste tr{background:#17233a;border-color:#31415b}}
@media(max-width:520px){.servis-sayfa-kart .kart-baslik>a.btn{width:100%}.yeni-is-emri-liste tbody{padding:10px}.yeni-is-emri-liste tr{grid-template-columns:1fr}.yeni-is-emri-liste .is-emri-action-cell{grid-column:auto}.is-emri-actions{grid-template-columns:1fr}}
</style>
@endsection
