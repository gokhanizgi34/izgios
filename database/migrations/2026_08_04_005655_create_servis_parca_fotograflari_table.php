<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('servis_parca_fotograflari', function (Blueprint $table) {

            $table->id();


            // Bağlı olduğu servis parçası
            $table->foreignId('servis_parca_id')
                  ->constrained('servis_parcalar')
                  ->cascadeOnDelete();



            /*
             Fotoğraf tipi

             eski
             yeni
            */

            $table->string('tip')
                  ->default('eski');



            // Fotoğraf dosya yolu

            $table->string('dosya_yolu');



            // Fotoğraf açıklaması

            $table->text('aciklama')
                  ->nullable();



            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('servis_parca_fotograflari');
    }

};