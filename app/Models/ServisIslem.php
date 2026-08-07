<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServisIslem extends Model
{

    use HasFactory;


    protected $table = 'servis_islemleri';



    protected $fillable = [

        'servis_id',

        'islem_adi',

        'aciklama',

        'tutar',

        'durum',

    ];



    public function servis()
    {

        return $this->belongsTo(
            Servis::class,
            'servis_id'
        );

    }


}