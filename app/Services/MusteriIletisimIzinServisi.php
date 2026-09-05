<?php

namespace App\Services;

use App\Models\Arac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusteriIletisimIzinServisi
{
    public const METIN_SURUMU = '2026-09-01-v2';

    public const SERVIS_METNI = 'Araç kabulü, servis durumu, teklif, randevu, bakım hatırlatması, teslim ve servis evrakları hakkında e-posta, SMS, WhatsApp veya telefon yoluyla bilgilendirilmek için sesli ve yazılı iletişime izin veriyorum.';

    public const TICARI_METIN = 'Sigorta, bakım paketleri, indirimler, kampanyalar, doğum günü ve özel gün içerikleri hakkında e-posta, SMS, WhatsApp veya telefon yoluyla ticari ileti almak için sesli ve yazılı iletişime izin veriyorum.';

    public const SERVIS_HUKUKI_METIN = <<<'TEXT'
Veri sorumlusu, araç servis kaydını oluşturan ve bu ekranda unvanı gösterilen firmadır. İletişim bilgileriniz; araç kabulü, arıza tespiti, teklif ve onay süreçleri, servis durum değişiklikleri, randevu, bakım hatırlatması, teslim, servis evrakları ve hizmet sonrası destek amacıyla işlenir.

Bu kapsamda ad-soyad, telefon numarası, e-posta adresi, araç ve servis kaydı bilgileri; 6698 sayılı Kişisel Verilerin Korunması Kanunu'nun 4'üncü maddesindeki genel ilkelere uygun olarak, 5'inci maddede düzenlenen sözleşmenin kurulması veya ifası, hukuki yükümlülük ve meşru menfaat şartları ile açık rıza gereken işlemlerde açık rızanıza dayanılarak işlenebilir. İletişimler e-posta, SMS, WhatsApp ve telefon kanallarından yapılabilir.

Veriler, yalnızca belirtilen amaçların gerektirdiği ölçüde; barındırma, e-posta, SMS, iletişim ve teknik destek hizmeti sağlayan veri işleyenlerle, yetkili kamu kurumlarıyla ve hukuken zorunlu hâllerde diğer yetkili mercilerle paylaşılabilir. Veriler elektronik yöntemlerle elde edilir; ilgili mevzuat ve uyuşmazlık/ispata ilişkin zamanaşımı süreleri boyunca, amaçla sınırlı olarak saklanır ve süre sonunda güvenli biçimde silinir, yok edilir veya anonim hâle getirilir.

6698 sayılı Kanun'un 11'inci maddesi kapsamında verilerinizin işlenip işlenmediğini öğrenme, bilgi talep etme, amacına uygun kullanılıp kullanılmadığını öğrenme, aktarılan kişileri bilme, düzeltme, silme veya yok etme isteme, bu işlemlerin aktarılan kişilere bildirilmesini isteme, otomatik işlemler sonucu aleyhe bir sonuca itiraz etme ve zararın giderilmesini talep etme haklarına sahipsiniz. Başvurunuzu veri sorumlusu firmaya iletebilirsiniz. Açık rızanızı geleceğe etkili olarak her zaman geri çekebilirsiniz; geri çekme öncesindeki işlemlerin hukuka uygunluğu etkilenmez.
TEXT;

    public const TICARI_HUKUKI_METIN = <<<'TEXT'
Veri sorumlusu, araç servis kaydını oluşturan ve bu ekranda unvanı gösterilen firmadır. Ad-soyad, telefon numarası, e-posta adresi ve müşteri/araç ilişki bilgileriniz; sigorta, bakım paketleri, indirimler, kampanyalar, doğum günü ve özel gün içeriklerinin sunulması amacıyla işlenir. Ticari ileti e-posta, SMS, WhatsApp veya telefon kanallarından gönderilebilir.

Ticari elektronik iletiler, 6563 sayılı Elektronik Ticaretin Düzenlenmesi Hakkında Kanun'un 6'ncı maddesi ile Ticari İletişim ve Ticari Elektronik İletiler Hakkında Yönetmelik uyarınca önceden verdiğiniz onaya dayanır. Kişisel verilerin bu amaçla işlenmesi 6698 sayılı Kişisel Verilerin Korunması Kanunu'nun 4'üncü ve 5'inci maddeleri uyarınca açık rızanıza dayanır. Onayınız verilmediğinde veya geri çekildiğinde ticari ileti gönderilmez; servis hizmetinin sunulması bu ticari ileti iznine bağlı değildir.

Onay ve ret bilgileriniz, yürürlükteki mevzuatın gerektirdiği hâllerde İleti Yönetim Sistemi'ne (İYS) kaydedilebilir. Veriler; ileti gönderimi, barındırma ve teknik destek hizmeti sağlayan veri işleyenlerle, İYS ile ve kanunen yetkili mercilerle amaçla sınırlı olarak paylaşılabilir. İzin ve ispat kayıtları ilgili mevzuatta öngörülen süre boyunca saklanır.

Ticari ileti onayınızı dilediğiniz zaman, gerekçe göstermeden ve ücretsiz olarak geri çekebilirsiniz. Ret bildiriminizin ardından ticari iletiler mevzuatta öngörülen süre içinde durdurulur. Ayrıca 6698 sayılı Kanun'un 11'inci maddesindeki bilgi alma, düzeltme, silme/yok etme, aktarılanları öğrenme, itiraz ve zararınızın giderilmesini talep etme haklarınızı veri sorumlusu firmaya başvurarak kullanabilirsiniz.
TEXT;

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
                'servis_metni_hash' => hash('sha256', self::SERVIS_METNI."\n\n".self::SERVIS_HUKUKI_METIN),
                'ticari_metni_surumu' => self::METIN_SURUMU,
                'ticari_metni_hash' => hash('sha256', self::TICARI_METIN."\n\n".self::TICARI_HUKUKI_METIN),
                'ip_adresi' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 2000),
                'qr_token_hash' => hash('sha256', $arac->qr_token),
                'onay_at' => $simdi,
                'created_at' => $simdi,
            ]);
        });
    }
}
