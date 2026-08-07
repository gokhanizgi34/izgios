<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('servis_parcalar', function (Blueprint $table) {

            $table->id();


            // Bağlı olduğu servis
            $table->foreignId('servis_id')
                  ->constrained('servisler')
                  ->cascadeOnDelete();


            // Parça bilgisi
            $table->string('parca_adi');


            // Kullanılan adet
            $table->integer('adet')
                  ->default(1);


            // Birim fiyat
            $table->decimal('birim_fiyat',10,2)
                  ->default(0);


            // Toplam fiyat
            $table->decimal('toplam_fiyat',10,2)
                  ->default(0);


            /*
             İleride depo bağlantısı için hazır alan

             stok sistemi geldiğinde:
             parca_id üzerinden depo ürününe bağlanabilir
            */

            $table->unsignedBigInteger('stok_parca_id')
                  ->nullable();


            // Usta açıklaması
            $table->text('aciklama')
                  ->nullable();


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('servis_parcalar');
    }

};