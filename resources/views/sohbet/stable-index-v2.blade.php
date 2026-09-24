@extends('layouts.app')

@section('title', 'Firma Genel Sohbeti | İZGİOS')

@section('content')
<style>
    .firma-sohbet { max-width: 1080px; margin: 0 auto; }
    .firma-sohbet-kart { min-height: 680px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #d8e3f1; border-radius: 22px; background: #fff; box-shadow: 0 18px 45px rgba(15, 45, 85, .10); }
    .firma-sohbet-baslik { padding: 22px 26px; color: #fff; background: linear-gradient(115deg, #0d2f5f, #1a5d9f); }
    .firma-sohbet-baslik h1 { margin: 0; font-size: 1.35rem; font-weight: 800; }
    .firma-sohbet-baslik p { margin: 6px 0 0; color: #e2eefc; }
    .firma-sohbet-mesajlar { flex: 1; min-height: 440px; padding: 24px; overflow-y: auto; background-color: #f7fbff; background-image: radial-gradient(#dce9f7 1px, transparent 1px); background-size: 18px 18px; }
    .firma-sohbet-mesaj { width: fit-content; max-width: min(78%, 680px); margin: 0 0 15px; padding: 12px 15px; border: 1px solid #d8e3f1; border-radius: 6px 16px 16px; background: #fff; box-shadow: 0 2px 8px rgba(15, 45, 85, .06); line-height: 1.55; white-space: pre-wrap; word-break: break-word; }
    .firma-sohbet-mesaj--ben { margin-left: auto; border-color: #f2cc55; border-radius: 16px 6px 16px 16px; background: #fff8df; }
    .firma-sohbet-mesaj-kisi { display: block; margin-bottom: 3px; color: #123d71; font-size: .78rem; font-weight: 800; }
    .firma-sohbet-zaman { display: block; margin-top: 6px; color: #718096; font-size: .72rem; }
    .firma-sohbet-yaz { display: flex; gap: 12px; padding: 16px; border-top: 1px solid #d8e3f1; background: #fff; }
    .firma-sohbet-yaz textarea { min-height: 54px; resize: vertical; }
    .firma-sohbet-bos { display: grid; min-height: 440px; place-items: center; padding: 32px; color: #61738b; text-align: center; }
    .sohbet-bildirim-kontrol { border: 1px solid rgba(255,255,255,.45); background: rgba(255,255,255,.12); color: #fff; border-radius: 8px; padding: 7px 10px; font-size: .78rem; font-weight: 700; }
    .sohbet-etiket-notu { margin: 0; padding: 0 16px 12px; color: #61738b; font-size: .75rem; background: #fff; }.sohbet-kanallar{display:flex;gap:8px;overflow-x:auto;padding:10px 0}.sohbet-kanallar a{white-space:nowrap;padding:9px 13px;border:1px solid #d8e3f1;border-radius:999px;background:#fff;color:#365675;text-decoration:none;font-weight:750}.sohbet-kanallar a.active{background:#123d71;color:#fff;border-color:#123d71}.sohbet-yetki-notu{padding:16px;background:#fff7db;color:#715700;border-top:1px solid #edd778;text-align:center;font-weight:700}
    @media (max-width: 640px) { .firma-sohbet { padding: 0 .3rem; } .firma-sohbet-kart { min-height: calc(100vh - 180px); border-radius: 16px; } .firma-sohbet-baslik { padding: 18px; } .firma-sohbet-mesajlar { padding: 15px; } .firma-sohbet-mesaj { max-width: 90%; } .firma-sohbet-yaz { align-items: stretch; flex-direction: column; } .firma-sohbet-yaz .btn { width: 100%; } }
</style>

<main class="container py-4 firma-sohbet">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="h3 mb-1 fw-bold">Firma Sohbeti</h2>
            <p class="text-muted mb-0">Genel ve rol bazlı firma içi iletişim kanalları.</p>
        </div>

        @if (auth()->user()->tamSistemYetkisiVarMi() && $firmalar->isNotEmpty())
            <form method="GET" action="{{ route('sohbet.index') }}" class="d-flex gap-2 align-items-center">
                <label class="visually-hidden" for="sohbet-firma">Firma</label>
                <select id="sohbet-firma" name="firma" class="form-select" onchange="this.form.submit()">
                    <option value="">Firma seçiniz</option>
                    @foreach ($firmalar as $firma)
                        <option value="{{ $firma->id }}" @selected($firmaId === $firma->id)>{{ $firma->unvan }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if($oda)<nav class="sohbet-kanallar" aria-label="Sohbet kanalları">@foreach($odalar as $kanal)<a class="{{ $oda->id===$kanal->id?'active':'' }}" href="{{ route('sohbet.index',['firma'=>$firmaId,'kanal'=>$kanal->tip]) }}"><i class="bi {{ $kanal->tip==='genel'?'bi-people-fill':'bi-hash' }}"></i> {{ $kanal->ad }}</a>@endforeach</nav>@endif
    <section class="firma-sohbet-kart">
        @if ($oda)
            <header class="firma-sohbet-baslik d-flex align-items-start justify-content-between gap-3">
                <div><h1><i class="bi bi-chat-square-text-fill me-2"></i>{{ $oda->ad }} Kanalı</h1>
                <p><span class="badge text-bg-light text-dark me-1">{{ $personelSayisi }}</span> aktif firma kullanıcısı · {{ $oda->tip==='genel'?'Herkes yazabilir':'Yalnız ilgili rol yazabilir' }}</p>
                </div>
                <button class="sohbet-bildirim-kontrol" id="sohbet-ses" type="button"><i class="bi bi-volume-up-fill"></i> Ses: Açık</button>
            </header>

            <div class="firma-sohbet-mesajlar" id="sohbet-mesajlar">
                @forelse ($mesajlar as $mesaj)
                    <article @class(['firma-sohbet-mesaj', 'firma-sohbet-mesaj--ben' => $mesaj->user_id === auth()->id()])>
                        <span class="firma-sohbet-mesaj-kisi">{{ $mesaj->kullanici?->tamAdi() ?? 'Kullanıcı' }} · {{ $mesaj->kullanici?->rolAdi() ?? 'Firma kullanıcısı' }}</span>
                        {{ $mesaj->mesaj }}
                        <time class="firma-sohbet-zaman">{{ $mesaj->created_at->format('d.m.Y H:i') }}</time>
                    </article>
                @empty
                    <div class="firma-sohbet-bos">
                        <div><i class="bi bi-chat-square-text fs-1 d-block mb-2 text-primary"></i>Henüz mesaj yok. Firma içi genel konuşmayı siz başlatın.</div>
                    </div>
                @endforelse
            </div>

            <p class="sohbet-etiket-notu"><i class="bi bi-at"></i> Bir kişiyi etiketlemek için <strong>@Ad Soyad</strong> yazın. Etiketlenen kullanıcıya e-posta bildirimi gider.
                @if($etiketAdlari->isNotEmpty()) <span class="d-block mt-1">Etiketlenebilir kişiler: {{ $etiketAdlari->map(fn($ad) => '@'.$ad)->implode(' · ') }}</span> @endif
            </p>
            @if($yazabilir)<form class="firma-sohbet-yaz" method="POST" action="{{ route('sohbet.mesaj.store', $oda) }}">
                @csrf
                <textarea class="form-control" name="mesaj" maxlength="4000" required placeholder="Firma genel sohbetine mesaj yazın... Örn: @Ayşe Yılmaz size bakabilir mi?"></textarea>
                <button type="submit" class="btn btn-servis-ana px-4"><i class="bi bi-send-fill me-1"></i> Gönder</button>
            </form>@else<div class="sohbet-yetki-notu"><i class="bi bi-lock-fill"></i> Bu kanalı okuyabilirsiniz; mesaj yazma yetkisi {{ $oda->ad }} rolündedir.</div>@endif
        @else
            <div class="firma-sohbet-bos">Sohbeti görüntülemek için önce bir firma seçin.</div>
        @endif
    </section>
</main>

@if ($oda)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kutu = document.getElementById('sohbet-mesajlar');
            const sesButonu = document.getElementById('sohbet-ses');
            const kendiId = {{ auth()->id() }};
            let sonId = {{ $mesajlar->last()?->id ?? 0 }};
            let sesAcik = true;
            let audioContext;

            if (kutu) kutu.scrollTop = kutu.scrollHeight;
            document.addEventListener('click', () => {
                if (!audioContext) audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }, { once: true });
            sesButonu?.addEventListener('click', () => {
                sesAcik = !sesAcik;
                sesButonu.innerHTML = sesAcik ? '<i class="bi bi-volume-up-fill"></i> Ses: Açık' : '<i class="bi bi-volume-mute-fill"></i> Ses: Kapalı';
            });
            const sesCal = () => {
                if (!sesAcik || !audioContext) return;
                [880, 1175].forEach((frekans, index) => {
                    const oscillator = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    oscillator.frequency.value = frekans;
                    oscillator.type = 'square';
                    gain.gain.setValueAtTime(.07, audioContext.currentTime + index * .12);
                    gain.gain.exponentialRampToValueAtTime(.001, audioContext.currentTime + index * .12 + .11);
                    oscillator.connect(gain).connect(audioContext.destination);
                    oscillator.start(audioContext.currentTime + index * .12);
                    oscillator.stop(audioContext.currentTime + index * .12 + .12);
                });
            };
            const mesajEkle = (mesaj) => {
                const kart = document.createElement('article');
                kart.className = 'firma-sohbet-mesaj' + (mesaj.user_id === kendiId ? ' firma-sohbet-mesaj--ben' : '');
                const kisi = document.createElement('span'); kisi.className = 'firma-sohbet-mesaj-kisi'; kisi.textContent = mesaj.ad + ' · ' + mesaj.rol;
                const metin = document.createElement('span'); metin.textContent = mesaj.mesaj;
                const zaman = document.createElement('time'); zaman.className = 'firma-sohbet-zaman'; zaman.textContent = mesaj.tarih;
                kart.append(kisi, metin, zaman); kutu.append(kart);
            };
            const yenileriKontrolEt = async () => {
                try {
                    const yanit = await fetch('{{ route('sohbet.mesajlar', $oda) }}?firma={{ $firmaId }}&son_id=' + sonId, { headers: { Accept: 'application/json' } });
                    if (!yanit.ok) return;
                    const veri = await yanit.json();
                    veri.mesajlar.forEach(mesaj => { mesajEkle(mesaj); sonId = mesaj.id; if (mesaj.user_id !== kendiId) sesCal(); });
                    if (veri.mesajlar.length) kutu.scrollTop = kutu.scrollHeight;
                } catch (_) {}
            };
            setInterval(yenileriKontrolEt, 8000);
        });
    </script>
@endif
@endsection
