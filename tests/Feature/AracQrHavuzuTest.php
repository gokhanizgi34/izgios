<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AracQrHavuzuTest extends TestCase
{
    public function test_mevcut_arac_qr_kodlari_havuza_atanmis_olarak_aktarilir(): void
    {
        Schema::create('araclar', function (Blueprint $table) {
            $table->id();
            $table->uuid('qr_token')->nullable();
            $table->timestamp('qr_created_at')->nullable();
            $table->timestamps();
        });

        $token = (string) Str::uuid();
        $aracId = DB::table('araclar')->insertGetId([
            'qr_token' => $token,
            'qr_created_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_09_24_120000_create_arac_qr_havuzu_table.php');
        $migration->up();

        $this->assertDatabaseHas('arac_qr_havuzu', [
            'token' => $token,
            'durum' => 'atanmis',
            'arac_id' => $aracId,
        ]);

        $migration->down();
        Schema::dropIfExists('araclar');
    }
}
