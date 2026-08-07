<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
Schema::table('users', function (Blueprint $table) {

    $table->string('username')
        ->unique()
        ->after('id');

    $table->string('surname')
        ->nullable()
        ->after('name');

    $table->string('phone')
        ->nullable()
        ->after('email');

    $table->string('tc_no')
        ->nullable()
        ->after('phone');

    $table->enum('status', [
        'aktif',
        'pasif'
    ])
    ->default('aktif')
    ->after('role');


    $table->foreignId('created_by')
        ->nullable()
        ->after('status')
        ->constrained('users')
        ->nullOnDelete();


    $table->timestamp('last_login_at')
        ->nullable()
        ->after('created_by');

});