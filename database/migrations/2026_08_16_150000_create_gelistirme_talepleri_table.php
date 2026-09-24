<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gelistirme_talepleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olusturan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('onaylayan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('baslik');
            $table->text('talep');
            $table->text('cozum_plani')->nullable();
            $table->string('durum', 30)->default('taslak');
            $table->timestamp('onaylandi_at')->nullable();
            $table->timestamp('uygulandi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gelistirme_talepleri');
    }
};
