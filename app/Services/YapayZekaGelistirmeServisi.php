<?php

namespace App\Services;

use App\Models\GelistirmeTalebi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YapayZekaGelistirmeServisi
{
    public function yanitla(GelistirmeTalebi $talep, string $mesaj): array
    {
        if (config('services.izgios_ai.provider') !== 'openai' || blank(config('services.izgios_ai.key'))) {
            return ['basarili' => false, 'mesaj' => 'Yapay zekâ bağlantısı yapılandırılmamış. API ayarlarını kontrol edin.'];
        }

        $baglam = $this->maskele($talep->talep . "\n\nYeni mesaj: " . $mesaj);
        $istem = <<<TEXT
Sen İZGİOS geliştirme merkezinin Türkçe konuşan güvenli çözüm danışmanısın.
İsteği analiz et, uygulanabilir planı açıkla, etkilenebilecek ekranları ve test adımlarını belirt.
Kesinlikle kod değiştirdiğini, veri sildiğini, sunucuya bağlandığını, test yaptığını veya değişikliği uyguladığını söyleme. Bu sistemde yalnızca öneri ve plan üretirsin. Her uygulama, Sistem Yöneticisinin ayrıca vereceği onayla yetkili geliştirme/dağıtım sürecine aktarılır.

Şu başlıklarla kısa ve net yanıt ver:
KISA DEĞERLENDİRME:
UYGULAMA PLANI:
ETKİLENECEK ALANLAR:
TEST VE KONTROL:
ONAY DURUMU: Kritik soru yoksa "Plan onayınıza hazır." yaz.

Talep başlığı: {$talep->baslik}
Talep bağlamı: {$baglam}
TEXT;

        try {
            $yanit = Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(45)
                ->post('https://api.openai.com/v1/responses', ['model' => config('services.izgios_ai.model', 'gpt-5.6'), 'input' => $istem]);
            if (!$yanit->successful()) {
                Log::warning('İZGİOS AI geliştirme yanıtı başarısız.', ['status' => $yanit->status(), 'talep_id' => $talep->id]);
                return ['basarili' => false, 'mesaj' => 'Yapay zekâ şu an yanıt üretemedi. Lütfen tekrar deneyin.'];
            }
            $cevap = data_get($yanit->json(), 'output.0.content.0.text') ?? data_get($yanit->json(), 'output_text') ?? '';
            return blank($cevap)
                ? ['basarili' => false, 'mesaj' => 'Yapay zekâdan okunabilir yanıt alınamadı.']
                : ['basarili' => true, 'mesaj' => mb_strimwidth(trim($cevap), 0, 8000, '…', 'UTF-8')];
        } catch (\Throwable $hata) {
            Log::warning('İZGİOS AI geliştirme bağlantı hatası.', ['talep_id' => $talep->id, 'sebep' => $hata->getMessage()]);
            return ['basarili' => false, 'mesaj' => 'Yapay zekâ hizmetine ulaşılamadı. Bağlantı ve API ayarlarını kontrol edin.'];
        }
    }

    private function maskele(string $metin): string
    {
        $metin = preg_replace('/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/u', '[e-posta]', $metin);
        return preg_replace('/\b\d{10,11}\b/u', '[telefon]', $metin);
    }
}
