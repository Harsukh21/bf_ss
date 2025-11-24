<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Increase web_pin column length to 255 to accommodate bcrypt hashes (60 chars)
        // Using raw SQL for PostgreSQL compatibility
        DB::statement('ALTER TABLE users ALTER COLUMN web_pin TYPE VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original length of 20
        // Note: This will fail if any hashed values exist (which are 60+ chars)
        DB::statement('ALTER TABLE users ALTER COLUMN web_pin TYPE VARCHAR(20)');
    }
};
