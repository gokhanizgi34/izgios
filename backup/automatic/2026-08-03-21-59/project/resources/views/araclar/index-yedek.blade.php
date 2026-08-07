@extends('layouts.app')


@section('content')


<div class="max-w-7xl mx-auto">


{{-- Başlık Kartı --}}

<div class="bg-white rounded-3xl shadow-sm p-8 mb-6">


<div class="flex justify-between items-center">


<div>


<h1 class="text-3xl font-bold text-gray-800">

🚗 Araç Yönetimi

</h1>


<p class="text-gray-500 mt-2">

Müşterilerin araç kayıtlarını yönetin, araç geçmişini takip edin.

</p>


</div>



<a href="{{ route('araclar.create') }}"

class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">


+ Yeni Araç


</a>



</div>


</div>






{{-- Liste Kartı --}}


<div class="bg-white rounded-3xl shadow-sm overflow-hidden">


<div class="p-6 border-b">


<h2 class="text-xl font-bold">

🚘 Araç Listesi

</h2>


<p class="text-gray-500 mt-1">

Kayıtlı araçlar ve müşteri bilgileri

</p>


</div>





<div class="overflow-x-auto">


<table class="w-full">


<thead class="bg-gray-100">


<tr>


<th class="text-left p-5">

Plaka

</th>


<th class="text-left p-5">

Müşteri

</th>


<th class="text-left p-5">

Araç

</th>


<th class="text-left p-5">

Model Yılı

</th>


<th class="text-left p-5">

Kilometre

</th>


<th class="text-left p-5">

İşlemler

</th>


</tr>


</thead>





<tbody>


@forelse($araclar as $arac)


<tr class="border-b hover:bg-gray-50">


<td class="p-5 font-bold">

{{ $arac->plaka }}

</td>



<td class="p-5">


<div class="font-semibold">

{{ $arac->musteri->ad_soyad ?? '-' }}

</div>


@if($arac->musteri)

<div class="text-sm text-gray-500">

TC: {{ $arac->musteri->tc_kimlik_no }}

</div>

@endif


</td>





<td class="p-5">


<div class="font-semibold">

{{ $arac->marka }} {{ $arac->model }}

</div>


<div class="text-sm text-gray-500">

{{ $arac->yakit_tipi }}

</div>


</td>





<td class="p-5">


{{ $arac->model_yili }}


</td>





<td class="p-5">


{{ number_format($arac->kilometre,0,',','.') }} KM


</td>



<td class="p-5">


<div class="flex gap-2">

{{-- Detay --}}

<a href="{{ route('araclar.show',$arac) }}"

class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100"

title="Detay">


👁


</a>





{{-- Düzenle --}}

<a href="{{ route('araclar.edit',$arac) }}"

class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center hover:bg-yellow-100"

title="Düzenle">


✏


</a>






{{-- Hasar Kaydı --}}

<a href="{{ route('araclar.hasar',$arac->id) }}"

class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100"

title="Hasar Kaydı">


🚗


</a>







{{-- Sil --}}

<form method="POST"

action="{{ route('araclar.destroy',$arac) }}"

onsubmit="return confirm('Bu aracı silmek istediğinize emin misiniz?');">


@csrf

@method('DELETE')



<button type="submit"

class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200"

title="Sil">


🗑


</button>


</form>



</div>


</td>



</tr>



@empty


<tr>


<td colspan="6"

class="p-8 text-center text-gray-500">


Henüz kayıtlı araç bulunmuyor.


</td>


</tr>



@endforelse



</tbody>


</table>


</div>


</div>






{{-- Sayfalama --}}

@if(method_exists($araclar,'links'))


<div class="p-6">


{{ $araclar->links() }}


</div>


@endif






</div>


@endsection