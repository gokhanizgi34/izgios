<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Araç QR Etiketleri</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:0;color:#10213d}.toolbar{position:sticky;top:0;background:#10213d;color:white;padding:14px;display:flex;justify-content:space-between;align-items:center}.toolbar button{border:0;border-radius:8px;padding:10px 18px;font-weight:700;cursor:pointer}.sheet{display:grid;grid-template-columns:repeat(4,1fr);gap:5mm;padding:8mm}.etiket{height:48mm;border:1px dashed #9aa7b8;border-radius:3mm;padding:3mm;display:flex;align-items:center;gap:3mm;break-inside:avoid}.etiket svg{width:31mm;height:31mm;flex:none}.marka{font-size:15px;font-weight:800}.seri{font-size:10px;margin-top:5px;word-break:break-all}.not{font-size:9px;color:#526177;margin-top:5px}@media print{.toolbar{display:none}.sheet{padding:0;gap:3mm}.etiket{border:1px solid #bbb}}@page{size:A4;margin:7mm}
    </style>
</head>
<body>
<div class="toolbar"><strong>100’lük QR Partisi · {{ $parti }}</strong><button onclick="window.print()">Yazdır / PDF Kaydet</button></div>
<main class="sheet">
@foreach($etiketler as $etiket)
    <article class="etiket">
        <div>{!! $etiket['qr'] !!}</div>
        <div><div class="marka">İZGİOS</div><div class="seri">{{ $etiket['seri'] }}</div><div class="not">ARAÇ DİJİTAL KİMLİĞİ<br>Etiketi sökmeyiniz.</div></div>
    </article>
@endforeach
</main>
</body>
</html>
