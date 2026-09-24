<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AracQrKodu extends Model
{
    protected $table = 'arac_qr_havuzu';

    protected $fillable = [
        'token', 'parti_kodu', 'sira_no', 'durum', 'arac_id',
        'olusturan_id', 'atayan_id', 'atanma_at', 'iptal_at',
    ];

    protected $casts = [
        'atanma_at' => 'datetime',
        'iptal_at' => 'datetime',
    ];
}
