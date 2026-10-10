<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('firma_iletisim_kanal_ayarlari',function(Blueprint $t){$t->id();$t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();$t->string('mesaj_grubu',40);$t->boolean('aktif')->default(true);$t->boolean('whatsapp')->default(false);$t->boolean('sms')->default(false);$t->boolean('email')->default(false);$t->time('gonderim_saati')->nullable();$t->text('sablon')->nullable();$t->timestamps();$t->unique(['firma_id','mesaj_grubu']);});
  Schema::create('iletisim_gonderim_loglari',function(Blueprint $t){$t->id();$t->foreignId('firma_id')->constrained('firmas')->cascadeOnDelete();$t->foreignId('musteri_id')->nullable()->constrained('musteris')->nullOnDelete();$t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();$t->string('mesaj_grubu',40);$t->string('kanal',20);$t->string('durum',30)->default('kuyrukta');$t->string('alici_maskeli',180)->nullable();$t->text('mesaj')->nullable();$t->timestamp('gonderildi_at')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('iletisim_gonderim_loglari');Schema::dropIfExists('firma_iletisim_kanal_ayarlari');}
};
