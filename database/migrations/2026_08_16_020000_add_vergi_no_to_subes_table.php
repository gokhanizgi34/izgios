<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subes', function (Blueprint $table) {
            $table->string('vergi_no', 50)->nullable()->after('sube_adi');
        });
    }

    public function down(): void
    {
        Schema::table('subes', function (Blueprint $table) {
            $table->dropColumn('vergi_no');
        });
    }
};
