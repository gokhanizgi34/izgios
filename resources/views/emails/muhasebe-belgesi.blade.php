<!doctype html>
<html lang="tr">
<body style="margin:0;background:#f3f6fa;font-family:Arial,sans-serif;color:#152e55;padding:24px">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:720px;margin:auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #dce4ed">
        <tr><td style="padding:24px 28px;background:#152e55;color:#fff">
            <div style="font-size:13px;letter-spacing:1px">{{ $firma->gosterim_adi ?? $firma->unvan }}</div>
            <div style="font-size:26px;font-weight:700;margin-top:7px">{{ $baslik }}</div>
            <div style="margin-top:6px;color:#f3d45c">Belge no: {{ $belge->belge_no }}</div>
        </td></tr>
        <tr><td style="padding:26px 28px">
            <p style="margin:0 0 18px">Belge detayları aşağıdadır.</p>
            @if(!empty($paylasimMesaji))<p style="padding:14px;border-radius:9px;background:#f4f7fb;white-space:pre-line">{{ $paylasimMesaji }}</p>@endif
            <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;font-size:13px">
                <thead><tr style="background:#eaf0f7"><th align="left">Ürün / Hizmet</th><th align="right">Adet</th><th align="right">KDV dahil</th></tr></thead>
                <tbody>
                @foreach ($satirlar as $satir)
                    <tr style="border-bottom:1px solid #e4e9f0"><td>{{ $satir->urun_adi }}</td><td align="right">{{ $satir->adet }} {{ $satir->birim }}</td><td align="right">₺{{ number_format($satir->kdv_dahil_tutar, 2, ',', '.') }}</td></tr>
                @endforeach
                </tbody>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:18px"><tr><td align="right" style="font-size:18px;font-weight:bold">KDV dahil genel toplam: <span style="color:#9c7000">₺{{ number_format($belge->tutar, 2, ',', '.') }}</span></td></tr></table>
            @if ($resmiFatura ?? false)
                <p style="margin:22px 0 0;color:#52657e;font-size:12px">Resmî fatura işlemi entegrasyon sağlayıcısı yanıtıyla doğrulanır.</p>
            @endif
        </td></tr>
        <tr><td style="padding:17px 28px;background:#f5f7fa;color:#697b90;font-size:12px">Bu belge {{ $firma->gosterim_adi ?? $firma->unvan }} tarafından İZGİOS üzerinden paylaşılmıştır.</td></tr>
    </table>
</body>
</html>
