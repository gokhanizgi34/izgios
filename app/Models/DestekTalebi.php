<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestekTalebi extends Model
{
    protected $table = 'destek_talepleri';
    protected $fillable = ['user_id', 'firma_id', 'kategori', 'oncelik', 'baslik', 'mesaj', 'durum', 'ai_durum', 'ai_ozet', 'ai_cozum', 'hata_kodu'];

    public function kullanici() { return $this->belongsTo(User::class, 'user_id'); }
    public function firma() { return $this->belongsTo(Firma::class); }
}
