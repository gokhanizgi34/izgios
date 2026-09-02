@extends('layouts.app')


@section('title','Araç Detay | İZGİ OS')


@section('content')


<div class="container">

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif


<div class="page-header">


<div>

<h1>
🚗 Araç Detay
</h1>


<p>
Araç dijital kimlik kartı
</p>


</div>






<div class="actions">

<a href="{{ route('servis.kabul', ['arac_id' => $arac->id]) }}" class="btn-service">

<i class="bi bi-clipboard2-plus"></i> Servis Kabule Al

</a>


<a href="{{ route('araclar.edit',$arac->id) }}"
class="btn-edit">

✏ Düzenle

</a>




<a href="{{ route('araclar.qr',$arac->id) }}"
class="btn-qr">

▣ QR Yazdır

</a>




<a href="{{ route('araclar.index') }}"
class="btn-back">

← Araçlara Dön

</a>



</div>


</div>









<div class="card vehicle-head kurumsal-kart">



<div>


<h2>

{{ $arac->plaka }}

</h2>


<h3>

{{ $arac->marka }}

{{ $arac->model }}

</h3>


</div>



</div>









<div class="card kurumsal-kart">


<div class="card-title">

📷 Araç ve Servis Fotoğrafları

</div>

<p class="photo-help">Fotoğraflar ilgili servis kaydına bağlı saklanır. Hatalı çekimi silebilir veya aynı servis kaydına yeniden fotoğraf ekleyebilirsiniz.</p>

@forelse($arac->servisler as $servis)
<section class="vehicle-photo-service">
    <header><div><strong>{{ $servis->servis_no }}</strong><small>{{ optional($servis->servis_tarihi)->format('d.m.Y H:i') ?? $servis->created_at->format('d.m.Y H:i') }}</small></div><span>{{ $servis->fotograflar->count() }} fotoğraf</span></header>
    <div class="vehicle-photo-grid">
        @foreach($servis->fotograflar as $foto)
        <article>
            <a href="{{ asset('storage/'.$foto->dosya_yolu) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$foto->dosya_yolu) }}" alt="Servis fotoğrafı"></a>
            <div><b>{{ $foto->aciklama ?: 'Açıklama girilmemiş' }}</b><small>{{ str_replace('_',' ',mb_strtoupper($foto->kategori ?? 'servis')) }}</small><form method="POST" action="{{ route('servis.fotograf.sil',[$servis,$foto]) }}" onsubmit="return confirm('Bu fotoğraf kalıcı olarak silinsin mi?')">@csrf @method('DELETE')<button type="submit">Sil</button></form></div>
        </article>
        @endforeach
    </div>
    <form class="vehicle-photo-form" method="POST" enctype="multipart/form-data" action="{{ route('servis.fotograf.ekle',$servis) }}">
        @csrf
        <select name="kategori" required><option value="">Fotoğraf amacı</option><option value="ariza_tespit">Arıza / hasar tespiti</option><option value="islem_oncesi">İşlem öncesi</option><option value="islem_sirasi">İşlem sırasında</option><option value="islem_sonrasi">İşlem sonrası</option><option value="parca_eski">Sökülen / eski parça</option><option value="parca_yeni">Takılan / yeni parça</option><option value="bakim">Periyodik bakım</option><option value="teslim">Teslim durumu</option></select>
        <input type="file" name="foto" accept="image/*" capture="environment" required>
        <input name="aciklama" maxlength="500" required placeholder="Fotoğraf açıklaması">
        <button type="submit">Fotoğraf Ekle</button>
    </form>
</section>
@empty
<div class="service-empty">Fotoğraf eklemek için önce bir servis kabul kaydı oluşturun.</div>
@endforelse

</div>





<div class="card kurumsal-kart">


<div class="card-title">

👤 Müşteri Bilgileri

</div>




<div class="info-grid">


<div>

<span>

Ad Soyad

</span>


<strong>

{{ $arac->musteri->ad_soyad ?? '-' }}

</strong>


</div>




<div>

<span>

Telefon

</span>


<strong>

{{ $arac->musteri->telefon ?? '-' }}

</strong>


</div>





<div>

<span>

E-Mail

</span>


<strong>

{{ $arac->musteri->email ?? '-' }}

</strong>


</div>



</div>


</div>









<div class="card kurumsal-kart">


<div class="card-title">

⚙ Araç Teknik Bilgileri

</div>





<div class="info-grid">



<div>

<span>

Marka

</span>


<strong>

{{ $arac->marka }}

</strong>


</div>





<div>

<span>

Model

</span>


<strong>

{{ $arac->model }}

</strong>


</div>





<div>

<span>

Model Yılı

</span>


<strong>

{{ $arac->model_yili ?? '-' }}

</strong>


</div>





<div>

<span>

Kilometre

</span>


<strong>

{{ number_format($arac->kilometre ?? 0,0,',','.') }}

KM

</strong>


</div>



</div>


</div>

<div class="card kurumsal-kart">


<div class="card-title">

⚙ Teknik Detaylar

</div>




<div class="info-grid">



<div>

<span>

Yakıt Tipi

</span>


<strong>

{{ $arac->yakit_tipi ?? '-' }}

</strong>


</div>





<div>

<span>

Vites

</span>


<strong>

{{ $arac->vites ?? '-' }}

</strong>


</div>





<div>

<span>

Şase No

</span>


<strong>

{{ $arac->sase_no ?? '-' }}

</strong>


</div>





<div>

<span>

Motor No

</span>


<strong>

{{ $arac->motor_no ?? '-' }}

</strong>


</div>




</div>


</div>









<div class="card kurumsal-kart">


<div class="card-title">

🔳 Dijital Araç Kimliği

</div>




<div class="qr-box">


<p>

Araç QR kimlik sistemi hazır.

</p>



@if($arac->qr_token)


<div class="token">


{{ $arac->qr_token }}


</div>


@else


<p>

QR oluşturulacak.

</p>


@endif



</div>



</div>









<div class="card kurumsal-kart">


<div class="card-title">

🔧 Servis Geçmişi

</div>





@forelse($arac->servisler as $servis)
<a class="service-history-row" href="{{ route('servisler.show', $servis->id) }}">
    <span><strong>{{ $servis->servis_no }}</strong><small>{{ optional($servis->servis_tarihi)->format('d.m.Y H:i') ?? $servis->created_at->format('d.m.Y H:i') }}</small></span>
    <span>{{ number_format($servis->giris_km ?? 0, 0, ',', '.') }} KM</span>
    <span class="service-state">{{ $servis->durum }}</span>
</a>
@empty
<div class="service-empty">Bu araç için henüz servis kabul veya iş emri kaydı yok. <a href="{{ route('servis.kabul', ['arac_id' => $arac->id]) }}">İlk servis kabulünü başlatın.</a></div>
@endforelse



</div>









</div>

<style>


.container{

padding:25px;

}



.page-header{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}



.page-header h1{

font-size:32px;

font-weight:800;

margin:0;

}




.actions{

display:flex;

gap:10px;

}



.actions a{


height:42px;

padding:0 20px;

display:flex;

align-items:center;

justify-content:center;

border-radius:12px;

font-weight:800;

text-decoration:none;

box-sizing:border-box;

}




.btn-edit{

background:#fef3c7;

color:#92400e;

}



.btn-qr{

background:#ede9fe;

color:#6d28d9;

}

.btn-service{background:#2563eb;color:#fff;}



.btn-back{

background:#e2e8f0;

color:#334155;

}






.card{


background:white;

padding:25px;

border-radius:20px;

margin-bottom:20px;

box-shadow:0 5px 20px rgba(0,0,0,.06);


}




.vehicle-head h2{

font-size:36px;

margin:0;

}




.vehicle-head h3{

color:#64748b;

}





.card-title{

font-size:20px;

font-weight:800;

margin-bottom:20px;

}




.info-grid{


display:grid;

grid-template-columns:repeat(4,1fr);

gap:20px;


}

.info-grid span{


display:block;

font-size:13px;

color:#64748b;


}



.info-grid strong{

font-size:16px;

}





.qr-box{


background:#f8fafc;

padding:25px;

border-radius:15px;


}



.token{


background:white;

padding:15px;

border-radius:10px;

font-family:monospace;

word-break:break-all;


}





.service-empty{


background:#f8fafc;

padding:30px;

border-radius:15px;

line-height:1.7;


}

.service-history-row{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:15px 0;border-bottom:1px solid #e5e7eb;text-decoration:none;color:#1e293b}.service-history-row:last-child{border-bottom:0}.service-history-row small{display:block;margin-top:4px;color:#64748b}.service-state{padding:5px 10px;border-radius:999px;background:#e0f2fe;color:#0369a1;font-size:12px;font-weight:700}
.photo-help{margin-top:-10px;color:#64748b}.vehicle-photo-service{padding:16px 0;border-top:1px solid #e5e7eb}.vehicle-photo-service>header{display:flex;align-items:center;justify-content:space-between;gap:12px}.vehicle-photo-service header small{display:block;color:#64748b}.vehicle-photo-service header>span{padding:5px 9px;border-radius:999px;background:#eef2ff;color:#3730a3;font-size:12px;font-weight:800}.vehicle-photo-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:14px 0}.vehicle-photo-grid article{overflow:hidden;border:1px solid #e2e8f0;border-radius:12px}.vehicle-photo-grid img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover}.vehicle-photo-grid article>div{padding:9px}.vehicle-photo-grid b,.vehicle-photo-grid small{display:block;overflow-wrap:anywhere}.vehicle-photo-grid b{font-size:12px}.vehicle-photo-grid small{margin-top:3px;color:#64748b;font-size:10px}.vehicle-photo-grid form{margin-top:8px}.vehicle-photo-grid button{width:100%;padding:7px;border:1px solid #fecaca;border-radius:7px;background:#fff1f2;color:#be123c;font-weight:800}.vehicle-photo-form{display:grid;grid-template-columns:1fr 1fr 1.4fr auto;gap:8px;padding:12px;border-radius:12px;background:#f8fafc}.vehicle-photo-form input,.vehicle-photo-form select{min-width:0;padding:10px;border:1px solid #cbd5e1;border-radius:8px}.vehicle-photo-form button{padding:10px 16px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:800}





@media(max-width:900px){



.page-header{


flex-direction:column;

align-items:flex-start;

gap:15px;


}



.actions{

flex-wrap:wrap;

}




.info-grid{


grid-template-columns:repeat(2,1fr);

}

.vehicle-photo-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.vehicle-photo-form{grid-template-columns:1fr 1fr}.vehicle-photo-form input[name="aciklama"],.vehicle-photo-form button{grid-column:1/-1}



}





@media(max-width:500px){


.info-grid{


grid-template-columns:1fr;

}



.actions a{


width:100%;

}

.vehicle-photo-grid{grid-template-columns:1fr 1fr}.vehicle-photo-form{grid-template-columns:1fr}.vehicle-photo-form button{width:100%}


}






</style>


@endsection
