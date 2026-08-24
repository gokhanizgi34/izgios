<?php

namespace App\Http\Controllers;

use App\Services\FirmaIletisimGonderici;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CiktiController extends Controller
{
    public function yazdir(Request $request, string $tur, int $id)
    {
        [$baslik, $belge, $satirlar, $firma, $resmiFatura] = $this->belge($request, $tur, $id);
        return view('ciktilar.belge', compact('tur', 'baslik', 'belge', 'satirlar', 'firma', 'resmiFatura'));
    }

    public function excel(Request $request, string $tur, int $id)
    {
        [$baslik, $belge, $satirlar, $firma] = $this->belge($request, $tur, $id);
        $dosya = str($tur.'-'.$id.'-'.now()->format('YmdHis').'.csv')->ascii();

        return response()->streamDownload(function () use ($baslik, $belge, $satirlar, $firma) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $para = static fn ($tutar): string => number_format((float) $tutar, 2, ',', '.');
            $adet = static fn ($miktar): string => number_format((float) $miktar, 3, ',', '.');
            $yaz = static fn (array $satir) => fputcsv($out, $satir, ';');
            $yaz([$baslik, $firma->gosterim_adi ?? $firma->unvan ?? '']);
            $yaz(['Belge No', $belge->belge_no]);
            $belgeTarihi = $belge->tarih ?? $belge->fis_tarihi ?? $belge->servis_tarihi ?? null;
            $yaz(['Tarih', $belgeTarihi ? \Carbon\Carbon::parse($belgeTarihi)->format('d.m.Y') : '—']);
            $yaz([]);
            $yaz(['Ürün / Hizmet', 'Adet', 'Birim', 'Birim Fiyat', 'KDV %', 'KDV Hariç', 'KDV', 'KDV Dahil']);
            foreach ($satirlar as $satir) {
                $yaz([$satir->urun_adi, $adet($satir->adet), $satir->birim, $para($satir->birim_fiyat), $para($satir->kdv_orani), $para($satir->kdv_haric_tutar), $para($satir->kdv_tutari), $para($satir->kdv_dahil_tutar)]);
            }
            $yaz([]);
            $yaz(['GENEL TOPLAM', '', '', '', '', '', '', $para($belge->tutar)]);
            fclose($out);
        }, $dosya, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function gonder(Request $request, string $tur, int $id, FirmaIletisimGonderici $gonderici)
    {
        [$baslik, $belge, $satirlar, $firma, $resmiFatura] = $this->belge($request, $tur, $id);
        $veri = $request->validate([
            'kanal' => ['required', 'in:email,whatsapp,sms'],
            'alici' => ['required', 'string', 'max:255'],
        ]);
        if ($veri['kanal'] === 'email') {
            $request->validate(['alici' => ['email']]);
        }

        $mesaj = $this->paylasimMesaji($baslik, $belge, $satirlar, $firma);
        $entegrasyonAktif = DB::table('muhasebe_entegrasyonlari')->where('firma_id', $belge->firma_id)->where('saglayici', $veri['kanal'])->where('aktif', true)->exists();

        if ($entegrasyonAktif) {
            try {
                $gonderici->gonder((object) ['firma_id' => $belge->firma_id, 'kanal' => $veri['kanal'], 'alici' => $veri['alici'], 'mesaj' => $mesaj], $baslik.' · '.$belge->belge_no);
                $this->gonderimLogla($belge, $veri['kanal'], $veri['alici'], $mesaj, 'gonderildi');
                return back()->with('success', strtoupper($veri['kanal']).' gönderimi tamamlandı.');
            } catch (Throwable $exception) {
                report($exception);
                return back()->withErrors(['alici' => 'Entegrasyon üzerinden gönderilemedi. API/SMTP ayarlarını kontrol edin.']);
            }
        }

        if ($veri['kanal'] === 'email') {
            try {
                $paylasimMesaji = $mesaj;
                Mail::send('emails.muhasebe-belgesi', compact('baslik', 'belge', 'satirlar', 'firma', 'resmiFatura', 'paylasimMesaji'), function ($mail) use ($veri, $baslik, $belge) {
                    $mail->to($veri['alici'])->subject("{$baslik} · {$belge->belge_no}");
                });
                $this->gonderimLogla($belge, 'email', $veri['alici'], $mesaj, 'gonderildi');

                return back()->with('success', 'Fiş/belge detayları e-posta ile gönderildi.');
            } catch (Throwable $exception) {
                report($exception);
                $this->gonderimLogla($belge, 'email', $veri['alici'], $mesaj, 'hata');

                return back()->withErrors(['alici' => 'E-posta gönderilemedi. SMTP ayarlarını ve alıcı adresini kontrol edin.']);
            }
        }

        $telefon = $this->telefonuNormalizeEt($veri['alici']);
        if (! $telefon) {
            return back()->withErrors(['alici' => 'WhatsApp veya SMS için geçerli bir telefon numarası girin.']);
        }

        $this->gonderimLogla($belge, $veri['kanal'], $telefon, $mesaj, 'uygulamada_hazir');

        if ($veri['kanal'] === 'whatsapp') {
            return redirect()->away('https://wa.me/'.$telefon.'?text='.rawurlencode($mesaj));
        }

        return redirect()->away('sms:+'.$telefon.'?body='.rawurlencode($mesaj));
    }

    private function belge(Request $request, string $tur, int $id): array
    {
        abort_unless(auth()->check(), 403);
        abort_unless(in_array($tur, ['teklif', 'fatura', 'fis', 'servis'], true), 404);
        $yetkili = auth()->user()->tamSistemYetkisiVarMi() || auth()->user()->isAdmin() || auth()->user()->isMuhasebe();
        abort_unless($yetkili || $tur === 'servis', 403);

        $tablo = ['teklif' => 'teklifler', 'fatura' => 'faturalar', 'fis' => 'muhasebe_fisleri', 'servis' => 'servisler'][$tur];
        $belge = DB::table($tablo)->where('id', $id)->first();
        abort_unless($belge, 404);
        if (! auth()->user()->tamSistemYetkisiVarMi()) {
            abort_unless((int) $belge->firma_id === (int) auth()->user()->firmaPersoneli?->firma_id, 403);
        }
        $firma = DB::table('firmas')->where('id', $belge->firma_id)->first();
        $belge->belge_no = $tur === 'teklif' ? $belge->teklif_no : ($tur === 'fatura' ? $belge->fatura_no : ($tur === 'fis' ? $belge->fis_no : $belge->servis_no));

        if (! empty($belge->cari_hesap_id)) {
            $cari = DB::table('cari_hesaplar')
                ->where('id', $belge->cari_hesap_id)
                ->select('unvan', 'email', 'telefon')
                ->first();
            $belge->musteri_unvan ??= $cari?->unvan;
            $belge->alici_email = $cari?->email;
            $belge->alici_telefon = $cari?->telefon;
        }

        if ($tur === 'servis' && ! empty($belge->musteri_id)) {
            $musteri = DB::table('musteris')->where('id', $belge->musteri_id)->select('ad_soyad', 'email', 'telefon')->first();
            $belge->musteri_unvan ??= $musteri?->ad_soyad;
            $belge->alici_email = $musteri?->email;
            $belge->alici_telefon = $musteri?->telefon;
            $arac = DB::table('araclar')->where('id', $belge->arac_id)->select('plaka', 'qr_token')->first();
            $belge->plaka = $arac?->plaka;
            $belge->qr_token = $arac?->qr_token;
        }

        if (in_array($tur, ['teklif', 'fatura'], true)) {
            $satirlar = DB::table('ticari_belge_satirlari')->where('belge_turu', $tur)->where('belge_id', $id)->get()->map(fn ($x) => (object) ['urun_adi' => $x->urun_hizmet_adi, 'adet' => $x->miktar, 'birim' => $x->birim, 'birim_fiyat' => $x->birim_fiyat, 'kdv_orani' => $x->kdv_orani, 'kdv_haric_tutar' => $x->kdv_haric_tutar, 'kdv_tutari' => $x->kdv_tutari, 'kdv_dahil_tutar' => $x->kdv_dahil_tutar]);
        } elseif ($tur === 'fis') {
            $satirlar = DB::table('muhasebe_fis_satirlari')->where('muhasebe_fis_id', $id)->get()->map(fn ($x) => (object) ['urun_adi' => $x->urun_adi, 'adet' => $x->adet, 'birim' => $x->birim, 'birim_fiyat' => $x->birim_fiyat, 'kdv_orani' => $x->kdv_orani, 'kdv_haric_tutar' => $x->kdv_haric_tutar, 'kdv_tutari' => $x->kdv_tutari, 'kdv_dahil_tutar' => $x->kdv_dahil_tutar]);
        } else {
            $islemler = DB::table('servis_islemleri')->where('servis_id', $id)->get();
            $parcalar = DB::table('servis_parcalar')->where('servis_id', $id)->get();
            $satirlar = $islemler->map(fn ($x) => (object) ['urun_adi' => $x->islem_adi, 'adet' => 1, 'birim' => 'Hizmet', 'birim_fiyat' => $x->tutar, 'kdv_orani' => 20, 'kdv_haric_tutar' => $x->tutar, 'kdv_tutari' => round($x->tutar * .20, 2), 'kdv_dahil_tutar' => round($x->tutar * 1.20, 2)])->concat($parcalar->map(fn ($x) => (object) ['urun_adi' => $x->parca_adi, 'adet' => $x->adet, 'birim' => 'Adet', 'birim_fiyat' => $x->birim_fiyat, 'kdv_orani' => 20, 'kdv_haric_tutar' => $x->toplam_fiyat, 'kdv_tutari' => round($x->toplam_fiyat * .20, 2), 'kdv_dahil_tutar' => round($x->toplam_fiyat * 1.20, 2)]));
            $belge->tutar = $satirlar->sum('kdv_dahil_tutar');
        }
        $baslik = ['teklif' => 'TEKLİF', 'fatura' => 'FATURA', 'fis' => 'GİDER FİŞİ', 'servis' => 'SERVİS İŞ EMRİ ÖZETİ'][$tur];
        $resmiFatura = $tur === 'fatura' && DB::table('muhasebe_entegrasyonlari')->where('firma_id', $belge->firma_id)->whereIn('saglayici', ['logo', 'parasut', 'efatura'])->where('aktif', true)->exists();
        return [$baslik, $belge, $satirlar, $firma, $resmiFatura];
    }

    private function paylasimMesaji(string $baslik, object $belge, $satirlar, object $firma): string
    {
        $satirOzeti = $satirlar->take(8)->map(fn ($satir) => sprintf(
            '- %s: %s %s · ₺%s',
            $satir->urun_adi,
            rtrim(rtrim(number_format((float) $satir->adet, 3, ',', '.'), '0'), ','),
            $satir->birim,
            number_format((float) $satir->kdv_dahil_tutar, 2, ',', '.')
        ))->implode("\n");
        $ek = $satirlar->count() > 8 ? "\n+ diğer satırlar" : '';

        $mesaj = trim(sprintf(
            "%s\n%s · %s\n\n%s%s\n\nKDV dahil genel toplam: ₺%s",
            $firma->gosterim_adi ?? $firma->unvan,
            $baslik,
            $belge->belge_no,
            $satirOzeti,
            $ek,
            number_format((float) $belge->tutar, 2, ',', '.')
        ));
        if (! empty($belge->qr_token) && ! empty($belge->plaka)) {
            $sifre = mb_substr(preg_replace('/[^A-Z0-9]/u', '', mb_strtoupper($belge->plaka)), -4);
            $mesaj .= "\n\nDetayları görmek için: ".route('qr.servis.show', ['token' => $belge->qr_token, 'ekran' => 'servis'])."\nŞifre: {$sifre}";
        }
        return $mesaj;
    }

    private function telefonuNormalizeEt(string $telefon): ?string
    {
        $numara = preg_replace('/\D+/', '', $telefon);
        if (str_starts_with($numara, '0') && strlen($numara) === 11) {
            $numara = '90'.substr($numara, 1);
        }
        if (strlen($numara) === 10 && str_starts_with($numara, '5')) {
            $numara = '90'.$numara;
        }

        return preg_match('/^90[5]\d{9}$/', $numara) ? $numara : null;
    }

    private function gonderimLogla(object $belge, string $kanal, string $alici, string $mesaj, string $durum): void
    {
        DB::table('iletisim_gonderim_loglari')->insert([
            'firma_id' => $belge->firma_id,
            'mesaj_grubu' => 'muhasebe_belgesi',
            'kanal' => $kanal,
            'durum' => $durum,
            'alici' => $alici,
            'alici_maskeli' => $kanal === 'email'
                ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $alici)
                : substr($alici, 0, 3).'****'.substr($alici, -2),
            'mesaj' => $mesaj,
            'planlanan_at' => now(),
            'kaynak_turu' => 'muhasebe_belgesi',
            'kaynak_id' => $belge->id,
            'gonderildi_at' => $durum === 'gonderildi' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
