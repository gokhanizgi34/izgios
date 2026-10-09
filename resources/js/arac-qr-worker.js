import { decodePixels } from './arac-qr-decoder';
self.onmessage = ({ data: { pixels, width, height, enhancement } }) => {
    try { self.postMessage({ token: decodePixels(new Uint8ClampedArray(pixels), width, height, enhancement) }); }
    catch { self.postMessage({ error: 'QR görüntüsü çözümlenemedi.' }); }
};
