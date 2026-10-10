<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('firmas', function (Blueprint $table) {
            $table->string('google_yorum_linki', 1000)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('firmas', function (Blueprint $table) {
            $table->dropColumn('google_yorum_linki');
        });
    }
};
