<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('market_lists', function (Blueprint $table) {
            $table->boolean('is_done')->default(false)->after('isLive');
            $table->jsonb('labels')->nullable()->after('is_done');
            $table->text('remark')->nullable()->after('labels');
        });

        DB::table('market_lists')->update([
            'labels' => json_encode([
                '4x' => false,
                'b2c' => false,
                'b2b' => false,
                'usdt' => false,
            ]),
        ]);

        DB::statement("
            ALTER TABLE market_lists
            ALTER COLUMN labels
            SET DEFAULT '{\"4x\":false,\"b2c\":false,\"b2b\":false,\"usdt\":false}'::jsonb
        ");
    }

    public function down(): void
    {
        Schema::table('market_lists', function (Blueprint $table) {
            $table->dropColumn(['remark', 'labels', 'is_done']);
        });
    }
};

