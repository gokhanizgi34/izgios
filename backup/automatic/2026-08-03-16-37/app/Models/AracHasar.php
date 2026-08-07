<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AracHasar extends Model
{


    protected $table = 'arac_hasarlari';



    protected $fillable = [

        'arac_id',
        'servis_id',
        'parca_adi',
        'aciklama',
        'konum',

    ];




    public function arac()
    {

        return $this->belongsTo(
            Arac::class
        );

    }




    public function servis()
    {

        return $this->belongsTo(
            Servis::class
        );

    }




    public function fotograflar()
    {

        return $this->hasMany(
            AracHasarFotografi::class
        );

    }



}