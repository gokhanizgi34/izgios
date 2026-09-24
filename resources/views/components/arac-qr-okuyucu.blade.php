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
</section>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-qr-okuyucu]').forEach((kutu) => {
        const id = kutu.dataset.qrOkuyucu;
        const tokenAlani = document.getElementById(id + 'Token');
        const kamera = document.getElementById(id + 'Kamera');
        const galeri = document.getElementById(id + 'Galeri');
        const durum = kutu.querySelector('[data-qr-durum]');
        kutu.querySelector('[data-qr-kamera]').addEventListener('click', () => kamera.click());
        kutu.querySelector('[data-qr-galeri]').addEventListener('click', () => galeri.click());

        async function oku(dosya) {
            if (!dosya) return;
            durum.textContent = 'QR kod okunuyor…';
            const bitmap = await createImageBitmap(dosya);
            try {
                let ham = '';
                if ('BarcodeDetector' in window) {
                    const detector = new BarcodeDetector({formats: ['qr_code']});
                    const kodlar = await detector.detect(bitmap).catch(() => []);
                    ham = kodlar[0]?.rawValue || '';
                }
                if (!ham) {
                    const canvas = document.createElement('canvas');
                    canvas.width = bitmap.width; canvas.height = bitmap.height;
                    const ctx = canvas.getContext('2d', {willReadFrequently: true});
                    ctx.drawImage(bitmap, 0, 0);
                    const image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const {default: jsQR} = await import('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/+esm');
                    ham = jsQR(image.data, image.width, image.height, {inversionAttempts: 'attemptBoth'})?.data || '';
                }
                const eslesme = ham.match(/\/arac\/([0-9a-f-]{36})(?:[/?#]|$)/i);
                const token = eslesme?.[1] || (/^[0-9a-f-]{36}$/i.test(ham.trim()) ? ham.trim() : '');
                if (!token) throw new Error('Bu görüntüde geçerli bir İZGİOS araç QR kodu bulunamadı.');
                tokenAlani.value = token;
                durum.innerHTML = '<strong class="text-success">QR başarıyla okundu.</strong> Araç kaydedildiğinde havuzdan bu araca atanacak.';
            } catch (hata) {
                tokenAlani.value = '';
                durum.innerHTML = '<strong class="text-danger">' + hata.message + '</strong>';
            } finally {
                bitmap.close?.();
            }
        }
        kamera.addEventListener('change', () => oku(kamera.files[0]));
        galeri.addEventListener('change', () => oku(galeri.files[0]));
    });
});
</script>
@endpush
@endonce
