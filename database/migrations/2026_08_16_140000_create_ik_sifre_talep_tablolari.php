<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ik_iletisim_ayarlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sifre_talep_email');
            $table->foreignId('guncelleyen_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique('firma_id');
        });

        Schema::create('sifre_yenileme_talepleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('firma_id')->nullable()->constrained()->nullOnDelete();
            $table->string('istek_email');
            $table->string('ik_email')->nullable();
            $table->string('durum', 30)->default('bekliyor');
            $table->foreignId('isleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('onaylandi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sifre_yenileme_talepleri');
        Schema::dropIfExists('ik_iletisim_ayarlari');
    }
};
