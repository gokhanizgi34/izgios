<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subes', function (Blueprint $table) {
            $table->string('whatsapp_no', 25)->nullable()->after('telefon');
        });

        Schema::table('servisler', function (Blueprint $table) {
            $table->foreignId('firma_id')->nullable()->after('arac_id')->constrained('firmas')->nullOnDelete();
            $table->foreignId('sube_id')->nullable()->after('firma_id')->constrained('subes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('servisler', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sube_id');
            $table->dropConstrainedForeignId('firma_id');
        });

        Schema::table('subes', function (Blueprint $table) {
            $table->dropColumn('whatsapp_no');
        });
    }
};
