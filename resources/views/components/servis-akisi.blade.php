@php($aktifAdim = $aktifAdim ?? 1)
<nav class="servis-akisi" aria-label="Servis işlem akışı">
    @foreach ([1 => ['Müşteri', 'person-vcard'], 2 => ['Araç', 'car-front'], 3 => ['Servis Kabul', 'clipboard2-check'], 4 => ['Tamir ve İş Emri', 'tools']] as $no => [$metin, $ikon])
        <div class="servis-adim {{ $no < $aktifAdim ? 'tamam' : '' }} {{ $no === $aktifAdim ? 'aktif' : '' }}">
            <span><i class="bi bi-{{ $no < $aktifAdim ? 'check-lg' : $ikon }}"></i></span><b>{{ $no }}</b><em>{{ $metin }}</em>
        </div>
    @endforeach
</nav>

<style>
    .servis-akisi{display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin:0 0 1.5rem}.servis-adim{display:flex;align-items:center;gap:.55rem;padding:.75rem .85rem;border:1px solid #dce5f1;border-radius:12px;background:#fff;color:#7688a3}.servis-adim>span{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;background:#eaf0f8}.servis-adim b{font-size:.78rem}.servis-adim em{font-size:.83rem;font-style:normal;font-weight:750}.servis-adim.aktif{border-color:#1f66d9;background:#eef5ff;color:#164da7}.servis-adim.aktif>span{background:#1f66d9;color:#fff}.servis-adim.tamam{border-color:#bfe6dd;color:#087a70}.servis-adim.tamam>span{background:#15856d;color:#fff}.tema-koyu .servis-adim{background:#17233a;border-color:#31415b;color:#b7c5dc}.tema-koyu .servis-adim.aktif{background:#18355e}@media(max-width:850px){.servis-akisi{grid-template-columns:repeat(2,1fr)}}
</style>
