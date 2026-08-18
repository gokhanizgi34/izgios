<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $arac->plaka }} | İZGİOS</title>
    <style>
        body{margin:0;background:#081426;color:#eef5ff;font-family:Arial,sans-serif}.app{max-width:620px;margin:auto;min-height:100vh}.brand{padding:20px;text-align:center;background:#0e203b}.brand-name{color:#fff;font-size:31px;font-weight:900;letter-spacing:-2px}.brand-name b{color:#e8bd38}.brand small{display:block;margin-top:4px;color:#9db1cf;font-size:10px;font-weight:700;letter-spacing:.12em}.vehicle{margin:0 16px 16px;padding:18px;border-radius:15px;background:linear-gradient(135deg,#0d3140,#123d4d);border:1px solid rgba(18,207,165,.23)}.vehicle-top{display:flex;gap:12px;align-items:center}.vehicle-icon{display:grid;place-items:center;width:43px;height:43px;border-radius:12px;background:#12cfa5;color:#04271f;font-size:23px}.vehicle h1{margin:0;font-size:22px}.vehicle p{margin:5px 0 0;color:#c2d5ea;font-size:14px}.vehicle-meta{margin:15px 0 0;padding-top:12px;border-top:1px solid rgba(238,245,255,.15);color:#b5c8dc;font-size:13px}.tabs{display:grid;grid-template-columns:1fr 1fr;margin:16px;gap:10px}.tabs a{padding:13px;text-align:center;border-radius:10px;text-decoration:none;background:#132641;color:#bed1ea;font-weight:700}.tabs a.active{background:#12cfa5;color:#04271f}.content{padding:0 16px 25px}.content h2{font-size:18px;margin:20px 0 12px}.year{font-size:22px;margin:22px 0 10px}.record{width:100%;border:0;text-align:left;font:inherit;color:#fff;cursor:pointer;display:grid;grid-template-columns:82px 1fr 24px;background:#10203a;margin:9px 0;border-radius:13px;overflow:hidden}.date{background:#f2c400;color:#182238;text-align:center;padding:15px 6px;font-weight:800}.date b{font-size:25px;display:block}.detail{padding:14px}.detail strong{font-size:20px}.detail small{display:block;color:#b9c9de;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.arrow{display:grid;place-items:center;color:#12cfa5;font-size:26px}.record-detail{display:none;margin:-2px 4px 10px;padding:13px 14px;border:1px solid rgba(18,207,165,.3);border-radius:0 0 12px 12px;background:#0c1b31}.record-detail.open{display:block}.record-detail div{display:flex;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.1);font-size:13px}.record-detail div:last-child{border:0}.record-detail span{color:#9db1cf}.record-detail b{color:#fff;text-align:right}.empty{padding:20px;border-radius:12px;background:#10203a;color:#afc0d8}.contact{padding:18px 16px 30px;text-align:center}.contact a{display:block;padding:15px;border-radius:13px;background:#25d366;color:#fff;text-decoration:none;font-weight:800}
    </style>
</head>
<body>
<main class="app">
    <header class="brand"><div class="brand-name">İZGİ<b>OS</b></div><small>OTO SERVİS OTOMASYONU</small></header>
    <section class="vehicle">
        <div class="vehicle-top"><span class="vehicle-icon">🚗</span><div><h1>{{ $arac->plaka }} · Araç Geçmişi</h1><p>{{ $arac->marka }} {{ $arac->model }} · {{ number_format($arac->kilometre ?? 0,0,',','.') }} KM</p></div></div>
        <div class="vehicle-meta">{{ $arac->model_yili ?: 'Model yılı belirtilmemiş' }} · Müşteri: {{ $musteri['ad_soyad'] ?? 'Kayıtlı müşteri' }}</div>
    </section>
    <nav class="tabs">
        <a class="{{ request('ekran','servis') === 'servis' ? 'active' : '' }}" href="?ekran=servis">Yapılan İşlemler</a>
        <a class="{{ request('ekran') === 'bakim' ? 'active' : '' }}" href="?ekran=bakim">Periyodik Bakım</a>
    </nav>
    <section class="content">
        @if(request('ekran','servis') === 'bakim')
            <h2>Yapılan Periyodik Bakımlar</h2>
            @forelse($periyodikBakimlar as $sira => $kayit)
                @php($servis = $kayit['servis'])
                @php($islem = $kayit['islem'])
                <button type="button" class="record" data-target="bakim-{{ $sira }}"><div class="date"><b>{{ ($servis->servis_tarihi ?? $servis->created_at)->format('d') }}</b>{{ ($servis->servis_tarihi ?? $servis->created_at)->translatedFormat('M') }}</div><div class="detail"><strong>{{ number_format($servis->giris_km ?? 0,0,',','.') }} KM</strong><small>{{ $islem->islem_adi }}</small></div><div class="arrow">›</div></button>
                <div id="bakim-{{ $sira }}" class="record-detail"><div><span>Yapılan bakım</span><b>{{ $islem->islem_adi }}</b></div><div><span>Kilometre</span><b>{{ number_format($servis->giris_km ?? 0,0,',','.') }} KM</b></div><div><span>Tarih</span><b>{{ ($servis->servis_tarihi ?? $servis->created_at)->format('d.m.Y') }}</b></div></div>
            @empty
                <div class="empty">Bu araç için henüz periyodik bakım kaydı yok.</div>
            @endforelse
        @else
            <h2>Yapılan Servis İşlemleri</h2>
            @php($servisYillari = $servisIslemleri->groupBy(fn ($kayit) => ($kayit['servis']->servis_tarihi ?? $kayit['servis']->created_at)->format('Y')))
            @forelse($servisYillari as $yil => $kayitlar)
                <div class="year">{{ $yil }}</div>
                @foreach($kayitlar as $kayit)
                    @php($servis = $kayit['servis'])
                    @php($islemler = $kayit['islemler'])
                    @php($islemAdlari = $islemler->pluck('islem_adi')->filter()->implode(', '))
                    <button type="button" class="record" data-target="servis-{{ $servis->id }}"><div class="date"><b>{{ ($servis->servis_tarihi ?? $servis->created_at)->format('d') }}</b>{{ ($servis->servis_tarihi ?? $servis->created_at)->translatedFormat('M') }}</div><div class="detail"><strong>{{ number_format($servis->giris_km ?? 0,0,',','.') }} KM</strong><small>{{ $islemAdlari }}</small></div><div class="arrow">›</div></button>
                    <div id="servis-{{ $servis->id }}" class="record-detail"><div><span>Yapılan işlem</span><b>{{ $islemAdlari }}</b></div><div><span>Kilometre</span><b>{{ number_format($servis->giris_km ?? 0,0,',','.') }} KM</b></div><div><span>Tarih</span><b>{{ ($servis->servis_tarihi ?? $servis->created_at)->format('d.m.Y') }}</b></div></div>
                @endforeach
            @empty
                <div class="empty">Bu araç için henüz yapılan servis işlemi yok.</div>
            @endforelse
        @endif
    </section>
    @if($whatsappUrl)<footer class="contact"><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener">WhatsApp ile Servise Ulaş</a></footer>@endif
</main>
<script>document.querySelectorAll('[data-target]').forEach(b=>b.addEventListener('click',()=>document.getElementById(b.dataset.target)?.classList.toggle('open')));</script>
</body>
</html>
