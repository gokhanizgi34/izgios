@extends('layouts.app')

@section('title', 'İletişim ve Hatırlatma Ayarları')

@section('content')
<style>
    .im { max-width: 1240px; }
    .im-head { padding: 26px; border-radius: 18px; color: #fff; background: linear-gradient(115deg, #102a50, #0f766e); }
    .im-head p { max-width: 900px; margin-bottom: 0; color: #e5f4f1; }
    .im-flow { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-top: 18px; }
    .im-flow span { padding: 10px; border: 1px solid #d9e5ef; border-radius: 10px; background: #fff; color: #34506f; font-size: .77rem; font-weight: 750; text-align: center; }
    .im-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 18px; }
    .im-card { display: flex; flex-direction: column; border: 1px solid #dbe5ef; border-radius: 16px; background: #fff; padding: 18px; }
    .im-card__title { display: flex; gap: 10px; align-items: center; }
    .im-card__title i { display: grid; width: 36px; height: 36px; place-items: center; border-radius: 10px; background: #fff5cd; color: #9b7100; }
    .im-card h2 { margin: 0; font-size: 1rem; font-weight: 800; }
    .im-card p { min-height: 42px; margin: 12px 0; color: #64748b; font-size: .84rem; }
    .im-switches { display: grid; gap: 9px; margin: 4px 0 14px; }
    .im-switches label { display: flex; align-items: center; gap: 8px; font-size: .85rem; font-weight: 700; }
    .im-card textarea { min-height: 110px; resize: vertical; }
    .im-card label:not(.im-switches label) { margin-top: 8px; color: #2c4768; font-size: .8rem; font-weight: 750; }
    .tema-koyu .im-card, .tema-koyu .im-flow span { border-color: #31415b; background: #17233a; color: #e9f1ff; }
    .tema-koyu .im-card p, .tema-koyu .im-card label:not(.im-switches label) { color: #bdcbe0; }
    @media (max-width: 1050px) { .im-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .im-flow { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 700px) { .im-grid, .im-flow { grid-template-columns: 1fr; } }
</style>

<section class="container im py-4">
    <header class="im-head">
        <h1 class="h3"><i class="bi bi-send-check-fill me-2"></i>İletişim ve Hatırlatma Merkezi</h1>
        <p>Her iletişim olayı için WhatsApp, SMS ve e-posta kanallarını ayrı ayrı veya birlikte seçin. Tercihler firma bazında kaydedilir; dış gönderim için ilgili kanalın sağlayıcı/API bağlantısı ayrıca tamamlanmalıdır.</p>
    </header>

    <div class="im-flow" aria-label="İletişim süreç akışı">
        @foreach ($vars as $akim)
            <span>{{ $akim[0] }}</span>
        @endforeach
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if (auth()->user()->tamSistemYetkisiVarMi())
        <form method="GET" action="{{ route('ayarlar.iletisim') }}" class="mt-3">
            <label class="form-label" for="iletisim-firma">Firma</label>
            <select id="iletisim-firma" name="firma_id" class="form-select" onchange="this.form.submit()">
                @foreach ($firmalar as $firma)
                    <option value="{{ $firma->id }}" @selected($firmaId === $firma->id)>{{ $firma->unvan }}</option>
                @endforeach
            </select>
        </form>
    @endif

    <form method="POST" action="{{ route('ayarlar.iletisim.kaydet', ['firma_id' => $firmaId]) }}">
        @csrf
        <div class="im-grid">
            @foreach ($vars as $key => [$baslik, $aciklama, $ikon, $varsayilanSablon])
                @php($ayar = $ayarlar->get($key))
                <article class="im-card">
                    <div class="im-card__title"><i class="bi {{ $ikon }}"></i><h2>{{ $baslik }}</h2></div>
                    <p>{{ $aciklama }}</p>

                    <div class="im-switches">
                        <label><input type="checkbox" name="{{ $key }}_aktif" @checked($ayar?->aktif ?? true)> Otomasyonu etkinleştir</label>
                        <label><input type="checkbox" name="{{ $key }}_whatsapp" @checked($ayar?->whatsapp)> <i class="bi bi-whatsapp text-success"></i> WhatsApp</label>
                        <label><input type="checkbox" name="{{ $key }}_sms" @checked($ayar?->sms)> <i class="bi bi-chat-dots-fill text-primary"></i> SMS</label>
                        <label><input type="checkbox" name="{{ $key }}_email" @checked($ayar?->email)> <i class="bi bi-envelope-fill text-warning"></i> E-posta</label>
                    </div>

                    <label for="{{ $key }}_saat">Planlı gönderim saati</label>
                    <input id="{{ $key }}_saat" class="form-control" type="time" name="{{ $key }}_saat" value="{{ substr($ayar?->gonderim_saati ?? '09:00', 0, 5) }}">

                    <label for="{{ $key }}_sablon">Varsayılan mesaj şablonu</label>
                    <textarea id="{{ $key }}_sablon" class="form-control" name="{{ $key }}_sablon" placeholder="{musteri_adi}, {plaka}, {firma_adi}, {servis_no}">{{ $ayar?->sablon ?? $varsayilanSablon }}</textarea>
                </article>
            @endforeach
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a class="btn btn-secondary" href="{{ auth()->user()->tamSistemYetkisiVarMi() ? route('ayarlar.index') : route('dashboard') }}">{{ auth()->user()->tamSistemYetkisiVarMi() ? 'Ayarlara Dön' : 'Firma Paneline Dön' }}</a>
            <button class="btn btn-servis-ana" type="submit"><i class="bi bi-save me-1"></i>Tüm tercihleri kaydet</button>
        </div>
    </form>

    <article class="card mt-4">
        <div class="card-body table-responsive">
            <h2 class="h5">Gönderim kayıtları</h2>
            <table class="table align-middle">
                <thead><tr><th>Süreç</th><th>Kanal</th><th>Alıcı</th><th>Durum</th><th>Tarih</th></tr></thead>
                <tbody>
                    @forelse ($loglar as $log)
                        <tr>
                            <td>{{ $vars[$log->mesaj_grubu][0] ?? str_replace('_', ' ', ucfirst($log->mesaj_grubu)) }}</td>
                            <td>{{ strtoupper($log->kanal) }}</td>
                            <td>{{ $log->alici_maskeli }}</td>
                            <td>{{ $log->durum }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Henüz gönderim kaydı yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>
@endsection
