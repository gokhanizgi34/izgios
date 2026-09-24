<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('silme_denetim_kayitlari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firma_id')->nullable()->index();
            $table->unsignedBigInteger('kullanici_id')->nullable()->index();
            $table->string('modul', 80)->index();
            $table->string('kayit_turu', 190);
            $table->string('kayit_id', 80)->nullable();
            $table->string('kayit_ozeti', 500)->nullable();
            $table->json('silinen_veri')->nullable();
            $table->string('islemi_yapan', 190)->nullable();
            $table->string('rol', 80)->nullable();
            $table->string('ip_adresi', 64)->nullable();
            $table->text('ekran_adresi')->nullable();
            $table->boolean('firma_sahibine_mail')->default(false);
            $table->text('mail_hatasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('silme_denetim_kayitlari');
    }
};
