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

        foreach ($this->konular() as $konu) {
            foreach ($konu['anahtarlar'] as $anahtar) {
                if (str_contains($metin, $anahtar)) return $konu;
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
            ['id'=>'musteri-duzenle','soru'=>'Müşteri kaydı nasıl aranır, düzenlenir veya silinir?','yanit'=>'Müşteriler ekranında ad, telefon, e-posta veya kimlik bilgisiyle arayın. Müşteri kartındaki Düzenle ile bilgileri değiştirin. Yetkiniz varsa Sil ile kaldırabilirsiniz; işlem firma sahibine bildirilir ve Sistem Yöneticisinin Silme Kayıtları ekranına yazılır.','anahtarlar'=>['müşteri ara','musteri ara','müşteri düzenle','müşteri sil']],
            ['id'=>'arac-yonetimi','soru'=>'Araç nasıl aranır, düzenlenir, silinir ve QR etiketi alınır?','yanit'=>'Araçlar ekranında plaka, müşteri adı veya telefonla arayın. Araç kartından Düzenle veya yetkiliyseniz Sil işlemini kullanın. Dijital kimlik/QR seçeneği müşterinin servis ve bakım geçmişi etiketini açar. Aynı plaka ikinci kez kaydedilemez.','anahtarlar'=>['araç ara','arac ara','araç düzenle','araç sil','qr etiketi']],
            ['id'=>'servis-sureci','soru'=>'Servis süreci hangi sırayla ilerler?','yanit'=>'Sıra Müşteri > Araç > Servis Kabul > İş Emri şeklindedir. İş emrinde durum Bekliyor, İşlemde, Teslime Hazır ve Tamamlandı olarak ilerler. Her aşamaya yalnız bir süreç notu girilir; tamamlandıktan sonra yeni not eklenmez.','anahtarlar'=>['servis süreci','servis sureci','bekliyor işlemde','teslime hazır','tamamlandı not']],
            ['id'=>'servis-islem-parca','soru'=>'İş emrine işlem, işçilik ve yedek parça nasıl eklenir?','yanit'=>'İş Emirleri içinden kaydı açın. Servis İşlemleri bölümünde yapılan işi ve işçilik tutarını, Yedek Parça bölümünde kullanılan parçayı, miktarı ve fiyatı girin. Bakım işlemlerini Bakım sekmesinde, diğer servis işlerini Servis sekmesinde kaydedin.','anahtarlar'=>['işçilik ekle','iscilik ekle','parça ekle','servis işlemi ekle']],
            ['id'=>'fotograflar','soru'=>'Araç kabul, servis, bakım ve parça fotoğrafları nasıl eklenir?','yanit'=>'Servis kabulde aracın dört cephesi kabul/tespit fotoğrafı olarak eklenir. İş emrinde servis süreci, bakım ve parça fotoğraflarını kendi başlığı altında yükleyin. QR geçmişinde servis fotoğrafları yalnız Servis, bakım fotoğrafları yalnız Bakım sekmesindeki ilgili kilometre altında görünür.','anahtarlar'=>['fotoğraf ekle','fotograf ekle','servis fotoğraf','bakım fotoğraf','parça fotoğraf']],
            ['id'=>'randevu','soru'=>'Randevu nasıl oluşturulur, düzenlenir veya silinir?','yanit'=>'Randevu ve Servis Ajandası ekranında müşteri, araç, hizmet, tarih ve saat seçerek kaydedin. Kayıt kartındaki Düzenle ile bilgileri değiştirin, Sil ile iptal edin veya Servise Al ile kabul akışını başlatın.','anahtarlar'=>['randevu oluştur','randevu olustur','randevu düzenle','randevu sil','ajanda']],
            ['id'=>'hatirlatma','soru'=>'Ay bazlı bakım hatırlatması nasıl kurulur?','yanit'=>'İş emrinde aracın servise giriş tarihini ve bakım periyodunu ay olarak seçin. Sistem giriş tarihine seçilen ayı ekleyerek yeni hatırlatma tarihini hesaplar; örneğin 12.05 tarihli kayıtta 3 ay seçilirse 12.08 için planlar.','anahtarlar'=>['ay bazlı hatırlatma','3 ay sonra','sonraki bakım tarihi']],
            ['id'=>'iletisim-api','soru'=>'E-posta, WhatsApp ve SMS entegrasyonları nasıl kurulur?','yanit'=>'API ve İletişim Entegrasyonları ekranında firma için SMTP, WhatsApp veya SMS sağlayıcısı bilgilerini girin. E-posta bağlantısını test edin. WhatsApp API yoksa paylaşım düğmesi telefon veya web uygulamasını hazır mesajla açar. Gizli anahtarlar ekranda yeniden gösterilmez.','anahtarlar'=>['smtp kur','whatsapp api','sms api','iletişim entegrasyonu']],
            ['id'=>'merkezi-yapay-zeka','soru'=>'Yapay zekâ API bağlantısı nasıl çalışır?','yanit'=>'Yalnız Sistem Yöneticisi, API ve İletişim Entegrasyonları ekranındaki Merkezi Yapay Zekâ bölümüne tek OpenAI anahtarı girer. Bu anahtar bütün firmalardaki asistan ve hata analizi için ortak kullanılır; firmalara ayrı anahtar tanımlanmaz.','anahtarlar'=>['yapay zeka api','openai anahtar','merkezi yapay zeka']],
            ['id'=>'depo-detay','soru'=>'Depo, stok, raf ve OEM kayıtları nasıl yönetilir?','yanit'=>'Depo ekranında önce depo ve rafları oluşturun. Yedek parça kartında ürün adı, OEM kodu, barkod, alış/satış fiyatı, miktar ve kritik stok seviyesini kaydedin. Raf Atama ile fiziksel konumu bağlayın; giriş ve çıkışları stok hareketi olarak işleyin.','anahtarlar'=>['depo oluştur','stok girişi','stok çıkışı','oem kodu','raf atama']],
            ['id'=>'muhasebe-detay','soru'=>'Cari, fiş, teklif ve fatura işlemleri nasıl yapılır?','yanit'=>'Muhasebe Merkezinde firmayı seçin. Cari Hesaplar bölümünde müşteri/tedarikçi kartını, Muhasebe Fişleri bölümünde gelir-gider satırlarını oluşturun. Teklif ve Fatura ekranlarında belgeyi kaydedip yazdırma veya Excel seçeneklerini kullanın.','anahtarlar'=>['cari hesap oluştur','muhasebe fişi','teklif oluştur','fatura oluştur']],
            ['id'=>'fis-kamera','soru'=>'Telefon kamerasıyla muhasebe fişi nasıl eklenir?','yanit'=>'Muhasebe Fişleri ekranındaki kamera düğmesini açın, fişi net ve tam kadraj çekin. Okunan ürün, tutar ve KDV satırlarını mutlaka kontrol edin; eksik veya yanlış alanları düzelttikten sonra kaydedin.','anahtarlar'=>['fiş fotoğrafı','fis fotografi','fiş kamera','ocr fiş']],
            ['id'=>'kullanici-rol','soru'=>'Personel ve rol yetkileri nasıl yönetilir?','yanit'=>'Sistem Yöneticisi tüm firmaları ve ekranları görür. Firma sahibi yalnız kendi firmasını yönetir. Usta servis iş emirlerini, yedek parça rolü depo/stok ekranlarını, muhasebe rolü mali ekranları, İK rolü personel süreçlerini kendi firması kapsamında görür.','anahtarlar'=>['rol yetkisi','personel rolü','usta rolü','firma sahibi yetki']],
            ['id'=>'firma-sube','soru'=>'Firma, şube ve firma ayarları nasıl düzenlenir?','yanit'=>'Firma Yönetimi ekranında firma kartını oluşturun veya Düzenle ile iletişim, logo ve Google yorum bağlantısını güncelleyin. Şubeleri firmaya bağlayın. Firma kullanıcıları yalnız bağlı oldukları firma verilerine erişir.','anahtarlar'=>['firma düzenle','şube ekle','sube ekle','google yorum bağlantısı']],
            ['id'=>'raporlar','soru'=>'Raporlar nasıl alınır?','yanit'=>'Raporlar ekranında firma, tarih aralığı ve rapor türünü seçin. Ekran sonucunu kontrol ettikten sonra desteklenen raporlarda Excel veya yazdırma seçeneğini kullanın. Firma kullanıcıları yalnız kendi firma verisini raporlar.','anahtarlar'=>['rapor al','excel rapor','servis raporu']],
            ['id'=>'silme-denetimi','soru'=>'Silinen kayıtlar nasıl takip edilir?','yanit'=>'Müşteri, araç, servis, randevu, personel, depo ve yedek parça gibi silmelerde işlemi yapan kullanıcı, rol, IP ve kayıt özeti denetime yazılır; firma sahibine e-posta gider. Sistem Yöneticisi Silme Kayıtları ekranından ayrıntıları görür.','anahtarlar'=>['silme kayıtları','silinen kayıt','kim sildi']],
            ['id'=>'sistem-hatalari','soru'=>'Sistem hataları nasıl izlenir?','yanit'=>'Sistem Yöneticisi Sistem Hataları ekranında açık uygulama hatalarını görür. Otomatik görev her 10 dakikada logları yeniler. Artık görülmeyen hata çözülmüş kabul edilerek açık listeden kaldırılır ve “Sistem hatası çözüldü” açıklamasıyla Silme Kayıtlarına aktarılır.','anahtarlar'=>['sistem hataları','gerçek zamanlı hata','hata izleme']],
            ['id'=>'guvenli-cikis','soru'=>'Güvenli çıkış ve şifre işlemleri nerede?','yanit'=>'Sol menünün altındaki Güvenli Çıkış oturumu kapatır. Şifre değiştirmek için Hesabım > Şifre alanını kullanın. Şifremi Unuttum akışı kayıtlı e-posta üzerinden ilerler; şifrenizi veya API anahtarınızı destek mesajına yazmayın.','anahtarlar'=>['güvenli çıkış','şifre değiştir','şifremi unuttum']],
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
