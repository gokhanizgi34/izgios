<?php

namespace App\Services;

use App\Models\Servis;
use App\Models\User;
use App\Notifications\FirmaSistemBildirimi;

class FirmaBildirimServisi
{
    public function servisKabulEdildi(Servis $servis): void
    {
        User::query()->where('role','usta')->where('status','aktif')->whereHas('firmaPersoneli',fn($q)=>$q->where('firma_id',$servis->firma_id)->where('aktif',true))->each(function(User $usta)use($servis){
            $usta->notify(new FirmaSistemBildirimi([
                'tur'=>'servis_kabul','baslik'=>'Yeni araç servise kabul edildi',
                'mesaj'=>($servis->arac?->plaka??'Araç').' · '.($servis->servis_no??'#'.$servis->id),
                'url'=>route('servis.islem',$servis->id),
            ]));
        });
    }
}
