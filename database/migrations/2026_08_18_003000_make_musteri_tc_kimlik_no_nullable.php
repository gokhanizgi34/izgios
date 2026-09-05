<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musteris', function (Blueprint $table) {
            $table->string('tc_kimlik_no', 11)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('musteris', function (Blueprint $table) {
            $table->string('tc_kimlik_no', 11)->nullable(false)->change();
        });
    }
};
