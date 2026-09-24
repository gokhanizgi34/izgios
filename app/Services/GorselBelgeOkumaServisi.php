<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GorselBelgeOkumaServisi
{
    public function __construct(private readonly YapayZekaIstemcisi $istemci) {}

    public function oku(UploadedFile $dosya, string $tur): array
    {
        if (! $this->istemci->hazirMi()) {
            throw new RuntimeException('Merkezi yapay zekâ bağlantısı aktif değil. API ve İletişim Entegrasyonları ayarını kontrol edin.');
        }

        $mime = $dosya->getMimeType();
        if (! in_array($mime, ['image/jpeg','image/png','image/webp','image/gif'], true)) {
            throw new RuntimeException('Fotoğraf biçimi desteklenmiyor. JPG, PNG veya WEBP kullanın.');
        }

        $sema = $tur === 'plaka' ? $this->plakaSemasi() : $this->fisSemasi();
        $istem = $tur === 'plaka'
            ? 'Görüntüdeki Türkiye araç plakasını oku. İl kodu 01-81 aralığında olmalı. O/0, I/1, B/8 ve S/5 karışıklıklarını plaka düzenine göre kontrol et. Görüntüde plaka yoksa plaka alanını boş döndür. Tahmin uydurma.'
            : 'Görüntüdeki muhasebe fişi veya faturayı Türkçe oku. Firma, belge numarası, tarih, genel toplam, KDV oranı ve okunabilen kalemleri çıkar. Genel toplamı yalnız belgede açıkça gördüğün değerden yaz. Emin olmadığın alanları boş bırak; tutar uydurma.';

        try {
            return $this->istemci->gorselJson($istem, $mime, $dosya->get(), $sema);
        } catch (RuntimeException $hata) {
            throw $hata;
        } catch (\Throwable $hata) {
            Log::warning('Görsel belge okuma bağlantı hatası.', ['tur'=>$tur,'sebep'=>$hata->getMessage()]);
            throw new RuntimeException('Yapay zekâ hizmetine ulaşılamadı. Fotoğraf yerel OCR ile okunacak.');
        }
    }

    private function plakaSemasi(): array
    {
        return ['type'=>'object','additionalProperties'=>false,'properties'=>[
            'plaka'=>['type'=>'string'],'guven'=>['type'=>'number','minimum'=>0,'maximum'=>1],
            'alternatifler'=>['type'=>'array','items'=>['type'=>'string'],'maxItems'=>3],
        ],'required'=>['plaka','guven','alternatifler']];
    }

    private function fisSemasi(): array
    {
        $nullableString=['type'=>['string','null']]; $nullableNumber=['type'=>['number','null']];
        return ['type'=>'object','additionalProperties'=>false,'properties'=>[
            'fis_no'=>$nullableString,'tarih'=>$nullableString,'firma'=>$nullableString,'toplam'=>$nullableNumber,
            'kdv_orani'=>$nullableNumber,'guven'=>['type'=>'number','minimum'=>0,'maximum'=>1],
            'kalemler'=>['type'=>'array','items'=>['type'=>'object','additionalProperties'=>false,'properties'=>[
                'ad'=>['type'=>'string'],'adet'=>$nullableNumber,'birim_fiyat'=>$nullableNumber,'kdv_orani'=>$nullableNumber,'toplam'=>$nullableNumber,
            ],'required'=>['ad','adet','birim_fiyat','kdv_orani','toplam']]],
        ],'required'=>['fis_no','tarih','firma','toplam','kdv_orani','guven','kalemler']];
    }
}
