<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestekTalebi extends Model
{
    protected $table = 'destek_talepleri';
    protected $fillable = ['user_id', 'firma_id', 'kategori', 'oncelik', 'baslik', 'mesaj', 'durum', 'ai_durum', 'ai_ozet', 'ai_cozum', 'hata_kodu', 'kullanici_geri_bildirimi', 'son_yanit_at', 'zaman_asimi_at'];
    protected $casts = ['son_yanit_at' => 'datetime', 'zaman_asimi_at' => 'datetime'];

    public function kullanici() { return $this->belongsTo(User::class, 'user_id'); }
    public function firma() { return $this->belongsTo(Firma::class); }
    public function mesajlar() { return $this->hasMany(DestekMesaji::class, 'destek_talebi_id'); }
}
