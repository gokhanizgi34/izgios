@php($aktifFirma = auth()->user()?->firmaPersoneli?->firma ?? (session('aktif_firma_id') ? \App\Models\Firma::find(session('aktif_firma_id')) : null))

<header class="izgios-topbar">


    {{-- ================================================= --}}
    {{-- MOBİL MENÜ --}}
    {{-- ================================================= --}}


    <button id="mobile-menu-btn"
            class="mobile-menu-button"
            type="button"
            aria-label="Menüyü aç veya kapat"
            aria-controls="izgios-sidebar"
            aria-expanded="false">


        <span class="hamburger-icon" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </span>


    </button>





    {{-- ================================================= --}}
    {{-- İZGİOS LOGO --}}
    {{-- ================================================= --}}


    <div class="topbar-brand">
        <a href="{{ route('dashboard') }}" class="topbar-company-identity" aria-label="{{ $aktifFirma?->unvan ?? 'İzgiOS' }} çalışma alanı">
            <span class="topbar-company-logo-box">
                @if ($aktifFirma?->logo_yolu)
                    <img src="{{ asset('storage/'.$aktifFirma->logo_yolu) }}" alt="{{ $aktifFirma->unvan }} logosu">
                @else
                    <span class="topbar-logo-text">
                        <span class="top-logo-black">İZGİ</span>
                        <span class="top-logo-gold">OS</span>
                    </span>
                @endif
            </span>
            <span class="topbar-company-copy">
                <small>ÇALIŞMA ALANI</small>
                <strong>{{ $aktifFirma?->unvan ?? 'İzgi Oto Servis Yönetim Sistemi' }}</strong>
            </span>
        </a>
    </div>







    {{-- ================================================= --}}
    {{-- ARAMA --}}
    {{-- ================================================= --}}


    <div class="topbar-search">


        <i class="bi bi-search"></i>



        <input type="text"
               placeholder="Müşteri, plaka, araç veya iş emri ara...">


    </div>
        {{-- ================================================= --}}
    {{-- SAĞ BİLGİ ALANI --}}
    {{-- ================================================= --}}


    <div class="topbar-actions">





        {{-- Bildirim --}}


        <button class="topbar-icon" id="bildirim-ac" type="button" aria-label="Bildirimleri aç" aria-expanded="false">


            <i class="bi bi-bell"></i>


            <span class="notification-count" id="bildirim-sayisi">0</span>


        </button>
        <aside class="topbar-notifications" id="bildirim-paneli" hidden><header><strong>Bildirimler</strong><form method="POST" action="{{ route('bildirimler.okundu') }}">@csrf<button type="submit">Tümünü okundu yap</button></form></header><div id="bildirim-listesi"><p>Bildirimler yükleniyor…</p></div></aside>







        {{-- TARİH + SAAT --}}


        <div class="topbar-date-time">



            <div class="date-item">


                <i class="bi bi-calendar3"></i>


                <span>

                    {{ now()->format('d.m.Y') }}

                </span>


            </div>




            <div class="time-item">


                <i class="bi bi-clock"></i>


                <span id="izgios-clock">

                    {{ now()->format('H:i:s') }}

                </span>


            </div>



        </div>








        {{-- Kullanıcı --}}


        <div class="topbar-user">


            <div class="top-user-avatar">


                <i class="bi bi-person-circle"></i>


            </div>




            <div class="top-user-info">


                <strong>

                {{ auth()->user()?->tamAdi() ?: 'Oturum Açılmadı' }}

                </strong>



                <span>

                {{ auth()->user()?->rolAdi() ?: 'Giriş yapınız' }}

                </span>


            </div>


        </div>



    </div>
    </header>





{{-- ================================================= --}}
{{-- CANLI SAAT --}}
{{-- ================================================= --}}


<script>

document.addEventListener("DOMContentLoaded", function(){


    function updateIzgiosClock(){


        const clock = document.getElementById(
            "izgios-clock"
        );


        if(clock){


            const now = new Date();


            clock.innerHTML =
            now.toLocaleTimeString(
                "tr-TR",
                {
                    hour:"2-digit",
                    minute:"2-digit",
                    second:"2-digit"
                }
            );


        }


    }



    updateIzgiosClock();



    setInterval(
        updateIzgiosClock,
        1000
    );


});

document.addEventListener('DOMContentLoaded',()=>{
    const trigger=document.getElementById('bildirim-ac'),panel=document.getElementById('bildirim-paneli'),count=document.getElementById('bildirim-sayisi'),list=document.getElementById('bildirim-listesi');
    let once=false,lastCount=0,audioContext;
    const escape=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const sound=()=>{try{audioContext??=new(window.AudioContext||window.webkitAudioContext)();const o=audioContext.createOscillator(),g=audioContext.createGain();o.frequency.value=880;g.gain.setValueAtTime(.07,audioContext.currentTime);g.gain.exponentialRampToValueAtTime(.001,audioContext.currentTime+.22);o.connect(g).connect(audioContext.destination);o.start();o.stop(audioContext.currentTime+.23)}catch(_){}};
    const load=async()=>{try{const response=await fetch(@json(route('bildirimler.liste')),{headers:{Accept:'application/json'}});if(!response.ok)return;const data=await response.json();const next=Number(data.okunmamis||0);count.textContent=next>99?'99+':next;count.hidden=next===0;if(once&&next>lastCount)sound();lastCount=next;once=true;list.innerHTML=data.bildirimler.length?data.bildirimler.map(x=>`<a class="topbar-notification ${x.okundu?'':'unread'}" href="${escape(x.url)}"><strong>${escape(x.baslik)}</strong><span>${escape(x.mesaj)}</span><small>${escape(x.tarih)}</small></a>`).join(''):'<p class="topbar-notification-empty">Yeni bildiriminiz yok.</p>'}catch(_){}};
    document.addEventListener('click',()=>{try{audioContext??=new(window.AudioContext||window.webkitAudioContext)();audioContext.resume?.()}catch(_){}},{once:true});trigger?.addEventListener('click',()=>{panel.hidden=!panel.hidden;trigger.setAttribute('aria-expanded',String(!panel.hidden));if(!panel.hidden)load()});document.addEventListener('click',e=>{if(panel&&!panel.hidden&&!panel.contains(e.target)&&!trigger.contains(e.target)){panel.hidden=true;trigger.setAttribute('aria-expanded','false')}});load();setInterval(load,8000);
});

</script>
<style>.topbar-actions{position:relative}.topbar-notifications{position:absolute;z-index:1000;right:190px;top:64px;width:min(390px,calc(100vw - 24px));max-height:520px;overflow:auto;background:#fff;border:1px solid #d9e3ef;border-radius:16px;box-shadow:0 18px 50px #102a5033}.topbar-notifications header{position:sticky;top:0;display:flex;justify-content:space-between;gap:10px;padding:14px;background:#fff;border-bottom:1px solid #e5ebf3}.topbar-notifications header button{border:0;background:none;color:#2465a8;font-size:12px;font-weight:700}.topbar-notification{display:block;padding:12px 14px;border-bottom:1px solid #edf1f5;text-decoration:none;color:#183353}.topbar-notification.unread{background:#eef6ff;border-left:4px solid #e4b92f}.topbar-notification span,.topbar-notification small{display:block;margin-top:3px;color:#657991;font-size:12px}.topbar-notification-empty{padding:24px;text-align:center;color:#657991}@media(max-width:760px){.topbar-notifications{position:fixed;right:12px;left:12px;top:86px;width:auto;max-height:65vh}}</style>
