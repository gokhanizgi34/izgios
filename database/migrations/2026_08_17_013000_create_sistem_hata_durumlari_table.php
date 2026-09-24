<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sistem_hata_durumlari', function (Blueprint $table) {
            $table->id();
            $table->string('hata_kodu', 80)->unique();
            $table->string('durum', 20)->default('acik');
            $table->text('kontrol_notu')->nullable();
            $table->foreignId('isleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cozuldu_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistem_hata_durumlari');
    }
};
