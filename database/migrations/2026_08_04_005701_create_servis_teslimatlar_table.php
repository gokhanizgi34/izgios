<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('servis_teslimatlar', function (Blueprint $table) {

            $table->id();


            // Bağlı olduğu servis
            $table->foreignId('servis_id')
                  ->constrained('servisler')
                  ->cascadeOnDelete();



            // Teslim alan kişi

            $table->string('teslim_alan')
                  ->nullable();



            // Teslim tarihi

            $table->dateTime('teslim_tarihi')
                  ->nullable();



            /*
             Ödeme tipi

             nakit
             kart
             havale
             cari
             */

            $table->string('odeme_tipi')
                  ->nullable();



            // Teslim notu

            $table->text('aciklama')
                  ->nullable();



            // İleride mesaj sistemi için hazır

            $table->boolean('teslim_mesaji_gonderildi')
                  ->default(false);



            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('servis_teslimatlar');
    }

};