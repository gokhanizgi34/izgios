<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sistem_hata_durumlari', function (Blueprint $table) {
            $table->string('seviye', 20)->nullable()->after('hata_kodu');
            $table->string('ekran')->nullable()->after('seviye');
            $table->string('islem')->nullable()->after('ekran');
            $table->text('sebep')->nullable()->after('islem');
            $table->timestamp('ilk_goruldu_at')->nullable()->after('kontrol_notu');
            $table->timestamp('son_goruldu_at')->nullable()->after('ilk_goruldu_at');
        });
    }

    public function down(): void
    {
        Schema::table('sistem_hata_durumlari', fn (Blueprint $table) => $table->dropColumn(['seviye','ekran','islem','sebep','ilk_goruldu_at','son_goruldu_at']));
    }
};
