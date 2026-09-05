<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destek_talepleri', function (Blueprint $table) {
            $table->string('kullanici_geri_bildirimi', 30)->nullable()->after('ai_durum');
            $table->timestamp('son_yanit_at')->nullable()->after('ai_cozum');
            $table->timestamp('zaman_asimi_at')->nullable()->after('son_yanit_at');
        });
    }

    public function down(): void
    {
        Schema::table('destek_talepleri', function (Blueprint $table) {
            $table->dropColumn(['kullanici_geri_bildirimi', 'son_yanit_at', 'zaman_asimi_at']);
        });
    }
};
