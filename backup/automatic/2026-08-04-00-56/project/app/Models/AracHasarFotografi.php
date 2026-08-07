<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class AracHasarFotografi extends Model
{


protected $fillable=[

'arac_hasari_id',
'dosya_yolu'

];



public function hasar()
{

return $this->belongsTo(
    AracHasar::class,
    'arac_hasari_id'
);

}


}   