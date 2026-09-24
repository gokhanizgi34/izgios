<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;



class FirmaPersonel extends Model
{


protected $fillable=[


'user_id',


'firma_id',

'sube_id',

'rol_id',

'ad_soyad',

'telefon',

'email',

'aktif'


];




public function firma()
{

return $this->belongsTo(Firma::class);

}


public function user()
{

return $this->belongsTo(User::class);

}




public function sube()
{

return $this->belongsTo(Sube::class);

}




public function rol()
{

return $this->belongsTo(Rol::class);

}



}
