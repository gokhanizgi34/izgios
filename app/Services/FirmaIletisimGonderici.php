<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class FirmaIletisimGonderici
{
    public function gonder(object $kayit, string $konu): void
    {
        $entegrasyon = DB::table('muhasebe_entegrasyonlari')
            ->where('firma_id', $kayit->firma_id)
            ->where('saglayici', $kayit->kanal)
            ->where('aktif', true)
            ->first();

        if (! $entegrasyon) {
            throw new RuntimeException("{$kayit->kanal} entegrasyonu etkin değil.");
        }

        $ayar = json_decode($entegrasyon->ayarlar ?: '{}', true) ?: [];
        $gizliAnahtar = $entegrasyon->api_anahtari_sifreli
            ? Crypt::decryptString($entegrasyon->api_anahtari_sifreli)
            : null;

        match ($kayit->kanal) {
            'email' => $this->emailGonder($kayit, $konu, $ayar, $gizliAnahtar),
            'whatsapp' => $this->whatsappGonder($kayit, $ayar, $gizliAnahtar),
            'sms' => $this->smsGonder($kayit, $ayar, $gizliAnahtar),
            default => throw new RuntimeException('Desteklenmeyen iletişim kanalı.'),
        };
    }

    private function emailGonder(object $kayit, string $konu, array $ayar, ?string $sifre): void
    {
        foreach (['smtp_host', 'kullanici_adi', 'gonderen'] as $alan) {
            if (blank($ayar[$alan] ?? null)) {
                throw new RuntimeException("E-posta entegrasyonunda {$alan} eksik.");
            }
        }
        if (blank($sifre)) {
            throw new RuntimeException('E-posta hesabı şifresi eksik.');
        }

        Config::set('mail.mailers.firma_iletisim', [
            'transport' => 'smtp',
            'host' => $ayar['smtp_host'],
            'port' => (int) ($ayar['smtp_port'] ?? 465),
            'scheme' => match ($ayar['smtp_sifreleme'] ?? 'ssl') {
                'ssl' => 'smtps',
                'tls' => 'smtp',
                default => null,
            },
            'username' => $ayar['kullanici_adi'],
            'password' => $sifre,
            'timeout' => 30,
        ]);
        Mail::purge('firma_iletisim');
        Mail::mailer('firma_iletisim')->send('emails.iletisim-bildirimi', [
            'firmaAdi' => $ayar['gonderen_adi'] ?? $ayar['gonderen'],
            'plaka' => null,
            'mesaj' => $kayit->mesaj,
            'konu' => $konu,
            'tarih' => now()->format('d.m.Y H:i'),
        ], function ($mail) use ($kayit, $konu, $ayar) {
            $mail->from($ayar['gonderen'], $ayar['gonderen_adi'] ?? null)
                ->to($kayit->alici)
                ->subject($konu);
        });
    }

    private function whatsappGonder(object $kayit, array $ayar, ?string $anahtar): void
    {
        $this->apiBilgileriniDogrula($ayar, $anahtar, 'WhatsApp');
        $tip = $ayar['saglayici_turu'] ?? 'http_json';
        $istek = Http::acceptJson()->withToken($anahtar)->timeout(30);
        $veri = $tip === 'meta_cloud'
            ? ['messaging_product' => 'whatsapp', 'to' => $this->telefon($kayit->alici), 'type' => 'text', 'text' => ['preview_url' => true, 'body' => $kayit->mesaj]]
            : ['to' => $this->telefon($kayit->alici), 'from' => $ayar['gonderen'] ?? null, 'message' => $kayit->mesaj];
        $yanit = $istek->post($ayar['endpoint'], $veri);
        $yanit->throw();
    }

    private function smsGonder(object $kayit, array $ayar, ?string $anahtar): void
    {
        $this->apiBilgileriniDogrula($ayar, $anahtar, 'SMS');
        $veri = ['to' => $this->telefon($kayit->alici), 'from' => $ayar['gonderen'] ?? null, 'message' => $kayit->mesaj, 'username' => $ayar['kullanici_adi'] ?? null];
        $yanit = Http::acceptJson()->withToken($anahtar)->timeout(30)->post($ayar['endpoint'], $veri);
        $yanit->throw();
    }

    private function apiBilgileriniDogrula(array $ayar, ?string $anahtar, string $kanal): void
    {
        if (blank($ayar['endpoint'] ?? null) || blank($anahtar)) {
            throw new RuntimeException("{$kanal} API uç noktası veya erişim anahtarı eksik.");
        }
    }

    private function telefon(string $telefon): string
    {
        $telefon = preg_replace('/\D+/', '', $telefon);
        return str_starts_with($telefon, '0') ? '90'.substr($telefon, 1) : $telefon;
    }
}
