<!DOCTYPE html>
<html lang="tr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>İZGİOS - Usta QR Menü</title>


<style>


body{

    margin:0;

    padding:20px;

    background:#f3f4f6;

    font-family:Arial, Helvetica, sans-serif;

}



.container{

    max-width:450px;

    margin:auto;

}



.logo{

    text-align:center;

    font-size:26px;

    font-weight:bold;

    margin-bottom:20px;

    color:#111827;

}



.card{

    background:white;

    border-radius:16px;

    padding:20px;

    margin-bottom:15px;

    box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.vehicle{

    text-align:center;

}



.vehicle .plaka{

    font-size:28px;

    font-weight:bold;

    color:#111827;

    margin-bottom:10px;

}



.vehicle .bilgi{

    color:#6b7280;

    font-size:16px;

    margin:5px 0;

}



.owner{

    background:#f9fafb;

    border-radius:10px;

    padding:12px;

    margin-top:15px;

}



.owner-title{

    font-size:13px;

    color:#6b7280;

    margin-bottom:5px;

}



.owner-name{

    font-size:16px;

    font-weight:bold;

}



.btn{

    display:block;

    width:100%;

    box-sizing:border-box;

    text-align:center;

    padding:15px;

    border-radius:12px;

    text-decoration:none;

    font-size:16px;

    font-weight:bold;

    margin-top:12px;

}



.btn-detail{

    background:#2563eb;

    color:white;

}



.btn-new{

    background:#16a34a;

    color:white;

}



.footer{

    text-align:center;

    color:#9ca3af;

    font-size:13px;

    margin-top:20px;

}


</style>


</head>


<body>


<div class="container">


<div class="logo">

İZGİOS

</div>



<div class="card vehicle">


<div class="plaka">

{{ $arac->plaka }}

</div>


<div class="bilgi">

{{ $arac->marka }}

{{ $arac->model }}

</div>


@if($arac->model_yili)

<div class="bilgi">

Model Yılı :
{{ $arac->model_yili }}

</div>

@endif
@if($arac->musteri)


<div class="owner">


<div class="owner-title">

Araç Sahibi

</div>


<div class="owner-name">

{{ $arac->musteri->ad_soyad }}

</div>


<div class="bilgi">

{{ $arac->musteri->telefon }}

</div>


</div>


@endif



</div>





<div class="card">


<a

href="{{ route('qr.servis.show',$arac->qr_token) }}"

class="btn btn-detail"

>

🔧 Servis Detaylarını Gör

</a>





<a

href="{{ route('servis.kabul',['arac'=>$arac->id]) }}"

class="btn btn-new"

>

➕ Yeni Servis Kaydı Aç

</a>



</div>





<div class="footer">

İZGİOS Servis Yönetim Sistemi

</div>




</div>


</body>


</html>