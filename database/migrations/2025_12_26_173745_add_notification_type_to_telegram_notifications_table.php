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
        Schema::table('telegram_notifications', function (Blueprint $table) {
            $table->string('notification_type')->default('inplay')->after('marketName');
            $table->index('notification_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_notifications', function (Blueprint $table) {
            $table->dropIndex(['notification_type']);
            $table->dropColumn('notification_type');
        });
    }
};
