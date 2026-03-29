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
            $table->date('note_date')->nullable()->after('label_id');
            $table->string('origin')->nullable()->after('note_date');
            $table->string('agent')->nullable()->after('origin');
            $table->string('user_name')->nullable()->after('agent');
            $table->string('whatsapp_group')->nullable()->after('user_name');
            $table->string('note')->nullable()->after('whatsapp_group');
        });
    }

    public function down(): void
    {
        Schema::table('label_notes', function (Blueprint $table) {
            $table->dropColumn(['note_date', 'origin', 'agent', 'user_name', 'whatsapp_group', 'note']);
        });
    }
};
