<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Musteri extends Model
{


    use HasFactory;




    protected $table = 'musteris';







    protected $fillable = [

        'firma_id',

        'sube_id',


        'ad_soyad',


        'tc_kimlik_no',


        'telefon',


        'telefon2',


        'email',

        'dogum_tarihi',


        'adres',


        'notlar',


    ];









    protected $casts = [

        'dogum_tarihi' => 'date',


        'created_at' => 'datetime',


        'updated_at' => 'datetime',


    ];









    /**
     * Müşterinin araçları
     */
    public function araclar()
    {


        return $this->hasMany(

            Arac::class,

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









    /**
     * Müşterinin servis kayıtları
     */
    public function servisler()
    {


        return $this->hasMany(

            Servis::class,

            'musteri_id'

        );


    }





}
