<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PeriyodikBakimKalemiServisi
{
    public const VARSAYILANLAR = [
        'motor_yagi' => 'Motor Yağı',
        'yag_filtresi' => 'Yağ Filtresi',
        'hava_filtresi' => 'Hava Filtresi',
        'polen_filtresi' => 'Polen Filtresi',
        'yakit_filtresi' => 'Yakıt Filtresi',
        'triger_seti' => 'Triger Seti',
        'v_kayisi' => 'V Kayışı',
        'gergi_rulmanlar' => 'Gergi ve Rulmanlar',
        'devirdaim_pompasi' => 'Devirdaim Pompası',
        'sanziman_yagi' => 'Şanzıman Yağı',
        'diferansiyel_yagi' => 'Diferansiyel Yağı',
        'fren_bakimi' => 'Fren Bakımı',
        'fren_hidroligi' => 'Fren Hidroliği',
        'fren_disk_balata' => 'Fren Disk ve Balata',
        'amortisor_kontrolu' => 'Amortisör Kontrolü',
        'direksiyon_sistemi' => 'Direksiyon Sistemi',
        'buji_bakimi' => 'Buji Bakımı',
        'atesleme_sistemi' => 'Ateşleme Sistemi',
        'enjektor_temizligi' => 'Enjektör Temizliği',
        'antifriz_kontrolu' => 'Antifriz Kontrolü',
        'silecek_bakimi' => 'Silecek Bakımı',
        'kartel_tapasi_contasi' => 'Kartel Tapası ve contası',
        'balata_spreyi' => 'Balata Spreyi',
        'diger' => 'Diğer',
        'genel_kontrol' => 'Genel Kontrol',
        'iscilik' => 'İşçilik',
    ];

    public function firmaIcin(int $firmaId): array
    {
        $json = DB::table('firma_periyodik_bakim_ayarlari')->where('firma_id', $firmaId)->value('kalemler');
        if ($json === null) return self::VARSAYILANLAR;

        return collect(json_decode($json, true) ?: [])
            ->sortBy('sira')->mapWithKeys(fn (array $kalem) => [$kalem['kod'] => $kalem['ad']])->all();
    }

    public function kaydet(int $firmaId, array $kalemler, ?int $kullaniciId): void
    {
        DB::table('firma_periyodik_bakim_ayarlari')->updateOrInsert(
            ['firma_id' => $firmaId],
            ['kalemler' => json_encode(array_values($kalemler), JSON_UNESCAPED_UNICODE), 'guncelleyen_id' => $kullaniciId, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
