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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('sc_type_updated_by')->nullable()->after('sc_type');
            $table->string('sc_type_updated_by_name')->nullable()->after('sc_type_updated_by');
            $table->string('sc_type_updated_by_email')->nullable()->after('sc_type_updated_by_name');
            $table->timestamp('sc_type_updated_at')->nullable()->after('sc_type_updated_by_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'sc_type_updated_by',
                'sc_type_updated_by_name',
                'sc_type_updated_by_email',
                'sc_type_updated_at',
            ]);
        });
    }
};
