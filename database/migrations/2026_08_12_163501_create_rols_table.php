<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {


        Schema::create('rols', function (Blueprint $table) {


            $table->id();


            /*
            Roller

            Firma Sahibi
            Müdür
            Usta
            Yedek Parça
            Ofis
            Muhasebe
            */


            $table->string('ad');



            $table->boolean('aktif')
                  ->default(true);



            $table->timestamps();


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('rols');

    }


};