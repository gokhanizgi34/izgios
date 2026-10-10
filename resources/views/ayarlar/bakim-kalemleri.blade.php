@extends('layouts.app')
@section('title', 'Periyodik Bakım Kalemleri')
@section('content')
<style>
.maintenance-settings{max-width:1050px;margin:auto}.maintenance-head{padding:24px;border-radius:18px;background:linear-gradient(115deg,#102a50,#0f766e);color:#fff}.maintenance-head p{margin:7px 0 0;color:#dff7f0}.maintenance-card{margin-top:18px;padding:22px;border:1px solid #dce6ef;border-radius:16px;background:#fff}.maintenance-list{display:grid;gap:10px;margin-top:18px}.maintenance-row{display:grid;grid-template-columns:90px 1fr 130px;gap:10px;align-items:center;padding:12px;border:1px solid #e2e8f0;border-radius:12px}.maintenance-row input{margin:0}.maintenance-delete{display:flex;gap:7px;align-items:center;color:#b42318;font-weight:700}.maintenance-add{display:grid;grid-template-columns:1fr auto;gap:10px;margin-top:18px;padding-top:18px;border-top:1px solid #e2e8f0}@media(max-width:650px){.maintenance-row{grid-template-columns:70px 1fr}.maintenance-delete{grid-column:1/-1}.maintenance-add{grid-template-columns:1fr}}
</style>
<section class="maintenance-settings container py-4">
  <header class="maintenance-head"><h1 class="h3 mb-0"><i class="bi bi-wrench-adjustable-circle-fill"></i> Periyodik Bakım Kalemleri</h1><p>Bakım seçeneklerini firma bazında yönetin. Geçmiş servis kayıtları bu değişikliklerden etkilenmez.</p></header>
  @if(session('success'))<div class="alert alert-success mt-3">{{session('success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger mt-3">{{$errors->first()}}</div>@endif
  <article class="maintenance-card">
    <form method="GET" action="{{route('ayarlar.bakim-kalemleri')}}"><label class="form-label fw-bold">Firma</label><select class="form-select" name="firma_id" onchange="this.form.submit()">@foreach($firmalar as $firma)<option value="{{$firma->id}}" @selected((int)$firmaId===(int)$firma->id)>{{$firma->gosterim_adi}}</option>@endforeach</select></form>
    @if($firmaId)
    <form method="POST" action="{{route('ayarlar.bakim-kalemleri.kaydet')}}">@csrf<input type="hidden" name="firma_id" value="{{$firmaId}}">
      <div class="maintenance-list">
      @foreach($kalemler as $kod=>$ad)
        <div class="maintenance-row">
          <input type="hidden" name="kalemler[{{$loop->index}}][kod]" value="{{$kod}}">
          <input class="form-control" type="number" min="1" max="999" name="kalemler[{{$loop->index}}][sira]" value="{{$loop->iteration}}" aria-label="Sıra">
          <input class="form-control" name="kalemler[{{$loop->index}}][ad]" value="{{$ad}}" maxlength="120" required aria-label="Bakım kalemi adı">
          <label class="maintenance-delete"><input type="checkbox" name="kalemler[{{$loop->index}}][sil]" value="1"> Listeden çıkar</label>
        </div>
      @endforeach
      </div>
      <div class="maintenance-add"><input class="form-control" name="yeni_ad" maxlength="120" placeholder="Yeni bakım kalemi adı"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-plus-lg"></i> Ekle ve Kaydet</button></div>
      <button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-check2-circle"></i> Firma Bakım Listesini Kaydet</button>
    </form>
    @else<p class="text-muted mt-3">Önce aktif bir firma oluşturun.</p>@endif
    <a class="btn btn-secondary mt-3" href="{{route('ayarlar.index')}}">Ayarlara Dön</a>
  </article>
</section>
@endsection
