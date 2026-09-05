<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $baslik }} · {{ $belge->belge_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #142a4b; margin: 34px; }
        header { display: flex; justify-content: space-between; border-bottom: 3px solid #ddb436; padding-bottom: 18px; }
        h1 { margin: 0; font-size: 27px; }
        .meta { margin: 24px 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 7px; }
        .etiket { font-size: 11px; color: #6b7b90; text-transform: uppercase; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #152e55; color: white; text-align: left; padding: 10px; font-size: 12px; }
        td { padding: 10px; border-bottom: 1px solid #dce4ed; font-size: 13px; }
        .sag { text-align: right; }
        .toplam { margin-left: auto; width: 330px; margin-top: 18px; }
        .toplam div { display: flex; justify-content: space-between; padding: 7px; }
        .genel { background: #fff2bf; font-size: 17px; font-weight: bold; border-radius: 6px; }
        .uyari { margin-top: 22px; padding: 12px; background: #f4f6f8; text-align: center; font-size: 12px; color: #52657e; }
        .paylas { margin: 0 0 20px; padding: 16px; border: 1px solid #dce4ed; border-radius: 10px; background: #f8fafc; }
        .paylas h2 { margin: 0 0 10px; font-size: 16px; }
        .paylas-grid { display: grid; grid-template-columns: 150px minmax(220px, 1fr) auto; gap: 10px; align-items: end; }
        .paylas label { display: block; color: #52657e; font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .paylas select, .paylas input { box-sizing: border-box; width: 100%; border: 1px solid #b9c8dc; border-radius: 7px; padding: 10px; font-size: 14px; background: white; }
        .paylas button, .print-button { border: 0; border-radius: 7px; padding: 11px 15px; background: #ddb436; color: #10274a; font-weight: bold; cursor: pointer; }
        .hint { display: block; margin-top: 9px; color: #52657e; font-size: 12px; }
        @media (max-width: 650px) { .paylas-grid { grid-template-columns: 1fr; } }
        @media print { body { margin: 16px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print print-button" onclick="window.print()">Yazdır / PDF olarak kaydet</button>
    <section class="no-print paylas">
        <h2>Belge detaylarını paylaş</h2>
        <form method="POST" action="{{ route('ciktilar.gonder', ['tur' => $tur, 'id' => $belge->id]) }}">
            @csrf
            <div class="paylas-grid">
                <div>
                    <label for="kanal">Gönderim kanalı</label>
                    <select id="kanal" name="kanal">
                        <option value="email">E-posta</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>
                <div>
                    <label for="alici">Alıcı</label>
                    <input id="alici" name="alici" value="{{ old('alici', $belge->alici_email ?? '') }}" placeholder="E-posta adresi">
                </div>
                <button type="submit">Gönder</button>
            </div>
            <span class="hint" id="kanal-ipucu">E-posta, fiş/belge satırları ve toplamlarıyla birlikte doğrudan gönderilir.</span>
        </form>
    </section>
    <header>
        <div>
            @if ($tur !== 'fis')
                <div class="etiket">İZGİOS Oto Servis Otomasyonu</div>
            @endif
            <h1>{{ $baslik }}</h1>
        </div>
        <div class="sag">
            <strong>{{ $firma->gosterim_adi ?? $firma->unvan }}</strong><br>
            {{ $belge->belge_no }}
        </div>
    </header>

    <section class="meta">
        <div>
            <div class="etiket">Belge tarihi</div>
            {{ \Carbon\Carbon::parse($belge->tarih ?? $belge->fis_tarihi ?? $belge->servis_tarihi)->format('d.m.Y') }}
        </div>
        <div>
            <div class="etiket">Cari / müşteri</div>
            {{ $belge->musteri_unvan ?? $belge->aciklama ?? '—' }}
        </div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Ürün / Hizmet</th>
                <th class="sag">Adet</th>
                <th>Birim</th>
                @if ($tur !== 'fis')
                    <th class="sag">Birim fiyat</th>
                @endif
                <th class="sag">KDV</th>
                <th class="sag">KDV dahil</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($satirlar as $satir)
                <tr>
                    <td>{{ $satir->urun_adi }}</td>
                    <td class="sag">{{ $satir->adet }}</td>
                    <td>{{ $satir->birim }}</td>
                    @if ($tur !== 'fis')
                        <td class="sag">₺{{ number_format($satir->birim_fiyat, 2, ',', '.') }}</td>
                    @endif
                    <td class="sag">%{{ $satir->kdv_orani }}</td>
                    <td class="sag">₺{{ number_format($satir->kdv_dahil_tutar, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <section class="toplam">
        <div><span>Ara toplam</span><strong>₺{{ number_format($satirlar->sum('kdv_haric_tutar'), 2, ',', '.') }}</strong></div>
        <div><span>KDV toplamı</span><strong>₺{{ number_format($satirlar->sum('kdv_tutari'), 2, ',', '.') }}</strong></div>
        <div class="genel"><span>GENEL TOPLAM</span><strong>₺{{ number_format($belge->tutar, 2, ',', '.') }}</strong></div>
    </section>

    @if ($tur === 'fatura' && ! $resmiFatura)
        <div class="uyari">PROFORMA BELGE — Mali değeri yoktur. Resmî e-fatura, aktif entegrasyon sağlayıcısının başarılı gönderim sonucunda kesilir.</div>
    @elseif ($tur === 'fatura')
        <div class="uyari">ENTEGRASYONA HAZIR FATURA — Resmî kesim sağlayıcı yanıtıyla doğrulanmalıdır.</div>
    @endif
    <script class="no-print">
        const kanal = document.getElementById('kanal');
        const alici = document.getElementById('alici');
        const ipucu = document.getElementById('kanal-ipucu');
        const telefon = @json($belge->alici_telefon ?? '');
        const email = @json($belge->alici_email ?? '');
        const guncelle = () => {
            const telefonMu = kanal.value !== 'email';
            alici.placeholder = telefonMu ? '05XX XXX XX XX' : 'E-posta adresi';
            if (! alici.value || alici.value === email || alici.value === telefon) alici.value = telefonMu ? telefon : email;
            ipucu.textContent = kanal.value === 'email'
                ? 'E-posta, fiş/belge satırları ve toplamlarıyla birlikte doğrudan gönderilir.'
                : (kanal.value === 'whatsapp'
                    ? 'WhatsApp uygulaması, fiş detayları doldurulmuş şekilde açılır.'
                    : 'SMS uygulaması, fiş özeti doldurulmuş şekilde açılır.');
        };
        kanal.addEventListener('change', guncelle);
        guncelle();
    </script>
</body>
</html>
