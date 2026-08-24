<?php

namespace App\Services;

use App\Models\Arac;
use App\Models\Firma;
use App\Models\Musteri;
use App\Models\Servis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IletisimOtomasyonServisi
{
    public function servisKabulEdildi(Servis $servis): void
    {
        [$firmaId] = $this->servisBaglantisiniOnar($servis);

        // Araç planlanan bakımından önce tekrar servise girdiyse eski bakım
        // randevusu ve gönderim planı geçersizdir. Yeni bakım tarihi, bu yeni
        // servis kaydından sonra tanımlandığında tek güncel plan oluşturulur.
        $this->bakimPlanlariniIptalEt($firmaId, (int) $servis->arac_id);

        $this->planla($firmaId, $servis->musteri_id, $servis->arac_id, 'servis_kabul', now(), 'servis', $servis->id);
    }

    public function servisDurumuDegisti(Servis $servis, string $oncekiDurum, string $yeniDurum): void
    {
        if ($oncekiDurum === $yeniDurum) {
            return;
        }

        [$firmaId] = $this->servisBaglantisiniOnar($servis);
        $grup = match ($yeniDurum) {
            'İşlemde' => 'islem_basladi',
            'Teslime Hazır' => 'teslimata_hazir',
            'Tamamlandı' => 'teslim_edildi',
            default => null,
        };

        if ($grup) {
            $this->planla($firmaId, $servis->musteri_id, $servis->arac_id, $grup, now(), 'servis_durum', $servis->id);
        }
    }

    public function randevuOlusturuldu(int $randevuId): void
    {
        $randevu = DB::table('randevular')->where('id', $randevuId)->first();
        if (! $randevu || $randevu->durum === 'iptal') {
            return;
        }

        $this->planla($randevu->firma_id, $randevu->musteri_id, $randevu->arac_id, 'randevu_olustuldu', now(), 'randevu', $randevuId);

        $tarih = Carbon::parse($randevu->baslangic);
        foreach ([15, 7, 4, 3, 1] as $gun) {
            $zaman = $tarih->copy()->subDays($gun);
            if ($zaman->isFuture()) {
                $this->planla(
                    $randevu->firma_id,
                    $randevu->musteri_id,
                    $randevu->arac_id,
                    'randevu_yaklasiyor',
                    $zaman,
                    'randevu',
                    $randevuId,
                    "{musteri_adi}, {plaka} aracınızın randevusuna {$gun} gün kaldı. Randevu tarihiniz: {randevu_tarihi} {randevu_saati}. Randevu iptali veya değişikliği için {firma_telefonu} numarasından {firma_adi} ile iletişime geçebilirsiniz."
                );
            }
        }
    }

    public function periyodikBakimPlanla(Servis $servis): void
    {
        if (! $servis->sonraki_bakim_tarihi || ! $servis->musteri_id || ! $servis->arac_id) {
            return;
        }

        [$firmaId, $subeId] = $this->servisBaglantisiniOnar($servis);
        $tarih = Carbon::parse($servis->sonraki_bakim_tarihi)->startOfDay()->setTime(9, 0);

        DB::transaction(function () use ($servis, $tarih, $firmaId, $subeId) {
            $eskiHatirlatmalar = DB::table('randevular')
                ->where('firma_id', $firmaId)
                ->where('arac_id', $servis->arac_id)
                ->where('kaynak', 'bakim_hatirlatmasi')
                ->whereIn('durum', ['planlandi', 'teyitli'])
                ->pluck('id');

            if ($eskiHatirlatmalar->isNotEmpty()) {
                DB::table('randevular')->whereIn('id', $eskiHatirlatmalar)->update(['durum' => 'iptal', 'updated_at' => now()]);
                DB::table('iletisim_gonderim_loglari')
                    ->where('kaynak_turu', 'randevu')
                    ->whereIn('kaynak_id', $eskiHatirlatmalar)
                    ->whereIn('durum', ['planlandi', 'kuyrukta'])
                    ->update(['durum' => 'iptal', 'updated_at' => now()]);
            }

            $randevuId = DB::table('randevular')->insertGetId([
                'firma_id' => $firmaId,
                'sube_id' => $subeId,
                'musteri_id' => $servis->musteri_id,
                'arac_id' => $servis->arac_id,
                'servis_id' => $servis->id,
                'hizmet_turu' => 'Periyodik bakım hatırlatması',
                'baslangic' => $tarih,
                'durum' => 'planlandi',
                'kaynak' => 'bakim_hatirlatmasi',
                'notlar' => 'Servis kaydındaki tarih bazlı bakım hatırlatmasından otomatik oluşturuldu.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ([15, 7, 4, 3, 1] as $gun) {
                $zaman = $tarih->copy()->subDays($gun);
                if ($zaman->isFuture()) {
                    $this->planla(
                        $firmaId,
                        $servis->musteri_id,
                        $servis->arac_id,
                        'bakim_hatirlatma',
                        $zaman,
                        'randevu',
                        $randevuId,
                        "{musteri_adi}, {plaka} aracınızın planlanan bakımına {$gun} gün kaldı. Servisinizden randevu alınız. Bakım tarihiniz: ".$tarih->format('d.m.Y').'. Randevu iptali veya değişikliği için {firma_telefonu} numarasından {firma_adi} ile iletişime geçebilirsiniz.'
                    );
                }
            }

            foreach ([5, 10, 15, 20] as $gecenGun) {
                $zaman = $tarih->copy()->addDays($gecenGun);
                $this->planla(
                    $firmaId,
                    $servis->musteri_id,
                    $servis->arac_id,
                    'bakim_gecikme',
                    $zaman,
                    'randevu',
                    $randevuId,
                    "{musteri_adi}, {plaka} aracınızın planlanan bakım tarihi üzerinden {$gecenGun} gün geçti. Aracınızın bakımını planlamak için {firma_telefonu} numarasından {firma_adi} ile iletişime geçebilirsiniz."
                );
            }
        });
    }

    private function bakimPlanlariniIptalEt(int $firmaId, int $aracId): void
    {
        $randevuIds = DB::table('randevular')
            ->where('firma_id', $firmaId)
            ->where('arac_id', $aracId)
            ->where('kaynak', 'bakim_hatirlatmasi')
            ->whereIn('durum', ['planlandi', 'teyitli'])
            ->pluck('id');

        if ($randevuIds->isEmpty()) {
            return;
        }

        DB::table('randevular')->whereIn('id', $randevuIds)->update(['durum' => 'iptal', 'updated_at' => now()]);
        DB::table('iletisim_gonderim_loglari')
            ->where('kaynak_turu', 'randevu')
            ->whereIn('kaynak_id', $randevuIds)
            ->whereIn('durum', ['planlandi', 'kuyrukta'])
            ->update(['durum' => 'iptal', 'updated_at' => now()]);
    }

    private function servisBaglantisiniOnar(Servis $servis): array
    {
        $firmaId = (int) $servis->firma_id;
        $subeId = $servis->sube_id ? (int) $servis->sube_id : null;
        $arac = $servis->arac_id ? DB::table('araclar')->where('id', $servis->arac_id)->select('firma_id', 'sube_id')->first() : null;
        $musteri = $servis->musteri_id ? DB::table('musteris')->where('id', $servis->musteri_id)->select('firma_id', 'sube_id')->first() : null;
        $cari = $servis->musteri_id ? DB::table('cari_hesaplar')->where('musteri_id', $servis->musteri_id)->whereNotNull('firma_id')->select('firma_id')->first() : null;

        $firmaId = $firmaId ?: (int) ($arac?->firma_id ?: $musteri?->firma_id ?: $cari?->firma_id);
        $subeId ??= $arac?->sube_id ?: $musteri?->sube_id;

        if (! $firmaId) {
            throw ValidationException::withMessages(['firma_id' => 'Bu servis kaydının firma bağlantısı bulunamadı. Önce araç veya müşteri kartındaki firma bilgisini tamamlayın.']);
        }

        if ((int) $servis->firma_id !== $firmaId || (int) ($servis->sube_id ?? 0) !== (int) ($subeId ?? 0)) {
            $servis->update(['firma_id' => $firmaId, 'sube_id' => $subeId]);
            $servis->setAttribute('firma_id', $firmaId);
            $servis->setAttribute('sube_id', $subeId);
        }

        return [$firmaId, $subeId];
    }

    public function planla(int $firmaId, ?int $musteriId, ?int $aracId, string $grup, Carbon|string $zaman, string $kaynakTuru, int $kaynakId, ?string $varsayilanSablon = null): void
    {
        $ayar = DB::table('firma_iletisim_kanal_ayarlari')
            ->where('firma_id', $firmaId)
            ->where('mesaj_grubu', $grup)
            ->first();

        // İlk kurulumda tüm otomasyon e-posta ile çalışır. Firma sahibi daha
        // sonra İletişim Merkezi'nden SMS/WhatsApp tercihini ayrıca açabilir.
        if (! $ayar) {
            $ayar = (object) [
                'aktif' => true,
                'email' => true,
                'sms' => false,
                'whatsapp' => false,
                'sablon' => null,
            ];
        }

        if (! $ayar->aktif) {
            return;
        }

        $musteri = $musteriId ? Musteri::find($musteriId) : null;
        $arac = $aracId ? Arac::find($aracId) : null;
        $firma = Firma::find($firmaId);
        $planlananAt = Carbon::parse($zaman);
        $sablon = $varsayilanSablon ?: ($ayar->sablon ?: $this->varsayilanSablon($grup));
        $qrTakipLinki = $arac?->qr_token ? route('qr.servis.show', ['token' => $arac->qr_token, 'ekran' => 'servis']) : null;
        $qrSifre = $arac ? mb_substr(preg_replace('/[^A-Z0-9]/u', '', mb_strtoupper($arac->plaka)), -4) : null;
        $servisNo = in_array($kaynakTuru, ['servis', 'servis_durum'], true)
            ? (Servis::find($kaynakId)?->servis_no ?: (string) $kaynakId)
            : '-';
        $mesaj = strtr($sablon, [
            '{musteri_adi}' => $musteri?->ad_soyad ?: 'Değerli müşterimiz',
            '{plaka}' => $arac?->plaka ?: 'aracınız',
            '{firma_adi}' => $firma?->unvan ?: 'servisiniz',
            '{randevu_tarihi}' => $planlananAt->format('d.m.Y'),
            '{randevu_saati}' => $planlananAt->format('H:i'),
            '{bakim_tarihi}' => $planlananAt->format('d.m.Y'),
            '{servis_no}' => $servisNo,
            '{firma_telefonu}' => $firma?->telefon ?: 'servisinizin iletişim hattı',
            '{qr_takip_linki}' => $qrTakipLinki ?: 'QR takip bağlantısı tanımlı değil',
            '{qr_sifre}' => $qrSifre ?: '-',
            '{yorum_linki}' => $firma?->google_yorum_linki ?: 'yorum bağlantısı yakında paylaşılacaktır',
        ]);
        if ($qrTakipLinki && in_array($kaynakTuru, ['servis', 'servis_durum'], true) && ! str_contains($mesaj, 'Şifre:')) {
            $mesaj .= " Detaylar: {$qrTakipLinki} Şifre: {$qrSifre}";
        }

        foreach (['whatsapp', 'sms', 'email'] as $kanal) {
            if (! $ayar->{$kanal}) {
                continue;
            }
            $alici = $kanal === 'email' ? $musteri?->email : $musteri?->telefon;
            if (! $alici) {
                continue;
            }
            $varMi = DB::table('iletisim_gonderim_loglari')
                ->where('kaynak_turu', $kaynakTuru)
                ->where('kaynak_id', $kaynakId)
                ->where('mesaj_grubu', $grup)
                ->where('kanal', $kanal)
                ->where('planlanan_at', $planlananAt)
                ->exists();
            if ($varMi) {
                continue;
            }
            DB::table('iletisim_gonderim_loglari')->insert([
                'firma_id' => $firmaId,
                'musteri_id' => $musteriId,
                'arac_id' => $aracId,
                'mesaj_grubu' => $grup,
                'kanal' => $kanal,
                'durum' => 'planlandi',
                'alici' => $alici,
                'alici_maskeli' => $this->maskele($alici, $kanal),
                'mesaj' => $mesaj,
                'planlanan_at' => $planlananAt,
                'kaynak_turu' => $kaynakTuru,
                'kaynak_id' => $kaynakId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function varsayilanSablon(string $grup): string
    {
        return match ($grup) {
            'randevu_olustuldu' => 'Merhaba {musteri_adi}, {plaka} için randevunuz oluşturuldu.',
            'randevu_yaklasiyor' => '{musteri_adi}, {plaka} aracınızın randevusu yaklaşıyor.',
            'servis_kabul' => 'Merhaba {musteri_adi}, {plaka} aracınız servise kabul edildi. Servis numaranız: {servis_no}. Detaylar: {qr_takip_linki} Şifreniz: {qr_sifre}',
            'islem_basladi' => '{musteri_adi}, {plaka} aracınızın servis işlemleri başladı.',
            'teslimata_hazir' => '{musteri_adi}, {plaka} aracınız teslimata hazır. Teslim saatiniz için {firma_telefonu} numarasından {firma_adi} ile iletişime geçebilirsiniz.',
            'teslim_edildi' => '{musteri_adi}, {plaka} aracınız teslim edildi. Bizi tercih ettiğiniz için teşekkür ederiz. Deneyiminizi Google üzerinde paylaşmak isterseniz: {yorum_linki}',
            'bakim_hatirlatma' => '{musteri_adi}, {plaka} için planlanan bakım tarihiniz yaklaşıyor: {bakim_tarihi}.',
            'bakim_gecikme' => '{musteri_adi}, {plaka} aracınızın bakım zamanı geçti. Servisinizle iletişime geçebilirsiniz.',
            default => '{musteri_adi}, {plaka} için bilgilendirmeniz bulunmaktadır.',
        };
    }

    private function maskele(string $alici, string $kanal): string
    {
        if ($kanal === 'email') {
            [$kullanici, $alan] = array_pad(explode('@', $alici, 2), 2, '');
            return mb_substr($kullanici, 0, 2).'***@'.$alan;
        }

        return strlen($alici) > 4 ? substr($alici, 0, 3).'****'.substr($alici, -2) : '***';
    }
}
