<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SilmeDenetimKaydi extends Model
{
    protected $table = 'silme_denetim_kayitlari';
    protected $fillable = ['firma_id','kullanici_id','modul','kayit_turu','kayit_id','kayit_ozeti','silinen_veri','islemi_yapan','rol','ip_adresi','ekran_adresi','firma_sahibine_mail','mail_hatasi'];
    protected $casts = ['silinen_veri' => 'array', 'firma_sahibine_mail' => 'boolean'];
}
