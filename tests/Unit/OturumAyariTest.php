<?php

namespace Tests\Unit;

use Tests\TestCase;

class OturumAyariTest extends TestCase
{
    public function test_oturum_en_az_uc_saat_ve_tarayici_kapanisindan_bagimsizdir(): void
    {
        $this->assertGreaterThanOrEqual(180, config('session.lifetime'));
        $this->assertFalse((bool) config('session.expire_on_close'));
    }
}
