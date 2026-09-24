<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('randevular', function (Blueprint $t) {
            $t->id(); $t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $t->foreignId('sube_id')->nullable()->constrained('subes')->nullOnDelete();
            $t->foreignId('musteri_id')->nullable()->constrained('musteris')->nullOnDelete();
            $t->foreignId('arac_id')->nullable()->constrained('araclar')->nullOnDelete();
            $t->foreignId('usta_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('hizmet_turu',100); $t->dateTime('baslangic'); $t->dateTime('bitis')->nullable();
            $t->string('durum',30)->default('planlandi'); $t->text('notlar')->nullable(); $t->timestamps();
        });
        Schema::create('sigorta_firmalari', function (Blueprint $t) {
            $t->id(); $t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $t->string('unvan'); $t->string('vergi_no',20)->nullable(); $t->string('telefon',30)->nullable();
            $t->string('eposta')->nullable(); $t->boolean('aktif')->default(true); $t->timestamps();
        });
        Schema::create('sigorta_hasarlari', function (Blueprint $t) {
            $t->id(); $t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();
            $t->foreignId('sube_id')->nullable()->constrained('subes')->nullOnDelete();
            $t->foreignId('musteri_id')->nullable()->constrained('musteris')->nullOnDelete();
            $t->foreignId('arac_id')->nullable()->constrained('araclar')->nullOnDelete();
            $t->foreignId('sigorta_firmasi_id')->nullable()->constrained('sigorta_firmalari')->nullOnDelete();
            $t->foreignId('servis_id')->nullable()->constrained('servisler')->nullOnDelete();
            $t->string('dosya_no',80); $t->string('durum',30)->default('acik');
            $t->decimal('onayli_tutar',14,2)->default(0); $t->decimal('tahsil_edilen',14,2)->default(0); $t->text('aciklama')->nullable(); $t->timestamps();
            $t->unique(['firma_id','dosya_no']);
        });
        Schema::create('b2b_siparisler', function (Blueprint $t) {
            $t->id(); $t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete(); $t->foreignId('sube_id')->nullable()->constrained('subes')->nullOnDelete();
            $t->string('siparis_no',60); $t->string('alici_unvan'); $t->string('durum',30)->default('taslak');
            $t->decimal('toplam_tutar',14,2)->default(0); $t->text('notlar')->nullable(); $t->timestamps(); $t->unique(['firma_id','siparis_no']);
        });
        Schema::create('b2b_siparis_satirlari', function (Blueprint $t) {
            $t->id(); $t->foreignId('b2b_siparis_id')->constrained('b2b_siparisler')->cascadeOnDelete();
            $t->foreignId('stok_parca_id')->nullable()->constrained('stok_parcalar')->nullOnDelete(); $t->string('urun_adi');
            $t->decimal('miktar',12,2); $t->decimal('birim_fiyat',14,2)->default(0); $t->decimal('kdv_orani',5,2)->default(20); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('b2b_siparis_satirlari'); Schema::dropIfExists('b2b_siparisler'); Schema::dropIfExists('sigorta_hasarlari'); Schema::dropIfExists('sigorta_firmalari'); Schema::dropIfExists('randevular'); }
};
