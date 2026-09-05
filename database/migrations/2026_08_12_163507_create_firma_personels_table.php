<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {


        Schema::create('firma_personels', function (Blueprint $table) {


            $table->id();



            /*
            Sistemdeki kullanıcı
            */

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();



            /*
            Bağlı firma
            */

            $table->foreignId('firma_id')
                  ->constrained()
                  ->cascadeOnDelete();



            /*
            Bağlı şube

            Aynı personel farklı şubelerde
            görev alabilir.
            */

            $table->foreignId('sube_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();



            /*
            Rol

            Usta
            Müdür
            Muhasebe
            */

            $table->foreignId('rol_id')
                  ->constrained()
                  ->cascadeOnDelete();



            $table->boolean('aktif')
                  ->default(true);



            $table->timestamps();


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('firma_personels');

    }


};