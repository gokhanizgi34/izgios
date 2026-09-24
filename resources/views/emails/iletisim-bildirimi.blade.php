<!doctype html>
<html lang="tr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;background:#f2f5f9;font-family:Arial,Helvetica,sans-serif;color:#14213d;">
    @php
        $mesajMetni = (string) $mesaj;
        if ($aksiyonUrl ?? null) {
            $mesajMetni = str_replace($aksiyonUrl, '', $mesajMetni);
            $mesajMetni = preg_replace('/\bDetaylar:\s*(?=Şifre)/iu', '', $mesajMetni);
            $mesajMetni = preg_replace('/\s{2,}/u', ' ', $mesajMetni);
        }
        $mesajHtml = e(trim($mesajMetni));
    @endphp
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;background:#f2f5f9;">
        <tr><td align="center">
            <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e1e8f0;">
                <tr><td style="padding:25px 32px;background:#102a52;color:#ffffff;">
                    <div style="font-size:24px;font-weight:800;letter-spacing:.5px;">İZGİ<span style="color:#e3b832;">OS</span></div>
                    <div style="margin-top:7px;font-size:13px;opacity:.86;">{{ $firmaAdi }} · Servis Bilgilendirmesi</div>
                </td></tr>
                <tr><td style="padding:32px;">
                    <h1 style="margin:0 0 18px;font-size:21px;color:#14213d;">{{ $konu }}</h1>
                    @if($plaka)<div style="display:inline-block;margin-bottom:18px;padding:8px 12px;border-radius:8px;background:#fff3cd;color:#7b5b00;font-weight:700;">Araç: {{ $plaka }}</div>@endif
                    <div style="font-size:15px;line-height:1.7;color:#334155;white-space:pre-line;">{!! nl2br($mesajHtml) !!}</div>
                    @if($aksiyonUrl ?? null)
                        <div style="margin-top:20px;"><a href="{{ $aksiyonUrl }}" target="_blank" rel="noopener" style="display:inline-block;padding:12px 18px;border-radius:9px;background:#e3b832;color:#14213d;font-weight:800;text-decoration:none;">{{ str_contains(mb_strtolower($aksiyonUrl), 'google') || str_contains(mb_strtolower($aksiyonUrl), 'g.page') ? 'Google Yorum Bağlantısını Aç' : 'Bağlantıyı Aç' }}</a></div>
                    @endif
                    <div style="margin-top:28px;padding-top:18px;border-top:1px solid #e7edf4;font-size:12px;color:#64748b;">Bildirim tarihi: {{ $tarih }}<br>Bu mesaj İZGİOS iletişim otomasyonu tarafından gönderilmiştir.</div>
                </td></tr>
                <tr><td style="padding:16px 32px;background:#102a52;color:#d7e1ef;font-size:12px;text-align:center;">{{ $firmaAdi }} · İZGİOS Oto Servis Otomasyonu</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
