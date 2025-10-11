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
            // Profile fields
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            $table->date('date_of_birth')->nullable()->after('avatar');
            $table->string('timezone')->default('UTC')->after('date_of_birth');
            $table->string('language')->default('en')->after('timezone');
            
            // Security fields
            $table->string('two_factor_secret')->nullable()->after('password');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_confirmed_at');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('last_login_user_agent')->nullable()->after('last_login_ip');
            $table->json('login_history')->nullable()->after('last_login_user_agent');
            $table->timestamp('password_changed_at')->nullable()->after('login_history');
            $table->string('current_session_id')->nullable()->after('password_changed_at');
            $table->json('active_sessions')->nullable()->after('current_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile fields
            $table->dropColumn([
                'first_name', 'last_name', 'phone', 'bio', 'avatar', 
                'date_of_birth', 'timezone', 'language'
            ]);
            
            // Security fields
            $table->dropColumn([
                'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
                'last_login_at', 'last_login_ip', 'last_login_user_agent', 'login_history',
                'password_changed_at', 'current_session_id', 'active_sessions'
            ]);
        });
    }
};
