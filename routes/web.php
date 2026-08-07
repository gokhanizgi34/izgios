<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MusteriController;
use App\Http\Controllers\AracController;
use App\Http\Controllers\ServisController;
use App\Http\Controllers\ServisKabulController;
use App\Models\ServisFotograf;
use App\Http\Controllers\ServisIslemController;
use App\Http\Controllers\QrServisController;

/*
|--------------------------------------------------------------------------
| İZGİ OS
| Web Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Ana Sayfa
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect()->route('dashboard');

});



/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class,'index'])
    ->name('dashboard');



/*
|--------------------------------------------------------------------------
| Müşteriler
|--------------------------------------------------------------------------
*/

Route::resource(
    'musteriler',
    MusteriController::class
)
->parameters([
    'musteriler'=>'musteri'
]);


Route::get(
    'araclar/{arac}/qr',
    [AracController::class, 'qr']
)->name('araclar.qr');

/*
|--------------------------------------------------------------------------
| Araçlar
|--------------------------------------------------------------------------
|
| Tek resource olacak.
|
*/

Route::resource(
    'araclar',
    AracController::class
)
->parameters([
    'araclar'=>'arac'
]);



/*
|--------------------------------------------------------------------------
| Servisler
|--------------------------------------------------------------------------
*/

Route::resource(
    'servisler',
    ServisController::class
);

Route::get(
    '/arac/{token}',
    [AracController::class,'qrShow']
)->name('araclar.qr.show');



/*
|--------------------------------------------------------------------------
| Servis Araç Kabul
|--------------------------------------------------------------------------
*/


Route::get(
    '/servis-kabul',
    [ServisKabulController::class,'create']
)
->name('servis.kabul');





Route::post(
    '/servis-kabul',
    [ServisKabulController::class,'store']
)
->name('servis.kabul.store');







/*
|--------------------------------------------------------------------------
| Servis Kabul Araç Arama
|--------------------------------------------------------------------------
*/


Route::get(
    '/servis-kabul/arac-bul',
    [ServisKabulController::class,'aracBul']
)
->name('servis.arac.bul');







Route::get(
    '/servis-kabul/qr-bul',
    [ServisKabulController::class,'qrBul']
)
->name('servis.qr.bul');


Route::get(
'/qr-servis/{token}',
[QrServisController::class,'show']
)
->name('qr.servis');

/*
|--------------------------------------------------------------------------
| QR Servis Görüntüleme
|--------------------------------------------------------------------------
*/


Route::get(
    '/qr-servis/{token}',
    [QrServisController::class,'show']
)
->name('qr.servis.show');