<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SohbetOdasi extends Model
{
    protected $table = 'sohbet_odalari';
    protected $fillable = ['firma_id', 'olusturan_id', 'ad', 'tip'];
    public function firma() { return $this->belongsTo(Firma::class); }
    public function mesajlar() { return $this->hasMany(SohbetMesaji::class, 'sohbet_odasi_id'); }
}
