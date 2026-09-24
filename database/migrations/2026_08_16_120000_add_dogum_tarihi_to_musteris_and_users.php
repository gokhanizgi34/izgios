<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('musteris', 'dogum_tarihi')) {
            Schema::table('musteris', function (Blueprint $table) {
                $table->date('dogum_tarihi')->nullable()->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'dogum_tarihi')) {
            Schema::table('users', function (Blueprint $table) {
                // Kullanıcı yönetimi alanları farklı sürümlerde farklı sırada
                // eklendiğinden mevcut bir sütuna bağımlı konumlandırma yapmıyoruz.
                $table->date('dogum_tarihi')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('musteris', 'dogum_tarihi')) {
            Schema::table('musteris', function (Blueprint $table) {
                $table->dropColumn('dogum_tarihi');
            });
        }

        if (Schema::hasColumn('users', 'dogum_tarihi')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dogum_tarihi');
            });
        }
    }
};
