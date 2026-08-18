<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Firma extends Model
{

    protected $fillable = [

        'unvan',
        'vergi_no',
        'telefon',
        'email',

        'google_yorum_linki',

        'logo_yolu',
        'adres',
        'aktif',
        'merkez_goster',

    ];



    protected $casts = [

        'aktif' => 'boolean',
        'merkez_goster' => 'boolean',

    ];



    public function subeler(): HasMany
    {

        return $this->hasMany(Sube::class);

    }



    public function personeller(): HasMany
    {

        return $this->hasMany(FirmaPersonel::class);

    }



    /*
    |--------------------------------------------------------------------------
    | Görünen Firma Adı
    |--------------------------------------------------------------------------
    |
    | Firma merkez olarak gösterilecekse
    | isim sonuna ekleme yapar.
    |
    */


    public function getGosterimAdiAttribute()
    {

        if($this->merkez_goster)
        {

            return $this->unvan . ' (Merkez Şube)';

        }


        return $this->unvan;

    }



}
