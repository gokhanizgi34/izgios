@extends('layouts.app')

@section('title', $modul === 'randevu' ? 'Randevu ve Ajanda' : 'Sigorta / Kasko')

@section('content')
<style>
    .op-center { max-width: 1380px; margin: 0 auto; }
    .op-hero { padding: 28px 32px; border-radius: 24px; color: #fff; background: linear-gradient(115deg, #112a51, #137f74); box-shadow: 0 18px 40px rgba(15, 42, 81, .14); }
    .op-hero h1 { font-size: clamp(1.35rem, 2.2vw, 1.9rem); margin: 0 0 7px; font-weight: 800; }
    .op-hero p { margin: 0; color: rgba(255,255,255,.84); }
    .op-grid { display: grid; grid-template-columns: minmax(300px, .85fr) minmax(0, 1.35fr); gap: 22px; margin-top: 22px; align-items: start; }
    .op-card { overflow: hidden; background: var(--bs-body-bg, #fff); border: 1px solid #dbe5f1; border-radius: 22px; box-shadow: 0 12px 32px rgba(16,42,81,.07); }
    .op-card-head { padding: 19px 23px; border-bottom: 1px solid #e8eef6; background: linear-gradient(100deg, #f7faff, #fffdf5); }
    .op-card-head h2 { margin: 0; color: #102a50; font-size: 1.08rem; font-weight: 800; }
    .op-card-head p { margin: 4px 0 0; color: #68809f; font-size: .9rem; }
    .op-card-body { padding: 22px; }
    .op-form .form-label { display: block; margin: 0 0 7px; font-size: .86rem; color: #17345d; font-weight: 750; }
    .op-form .form-control, .op-form .form-select { min-height: 46px; border: 1px solid #cbd9e9; border-radius: 12px; color: #102a50; background: var(--bs-body-bg, #fff); box-shadow: none; }
    .op-form textarea.form-control { min-height: 95px; padding-top: 12px; resize: vertical; }
    .op-form .form-control:focus, .op-form .form-select:focus { border-color: #e5bb2c; box-shadow: 0 0 0 .22rem rgba(229,187,44,.18); }
    .op-form .field-gap { margin-bottom: 16px; }
    .op-form .btn-submit { width: 100%; min-height: 47px; border: 0; border-radius: 12px; color: #102a50; font-weight: 800; background: #e5bb2c; }
    .appointment-list { display: grid; gap: 12px; }
    .appointment-row { display: grid; grid-template-columns: 88px minmax(135px, 1.1fr) minmax(130px, 1fr) auto auto; gap: 16px; align-items: center; padding: 14px; border: 1px solid #dce6f1; border-radius: 16px; background: var(--bs-body-bg, #fff); }
    .appointment-date { min-height: 67px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 13px; color: #102a50; background: #fff8dd; text-align: center; }
    .appointment-date strong { font-size: 1.08rem; line-height: 1.1; }
    .appointment-date span { margin-top: 4px; font-size: .72rem; font-weight: 700; }
    .appointment-person strong, .appointment-service strong { display: block; color: #102a50; font-size: .94rem; }
    .appointment-person span, .appointment-service span { display: block; margin-top: 4px; color: #6a7f9d; font-size: .8rem; }
    .appointment-status { white-space: nowrap; padding: 7px 10px; border-radius: 999px; color: #265b42; background: #e5f7eb; font-weight: 800; font-size: .78rem; text-transform: capitalize; }
    .appointment-status[data-status="iptal"] { color: #9d3333; background: #ffe7e7; }
    .appointment-status[data-status="planlandi"] { color: #235c9e; background: #e8f1ff; }
    .appointment-status[data-status="teyitli"] { color: #7b5612; background: #fff4d0; }
    .appointment-empty { padding: 40px 20px; border: 1px dashed #c8d6e7; border-radius: 16px; color: #6a7f9d; text-align: center; }
    .appointment-actions{display:flex;gap:6px;flex-wrap:wrap}.appointment-actions details{position:relative}.appointment-actions summary,.appointment-actions button{list-style:none;border:1px solid #cbd9ea;border-radius:8px;background:#fff;color:#185fae;padding:7px 9px;font-size:.75rem;font-weight:800;cursor:pointer}.appointment-actions summary::-webkit-details-marker{display:none}.appointment-actions>form button{border-color:#f0c6ca;color:#bf2f3c;background:#fff7f8}.appointment-row:has(.appointment-actions details[open]) .appointment-actions{grid-column:1/-1;width:100%}.appointment-actions details[open]{flex:1 0 100%}.appointment-edit{position:static;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;width:100%;margin-top:9px;padding:14px;border:1px solid #cfdaea;border-radius:12px;background:#f8fbff;box-shadow:none}.appointment-edit label{font-size:.72rem;font-weight:800;color:#385675}.appointment-edit input,.appointment-edit select,.appointment-edit textarea{width:100%;margin-top:4px;padding:9px;border:1px solid #cbd9e9;border-radius:7px}.appointment-edit label:last-of-type,.appointment-edit button{grid-column:1/-1}.appointment-edit button{min-height:40px;background:#185fae;color:#fff}
    @media (max-width: 990px) { .op-grid { grid-template-columns: 1fr; } }
    @media (max-width: 650px) { .op-hero { padding: 22px 19px; border-radius: 18px; } .op-card-body { padding: 16px; } .appointment-row { grid-template-columns: 72px 1fr; gap: 12px; } .appointment-service { grid-column: 2; } .appointment-status { grid-column: 2; justify-self: start; }.appointment-actions{grid-column:1/-1}.appointment-edit{grid-template-columns:1fr} }
    body.dark-theme .op-card, body.dark-mode .op-card { border-color: #314158; }
    body.dark-theme .op-card-head, body.dark-mode .op-card-head { background: #17243a; border-color: #314158; }
    body.dark-theme .op-card-head h2, body.dark-mode .op-card-head h2, body.dark-theme .appointment-person strong, body.dark-mode .appointment-person strong, body.dark-theme .appointment-service strong, body.dark-mode .appointment-service strong { color: #f3f7ff; }
</style>

<section class="container op-center">
    <header class="op-hero">
        <h1>
            @if($modul === 'randevu') <i class="bi bi-calendar-week"></i> Randevu ve Servis Ajandası
            @else <i class="bi bi-shield-check"></i> Sigorta / Kasko Hasar Takibi
            @endif
        </h1>
        <p>Firma ve şube kapsamlı kayıtlar; ilgili müşteri, araç ve operasyon süreciyle birlikte izlenir.</p>
    </header>

    @if(session('success')) <div class="alert alert-success mt-3">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger mt-3">{{ $errors->first() }}</div> @endif

    <div class="op-grid">
        <article class="op-card">
            <div class="op-card-head">
                <h2>{{ $modul === 'randevu' ? 'Yeni randevu' : 'Yeni hasar dosyası' }}</h2>
                <p>@if($modul === 'randevu') Müşteri ve aracı seçip ajandaya ekleyin. @else Kayıt bilgilerini tamamlayın. @endif</p>
            </div>
            <div class="op-card-body op-form">
                @if($modul === 'randevu')
                    <form method="post" action="{{ route('operasyon.randevular.kaydet') }}">
                        @csrf
                        <div class="field-gap">
                            <label class="form-label" for="musteri_id">Müşteri</label>
                            <select id="musteri_id" name="musteri_id" class="form-select"><option value="">Müşteri seçiniz</option>@foreach($musteriler as $m)<option value="{{ $m->id }}">{{ $m->ad_soyad }}</option>@endforeach</select>
                        </div>
                        <div class="field-gap">
                            <label class="form-label" for="arac_id">Araç</label>
                            <select id="arac_id" name="arac_id" class="form-select"><option value="">Araç seçiniz</option>@foreach($araclar as $a)<option value="{{ $a->id }}">{{ $a->plaka }} · {{ $a->marka }} {{ $a->model }}</option>@endforeach</select>
                        </div>
                        <div class="field-gap">
                            <label class="form-label" for="hizmet_turu">Hizmet türü</label>
                            <select id="hizmet_turu" name="hizmet_turu" class="form-select" required><option>Periyodik Bakım</option><option>Yağ Değişimi</option><option>Mekanik Onarım</option><option>Elektrik ve Elektronik</option><option>Kaporta / Boya</option><option>Lastik / Rot Balans</option></select>
                        </div>
                        <div class="field-gap">
                            <label class="form-label" for="baslangic">Randevu tarihi ve saati</label>
                            <input id="baslangic" class="form-control" type="datetime-local" name="baslangic" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
                        </div>
                        <div class="field-gap">
                            <label class="form-label" for="durum">Durum</label>
                            <select id="durum" name="durum" class="form-select"><option value="planlandi">Planlandı</option><option value="teyitli">Teyitli</option><option value="iptal">İptal</option><option value="tamamlandi">Tamamlandı</option></select>
                        </div>
                        <div class="field-gap">
                            <label class="form-label" for="notlar">Randevu notu</label>
                            <textarea id="notlar" name="notlar" class="form-control" placeholder="Talep, servis notu veya müşteriye iletilecek bilgiyi yazın."></textarea>
                        </div>
                        <button class="btn-submit" type="submit"><i class="bi bi-calendar-plus"></i> Ajandaya Ekle</button>
                    </form>
                @elseif($modul === 'sigorta')
                    <form method="post" action="{{ route('operasyon.sigorta.kaydet') }}">@csrf
                        <div class="field-gap"><label class="form-label">Hasar dosya no</label><input class="form-control" name="dosya_no" required></div>
                        <div class="field-gap"><label class="form-label">Sigorta firması</label><select class="form-select" name="sigorta_firmasi_id"><option value="">Seçiniz</option>@foreach($sigortalar as $s)<option value="{{ $s->id }}">{{ $s->unvan }}</option>@endforeach</select></div>
                        <div class="field-gap"><label class="form-label">Durum</label><select class="form-select" name="durum"><option value="acik">Açık</option><option value="ekspertiz">Ekspertiz</option><option value="onaylandi">Onaylandı</option><option value="odendi">Ödendi</option><option value="kapandi">Kapandı</option></select></div>
                        <div class="field-gap"><label class="form-label">Onaylı tutar</label><input class="form-control" type="number" step=".01" name="onayli_tutar" value="0"></div>
                        <div class="field-gap"><label class="form-label">Açıklama</label><textarea class="form-control" name="aciklama"></textarea></div>
                        <button class="btn-submit" type="submit">Hasar Dosyası Aç</button>
                    </form>
                @endif
            </div>
        </article>

        <article class="op-card">
            <div class="op-card-head"><h2>Kayıtlar</h2><p>Güncel operasyon kayıtlarını buradan takip edin.</p></div>
            <div class="op-card-body">
                @if($modul === 'randevu')
                    <div class="appointment-list">
                        @forelse($kayitlar as $k)
                            <article class="appointment-row">
                                <div class="appointment-date"><strong>{{ \Carbon\Carbon::parse($k->baslangic)->format('d.m') }}</strong><span>{{ \Carbon\Carbon::parse($k->baslangic)->format('H:i') }}</span></div>
                                <div class="appointment-person"><strong>{{ $k->ad_soyad ?: 'Müşteri seçilmedi' }}</strong><span><i class="bi bi-car-front"></i> {{ $k->plaka ?: 'Araç seçilmedi' }}</span></div>
                                <div class="appointment-service"><strong>{{ $k->hizmet_turu }}</strong><span>{{ $k->notlar ?: 'Ek randevu notu yok.' }}</span></div>
                                <span class="appointment-status" data-status="{{ $k->durum }}">{{ str_replace('_', ' ', $k->durum) }}</span>
                                <div class="appointment-actions"><details><summary><i class="bi bi-pencil-square"></i> Düzenle</summary><form class="appointment-edit" method="POST" action="{{ route('operasyon.randevular.guncelle',$k->id) }}">@csrf @method('PATCH')<label>Müşteri<select name="musteri_id"><option value="">Seçimsiz</option>@foreach($musteriler as $m)<option value="{{ $m->id }}" @selected((int)$k->musteri_id===$m->id)>{{ $m->ad_soyad }}</option>@endforeach</select></label><label>Araç<select name="arac_id"><option value="">Seçimsiz</option>@foreach($araclar as $a)<option value="{{ $a->id }}" @selected((int)$k->arac_id===$a->id)>{{ $a->plaka }}</option>@endforeach</select></label><label>Hizmet<input name="hizmet_turu" value="{{ $k->hizmet_turu }}" required></label><label>Tarih ve saat<input type="datetime-local" name="baslangic" value="{{ \Carbon\Carbon::parse($k->baslangic)->format('Y-m-d\TH:i') }}" required></label><label>Durum<select name="durum">@foreach(['planlandi'=>'Planlandı','teyitli'=>'Teyitli','iptal'=>'İptal','tamamlandi'=>'Tamamlandı'] as $deger=>$metin)<option value="{{ $deger }}" @selected($k->durum===$deger)>{{ $metin }}</option>@endforeach</select></label><label>Not<textarea name="notlar">{{ $k->notlar }}</textarea></label><button type="submit">Değişiklikleri Kaydet</button></form></details><form method="POST" action="{{ route('operasyon.randevular.sil',$k->id) }}" onsubmit="return confirm('Bu randevu ve bildirim planları silinsin mi?')">@csrf @method('DELETE')<button type="submit"><i class="bi bi-trash3"></i> Sil</button></form></div>
                            </article>
                        @empty
                            <div class="appointment-empty"><i class="bi bi-calendar-x d-block fs-3 mb-2"></i>Henüz randevu kaydı yok.</div>
                        @endforelse
                    </div>
                @else
                    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Dosya</th><th>Sigorta / Araç</th><th>Tutar</th><th>Durum</th></tr></thead><tbody>@forelse($kayitlar as $k)<tr><td>{{ $k->dosya_no }}</td><td>{{ $k->sigorta_unvan ?: 'Firma seçilmedi' }}<small class="d-block">{{ $k->plaka ?: '-' }}</small></td><td>₺{{ number_format($k->onayli_tutar, 2, ',', '.') }}</td><td>{{ $k->durum }}</td></tr>@empty<tr><td colspan="4" class="text-muted">Henüz kayıt yok.</td></tr>@endforelse</tbody></table></div>
                @endif
            </div>
        </article>
    </div>
</section>
@endsection
