import { tokenFromQr } from './arac-qr-token.js';
import { scanPasses, boundedDetection } from './arac-qr-scan.js';

function initReader(box) {
    const get = name => box.querySelector(`[data-qr-${name}]`);
    const token = box.querySelector('[name="qr_havuz_token"]');
    const photo = document.getElementById(box.dataset.qrOkuyucu + 'Kamera');
    const gallery = document.getElementById(box.dataset.qrOkuyucu + 'Galeri');
    const video = get('video'), modal = get('kamera-modal'), status = get('durum');
    const hint = get('kamera-durum'), torch = get('fener'), zoom = get('zoom'), cameras = get('cihaz');
    let stream, worker, pending, timer, generation = 0, torchOn = false, overflow = '', workerTimer;
    let detector;
    try { detector = new BarcodeDetector({ formats: ['qr_code'] }); } catch { /* Safari: bundled decoder */ }
    function stop() {
        generation++;
        clearTimeout(timer);
        clearTimeout(workerTimer);
        worker?.terminate(); worker = null;
        pending?.(''); pending = null;
        stream?.getTracks().forEach(track => track.stop()); stream = null;
        video.srcObject = null;
        if (!modal.hidden) document.body.style.overflow = overflow;
        modal.hidden = true; modal.style.display = 'none';
        torchOn = false; torch.hidden = true; torch.setAttribute('aria-pressed', 'false'); torch.textContent = 'Feneri aç';
        zoom.parentElement.hidden = true;
        cameras.parentElement.hidden = true;
    }
    function success(value) {
        const parsed = tokenFromQr(value);
        if (!parsed) return false;
        token.value = parsed;
        status.classList.remove('text-danger');
        status.textContent = 'QR başarıyla okundu. Araç kaydedilirken etiketin boş olduğu kontrol edilip araca atanacak.';
        stop();
        return true;
    }
    function decode(canvas, enhancement) {
        if (!worker) worker = new Worker(new URL('./arac-qr-worker.js', import.meta.url), { type: 'module' });
        const image = canvas.getContext('2d', { willReadFrequently: true }).getImageData(0, 0, canvas.width, canvas.height);
        return new Promise((resolve, reject) => {
            pending = resolve;
            worker.onmessage = ({ data }) => { clearTimeout(workerTimer); pending = null; data.error ? reject(new Error(data.error)) : resolve(data.token); };
            worker.onerror = () => { clearTimeout(workerTimer); pending = null; worker.terminate(); worker = null; reject(new Error('QR okuyucu yeniden hazırlanıyor…')); };
            workerTimer = setTimeout(() => {
                worker?.terminate(); worker = null; pending = null;
                reject(new Error('Görüntü yeniden taranıyor. Etiketi sabit tutun.'));
            }, 5000);
            worker.postMessage({ pixels: image.data.buffer, width: image.width, height: image.height, enhancement }, [image.data.buffer]);
        });
    }
    function frame(source, width, height, crop, limit) {
        const side = Math.min(width, height) * crop;
        const sw = crop < 1 ? side : width, sh = crop < 1 ? side : height;
        const ratio = Math.min(1, limit / Math.max(sw, sh));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(sw * ratio)); canvas.height = Math.max(1, Math.round(sh * ratio));
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(source, (width - sw) / 2, (height - sh) / 2, sw, sh, 0, 0, canvas.width, canvas.height);
        return canvas;
    }
    async function scan(canvas, enhancement, session) {
        if (detector) {
            for (let orientation = 0; orientation < 2; orientation++) {
                try {
                    const codes = await boundedDetection(detector, canvas);
                    if (session !== generation) return '';
                    for (const code of codes) if (tokenFromQr(code.rawValue)) return code.rawValue;
                } catch { detector = null; break; /* Continue with the independent software decoder. */ }
                if (orientation === 0) {
                    const ctx = canvas.getContext('2d');
                    ctx.save(); ctx.translate(canvas.width, 0); ctx.scale(-1, 1); ctx.drawImage(canvas, 0, 0); ctx.restore();
                }
            }
        }
        if (session !== generation) return '';
        return decode(canvas, enhancement);
    }
    function guidance(canvas, turn) {
        const ctx = canvas.getContext('2d'), pixels = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        let light = 0, n = 0;
        for (let i = 0; i < pixels.length; i += 160) { light += (pixels[i] + pixels[i + 1] + pixels[i + 2]) / 3; n++; }
        if (light / n < 65) return 'Işığı artırın veya feneri açın. Camdaki yansımayı azaltın.';
        return [
            'QR küçük görünüyorsa yaklaştırın veya yakınlaştırmayı artırın. Beyaz kenarların tamamı görünsün.',
            'Görüntü bulanıksa biraz uzaklaştırın, telefonu sabit tutup odaklanmasını bekleyin.',
            'Kamerayı etikete paralel tutun. Parlama varsa açıyı hafifçe değiştirin.',
        ][Math.floor(turn / 6) % 3];
    }
    async function configure(track, session) {
        const caps = track.getCapabilities?.() || {};
        for (const key of ['focusMode', 'exposureMode', 'whiteBalanceMode']) {
            if (caps[key]?.includes('continuous')) {
                try { await track.applyConstraints({ advanced: [{ [key]: 'continuous' }] }); } catch { /* optional */ }
            }
        }
        if (session !== generation) return;
        torch.hidden = !caps.torch;
        zoom.parentElement.hidden = !(caps.zoom && caps.zoom.max > caps.zoom.min);
        if (caps.zoom) {
            zoom.min = caps.zoom.min; zoom.max = caps.zoom.max; zoom.step = caps.zoom.step || .1;
            zoom.value = track.getSettings().zoom || caps.zoom.min;
        }
    }
    async function open(deviceId) {
        stop();
        if (!navigator.mediaDevices?.getUserMedia) {
            status.textContent = 'Canlı kamera için HTTPS veya localhost ve kamera destekli bir tarayıcı gerekir. Fotoğraf çek veya galeri seç seçeneğini kullanın.';
            photo.click(); return;
        }
        const session = generation;
        overflow = document.body.style.overflow; document.body.style.overflow = 'hidden';
        modal.hidden = false; modal.style.display = 'flex'; hint.textContent = 'Kamera hazırlanıyor…';
        try {
            const requested = await navigator.mediaDevices.getUserMedia({ audio: false, video: {
                ...(deviceId ? { deviceId: { exact: deviceId } } : { facingMode: { ideal: 'environment' } }),
                width: { ideal: 1920 }, height: { ideal: 1080 },
            } });
            if (session !== generation) { requested.getTracks().forEach(track => track.stop()); return; }
            stream = requested; video.srcObject = stream; await video.play();
            if (session !== generation) return;
            const track = stream.getVideoTracks()[0];
            await configure(track, session);
            if (session !== generation) return;
            try {
                const devices = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
                if (session !== generation) return;
                cameras.replaceChildren(...devices.map((d, index) => new Option(d.label || `Kamera ${index + 1}`, d.deviceId)));
                cameras.value = track.getSettings().deviceId || ''; cameras.parentElement.hidden = devices.length < 2;
            } catch { /* Selection is optional. */ }
            let turn = 0;
            const loop = async () => {
                if (session !== generation) return;
                try {
                    if (video.readyState >= 2 && video.videoWidth) {
                        // Alternate full frame and centre crops; never discard the full field of view.
                        const pass = scanPasses[turn++ % scanPasses.length];
                        const canvas = frame(video, video.videoWidth, video.videoHeight, pass.crop, pass.limit);
                        hint.textContent = guidance(canvas, turn);
                        const value = await scan(canvas, pass.enhancement, session);
                        if (session !== generation) return;
                        if (success(value)) return;

                    }
                } catch (error) { if (session === generation) hint.textContent = error.message; }
                if (session === generation) timer = setTimeout(loop, 120);
            };
            loop();
        } catch (error) {
            if (session !== generation) return;
            stop(); status.classList.add('text-danger');
            status.textContent = error.name === 'NotAllowedError'
                ? 'Kamera izni verilmedi. Tarayıcı ayarlarından izin verin veya galeriden fotoğraf seçin.'
                : 'Kamera açılamadı. Kamerayı kullanan uygulamaları kapatın veya fotoğraf seçin.';
        }
    }
    async function readFile(file) {
        if (!file) return;
        stop(); const session = generation;
        status.classList.remove('text-danger'); status.textContent = 'Fotoğraf normal ve aynalı olarak taranıyor…';
        let bitmap, objectUrl;
        try {
            try { bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' }); }
            catch {
                objectUrl = URL.createObjectURL(file);
                bitmap = new Image(); bitmap.src = objectUrl; await bitmap.decode();
            }
            if (session !== generation) return;
            for (const [crop, limit, enhancement] of [[1, 1280, 0], [1, 2000, 1], [.8, 1600, 1], [.5, 1600, 2], [1, 2000, 2], [1, 2000, 3], [.5, 1600, 3]]) {
                const canvas = frame(bitmap, bitmap.width, bitmap.height, crop, limit);
                const value = await scan(canvas, enhancement, session);
                if (session !== generation) return;
                if (success(value)) return;
            }
            throw new Error('Geçerli araç QR kodu bulunamadı. Etiketi tüm beyaz kenarlarıyla, yakından ve net çekin; ışığı artırıp yansımayı azaltın.');
        } catch (error) {
            if (session === generation) { status.textContent = error.message; status.classList.add('text-danger'); }
        } finally {
            bitmap?.close?.(); if (objectUrl) URL.revokeObjectURL(objectUrl);
            if (session === generation) { worker?.terminate(); worker = null; }
            photo.value = ''; gallery.value = '';
        }
    }
    get('kamera').addEventListener('click', () => open());
    get('kapat').addEventListener('click', stop);
    get('galeri').addEventListener('click', () => { stop(); gallery.click(); });
    get('fotograf').addEventListener('click', () => { stop(); photo.click(); });
    cameras.addEventListener('change', () => open(cameras.value));
    zoom.addEventListener('change', async () => {
        const track = stream?.getVideoTracks()[0]; if (!track) return;
        try { await track.applyConstraints({ advanced: [{ zoom: Number(zoom.value) }] }); }
        catch { hint.textContent = 'Yakınlaştırma uygulanamadı. Telefonu etikete yaklaştırın.'; }
    });
    torch.addEventListener('click', async () => {
        const track = stream?.getVideoTracks()[0]; if (!track) return;
        const next = !torchOn;
        try { await track.applyConstraints({ advanced: [{ torch: next }] }); torchOn = next; torch.setAttribute('aria-pressed', String(next)); torch.textContent = next ? 'Feneri kapat' : 'Feneri aç'; }
        catch { hint.textContent = 'Fener açılamadı. Ortam ışığını artırın.'; }
    });
    photo.addEventListener('change', () => readFile(photo.files[0]));
    gallery.addEventListener('change', () => readFile(gallery.files[0]));
    window.addEventListener('pagehide', stop);
    document.addEventListener('visibilitychange', () => { if (document.hidden) stop(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') stop(); });
}

function init() { document.querySelectorAll('[data-qr-okuyucu]').forEach(initReader); }
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
else init();
