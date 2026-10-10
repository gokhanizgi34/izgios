<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestekMesaji extends Model
{
    protected $table = 'destek_mesajlari';

    protected $fillable = ['destek_talebi_id', 'user_id', 'gonderen_tipi', 'mesaj'];

    public function talep()
    {
        return $this->belongsTo(DestekTalebi::class, 'destek_talebi_id');
    }

    public function kullanici()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
