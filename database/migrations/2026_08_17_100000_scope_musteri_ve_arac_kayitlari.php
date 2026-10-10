<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musteris', function (Blueprint $table) {
            if (! Schema::hasColumn('musteris', 'firma_id')) {
                $table->foreignId('firma_id')->nullable()->after('id')->constrained('firmas')->nullOnDelete();
            }
            if (! Schema::hasColumn('musteris', 'sube_id')) {
                $table->foreignId('sube_id')->nullable()->after('firma_id')->constrained('subes')->nullOnDelete();
            }
            if (! Schema::hasColumn('musteris', 'dogum_tarihi')) {
                $table->date('dogum_tarihi')->nullable()->after('email');
            }
        });

        Schema::table('araclar', function (Blueprint $table) {
            if (! Schema::hasColumn('araclar', 'firma_id')) {
                $table->foreignId('firma_id')->nullable()->after('musteri_id')->constrained('firmas')->nullOnDelete();
            }
            if (! Schema::hasColumn('araclar', 'sube_id')) {
                $table->foreignId('sube_id')->nullable()->after('firma_id')->constrained('subes')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('araclar', function (Blueprint $table) {
            if (Schema::hasColumn('araclar', 'sube_id')) $table->dropConstrainedForeignId('sube_id');
            if (Schema::hasColumn('araclar', 'firma_id')) $table->dropConstrainedForeignId('firma_id');
        });
        Schema::table('musteris', function (Blueprint $table) {
            if (Schema::hasColumn('musteris', 'sube_id')) $table->dropConstrainedForeignId('sube_id');
            if (Schema::hasColumn('musteris', 'firma_id')) $table->dropConstrainedForeignId('firma_id');
            if (Schema::hasColumn('musteris', 'dogum_tarihi')) $table->dropColumn('dogum_tarihi');
        });
    }
};
