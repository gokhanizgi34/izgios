<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servis_durum_notlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servis_id')->constrained('servisler')->cascadeOnDelete();
            $table->foreignId('kullanici_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('durum', 40);
            $table->text('not');
            $table->timestamps();
            $table->index(['servis_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servis_durum_notlari');
    }
};
