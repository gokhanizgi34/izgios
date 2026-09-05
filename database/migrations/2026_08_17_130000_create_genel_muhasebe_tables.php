<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muhasebe_hesap_planlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('kod', 25);
            $table->string('ad', 180);
            $table->enum('sinif', ['varlik', 'borc', 'sermaye', 'gelir', 'gider']);
            $table->enum('normal_bakiye', ['borc', 'alacak']);
            $table->foreignId('ust_hesap_id')->nullable()->constrained('muhasebe_hesap_planlari')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->unique(['firma_id', 'kod']);
        });

        Schema::create('muhasebe_donemleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('ad', 100);
            $table->date('baslangic_tarihi');
            $table->date('bitis_tarihi');
            $table->enum('durum', ['acik', 'kilitli', 'kapali'])->default('acik');
            $table->timestamps();
            $table->unique(['firma_id', 'baslangic_tarihi', 'bitis_tarihi']);
        });

        Schema::create('muhasebe_masraf_merkezleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('kod', 30);
            $table->string('ad', 150);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->unique(['firma_id', 'kod']);
        });

        Schema::create('muhasebe_projeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('kod', 30);
            $table->string('ad', 150);
            $table->date('baslangic_tarihi')->nullable();
            $table->date('bitis_tarihi')->nullable();
            $table->enum('durum', ['acik', 'tamamlandi', 'iptal'])->default('acik');
            $table->timestamps();
            $table->unique(['firma_id', 'kod']);
        });

        Schema::create('muhasebe_yevmiye_fisleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('muhasebe_donem_id')->nullable()->constrained('muhasebe_donemleri')->nullOnDelete();
            $table->string('fis_no', 60);
            $table->date('fis_tarihi');
            $table->enum('tip', ['mahsup', 'tahsilat', 'tediye', 'acilis', 'kapanis', 'entegrasyon'])->default('mahsup');
            $table->string('aciklama', 1000)->nullable();
            $table->string('kaynak', 60)->default('manuel');
            $table->unsignedBigInteger('kaynak_id')->nullable();
            $table->enum('durum', ['taslak', 'onaylandi', 'iptal'])->default('taslak');
            $table->foreignId('olusturan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('onaylayan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('onay_tarihi')->nullable();
            $table->timestamps();
            $table->unique(['firma_id', 'fis_no']);
            $table->index(['firma_id', 'fis_tarihi', 'durum']);
        });

        Schema::create('muhasebe_yevmiye_satirlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('muhasebe_yevmiye_fis_id')->constrained('muhasebe_yevmiye_fisleri')->cascadeOnDelete();
            $table->foreignId('muhasebe_hesap_plan_id')->constrained('muhasebe_hesap_planlari')->restrictOnDelete();
            $table->foreignId('cari_hesap_id')->nullable()->constrained('cari_hesaplar')->nullOnDelete();
            $table->foreignId('masraf_merkezi_id')->nullable()->constrained('muhasebe_masraf_merkezleri')->nullOnDelete();
            $table->foreignId('proje_id')->nullable()->constrained('muhasebe_projeler')->nullOnDelete();
            $table->string('aciklama', 500)->nullable();
            $table->decimal('borc', 14, 2)->default(0);
            $table->decimal('alacak', 14, 2)->default(0);
            $table->unsignedInteger('sira')->default(1);
            $table->timestamps();
            $table->index('muhasebe_hesap_plan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muhasebe_yevmiye_satirlari');
        Schema::dropIfExists('muhasebe_yevmiye_fisleri');
        Schema::dropIfExists('muhasebe_projeler');
        Schema::dropIfExists('muhasebe_masraf_merkezleri');
        Schema::dropIfExists('muhasebe_donemleri');
        Schema::dropIfExists('muhasebe_hesap_planlari');
    }
};
