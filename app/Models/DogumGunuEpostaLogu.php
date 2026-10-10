<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DogumGunuEpostaLogu extends Model
{
    protected $table = 'dogum_gunu_eposta_loglari';

    protected $fillable = ['alici_tipi', 'alici_id', 'yil', 'gonderildi_at'];

    protected $casts = ['gonderildi_at' => 'datetime'];
}
