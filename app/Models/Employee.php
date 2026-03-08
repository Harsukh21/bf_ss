<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Basic Information
        'employee_id', 'email', 'name', 'username', 'date_of_birth',
        'gender', 'marital_status', 'blood_group', 'address',
        // Job Related
        'joining_date', 'department', 'designation', 'reporting_manager_id', 'reporting_manager_name',
        'employment_type', 'work_location', 'probation_period', 'status',
        // Bank Details
        'bank_name', 'account_holder_name', 'bank_account_number', 'ifsc_code',
        'upi_id', 'salary_payment_method', 'bank_account_proof_link',
        // Emergency Contact
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_number',
        // Documents
        'photo_link', 'address_proof_link', 'aadhar_number', 'aadhar_proof_link',
        'pan_number', 'pan_proof_link', 'resume_link',
        'education_certificates', 'experience_certificates',
        // Device Details
        'device_type', 'device_brand', 'device_model', 'serial_number', 'imei_number',
        // System Information
        'operating_system', 'ram', 'storage', 'processor',
        'extra_devices_details', 'device_location',
        // Company Tracking
        'device_assigned_date', 'device_condition', 'company_asset_id', 'installed_work_apps',
        // Security
        'device_login_email', 'antivirus_installed', 'remote_access_enabled',
        // Optional Advanced
        'mac_address', 'ip_address',
        // Additional
        'additional_note',
    ];

    protected $casts = [
        'date_of_birth'        => 'date',
        'joining_date'         => 'date',
        'device_assigned_date' => 'date',
        'antivirus_installed'  => 'boolean',
        'remote_access_enabled'=> 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'reporting_manager_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public static function generateEmployeeId(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) ltrim(str_replace('EMP-', '', $last->employee_id), '0') + 1) : 1;
        return 'EMP-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'green',
            'on-leave'   => 'yellow',
            'resigned'   => 'gray',
            'terminated' => 'red',
            default      => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'Active',
            'on-leave'   => 'On Leave',
            'resigned'   => 'Resigned',
            'terminated' => 'Terminated',
            default      => ucfirst($this->status),
        };
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return match ($this->employment_type) {
            'full-time' => 'Full-time',
            'part-time' => 'Part-time',
            'contract'  => 'Contract',
            'intern'    => 'Intern',
            default     => ucfirst($this->employment_type),
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
