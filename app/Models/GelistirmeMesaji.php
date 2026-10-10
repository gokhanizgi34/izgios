<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GelistirmeMesaji extends Model
{
    protected $table = 'gelistirme_mesajlari';
    protected $fillable = ['gelistirme_talebi_id', 'user_id', 'gonderen_tipi', 'mesaj'];
    public function kullanici() { return $this->belongsTo(User::class, 'user_id'); }
}
