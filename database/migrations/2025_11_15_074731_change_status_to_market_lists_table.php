<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE market_lists ALTER COLUMN status DROP DEFAULT');

        DB::statement("
            UPDATE market_lists
            SET status = CASE UPPER(status)
                WHEN 'UNSETTLED' THEN '1'
                WHEN 'UPCOMING' THEN '2'
                WHEN 'INPLAY' THEN '3'
                WHEN 'SETTLED' THEN '4'
                WHEN 'CLOSED' THEN '4'
                WHEN 'VOIDED' THEN '5'
                WHEN 'REMOVED' THEN '6'
                WHEN '' THEN NULL
                ELSE NULL
            END
        ");

        DB::statement("ALTER TABLE market_lists ALTER COLUMN status TYPE SMALLINT USING NULLIF(status, '')::smallint");

        Schema::table('market_lists', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_lists', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });
    }
};
