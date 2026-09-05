<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SohbetMesaji extends Model
{
    protected $table = 'sohbet_mesajlari';
    protected $fillable = ['sohbet_odasi_id', 'user_id', 'mesaj'];
    public function kullanici() { return $this->belongsTo(User::class, 'user_id'); }
}
