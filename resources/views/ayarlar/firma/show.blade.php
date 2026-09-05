@extends('layouts.app')


@section('title','Firma Kartı')


@section('content')


<div class="container">



<div class="page-header">


<div>

<h1>

🏢 {{ $firma->unvan }}

</h1>


<p>

Firma Kartı

</p>


</div>





<div class="firma-actions-top">


<a href="{{ route('firma.index') }}"
class="btn-firma btn-back">

← Firmalara Dön

</a>



<a href="{{ route('ayarlar.index') }}"
class="btn-firma btn-settings">

⚙ Ayarlara Dön

</a>



<a href="{{ route('firma.edit',$firma->id) }}"
class="btn-firma btn-edit">

✏ Düzenle

</a>



<a href="{{ route('sube.index',$firma->id) }}"
class="btn-firma btn-branch">

🏢 Şubeler

</a>


</div>







<div class="firma-detail-card">





<div class="firma-detail-header">


<h2>

🏢 Firma Bilgileri

</h2>


</div>








<div class="firma-detail-grid">





<div>

<span>
Firma Ünvanı
</span>

<strong>
{{ $firma->unvan }}
</strong>

</div>







<div>

<span>
Vergi No
</span>

<strong>
{{ $firma->vergi_no ?? '-' }}
</strong>

</div>








<div>

<span>
Telefon
</span>

<strong>
{{ $firma->telefon ?? '-' }}
</strong>

</div>








<div>

<span>
E-Mail
</span>

<strong>
{{ $firma->email ?? '-' }}
</strong>

</div>








<div>

<span>
Şube Sayısı
</span>

<strong>
{{ $firma->subeler_count ?? 0 }}
</strong>

</div>







<div>

<span>
Personel Sayısı
</span>

<strong>
{{ $firma->personeller_count ?? 0 }}
</strong>

</div>








<div>

<span>
Durum
</span>


<strong>


@if($firma->aktif)

<span class="aktif">

Aktif

</span>

@else

<span class="pasif">

Pasif

</span>

@endif


</strong>


</div>








<div class="full">


<span>

Adres

</span>


<strong>

{{ $firma->adres ?? '-' }}

</strong>


</div>






</div>









@endsection