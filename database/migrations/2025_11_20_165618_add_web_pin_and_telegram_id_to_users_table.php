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
        Schema::table('users', function (Blueprint $table) {
            // Using 255 to accommodate bcrypt hashes (60 chars) and future-proofing
            $table->string('web_pin', 255)->nullable()->after('password');
            $table->string('telegram_id', 100)->nullable()->after('web_pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['web_pin', 'telegram_id']);
        });
    }
};
