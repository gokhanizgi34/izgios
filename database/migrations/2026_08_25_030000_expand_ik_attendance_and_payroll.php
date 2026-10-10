<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ik_personel_ozlukleri', function (Blueprint $table) {
            $table->time('calisma_baslangic')->default('08:30:00')->after('saatlik_mesai_ucreti');
            $table->time('calisma_bitis')->default('18:00:00')->after('calisma_baslangic');
            $table->unsignedSmallInteger('gunluk_mola_dakika')->default(60)->after('calisma_bitis');
            $table->json('calisma_gunleri')->nullable()->after('gunluk_mola_dakika');
            $table->decimal('fazla_mesai_carpani', 4, 2)->default(1.50)->after('calisma_gunleri');
            $table->uuid('puantaj_qr_token')->nullable()->unique()->after('fazla_mesai_carpani');
            $table->timestamp('puantaj_qr_yenilendi_at')->nullable()->after('puantaj_qr_token');
        });

        Schema::table('ik_puantaj_kayitlari', function (Blueprint $table) {
            $table->decimal('calisma_saati', 6, 2)->default(0)->after('cikis_saati');
            $table->string('kaynak', 20)->default('manuel')->after('durum');
            $table->string('giris_ip', 64)->nullable()->after('kaynak');
            $table->string('cikis_ip', 64)->nullable()->after('giris_ip');
        });

        Schema::create('ik_puantaj_hareketleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('hareket', 10);
            $table->timestamp('kayit_zamani');
            $table->string('kaynak', 20)->default('qr');
            $table->string('ip_adresi', 64)->nullable();
            $table->string('cihaz', 500)->nullable();
            $table->timestamps();
            $table->index(['firma_id', 'user_id', 'kayit_zamani'], 'ik_puantaj_hareket_index');
        });

        Schema::table('ik_bordrolar', function (Blueprint $table) {
            $table->decimal('sgk_isci', 14, 2)->default(0)->after('brut_ucret');
            $table->decimal('issizlik_isci', 14, 2)->default(0)->after('sgk_isci');
            $table->decimal('gelir_vergisi_matrahi', 14, 2)->default(0)->after('issizlik_isci');
            $table->decimal('gelir_vergisi', 14, 2)->default(0)->after('gelir_vergisi_matrahi');
            $table->decimal('damga_vergisi', 14, 2)->default(0)->after('gelir_vergisi');
            $table->decimal('sgk_isveren', 14, 2)->default(0)->after('damga_vergisi');
            $table->decimal('issizlik_isveren', 14, 2)->default(0)->after('sgk_isveren');
            $table->decimal('toplam_kesinti', 14, 2)->default(0)->after('issizlik_isveren');
            $table->decimal('isveren_maliyeti', 14, 2)->default(0)->after('toplam_kesinti');
        });
    }

    public function down(): void
    {
        Schema::table('ik_bordrolar', fn (Blueprint $table) => $table->dropColumn(['sgk_isci','issizlik_isci','gelir_vergisi_matrahi','gelir_vergisi','damga_vergisi','sgk_isveren','issizlik_isveren','toplam_kesinti','isveren_maliyeti']));
        Schema::dropIfExists('ik_puantaj_hareketleri');
        Schema::table('ik_puantaj_kayitlari', fn (Blueprint $table) => $table->dropColumn(['calisma_saati','kaynak','giris_ip','cikis_ip']));
        Schema::table('ik_personel_ozlukleri', function (Blueprint $table) {
            $table->dropUnique(['puantaj_qr_token']);
            $table->dropColumn(['calisma_baslangic','calisma_bitis','gunluk_mola_dakika','calisma_gunleri','fazla_mesai_carpani','puantaj_qr_token','puantaj_qr_yenilendi_at']);
        });
    }
};
