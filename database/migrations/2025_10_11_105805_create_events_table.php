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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('_id')->unique(); // MongoDB style ID
            $table->bigInteger('eventId')->unique(); // Event identifier
            $table->string('exEventId')->unique(); // External event identifier
            $table->string('sportId'); // Sport identifier
            $table->string('tournamentsId'); // Tournament identifier
            $table->string('tournamentsName'); // Tournament name
            $table->string('eventName'); // Event name
            $table->boolean('highlight')->default(false); // Highlight flag
            $table->boolean('quicklink')->default(false); // Quicklink flag
            $table->boolean('popular')->default(false); // Popular flag
            $table->tinyInteger('IsSettle')->default(0); // Settlement status
            $table->tinyInteger('IsVoid')->default(0); // Void status
            $table->tinyInteger('IsUnsettle')->default(1); // Unsettled status
            $table->tinyInteger('dataSwitch')->default(0); // Data switch flag
            $table->timestamp('createdAt'); // Creation timestamp
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('exEventId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
