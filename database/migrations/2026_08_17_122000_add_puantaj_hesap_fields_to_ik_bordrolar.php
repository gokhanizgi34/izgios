<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ik_bordrolar', function (Blueprint $table) {
            $table->decimal('esas_net_ucret', 14, 2)->default(0)->after('net_ucret');
            $table->decimal('calisilan_gun', 5, 1)->default(30)->after('esas_net_ucret');
            $table->decimal('eksik_gun', 5, 1)->default(0)->after('calisilan_gun');
            $table->decimal('eksik_gun_kesintisi', 14, 2)->default(0)->after('eksik_gun');
            $table->decimal('odenecek_net', 14, 2)->default(0)->after('eksik_gun_kesintisi');
        });
    }

    public function down(): void
    {
        Schema::table('ik_bordrolar', function (Blueprint $table) {
            $table->dropColumn(['esas_net_ucret', 'calisilan_gun', 'eksik_gun', 'eksik_gun_kesintisi', 'odenecek_net']);
        });
    }
};
