<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {


        Schema::create('araclar', function (Blueprint $table) {


            $table->id();



            /*
            |--------------------------------------------------------------------------
            | Müşteri bağlantısı
            |--------------------------------------------------------------------------
            */

            $table->foreignId('musteri_id')

                ->constrained('musteris')

                ->cascadeOnDelete();




            /*
            |--------------------------------------------------------------------------
            | Araç kimlik bilgileri
            |--------------------------------------------------------------------------
            */

            $table->string('plaka',20);



            $table->string('marka',100);



            $table->string('model',100);




            /*
            |--------------------------------------------------------------------------
            | Teknik bilgiler
            |--------------------------------------------------------------------------
            */


            $table->string('model_yili',20)
                ->nullable();



            $table->integer('kilometre')
                ->nullable();



            $table->string('sase_no',100)
                ->nullable();



            $table->string('motor_no',100)
                ->nullable();




            $table->string('yakit_tipi',50)
                ->nullable();



            $table->string('vites',50)
                ->nullable();





            /*
            |--------------------------------------------------------------------------
            | QR Dijital Kimlik Hazırlığı
            |--------------------------------------------------------------------------
            */


            $table->uuid('qr_token')
                ->unique()
                ->nullable();



            $table->timestamp('qr_created_at')
                ->nullable();





            /*
            |--------------------------------------------------------------------------
            | Sistem durumu
            |--------------------------------------------------------------------------
            */


            $table->boolean('aktif')
                ->default(true);





            /*
            |--------------------------------------------------------------------------
            | Notlar
            |--------------------------------------------------------------------------
            */


            $table->text('notlar')
                ->nullable();




            $table->timestamps();


        });


    }





    public function down(): void
    {

        Schema::dropIfExists('araclar');

    }


};