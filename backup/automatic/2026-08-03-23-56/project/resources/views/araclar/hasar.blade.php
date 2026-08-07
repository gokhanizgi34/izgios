@extends('layouts.app')


@section('title','Araç Hasar Tespit | İZGİOS')



@section('content')


<div class="izgios-home">


<section class="welcome-panel">


<div class="welcome-content">


<h1>

Hasar Tespit

</h1>


<p>

{{ $arac->plaka }} -
{{ $arac->marka }}
{{ $arac->model }}

</p>


</div>


</section>





<section class="dashboard-box">


<div class="box-header">

<h3>

<i class="bi bi-car-front"></i>

Araç Hasar Kayıtları

</h3>

</div>





<div style="padding:35px;">


<a href="{{ route('araclar.show',$arac->id) }}">

← Araç Kartına Dön

</a>



<hr>


@if($hasarlar->count())


<table width="100%">


<tr>

<th>Parça</th>

<th>Açıklama</th>

<th>Tarih</th>

</tr>



@foreach($hasarlar as $hasar)


<tr>


<td>

{{ $hasar->parca_adi }}

</td>


<td>

{{ $hasar->aciklama }}

</td>


<td>

{{ $hasar->created_at->format('d.m.Y H:i') }}

</td>


</tr>


@endforeach



</table>


@else


<div style="
padding:30px;
background:#F3F4F6;
border-radius:12px;
">

Henüz hasar kaydı bulunmuyor.


</div>


@endif



</div>


</section>



</div>


@endsection