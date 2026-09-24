<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Araç QR Etiketleri</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:0;color:#10213d}.toolbar{position:sticky;top:0;background:#10213d;color:white;padding:14px;display:flex;justify-content:space-between;align-items:center}.toolbar button{border:0;border-radius:8px;padding:10px 18px;font-weight:700;cursor:pointer}.sheet{display:grid;grid-template-columns:repeat(4,46mm);justify-content:center;gap:4mm;padding:8mm}.etiket{width:46mm;height:52mm;border:1px dashed #9aa7b8;border-radius:3mm;padding:2.5mm;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;break-inside:avoid}.etiket svg{width:35mm;height:35mm;display:block;flex:none}.site{font-size:10px;font-weight:800;margin-top:1.5mm}.not{font-size:8px;color:#526177;margin-top:.8mm}@media print{.toolbar{display:none}.sheet{padding:0;gap:3mm}.etiket{border:1px solid #bbb}}@page{size:A4;margin:7mm}
    </style>
</head>
<body>
<div class="toolbar"><strong>100’lük QR Partisi · {{ $parti }}</strong><button onclick="window.print()">Yazdır / PDF Kaydet</button></div>
<main class="sheet">
@foreach($etiketler as $etiket)
    <article class="etiket">
        <div>{!! $etiket['qr'] !!}</div>
        <div class="site">www.izgios.com</div>
        <div class="not">Etiketi sökmeyiniz.</div>
    </article>
@endforeach
</main>
</body>
</html>
