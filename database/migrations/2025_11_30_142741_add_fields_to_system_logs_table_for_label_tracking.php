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
        Schema::table('system_logs', function (Blueprint $table) {
            // Add fields for event/label tracking
            $table->string('exEventId')->nullable()->after('user_id');
            $table->string('label_name')->nullable()->after('exEventId');
            $table->string('old_value')->nullable()->after('label_name');
            $table->string('new_value')->nullable()->after('old_value');
            $table->string('event_name')->nullable()->after('new_value');
            
            // Add indexes for better query performance
            $table->index('exEventId');
            $table->index('action');
            $table->index('label_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropIndex(['exEventId']);
            $table->dropIndex(['action']);
            $table->dropIndex(['label_name']);
            $table->dropIndex(['created_at']);
            
            $table->dropColumn([
                'exEventId',
                'label_name',
                'old_value',
                'new_value',
                'event_name'
            ]);
        });
    }
};
