<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ik_acik_pozisyonlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('birim', 120);
            $table->string('pozisyon', 160);
            $table->unsignedSmallInteger('ihtiyac_adedi')->default(1);
            $table->date('acilis_tarihi');
            $table->date('son_basvuru_tarihi')->nullable();
            $table->enum('durum', ['acik', 'beklemede', 'kapali'])->default('acik');
            $table->text('gorev_tanimi')->nullable();
            $table->timestamps();
            $table->index(['firma_id', 'durum']);
        });

        Schema::create('ik_is_basvurulari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('pozisyon_id')->nullable()->constrained('ik_acik_pozisyonlar')->nullOnDelete();
            $table->string('aday_adi', 160);
            $table->string('email', 160)->nullable();
            $table->string('telefon', 40)->nullable();
            $table->string('kaynak', 100)->nullable();
            $table->enum('asama', ['basvuru', 'on_gorusme', 'teknik_gorusme', 'referans', 'teklif', 'ise_alindi', 'olumsuz'])->default('basvuru');
            $table->text('notlar')->nullable();
            $table->timestamps();
            $table->index(['firma_id', 'asama']);
        });

        Schema::create('ik_izin_talepleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('izin_turu', 80);
            $table->date('baslangic_tarihi');
            $table->date('bitis_tarihi');
            $table->decimal('gun_sayisi', 5, 1)->default(1);
            $table->enum('durum', ['beklemede', 'onaylandi', 'reddedildi', 'iptal'])->default('beklemede');
            $table->text('aciklama')->nullable();
            $table->foreignId('onaylayan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('onay_tarihi')->nullable();
            $table->timestamps();
            $table->index(['firma_id', 'user_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ik_izin_talepleri');
        Schema::dropIfExists('ik_is_basvurulari');
        Schema::dropIfExists('ik_acik_pozisyonlar');
    }
};
