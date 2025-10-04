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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->date('date_of_birth')->nullable()->after('last_name');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->string('phone')->nullable()->after('gender');
            
            // Location Information
            $table->string('country')->nullable()->after('phone');
            $table->string('city')->nullable()->after('country');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->text('address')->nullable()->after('postal_code');
            
            // Professional Information
            $table->string('job_title')->nullable()->after('address');
            $table->string('company')->nullable()->after('job_title');
            $table->string('industry')->nullable()->after('company');
            $table->decimal('salary', 10, 2)->nullable()->after('industry');
            
            // Profile Information
            $table->text('bio')->nullable()->after('salary');
            $table->string('website')->nullable()->after('bio');
            $table->string('linkedin_url')->nullable()->after('website');
            $table->string('twitter_handle')->nullable()->after('linkedin_url');
            
            // System Information
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('twitter_handle');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('login_count')->default(0)->after('last_login_ip');
            
            // Preferences
            $table->json('preferences')->nullable()->after('login_count');
            $table->boolean('email_notifications')->default(true)->after('preferences');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            
            // Additional Fields
            $table->string('avatar')->nullable()->after('sms_notifications');
            $table->text('notes')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop all the added columns
            $table->dropColumn([
                'first_name',
                'last_name',
                'date_of_birth',
                'gender',
                'phone',
                'country',
                'city',
                'state',
                'postal_code',
                'address',
                'job_title',
                'company',
                'industry',
                'salary',
                'bio',
                'website',
                'linkedin_url',
                'twitter_handle',
                'status',
                'last_login_at',
                'last_login_ip',
                'login_count',
                'preferences',
                'email_notifications',
                'sms_notifications',
                'avatar',
                'notes'
            ]);
        });
    }
};
