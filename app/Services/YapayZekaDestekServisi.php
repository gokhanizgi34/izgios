<?php

namespace App\Services;

use App\Models\DestekTalebi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YapayZekaDestekServisi
{
    public function analizEt(DestekTalebi $talep): array
    {
        if (config('services.izgios_ai.provider') !== 'openai' || blank(config('services.izgios_ai.key'))) {
            return ['durum' => 'bekliyor', 'ozet' => 'Yapay zekâ entegrasyonu yapılandırılmamış.', 'cozum' => null];
        }

        $metin = $this->hassasVeriyiMaskele($talep->baslik . "\n" . $talep->mesaj);
        $istem = <<<TEXT
Sen İZGİOS oto servis otomasyonu için güvenli destek asistanısın.
Destek talebini Türkçe değerlendir. Kod değiştirme, veri silme, sunucu komutu, ödeme veya dış sistem işlemi önerme.
Basit kullanıcı sorunlarında kısa, uygulanabilir çözüm öner. Teknik hata, güvenlik, veri kaybı, ödeme veya birden fazla firmayı etkileyen sorunlarda Sistem Yöneticisine yönlendir ve güvenli işlem planı öner.
Yalnızca aşağıdaki üç satırı üret:
DURUM: COZUM_ONERILDI veya SISTEM_YONETICISINE_YONLENDIR
OZET: en fazla 180 karakter
COZUM: en fazla 500 karakter

Kategori: {$talep->kategori}
Öncelik: {$talep->oncelik}
Hata kodu: {$talep->hata_kodu}
Talep: {$metin}
TEXT;

        try {
            $yanit = Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(30)
                ->post('https://api.openai.com/v1/responses', ['model' => config('services.izgios_ai.model', 'gpt-5.6'), 'input' => $istem]);

            if (!$yanit->successful()) {
                Log::warning('İZGİOS AI destek analizi başarısız.', ['status' => $yanit->status(), 'talep_id' => $talep->id]);
                return ['durum' => 'sistem_yoneticisine_yonlendirildi', 'ozet' => 'Yapay zekâ analizi şu an tamamlanamadı.', 'cozum' => 'Talep Sistem Yöneticisi inceleme kuyruğuna alındı.'];
            }

            $cevap = data_get($yanit->json(), 'output.0.content.0.text') ?? data_get($yanit->json(), 'output_text') ?? '';
            return $this->cevabiAyristir($cevap);
        } catch (\Throwable $hata) {
            Log::warning('İZGİOS AI destek bağlantı hatası.', ['talep_id' => $talep->id, 'sebep' => $hata->getMessage()]);
            return ['durum' => 'sistem_yoneticisine_yonlendirildi', 'ozet' => 'Yapay zekâ hizmetine ulaşılamadı.', 'cozum' => 'Talep Sistem Yöneticisi inceleme kuyruğuna alındı.'];
        }
    }

    private function hassasVeriyiMaskele(string $metin): string
    {
        $metin = preg_replace('/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/u', '[e-posta]', $metin);
        return preg_replace('/\b\d{10,11}\b/u', '[telefon]', $metin);
    }

    private function cevabiAyristir(string $metin): array
    {
        preg_match('/DURUM:\s*(.+)/iu', $metin, $durum);
        preg_match('/OZET:\s*(.+)/iu', $metin, $ozet);
        preg_match('/COZUM:\s*(.+)/iu', $metin, $cozum);
        $yonlendirme = mb_strtoupper(trim($durum[1] ?? ''), 'UTF-8') === 'COZUM_ONERILDI' ? 'cozum_onerildi' : 'sistem_yoneticisine_yonlendirildi';
        return ['durum' => $yonlendirme, 'ozet' => mb_strimwidth(trim($ozet[1] ?? 'Talep yapay zekâ tarafından incelendi.'), 0, 180, '…', 'UTF-8'), 'cozum' => mb_strimwidth(trim($cozum[1] ?? 'Talep Sistem Yöneticisi inceleme kuyruğuna alındı.'), 0, 500, '…', 'UTF-8')];
    }
}
