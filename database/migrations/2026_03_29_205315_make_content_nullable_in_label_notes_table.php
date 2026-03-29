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
        Schema::table('label_notes', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
            $table->string('title')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('label_notes', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
        });
    }
};
