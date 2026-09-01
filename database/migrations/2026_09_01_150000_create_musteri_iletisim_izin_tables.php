<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('musteri_iletisim_izinleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firma_id');
            $table->unsignedBigInteger('musteri_id');
            $table->boolean('servis_iletisim_izni')->default(false);
            $table->boolean('ticari_iletisim_izni')->default(false);
            $table->timestamp('tercih_at');
            $table->timestamps();
            $table->unique(['firma_id', 'musteri_id'], 'musteri_firma_iletisim_izni_unique');
        });

        Schema::create('musteri_iletisim_izin_hareketleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firma_id');
            $table->unsignedBigInteger('musteri_id');
            $table->unsignedBigInteger('servis_id')->nullable();
            $table->string('firma_unvani', 255);
            $table->string('musteri_adi', 255);
            $table->string('email', 255)->nullable();
            $table->string('telefon', 40)->nullable();
            $table->boolean('servis_iletisim_izni');
            $table->boolean('ticari_iletisim_izni');
            $table->string('servis_metni_surumu', 40);
            $table->char('servis_metni_hash', 64);
            $table->string('ticari_metni_surumu', 40);
            $table->char('ticari_metni_hash', 64);
            $table->string('ip_adresi', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->char('qr_token_hash', 64);
            $table->timestamp('onay_at');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['firma_id', 'musteri_id', 'onay_at'], 'iletisim_izin_kanit_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musteri_iletisim_izin_hareketleri');
        Schema::dropIfExists('musteri_iletisim_izinleri');
    }
};
