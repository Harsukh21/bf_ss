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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('about_pc_link')->nullable()->after('remote_access_enabled');
            $table->string('cmd_photo_link')->nullable()->after('about_pc_link');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['about_pc_link', 'cmd_photo_link']);
        });
    }
};
