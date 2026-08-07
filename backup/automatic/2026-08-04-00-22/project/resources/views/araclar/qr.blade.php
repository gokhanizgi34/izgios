@extends('layouts.app')


@section('title','Araç QR | İZGİ OS')


@section('content')


<div class="container">


<div class="qr-page">


<div class="qr-header no-print">

<h1>
🚗 Araç Dijital Kimliği
</h1>


<p>
İZGİ OS QR Servis Takip Kodu
</p>

</div>



<div class="qr-sticker">




<div class="qr-box">


{!! $qrCode !!}


<p class="qr-info">

Bu QR kod İZGİ OS dijital araç kimliğidir.

</p>


</div>



</div>



<div class="qr-actions no-print">


<button 
onclick="window.print()"
class="btn-print">

🖨 Yazdır

</button>



<a href="{{ route('araclar.show',$arac->id) }}"
class="btn-back">

← Araç Detay

</a>



</div>


</div>


</div>



<style>


.container{

padding:30px;

}



.qr-page{

max-width:500px;

margin:auto;

text-align:center;

}



.qr-header h1{

font-size:30px;

margin-bottom:5px;

}



.qr-header p{

margin-top:0;

}



.qr-sticker{

background:white;

}



.vehicle-card{


padding:25px;

border-radius:20px;

margin:25px 0;

box-shadow:0 5px 20px rgba(0,0,0,.08);


}



.vehicle-card h2{

font-size:36px;

margin:0;

}



.vehicle-card h3{

color:#475569;

}



.qr-box{


background:white;

padding:30px;

border-radius:20px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

display:flex;

flex-direction:column;

align-items:center;


}



.qr-box svg{

width:260px;

height:260px;

}



.qr-info{

font-size:14px;

margin-top:15px;

}



.qr-actions{


display:flex;

justify-content:center;

gap:15px;

margin-top:25px;

}



.btn-print,
.btn-back{


padding:15px 30px;

border-radius:12px;

font-weight:800;

text-decoration:none;

border:none;

cursor:pointer;

}



.btn-print{

background:#2563eb;

color:white;

}



.btn-back{

background:#e2e8f0;

color:#334155;

}





/* SADECE QR STICKER BASILIR */


@media print{


body *{

visibility:hidden;

}



.qr-sticker,
.qr-sticker *{

visibility:visible;

}



.qr-sticker{


position:absolute;

left:0;

top:0;

width:100%;

}



.no-print{

display:none!important;

}



.vehicle-card,
.qr-box{

box-shadow:none;

}



}



</style>



@endsection