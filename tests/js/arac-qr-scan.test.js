import test from 'node:test';
import assert from 'node:assert/strict';
import { scanPasses, boundedDetection } from '../../resources/js/arac-qr-scan.js';

test('off-centre labels get contrast, sharpness and shadow correction without cropping', () => {
    for (const enhancement of [0, 1, 2, 3]) {
        assert.ok(scanPasses.some(pass => pass.crop === 1 && pass.enhancement === enhancement));
    }
    assert.ok(scanPasses.some(pass => pass.crop === 1 && pass.limit >= 1920));
});
test('an unresponsive native detector cannot block software scanning indefinitely', async () => {
    await assert.rejects(boundedDetection({ detect: () => new Promise(() => {}) }, {}, 10), /timed out/);
});
test('native errors propagate for software fallback', async () => {
    await assert.rejects(boundedDetection({ detect: () => { throw new Error('unsupported'); } }, {}), /unsupported/);
});
test('a responsive native detector preserves decoded results', async () => {
    const codes = [{ rawValue: 'test' }];
    assert.equal(await boundedDetection({ detect: async () => codes }, {}), codes);
});
