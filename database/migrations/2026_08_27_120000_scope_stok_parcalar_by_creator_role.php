<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stok_parcalar', function (Blueprint $table) {
            $table->dropUnique('stok_parcalar_firma_id_oem_no_unique');
            $table->foreignId('olusturan_id')->nullable()->after('firma_id')->constrained('users')->nullOnDelete();
            $table->string('olusturan_rol', 40)->nullable()->after('olusturan_id')->index();
            $table->unique(['firma_id', 'oem_no', 'olusturan_rol'], 'stok_parca_firma_oem_rol_unique');
        });
    }

    public function down(): void
    {
        Schema::table('stok_parcalar', function (Blueprint $table) {
            $table->dropUnique('stok_parca_firma_oem_rol_unique');
            $table->dropConstrainedForeignId('olusturan_id');
            $table->dropColumn('olusturan_rol');
            $table->unique(['firma_id', 'oem_no']);
        });
    }
};
