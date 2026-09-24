<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SifreYenilemeTalebi extends Model
{
    protected $table = 'sifre_yenileme_talepleri';
    protected $fillable = ['user_id', 'firma_id', 'istek_email', 'ik_email', 'durum', 'isleyen_id', 'onaylandi_at'];
    protected $casts = ['onaylandi_at' => 'datetime'];
    public function kullanici() { return $this->belongsTo(User::class, 'user_id'); }
}
