<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cari_hesaplar', function (Blueprint $table) {
            $table->foreignId('musteri_id')->nullable()->after('firma_id')->constrained('musteris')->nullOnDelete();
            $table->string('plaka', 20)->nullable()->after('unvan');
            $table->string('kaynak', 30)->default('manuel')->after('aktif');
            $table->unique(['firma_id', 'musteri_id'], 'cari_musteri_tekil');
            $table->index(['firma_id', 'plaka'], 'cari_plaka_arama_idx');
        });

        Schema::table('muhasebe_fis_satirlari', function (Blueprint $table) {
            $table->decimal('adet', 12, 3)->default(1)->after('urun_adi');
            $table->string('birim', 30)->default('Adet')->after('adet');
        });

        Schema::table('teklifler', function (Blueprint $table) {
            $table->foreignId('servis_id')->nullable()->after('cari_hesap_id')->constrained('servisler')->nullOnDelete();
        });

        Schema::table('faturalar', function (Blueprint $table) {
            $table->foreignId('servis_id')->nullable()->after('cari_hesap_id')->constrained('servisler')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('faturalar', fn (Blueprint $table) => $table->dropConstrainedForeignId('servis_id'));
        Schema::table('teklifler', fn (Blueprint $table) => $table->dropConstrainedForeignId('servis_id'));
        Schema::table('muhasebe_fis_satirlari', fn (Blueprint $table) => $table->dropColumn(['adet', 'birim']));
        Schema::table('cari_hesaplar', function (Blueprint $table) {
            $table->dropUnique('cari_musteri_tekil');
            $table->dropIndex('cari_plaka_arama_idx');
            $table->dropConstrainedForeignId('musteri_id');
            $table->dropColumn(['plaka', 'kaynak']);
        });
    }
};
