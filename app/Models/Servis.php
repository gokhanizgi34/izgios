<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Servis extends Model
{

    use HasFactory;


    protected $table = 'servisler';



    protected $fillable = [

        'musteri_id',

        'arac_id',

        'servis_no',

        'servis_tarihi',

        'giris_km',

        'sikayet',

        'usta_notu',

        'oncelik',

        'yakit_seviyesi',

        'anahtar_durumu',

        'ruhsat_aracta',

        'yapilan_islem',

        'kullanilan_parca',

        'parca_tutari',

        'iscilik_tutari',

        'toplam_tutar',

        'durum',

        'notlar',

        'teslim_tarihi',

        'sonraki_bakim_tarihi',

        'bakim_periyodu',

    ];





    protected $casts = [

        'ruhsat_aracta' => 'boolean',

        'servis_tarihi' => 'date',

        'teslim_tarihi' => 'date',

        'sonraki_bakim_tarihi' => 'date',

    ];






    /*
    |--------------------------------------------------------------------------
    | Müşteri
    |--------------------------------------------------------------------------
    */


    public function musteri()
    {

        return $this->belongsTo(
            Musteri::class,
            'musteri_id'
        );

    }






    /*
    |--------------------------------------------------------------------------
    | Araç
    |--------------------------------------------------------------------------
    */


    public function arac()
    {

        return $this->belongsTo(
            Arac::class,
            'arac_id'
        );

    }







    /*
    |--------------------------------------------------------------------------
    | Araç Fotoğrafları
    |--------------------------------------------------------------------------
    */


    public function fotograflar()
    {

        return $this->hasMany(
            ServisFotograf::class,
            'servis_id'
        );

    }







    /*
    |--------------------------------------------------------------------------
    | Yapılan İşlemler
    |--------------------------------------------------------------------------
    */


    public function islemler()
    {

        return $this->hasMany(
            ServisIslem::class,
            'servis_id'
        );

    }







    /*
    |--------------------------------------------------------------------------
    | Kullanılan Parçalar
    |--------------------------------------------------------------------------
    */


    public function parcalar()
    {

        return $this->hasMany(
            ServisParca::class,
            'servis_id'
        );

    }








    /*
    |--------------------------------------------------------------------------
    | Teslimat
    |--------------------------------------------------------------------------
    */


    public function teslimat()
    {

        return $this->hasOne(
            ServisTeslimat::class,
            'servis_id'
        );

    }







    /*
    |--------------------------------------------------------------------------
    | Toplam Tutar Hesaplama
    |--------------------------------------------------------------------------
    */


    public function toplamHesapla()
    {

        $this->toplam_tutar =

            $this->parca_tutari +
            $this->iscilik_tutari;


        return $this;

    }

}