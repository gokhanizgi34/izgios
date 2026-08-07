<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::table('servisler', function (Blueprint $table) {

            $table->date('servis_tarihi')
                ->nullable();

            $table->integer('giris_km')
                ->nullable();

            $table->date('teslim_tarihi')
                ->nullable();

            $table->date('sonraki_bakim_tarihi')
                ->nullable();

            $table->integer('bakim_periyodu')
                ->nullable();

        });

    }



    public function down(): void
    {

        Schema::table('servisler', function (Blueprint $table) {

            $table->dropColumn([
                'servis_tarihi',
                'giris_km',
                'teslim_tarihi',
                'sonraki_bakim_tarihi',
                'bakim_periyodu'
            ]);

        });

    }

};