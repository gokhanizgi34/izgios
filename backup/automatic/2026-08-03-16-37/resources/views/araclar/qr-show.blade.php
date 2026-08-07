@extends('layouts.app')


@section('title','Araç Dijital Kimliği')


@section('content')


<div class="container">


<div class="digital-card">


<h1>

🚗 Araç Dijital Kimliği

</h1>




<div class="vehicle">


<h2>

{{ $arac->plaka }}

</h2>


<h3>

{{ $arac->marka }}

{{ $arac->model }}

</h3>


<p>

Model Yılı:

{{ $arac->model_yili ?? '-' }}

</p>


</div>








<div class="info">


<h3>

Araç Bilgileri

</h3>


<div>

Kilometre:

<strong>

{{ number_format($arac->kilometre ?? 0,0,',','.') }}

KM

</strong>

</div>



</div>









<div class="section">


<h3>

🔧 Servis Geçmişi

</h3>



<p>

Servis kayıtları hazırlanıyor.

</p>


</div>









<div class="section">


<h3>

📷 Yapılan İşlemler

</h3>



<p>

İşlem fotoğrafları ve değişen parçalar burada gösterilecek.

</p>


</div>







</div>


</div>



<style>


.container{

padding:25px;

}



.digital-card{

max-width:600px;

margin:auto;

background:white;

padding:30px;

border-radius:25px;

box-shadow:0 5px 25px rgba(0,0,0,.08);

}



.vehicle{

text-align:center;

padding:25px;

background:#f8fafc;

border-radius:20px;

}



.vehicle h2{

font-size:38px;

margin:0;

}



.vehicle h3{

color:#475569;

}



.section,
.info{

margin-top:25px;

padding:20px;

background:#f8fafc;

border-radius:15px;

}


</style>


@endsection