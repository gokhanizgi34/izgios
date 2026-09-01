<?php

namespace Tests\Feature;

use Tests\TestCase;

class IletisimEmailiTest extends TestCase
{
    public function test_google_yorum_baglantisini_tiklanabilir_dugme_olarak_gosterir(): void
    {
        $url = 'https://g.page/r/AbCdEf123/review';
        $html = view('emails.iletisim-bildirimi', [
            'firmaAdi' => 'Test Servisi',
            'plaka' => '34 TEST 34',
            'mesaj' => 'Yorumunuz için: '.$url,
            'konu' => 'Araç teslim edildi',
            'tarih' => '24.08.2026 12:00',
            'aksiyonUrl' => $url,
        ])->render();

        $this->assertStringContainsString('href="'.$url.'"', $html);
        $this->assertStringContainsString('Google Yorum Bağlantısını Aç', $html);
        $this->assertSame(1, substr_count($html, 'href="'.$url.'"'));
    }

    public function test_servis_takip_baglantisini_tek_dugme_olarak_gosterir(): void
    {
        $url = 'https://izgios.com/qr-servis/test-token?ekran=servis';
        $html = view('emails.iletisim-bildirimi', [
            'firmaAdi' => 'Test Servisi',
            'plaka' => null,
            'mesaj' => 'Aracınız servise kabul edildi. Detaylar: '.$url.' Şifre: CY24',
            'konu' => 'Servis kabul',
            'tarih' => '01.09.2026 15:00',
            'aksiyonUrl' => $url,
        ])->render();

        $this->assertSame(1, substr_count($html, 'href="'.$url.'"'));
        $this->assertSame(1, substr_count($html, 'Bağlantıyı Aç'));
        $this->assertStringContainsString('Şifre: CY24', $html);
    }
}
