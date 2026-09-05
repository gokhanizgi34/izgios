<!doctype html>
<html lang="tr">
<body style="margin:0;background:#f4f7fb;color:#102a4d;font-family:Arial,sans-serif">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 12px"><tr><td align="center">
        <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border-radius:18px;overflow:hidden;border:1px solid #dbe5f2">
            <tr><td style="padding:26px 30px;background:#102a4d;color:#fff"><strong style="font-size:25px">İZGİOS</strong><div style="margin-top:7px;color:#f0c736;font-weight:bold">Yeni destek talebi</div></td></tr>
            <tr><td style="padding:30px"><h1 style="margin:0 0 18px;font-size:22px">{{ $talep->baslik }}</h1>
                <p style="margin:0 0 16px;color:#60708b"><strong>Gönderen:</strong> {{ $talep->kullanici?->tamAdi() ?? 'Kullanıcı' }} · {{ $talep->kullanici?->email ?? '-' }}</p>
                <p style="margin:0 0 16px;color:#60708b"><strong>Kategori:</strong> {{ ucfirst($talep->kategori) }} · <strong>Öncelik:</strong> {{ ucfirst($talep->oncelik) }} · <strong>Hata kodu:</strong> {{ $talep->hata_kodu ?: '-' }}</p>
                <div style="padding:18px;border-radius:12px;background:#f4f7fb;line-height:1.6;white-space:pre-line">{{ $talep->mesaj }}</div>
                @if($yeniMesaj)
                    <p style="margin:20px 0 8px;font-weight:bold">Son kullanıcı mesajı</p>
                    <div style="padding:18px;border-radius:12px;background:#fff8dc;line-height:1.6;white-space:pre-line">{{ $yeniMesaj }}</div>
                @endif
                <p style="margin:24px 0 0"><a href="{{ $destekUrl }}" style="display:inline-block;padding:12px 18px;border-radius:9px;background:#e1bc32;color:#102a4d;text-decoration:none;font-weight:bold">Talebi aç ve yanıtla</a></p>
            </td></tr>
        </table>
    </td></tr></table>
</body>
</html>
