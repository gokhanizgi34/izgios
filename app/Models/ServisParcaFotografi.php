<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServisParcaFotografi extends Model
{

    use HasFactory;


    protected $table = 'servis_parca_fotograflari';



    protected $fillable = [

        'servis_parca_id',

        'tip',

        'dosya_yolu',

        'aciklama',

    ];



    public function servisParca()
    {

        return $this->belongsTo(

            ServisParca::class,

            'servis_parca_id'

        );

    }


}