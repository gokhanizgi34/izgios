<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE destek_talepleri MODIFY ai_durum VARCHAR(80) NOT NULL DEFAULT 'bekliyor'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE destek_talepleri MODIFY ai_durum VARCHAR(30) NOT NULL DEFAULT 'bekliyor'");
    }
};
