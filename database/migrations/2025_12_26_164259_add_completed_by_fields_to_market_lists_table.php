<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('market_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('completed_by')->nullable()->after('is_done');
            $table->string('completed_by_name')->nullable()->after('completed_by');
            $table->string('completed_by_email')->nullable()->after('completed_by_name');
            $table->timestamp('completed_at')->nullable()->after('completed_by_email');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_lists', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'completed_by',
                'completed_by_name',
                'completed_by_email',
                'completed_at',
            ]);
        });
    }
};
