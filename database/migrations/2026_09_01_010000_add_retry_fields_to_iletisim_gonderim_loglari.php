<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('iletisim_gonderim_loglari', function (Blueprint $table) {
            $table->unsignedTinyInteger('deneme_sayisi')->default(0)->after('durum');
            $table->text('son_hata')->nullable()->after('deneme_sayisi');
            $table->timestamp('sonraki_deneme_at')->nullable()->after('son_hata');
            $table->index(['durum', 'sonraki_deneme_at'], 'iletisim_tekrar_deneme_idx');
        });
    }

    public function down(): void
    {
        Schema::table('iletisim_gonderim_loglari', function (Blueprint $table) {
            $table->dropIndex('iletisim_tekrar_deneme_idx');
            $table->dropColumn(['deneme_sayisi', 'son_hata', 'sonraki_deneme_at']);
        });
    }
};
