<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GorselBelgeOkumaServisi
{
    public function oku(UploadedFile $dosya, string $tur): array
    {
        if (config('services.izgios_ai.provider') !== 'openai' || blank(config('services.izgios_ai.key'))) {
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
            $yanit = Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(60)->retry(1, 800)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.izgios_ai.model', 'gpt-5.6'),
                    'store' => false,
                    'input' => [[
                        'role' => 'user',
                        'content' => [
                            ['type'=>'input_text','text'=>$istem],
                            ['type'=>'input_image','detail'=>'high','image_url'=>'data:'.$mime.';base64,'.base64_encode($dosya->get())],
                        ],
                    ]],
                    'text' => ['format'=>['type'=>'json_schema','name'=>$tur.'_okuma','strict'=>true,'schema'=>$sema]],
                    'max_output_tokens' => 1200,
                ]);
            if (! $yanit->successful()) {
                Log::warning('Görsel belge yapay zekâ okuması başarısız.', ['tur'=>$tur,'status'=>$yanit->status()]);
                throw new RuntimeException('Yapay zekâ görüntüyü şu anda okuyamadı. Tekrar deneyin.');
            }
            $metin = data_get($yanit->json(), 'output.0.content.0.text') ?? data_get($yanit->json(), 'output_text');
            $veri = is_string($metin) ? json_decode($metin, true) : null;
            if (! is_array($veri)) throw new RuntimeException('Yapay zekâdan geçerli okuma sonucu alınamadı.');
            return $veri;
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
