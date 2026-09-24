<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('firma_personels', function (Blueprint $table) {
            $table->string('ad_soyad')->nullable()->after('rol_id');
            $table->string('telefon', 30)->nullable()->after('ad_soyad');
            $table->string('email')->nullable()->after('telefon');
        });
    }

    public function down(): void
    {
        Schema::table('firma_personels', function (Blueprint $table) {
            $table->dropColumn(['ad_soyad', 'telefon', 'email']);
        });
    }
};
