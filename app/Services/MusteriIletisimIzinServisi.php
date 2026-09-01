<?php

namespace App\Services;

use App\Models\Arac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusteriIletisimIzinServisi
{
    public const METIN_SURUMU = '2026-09-01-v1';

    public const SERVIS_METNI = 'Araç kabulü, servis durumu, teklif, randevu, bakım hatırlatması, teslim ve servis evrakları hakkında e-posta, SMS, WhatsApp veya telefon yoluyla bilgilendirilmek için sesli ve yazılı iletişime izin veriyorum.';

    public const TICARI_METIN = 'Sigorta, bakım paketleri, indirimler, kampanyalar, doğum günü ve özel gün içerikleri hakkında e-posta, SMS, WhatsApp veya telefon yoluyla ticari ileti almak için sesli ve yazılı iletişime izin veriyorum.';

    public function izinKaydi(?int $firmaId, ?int $musteriId): ?object
    {
        if (! $firmaId || ! $musteriId) {
            return null;
        }

        return DB::table('musteri_iletisim_izinleri')
            ->where('firma_id', $firmaId)
            ->where('musteri_id', $musteriId)
            ->first();
    }

    public function izinliMi(?int $firmaId, ?int $musteriId, string $tur): bool
    {
        $kayit = $this->izinKaydi($firmaId, $musteriId);
        $alan = $tur === 'ticari' ? 'ticari_iletisim_izni' : 'servis_iletisim_izni';

        return $kayit && (bool) $kayit->{$alan};
    }

    public function kaydet(Request $request, Arac $arac, bool $servisIzni, bool $ticariIzni): void
    {
        $musteri = $arac->musteri;
        abort_unless($musteri && $arac->firma_id, 404);
        $firma = DB::table('firmas')->where('id', $arac->firma_id)->firstOrFail();
        $servisId = $arac->servisler()->latest('id')->value('id');
        $simdi = now();

        DB::transaction(function () use ($request, $arac, $musteri, $firma, $servisId, $servisIzni, $ticariIzni, $simdi) {
            $anahtar = ['firma_id' => $arac->firma_id, 'musteri_id' => $musteri->id];
            $degerler = [
                    'servis_iletisim_izni' => $servisIzni,
                    'ticari_iletisim_izni' => $ticariIzni,
                    'tercih_at' => $simdi,
                    'updated_at' => $simdi,
            ];
            if (! DB::table('musteri_iletisim_izinleri')->where($anahtar)->exists()) {
                $degerler['created_at'] = $simdi;
            }
            DB::table('musteri_iletisim_izinleri')->updateOrInsert($anahtar, $degerler);

            DB::table('musteri_iletisim_izin_hareketleri')->insert([
                'firma_id' => $arac->firma_id,
                'musteri_id' => $musteri->id,
                'servis_id' => $servisId,
                'firma_unvani' => $firma->unvan,
                'musteri_adi' => $musteri->ad_soyad,
                'email' => $musteri->email,
                'telefon' => $musteri->telefon,
                'servis_iletisim_izni' => $servisIzni,
                'ticari_iletisim_izni' => $ticariIzni,
                'servis_metni_surumu' => self::METIN_SURUMU,
                'servis_metni_hash' => hash('sha256', self::SERVIS_METNI),
                'ticari_metni_surumu' => self::METIN_SURUMU,
                'ticari_metni_hash' => hash('sha256', self::TICARI_METIN),
                'ip_adresi' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 2000),
                'qr_token_hash' => hash('sha256', $arac->qr_token),
                'onay_at' => $simdi,
                'created_at' => $simdi,
            ]);
        });
    }
}
