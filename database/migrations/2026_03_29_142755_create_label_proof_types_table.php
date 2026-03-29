<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('label_proof_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['label_id', 'slug']);
        });
    }
    public function down(): void { Schema::dropIfExists('label_proof_types'); }
};
