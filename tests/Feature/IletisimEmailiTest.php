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
    }
}
