<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servisler', function (Blueprint $table) {
            $table->foreignId('usta_id')->nullable()->after('sube_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('servisler', function (Blueprint $table) {
            $table->dropConstrainedForeignId('usta_id');
        });
    }
};
