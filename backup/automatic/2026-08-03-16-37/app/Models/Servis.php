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

        'sikayet',

        'yapilan_islem',

        'kullanilan_parca',

        'parca_tutari',

        'iscilik_tutari',

        'toplam_tutar',

        'durum',

        'notlar',


    ];









    /**
     * Servisin sahibi müşteri
     */
    public function musteri()
    {


        return $this->belongsTo(
            Musteri::class,
            'musteri_id'
        );


    }









    /**
     * Servisin bağlı olduğu araç
     */
    public function arac()
    {


        return $this->belongsTo(
            Arac::class,
            'arac_id'
        );


    }








    /**
     * Toplam tutarı otomatik hesapla
     */
    public function toplamHesapla()
    {


        $this->toplam_tutar =
            
            $this->parca_tutari +
            $this->iscilik_tutari;



        return $this;


    }

public function hasarlar()
{

    return $this->hasMany(
        AracHasar::class
    );

}


}