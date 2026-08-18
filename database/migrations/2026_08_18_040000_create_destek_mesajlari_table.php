<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destek_mesajlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destek_talebi_id')->constrained('destek_talepleri')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gonderen_tipi', 30);
            $table->text('mesaj');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destek_mesajlari');
    }
};
