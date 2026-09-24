<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('firmas', function (Blueprint $table) {

            $table->boolean('merkez_goster')
                ->default(false)
                ->after('aktif');

        });
    }


    public function down(): void
    {
        Schema::table('firmas', function (Blueprint $table) {

            $table->dropColumn('merkez_goster');

        });
    }

};