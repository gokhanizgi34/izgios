<?php

namespace Tests\Unit;

use App\Http\Controllers\CiktiController;
use ReflectionMethod;
use Tests\TestCase;

class CiktiWhatsappMesajiTest extends TestCase
{
    public function test_servis_whatsapp_mesaji_miktar_birim_fiyat_ve_kalem_detayi_icermez(): void
    {
        $metot = new ReflectionMethod(CiktiController::class, 'paylasimMesaji');
        $mesaj = $metot->invoke(
            new CiktiController,
            'SERVİS İŞ EMRİ ÖZETİ',
            (object) [
                'belge_no' => 'SRV-TEST',
                'plaka' => '34TEST99',
                'qr_token' => 'test-token',
                'tutar' => 12345.67,
            ],
            collect([(object) [
                'urun_adi' => 'TEST PARÇASI',
                'adet' => 7,
                'birim' => 'Adet',
                'kdv_dahil_tutar' => 12345.67,
            ]]),
            (object) ['unvan' => 'Test Servisi'],
            'whatsapp'
        );

        $this->assertStringContainsString('SRV-TEST', $mesaj);
        $this->assertStringContainsString('Servis detayları:', $mesaj);
        $this->assertStringNotContainsString('TEST PARÇASI', $mesaj);
        $this->assertStringNotContainsString('Adet', $mesaj);
        $this->assertStringNotContainsString('12.345,67', $mesaj);
    }
}
