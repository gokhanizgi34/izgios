<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServisDurumNotu extends Model
{
    protected $table = 'servis_durum_notlari';
    protected $fillable = ['servis_id', 'kullanici_id', 'durum', 'not'];

    public function servis() { return $this->belongsTo(Servis::class); }
    public function kullanici() { return $this->belongsTo(User::class, 'kullanici_id'); }
}
