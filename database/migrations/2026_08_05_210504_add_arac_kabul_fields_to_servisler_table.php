<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('servisler', function (Blueprint $table) {


            $table->text('usta_notu')
                ->nullable();


            $table->string('oncelik')
                ->default('Normal');


            $table->string('yakit_seviyesi')
                ->nullable();


            $table->string('anahtar_durumu')
                ->nullable();


            $table->boolean('ruhsat_aracta')
                ->default(false);


        });

    }



    public function down(): void
    {

        Schema::table('servisler', function (Blueprint $table) {


            $table->dropColumn([

                'usta_notu',

                'oncelik',

                'yakit_seviyesi',

                'anahtar_durumu',

                'ruhsat_aracta',

            ]);


        });

    }

};