<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('label_reports', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('label_id');
            $table->string('agent')->nullable()->after('user_name');
            $table->string('origin')->nullable()->after('agent');
            $table->decimal('before_void_balance', 15, 2)->nullable()->after('origin');
            $table->decimal('after_void_balance', 15, 2)->nullable()->after('before_void_balance');
            $table->string('catch_by')->nullable()->after('after_void_balance');
            $table->foreignId('proof_type_id')->nullable()->constrained('label_proof_types')->nullOnDelete()->after('catch_by');
            $table->string('proof_status')->default('submitted')->after('proof_type_id');
            $table->string('void_status')->nullable()->after('proof_status');
            $table->text('remark')->nullable()->after('void_status');
            $table->json('originals')->nullable()->after('remark');
        });
    }

    public function down(): void
    {
        Schema::table('label_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proof_type_id');
            $table->dropColumn([
                'user_name','agent','origin','before_void_balance','after_void_balance',
                'catch_by','proof_status','void_status','remark','originals',
            ]);
        });
    }
};
