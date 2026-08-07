<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {


        Schema::create('arac_hasar_fotograflari', function(Blueprint $table){


            $table->id();



            $table->foreignId('arac_hasari_id')
            ->constrained('arac_hasarlari')
            ->cascadeOnDelete();



            $table->string('dosya_yolu');



            $table->timestamps();


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('arac_hasar_fotograflari');

    }

};