<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ik_puantaj_kayitlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tarih');
            $table->time('giris_saati')->nullable();
            $table->time('cikis_saati')->nullable();
            $table->decimal('mesai_saati', 6, 2)->default(0);
            $table->string('durum', 30)->default('calisti');
            $table->string('aciklama', 500)->nullable();
            $table->timestamps();
            $table->unique(['firma_id', 'user_id', 'tarih']);
        });

        Schema::create('ik_ozel_gunler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tur', 40);
            $table->string('baslik', 160);
            $table->date('tarih');
            $table->boolean('hatirlatma_aktif')->default(true);
            $table->unsignedSmallInteger('hatirlatma_gun_once')->default(1);
            $table->timestamps();
            $table->index(['firma_id', 'tarih']);
        });

        Schema::create('ik_personel_dosyalari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kategori', 60);
            $table->string('baslik', 160);
            $table->string('dosya_yolu', 500);
            $table->date('gecerlilik_tarihi')->nullable();
            $table->foreignId('yukleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['firma_id', 'user_id', 'kategori']);
        });

        Schema::create('ik_egitim_planlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('egitim_adi', 180);
            $table->string('egitim_turu', 80)->nullable();
            $table->date('planlanan_tarih')->nullable();
            $table->date('tamamlanma_tarihi')->nullable();
            $table->string('durum', 30)->default('planlandi');
            $table->text('notlar')->nullable();
            $table->timestamps();
            $table->index(['firma_id', 'durum']);
        });

        Schema::create('ik_performans_degerlendirmeleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('donem_baslangic');
            $table->date('donem_bitis');
            $table->decimal('puan', 5, 2)->nullable();
            $table->text('guclu_yonler')->nullable();
            $table->text('gelisim_alanlari')->nullable();
            $table->text('hedefler')->nullable();
            $table->foreignId('degerlendiren_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['firma_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ik_performans_degerlendirmeleri');
        Schema::dropIfExists('ik_egitim_planlari');
        Schema::dropIfExists('ik_personel_dosyalari');
        Schema::dropIfExists('ik_ozel_gunler');
        Schema::dropIfExists('ik_puantaj_kayitlari');
    }
};
