import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { decodePixels, mirrorPixels, tokenFromQr } from '../../resources/js/arac-qr-decoder.js';
const matrices = JSON.parse(readFileSync(new URL('./qr-matrix.json', import.meta.url), 'utf8').replace(/^\uFEFF/, ''));
const token = '12345678-1234-4234-8234-123456789abc';
function fixture(dark = 0, light = 255, skew = false, rotate = false, matrix = matrices.Q) {
    const size = (matrix.length + 12) * 6, pixels = new Uint8ClampedArray(size * size * 4);
    for (let y = 0; y < size; y++) for (let x = 0; x < size; x++) {
        // Inverse projective transform produces an oblique camera view.
        let px = x, py = y;
        if (rotate) { px = y; py = size - x - 1; }
        if (skew) { const d = 1 + .00035 * py; px = (px - .12 * py) / d; py /= d; }
        const mx = Math.floor(px / 6) - 6, my = Math.floor(py / 6) - 6;
        const v = matrix[my]?.[mx] === '1' ? dark : light, i = (y * size + x) * 4;
        pixels[i] = pixels[i + 1] = pixels[i + 2] = v; pixels[i + 3] = 255;
    }
    return { pixels, size };
}
for (const [name, dark, light, skew, rotate] of [
    ['normal', 0, 255], ['low contrast', 112, 140], ['low light', 8, 48],
    ['perspective', 0, 255, true], ['rotated', 0, 255, false, true],
]) for (const mirrored of [false, true]) {
    test(`${name}, mirrored=${mirrored}`, () => {
        const { pixels, size } = fixture(dark, light, skew, rotate);
        assert.equal(decodePixels(mirrored ? mirrorPixels(pixels, size, size) : pixels, size, size, 2), token);
    });
}
test('mild blur and mirrored blur', () => {
    const { pixels, size } = fixture();
    const blurred = pixels.slice();
    for (let y = 1; y < size - 1; y++) for (let x = 1; x < size - 1; x++) {
        let sum = 0;
        for (let dy = -1; dy <= 1; dy++) for (let dx = -1; dx <= 1; dx++) sum += pixels[((y + dy) * size + x + dx) * 4];
        const i = (y * size + x) * 4;
        blurred[i] = blurred[i + 1] = blurred[i + 2] = sum / 9;
    }
    assert.equal(decodePixels(blurred, size, size, 2), token);
    assert.equal(decodePixels(mirrorPixels(blurred, size, size), size, size, 2), token);
});
test('blank images and unrelated QR text are not accepted', () => {
    assert.equal(decodePixels(new Uint8ClampedArray(100 * 100 * 4).fill(255), 100, 100, 2), '');
    assert.equal(tokenFromQr('https://example.com/not-a-vehicle'), '');
    assert.equal(tokenFromQr(token + 'extra'), '');
    assert.equal(tokenFromQr(`https://www.izgios.com/arac/${token}?source=old-label`), token);
    assert.equal(tokenFromQr(token.toUpperCase()), token);
});

test('existing L-level printed labels remain readable in both orientations', () => {
    const { pixels, size } = fixture(0, 255, false, false, matrices.L);
    assert.equal(decodePixels(pixels, size, size, 0), token);
    assert.equal(decodePixels(mirrorPixels(pixels, size, size), size, size, 0), token);
});