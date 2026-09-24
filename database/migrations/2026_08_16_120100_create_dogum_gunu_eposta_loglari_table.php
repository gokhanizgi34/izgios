<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dogum_gunu_eposta_loglari', function (Blueprint $table) {
            $table->id();
            $table->string('alici_tipi', 30);
            $table->unsignedBigInteger('alici_id');
            $table->unsignedSmallInteger('yil');
            $table->timestamp('gonderildi_at');
            $table->timestamps();
            $table->unique(['alici_tipi', 'alici_id', 'yil'], 'dogum_gunu_eposta_benzersiz');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dogum_gunu_eposta_loglari');
    }
};
