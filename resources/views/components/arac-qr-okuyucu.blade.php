@props(['alanId' => 'aracQr', 'zorunlu' => false, 'mevcutToken' => null])

<section class="servis-sayfa-kart mt-3 arac-qr-okuyucu" data-qr-okuyucu="{{ $alanId }}">
    <div class="kart-baslik">
        <div>
            <h2><i class="bi bi-qr-code-scan"></i> Fiziksel araç QR etiketi</h2>
            <p>Önceden bastırılmış İZGİOS QR etiketini kameraya gösterin veya fotoğrafını seçin.</p>
        </div>
    </div>

    @if($mevcutToken)
        <div class="alert alert-success mb-3"><strong>Atanmış QR mevcut.</strong> Etiket değişecekse aşağıdan yeni, boş bir QR okutun.</div>
    @endif

    <input type="hidden" name="qr_havuz_token" id="{{ $alanId }}Token" value="{{ old('qr_havuz_token') }}" @required($zorunlu)>
    <input type="file" id="{{ $alanId }}Kamera" accept="image/*" capture="environment" hidden>
    <input type="file" id="{{ $alanId }}Galeri" accept="image/*" hidden>

    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary" data-qr-kamera><i class="bi bi-camera-fill"></i> Kamerayla QR okut</button>
        <button type="button" class="btn btn-outline-primary" data-qr-galeri><i class="bi bi-image-fill"></i> Galeriden QR seç</button>
    </div>
    <div class="mt-3 p-3 rounded border" data-qr-durum role="status" aria-live="polite">
        {{ old('qr_havuz_token') ? 'QR okundu ve kayda hazır.' : ($mevcutToken ? 'Yeni QR okutulmazsa mevcut QR korunur.' : 'Henüz QR okutulmadı.') }}
    </div>
    @error('qr_havuz_token')<div class="text-danger mt-2">{{ $message }}</div>@enderror

    <div data-qr-kamera-modal role="dialog" aria-modal="true" aria-label="Araç QR okuyucu" hidden style="position:fixed;inset:0;z-index:1090;background:rgba(4,13,30,.92);padding:18px;overflow-y:auto;align-items:center;justify-content:center;">
        <div style="width:min(560px,100%);max-height:95dvh;overflow-y:auto;background:#fff;border-radius:18px;padding:16px;box-shadow:0 24px 70px rgba(0,0,0,.35)">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <div><strong>QR kodu kameraya gösterin</strong><div class="text-secondary small">Etiketi çerçevenin ortasında sabit tutun.</div></div>
                <button type="button" class="btn btn-outline-secondary" data-qr-kapat aria-label="Kamerayı kapat"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="position:relative;overflow:hidden;border-radius:14px;background:#071327;aspect-ratio:1/1">
                <video data-qr-video playsinline muted style="width:100%;height:100%;object-fit:contain"></video>
                <div style="position:absolute;inset:12%;border:4px solid #e3b82e;border-radius:18px;box-shadow:0 0 0 999px rgba(0,0,0,.22)"></div>
            </div>
            <label class="mt-2" style="width:100%" hidden>Kamera<select data-qr-cihaz class="form-select"></select></label><label class="mt-2" style="width:100%" hidden>Yakınlaştırma<input data-qr-zoom type="range" class="form-range" aria-label="Kamera yakınlaştırma"></label><div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary flex-grow-1" data-qr-fener hidden><i class="bi bi-lightbulb"></i> Fener</button>
                <button type="button" class="btn btn-outline-secondary flex-grow-1" data-qr-fotograf><i class="bi bi-camera"></i> Fotoğraf çekerek dene</button>
            </div>
            <div class="small text-secondary mt-2" data-qr-kamera-durum>Kamera hazırlanıyor…</div>
        </div>
    </div>
</section>
