<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('label_whitelabels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('logo_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('label_whitelabels'); }
};
