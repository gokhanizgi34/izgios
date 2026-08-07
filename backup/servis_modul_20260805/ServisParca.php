<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServisParca extends Model
{

    use HasFactory;


    protected $table = 'servis_parcalar';



    protected $fillable = [

        'servis_id',

        'parca_adi',

        'adet',

        'birim_fiyat',

        'toplam_fiyat',

        'stok_parca_id',

        'aciklama',

    ];



    public function servis()
    {

        return $this->belongsTo(
            Servis::class,
            'servis_id'
        );

    }



    public function fotograflar()
    {

        return $this->hasMany(
            ServisParcaFotografi::class,
            'servis_parca_id'
        );

    }


}