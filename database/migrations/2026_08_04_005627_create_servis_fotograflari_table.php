<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('servis_fotograflari', function (Blueprint $table) {

            $table->id();


            // Hangi servise ait
            $table->foreignId('servis_id')
                  ->constrained('servisler')
                  ->cascadeOnDelete();


            /*
            Fotoğraf kategorisi

            on
            arka
            sag
            sol
            ic
            hasar
            */

            $table->string('kategori')
                  ->nullable();


            // Dosyanın kayıt yolu
            $table->string('dosya_yolu');


            // Usta açıklaması
            $table->text('aciklama')
                  ->nullable();


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('servis_fotograflari');
    }

};