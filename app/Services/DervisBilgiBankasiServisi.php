<?php

namespace App\Services;

/**
 * Derviş'in ilk başvurduğu, kişisel veri içermeyen uygulama bilgi bankası.
 * Yeni ekranlar eklendikçe bu liste, görsel/video bağlantılarıyla genişletilir.
 */
class DervisBilgiBankasiServisi
{
    public function eslestir(string $metin): ?array
    {
        $metin = mb_strtolower($metin, 'UTF-8');

        foreach ($this->konular() as $konu) {
            foreach ($konu['anahtarlar'] as $anahtar) {
                if (str_contains($metin, $anahtar)) {
                    return $konu;
                }
            }
        }

        foreach ($this->sss() as $sss) {
            foreach ($sss['anahtarlar'] as $anahtar) {
                if (str_contains($metin, $anahtar)) {
                    return [
                        'modul' => 'Bilgi Merkezi',
                        'ekran' => 'Sık Sorulan Sorular',
                        'ozet' => $sss['yanit'],
                        'adimlar' => [],
                        'baglanti' => '/sss#' . $sss['id'],
                    ];
                }
            }
        }

        return null;
    }

    public function cevap(?array $konu): ?array
    {
        if (!$konu) {
            return null;
        }

        $adimlar = collect($konu['adimlar'])->map(fn (string $adim, int $sira) => ($sira + 1) . '. ' . $adim)->implode(' ');

        $adimAciklamasi = filled($adimlar) ? ' ' . $adimlar : '';

        return [
            'ozet' => "{$konu['modul']} > {$konu['ekran']} için Bilgi Merkezi eşleşmesi bulundu.",
            'cozum' => "{$konu['ozet']}{$adimAciklamasi} Ayrıntılı yardım: {$konu['baglanti']}",
        ];
    }

    /** @return array<int, array{id:string,soru:string,yanit:string,anahtarlar:array<int,string>}> */
    public function sss(): array
    {
        return [
            ['id' => 'mustteri-arac', 'soru' => 'Yeni müşteri ve araç nasıl eklenir?', 'yanit' => 'Müşteriler > Yeni Müşteri ile iletişim kartını kaydedin. Kaydet ve araç kartına geç ile aracı aynı müşteriye bağlayın; araç kaydı sonrası Servis Kabul açılır.', 'anahtarlar' => ['yeni müşteri', 'yeni musteri', 'müşteri ekle', 'musteri ekle', 'araç ekle', 'arac ekle']],
            ['id' => 'servis-kabul', 'soru' => 'Servis kabul nasıl başlatılır?', 'yanit' => 'Servis Kabul ekranında plaka, QR veya kayıtlı araçtan seçin. Kilometre, müşteri şikâyeti ve kabul bilgilerini kaydedin. Kayıt iş emrine dönüşür.', 'anahtarlar' => ['servis kabul nasıl', 'servis kabul başlat', 'servis kabul baslat']],
            ['id' => 'usta-is-emri', 'soru' => 'Usta iş emrini nasıl alır?', 'yanit' => 'Usta, kendi firmasındaki araç için plaka veya QR ile iş emrini açar ve Üzerime Al seçeneğini kullanır. Usta yalnız kendi aldığı iş emirlerini görür.', 'anahtarlar' => ['usta iş emri', 'usta is emri', 'üzerime al', 'uzerime al']],
            ['id' => 'bakim-hatirlatma', 'soru' => 'Bakım hatırlatması nasıl çalışır?', 'yanit' => 'Hatırlatmalar kilometreye değil tarih bazlıdır. Yeni servis veya bakım girildiğinde önceki hatırlatma iptal olur, usta tarafından tanımlanan yeni tarih geçerli olur.', 'anahtarlar' => ['bakım hatırlatması nasıl', 'bakim hatirlatmasi nasil']],
            ['id' => 'google-yorum', 'soru' => 'Google yorum isteme bağlantısını nasıl alır ve eklerim?', 'yanit' => 'Google Haritalar veya Google Arama üzerinden işletmenizin Business Profile kaydını açın. Yorum iste bağlantısını kopyalayın. İZGİOS’ta Sistem Ayarları > Firma Yönetimi > Firma Kartı > Düzenle yolundaki Google yorum bağlantısı alanına yapıştırın. Araç teslim edildi mesajında bu bağlantı müşteriye otomatik gönderilir.', 'anahtarlar' => ['google yorum', 'yorum linki', 'review link', 'yorum bağlantısı', 'yorum baglantisi']],
            ['id' => 'qr', 'soru' => 'QR ekranında müşteri ne görür?', 'yanit' => 'Müşteri yalnız kendi plakası için servis ve periyodik bakım geçmişini ayrı sekmelerde görür. Detayda yapılan işlem, kilometre ve tarih yer alır.', 'anahtarlar' => ['qr ekranında', 'qr ekrani', 'müşteri qr', 'musteri qr']],
            ['id' => 'muhasebe-depo', 'soru' => 'Muhasebe ve depo kimler içindir?', 'yanit' => 'Muhasebe firma sahibi ve muhasebe rolüne; depo firma sahibi ve yedek parça rolüne açıktır. Her kayıt bağlı firmayla sınırlıdır.', 'anahtarlar' => ['muhasebe kim', 'depo kim', 'muhasebe yetki', 'depo yetki']],
            ['id' => 'destek', 'soru' => 'Bir hata ile karşılaşırsam ne yapmalıyım?', 'yanit' => 'Destek Merkezi > Yeni Destek Talebi üzerinden ekran adresini, yaptığınız adımı ve hata kodunu yazın. Yapay zekâ öneri üretir; çözemezse Sistem Yöneticisi kuyruğuna aktarır.', 'anahtarlar' => ['hata ile karşılaş', 'hata ile karsilas', 'destek talebi', 'teknik hata']],
        ];
    }

    public function asistanBaglami(): string
    {
        return collect($this->sss())
            ->map(fn (array $konu) => "Soru: {$konu['soru']}\nCevap: {$konu['yanit']}")
            ->implode("\n\n");
    }

    private function konular(): array
    {
        return [
            [
                'anahtarlar' => ['müşteri', 'musteri', 'araç ekle', 'arac ekle', 'araç kartı', 'arac karti'],
                'modul' => 'Müşteriler ve Araçlar', 'ekran' => 'Yeni kayıt akışı',
                'ozet' => 'Müşteri kaydı araç kartının temelidir; araç kaydı aynı müşteriye bağlanmalıdır.',
                'adimlar' => ['Müşteriler ekranından Yeni Müşteri seçin.', 'Zorunlu iletişim bilgilerini kaydedin.', 'Kaydet ve araç kartına geç ile plakayı ve teknik bilgileri girin.'],
                'baglanti' => '/sss#mustteri-arac',
            ],
            [
                'anahtarlar' => ['servis kabul', 'iş emri', 'is emri', 'servise al', 'servis işlemi', 'servis islemi'],
                'modul' => 'Servis', 'ekran' => 'Servis kabul ve iş emri',
                'ozet' => 'Servis kabul, araçla müşteri kaydını tek bir iş emrinde birleştirir.',
                'adimlar' => ['Plaka, QR veya araç listesinden aracı seçin.', 'Kilometre ve müşteri talebini kaydedin.', 'Servisi başlat ile iş emrini açın; işlem, bakım ve parça satırlarını ekleyin.'],
                'baglanti' => '/sss#servis-kabul',
            ],
            [
                'anahtarlar' => ['periyodik', 'bakım', 'bakim', 'hatırlatma', 'hatirlatma'],
                'modul' => 'Servis', 'ekran' => 'Periyodik bakım ve hatırlatma',
                'ozet' => 'Bakım hatırlatmaları tarih bazlıdır; yeni bakım kaydı önceki bekleyen hatırlatmayı geçersiz kılar.',
                'adimlar' => ['İş emrindeki Periyodik Bakım alanından yapılan bakımı seçin.', 'Yeni hatırlatma tarihini girin.', 'İletişim Merkezi ayarlarına göre planlanan bildirim kanallarını kontrol edin.'],
                'baglanti' => '/sss#bakim-hatirlatma',
            ],
            [
                'anahtarlar' => ['qr', 'karekod', 'kare kod'],
                'modul' => 'QR Araç Geçmişi', 'ekran' => 'Müşteri görünümü',
                'ozet' => 'QR bağlantısı giriş gerektirmeden yalnız ilgili plakaya ait servis ve bakım geçmişini gösterir.',
                'adimlar' => ['Araç kartındaki QR Yazdır bağlantısını kullanın.', 'Müşteri ekranda Servis Geçmişi veya Periyodik Bakım sekmesini seçer.', 'Kilometre satırına dokunarak işlem ve tarih detayını açar.'],
                'baglanti' => '/sss#qr',
            ],
            [
                'anahtarlar' => ['cari', 'fatura', 'teklif', 'fiş', 'fis', 'muhasebe', 'kdv'],
                'modul' => 'Muhasebe', 'ekran' => 'Cari, belge ve fiş işlemleri',
                'ozet' => 'Tamamlanan servisler müşteri cari hesabına aktarılır; belge ve fişler firma bazında tutulur.',
                'adimlar' => ['Muhasebe Merkezi üzerinden çalışılacak firmayı seçin.', 'Cari, teklif, fatura veya fiş ekranından işlem türünü açın.', 'KDV dahil toplamları kontrol edip kaydedin; yazdırma/Excel bağlantısını kullanın.'],
                'baglanti' => '/sss#muhasebe',
            ],
            [
                'anahtarlar' => ['stok', 'depo', 'oem', 'barkod', 'raf'],
                'modul' => 'Depo', 'ekran' => 'Stok, raf ve barkod yönetimi',
                'ozet' => 'Parçalar OEM kodu ve raf adresiyle firma deposuna bağlı olarak takip edilir.',
                'adimlar' => ['Stok Yönetiminden ürün kartını açın.', 'OEM kodu, alış/satış fiyatı ve kritik stok seviyesini girin.', 'Raf adresini atayıp ürün veya raf barkodunu yazdırın.'],
                'baglanti' => '/sss#depo',
            ],
            [
                'anahtarlar' => ['şifre', 'sifre', 'giriş', 'giris', 'kullanıcı', 'kullanici', 'personel'],
                'modul' => 'Hesabım ve İnsan Kaynakları', 'ekran' => 'Kullanıcı erişimi',
                'ozet' => 'Kullanıcı girişi e-posta adresiyle yapılır; şifre yenileme talepleri İK onayına gider.',
                'adimlar' => ['Giriş ekranındaki Şifremi unuttum bağlantısını açın.', 'Kayıtlı e-posta adresinizi girin.', 'İK onayından sonra gelen güvenli bağlantıyla yeni şifre oluşturun.'],
                'baglanti' => '/sss#kullanici',
            ],
            [
                'anahtarlar' => ['iletişim', 'iletisim', 'whatsapp', 'sms', 'e-posta', 'mail'],
                'modul' => 'İletişim Merkezi', 'ekran' => 'Kanal ve şablon ayarları',
                'ozet' => 'Randevu, servis süreci, hatırlatma ve özel gün mesajları firma bazlı kanal tercihlerine göre planlanır.',
                'adimlar' => ['İletişim Merkezi ekranını açın.', 'Her mesaj türü için WhatsApp, SMS ve e-posta kanallarını seçin.', 'Sağlayıcı erişim bilgilerini API ve Entegrasyonlar ekranından kaydedin.'],
                'baglanti' => '/sss#iletisim',
            ],
        ];
    }
}
