<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servis_islemleri', function (Blueprint $table) {
            $table->string('kategori', 30)->default('servis')->after('servis_id');
            $table->index(['servis_id', 'kategori']);
        });

        Schema::table('stok_hareketleri', function (Blueprint $table) {
            $table->foreignId('cari_hesap_id')->nullable()->after('stok_parca_id')->constrained('cari_hesaplar')->nullOnDelete();
            $table->decimal('birim_alis_fiyati', 14, 2)->default(0)->after('miktar');
            $table->decimal('toplam_tutar', 14, 2)->default(0)->after('birim_alis_fiyati');
        });
    }

    public function down(): void
    {
        Schema::table('stok_hareketleri', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cari_hesap_id');
            $table->dropColumn(['birim_alis_fiyati', 'toplam_tutar']);
        });
        Schema::table('servis_islemleri', function (Blueprint $table) {
            $table->dropIndex(['servis_id', 'kategori']);
            $table->dropColumn('kategori');
        });
    }
};
