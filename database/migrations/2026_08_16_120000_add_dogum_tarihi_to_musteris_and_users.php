<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musteris', function (Blueprint $table) {
            $table->date('dogum_tarihi')->nullable()->after('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->date('dogum_tarihi')->nullable()->after('tc_no');
        });
    }

    public function down(): void
    {
        Schema::table('musteris', function (Blueprint $table) {
            $table->dropColumn('dogum_tarihi');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dogum_tarihi');
        });
    }
};
