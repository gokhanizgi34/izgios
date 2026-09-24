<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'surname')) {
                $table->string('surname')->nullable()->after('name');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'tc_no')) {
                $table->string('tc_no')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['aktif', 'pasif'])->default('aktif')->after('role');
            }

            if (! Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        // Bu migration, önceki eksik migration nedeniyle oluşan şema farkını
        // düzeltir. Mevcut canlı veriyi geri alma sırasında silmemek için no-op'tur.
    }
};
