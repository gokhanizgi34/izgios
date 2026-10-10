<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->string('kaynak', 40)->default('manuel')->after('durum');
            $table->foreignId('servis_id')->nullable()->after('arac_id')->constrained('servisler')->nullOnDelete();
            $table->index(['firma_id', 'arac_id', 'kaynak', 'durum'], 'randevu_hatirlatma_takip_idx');
        });

        Schema::table('iletisim_gonderim_loglari', function (Blueprint $table) {
            $table->foreignId('arac_id')->nullable()->after('musteri_id')->constrained('araclar')->nullOnDelete();
            $table->string('alici', 190)->nullable()->after('alici_maskeli');
            $table->timestamp('planlanan_at')->nullable()->after('mesaj');
            $table->string('kaynak_turu', 40)->nullable()->after('mesaj_grubu');
            $table->unsignedBigInteger('kaynak_id')->nullable()->after('kaynak_turu');
            $table->index(['durum', 'planlanan_at'], 'iletisim_planlama_idx');
            $table->index(['kaynak_turu', 'kaynak_id'], 'iletisim_kaynak_idx');
        });
    }

    public function down(): void
    {
        Schema::table('iletisim_gonderim_loglari', function (Blueprint $table) {
            $table->dropIndex('iletisim_planlama_idx');
            $table->dropIndex('iletisim_kaynak_idx');
            $table->dropConstrainedForeignId('arac_id');
            $table->dropColumn(['alici', 'planlanan_at', 'kaynak_turu', 'kaynak_id']);
        });

        Schema::table('randevular', function (Blueprint $table) {
            $table->dropIndex('randevu_hatirlatma_takip_idx');
            $table->dropConstrainedForeignId('servis_id');
            $table->dropColumn('kaynak');
        });
    }
};
