<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servisler', function (Blueprint $table) {
            $table->dateTime('servis_tarihi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('servisler', function (Blueprint $table) {
            $table->date('servis_tarihi')->nullable()->change();
        });
    }
};
