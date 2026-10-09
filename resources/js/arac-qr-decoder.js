import jsQR from 'jsqr';

import { tokenFromQr } from './arac-qr-token.js';
export { tokenFromQr } from './arac-qr-token.js';

export function mirrorPixels(data, width, height) {
    const out = new Uint8ClampedArray(data.length);
    for (let y = 0; y < height; y++) for (let x = 0; x < width; x++) {
        const from = (y * width + x) * 4, to = (y * width + width - x - 1) * 4;
        out.set(data.subarray(from, from + 4), to);
    }
    return out;
}

export function enhancePixels(data, width, height, sharpen = false) {
    const gray = new Uint8ClampedArray(width * height), hist = new Uint32Array(256);
    for (let i = 0; i < gray.length; i++) {
        gray[i] = .299 * data[i * 4] + .587 * data[i * 4 + 1] + .114 * data[i * 4 + 2];
        hist[gray[i]]++;
    }
    let low = 0, high = 255, count = 0;
    while (low < 254 && count + hist[low] < gray.length * .01) count += hist[low++];
    count = 0;
    while (high > low + 1 && count + hist[high] < gray.length * .01) count += hist[high--];
    const out = new Uint8ClampedArray(data.length), range = Math.max(16, high - low);
    for (let y = 0; y < height; y++) for (let x = 0; x < width; x++) {
        const i = y * width + x;
        let value = gray[i];
        if (sharpen && x && y && x < width - 1 && y < height - 1) {
            value += .6 * (4 * value - gray[i - 1] - gray[i + 1] - gray[i - width] - gray[i + width]);
        }
        value = (value - low) * 255 / range;
        out[i * 4] = out[i * 4 + 1] = out[i * 4 + 2] = value;
        out[i * 4 + 3] = 255;
    }
    return out;
}

// jsQR performs local thresholding and perspective extraction. Always try both
// orientations, including after a different, unrelated QR was found.
export function decodePixels(data, width, height, enhancement = 0) {
    const variants = [data];
    if (enhancement) variants.push(enhancePixels(data, width, height, enhancement === 2));
    for (const pixels of variants) for (const mirrored of [false, true]) {
        const result = jsQR(mirrored ? mirrorPixels(pixels, width, height) : pixels,
            width, height, { inversionAttempts: 'attemptBoth' });
        const token = tokenFromQr(result?.data);
        if (token) return token;
    }
    return '';
}
