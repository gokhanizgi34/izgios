<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kept for migration history compatibility. The base table already
        // contains this nullable field.
    }

    public function down(): void
    {
    }
};
