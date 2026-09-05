<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arac_hasarlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arac_id')->constrained('araclar')->cascadeOnDelete();
            $table->foreignId('servis_id')->nullable()->constrained('servisler')->nullOnDelete();
            $table->string('parca_adi');
            $table->text('aciklama')->nullable();
            $table->string('konum')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arac_hasarlari');
    }
};
