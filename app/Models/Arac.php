<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;



class Arac extends Model
{


    use HasFactory;



    /*
    |--------------------------------------------------------------------------
    | Tablo
    |--------------------------------------------------------------------------
    */


    protected $table = 'araclar';





    /*
    |--------------------------------------------------------------------------
    | Kaydedilebilir Alanlar
    |--------------------------------------------------------------------------
    */


    protected $fillable = [

        'firma_id',

        'sube_id',


        'musteri_id',

        'plaka',

        'marka',

        'model',

        'model_yili',

        'kilometre',

        'sase_no',

        'motor_no',

        'yakit_tipi',

        'vites',

        'notlar',


        // QR Sistem

        'qr_token',

        'qr_created_at',


    ];







    /*
    |--------------------------------------------------------------------------
    | Veri Tipleri
    |--------------------------------------------------------------------------
    */


    protected $casts = [


        'model_yili'=>'integer',


        'kilometre'=>'integer',


        'qr_created_at'=>'datetime',


    ];








    /*
    |--------------------------------------------------------------------------
    | Müşteri İlişkisi
    |--------------------------------------------------------------------------
    */


    public function musteri()
    {


        return $this->belongsTo(

            Musteri::class,

            'musteri_id'

        );


    }

    public function firma()
    {
        return $this->belongsTo(Firma::class);
    }

    public function sube()
    {
        return $this->belongsTo(Sube::class);
    }

/*
|--------------------------------------------------------------------------
| Servis Kayıtları
|--------------------------------------------------------------------------
*/


public function servisler()
{

    return $this->hasMany(

        Servis::class,

        'arac_id'

    );

}







    /*
    |--------------------------------------------------------------------------
    | QR Otomatik Oluşturma
    |--------------------------------------------------------------------------
    */


    protected static function boot()
    {


        parent::boot();




        static::creating(function($arac){



            if(empty($arac->qr_token))
            {


                $arac->qr_token = Str::uuid();



                $arac->qr_created_at = now();


            }



        });



    }




}
