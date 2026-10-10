@extends('layouts.app')


@section('title','Servisler | İZGİOS')


@section('content')


<div class="izgios-home">



<section class="welcome-panel">

<div class="welcome-content">


<h1>
🔧 İş Emirleri
</h1>


<p>
Servis kabulden oluşan açık ve tamamlanmış iş emirlerini yönetin.
</p>


</div>

</section>







<section class="dashboard-box">



<div class="box-header" style="
display:flex;
justify-content:space-between;
align-items:center;
">


<h3>

<i class="bi bi-tools"></i>

İş Emirleri

</h3>



<a href="{{ route('servis.kabul') }}"
style="
background:#2563eb;
color:white;
padding:12px 22px;
border-radius:12px;
text-decoration:none;
font-weight:600;
display:flex;
align-items:center;
gap:8px;
">

<i class="bi bi-plus-circle"></i>

<i class="bi bi-clipboard2-plus"></i>

Servis Kabul

</a>



</div>







<div style="
padding:25px;
overflow-x:auto;
">







@if(session('success'))


<div style="
background:#dcfce7;
color:#166534;
padding:15px 20px;
border-radius:12px;
margin-bottom:20px;
">

{{ session('success') }}

</div>


@endif








<table style="
width:100%;
border-collapse:collapse;
background:white;
border-radius:12px;
overflow:hidden;
">





<thead>


<tr style="
background:#f1f5f9;
">



<th style="
padding:15px;
text-align:left;
white-space:nowrap;
">

Servis No

</th>




<th style="
padding:15px;
text-align:left;
white-space:nowrap;
">

Müşteri

</th>




<th style="
padding:15px;
text-align:left;
white-space:nowrap;
">

Plaka

</th>




<th style="
padding:15px;
text-align:left;
white-space:nowrap;
">

Durum

</th>




<th style="
padding:15px;
text-align:right;
white-space:nowrap;
">

Tutar

</th>




<th style="
padding:15px;
text-align:center;
white-space:nowrap;
">

İşlemler

</th>



</tr>


</thead>










<tbody>



@if($servisler->count() > 0)



@foreach($servisler as $servis)



<tr style="
border-bottom:1px solid #e5e7eb;
">





<td style="
padding:15px;
font-weight:600;
">

{{ $servis->servis_no }}

</td>







<td style="
padding:15px;
">


@if($servis->musteri)

{{ $servis->musteri->ad_soyad }}

@else

-

@endif


</td>







<td style="
padding:15px;
">


@if($servis->arac)

{{ $servis->arac->plaka }}

@else

-

@endif


</td>







<td style="
padding:15px;
">



@if($servis->durum == 'Bekliyor')


<span style="
background:#fef3c7;
color:#92400e;
padding:6px 12px;
border-radius:20px;
font-size:13px;
">

{{ $servis->durum }}

</span>



@elseif($servis->durum == 'Tamamlandı')


<span style="
background:#dcfce7;
color:#166534;
padding:6px 12px;
border-radius:20px;
font-size:13px;
">

{{ $servis->durum }}

</span>



@else


<span style="
background:#dbeafe;
color:#1e40af;
padding:6px 12px;
border-radius:20px;
font-size:13px;
">

{{ $servis->durum }}

</span>



@endif



</td>









<td style="
padding:15px;
text-align:right;
font-weight:600;
">


{{ number_format($servis->toplam_tutar,2,',','.') }}

 TL


</td>









<td style="
padding:15px;
text-align:center;
">



<a href="{{ route('servisler.show',$servis->id) }}"
style="
background:#2563eb;
color:white;
padding:8px 14px;
border-radius:8px;
text-decoration:none;
font-size:14px;
">


<i class="bi bi-eye"></i>

Detay


</a>






<a href="{{ route('servisler.edit',$servis->id) }}"
style="
background:#f59e0b;
color:white;
padding:8px 14px;
border-radius:8px;
text-decoration:none;
font-size:14px;
margin-left:5px;
">


<i class="bi bi-pencil"></i>

Düzenle


</a>



</td>





</tr>




@endforeach





@else



<tr>

<td colspan="6"
style="
padding:40px;
text-align:center;
color:#64748b;
">


<i class="bi bi-info-circle"
style="font-size:30px;">
</i>


<br><br>


Henüz servis kaydı bulunmuyor.



</td>

</tr>



@endif





</tbody>





</table>







</div>



</section>





</div>



@endsection
