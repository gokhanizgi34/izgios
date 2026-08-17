<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gelistirme_mesajlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelistirme_talebi_id')->constrained('gelistirme_talepleri')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('gonderen_tipi', 20)->default('sistem_yoneticisi');
            $table->text('mesaj');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('gelistirme_mesajlari'); }
};
