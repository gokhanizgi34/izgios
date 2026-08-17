<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teklifler', function (Blueprint $table) {
            $table->date('gecerlilik_tarihi')->nullable()->after('tarih');
            $table->foreignId('cari_hesap_id')->nullable()->after('firma_id')->constrained('cari_hesaplar')->nullOnDelete();
            $table->decimal('ara_toplam', 14, 2)->default(0)->after('tutar');
            $table->decimal('kdv_toplam', 14, 2)->default(0)->after('ara_toplam');
            $table->decimal('iskonto_toplam', 14, 2)->default(0)->after('kdv_toplam');
            $table->string('para_birimi', 5)->default('TRY')->after('durum');
            $table->text('notlar')->nullable()->after('aciklama');
        });
        Schema::table('faturalar', function (Blueprint $table) {
            $table->date('vade_tarihi')->nullable()->after('tarih');
            $table->foreignId('cari_hesap_id')->nullable()->after('firma_id')->constrained('cari_hesaplar')->nullOnDelete();
            $table->decimal('ara_toplam', 14, 2)->default(0)->after('tutar');
            $table->decimal('kdv_toplam', 14, 2)->default(0)->after('ara_toplam');
            $table->decimal('iskonto_toplam', 14, 2)->default(0)->after('kdv_toplam');
            $table->string('para_birimi', 5)->default('TRY')->after('durum');
            $table->text('notlar')->nullable()->after('entegrasyon_durumu');
        });
        Schema::create('ticari_belge_satirlari', function (Blueprint $table) {
            $table->id();
            $table->string('belge_turu', 12);
            $table->unsignedBigInteger('belge_id');
            $table->string('urun_hizmet_adi');
            $table->decimal('miktar', 12, 3)->default(1);
            $table->string('birim', 30)->default('Adet');
            $table->decimal('birim_fiyat', 14, 2)->default(0);
            $table->decimal('iskonto_orani', 6, 2)->default(0);
            $table->decimal('kdv_orani', 6, 2)->default(20);
            $table->decimal('kdv_haric_tutar', 14, 2)->default(0);
            $table->decimal('kdv_tutari', 14, 2)->default(0);
            $table->decimal('kdv_dahil_tutar', 14, 2)->default(0);
            $table->timestamps();
            $table->index(['belge_turu', 'belge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticari_belge_satirlari');
        Schema::table('teklifler', function (Blueprint $table) { $table->dropConstrainedForeignId('cari_hesap_id'); $table->dropColumn(['gecerlilik_tarihi','ara_toplam','kdv_toplam','iskonto_toplam','para_birimi','notlar']); });
        Schema::table('faturalar', function (Blueprint $table) { $table->dropConstrainedForeignId('cari_hesap_id'); $table->dropColumn(['vade_tarihi','ara_toplam','kdv_toplam','iskonto_toplam','para_birimi','notlar']); });
    }
};
