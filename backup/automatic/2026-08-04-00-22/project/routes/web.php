<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MusteriController;
use App\Http\Controllers\AracController;
use App\Http\Controllers\ServisController;


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