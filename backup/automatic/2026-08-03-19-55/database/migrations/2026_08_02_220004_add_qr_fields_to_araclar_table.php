<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {

        Schema::table('araclar', function (Blueprint $table) {


            $table->uuid('qr_token')
                ->nullable()
                ->unique()
                ->after('notlar');


            $table->timestamp('qr_created_at')
                ->nullable()
                ->after('qr_token');


        });


    }




    public function down(): void
    {


        Schema::table('araclar', function (Blueprint $table) {


            $table->dropColumn([
                'qr_token',
                'qr_created_at'
            ]);


        });


    }


};