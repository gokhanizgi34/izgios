<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arac_qr_havuzu', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->uuid('parti_kodu')->index();
            $table->unsignedSmallInteger('sira_no');
            $table->string('durum', 20)->default('bos')->index();
            $table->unsignedBigInteger('arac_id')->nullable()->unique();
            $table->unsignedBigInteger('olusturan_id')->nullable();
            $table->unsignedBigInteger('atayan_id')->nullable();
            $table->timestamp('atanma_at')->nullable();
            $table->timestamp('iptal_at')->nullable();
            $table->timestamps();

            $table->unique(['parti_kodu', 'sira_no']);
        });

        if (Schema::hasTable('araclar')) {
            DB::table('araclar')
                ->whereNotNull('qr_token')
                ->orderBy('id')
                ->chunkById(200, function ($araclar) {
                    $parti = (string) \Illuminate\Support\Str::uuid();
                    foreach ($araclar as $index => $arac) {
                        DB::table('arac_qr_havuzu')->insertOrIgnore([
                            'token' => $arac->qr_token,
                            'parti_kodu' => $parti,
                            'sira_no' => $index + 1,
                            'durum' => 'atanmis',
                            'arac_id' => $arac->id,
                            'atanma_at' => $arac->qr_created_at ?? $arac->created_at ?? now(),
                            'created_at' => $arac->created_at ?? now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('arac_qr_havuzu');
    }
};
