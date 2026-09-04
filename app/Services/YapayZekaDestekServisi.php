<?php

namespace App\Services;

use App\Models\DestekTalebi;
use Illuminate\Support\Facades\Log;

class YapayZekaDestekServisi
{
    public function __construct(private readonly DervisBilgiBankasiServisi $bilgiBankasi, private readonly YapayZekaIstemcisi $istemci)
    {
    }

    public function analizEt(DestekTalebi $talep): array
    {
        $bilgiCevabi = $this->bilgiBankasi->cevap(
            $this->bilgiBankasi->eslestir($talep->baslik . "\n" . $talep->mesaj)
        );

        if ($bilgiCevabi) {
            return ['durum' => 'cozum_onerildi', 'ozet' => $bilgiCevabi['ozet'], 'cozum' => $bilgiCevabi['cozum']];
        }

        if (! $this->istemci->hazirMi()) {
            return ['durum' => 'sistem_yoneticisine_yonlendirildi', 'ozet' => 'Derviş, bu konu için Bilgi Merkezi eşleşmesi bulamadı.', 'cozum' => 'Talep Sistem Yöneticisi inceleme kuyruğuna alındı. Ekran adresi, yapılan adım ve varsa hata kodunu ekleyin.'];
        }

        $metin = $this->hassasVeriyiMaskele($talep->baslik . "\n" . $talep->mesaj);
        $istem = <<<TEXT
Sen Derviş'sin: İZGİOS oto servis otomasyonunun güvenli destek asistanısın.
Destek talebini Türkçe değerlendir. Kod değiştirme, veri silme, sunucu komutu, ödeme veya dış sistem işlemi önerme.
Basit kullanıcı sorunlarında kısa, uygulanabilir çözüm öner ve ilgili İZGİOS modülünü belirt. Teknik hata, güvenlik, veri kaybı, ödeme veya birden fazla firmayı etkileyen sorunlarda Sistem Yöneticisine yönlendir ve güvenli işlem planı öner. Asla sorunu çözdüğünü, kod değiştirdiğini veya sistemde işlem yaptığını iddia etme.
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
            $cevap = $this->istemci->metin($istem);
            return $this->cevabiAyristir($cevap);
        } catch (\Throwable $hata) {
            Log::warning('İZGİOS AI destek bağlantı hatası.', ['talep_id' => $talep->id, 'sebep' => $hata->getMessage()]);
            return ['durum' => 'sistem_yoneticisine_yonlendirildi', 'ozet' => 'Yapay zekâ hizmetine ulaşılamadı.', 'cozum' => 'Talep Sistem Yöneticisi inceleme kuyruğuna alındı.'];
        }
    }

    public function yanitlaMesaj(DestekTalebi $talep, string $mesaj): string
    {
        $bilgiCevabi = $this->bilgiBankasi->cevap($this->bilgiBankasi->eslestir($mesaj));

        if ($bilgiCevabi) {
            return $bilgiCevabi['cozum'];
        }

        if (! $this->istemci->hazirMi()) {
            return 'Bu mesaj için Bilgi Merkezi’nde doğrudan bir yanıt bulunamadı. Ekran adresini, yaptığınız son adımı ve varsa hata kodunu paylaşın; Sistem Yöneticisi inceleme kuyruğuna aktarılır.';
        }

        $metin = $this->hassasVeriyiMaskele($mesaj);
        $istem = <<<TEXT
Sen Derviş'sin: İZGİOS oto servis otomasyonunun güvenli destek asistanısın.
Türkçe, kısa ve doğrudan yanıt ver. Kod, veri, sunucu veya dış sistem üzerinde işlem yapma ve yaptığını iddia etme.
Kullanıcıya kontrol edeceği ekranı ve adımları söyle. Çözemediğin teknik bir sorun varsa Sistem Yöneticisinin talep konuşmasını inceleyeceğini belirt.
Talep başlığı: {$talep->baslik}
Kullanıcı mesajı: {$metin}
TEXT;

        try {
            $cevap = $this->istemci->metin($istem);

            return filled($cevap)
                ? mb_strimwidth(trim($cevap), 0, 1800, '…', 'UTF-8')
                : 'Yanıt hazırlanamıyor. Mesajınız Sistem Yöneticisi inceleme kuyruğunda bekliyor.';
        } catch (\Throwable $hata) {
            Log::warning('İZGİOS AI destek mesaj yanıtı hatası.', ['talep_id' => $talep->id, 'sebep' => $hata->getMessage()]);
            return 'Derviş şu an yanıt üretemedi. Mesajınız Sistem Yöneticisi inceleme kuyruğunda bekliyor.';
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
