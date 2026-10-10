<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cari_hesaplar', function (Blueprint $table) {
            $table->id(); $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('tip', 20); $table->string('unvan'); $table->string('vergi_no', 20)->nullable();
            $table->string('telefon', 30)->nullable(); $table->string('email')->nullable();
            $table->decimal('bakiye', 14, 2)->default(0); $table->boolean('aktif')->default(true); $table->timestamps();
        });
        Schema::create('muhasebe_fisleri', function (Blueprint $table) {
            $table->id(); $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('cari_hesap_id')->nullable()->constrained('cari_hesaplar')->nullOnDelete();
            $table->string('fis_no')->unique(); $table->string('tip', 30); $table->date('fis_tarihi');
            $table->string('aciklama')->nullable(); $table->decimal('tutar', 14, 2); $table->string('yon', 10);
            $table->string('kaynak', 30)->default('manuel'); $table->unsignedBigInteger('kaynak_id')->nullable();
            $table->string('durum', 20)->default('taslak'); $table->foreignId('olusturan_id')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
        });
        Schema::create('muhasebe_entegrasyonlari', function (Blueprint $table) {
            $table->id(); $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('saglayici'); $table->boolean('aktif')->default(false); $table->string('durum', 30)->default('yapilandirilmamis');
            $table->text('ayarlar')->nullable(); $table->timestamp('son_senkron_at')->nullable(); $table->timestamps();
            $table->unique(['firma_id', 'saglayici']);
        });
    }
    public function down(): void { Schema::dropIfExists('muhasebe_entegrasyonlari'); Schema::dropIfExists('muhasebe_fisleri'); Schema::dropIfExists('cari_hesaplar'); }
};
