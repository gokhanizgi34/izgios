@extends('layouts.app')

@section('title', 'Destek Merkezi')

@section('content')
<section class="container py-2 support-conversation-page">
    <div class="page-header">
        <div>
            <h1><i class="bi bi-stars"></i> Derviş Destek Merkezi</h1>
            <p>Derviş ilk yanıtı verir; talep konuşmasına Sistem Yöneticisi doğrudan katılır. Yeni talep ve kullanıcı mesajları yöneticilere e-posta ile bildirilir.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-primary" href="{{ route('sss.index') }}"><i class="bi bi-journal-text"></i> Bilgi Merkezi</a>
            <a class="btn btn-primary" href="{{ route('destek.create') }}"><i class="bi bi-plus-circle"></i> Yeni Destek Talebi</a>
        </div>
    </div>

    <div class="alert alert-light border mb-3 small"><strong>Güvenli çalışma kuralı:</strong> Derviş kod, veri veya sunucuda işlem yapmaz. Teknik değişiklikler yalnız Sistem Yöneticisi onayıyla Geliştirme Merkezi sürecine aktarılır.</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($talepler as $talep)
        <article class="card mb-4 shadow-sm border-0 support-thread">
            <div class="card-header bg-white p-4 border-bottom">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <h2 class="h5 mb-0">{{ $talep->baslik }}</h2>
                            <span class="badge text-bg-primary">{{ ucfirst($talep->kategori) }}</span>
                            <span class="badge text-bg-secondary">{{ str_replace('_', ' ', ucfirst($talep->durum)) }}</span>
                            @if($talep->hata_kodu)<span class="badge text-bg-light border">{{ $talep->hata_kodu }}</span>@endif
                        </div>
                        <small class="text-muted">{{ $talep->kullanici?->tamAdi() ?? 'Kullanıcı' }} · {{ $talep->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    @if(auth()->user()->tamSistemYetkisiVarMi())
                        <form class="d-flex gap-2 align-items-center" method="POST" action="{{ route('destek.durum', $talep) }}">
                            @csrf @method('PATCH')
                            <select class="form-select form-select-sm" name="durum" aria-label="Talep durumu">
                                <option value="acik" @selected($talep->durum === 'acik')>Açık</option>
                                <option value="inceleniyor" @selected($talep->durum === 'inceleniyor')>İnceleniyor</option>
                                <option value="cozuldu" @selected($talep->durum === 'cozuldu')>Çözüldü</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary text-nowrap" type="submit">Durumu Kaydet</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <div class="support-thread__messages" aria-label="Destek konuşması">
                    @forelse($talep->mesajlar->sortBy('id') as $mesaj)
                        @php($tip = $mesaj->gonderen_tipi)
                        <div class="support-bubble support-bubble--{{ $tip }}">
                            <div class="support-bubble__meta">
                                <strong>{{ $tip === 'dervis' ? '✦ Derviş' : ($tip === 'sistem_yoneticisi' ? '◉ Sistem Yöneticisi' : ($mesaj->kullanici?->tamAdi() ?? 'Kullanıcı')) }}</strong>
                                <span>{{ $mesaj->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <p>{{ $mesaj->mesaj }}</p>
                        </div>
                    @empty
                        <div class="support-bubble support-bubble--kullanici"><div class="support-bubble__meta"><strong>{{ $talep->kullanici?->tamAdi() ?? 'Kullanıcı' }}</strong><span>{{ $talep->created_at->format('d.m.Y H:i') }}</span></div><p>{{ $talep->mesaj }}</p></div>
                        @if($talep->ai_cozum)
                            <div class="support-bubble support-bubble--dervis"><div class="support-bubble__meta"><strong>✦ Derviş</strong><span>{{ $talep->updated_at->format('d.m.Y H:i') }}</span></div><p>{{ $talep->ai_cozum }}</p></div>
                        @endif
                    @endforelse
                </div>

                @if($talep->durum === 'ai_yonlendirildi' && is_null($talep->kullanici_geri_bildirimi) && ($talep->user_id === auth()->id() || auth()->user()->tamSistemYetkisiVarMi()))
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <form method="POST" action="{{ route('destek.geri-bildirim', $talep) }}">@csrf @method('PATCH')<input type="hidden" name="sonuc" value="cozuldu"><button class="btn btn-sm btn-success" type="submit"><i class="bi bi-check-circle"></i> Derviş çözümü işe yaradı</button></form>
                        <form method="POST" action="{{ route('destek.geri-bildirim', $talep) }}">@csrf @method('PATCH')<input type="hidden" name="sonuc" value="cozulemedi"><button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-arrow-up-right-circle"></i> Yönetici incelemesi gerekli</button></form>
                    </div>
                @endif

                @if($talep->durum !== 'cozuldu')
                    <form class="support-reply-form mt-4" method="POST" action="{{ route('destek.mesaj', $talep) }}">
                        @csrf
                        <label for="destek-mesaj-{{ $talep->id }}" class="form-label">
                            {{ auth()->user()->tamSistemYetkisiVarMi() ? 'Sistem Yöneticisi yanıtı' : 'Derviş ve Sistem Yöneticisine mesajınız' }}
                        </label>
                        <div class="input-group">
                            <textarea id="destek-mesaj-{{ $talep->id }}" name="mesaj" class="form-control" rows="3" maxlength="3000" required placeholder="Mesajınızı yazın; ekran adresi ve hata kodu varsa ekleyin."></textarea>
                            <button class="btn btn-primary px-4" type="submit"><i class="bi bi-send-fill"></i><span> Gönder</span></button>
                        </div>
                        <small class="text-muted">{{ auth()->user()->tamSistemYetkisiVarMi() ? 'Yanıtınız kullanıcıya bu konuşmada görünür.' : 'Mesajınız Derviş tarafından yanıtlanır ve Sistem Yöneticisine e-posta ile iletilir.' }}</small>
                    </form>
                @else
                    <p class="text-success small mb-0 mt-3"><i class="bi bi-check-circle-fill"></i> Talep çözüldü olarak kapatıldı.</p>
                @endif
            </div>
        </article>
    @empty
        <div class="card"><div class="card-body text-center text-muted py-5">Henüz destek talebi bulunmuyor. Yardıma ihtiyacınız olduğunda yeni talep oluşturabilirsiniz.</div></div>
    @endforelse

    <div class="mt-3">{{ $talepler->links() }}</div>
</section>
@endsection

@push('styles')
<style>
.support-thread__messages{display:grid;gap:12px;max-height:620px;overflow:auto;padding:4px}.support-bubble{max-width:min(820px,92%);padding:14px 16px;border-radius:16px;border:1px solid #dbe5f2;background:#f7faff;color:#102a4d}.support-bubble--kullanici{justify-self:end;background:#eef5ff;border-color:#cbdcff}.support-bubble--dervis{justify-self:start;background:#f3f0ff;border-color:#d8cdfb}.support-bubble--sistem_yoneticisi{justify-self:start;background:#fff8dc;border-color:#eed477}.support-bubble__meta{display:flex;gap:12px;justify-content:space-between;font-size:12px;margin-bottom:7px}.support-bubble__meta span{color:#6d7c96}.support-bubble p{white-space:pre-line;margin:0;line-height:1.55}.support-reply-form .input-group{align-items:stretch}.support-reply-form textarea{min-height:92px;resize:vertical}.support-reply-form .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px}@media(max-width:575px){.support-reply-form .input-group{display:block}.support-reply-form .btn{width:100%;margin-top:10px;height:46px}.support-bubble{max-width:100%}}
</style>
@endpush
