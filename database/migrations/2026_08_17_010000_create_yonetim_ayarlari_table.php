<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('yonetim_ayarlari', function (Blueprint $table) {
            $table->id();
            $table->string('grup', 50);
            $table->string('anahtar', 100);
            $table->text('deger')->nullable();
            $table->foreignId('guncelleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['grup', 'anahtar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yonetim_ayarlari');
    }
};
