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
        Schema::table('label_whitelabels', function (Blueprint $table) {
            $table->string('whatsapp_group')->nullable()->after('name');
            $table->string('color', 20)->nullable()->default('#000000')->after('whatsapp_group');
        });
    }

    public function down(): void
    {
        Schema::table('label_whitelabels', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_group', 'color']);
        });
    }
};
