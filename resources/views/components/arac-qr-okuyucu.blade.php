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

    <div data-qr-kamera-modal hidden style="position:fixed;inset:0;z-index:1090;background:rgba(4,13,30,.92);padding:18px;align-items:center;justify-content:center;">
        <div style="width:min(560px,100%);background:#fff;border-radius:18px;padding:16px;box-shadow:0 24px 70px rgba(0,0,0,.35)">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <div><strong>QR kodu kameraya gösterin</strong><div class="text-secondary small">Etiketi çerçevenin ortasında sabit tutun.</div></div>
                <button type="button" class="btn btn-outline-secondary" data-qr-kapat aria-label="Kamerayı kapat"><i class="bi bi-x-lg"></i></button>
            </div>
            <div style="position:relative;overflow:hidden;border-radius:14px;background:#071327;aspect-ratio:1/1">
                <video data-qr-video playsinline muted style="width:100%;height:100%;object-fit:cover"></video>
                <div style="position:absolute;inset:12%;border:4px solid #e3b82e;border-radius:18px;box-shadow:0 0 0 999px rgba(0,0,0,.22)"></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary flex-grow-1" data-qr-fener hidden><i class="bi bi-lightbulb"></i> Fener</button>
                <button type="button" class="btn btn-outline-secondary flex-grow-1" data-qr-fotograf><i class="bi bi-camera"></i> Fotoğraf çekerek dene</button>
            </div>
            <div class="small text-secondary mt-2" data-qr-kamera-durum>Kamera hazırlanıyor…</div>
        </div>
    </div>
</section>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let jsQrModulu;
    async function jsQrYukle() {
        if (!jsQrModulu) {
            jsQrModulu = (async () => {
                for (const adres of ['https://cdn.jsdelivr.net/npm/jsqr@1.4.0/+esm', 'https://esm.sh/jsqr@1.4.0']) {
                    try { return (await import(adres)).default; } catch (_) {}
                }
                throw new Error('QR okuma modülü yüklenemedi. İnternet bağlantısını kontrol edin.');
            })();
        }
        return jsQrModulu;
    }

    document.querySelectorAll('[data-qr-okuyucu]').forEach((kutu) => {
        const id = kutu.dataset.qrOkuyucu;
        const tokenAlani = document.getElementById(id + 'Token');
        const kamera = document.getElementById(id + 'Kamera');
        const galeri = document.getElementById(id + 'Galeri');
        const durum = kutu.querySelector('[data-qr-durum]');
        const modal = kutu.querySelector('[data-qr-kamera-modal]');
        const video = kutu.querySelector('[data-qr-video]');
        const kameraDurum = kutu.querySelector('[data-qr-kamera-durum]');
        const fener = kutu.querySelector('[data-qr-fener]');
        let akis = null, taramaAktif = false, fenerAcik = false;

        kutu.querySelector('[data-qr-galeri]').addEventListener('click', () => galeri.click());

        function tokenCikar(ham) {
            const metin = String(ham || '').trim();
            const eslesme = metin.match(/\/arac\/([0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})(?:[/?#]|$)/i);
            return eslesme?.[1] || (/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(metin) ? metin : '');
        }

        function kamerayiKapat() {
            taramaAktif = false;
            akis?.getTracks().forEach(track => track.stop());
            akis = null;
            video.srcObject = null;
            modal.hidden = true;
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        function basarili(ham) {
            const token = tokenCikar(ham);
            if (!token) return false;
            tokenAlani.value = token;
            durum.classList.remove('text-danger');
            durum.innerHTML = '<strong class="text-success">QR başarıyla okundu.</strong> Araç kaydedildiğinde havuzdan bu araca atanacak.';
            kamerayiKapat();
            return true;
        }

        async function canliKameraAc() {
            if (!navigator.mediaDevices?.getUserMedia) {
                durum.textContent = 'Canlı kamera bu tarayıcıda desteklenmiyor. Fotoğraf yöntemi açıldı.';
                kamera.click();
                return;
            }
            try {
                modal.hidden = false;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                kameraDurum.textContent = 'Kamera hazırlanıyor…';
                akis = await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'},width:{ideal:1920},height:{ideal:1080}},audio:false});
                video.srcObject = akis;
                await video.play();
                taramaAktif = true;
                kameraDurum.textContent = 'QR kod aranıyor…';

                const track = akis.getVideoTracks()[0];
                const yetenekler = track.getCapabilities?.() || {};
                if (yetenekler.torch) fener.hidden = false;
                let detector = null;
                if ('BarcodeDetector' in window) {
                    try { detector = new BarcodeDetector({formats:['qr_code']}); } catch (_) {}
                }
                const jsQR = await jsQrYukle();
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d', {willReadFrequently:true});

                async function tara() {
                    if (!taramaAktif) return;
                    try {
                        if (detector) {
                            const kodlar = await detector.detect(video);
                            if (kodlar[0]?.rawValue && basarili(kodlar[0].rawValue)) return;
                        }
                        if (video.readyState >= 2) {
                            const oran = Math.min(1, 900 / video.videoWidth);
                            canvas.width = Math.max(1, Math.round(video.videoWidth * oran));
                            canvas.height = Math.max(1, Math.round(video.videoHeight * oran));
                            ctx.setTransform(1, 0, 0, 1, 0, 0);
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            let image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            let sonuc = jsQR(image.data, image.width, image.height, {inversionAttempts:'attemptBoth'});
                            if (!sonuc) {
                                ctx.setTransform(-1, 0, 0, 1, canvas.width, 0);
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                ctx.setTransform(1, 0, 0, 1, 0, 0);
                                image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                sonuc = jsQR(image.data, image.width, image.height, {inversionAttempts:'attemptBoth'});
                            }
                            if (sonuc?.data && basarili(sonuc.data)) return;
                        }
                    } catch (_) {}
                    if (taramaAktif) setTimeout(tara, 140);
                }
                tara();
            } catch (_) {
                kamerayiKapat();
                durum.textContent = 'Kamera açılamadı. Kamera iznini kontrol edin; fotoğraf yöntemi açıldı.';
                kamera.click();
            }
        }

        kutu.querySelector('[data-qr-kamera]').addEventListener('click', canliKameraAc);
        kutu.querySelector('[data-qr-kapat]').addEventListener('click', kamerayiKapat);
        kutu.querySelector('[data-qr-fotograf]').addEventListener('click', () => { kamerayiKapat(); kamera.click(); });
        fener.addEventListener('click', async () => {
            const track = akis?.getVideoTracks()[0];
            if (!track) return;
            fenerAcik = !fenerAcik;
            try { await track.applyConstraints({advanced:[{torch:fenerAcik}]}); fener.classList.toggle('btn-primary', fenerAcik); } catch (_) {}
        });

        async function oku(dosya) {
            if (!dosya) return;
            durum.textContent = 'QR kod okunuyor…';
            let bitmap;
            try {
                bitmap = await createImageBitmap(dosya);
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
                    let image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const jsQR = await jsQrYukle();
                    ham = jsQR(image.data, image.width, image.height, {inversionAttempts: 'attemptBoth'})?.data || '';
                    if (!ham) {
                        ctx.setTransform(-1, 0, 0, 1, canvas.width, 0);
                        ctx.drawImage(bitmap, 0, 0);
                        ctx.setTransform(1, 0, 0, 1, 0, 0);
                        image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        ham = jsQR(image.data, image.width, image.height, {inversionAttempts: 'attemptBoth'})?.data || '';
                    }
                }
                if (!basarili(ham)) throw new Error('Bu görüntüde geçerli bir İZGİOS araç QR kodu bulunamadı.');
            } catch (hata) {
                tokenAlani.value = '';
                durum.textContent = hata.message;
                durum.classList.add('text-danger');
            } finally {
                bitmap?.close?.();
                kamera.value = '';
                galeri.value = '';
            }
        }
        kamera.addEventListener('change', () => oku(kamera.files[0]));
        galeri.addEventListener('change', () => oku(galeri.files[0]));
        window.addEventListener('pagehide', kamerayiKapat);
    });
});
</script>
@endpush
@endonce
