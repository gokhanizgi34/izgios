<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kdv_urun_gruplari', function (Blueprint $table) {
            $table->id(); $table->string('grup_adi'); $table->decimal('kdv_orani', 5, 2)->default(20); $table->boolean('aktif')->default(true); $table->timestamps();
            $table->unique('grup_adi');
        });
        Schema::create('muhasebe_fis_satirlari', function (Blueprint $table) {
            $table->id(); $table->foreignId('muhasebe_fis_id')->constrained('muhasebe_fisleri')->cascadeOnDelete();
            $table->string('urun_adi'); $table->decimal('birim_fiyat', 14, 2); $table->decimal('kdv_orani', 5, 2);
            $table->decimal('kdv_haric_tutar', 14, 2); $table->decimal('kdv_tutari', 14, 2); $table->decimal('kdv_dahil_tutar', 14, 2); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('muhasebe_fis_satirlari'); Schema::dropIfExists('kdv_urun_gruplari'); }
};
