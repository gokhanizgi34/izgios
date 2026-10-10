<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('firma_periyodik_bakim_ayarlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->unique()->constrained('firmas')->cascadeOnDelete();
            $table->json('kalemler');
            $table->foreignId('guncelleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firma_periyodik_bakim_ayarlari');
    }
};
