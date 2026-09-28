<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Araç QR Etiketleri</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:0;color:#10213d}.toolbar{position:sticky;top:0;background:#10213d;color:white;padding:14px;display:flex;justify-content:space-between;align-items:center;gap:16px}.toolbar small{display:block;margin-top:4px;color:#ffd96a}.toolbar-actions{display:flex;gap:8px;align-items:center}.toolbar a,.toolbar button{border:0;border-radius:8px;padding:10px 14px;font-weight:700;cursor:pointer;white-space:nowrap;text-decoration:none}.toolbar a{background:#fff;color:#10213d}.toolbar button{background:#e3b82e;color:#10213d}.sheet{display:grid;grid-template-columns:repeat(4,46mm);justify-content:center;gap:4mm;padding:8mm}.etiket{width:46mm;height:52mm;border:1px dashed #9aa7b8;border-radius:3mm;padding:2.5mm;display:flex;align-items:center;justify-content:center;text-align:center;break-inside:avoid}.etiket-icerik{display:flex;flex-direction:column;align-items:center;justify-content:center}.cam-ici .etiket-icerik{transform:scaleX(-1)}.qr-zemin{background:#fff;padding:1mm;border-radius:1mm;line-height:0}.etiket svg{width:35mm;height:35mm;display:block;flex:none;background:#fff}.site{font-size:10px;font-weight:800;margin-top:1.5mm}.not{font-size:8px;color:#526177;margin-top:.8mm}@media print{.toolbar{display:none}.sheet{padding:0;gap:3mm}.etiket{border:1px solid #bbb}.qr-zemin,.etiket svg{background:#fff!important;print-color-adjust:exact;-webkit-print-color-adjust:exact}}@page{size:A4;margin:7mm}
    </style>
</head>
<body class="{{ request('baski', 'cam-ici') === 'cam-ici' ? 'cam-ici' : 'normal-baski' }}">
<div class="toolbar"><div><strong>100’lük QR Partisi · {{ $parti }}</strong><small>{{ request('baski', 'cam-ici') === 'cam-ici' ? 'CAM İÇİ: Görsel bilinçli olarak aynalıdır; dışarıdan düz görünür.' : 'DIŞ YÜZEY: Görsel normal yöndedir.' }} QR alanına beyaz opak alt baskı uygulanmalıdır.</small></div><div class="toolbar-actions"><a href="{{ route('arac-qr-havuzu.yazdir', ['parti' => $parti, 'baski' => request('baski', 'cam-ici') === 'cam-ici' ? 'normal' : 'cam-ici']) }}">{{ request('baski', 'cam-ici') === 'cam-ici' ? 'Dış yüzey baskısına geç' : 'Cam içi baskıya geç' }}</a><button onclick="window.print()">Yazdır / PDF Kaydet</button></div></div>
<main class="sheet">
@foreach($etiketler as $etiket)
    <article class="etiket">
        <div class="etiket-icerik">
            <div class="qr-zemin">{!! $etiket['qr'] !!}</div>
            <div class="site">www.izgios.com</div>
            <div class="not">Etiketi sökmeyiniz.</div>
        </div>
    </article>
@endforeach
</main>
</body>
</html>
