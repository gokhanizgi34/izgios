<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('kullanici_tercihleri',function(Blueprint $t){$t->id();$t->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();$t->string('tema')->default('acik');$t->boolean('e_posta_bildirimleri')->default(true);$t->boolean('sistem_bildirimleri')->default(true);$t->timestamps();});} public function down(): void {Schema::dropIfExists('kullanici_tercihleri');} };
