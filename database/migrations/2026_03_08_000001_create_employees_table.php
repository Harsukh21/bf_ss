<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // ── Basic Information ─────────────────────────────────────────
            $table->string('employee_id')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();

            // ── Job Related ───────────────────────────────────────────────
            $table->date('joining_date')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->unsignedBigInteger('reporting_manager_id')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'intern'])->default('full-time');
            $table->string('work_location')->nullable();
            $table->string('probation_period')->nullable();
            $table->enum('status', ['active', 'on-leave', 'resigned', 'terminated'])->default('active');

            // ── Bank Details ──────────────────────────────────────────────
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->enum('salary_payment_method', ['bank-transfer', 'cash'])->nullable();
            $table->string('bank_account_proof_link')->nullable();

            // ── Emergency Contact ─────────────────────────────────────────
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_number')->nullable();

            // ── Documents ─────────────────────────────────────────────────
            $table->string('photo_link')->nullable();
            $table->string('address_proof_link')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('aadhar_proof_link')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('pan_proof_link')->nullable();
            $table->string('resume_link')->nullable();
            $table->text('education_certificates')->nullable();
            $table->text('experience_certificates')->nullable();

            // ── Basic Device Details ──────────────────────────────────────
            $table->string('device_type')->nullable();
            $table->string('device_brand')->nullable();
            $table->string('device_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('imei_number')->nullable();

            // ── System Information ────────────────────────────────────────
            $table->string('operating_system')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('processor')->nullable();
            $table->text('extra_devices_details')->nullable();
            $table->string('device_location')->nullable();

            // ── Company Tracking ──────────────────────────────────────────
            $table->date('device_assigned_date')->nullable();
            $table->enum('device_condition', ['new', 'good', 'used'])->nullable();
            $table->string('company_asset_id')->nullable();
            $table->text('installed_work_apps')->nullable();

            // ── Security ──────────────────────────────────────────────────
            $table->string('device_login_email')->nullable();
            $table->boolean('antivirus_installed')->default(false);
            $table->boolean('remote_access_enabled')->default(false);

            // ── Optional Advanced ─────────────────────────────────────────
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();

            // ── Additional Note ───────────────────────────────────────────
            $table->text('additional_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reporting_manager_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
