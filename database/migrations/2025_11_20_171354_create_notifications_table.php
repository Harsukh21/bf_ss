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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('notification_type', ['instant', 'after_minutes', 'daily', 'weekly', 'monthly', 'after_hours'])->default('instant');
            $table->integer('duration_value')->nullable(); // For after_minutes, after_hours
            $table->time('daily_time')->nullable(); // For daily notifications
            $table->integer('weekly_day')->nullable(); // 0-6 (Sunday-Saturday)
            $table->time('weekly_time')->nullable(); // For weekly notifications
            $table->integer('monthly_day')->nullable(); // 1-31 (Day of month)
            $table->time('monthly_time')->nullable(); // For monthly notifications
            $table->timestamp('scheduled_at')->nullable(); // Calculated scheduled time
            $table->json('delivery_methods')->default('["push", "telegram", "login_popup"]'); // push, telegram, login_popup
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->unsignedBigInteger('created_by'); // No foreign key constraint
            $table->boolean('requires_web_pin')->default(true); // Require web_pin to close popup
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship between notifications and users
        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id'); // No foreign key constraint
            $table->unsignedBigInteger('user_id'); // No foreign key constraint
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_delivered')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_status')->nullable(); // Track delivery status for each method
            $table->timestamps();
            
            $table->unique(['notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
};
