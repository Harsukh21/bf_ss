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
        Schema::create('telegram_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('exMarketId')->unique(); // Unique market identifier
            $table->string('exEventId')->nullable(); // Event identifier for reference
            $table->string('eventName')->nullable(); // Event name for reference
            $table->string('marketName')->nullable(); // Market name for reference
            $table->timestamp('notified_at'); // When notification was sent
            $table->timestamps();
            
            // Index for faster lookups
            $table->index('exMarketId');
            $table->index('notified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_notifications');
    }
};
