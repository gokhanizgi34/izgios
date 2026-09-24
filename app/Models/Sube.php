<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Sube extends Model
{


    protected $fillable = [

        'firma_id',
        'sube_adi',
        'vergi_no',
        'adres',
        'telefon',
        'whatsapp_no',
        'aktif',

    ];




    protected $casts = [

        'aktif' => 'boolean',

    ];





    public function firma(): BelongsTo
    {

        return $this->belongsTo(Firma::class);

    }





    public function personeller(): HasMany
    {

        return $this->hasMany(FirmaPersonel::class);

    }

    public function servisler(): HasMany
    {
        return $this->hasMany(Servis::class);
    }



}
