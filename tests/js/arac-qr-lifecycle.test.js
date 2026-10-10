import test from 'node:test';
import assert from 'node:assert/strict';

class Element extends EventTarget {
    hidden = true; style = {}; dataset = { qrOkuyucu: 'test' }; value = '';
    parentElement = { hidden: true }; classList = { add() {}, remove() {} };
    setAttribute() {} replaceChildren() {} click() { this.dispatchEvent(new Event('click')); }
    async play() {} readyState = 0;
}
const elements = new Map();
const get = key => { if (!elements.has(key)) elements.set(key, new Element()); return elements.get(key); };
const box = get('box'); box.querySelector = key => get(key);
const documentMock = new Element();
documentMock.readyState = 'complete'; documentMock.body = { style: { overflow: 'auto' } };
documentMock.querySelectorAll = () => [box]; documentMock.getElementById = key => get(key);
globalThis.document = documentMock; globalThis.window = new Element();
let resolvePermission;
Object.defineProperty(globalThis, 'navigator', { configurable: true, value: {
    mediaDevices: { getUserMedia: () => new Promise(resolve => { resolvePermission = resolve; }), enumerateDevices: async () => [] },
} });
await import('../../resources/js/arac-qr-okuyucu.js');
const flush = () => new Promise(resolve => setImmediate(resolve));

test('closing before camera permission resolves stops the late stream', async () => {
    get('[data-qr-kamera]').click();
    assert.equal(get('[data-qr-kamera-modal]').hidden, false);
    get('[data-qr-kapat]').click();
    let stopped = 0;
    resolvePermission({ getTracks: () => [{ stop: () => stopped++ }] });
    await flush();
    assert.equal(stopped, 1);
    assert.equal(get('[data-qr-kamera-modal]').hidden, true);
    assert.equal(get('[data-qr-video]').srcObject, null);
    assert.equal(documentMock.body.style.overflow, 'auto');
});
test('camera rejection leaves gallery available and explains permission', async () => {
    navigator.mediaDevices.getUserMedia = async () => { const error = new Error(); error.name = 'NotAllowedError'; throw error; };
    get('[data-qr-kamera]').click(); await flush();
    assert.equal(get('[data-qr-kamera-modal]').hidden, true);
    assert.match(get('[data-qr-durum]').textContent, /Kamera izni verilmedi/);
});
test('pagehide stops an active camera without optional capabilities', async () => {
    let stopped = 0;
    const track = { stop: () => stopped++, getSettings: () => ({}) };
    navigator.mediaDevices.getUserMedia = async () => ({ getTracks: () => [track], getVideoTracks: () => [track] });
    get('[data-qr-kamera]').click(); await flush();
    assert.equal(get('[data-qr-fener]').hidden, true);
    window.dispatchEvent(new Event('pagehide'));
    assert.equal(stopped, 1);
    assert.equal(get('[data-qr-kamera-modal]').hidden, true);
});

test('a stalled worker is replaced and camera scanning continues', async () => {
    let created = 0, terminated = 0;
    globalThis.Worker = class {
        constructor() { created++; }
        postMessage() {} // Simulate a decoder that never replies.
        terminate() { terminated++; }
    };
    documentMock.createElement = () => ({
        width: 0, height: 0,
        getContext: () => ({
            fillRect() {}, drawImage() {},
            getImageData: () => ({ data: new Uint8ClampedArray(16).fill(255), width: 2, height: 2 }),
        }),
    });
    const video = get('[data-qr-video]');
    video.readyState = 2; video.videoWidth = 2; video.videoHeight = 2;
    try {
        get('[data-qr-kamera]').click(); await flush();
        assert.equal(created, 1);
        await new Promise(resolve => setTimeout(resolve, 5350));
        assert.ok(created >= 2);
        assert.ok(terminated >= 1);
    } finally {
        window.dispatchEvent(new Event('pagehide'));
        video.readyState = 0;
        delete globalThis.Worker;
    }
});
