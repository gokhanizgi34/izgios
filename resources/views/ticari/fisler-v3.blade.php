@extends('layouts.app')

@section('title', 'Muhasebe Fişleri')

@section('content')
<style>
    .fis { max-width: 1440px; margin: auto; }
    .fis-hero { padding: 25px; border-radius: 20px; background: linear-gradient(110deg, #102a50, #1d5a70); color: #fff; }
    .fis-hero p { margin: 7px 0 0; color: #d9eaff; }
    .fis-card { margin-top: 16px; padding: 17px; background: #fff; border: 1px solid #dce6ef; border-radius: 16px; }
    .fis-tools, .fis-actions { display: flex; gap: 11px; align-items: end; flex-wrap: wrap; }
    .fis-tools > div { min-width: 280px; }
    .fis-head-fields { display: grid; grid-template-columns: 1.3fr 150px 155px 1.2fr 1.3fr; gap: 11px; }
    .fis-head-fields label, .fis-detail-label { font-size: 12px; color: #3e5573; font-weight: 800; display: block; margin-bottom: 5px; }
    .fis-table { width: 100%; min-width: 780px; border-collapse: collapse; }
    .fis-table th { background: #eff5fa; color: #506681; padding: 11px; text-align: left; font-size: 12px; }
    .fis-table td { padding: 7px; border-bottom: 1px solid #e7edf4; }
    .fis-table input, .fis-table select { margin: 0; min-height: 38px; padding: 7px; }
    .fis-actions { justify-content: space-between; margin-top: 15px; }
    .fis-total { padding: 11px 13px; background: #fff7d9; border-radius: 10px; color: #11284d; font-weight: 800; }
    .fis-list { width: 100%; min-width: 760px; border-collapse: collapse; }
    .fis-list th { background: #eff5fa; padding: 11px; text-align: left; font-size: 12px; color: #506681; }
    .fis-list td { padding: 12px; border-bottom: 1px solid #e7edf4; vertical-align: middle; }
    .fis-detail-dialog { width: min(930px, calc(100% - 28px)); max-height: calc(100vh - 36px); border: 0; border-radius: 20px; padding: 0; box-shadow: 0 26px 70px rgba(11, 35, 71, .28); }
    .fis-detail-dialog::backdrop { background: rgba(8, 28, 55, .55); backdrop-filter: blur(2px); }
    .fis-detail-content { padding: 22px; overflow: auto; max-height: calc(100vh - 36px); }
    .fis-detail-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; padding-bottom: 17px; margin-bottom: 17px; border-bottom: 1px solid #e4ebf3; }
    .fis-detail-heading h3 { margin: 0; color: #102a50; font-size: 20px; }
    .fis-detail-heading p { margin: 4px 0 0; color: #64748b; font-size: 13px; }
    .fis-detail-meta { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 18px; }
    .fis-detail-meta .wide { grid-column: span 2; }
    .fis-detail-meta input, .fis-detail-lines input { background: #f7fafc; border-color: #d9e3ef; color: #183557; font-weight: 650; }
    .fis-detail-lines { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .fis-detail-lines th { padding: 0 7px 5px; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: .02em; }
    .fis-detail-lines td { padding: 0 5px; }
    .fis-detail-lines input { margin: 0; min-height: 39px; padding: 8px; }
    .fis-detail-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 16px; margin-top: 12px; border-top: 1px solid #e4ebf3; }
    @media (max-width: 1000px) { .fis-head-fields { grid-template-columns: repeat(3, 1fr); } .fis-detail-meta { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 650px) { .fis-hero { padding: 20px; } .fis-tools > div { width: 100%; } .fis-head-fields { grid-template-columns: 1fr 1fr; } .fis-actions { align-items: stretch; flex-direction: column; } .fis-actions .btn { width: 100%; } .fis-detail-content { padding: 17px; } .fis-detail-meta { grid-template-columns: 1fr; } .fis-detail-meta .wide { grid-column: auto; } .fis-detail-lines { min-width: 620px; } }
    @media (max-width: 420px) { .fis-head-fields { grid-template-columns: 1fr; } }
</style>

<section class="fis">
    <header class="fis-hero">
        <div class="small fw-bold text-warning">GİDER KAYIT YÖNETİMİ</div>
        <h1 class="h3 mb-0">Muhasebe Fişleri</h1>
        <p>Harcama fişini KDV dahil tutarla işleyin. Sistem, KDV hariç tutarı ve KDV tutarını otomatik ayırır; fiş satırları gider olarak kaydedilir.</p>
        @include('components.muhasebe-menu', ['firmaId' => $firmaId])
    </header>

    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
    @endif

    <article class="fis-card">
        <form method="GET" class="fis-tools">
            <div>
                <label class="form-label">Firma</label>
                <select name="firma_id" onchange="this.form.submit()">
                    @foreach ($firmalar as $firma)
                        <option value="{{ $firma->id }}" @selected($firmaId == $firma->id)>{{ $firma->gosterim_adi }}</option>
                    @endforeach
                </select>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('ticari.entegrasyonlar', ['firma_id' => $firmaId]) }}"><i class="bi bi-plug"></i> Entegrasyon Ayarları</a>
            <a class="btn btn-outline-secondary" href="{{ route('ticari.index', ['firma_id' => $firmaId]) }}">Muhasebe Merkezi</a>
        </form>
    </article>

    <article class="fis-card">
        <h2 class="h5">Yeni gider fişi</h2>
        <form method="POST" action="{{ route('ticari.fisler.kaydet') }}">
            @csrf
            <input type="hidden" name="firma_id" value="{{ $firmaId }}">
            <div class="fis-head-fields mb-3">
                <div><label>Firma adı</label><input value="{{ $firmalar->firstWhere('id', $firmaId)?->gosterim_adi }}" disabled></div>
                <div><label>Fiş no</label><input name="fis_no" placeholder="Otomatik"></div>
                <div><label>Fiş tarihi</label><input type="date" name="fis_tarihi" value="{{ now()->format('Y-m-d') }}" required></div>
                <div><label>Firma cari hesabı</label><select name="cari_hesap_id"><option value="">Cari seçilmedi</option>@foreach ($cariler as $cari)<option value="{{ $cari->id }}">{{ $cari->unvan }}</option>@endforeach</select></div>
                <div><label>Açıklama</label><input name="aciklama" placeholder="Örn. aylık kira gideri"></div>
            </div>
            <p class="small text-muted mb-3">Adet bilgi amaçlıdır; tutar, girilen <strong>KDV dahil toplam tutar</strong> üzerinden kaydedilir, adet × fiyat hesabı yapılmaz.</p>
            <div class="table-responsive">
                <table class="fis-table">
                    <thead><tr><th>Ürün / gider adı</th><th>Adet</th><th>Birim</th><th>Birim fiyat</th><th>KDV oranı</th><th>KDV dahil tutar</th><th>KDV hariç</th><th>KDV</th><th></th></tr></thead>
                    <tbody id="fis-satirlar">
                        <tr class="fis-satir">
                            <td><input name="satirlar[0][urun_adi]" required placeholder="Gider kalemi"></td>
                            <td><input type="number" step=".001" min=".001" value="1" name="satirlar[0][adet]"></td>
                            <td>
                                <select name="satirlar[0][birim]" class="birim-turu" aria-label="Birim seçin">
                                    <option value="Adet" selected>Adet</option>
                                    <option value="Litre">Litre</option>
                                    <option value="Kilogram">Kilogram</option>
                                    <option value="Gram">Gram</option>
                                    <option value="Metre">Metre</option>
                                    <option value="Paket">Paket</option>
                                    <option value="Kutu">Kutu</option>
                                    <option value="Çuval">Çuval</option>
                                    <option value="Takım">Takım</option>
                                    <option value="Hizmet">Hizmet</option>
                                    <option value="Saat">Saat</option>
                                    <option value="Gün">Gün</option>
                                    <option value="Ay">Ay</option>
                                </select>
                            </td>
                            <td><input class="birim" type="number" step=".01" min="0.01" name="satirlar[0][birim_fiyat]" required></td>
                            <td><select class="oran" name="satirlar[0][kdv_orani]"><option value="1">%1</option><option value="5">%5</option><option value="8">%8</option><option value="10">%10</option><option value="20" selected>%20</option></select></td>
                            <td><input class="dahil" type="number" step=".01" min="0.01" name="satirlar[0][kdv_dahil_tutar]" required></td>
                            <td class="haric">₺0,00</td><td class="kdv">₺0,00</td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger sil" aria-label="Satırı sil"><i class="bi bi-trash3"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="fis-actions">
                <button class="btn btn-outline-secondary" id="satir-ekle" type="button"><i class="bi bi-plus-lg"></i> Satır Ekle</button>
                <div class="fis-total">KDV dahil toplam: <strong id="fis-toplam">₺0,00</strong></div>
                <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Fişi Kaydet</button>
            </div>
        </form>
    </article>

    <article class="fis-card">
        <div class="d-flex justify-content-between">
            <h2 class="h5">Fiş tablosu</h2>
            <span class="small text-muted">{{ $fisler->count() }} fiş</span>
        </div>
        <div class="table-responsive mt-3">
            <table class="fis-list">
                <thead><tr><th>Fiş no</th><th>Tarih</th><th>Firma cari / açıklama</th><th>Satır</th><th class="text-end">KDV dahil tutar</th><th></th></tr></thead>
                <tbody>
                    @forelse ($fisler as $fis)
                        <tr>
                            <td><strong>{{ $fis->fis_no }}</strong><br><small>{{ $fis->tip }}</small></td>
                            <td>{{ \Carbon\Carbon::parse($fis->fis_tarihi)->format('d.m.Y') }}</td>
                            <td>{{ $fis->cari_unvan ?: ($fis->aciklama ?: '—') }}</td>
                            <td><button type="button" class="btn btn-sm btn-outline-primary" onclick="openFisDetay('fis-detay-{{ $fis->id }}')">Detay ({{ ($fisSatirlari[$fis->id] ?? collect())->count() }})</button></td>
                            <td class="text-end fw-bold">₺{{ number_format($fis->tutar, 2, ',', '.') }}</td>
                            <td><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('ciktilar.yazdir', ['tur' => 'fis', 'id' => $fis->id]) }}">Yazdır / Gönder</a></td>
                        </tr>

                        <tr class="fis-dialog-row"><td colspan="6"><dialog id="fis-detay-{{ $fis->id }}" class="fis-detail-dialog">
                            <div class="fis-detail-content">
                                <div class="fis-detail-heading">
                                    <div><h3>Fiş detayları</h3><p>Fiş bilgileri ve satırlar okunabilir metin kutularında gösterilir.</p></div>
                                    <form method="dialog"><button class="btn btn-sm btn-outline-secondary" aria-label="Detay penceresini kapat"><i class="bi bi-x-lg"></i></button></form>
                                </div>
                                <div class="fis-detail-meta">
                                    <div><span class="fis-detail-label">Fiş no</span><input readonly value="{{ $fis->fis_no }}"></div>
                                    <div><span class="fis-detail-label">Fiş tarihi</span><input readonly value="{{ \Carbon\Carbon::parse($fis->fis_tarihi)->format('d.m.Y') }}"></div>
                                    <div class="wide"><span class="fis-detail-label">Firma cari hesabı</span><input readonly value="{{ $fis->cari_unvan ?: 'Cari seçilmedi' }}"></div>
                                    <div class="wide"><span class="fis-detail-label">Açıklama</span><input readonly value="{{ $fis->aciklama ?: 'Açıklama girilmedi' }}"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="fis-detail-lines">
                                        <thead><tr><th>Ürün / gider adı</th><th>Adet</th><th>Birim</th><th>KDV oranı</th><th>KDV dahil tutar</th></tr></thead>
                                        <tbody>
                                            @foreach (($fisSatirlari[$fis->id] ?? collect()) as $satir)
                                                <tr>
                                                    <td><input readonly value="{{ $satir->urun_adi }}"></td>
                                                    <td><input readonly value="{{ rtrim(rtrim(number_format($satir->adet, 3, ',', '.'), '0'), ',') }}"></td>
                                                    <td><input readonly value="{{ $satir->birim }}"></td>
                                                    <td><input readonly value="%{{ $satir->kdv_orani }}"></td>
                                                    <td><input readonly value="₺{{ number_format($satir->kdv_dahil_tutar, 2, ',', '.') }}"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="fis-detail-footer">
                                    <a class="btn btn-primary" target="_blank" href="{{ route('ciktilar.yazdir', ['tur' => 'fis', 'id' => $fis->id]) }}"><i class="bi bi-printer"></i> Fişi Yazdır / Gönder</a>
                                    <form method="dialog"><button class="btn btn-outline-secondary">Kapat</button></form>
                                </div>
                            </div>
                        </dialog></td></tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Henüz gider fişi yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

<script>
    function openFisDetay(id) {
        const dialog = document.getElementById(id);
        if (dialog && typeof dialog.showModal === 'function') {
            dialog.showModal();
        } else if (dialog) {
            dialog.setAttribute('open', 'open');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const body = document.getElementById('fis-satirlar');
        const total = document.getElementById('fis-toplam');
        let index = 1;
        const money = value => '₺' + Number(value || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function calculate() {
            let sum = 0;
            body.querySelectorAll('.fis-satir').forEach(row => {
                const includingVat = Number(row.querySelector('.dahil').value) || 0;
                const rate = Number(row.querySelector('.oran').value) || 0;
                const excludingVat = includingVat / (1 + rate / 100);
                row.querySelector('.haric').textContent = money(excludingVat);
                row.querySelector('.kdv').textContent = money(includingVat - excludingVat);
                sum += includingVat;
            });
            total.textContent = money(sum);
        }

        function bindRow(row) {
            row.querySelectorAll('.dahil, .oran').forEach(input => input.addEventListener('input', calculate));
            row.querySelector('.sil').addEventListener('click', () => {
                if (body.children.length > 1) {
                    row.remove();
                    calculate();
                }
            });
        }

        bindRow(body.firstElementChild);
        document.getElementById('satir-ekle').addEventListener('click', () => {
            const row = body.firstElementChild.cloneNode(true);
            row.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
                if (input.tagName === 'SELECT') {
                    input.value = input.classList.contains('oran') ? '20' : 'Adet';
                } else {
                    input.value = input.type === 'number' && input.name.includes('[adet]') ? '1' : '';
                }
            });
            row.querySelector('.haric').textContent = '₺0,00';
            row.querySelector('.kdv').textContent = '₺0,00';
            body.appendChild(row);
            bindRow(row);
            index++;
        });
    });
</script>
@endsection
