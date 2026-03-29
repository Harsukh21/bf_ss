<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('label_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
            $table->foreignId('proof_type_id')->nullable()->constrained('label_proof_types')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_link')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('label_proofs'); }
};
