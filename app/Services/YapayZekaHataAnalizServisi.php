<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class YapayZekaHataAnalizServisi
{
    public function __construct(private readonly YapayZekaIstemcisi $istemci) {}

    public function analizEt(array $hatalar): array
    {
        if (! $this->istemci->hazirMi()) {
            return ['basarili' => false, 'mesaj' => 'Yapay zekâ bağlantısı yapılandırılmamış. API ayarlarını kontrol edin.'];
        }

        $ozetler = collect($hatalar)->map(fn (array $hata) => [
            'kod' => $hata['kod'] ?? 'HATA',
            'ekran' => $hata['ekran'] ?? 'Sistem işlemi',
            'islem' => $hata['islem'] ?? 'Uygulama akışı',
            'sebep' => $this->maskele((string) ($hata['sebep'] ?? 'Belirsiz')),
        ])->values()->all();

        $istem = "Sen İZGİOS uygulamasının güvenli hata analiz danışmanısın. Sana yalnızca maskelenmiş hata özetleri verildi; ham günlük, yığın izi, kullanıcı verisi veya erişim anahtarı yok. Sadece analiz ve çözüm önerisi sun. Kod, veri veya sunucu üzerinde işlem yaptığını söyleme.\n\nTürkçe, kısa ve net olarak şu başlıklarla yanıt ver:\nÖZET:\nÖNCELİKLİ HATALAR:\nOLASI NEDENLER:\nÖNERİLEN ÇÖZÜM ADIMLARI:\nKONTROL LİSTESİ:\nONAY DURUMU: Uygulama için Sistem Yöneticisi onayı gerekir.\n\nMASKELENMİŞ HATA ÖZETLERİ:\n" . json_encode($ozetler, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        try {
            $cevap = $this->istemci->metin($istem);
            return blank($cevap)
                ? ['basarili' => false, 'mesaj' => 'Yapay zekâdan okunabilir bir analiz alınamadı.']
                : ['basarili' => true, 'mesaj' => mb_strimwidth(trim($cevap), 0, 12000, '…', 'UTF-8')];
        } catch (\Throwable $hata) {
            Log::warning('İZGİOS AI hata analizi bağlantı hatası.', ['sebep' => $hata->getMessage()]);
            return ['basarili' => false, 'mesaj' => 'Yapay zekâ hizmetine ulaşılamadı. Bağlantı ve API ayarlarını kontrol edin.'];
        }
    }

    private function maskele(string $metin): string
    {
        $kurallar = [
            '/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/u' => '[e-posta]',
            '/\b(?:\+?90|0)?\s?5\d{2}[\s.-]?\d{3}[\s.-]?\d{2}[\s.-]?\d{2}\b/u' => '[telefon]',
            '/\bsk-[A-Za-z0-9_-]{12,}\b/u' => '[api-anahtarı]',
            '/\b(Bearer\s+)[A-Za-z0-9._-]{12,}\b/i' => '$1[erişim-tokenı]',
            '/\b(api[_-]?key|token|secret|password|parola)\s*[=:]\s*["\']?[^\s,;"\']+/iu' => '$1=[gizli]',
        ];
        return (string) preg_replace(array_keys($kurallar), array_values($kurallar), $metin);
    }
}
