<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Rol extends Model
{


    protected $fillable = [

        'ad',
        'aktif',

    ];



    public function personeller(): HasMany
    {

        return $this->hasMany(FirmaPersonel::class);

    }



}