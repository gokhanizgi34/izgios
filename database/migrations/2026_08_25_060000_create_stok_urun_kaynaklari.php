<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_urun_kaynaklari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->string('ad', 120);
            $table->text('adres_sifreli');
            $table->boolean('aktif')->default(true);
            $table->string('son_durum', 30)->default('bekliyor');
            $table->unsignedInteger('son_urun_sayisi')->default(0);
            $table->text('son_hata')->nullable();
            $table->timestamp('son_senkron_at')->nullable();
            $table->foreignId('olusturan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['firma_id', 'ad']);
        });
        Schema::table('stok_parcalar', function (Blueprint $table) {
            $table->foreignId('urun_kaynak_id')->nullable()->after('firma_id')->constrained('stok_urun_kaynaklari')->nullOnDelete();
            $table->decimal('tedarikci_stok', 14, 2)->default(0)->after('stok_miktari');
            $table->decimal('tedarikci_fiyat', 14, 2)->default(0)->after('satis_fiyati');
            $table->timestamp('kaynak_senkron_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('stok_parcalar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('urun_kaynak_id');
            $table->dropColumn(['tedarikci_stok','tedarikci_fiyat','kaynak_senkron_at']);
        });
        Schema::dropIfExists('stok_urun_kaynaklari');
    }
};
