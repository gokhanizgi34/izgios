// Full-frame enhancements are interleaved with crops so off-centre labels
// receive the same recovery attempts as labels inside the guide.
export const scanPasses = [
    { crop: 1, limit: 960, enhancement: 0 },
    { crop: .75, limit: 1280, enhancement: 0 },
    { crop: 1, limit: 1920, enhancement: 1 },
    { crop: .45, limit: 1280, enhancement: 1 },
    { crop: 1, limit: 1280, enhancement: 2 },
    { crop: .75, limit: 960, enhancement: 2 },
    { crop: 1, limit: 1920, enhancement: 3 },
    { crop: .45, limit: 1280, enhancement: 3 },
];

export function boundedDetection(detector, canvas, timeout = 600) {
    let timer;
    return Promise.race([
        Promise.resolve().then(() => detector.detect(canvas)),
        new Promise((_, reject) => { timer = setTimeout(() => reject(new Error('Native decoder timed out')), timeout); }),
    ]).finally(() => clearTimeout(timer));
}
