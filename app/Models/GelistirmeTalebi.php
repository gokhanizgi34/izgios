<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GelistirmeTalebi extends Model
{
    protected $table = 'gelistirme_talepleri';
    protected $fillable = ['olusturan_id', 'onaylayan_id', 'baslik', 'talep', 'cozum_plani', 'durum', 'onaylandi_at', 'uygulandi_at'];
    protected $casts = ['onaylandi_at' => 'datetime', 'uygulandi_at' => 'datetime'];
    public function olusturan() { return $this->belongsTo(User::class, 'olusturan_id'); }
    public function onaylayan() { return $this->belongsTo(User::class, 'onaylayan_id'); }
    public function mesajlar() { return $this->hasMany(GelistirmeMesaji::class, 'gelistirme_talebi_id'); }
}
