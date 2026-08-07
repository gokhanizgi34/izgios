<!DOCTYPE html>
<html lang="tr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>İZGİOS Araç Servis Geçmişi</title>


<style>

*{
    box-sizing:border-box;
}


body{

    margin:0;

    background:#f1f5f9;

    font-family:Arial, Helvetica, sans-serif;

    color:#0f172a;

}



.header{

    background:#111827;

    color:white;

    padding:25px;

    text-align:center;

}



.logo{

    font-size:36px;

    font-weight:800;

}



.logo span{

    color:#eab308;

}



.subtitle{

    margin-top:8px;

    opacity:.8;

}



.container{

    max-width:750px;

    margin:auto;

    padding:20px;

}



.card{

    background:white;

    border-radius:20px;

    padding:20px;

    margin-bottom:20px;

    box-shadow:0 5px 20px rgba(0,0,0,.08);

}



.title{

    font-size:21px;

    font-weight:bold;

    color:#2563eb;

    margin-bottom:20px;

}



.row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:12px 0;

    border-bottom:1px solid #eee;

}



.label{

    color:#64748b;

}



.value{

    font-weight:bold;

}



.bakim-card{

    background:#eff6ff;

    border-left:5px solid #2563eb;

}



.bakim-tarih{

    font-size:25px;

    font-weight:bold;

    color:#1d4ed8;

}



.bakim-text{

    margin-top:8px;

    color:#475569;

}

/*

SERVİS GEÇMİŞİ

*/


.servis-item{

    background:#f8fafc;

    border-radius:15px;

    margin-bottom:15px;

    overflow:hidden;

    border-left:5px solid #2563eb;

}



.servis-baslik{

    padding:18px;

    cursor:pointer;

    display:flex;

    justify-content:space-between;

    align-items:center;

}



.servis-tarih{

    font-size:18px;

    font-weight:bold;

}



.servis-konu{

    color:#475569;

    margin-top:6px;

}



.ok{

    font-size:28px;

    color:#2563eb;

}



.servis-detay{

    display:none;

    background:white;

    padding:20px;

    border-top:1px solid #ddd;

}



.detay-satir{

    margin-bottom:15px;

    line-height:1.5;

}



.detay-baslik{

    font-weight:bold;

    color:#334155;

}




.badge{

    display:inline-block;

    padding:6px 14px;

    background:#2563eb;

    color:white;

    border-radius:20px;

    font-size:13px;

}




/*

FOTOĞRAF GALERİ

*/


.foto-alan{

    display:flex;

    gap:10px;

    flex-wrap:wrap;

    margin-top:15px;

}



.foto-alan img{

    width:95px;

    height:95px;

    object-fit:cover;

    border-radius:12px;

}




.footer{

    text-align:center;

    color:#64748b;

    padding:25px;

}



</style>


</head>


<body>



<div class="header">


<div class="logo">

İZGİ<span>OS</span>

</div>


<div class="subtitle">

Araç Servis Geçmişi

</div>


</div>




<div class="container">



<!-- ARAÇ BİLGİLERİ -->


<div class="card">


<div class="title">

🚗 Araç Bilgileri

</div>



<div class="row">

<span class="label">
Plaka
</span>


<span class="value">

{{ $arac->plaka }}

</span>

</div>




<div class="row">

<span class="label">
Marka
</span>


<span class="value">

{{ $arac->marka }}

</span>

</div>





<div class="row">

<span class="label">
Model
</span>


<span class="value">

{{ $arac->model }}

</span>

</div>





<div class="row">

<span class="label">
Model Yılı
</span>


<span class="value">

{{ $arac->model_yili }}

</span>

</div>



</div>





@if($musteri)


<!-- ARAÇ SAHİBİ -->


<div class="card">


<div class="title">

👤 Araç Sahibi

</div>



<div class="row">


<span class="label">

Ad Soyad

</span>


<span class="value">

{{ $musteri['ad_soyad'] ?? '-' }}

</span>


</div>



<div class="row">


<span class="label">

Telefon

</span>


<span class="value">

{{ $musteri['telefon'] ?? '-' }}

</span>


</div>



</div>


@endif







<!-- BİR SONRAKİ BAKIM -->


@if($sonrakiBakim)


<div class="card bakim-card">


<div class="title">

🔔 Bir Sonraki Bakım

</div>




<div class="bakim-tarih">


{{ 

$sonrakiBakim->sonraki_bakim_tarihi

?

$sonrakiBakim->sonraki_bakim_tarihi->format('d.m.Y')

:

'-'

}}


</div>




<div class="bakim-text">


Bakım Periyodu:

<strong>

{{ $sonrakiBakim->bakim_periyodu ?? '-' }}

</strong>


</div>



</div>


@endif







<!-- SERVİS GEÇMİŞİ -->


<div class="card">


<div class="title">

🔧 Servis Geçmişi

</div>




@if($arac->servisler->count())



@foreach($arac->servisler as $servis)



<div class="servis-item">



<div class="servis-baslik"

onclick="acKapat(this)">



<div>



<div class="servis-tarih">


{{

$servis->servis_tarihi

?

$servis->servis_tarihi->format('d.m.Y')

:

$servis->created_at->format('d.m.Y')

}}


</div>




<div class="servis-konu">


{{

$servis->yapilan_islem

??

$servis->sikayet

??

'Servis İşlemi'

}}



</div>



</div>




<div class="ok">

+

</div>



</div>





<div class="servis-detay">




<div class="detay-satir">


<span class="detay-baslik">

Servis No:

</span>


<br>


{{ $servis->servis_no }}



</div>





<div class="detay-satir">


<span class="detay-baslik">

Durum:

</span>


<br>



<span class="badge">

{{ $servis->durum }}

</span>



</div>





<div class="detay-satir">


<span class="detay-baslik">

Kilometre:

</span>


<br>


{{ $servis->giris_km ?? '-' }} KM



</div>





<div class="detay-satir">


<span class="detay-baslik">

Şikayet:

</span>


<br>


{{ $servis->sikayet ?? '-' }}



</div>





<div class="detay-satir">


<span class="detay-baslik">

Yapılan İşlem:

</span>


<br>


{{ $servis->yapilan_islem ?? '-' }}



</div>





<div class="detay-satir">


<span class="detay-baslik">

Kullanılan Parça:

</span>


<br>


{{ $servis->kullanilan_parca ?? '-' }}



</div>
<!-- SERVİS FOTOĞRAFLARI -->


@if($servis->fotograflar->count())


<div class="detay-satir">


<span class="detay-baslik">

📷 İşlem Fotoğrafları

</span>



<div class="foto-alan">


@foreach($servis->fotograflar as $foto)



<img

src="{{ asset('storage/'.$foto->dosya_yolu) }}"

alt="Servis Fotoğrafı"



>



@endforeach



</div>


</div>


@endif





<div class="detay-satir">


<span class="detay-baslik">

Usta Notu:

</span>


<br>


{{ $servis->usta_notu ?? '-' }}


</div>




</div>


</div>




@endforeach





@else


<p>

Henüz servis kaydı bulunmamaktadır.

</p>



@endif



</div>



</div>





<div class="footer">


İZGİOS Otomotiv Servis Yönetim Sistemi


</div>






<script>


function acKapat(element){


    let detay = element.nextElementSibling;



    if(detay.style.display === "block"){


        detay.style.display="none";


        element.querySelector('.ok').innerHTML="+";


    }

    else{


        detay.style.display="block";


        element.querySelector('.ok').innerHTML="−";


    }


}



</script>




</body>

</html>