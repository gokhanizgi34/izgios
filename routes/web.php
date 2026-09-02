<?php


use Illuminate\Support\Facades\Route;



use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MusteriController;
use App\Http\Controllers\AracController;
use App\Http\Controllers\ServisController;
use App\Http\Controllers\ServisIslemController;
use App\Http\Controllers\ServisKabulController;
use App\Http\Controllers\QrServisController;
use App\Http\Controllers\KullaniciController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AyarController;
use App\Http\Controllers\SistemHataController;
use App\Http\Controllers\DestekController;
use App\Http\Controllers\SohbetController;
use App\Http\Controllers\BildirimController;
use App\Http\Controllers\SifreYonetimController;
use App\Http\Controllers\TicariController;
use App\Http\Controllers\MuhasebeMerkeziController;
use App\Http\Controllers\GenelMuhasebeController;
use App\Http\Controllers\MuhasebeAsistanController;
use App\Http\Controllers\DepoController;
use App\Http\Controllers\StokKaynakController;
use App\Http\Controllers\HesapController;
use App\Http\Controllers\IkController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\OperasyonController;
use App\Http\Controllers\IletisimAyarController;
use App\Http\Controllers\CiktiController;
use App\Http\Controllers\SilmeDenetimController;
use App\Http\Controllers\GorselOkumaController;

use App\Http\Controllers\FirmaYonetimController;
use App\Http\Controllers\SubeController;





/*
|--------------------------------------------------------------------------
| ANA SAYFA
|--------------------------------------------------------------------------
*/


Route::get('/', function () {

    return redirect()
        ->route('dashboard');

});







/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/


Route::get(
    '/dashboard',
    [DashboardController::class,'index']
)
->name('dashboard');

Route::get('/ticari', [MuhasebeMerkeziController::class, 'index'])->name('ticari.index');
Route::get('/ticari/genel-muhasebe', [GenelMuhasebeController::class, 'index'])->name('ticari.genel-muhasebe');
Route::get('/ticari/genel-muhasebe/mizan/excel', [GenelMuhasebeController::class, 'mizanExcel'])->name('ticari.genel-muhasebe.mizan.excel');
Route::post('/ticari/genel-muhasebe/hesaplar', [GenelMuhasebeController::class, 'hesapKaydet'])->name('ticari.genel-muhasebe.hesap');
Route::post('/ticari/genel-muhasebe/donemler', [GenelMuhasebeController::class, 'donemKaydet'])->name('ticari.genel-muhasebe.donem');
Route::post('/ticari/genel-muhasebe/boyutlar', [GenelMuhasebeController::class, 'boyutKaydet'])->name('ticari.genel-muhasebe.boyut');
Route::post('/ticari/genel-muhasebe/yevmiye', [GenelMuhasebeController::class, 'yevmiyeKaydet'])->name('ticari.genel-muhasebe.yevmiye');
Route::get('/ticari/cari-hesaplar', [MuhasebeMerkeziController::class, 'cariler'])->name('ticari.cari');
Route::get('/ciktilar/{tur}/{id}', [CiktiController::class, 'yazdir'])->name('ciktilar.yazdir');
Route::get('/ciktilar/{tur}/{id}/excel', [CiktiController::class, 'excel'])->name('ciktilar.excel');
Route::post('/ciktilar/{tur}/{id}/gonder', [CiktiController::class, 'gonder'])->name('ciktilar.gonder');
Route::post('/ticari/cari-hesaplar', [MuhasebeMerkeziController::class, 'cariKaydet'])->name('ticari.cari.kaydet');
Route::put('/ticari/cari-hesaplar/{cari}', [MuhasebeMerkeziController::class, 'cariGuncelle'])->name('ticari.cari.guncelle');
Route::delete('/ticari/cari-hesaplar/{cari}', [MuhasebeMerkeziController::class, 'cariSil'])->name('ticari.cari.sil');
Route::get('/ticari/muhasebe-fisleri', [MuhasebeMerkeziController::class, 'fisler'])->name('ticari.fisler');
Route::post('/ticari/muhasebe-fisleri', [TicariController::class, 'fisKaydet'])->name('ticari.fisler.kaydet');
Route::put('/ticari/muhasebe-fisleri/{fis}', [MuhasebeMerkeziController::class, 'fisGuncelle'])->name('ticari.fisler.guncelle');
Route::delete('/ticari/muhasebe-fisleri/{fis}', [MuhasebeMerkeziController::class, 'fisSil'])->name('ticari.fisler.sil');
Route::get('/ticari/entegrasyonlar', [TicariController::class, 'apiAyarlari'])->name('ticari.entegrasyonlar');
Route::get('/ticari/{tur}', [MuhasebeMerkeziController::class, 'belgeler'])->whereIn('tur',['teklif','fatura'])->name('ticari.belgeler');
Route::post('/ticari/{tur}', [MuhasebeMerkeziController::class, 'belgeKaydet'])->whereIn('tur',['teklif','fatura'])->name('ticari.belge.kaydet');
Route::put('/ticari/{tur}/{belge}', [MuhasebeMerkeziController::class, 'belgeGuncelle'])->whereIn('tur',['teklif','fatura'])->name('ticari.belge.guncelle');
Route::delete('/ticari/{tur}/{belge}', [MuhasebeMerkeziController::class, 'belgeSil'])->whereIn('tur',['teklif','fatura'])->name('ticari.belge.sil');
Route::post('/ticari/asistan', [MuhasebeAsistanController::class, 'yanitla'])->name('ticari.asistan');
Route::post('/asistan/yanitla', [MuhasebeAsistanController::class, 'yanitla'])->name('asistan.yanitla');
Route::view('/sss', 'sss.index')->name('sss.index');
Route::get('/ayarlar/api-entegrasyonlari', [TicariController::class, 'apiAyarlari'])->name('ticari.api');
Route::post('/ayarlar/api-entegrasyonlari', [TicariController::class, 'apiKaydet'])->name('ticari.api.kaydet');
Route::post('/ayarlar/api-entegrasyonlari/email-test', [TicariController::class, 'apiEmailTest'])->name('ticari.api.email-test');
Route::post('/ayarlar/api-entegrasyonlari/yapay-zeka', [TicariController::class, 'merkeziYapayZekaKaydet'])->name('ticari.api.yapay-zeka');
Route::post('/ayarlar/api-entegrasyonlari/sistem-email', [TicariController::class, 'merkeziEmailKaydet'])->name('ticari.api.sistem-email');
Route::post('/ayarlar/api-entegrasyonlari/sistem-email-test', [TicariController::class, 'merkeziEmailTest'])->name('ticari.api.sistem-email-test');
Route::post('/gorsel-okuma', [GorselOkumaController::class, 'oku'])->name('gorsel.okuma');
Route::redirect('/yapay-zeka-kontrol', '/ayarlar/api-entegrasyonlari')->name('yapayzeka.index');
Route::get('/ayarlar/iletisim-merkezi', [IletisimAyarController::class, 'index'])->name('ayarlar.iletisim');
Route::post('/ayarlar/iletisim-merkezi', [IletisimAyarController::class, 'kaydet'])->name('ayarlar.iletisim.kaydet');
Route::get('/depo', [DepoController::class, 'index'])->name('depo.index');
Route::get('/depo/parca-onerileri', [DepoController::class, 'parcaOnerileri'])->name('depo.parca.onerileri');
Route::post('/depo/parca', [DepoController::class, 'parcaKaydet'])->name('depo.parca');
Route::post('/depo/hareket', [DepoController::class, 'hareketKaydet'])->name('depo.hareket');
Route::get('/depo/barkod', [DepoController::class, 'barkod'])->name('depo.barkod');
Route::post('/depo/depolar', [DepoController::class, 'depoKaydet'])->name('depo.depo.kaydet');
Route::patch('/depo/depolar/{depo}', [DepoController::class, 'depoGuncelle'])->name('depo.depo.guncelle');
Route::delete('/depo/depolar/{depo}', [DepoController::class, 'depoSil'])->name('depo.depo.sil');
Route::post('/depo/raflar', [DepoController::class, 'rafKaydet'])->name('depo.raf.kaydet');
Route::patch('/depo/raflar/{raf}', [DepoController::class, 'rafGuncelle'])->name('depo.raf.guncelle');
Route::delete('/depo/raflar/{raf}', [DepoController::class, 'rafSil'])->name('depo.raf.sil');
Route::post('/depo/raf-adresle', [DepoController::class, 'rafAta'])->name('depo.raf.ata');
Route::patch('/depo/parcalar/{parca}', [DepoController::class, 'parcaGuncelle'])->name('depo.parca.guncelle');
Route::delete('/depo/parcalar/{parca}', [DepoController::class, 'parcaSil'])->name('depo.parca.sil');
Route::post('/depo/urun-kaynaklari', [StokKaynakController::class, 'kaydet'])->name('depo.kaynak.kaydet');
Route::post('/depo/urun-kaynaklari/xml', [StokKaynakController::class, 'xmlAktar'])->name('depo.kaynak.xml');
Route::post('/depo/urun-kaynaklari/{kaynak}/test', [StokKaynakController::class, 'test'])->name('depo.kaynak.test');
Route::post('/depo/urun-kaynaklari/{kaynak}/senkronize-et', [StokKaynakController::class, 'senkronizeEt'])->name('depo.kaynak.senkronize');
Route::get('/operasyon/randevular', [OperasyonController::class, 'randevular'])->name('operasyon.randevular');
Route::post('/operasyon/randevular', [OperasyonController::class, 'randevuKaydet'])->name('operasyon.randevular.kaydet');
Route::patch('/operasyon/randevular/{randevu}', [OperasyonController::class, 'randevuGuncelle'])->name('operasyon.randevular.guncelle');
Route::delete('/operasyon/randevular/{randevu}', [OperasyonController::class, 'randevuSil'])->name('operasyon.randevular.sil');
Route::post('/operasyon/randevular/{randevu}/servise-al', [OperasyonController::class, 'randevuyuServiseAl'])->name('operasyon.randevular.servise-al');
Route::get('/operasyon/sigorta-kasko', [OperasyonController::class, 'sigorta'])->name('operasyon.sigorta');
Route::post('/operasyon/sigorta-kasko', [OperasyonController::class, 'sigortaKaydet'])->name('operasyon.sigorta.kaydet');
Route::get('/raporlar', [RaporController::class, 'index'])->name('raporlar.index');
Route::get('/sistem/silme-kayitlari', [SilmeDenetimController::class, 'index'])->name('sistem.silme-kayitlari');
Route::post('/raporlar/al', [RaporController::class, 'al'])->name('raporlar.al');







/*
|--------------------------------------------------------------------------
| MÜŞTERİLER
|--------------------------------------------------------------------------
*/


Route::resource(
    'musteriler',
    MusteriController::class
)
->parameters([

    'musteriler'=>'musteri'

]);








/*
|--------------------------------------------------------------------------
| ARAÇLAR
|--------------------------------------------------------------------------
*/


Route::resource(
    'araclar',
    AracController::class
)
->parameters([

    'araclar'=>'arac'

]);



Route::get(
    'araclar/{arac}/qr',
    [AracController::class,'qr']
)
->name('araclar.qr');



Route::get(
    '/arac/{token}',
    [QrServisController::class,'show']
)
->name('araclar.qr.show');








/*
|--------------------------------------------------------------------------
| SERVİS
|--------------------------------------------------------------------------
*/


Route::resource(
    'servisler',
    ServisController::class
);

Route::get('/servisler/{servis}/islem', [ServisIslemController::class, 'show'])->name('servis.islem');
Route::post('/servisler/{servis}/uzerine-al', [ServisIslemController::class, 'uzerineAl'])->name('servis.uzerine.al');
Route::post('/servisler/{servis}/durum', [ServisIslemController::class, 'durumGuncelle'])->name('servis.islem.durum');
Route::patch('/servisler/{servis}/durum-notlari/{not}', [ServisIslemController::class, 'durumNotuGuncelle'])->name('servis.durum-notu.guncelle');
Route::delete('/servisler/{servis}/durum-notlari/{not}', [ServisIslemController::class, 'durumNotuSil'])->name('servis.durum-notu.sil');
Route::post('/servisler/{servis}/hatirlatma', [ServisIslemController::class, 'hatirlatmaGuncelle'])->name('servis.hatirlatma.guncelle');
Route::post('/servisler/{servis}/islemler', [ServisIslemController::class, 'islemEkle'])->name('servis.islem.ekle');
Route::post('/servisler/{servis}/periyodik-bakimlar', [ServisIslemController::class, 'periyodikBakimEkle'])->name('servis.periyodik-bakim.ekle');
Route::patch('/servisler/{servis}/islemler/{islem}', [ServisIslemController::class, 'islemGuncelle'])->name('servis.islem.guncelle');
Route::delete('/servisler/{servis}/islemler/{islem}', [ServisIslemController::class, 'islemSil'])->name('servis.islem.sil');
Route::post('/servisler/{servis}/parcalar', [ServisIslemController::class, 'parcaEkle'])->name('servis.parca.ekle');
Route::post('/servisler/{servis}/hasarlar', [ServisIslemController::class, 'hasarEkle'])->name('servis.hasar.ekle');
Route::post('/servisler/{servis}/fotograflar', [ServisIslemController::class, 'fotografEkle'])->name('servis.fotograf.ekle');
Route::delete('/servisler/{servis}/fotograflar/{fotograf}', [ServisIslemController::class, 'fotografSil'])->name('servis.fotograf.sil');








/*
|--------------------------------------------------------------------------
| SERVİS KABUL
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
->name('qr.servis.show');
Route::post('/qr-servis/{token}/sifre', [QrServisController::class, 'sifreDogrula'])->middleware('throttle:10,1')->name('qr.servis.sifre');
Route::post('/qr-servis/{token}/iletisim-izni', [QrServisController::class, 'iletisimIzniKaydet'])->middleware('throttle:10,1')->name('qr.servis.iletisim-izni');
Route::get('/qr-servis/{token}/acik-riza/{tur}', [QrServisController::class, 'acikRizaMetni'])->whereIn('tur', ['servis', 'ticari'])->name('qr.servis.acik-riza');
Route::get('/qr-servis/{token}/fotograflar/{fotograf}', [QrServisController::class, 'fotograf'])->name('qr.servis.fotograf');

/*
|--------------------------------------------------------------------------
| KULLANICI YÖNETİMİ
|--------------------------------------------------------------------------
*/


Route::resource(
    'kullanicilar',
    KullaniciController::class
)
->parameters([

    'kullanicilar'=>'kullanici'

])
->except([

    'show',
    'destroy'

]);

Route::get('/kullanicilar/aktifler', [KullaniciController::class, 'aktifler'])
    ->name('kullanicilar.aktifler');

Route::get('/kullanicilar/pasifler', [KullaniciController::class, 'pasifler'])
    ->name('kullanicilar.pasifler');




Route::patch(
    '/kullanicilar/{kullanici}/pasif',
    [KullaniciController::class,'pasifYap']
)
->name('kullanicilar.pasif');



Route::patch(
    '/kullanicilar/{kullanici}/aktif',
    [KullaniciController::class,'aktifYap']
)
->name('kullanicilar.aktif');








/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/


Route::get(
    '/login',
    [LoginController::class,'showLogin']
)
->name('login');

Route::get('/demo', [LoginController::class, 'demo'])
    ->name('demo');



Route::post(
    '/login',
    [LoginController::class,'login']
)
->name('login.post');



Route::post(
    '/logout',
    [LoginController::class,'logout']
)
->name('logout');

Route::get('/hesabim/profil', [HesapController::class, 'profil'])->name('hesap.profil');
Route::put('/hesabim/profil', [HesapController::class, 'profilGuncelle'])->name('hesap.profil.update');
Route::get('/hesabim/tercihler', [HesapController::class, 'tercihler'])->name('hesap.tercihler');
Route::put('/hesabim/tercihler', [HesapController::class, 'tercihGuncelle'])->name('hesap.tercihler.update');

Route::get('/sifremi-unuttum', [SifreYonetimController::class, 'talepFormu'])->name('password.request');
Route::post('/sifremi-unuttum', [SifreYonetimController::class, 'talepOlustur'])->name('password.email');
Route::get('/sifre-sifirla/{token}', [SifreYonetimController::class, 'sifirlaFormu'])->name('password.reset');
Route::post('/sifre-sifirla', [SifreYonetimController::class, 'sifirla'])->name('password.update');
Route::get('/hesabim/sifre', [SifreYonetimController::class, 'kendiSifreFormu'])->name('hesap.sifre');
Route::post('/hesabim/sifre', [SifreYonetimController::class, 'kendiSifreGuncelle'])->name('hesap.sifre.update');
Route::get('/ayarlar/ik/sifre-talepleri', [SifreYonetimController::class, 'ikTalepleri'])->name('ik.sifre.talepleri');
Route::get('/ayarlar/ik', [IkController::class, 'index'])->name('ik.index');
Route::post('/ayarlar/ik/ozluk', [IkController::class, 'ozlukKaydet'])->name('ik.ozluk.kaydet');
Route::post('/ayarlar/ik/bordro', [IkController::class, 'bordroKaydet'])->name('ik.bordro.kaydet');
Route::get('/ayarlar/ik/personel/{user}/puantaj-qr', [IkController::class, 'puantajQr'])->name('ik.puantaj.qr');
Route::get('/ayarlar/ik/bordro/{bordro}', [IkController::class, 'bordroYazdir'])->name('ik.bordro.yazdir');
Route::post('/ayarlar/ik/bordro/{bordro}/gonder', [IkController::class, 'bordroGonder'])->name('ik.bordro.gonder');
Route::get('/puantaj/{token}', [IkController::class, 'qrOkut'])->middleware('throttle:30,1')->name('ik.puantaj.qr.okut');
Route::post('/puantaj/{token}', [IkController::class, 'qrKaydet'])->middleware('throttle:10,1')->name('ik.puantaj.qr.kaydet');
Route::patch('/ayarlar/ik/sifre-talepleri/{talep}', [SifreYonetimController::class, 'ikOnayla'])->name('ik.sifre.onayla');
Route::get('/ayarlar/ik/iletisim', [SifreYonetimController::class, 'ikAyarFormu'])->name('ik.iletisim');
Route::put('/ayarlar/ik/iletisim', [SifreYonetimController::class, 'ikAyarKaydet'])->name('ik.iletisim.update');










/*
|--------------------------------------------------------------------------
| AYARLAR
|--------------------------------------------------------------------------
*/


Route::get(
    '/ayarlar',
    [AyarController::class,'index']
)
->name('ayarlar.index');

Route::get('/ayarlar/qr-iletisim', [AyarController::class, 'qrIletisim'])->name('ayarlar.qr.iletisim');
Route::post('/ayarlar/qr-iletisim', [AyarController::class, 'qrIletisimKaydet'])->name('ayarlar.qr.iletisim.kaydet');
Route::get('/ayarlar/{grup}-ayarlari', [AyarController::class, 'yonetimAyarlari'])->whereIn('grup', ['bildirim', 'servis', 'sistem'])->name('ayarlar.yonetim');
Route::post('/ayarlar/{grup}-ayarlari', [AyarController::class, 'yonetimAyarlariKaydet'])->whereIn('grup', ['bildirim', 'servis', 'sistem'])->name('ayarlar.yonetim.kaydet');
Route::get('/ayarlar/kdv-urun-gruplari', [AyarController::class, 'kdvGruplari'])->name('ayarlar.kdv.gruplari');
Route::post('/ayarlar/kdv-urun-gruplari', [AyarController::class, 'kdvGrubuKaydet'])->name('ayarlar.kdv.gruplari.kaydet');

Route::view('/ayarlar/roller', 'ayarlar.roller-kurumsal')
    ->name('ayarlar.roller');

Route::get('/sistem-hatalari', [SistemHataController::class, 'index'])
    ->name('sistem.hatalari');
Route::post('/sistem-hatalari/yapay-zeka-tara', [SistemHataController::class, 'yapayZekaTara'])
    ->name('sistem.hatalari.yapayzeka');
Route::post('/sistem-hatalari/cozum-planini-onayla', [SistemHataController::class, 'cozumPlaniniOnayla'])->name('sistem.hatalari.onayla');

Route::get('/destek', [DestekController::class, 'index'])->name('destek.index');
Route::get('/destek/yeni', [DestekController::class, 'create'])->name('destek.create');
Route::post('/destek', [DestekController::class, 'store'])->name('destek.store');
Route::post('/destek/{talep}/mesaj', [DestekController::class, 'mesajGonder'])->name('destek.mesaj');
Route::patch('/destek/{talep}/durum', [DestekController::class, 'durumGuncelle'])->name('destek.durum');
Route::patch('/destek/{talep}/geri-bildirim', [DestekController::class, 'geriBildirim'])->name('destek.geri-bildirim');

Route::get('/sohbet', [SohbetController::class, 'index'])->name('sohbet.index');
Route::post('/sohbet/oda', [SohbetController::class, 'odaOlustur'])->name('sohbet.oda.store');
Route::post('/sohbet/{oda}/mesaj', [SohbetController::class, 'mesajGonder'])->name('sohbet.mesaj.store');
Route::get('/sohbet/{oda}/mesajlar', [SohbetController::class, 'mesajlarJson'])->name('sohbet.mesajlar');
Route::get('/bildirimler', [BildirimController::class, 'liste'])->name('bildirimler.liste');
Route::post('/bildirimler/okundu', [BildirimController::class, 'okundu'])->name('bildirimler.okundu');










/*
|--------------------------------------------------------------------------
| FİRMA YÖNETİMİ
|--------------------------------------------------------------------------
*/


Route::prefix('ayarlar')
->group(function(){



    Route::prefix('firma')
    ->group(function(){





        /*
        |--------------------------------------------------------------------------
        | Firma Liste
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/',
            [FirmaYonetimController::class,'index']
        )
        ->name('firma.index');







        /*
        |--------------------------------------------------------------------------
        | Firma Oluştur
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/create',
            [FirmaYonetimController::class,'create']
        )
        ->name('firma.create');





        Route::post(
            '/',
            [FirmaYonetimController::class,'store']
        )
        ->name('firma.store');









        /*
        |--------------------------------------------------------------------------
        | Firma Düzenleme
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/{firma}/edit',
            [FirmaYonetimController::class,'edit']
        )
        ->name('firma.edit');





        Route::put(
            '/{firma}',
            [FirmaYonetimController::class,'update']
        )
        ->name('firma.update');









        /*
        |--------------------------------------------------------------------------
        | Firma Aktif Pasif
        |--------------------------------------------------------------------------
        */


        Route::patch(
            '/{firma}/durum',
            [FirmaYonetimController::class,'durumDegistir']
        )
        ->name('firma.durum');



        Route::delete(
            '/{firma}',
            [FirmaYonetimController::class,'destroy']
        )
        ->name('firma.destroy');








        /*
        |--------------------------------------------------------------------------
        | Firma Kartı
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/{firma}',
            [FirmaYonetimController::class,'show']
        )
        ->name('firma.show');



        /*
        |--------------------------------------------------------------------------
        | ŞUBE YÖNETİMİ
        |--------------------------------------------------------------------------
        */


        
Route::prefix('{firma}/sube')
->group(function(){





    /*
    |--------------------------------------------------------------------------
    | Şube Liste
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/',
        [SubeController::class,'index']
    )
    ->name('sube.index');








    /*
    |--------------------------------------------------------------------------
    | Yeni Şube
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/create',
        [SubeController::class,'create']
    )
    ->name('sube.create');





    Route::post(
        '/',
        [SubeController::class,'store']
    )
    ->name('sube.store');









    /*
    |--------------------------------------------------------------------------
    | Şube Detay
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/{sube}',
        [SubeController::class,'show']
    )
    ->name('sube.show');









    /*
    |--------------------------------------------------------------------------
    | Şube Düzenleme
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/{sube}/edit',
        [SubeController::class,'edit']
    )
    ->name('sube.edit');





    Route::put(
        '/{sube}',
        [SubeController::class,'update']
    )
    ->name('sube.update');









    /*
    |--------------------------------------------------------------------------
    | Şube Aktif Pasif
    |--------------------------------------------------------------------------
    */


    Route::patch(
        '/{sube}/durum',
        [SubeController::class,'durumDegistir']
    )
    ->name('sube.durum');



    Route::delete(
        '/{sube}',
        [SubeController::class,'destroy']
    )
    ->name('sube.destroy');





});





    });



});
