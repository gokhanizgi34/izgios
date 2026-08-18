@extends('layouts.app')

@section('title', 'İş Emri Detayı')

@section('content')
<section class="container py-4 servis-detay">
    @php
        $musteriTelefon = preg_replace('/\D+/', '', (string) $servis->musteri?->telefon);
        if (str_starts_with($musteriTelefon, '0')) { $musteriTelefon = '90'.substr($musteriTelefon, 1); }
        $islemOzeti = $servis->islemler->pluck('islem_adi')->filter()->implode(', ') ?: ($servis->yapilan_islem ?: 'Servis işlemi');
        $parcaOzeti = $servis->parcalar->map(fn ($parca) => $parca->parca_adi.' ('.$parca->adet.' adet)')->implode(', ');
        $whatsappMesaj = "İZGİOS Servis Özeti\n\n"
            ."İş Emri: {$servis->servis_no}\n"
            ."Araç: ".($servis->arac?->plaka ?: '-')." · ".trim(($servis->arac?->marka ?? '').' '.($servis->arac?->model ?? ''))."\n"
            ."Tarih: ".(optional($servis->servis_tarihi)->format('d.m.Y H:i') ?? $servis->created_at->format('d.m.Y H:i'))."\n"
            ."Yapılan işlem: {$islemOzeti}\n"
            .($parcaOzeti ? "Kullanılan parça: {$parcaOzeti}\n" : '')
            ."Toplam: ₺ ".number_format($servis->toplam_tutar, 2, ',', '.')."\n\n"
            ."Bizi tercih ettiğiniz için teşekkür ederiz.";
        $whatsappUrl = strlen($musteriTelefon) >= 10 ? 'https://wa.me/'.$musteriTelefon.'?text='.rawurlencode($whatsappMesaj) : null;
    @endphp
    <div class="servis-detay-hero mb-4">
        <div>
            <div class="eyebrow"><i class="bi bi-clipboard2-check"></i> SERVİS İŞ EMRİ</div>
            <h1>{{ $servis->servis_no }}</h1>
            <p>
                {{ $servis->arac?->plaka ?: 'Plaka bilgisi yok' }} ·
                {{ $servis->arac?->marka }} {{ $servis->arac?->model }} ·
                Kabul: {{ optional($servis->servis_tarihi)->format('d.m.Y H:i') ?? $servis->created_at->format('d.m.Y H:i') }}
            </p>
        </div>
        <span class="servis-durum servis-durum-{{ \Illuminate\Support\Str::slug($servis->durum) }}">{{ $servis->durum }}</span>
    </div>

    @include('components.servis-akisi', ['aktifAdim' => 4])

    <div class="servis-aksiyonlar mb-4">
        <a class="btn btn-primary" href="{{ route('servis.islem', $servis->id) }}"><i class="bi bi-tools"></i> Serviste Çalış</a>
        <a class="btn btn-outline-primary" href="{{ route('servisler.edit', $servis->id) }}"><i class="bi bi-pencil-square"></i> İş Emrini Düzenle</a>
        <a class="btn btn-outline-secondary" href="{{ route('araclar.show', $servis->arac_id) }}"><i class="bi bi-car-front"></i> Araç Kartı</a>
        @if($whatsappUrl)
            <a class="btn btn-whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> WhatsApp ile Servis Özeti</a>
        @else
            <span class="btn btn-light disabled" title="Müşteri telefon numarası kayıtlı değil"><i class="bi bi-whatsapp"></i> Telefon Numarası Yok</span>
        @endif
        <a class="btn btn-light" href="{{ route('servisler.index') }}"><i class="bi bi-arrow-left"></i> İş Emirlerine Dön</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <article class="card h-100">
                <div class="card-body p-4">
                    <div class="section-title"><i class="bi bi-car-front-fill"></i><h2>Araç ve Müşteri Bilgileri</h2></div>
                    <div class="row g-3 bilgi-grid">
                        <div class="col-sm-6"><span>Plaka</span><strong>{{ $servis->arac?->plaka ?: '-' }}</strong></div>
                        <div class="col-sm-6"><span>Araç</span><strong>{{ trim(($servis->arac?->marka ?? '').' '.($servis->arac?->model ?? '')) ?: '-' }}</strong></div>
                        <div class="col-sm-6"><span>Müşteri</span><strong>{{ $servis->musteri?->ad_soyad ?: '-' }}</strong></div>
                        <div class="col-sm-6"><span>Giriş Kilometresi</span><strong>{{ number_format($servis->giris_km ?? 0, 0, ',', '.') }} KM</strong></div>
                    </div>
                    <div class="servis-metin-havuzu mt-4">
                        <article class="servis-metin-kutusu">
                            <span><i class="bi bi-chat-left-text"></i> Müşteri Şikayeti</span>
                            <p>{{ $servis->sikayet ?: 'Şikayet kaydı bulunmuyor.' }}</p>
                        </article>
                        <article class="servis-metin-kutusu">
                            <span><i class="bi bi-wrench-adjustable"></i> Usta İlk Kontrolü</span>
                            <p>{{ $servis->usta_notu ?: 'Henüz ilk kontrol notu eklenmedi.' }}</p>
                        </article>
                    </div>
                </div>
            </article>
        </div>
        <div class="col-lg-5">
            <article class="card h-100">
                <div class="card-body p-4">
                    <div class="section-title"><i class="bi bi-receipt"></i><h2>Maliyet Özeti</h2></div>
                    <div class="maliyet-havuzu">
                        <div class="maliyet-satir"><span>İşçilik</span><strong>₺ {{ number_format($servis->iscilik_tutari, 2, ',', '.') }}</strong></div>
                        <div class="maliyet-satir"><span>Yedek parça</span><strong>₺ {{ number_format($servis->parca_tutari, 2, ',', '.') }}</strong></div>
                        <div class="maliyet-satir toplam"><span>Genel toplam</span><strong>₺ {{ number_format($servis->toplam_tutar, 2, ',', '.') }}</strong></div>
                    </div>
                </div>
            </article>
        </div>
        <div class="col-12">
            <article class="card">
                <div class="card-body p-4">
                    <div class="section-title"><i class="bi bi-list-check"></i><h2>Yapılan İşlemler</h2><span class="ms-auto text-muted small">{{ $servis->islemler->count() }} kayıt</span></div>
                    @forelse($servis->islemler as $islem)
                        <div class="islem-satir"><i class="bi bi-check-circle-fill"></i><div><strong>{{ $islem->islem_adi ?? 'Servis İşlemi' }}</strong><p>{{ $islem->aciklama ?: 'Açıklama eklenmemiş.' }}</p></div><b>₺ {{ number_format($islem->tutar, 2, ',', '.') }}</b><div class="islem-islemleri"><details><summary><i class="bi bi-pencil-square"></i> Düzenle</summary><form method="POST" action="{{ route('servis.islem.guncelle', [$servis, $islem]) }}">@csrf @method('PATCH')<input name="islem_adi" value="{{ $islem->islem_adi }}" required><input name="aciklama" value="{{ $islem->aciklama }}" placeholder="Açıklama"><input name="tutar" type="number" step="0.01" min="0" value="{{ $islem->tutar }}"><button type="submit">Kaydet</button></form></details><form method="POST" action="{{ route('servis.islem.sil', [$servis, $islem]) }}" onsubmit="return confirm('Bu işlem silinsin mi?')">@csrf @method('DELETE')<button class="islem-sil" type="submit"><i class="bi bi-trash3"></i> Sil</button></form></div></div>
                    @empty
                        <div class="bos-durum"><i class="bi bi-tools"></i><p>Henüz işlem kaydı yok. <a href="{{ route('servis.islem', $servis->id) }}">Servis ekranından işlem ekleyin.</a></p></div>
                    @endforelse
                </div>
            </article>
        </div>
    </div>
</section>

<style>
    .servis-detay-hero{display:flex;align-items:center;justify-content:space-between;gap:1.25rem;padding:1.7rem 1.9rem;border-radius:20px;background:linear-gradient(115deg,#102a56,#167c79);color:#fff;box-shadow:0 16px 32px rgba(14,42,86,.15)}
    .servis-detay-hero .eyebrow{font-size:.74rem;font-weight:800;letter-spacing:.09em;opacity:.78}.servis-detay-hero h1{font-size:1.7rem;margin:.35rem 0}.servis-detay-hero p{margin:0;opacity:.9}
    .servis-durum{padding:.55rem .9rem;border-radius:999px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.35);font-weight:800;white-space:nowrap}
    .servis-aksiyonlar{display:flex;flex-wrap:wrap;gap:.75rem;padding:1rem;border:1px solid #dce5f1;border-radius:16px;background:#fff}.servis-aksiyonlar .btn{min-height:44px;padding:.65rem 1rem;font-weight:700}.servis-aksiyonlar .btn i{margin-right:.38rem}.servis-aksiyonlar .btn-whatsapp{background:#25d366;border-color:#25d366;color:#fff}.servis-aksiyonlar .btn-whatsapp:hover{background:#1eb85a;border-color:#1eb85a;color:#fff}
    .section-title{display:flex;align-items:center;gap:.6rem;margin-bottom:1.25rem}.section-title>i{color:#167c79;font-size:1.15rem}.section-title h2{font-size:1.05rem;margin:0;font-weight:800;color:#142b4e}
    .bilgi-grid>div{display:flex;flex-direction:column;gap:.25rem;padding:.85rem;border-radius:12px;background:#f7faff}.bilgi-grid span,.servis-notu>span{font-size:.78rem;color:#687b99;font-weight:700}.bilgi-grid strong{color:#102a56}
    .servis-notu{border-top:1px solid #e8edf4;padding-top:1rem;margin-top:1rem}.servis-notu>span{display:block;margin-bottom:.35rem}.servis-notu p{margin:0;color:#334865;line-height:1.55}
    .maliyet-satir{display:flex;justify-content:space-between;padding:.9rem 0;border-bottom:1px solid #e9eef5;color:#5b6e89}.maliyet-satir strong{color:#142b4e}.maliyet-satir.toplam{margin-top:.4rem;border-bottom:0;color:#142b4e;font-size:1.06rem}.maliyet-satir.toplam strong{color:#087a70;font-size:1.15rem}
    .islem-satir{display:flex;gap:.75rem;padding:1rem 0;border-bottom:1px solid #e9eef5}.islem-satir>i{color:#15856d;font-size:1.1rem}.islem-satir strong{color:#142b4e}.islem-satir p{margin:.2rem 0 0;color:#687b99;font-size:.9rem}.bos-durum{padding:1.3rem;text-align:center;color:#687b99;background:#f7faff;border-radius:12px}.bos-durum i{font-size:1.5rem;color:#167c79}.bos-durum p{margin:.5rem 0 0}.bos-durum a{font-weight:700}
    .islem-islemleri{display:flex;align-items:center;gap:.45rem;margin-left:auto}.islem-islemleri details{position:relative}.islem-islemleri summary,.islem-islemleri .islem-sil{list-style:none;border:1px solid #c8d8ee;border-radius:8px;background:#fff;color:#1764c0;padding:.42rem .62rem;font-size:.78rem;font-weight:800;white-space:nowrap;cursor:pointer}.islem-islemleri summary::-webkit-details-marker{display:none}.islem-islemleri details form{position:absolute;right:0;top:calc(100% + .45rem);z-index:5;display:grid;gap:.5rem;width:270px;padding:.7rem;border:1px solid #cddbeb;border-radius:10px;background:#fff;box-shadow:0 12px 28px rgba(19,42,75,.16)}.islem-islemleri input{width:100%;padding:.5rem .6rem;border:1px solid #c9d8e9;border-radius:7px;font-size:.8rem}.islem-islemleri details button{border:0;border-radius:7px;padding:.5rem;background:#1764c0;color:#fff;font-weight:800}.islem-islemleri .islem-sil{border-color:#f3c5c9;color:#c62c3a;background:#fff6f7}.tema-koyu .islem-islemleri summary{background:#202f49;border-color:#41526e;color:#edf4ff}.tema-koyu .islem-islemleri details form{background:#17233a;border-color:#41526e}.tema-koyu .islem-islemleri input{background:#202f49;border-color:#41526e;color:#edf4ff}.tema-koyu .servis-aksiyonlar,.tema-koyu .servis-detay .card{background:#17233a;border-color:#31415b}.tema-koyu .section-title h2,.tema-koyu .bilgi-grid strong,.tema-koyu .maliyet-satir strong,.tema-koyu .islem-satir strong{color:#edf4ff}.tema-koyu .bilgi-grid>div,.tema-koyu .bos-durum{background:#202f49}.tema-koyu .servis-notu p,.tema-koyu .maliyet-satir,.tema-koyu .islem-satir p{color:#b7c5dc}.tema-koyu .servis-notu,.tema-koyu .maliyet-satir,.tema-koyu .islem-satir{border-color:#31415b}
    @media(max-width:640px){.servis-detay-hero{align-items:flex-start;flex-direction:column;padding:1.3rem}.servis-aksiyonlar .btn{width:100%;text-align:left}.islem-satir{flex-wrap:wrap}.islem-islemleri{width:100%;margin-left:0}.islem-islemleri details form{left:0;right:auto;width:min(270px,calc(100vw - 70px))}}
    .servis-detay .section-title{position:static!important;display:flex!important;align-items:center!important;min-height:28px!important;margin:0 0 1.1rem!important;line-height:1.2!important}.servis-detay .section-title h2{position:static!important;display:block!important;margin:0!important;line-height:1.25!important}.servis-metin-havuzu{display:grid;gap:12px}.servis-metin-kutusu{min-height:104px;padding:14px 15px;border:1px solid #dce6f1;border-radius:12px;background:#f8fbff}.servis-metin-kutusu span{display:block;margin:0 0 8px;color:#456789;font-size:.78rem;font-weight:800}.servis-metin-kutusu p{margin:0;color:#1f3857;line-height:1.55;overflow-wrap:anywhere}.maliyet-havuzu{display:grid;gap:0}.maliyet-havuzu .maliyet-satir{min-height:60px;align-items:center;margin:0}.tema-koyu .servis-metin-kutusu{background:#202f49;border-color:#31415b}.tema-koyu .servis-metin-kutusu p{color:#edf4ff}
</style>
@endsection
