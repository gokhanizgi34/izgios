<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {


        Schema::table('araclar', function (Blueprint $table) {


            $table->dropForeign([
                'musteri_id'
            ]);


            $table->foreignId('musteri_id')
                ->nullable()
                ->change();



            $table->foreign('musteri_id')
                ->references('id')
                ->on('musteris')
                ->nullOnDelete();



        });


    }






    public function down(): void
    {


        Schema::table('araclar', function (Blueprint $table) {


            $table->dropForeign([
                'musteri_id'
            ]);



            $table->foreignId('musteri_id')
                ->nullable(false)
                ->change();



            $table->foreign('musteri_id')
                ->references('id')
                ->on('musteris')
                ->cascadeOnDelete();



        });


    }


};