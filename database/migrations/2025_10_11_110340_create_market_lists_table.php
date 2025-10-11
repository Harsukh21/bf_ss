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
        Schema::create('market_lists', function (Blueprint $table) {
            $table->id();
            $table->string('_id')->unique(); // MongoDB style ID
            $table->string('eventName'); // Event name
            $table->string('exEventId'); // External event identifier
            $table->string('exMarketId')->unique(); // External market identifier
            $table->boolean('isPreBet')->default(false); // Pre-bet flag
            $table->string('marketName'); // Market name
            $table->timestamp('marketTime'); // Market time
            $table->string('sportName'); // Sport name
            $table->string('tournamentsName'); // Tournament name
            $table->string('type'); // Market type (e.g., match_odds)
            $table->boolean('isLive')->default(false); // Live flag
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('exEventId');
            $table->index('exMarketId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_lists');
    }
};
