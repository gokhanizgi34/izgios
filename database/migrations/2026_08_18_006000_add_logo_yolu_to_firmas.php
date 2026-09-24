<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('firmas', function (Blueprint $table) {
            $table->string('logo_yolu')->nullable()->after('google_yorum_linki');
        });
    }

    public function down(): void
    {
        Schema::table('firmas', function (Blueprint $table) {
            $table->dropColumn('logo_yolu');
        });
    }
};
