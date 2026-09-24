<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IkIletisimAyari extends Model
{
    protected $table = 'ik_iletisim_ayarlari';
    protected $fillable = ['firma_id', 'sifre_talep_email', 'guncelleyen_id'];
}
