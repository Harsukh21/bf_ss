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
            $table->jsonb('label_timestamps')->nullable()->after('labels');
        });

        // Initialize label_timestamps with null values for all labels
        // Format: {"4x": null, "b2c": null, "b2b": null, "usdt": null, "bookmaker": null, "unmatch": null}
        $labelKeys = array_keys(config('labels.labels', []));
        $defaultTimestamps = [];
        foreach ($labelKeys as $key) {
            $defaultTimestamps[strtolower($key)] = null;
        }
        $defaultTimestampsJson = json_encode($defaultTimestamps);

        DB::statement("
            UPDATE events
            SET label_timestamps = '{$defaultTimestampsJson}'::jsonb
            WHERE label_timestamps IS NULL
        ");

        // Set default value for label_timestamps column
        DB::statement("
            ALTER TABLE events
            ALTER COLUMN label_timestamps
            SET DEFAULT '{$defaultTimestampsJson}'::jsonb
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('label_timestamps');
        });
    }
};
