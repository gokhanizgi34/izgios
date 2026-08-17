<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::create('firmas', function (Blueprint $table) {

            $table->id();

            $table->string('unvan');

            $table->string('vergi_no')
                  ->nullable();

            $table->string('telefon')
                  ->nullable();

            $table->string('email')
                  ->nullable();

            $table->text('adres')
                  ->nullable();


            // Firma aktif / pasif

            $table->boolean('aktif')
                  ->default(true);


            $table->timestamps();

        });

    }



    public function down(): void
    {

        Schema::dropIfExists('firmas');

    }

};