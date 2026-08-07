<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {


        Schema::create('servisler', function (Blueprint $table) {


            $table->id();



            /*
            |--------------------------------------------------------------------------
            | Bağlantılar
            |--------------------------------------------------------------------------
            */


            $table->foreignId('musteri_id')
                ->constrained('musteris')
                ->cascadeOnDelete();



            $table->foreignId('arac_id')
    ->constrained('araclar')
    ->cascadeOnDelete();





            /*
            |--------------------------------------------------------------------------
            | Servis Bilgileri
            |--------------------------------------------------------------------------
            */


            $table->string('servis_no')
                ->unique();



            $table->text('sikayet')
                ->nullable();



            $table->text('yapilan_islem')
                ->nullable();



            $table->text('kullanilan_parca')
                ->nullable();






            /*
            |--------------------------------------------------------------------------
            | Ücret Bilgileri
            |--------------------------------------------------------------------------
            */


            $table->decimal('parca_tutari',10,2)
                ->default(0);



            $table->decimal('iscilik_tutari',10,2)
                ->default(0);



            $table->decimal('toplam_tutar',10,2)
                ->default(0);







            /*
            |--------------------------------------------------------------------------
            | Servis Durumu
            |--------------------------------------------------------------------------
            */


            $table->string('durum')
                ->default('Bekliyor');







            /*
            |--------------------------------------------------------------------------
            | Ek Bilgiler
            |--------------------------------------------------------------------------
            */


            $table->text('notlar')
                ->nullable();



            $table->timestamps();



        });


    }







    public function down(): void
    {


        Schema::dropIfExists('servisler');


    }


};