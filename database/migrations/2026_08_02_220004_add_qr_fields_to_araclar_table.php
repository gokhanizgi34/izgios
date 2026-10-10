<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {
        if (! Schema::hasColumn('araclar', 'qr_token')) {
            Schema::table('araclar', function (Blueprint $table) {
                $table->uuid('qr_token')
                    ->nullable()
                    ->unique()
                    ->after('notlar');
            });
        }

        if (! Schema::hasColumn('araclar', 'qr_created_at')) {
            Schema::table('araclar', function (Blueprint $table) {
                $table->timestamp('qr_created_at')
                    ->nullable()
                    ->after('qr_token');
            });
        }


    }




    public function down(): void
    {


        $columns = array_values(array_filter([
            Schema::hasColumn('araclar', 'qr_token') ? 'qr_token' : null,
            Schema::hasColumn('araclar', 'qr_created_at') ? 'qr_created_at' : null,
        ]));

        if ($columns !== []) {
            Schema::table('araclar', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }


    }


};
