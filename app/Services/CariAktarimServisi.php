<?php

namespace App\Services;

use App\Models\Musteri;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CariAktarimServisi
{
    public function musteriKarti(Musteri $musteri): int
    {
        if (! $musteri->firma_id) {
            throw ValidationException::withMessages([
                'firma_id' => 'Cari kart oluşturulamadı: müşteri kaydının firma bağlantısı bulunmuyor.',
            ]);
        }

        $plaka = $musteri->araclar()->orderByDesc('id')->value('plaka');

        DB::table('cari_hesaplar')->updateOrInsert(
            ['firma_id' => $musteri->firma_id, 'musteri_id' => $musteri->id],
            [
                'tip' => 'musteri',
                'unvan' => $musteri->ad_soyad,
                'plaka' => $plaka,
                'telefon' => $musteri->telefon,
                'email' => $musteri->email,
                'vergi_no' => $musteri->tc_kimlik_no,
                'aktif' => true,
                'kaynak' => 'musteri',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return (int) DB::table('cari_hesaplar')
            ->where('firma_id', $musteri->firma_id)
            ->where('musteri_id', $musteri->id)
            ->value('id');
    }
}
