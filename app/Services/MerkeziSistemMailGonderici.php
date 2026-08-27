<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MerkeziSistemMailGonderici
{
    public function ayarlar(): array
    {
        if (! Schema::hasTable('yonetim_ayarlari')) return [];
        return DB::table('yonetim_ayarlari')
            ->where('grup', 'sistem_email')
            ->pluck('deger', 'anahtar')
            ->all();
    }

    public function hazirMi(): bool
    {
        $ayar = $this->ayarlar();
        return ($ayar['aktif'] ?? '0') === '1'
            && collect(['smtp_host', 'smtp_port', 'kullanici_adi', 'sifre', 'gonderen', 'bildirim_alicisi'])
                ->every(fn (string $alan) => filled($ayar[$alan] ?? null));
    }

    public function bildirimGonder(string $konu, string $mesaj): void
    {
        $alici = $this->ayarlar()['bildirim_alicisi'] ?? null;
        if (blank($alici)) {
            throw new RuntimeException('Sistem bildirim e-posta adresi tanımlı değil.');
        }
        $this->metinGonder($alici, $konu, $mesaj);
    }

    public function metinGonder(string|array $alici, string $konu, string $mesaj): void
    {
        $this->mailer()->send('emails.iletisim-bildirimi', [
            'firmaAdi' => $this->ayarlar()['gonderen_adi'] ?? 'İZGİOS Sistem Yönetimi',
            'plaka' => null,
            'mesaj' => $mesaj,
            'konu' => $konu,
            'tarih' => now()->format('d.m.Y H:i'),
            'aksiyonUrl' => $this->mesajdakiUrl($mesaj),
        ], fn ($mail) => $mail->from($this->ayarlar()['gonderen'], $this->ayarlar()['gonderen_adi'] ?? null)->to($alici)->subject($konu));
    }

    public function gorunumGonder(string|array $alici, string $konu, string $gorunum, array $veri): void
    {
        $ayar = $this->ayarlar();
        $this->mailer()->send($gorunum, $veri, fn ($mail) => $mail->from($ayar['gonderen'], $ayar['gonderen_adi'] ?? null)->to($alici)->subject($konu));
    }

    private function mailer()
    {
        $ayar = $this->ayarlar();
        if (! $this->hazirMi()) {
            throw new RuntimeException('Sistem e-posta entegrasyonu etkin veya eksiksiz değil.');
        }

        Config::set('mail.mailers.sistem_iletisim', [
            'transport' => 'smtp',
            'host' => $ayar['smtp_host'],
            'port' => (int) $ayar['smtp_port'],
            'scheme' => match ($ayar['smtp_sifreleme'] ?? 'ssl') {
                'ssl' => 'smtps',
                'tls' => 'smtp',
                default => null,
            },
            'username' => $ayar['kullanici_adi'],
            'password' => Crypt::decryptString($ayar['sifre']),
            'timeout' => 30,
        ]);
        Mail::purge('sistem_iletisim');
        return Mail::mailer('sistem_iletisim');
    }

    private function mesajdakiUrl(string $mesaj): ?string
    {
        if (! preg_match('/https?:\/\/[^\s<]+/iu', $mesaj, $eslesme)) return null;
        $url = rtrim($eslesme[0], '.,;:!?)]}');
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }
}
