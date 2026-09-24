<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $arac->plaka }} · Araç Dijital Kimliği</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#eef2f7;color:#15233a;font-family:Arial,sans-serif}.app{width:min(100%,620px);min-height:100vh;margin:auto;background:#f8fafc;box-shadow:0 0 35px #0f172a18}.head{padding:18px 20px;background:linear-gradient(115deg,#102a55,#1558d8);color:#fff}.head-brand{display:flex;align-items:center;gap:12px;font-size:19px;font-weight:800}.head-icon{display:grid;place-items:center;width:44px;height:44px;border-radius:12px;background:#ffffff1c;font-size:23px}.vehicle{margin:18px;padding:19px;border:1px solid #dce5f0;border-radius:20px;background:#fff}.vehicle small,.vehicle span{display:block;color:#66758a}.vehicle h1{margin:5px 0 3px;font-size:27px;letter-spacing:.02em}.vehicle strong{display:block;margin-top:7px;font-size:17px}.staff-actions{margin:18px;padding:16px;border:1px solid #e8c24a;border-radius:17px;background:#fff9df}.staff-actions h2{margin:0 0 5px;font-size:16px}.staff-actions p{margin:0 0 12px;color:#6e6140;font-size:12px}.staff-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}.staff-grid a{display:grid;place-items:center;min-height:48px;padding:9px;border-radius:11px;text-align:center;text-decoration:none;font-size:13px;font-weight:800}.staff-grid a:first-child{background:#123466;color:#fff}.staff-grid a:last-child{background:#e1b82f;color:#132949}.tabs{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin:18px;padding:5px;border-radius:14px;background:#e9edf3}.tabs a{padding:12px;border-radius:10px;color:#26374d;text-align:center;text-decoration:none;font-weight:800}.tabs a.active{background:#16396f;color:#fff;box-shadow:0 5px 14px #123b7130}.content{padding:0 18px 28px}.content h2{margin:20px 0 12px;font-size:18px}.content-note{margin:-5px 0 15px;color:#6a788c;font-size:12px}.plan,.service-card{margin:9px 0;overflow:hidden;border-radius:13px;background:linear-gradient(110deg,#0b2850,#073d66);color:#fff;box-shadow:0 4px 12px #0e2d531c}.plan summary,.service-head{display:flex;align-items:center;gap:12px;min-height:58px;padding:10px 14px;cursor:pointer;list-style:none}.plan summary::-webkit-details-marker{display:none}.plan-km,.service-km{flex:1}.plan-km b,.service-km b{display:block;font-size:18px}.plan-km small,.service-km small{color:#c6d7ea;font-size:10px;font-weight:700}.status{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:800}.status-icon{display:grid;place-items:center;width:30px;height:30px;border:2px solid #63d6ad;border-radius:50%;color:#63d6ad}.status.pending .status-icon{border-color:#b8c5d5;color:#b8c5d5}.detail{padding:0 14px 14px;color:#e5eef9;font-size:13px}.detail div{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-top:1px solid #ffffff1c}.detail span{color:#b9cbe0}.operation-list{padding:0 14px 13px}.operation-list div{padding:8px 0;border-top:1px solid #ffffff1c;font-size:13px}.operation-list b{display:block}.operation-list small{display:block;margin-top:3px;color:#b9cbe0}.empty{padding:22px;border:1px dashed #bdc9d8;border-radius:14px;background:#fff;color:#718096;text-align:center}.contact{padding:0 18px 28px}.contact a{display:block;padding:14px;border-radius:12px;background:#25a95b;color:#fff;text-align:center;text-decoration:none;font-weight:800}@media(max-width:430px){.vehicle,.staff-actions,.tabs{margin:13px}.content,.contact{padding-left:13px;padding-right:13px}.staff-grid{grid-template-columns:1fr}.head{padding:15px}.vehicle h1{font-size:24px}}
        .service-card summary{display:flex;align-items:center;gap:12px;min-height:58px;padding:10px 14px;cursor:pointer;list-style:none}.service-card summary::-webkit-details-marker{display:none}.expand-icon{font-size:23px;transition:transform .2s}.service-card[open] .expand-icon{transform:rotate(90deg)}.consent,.unlock{margin:18px;padding:22px;border:1px solid #d9e3ef;border-radius:18px;background:#fff}.consent h2,.unlock h2{margin:0 0 7px;font-size:18px}.consent p,.unlock p{color:#66758a;font-size:13px}.consent-option{display:flex;align-items:flex-start;gap:11px;margin:12px 0;padding:14px;border:1px solid #dce5ef;border-radius:12px;background:#f8fafc}.consent-option input{width:20px;height:20px;margin-top:2px}.consent-option strong{display:block;font-size:14px}.consent-option a{display:inline-block;margin-top:5px;color:#1558d8;font-size:12px;font-weight:700}.consent button,.unlock button{width:100%;margin-top:9px;padding:13px;border:0;border-radius:10px;background:#16396f;color:#fff;font-weight:800}.consent-success{margin:18px;padding:12px;border-radius:10px;background:#dcfce7;color:#166534;font-size:13px;font-weight:700}.unlock input{width:100%;padding:13px;border:1px solid #cbd7e5;border-radius:10px;font-size:18px;text-transform:uppercase}.unlock .error{color:#b42318}.photo-groups{padding:0 18px 25px}.photo-group{margin:10px 0;border:1px solid #dce5ef;border-radius:13px;background:#fff}.photo-group summary{padding:13px;font-weight:800;cursor:pointer}.photo-tabs{display:block;padding:0 13px 13px}.photo-box h3{font-size:12px}.photos{display:grid;grid-template-columns:repeat(2,1fr);gap:7px}.photos a{display:block}.photos img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px}.photos small{display:block;color:#74849a;font-size:10px;margin-top:3px}
    </style>
</head>
<body>
<main class="app">
    <header class="head"><div class="head-brand"><span class="head-icon">🚘</span><span>Araç Yönetimi</span></div></header>
    <section class="vehicle">
        <small>Araç Dijital Kimliği</small>
        <h1>{{ $arac->plaka }}</h1>
        <strong>{{ trim(($arac->marka ?? '').' '.($arac->model ?? '')) ?: 'Marka / model belirtilmemiş' }}</strong>
        <span>Model Yılı: {{ $arac->model_yili ?: '-' }} · {{ number_format($arac->kilometre ?? 0,0,',','.') }} KM</span>
        @if($musteri)<span style="margin-top:7px">Araç sahibi: {{ $musteri['ad_soyad'] }}</span>@endif
    </section>

    @php($servisIletisimOnayli=$hizliIslemYetkisi || (bool) ($iletisimIzni?->servis_iletisim_izni))
    @if(session('izin_basarili'))<div class="consent-success">{{ session('izin_basarili') }}</div>@endif
    @unless($servisIletisimOnayli)
    <section class="consent">
        <h2>İletişim onayları</h2>
        <p>Onay verdiğiniz iletişim türleri firma tarafından e-posta, SMS, WhatsApp veya telefon kanallarından yürütülebilir.</p>
        <form method="POST" action="{{ route('qr.servis.iletisim-izni',$arac->qr_token) }}">
            @csrf<input type="hidden" name="ekran" value="{{ request('ekran','servis') }}">
            @if($errors->has('servis_iletisim_izni'))<p class="error">{{ $errors->first('servis_iletisim_izni') }}</p>@endif
            <label class="consent-option"><input type="checkbox" name="servis_iletisim_izni" value="1" @checked($iletisimIzni?->servis_iletisim_izni)><span><strong>Servis iletişimleri için sesli ve yazılı iletişime izin veriyorum.</strong><a href="{{ route('qr.servis.acik-riza',[$arac->qr_token,'servis']) }}">Açık Rıza metnini okuyunuz</a></span></label>
            <label class="consent-option"><input type="checkbox" name="ticari_iletisim_izni" value="1" @checked($iletisimIzni?->ticari_iletisim_izni)><span><strong>Ticari iletişimler için sesli ve yazılı iletişime izin veriyorum.</strong><a href="{{ route('qr.servis.acik-riza',[$arac->qr_token,'ticari']) }}">Açık Rıza metnini okuyunuz</a></span></label>
            <button type="submit">Onayla</button>
        </form>
    </section>
    @endunless

    @if($hizliIslemYetkisi)
        <section class="staff-actions">
            <h2>Servis hızlı işlemleri</h2>
            <p>Bu araç firmanızın kayıtlarıyla eşleşti.</p>
            <div class="staff-grid">
                <a href="{{ route('araclar.qr.show',$arac->qr_token) }}?ekran=servis#kayitlar">Araç servis kayıtları</a>
                <a href="{{ route('servis.kabul',['arac_id'=>$arac->id]) }}">Servis kaydı oluştur</a>
            </div>
        </section>
    @endif

    @if($servisIletisimOnayli && $detayYetkisi)
    <nav class="tabs" aria-label="Araç geçmişi bölümleri">
        <a class="{{ request('ekran','servis') === 'servis' ? 'active' : '' }}" href="?ekran=servis#kayitlar">Servis İşlemleri</a>
        <a class="{{ request('ekran') === 'bakim' ? 'active' : '' }}" href="?ekran=bakim#kayitlar">Bakım</a>
    </nav>

    <section class="content" id="kayitlar">
        @if(request('ekran','servis') === 'bakim')
            <h2>Yapılan Bakımlar</h2>
            <p class="content-note">Yalnızca araç üzerinde tamamlanarak kaydedilmiş bakım işlemleri gösterilir.</p>
            @forelse($periyodikBakimlar as $kayit)
                <details class="service-card"><summary><span class="service-km"><b>{{ number_format($kayit['km'],0,',','.') }} KM</b><small>{{ $kayit['tarih']->format('d.m.Y') }} · {{ $kayit['islemler']->count() }} bakım işlemi</small></span><span class="status"><i class="status-icon">✓</i>Yapıldı</span><span class="expand-icon">›</span></summary><div class="operation-list">@foreach($kayit['islemler'] as $islem)<div><b>{{ $islem->islem_adi }}</b>@if($islem->aciklama)<small>{{ $islem->aciklama }}</small>@endif</div>@endforeach</div></details>
            @empty<div class="empty">Bu araç için henüz bakım kaydı yok.</div>@endforelse
        @else
            <h2>Servis İşlemleri</h2>
            <p class="content-note">Serviste tamamlanan işlemler, bakım kayıtlarından ayrı gösterilir.</p>
            @forelse($servisIslemleri as $kayit)
                <details class="service-card"><summary><span class="service-km"><b>{{ number_format($kayit['km'],0,',','.') }} KM</b><small>{{ $kayit['tarih']->format('d.m.Y') }}@if($kayit['servis_nolari']) · {{ $kayit['servis_nolari'] }}@endif · {{ $kayit['islemler']->count() }} işlem</small></span><span class="status"><i class="status-icon">✓</i>Yapıldı</span><span class="expand-icon">›</span></summary><div class="operation-list">@foreach($kayit['islemler'] as $islem)<div><b>{{ $islem->islem_adi }}</b>@if($islem->aciklama)<small>{{ $islem->aciklama }}</small>@endif</div>@endforeach</div></details>
            @empty<div class="empty">Bu araç için henüz servis işlemi kaydı yok.</div>@endforelse
        @endif
    </section>
    @php($fotoTuru=request('ekran','servis') === 'bakim' ? 'bakim' : 'servis')
    @php($gorunenFotoGruplari=$fotoGruplari->filter(fn($grup) => $grup[$fotoTuru]->isNotEmpty()))
    <section class="photo-groups"><h2>{{$fotoTuru === 'bakim' ? 'Bakım Fotoğrafları' : 'Servis Fotoğrafları'}}</h2>@forelse($gorunenFotoGruplari as $grup)<details class="photo-group"><summary>{{ number_format($grup['km'],0,',','.') }} KM · {{ $grup['tarih']->format('d.m.Y') }} · {{ $grup['servis_no'] }} · {{$grup[$fotoTuru]->count()}} fotoğraf</summary><div class="photo-tabs"><div class="photo-box"><div class="photos">@foreach($grup[$fotoTuru] as $foto)<a href="{{route('qr.servis.fotograf',[$arac->qr_token,$foto])}}" target="_self"><img src="{{route('qr.servis.fotograf',[$arac->qr_token,$foto])}}" alt="{{$foto->aciklama ?: ($fotoTuru === 'bakim' ? 'Bakım fotoğrafı' : 'Servis fotoğrafı')}}"><small>{{$foto->aciklama ?: ($fotoTuru === 'bakim' ? 'Bakım fotoğrafı' : 'Servis fotoğrafı')}}</small></a>@endforeach</div></div></div></details>@empty<div class="empty">Bu bölümde henüz {{$fotoTuru === 'bakim' ? 'bakım' : 'servis'}} fotoğrafı bulunmuyor.</div>@endforelse</section>
    @elseif($servisIletisimOnayli)
    <section class="unlock"><h2>Detaylar için şifreyi girin</h2><p>Servis işlemleri, bakım kayıtları ve fotoğrafları görmek için size gönderilen dört karakterli şifreyi yazın.</p>@if($errors->has('sifre'))<p class="error">{{$errors->first('sifre')}}</p>@endif<form method="POST" action="{{route('qr.servis.sifre',$arac->qr_token)}}">@csrf<input type="hidden" name="ekran" value="{{request('ekran','servis')}}"><input name="sifre" maxlength="4" autocomplete="one-time-code" required placeholder="••••"><button type="submit">Detayları Aç</button></form></section>
    @endif
    @if($servisIletisimOnayli && $whatsappUrl)<footer class="contact"><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener">İlgili Servise WhatsApp Mesajı Gönder</a></footer>@endif
</main>
</body>
</html>
