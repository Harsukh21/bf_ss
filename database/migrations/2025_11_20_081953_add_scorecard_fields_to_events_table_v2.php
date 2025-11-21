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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_interrupted')->default(false)->after('isCompleted');
            $table->jsonb('labels')->nullable()->after('is_interrupted');
            $table->integer('remind_me_after')->nullable()->after('labels');
        });

        // Set default labels structure for events table (same as market_lists)
        // Format: {"4x":false,"b2c":false,"b2b":false,"usdt":false}
        DB::statement("
            UPDATE events
            SET labels = '{\"4x\":false,\"b2c\":false,\"b2b\":false,\"usdt\":false}'::jsonb
            WHERE labels IS NULL
        ");

        // Set default value for labels column (same structure as market_lists)
        DB::statement("
            ALTER TABLE events
            ALTER COLUMN labels
            SET DEFAULT '{\"4x\":false,\"b2c\":false,\"b2b\":false,\"usdt\":false}'::jsonb
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_interrupted', 'labels', 'remind_me_after']);
        });
    }
};
