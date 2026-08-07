<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('servis_islemleri', function (Blueprint $table) {

            $table->id();


            // Bağlı olduğu servis
            $table->foreignId('servis_id')
                  ->constrained('servisler')
                  ->cascadeOnDelete();


            // Yapılan işlem adı
            $table->string('islem_adi');


            // Usta açıklaması
            $table->text('aciklama')
                  ->nullable();


            // İşlem tutarı
            $table->decimal('tutar',10,2)
                  ->default(0);


            /*
             Durum:

             bekliyor
             devam_ediyor
             tamamlandi
             iptal
            */

            $table->string('durum')
                  ->default('bekliyor');


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('servis_islemleri');
    }

};