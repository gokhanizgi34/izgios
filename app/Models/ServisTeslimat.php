<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServisTeslimat extends Model
{

    use HasFactory;


    protected $table = 'servis_teslimatlar';



    protected $fillable = [

        'servis_id',

        'teslim_alan',

        'teslim_tarihi',

        'odeme_tipi',

        'aciklama',

        'teslim_mesaji_gonderildi',

    ];



    public function servis()
    {

        return $this->belongsTo(

            Servis::class,

            'servis_id'

        );

    }


}