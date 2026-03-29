<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('label_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
            $table->string('title');
            $table->string('report_type')->nullable();
            $table->text('content')->nullable();
            $table->string('file_link')->nullable();
            $table->date('report_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('label_reports'); }
};
