@php
    $baglanti = $personelBaglantisi ?? null;
    $secilenFirmaId = old('firma_id', $baglanti?->firma_id);
    $secilenSubeId = old('sube_id', $baglanti?->sube_id);
    $subeVerisi = $firmalar->mapWithKeys(fn ($firma) => [$firma->id => $firma->subeler->map(fn ($sube) => ['id' => $sube->id, 'ad' => $sube->sube_adi])->values()]);
@endphp
<div class="row g-3">
    <div class="col-12">
        <div class="personel-baglanti-baslik">
            <div><i class="bi bi-diagram-3"></i> Çalışma Bağlantısı</div>
            <small>Personel, seçilen firma ve şubeye bağlı olarak çalışır.</small>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="firma_id">Firma</label>
        <select id="firma_id" name="firma_id" required>
            <option value="">Firma seçiniz</option>
            @foreach($firmalar as $firma)
                <option value="{{ $firma->id }}" @selected((string) $secilenFirmaId === (string) $firma->id)>{{ $firma->gosterim_adi }}</option>
            @endforeach
        </select>
        @error('firma_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="sube_id">Şube</label>
        <select id="sube_id" name="sube_id" required disabled><option value="">Önce firma seçiniz</option></select>
        <div class="form-text" id="sube-yardim">Tek şubeli firmalarda merkez şube otomatik seçilir.</div>
        @error('sube_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="col-12"><hr class="my-1"></div>
    <div class="col-md-6"><label class="form-label" for="name">Ad</label><input id="name" type="text" name="name" value="{{ old('name', $kullanici?->name) }}" required>@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label" for="surname">Soyad</label><input id="surname" type="text" name="surname" value="{{ old('surname', $kullanici?->surname) }}" required>@error('surname')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label" for="email">E-posta</label><input id="email" type="email" name="email" value="{{ old('email', $kullanici?->email) }}" required>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label" for="dogum_tarihi">Doğum Tarihi</label><input id="dogum_tarihi" type="date" name="dogum_tarihi" value="{{ old('dogum_tarihi', $kullanici?->dogum_tarihi?->format('Y-m-d')) }}">@error('dogum_tarihi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label" for="phone">Telefon</label><input id="phone" type="text" name="phone" value="{{ old('phone', $kullanici?->phone) }}"></div>
    <div class="col-md-6"><label class="form-label" for="tc_no">TC Kimlik No</label><input id="tc_no" type="text" name="tc_no" maxlength="11" value="{{ old('tc_no', $kullanici?->tc_no) }}"></div>
    <div class="col-md-6">
        <label class="form-label" for="role">Personel Rolü</label>
        <select id="role" name="role" required>
            <option value="">Rol seçiniz</option>
            @foreach($roller as $kod => $ad)
                @if(auth()->user()?->isIk() && in_array($kod, ['sistem_yoneticisi', 'admin'], true))
                    @continue
                @endif
                <option value="{{ $kod }}" @selected(old('role', $kullanici?->role) === $kod)>{{ $ad }}</option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label class="form-label" for="password">{{ $kullanici ? 'Yeni Şifre (değişmeyecekse boş bırakın)' : 'Şifre' }}</label><input id="password" type="password" name="password" {{ $kullanici ? '' : 'required' }}>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label" for="password_confirmation">Şifre Tekrar</label><input id="password_confirmation" type="password" name="password_confirmation" {{ $kullanici ? '' : 'required' }}></div>
    <div class="col-12"><div class="alert alert-info mb-0"><strong>Rol bilgisi:</strong> Kullanıcının menüsü, dashboard kısayolları ve sayfa erişimleri atanan role göre belirlenecek.</div></div>
</div>

<style>
    .personel-baglanti-baslik{padding:15px 18px;border:1px solid #dbe6f8;border-radius:12px;background:#f6f9ff;display:flex;justify-content:space-between;align-items:center;color:#112d57;font-weight:700}.personel-baglanti-baslik small{font-weight:500;color:#5c6f89}@media(max-width:700px){.personel-baglanti-baslik{display:block}.personel-baglanti-baslik small{display:block;margin-top:5px}}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const firmalar = @json($subeVerisi);
    const firma = document.getElementById('firma_id');
    const sube = document.getElementById('sube_id');
    const yardim = document.getElementById('sube-yardim');
    const seciliSube = @json((string) $secilenSubeId);

    const subeleriYukle = () => {
        const liste = firmalar[firma.value] || [];
        sube.innerHTML = '';
        sube.disabled = liste.length === 0;
        if (!firma.value) { sube.add(new Option('Önce firma seçiniz', '')); yardim.textContent = 'Tek şubeli firmalarda merkez şube otomatik seçilir.'; return; }
        if (!liste.length) { sube.add(new Option('Bu firmada aktif şube yok', '')); yardim.textContent = 'Önce firma kartından aktif bir şube ekleyin.'; return; }
        sube.add(new Option('Şube seçiniz', ''));
        liste.forEach(item => sube.add(new Option(item.ad, item.id)));
        if (liste.length === 1) { sube.value = String(liste[0].id); yardim.textContent = 'Tek aktif şube bulundu; merkez şube otomatik seçildi.'; }
        else { sube.value = seciliSube && liste.some(item => String(item.id) === seciliSube) ? seciliSube : ''; yardim.textContent = 'Bu firmada birden fazla şube var; çalışacağı şubeyi seçin.'; }
    };
    firma.addEventListener('change', subeleriYukle);
    subeleriYukle();
});
</script>
