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
            // Drop all indexes we added, keeping only the primary key (id)
            $table->dropIndex(['gender']);
            $table->dropIndex(['industry']);
            $table->dropIndex(['status']);
            $table->dropIndex(['country']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['salary']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['gender', 'industry']);
            $table->dropIndex(['country', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-add indexes if needed to rollback
            $table->index('gender');
            $table->index('industry');
            $table->index('status');
            $table->index('country');
            $table->index('created_at');
            $table->index('salary');
            $table->index(['status', 'created_at']);
            $table->index(['gender', 'industry']);
            $table->index(['country', 'status']);
        });
    }
};