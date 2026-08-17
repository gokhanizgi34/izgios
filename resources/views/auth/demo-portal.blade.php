<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İZGİOS | Servis Yönetim Platformu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy:#0b1830; --blue:#2563eb; --gold:#e6b932; --muted:#64748b; }
        * { box-sizing:border-box; }
        body { margin:0; color:#12213b; font-family:Manrope,Arial,sans-serif; background:#f4f7fb; }
        .shell { max-width:1180px; margin:0 auto; padding:22px; }
        .nav { display:flex; align-items:center; justify-content:space-between; gap:18px; }
        .brand { color:var(--navy); font-weight:800; font-size:29px; letter-spacing:-1.5px; text-decoration:none; }
        .brand span { color:var(--gold); }
        .nav-links { display:flex; align-items:center; gap:10px; }
        .nav-links a { color:#42526b; text-decoration:none; font-size:13px; font-weight:700; padding:10px 13px; }
        .nav-links .login { border-radius:10px; background:var(--navy); color:white; }
        .hero { margin-top:22px; overflow:hidden; position:relative; border-radius:26px; padding:68px 54px 48px; color:white; background:linear-gradient(125deg,#081429 0%,#112b5d 53%,#1d4ed8 130%); }
        .hero:after { content:''; position:absolute; width:430px; height:430px; border:55px solid rgba(230,185,50,.15); border-radius:50%; right:-160px; top:-180px; }
        .eyebrow { position:relative; z-index:1; display:inline-block; padding:7px 11px; border:1px solid rgba(255,255,255,.24); border-radius:100px; color:#dbeafe; font-size:11px; font-weight:800; letter-spacing:.7px; }
        h1 { position:relative; z-index:1; max-width:710px; margin:18px 0 14px; font-size:45px; letter-spacing:-2.3px; line-height:1.1; }
        .hero p { position:relative; z-index:1; max-width:600px; margin:0; color:#d6e3fb; line-height:1.75; font-size:15px; }
        .actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:12px; margin-top:27px; }
        .button { display:inline-flex; align-items:center; justify-content:center; padding:13px 17px; border-radius:11px; text-decoration:none; font-size:13px; font-weight:800; }
        .button-primary { color:#17233c; background:var(--gold); }.button-secondary { color:white; border:1px solid rgba(255,255,255,.28); }
        .proof { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:22px; margin-top:40px; color:#d7e4fb; font-size:12px; }.proof b { color:white; }
        .section-title { margin:42px 0 17px; font-size:22px; letter-spacing:-.8px; }.section-title span { color:var(--blue); }
        .benefits { display:grid; grid-template-columns:repeat(3,1fr); gap:15px; }.benefit { padding:22px; border:1px solid #e1e8f2; border-radius:16px; background:white; box-shadow:0 8px 25px rgba(15,23,42,.04); }.badge { width:36px; height:36px; display:grid; place-items:center; border-radius:10px; color:#1d4ed8; background:#eaf1ff; font-size:16px; font-weight:800; }.benefit h2 { margin:14px 0 8px; font-size:16px; }.benefit p { margin:0; color:var(--muted); font-size:13px; line-height:1.65; }
        .showcase { display:grid; grid-template-columns:1.1fr .9fr; gap:22px; margin-top:42px; padding:27px; border-radius:20px; background:white; border:1px solid #e1e8f2; }.dashboard { padding:18px; border-radius:14px; background:#0d1b36; }.dashboard-head { display:flex; justify-content:space-between; color:#dbeafe; font-size:11px; }.dots i { display:inline-block; width:6px; height:6px; margin-left:4px; border-radius:50%; background:#6a7ba0; }.stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:9px; margin-top:16px; }.stat { min-height:64px; padding:10px; border-radius:9px; color:#dce8fb; background:#182b50; font-size:10px; }.stat b { display:block; margin-top:7px; color:white; font-size:19px; }.chart { display:flex; align-items:end; gap:7px; height:88px; margin-top:13px; padding:13px; border-radius:9px; background:#14264a; }.chart i { flex:1; border-radius:3px 3px 0 0; background:#2f75ef; }.showcase-text { align-self:center; }.showcase-text h2 { margin:0 0 10px; font-size:23px; letter-spacing:-.8px; }.showcase-text p { color:var(--muted); font-size:13px; line-height:1.7; }.check { margin-top:12px; color:#334155; font-size:13px; }.check b { color:#1d4ed8; }
        .contact { display:flex; justify-content:space-between; align-items:center; gap:20px; margin:42px 0 18px; padding:24px 28px; border-radius:18px; color:white; background:var(--navy); }.contact h2 { margin:0 0 5px; font-size:19px; }.contact p { margin:0; color:#b8c7df; font-size:13px; }.contact-links { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:9px; }.contact-links a { padding:10px 12px; border:1px solid #405374; border-radius:9px; color:white; text-decoration:none; font-size:12px; font-weight:700; }
        @media (max-width:760px) { .shell{padding:16px}.nav-links a:not(.login){display:none}.hero{padding:44px 25px}.hero h1{font-size:33px}.benefits,.showcase{grid-template-columns:1fr}.contact{align-items:flex-start;flex-direction:column}.contact-links{justify-content:flex-start}.stat-grid{grid-template-columns:repeat(3,1fr)} }
    </style>
</head>
<body>
    <main class="shell">
        <nav class="nav">
            <a class="brand" href="{{ route('login') }}">İZGİ<span>OS</span></a>
            <div class="nav-links">
                <a href="mailto:info@izgios.com">info@izgios.com</a>
                <a class="login" href="{{ route('login') }}">Sisteme Giriş</a>
            </div>
        </nav>

        <section class="hero">
            <span class="eyebrow">OTO SERVİSLER İÇİN DİJİTAL YÖNETİM</span>
            <h1>Servisinizi daha kontrollü, daha hızlı ve daha kârlı yönetin.</h1>
            <p>İZGİOS; servis kabulü, iş emirleri, müşteri ve araç yönetimi, stok, muhasebe ve raporlamayı tek bir kurumsal çalışma alanında birleştirir.</p>
            <div class="actions">
                <a class="button button-primary" href="https://wa.me/905374916936" target="_blank" rel="noopener">WhatsApp'tan demo isteyin</a>
                <a class="button button-secondary" href="tel:+905374916936">0537 491 69 36</a>
            </div>
            <div class="proof"><span><b>Tek giriş</b> ile güvenli erişim</span><span><b>Rol bazlı</b> çalışma ekranları</span><span><b>Firma ve şube</b> yapısına uygun</span></div>
        </section>

        <h2 class="section-title">Operasyonunuzu <span>tek ekranda</span> görün.</h2>
        <section class="benefits">
            <article class="benefit"><div class="badge">01</div><h2>Servis akışı netleşir</h2><p>Plaka ve QR ile araç geçmişine ulaşın; kabulden teslimata tüm işi kayıtsız bırakmayın.</p></article>
            <article class="benefit"><div class="badge">02</div><h2>Her rol işine odaklanır</h2><p>Usta, muhasebe, ofis, yedek parça ve yönetim kendi ihtiyaçlarına göre sade bir ekran görür.</p></article>
            <article class="benefit"><div class="badge">03</div><h2>Yönetim karar alır</h2><p>Firma ve şube bazlı özetler sayesinde operasyonu ve sonuçlarını zamanında takip edin.</p></article>
        </section>

        <section class="showcase">
            <div class="dashboard"><div class="dashboard-head"><span>İZGİOS • Yönetim Paneli</span><span class="dots"><i></i><i></i><i></i></span></div><div class="stat-grid"><div class="stat">Açık iş emri<b>24</b></div><div class="stat">Günlük kabul<b>18</b></div><div class="stat">Stok uyarısı<b>06</b></div></div><div class="chart"><i style="height:36%"></i><i style="height:64%"></i><i style="height:47%"></i><i style="height:82%"></i><i style="height:58%"></i><i style="height:93%"></i><i style="height:72%"></i></div></div>
            <div class="showcase-text"><h2>Servisin tüm ritmi görünür olsun.</h2><p>İZGİOS, büyüyen servislerin dağınık süreçlerini ortak bir düzene taşımak için tasarlandı. Her firma kendi marka deneyimiyle çalışır.</p><div class="check"><b>✓</b> Firma, şube ve kullanıcı bazlı yapı</div><div class="check"><b>✓</b> Yetkili kullanıcılar için kontrollü erişim</div><div class="check"><b>✓</b> Operasyon, stok ve mali süreçler için hazır altyapı</div></div>
        </section>

        <section class="contact">
            <div><h2>İZGİOS'u kendi servisinize göre konuşalım.</h2><p>Demo talebi ve iş ortaklığı için bize doğrudan ulaşabilirsiniz.</p></div>
            <div class="contact-links"><a href="tel:+905374916936">Telefon: 0537 491 69 36</a><a href="https://wa.me/905374916936" target="_blank" rel="noopener">WhatsApp</a><a href="mailto:info@izgios.com">info@izgios.com</a></div>
        </section>
    </main>
</body>
</html>
