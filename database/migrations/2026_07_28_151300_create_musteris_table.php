<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musteris', function (Blueprint $table) {
            $table->id();
            $table->string('ad_soyad');
            $table->string('tc_kimlik_no', 11)->nullable();
            $table->string('telefon', 30)->nullable();
            $table->string('telefon2', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('adres')->nullable();
            $table->text('notlar')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musteris');
    }
};
