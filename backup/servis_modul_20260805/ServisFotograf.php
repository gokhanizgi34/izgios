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



    /**
     * Fotoğrafın bağlı olduğu servis
     */

    public function servis()
    {

        return $this->belongsTo(

            Servis::class,

            'servis_id'

        );

    }


}use App\Models\ServisFotograf;

$servis->save();

/*
|--------------------------------------------------------------------------
| Araç Kabul Fotoğrafları
|--------------------------------------------------------------------------
*/

if($request->hasFile('fotograflar'))
{

    foreach($request->file('fotograflar') as $kategori=>$foto)
    {

        if($foto)
        {

            $path = $foto->store(
                'servisler/'.$servis->id,
                'public'
            );


            ServisFotograf::create([

                'servis_id' => $servis->id,

                'kategori' => $kategori,

                'dosya_yolu' => $path,

                'aciklama' => 'Araç kabul fotoğrafı'

            ]);

        }

    }

}