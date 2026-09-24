<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {


        Schema::create('subes', function (Blueprint $table) {


            $table->id();


            /*
            Hangi firma
            */

            $table->foreignId('firma_id')
                  ->constrained()
                  ->cascadeOnDelete();



            /*
            Şube adı

            Örnek:

            İzgi Oto
            İzgi Oto Kadosan
            */

            $table->string('sube_adi');



            /*
            Merkez şube tercihi

            Tek şubeli firma da merkez olabilir.
            İsimde gösterilip gösterilmeyeceği
            ileride ayardan yönetilecek.
            */

            $table->boolean('merkez_mi')
                  ->default(false);



            $table->string('adres')
                  ->nullable();



            $table->string('telefon')
                  ->nullable();



            $table->boolean('aktif')
                  ->default(true);



            $table->timestamps();


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('subes');

    }


};