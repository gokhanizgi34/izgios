<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destek_talepleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('firma_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kategori', 40)->default('genel');
            $table->string('oncelik', 20)->default('normal');
            $table->string('baslik');
            $table->text('mesaj');
            $table->string('durum', 30)->default('acik');
            $table->string('ai_durum', 30)->default('bekliyor');
            $table->text('ai_ozet')->nullable();
            $table->text('ai_cozum')->nullable();
            $table->string('hata_kodu', 70)->nullable();
            $table->timestamps();
        });

        Schema::create('sohbet_odalari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained()->cascadeOnDelete();
            $table->foreignId('olusturan_id')->constrained('users')->cascadeOnDelete();
            $table->string('ad');
            $table->string('tip', 20)->default('birim');
            $table->timestamps();
        });

        Schema::create('sohbet_oda_kullanicilari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sohbet_odasi_id')->constrained('sohbet_odalari')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sohbet_odasi_id', 'user_id']);
        });

        Schema::create('sohbet_mesajlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sohbet_odasi_id')->constrained('sohbet_odalari')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('mesaj');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sohbet_mesajlari');
        Schema::dropIfExists('sohbet_oda_kullanicilari');
        Schema::dropIfExists('sohbet_odalari');
        Schema::dropIfExists('destek_talepleri');
    }
};
