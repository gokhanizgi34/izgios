<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServisFotograf extends Model
{

    use HasFactory;


    protected $table = 'servis_fotograflari';



    protected $fillable = [

        'servis_id',

        'kategori',

        'dosya_yolu',

        'aciklama',

    ];





    public function servis()
    {

        return $this->belongsTo(

            Servis::class,

            'servis_id'

        );

    }


}