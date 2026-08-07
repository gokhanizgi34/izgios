<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Musteri extends Model
{


    use HasFactory;




    protected $table = 'musteris';







    protected $fillable = [


        'ad_soyad',


        'tc_kimlik_no',


        'telefon',


        'telefon2',


        'email',


        'adres',


        'notlar',


    ];









    protected $casts = [


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