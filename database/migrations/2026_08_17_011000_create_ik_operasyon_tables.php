<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ik_personel_ozlukleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('ise_baslama_tarihi')->nullable();
            $table->date('isten_ayrilma_tarihi')->nullable();
            $table->string('unvan')->nullable();
            $table->decimal('brut_ucret', 14, 2)->default(0);
            $table->decimal('net_ucret', 14, 2)->default(0);
            $table->decimal('saatlik_mesai_ucreti', 14, 2)->default(0);
            $table->text('notlar')->nullable();
            $table->timestamps();
            $table->unique(['firma_id', 'user_id']);
        });

        Schema::create('ik_bordrolar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('donem');
            $table->decimal('brut_ucret', 14, 2)->default(0);
            $table->decimal('net_ucret', 14, 2)->default(0);
            $table->decimal('mesai_saati', 8, 2)->default(0);
            $table->decimal('mesai_tutari', 14, 2)->default(0);
            $table->decimal('hak_edis', 14, 2)->default(0);
            $table->decimal('avans', 14, 2)->default(0);
            $table->string('durum', 30)->default('taslak');
            $table->text('aciklama')->nullable();
            $table->foreignId('olusturan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['firma_id', 'user_id', 'donem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ik_bordrolar');
        Schema::dropIfExists('ik_personel_ozlukleri');
    }
};
