<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    // File fields → subfolder name under employees/{id}/
    private const FILE_FIELDS = [
        'photo_link'              => 'photo',
        'address_proof_link'      => 'address_proof',
        'aadhar_proof_link'       => 'aadhar_proof',
        'pan_proof_link'          => 'pan_proof',
        'resume_link'             => 'resume',
        'bank_account_proof_link' => 'bank_proof',
    ];

    public function index(Request $request)
    {
        $query = Employee::with('reportingManager');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('employee_id', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('designation', 'like', "%{$s}%")
                  ->orWhere('department', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status'))          $query->where('status', $request->status);
        if ($request->filled('department'))      $query->where('department', $request->department);
        if ($request->filled('employment_type')) $query->where('employment_type', $request->employment_type);
        if ($request->filled('gender'))          $query->where('gender', $request->gender);
        if ($request->filled('joining_from'))    $query->whereDate('joining_date', '>=', $request->joining_from);
        if ($request->filled('joining_to'))      $query->whereDate('joining_date', '<=', $request->joining_to);

        $employees   = $query->latest()->paginate(15)->appends($request->except('page'));
        $departments = Employee::distinct()->whereNotNull('department')->orderBy('department')->pluck('department');

        $stats = [
            'total'    => Employee::count(),
            'active'   => Employee::where('status', 'active')->count(),
            'on_leave' => Employee::where('status', 'on-leave')->count(),
            'resigned' => Employee::whereIn('status', ['resigned', 'terminated'])->count(),
        ];

        return view('employees.index', compact('employees', 'departments', 'stats'));
    }

    public function create()
    {
        $managers       = Employee::where('status', 'active')->orderBy('name')->get();
        $nextEmployeeId = Employee::generateEmployeeId();
        return view('employees.create', compact('managers', 'nextEmployeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['antivirus_installed']   = $request->boolean('antivirus_installed');
        $validated['remote_access_enabled'] = $request->boolean('remote_access_enabled');
        $validated['status']                = $validated['status'] ?? 'active';
        $validated['employment_type']       = $validated['employment_type'] ?? 'full-time';
        $validated['reporting_manager_id']  = null;

        // Handle file uploads — use the employee_id slug as the folder
        $empId = $validated['employee_id'];
        foreach (self::FILE_FIELDS as $field => $subfolder) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("employees/{$empId}/{$subfolder}", 'public');
                $validated[$field] = Storage::url($path);
            } else {
                unset($validated[$field]);
            }
        }

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $employee->load('reportingManager', 'subordinates');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $managers = Employee::where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->orderBy('name')
            ->get();
        return view('employees.edit', compact('employee', 'managers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->rules($employee->id));

        $validated['antivirus_installed']   = $request->boolean('antivirus_installed');
        $validated['remote_access_enabled'] = $request->boolean('remote_access_enabled');
        $validated['reporting_manager_id']  = null;

        $empId = $validated['employee_id'];

        foreach (self::FILE_FIELDS as $field => $subfolder) {
            if ($request->hasFile($field)) {
                // Delete old file if it exists in local storage
                if ($employee->$field && str_starts_with($employee->$field, '/storage/')) {
                    Storage::disk('public')->delete(
                        ltrim(str_replace('/storage', '', $employee->$field), '/')
                    );
                }
                $path = $request->file($field)->store("employees/{$empId}/{$subfolder}", 'public');
                $validated[$field] = Storage::url($path);
            } else {
                // Keep existing value — remove from update data so it's not overwritten
                unset($validated[$field]);
            }
        }

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }

    // ── Shared validation rules ─────────────────────────────────────────────

    private function rules(?int $ignoreId = null): array
    {
        $unique = fn(string $col) => 'nullable|string|max:100|unique:employees,' . $col . ($ignoreId ? ",{$ignoreId}" : '');

        return [
            // Basic
            'employee_id'               => 'required|string|max:50|unique:employees,employee_id' . ($ignoreId ? ",{$ignoreId}" : ''),
            'email'                     => 'required|email|max:255|unique:employees,email' . ($ignoreId ? ",{$ignoreId}" : ''),
            'name'                      => 'required|string|max:255',
            'username'                  => $unique('username'),
            'date_of_birth'             => 'nullable|date|before:today',
            'gender'                    => 'nullable|in:male,female,other',
            'marital_status'            => 'nullable|in:single,married,divorced,widowed',
            'blood_group'               => 'nullable|string|max:10',
            'address'                   => 'nullable|string|max:1000',
            // Job
            'joining_date'              => 'nullable|date',
            'department'                => 'nullable|string|max:100',
            'designation'               => 'nullable|string|max:100',
            'reporting_manager_id'      => 'nullable|exists:employees,id',
            'reporting_manager_name'    => 'nullable|string|max:255',
            'employment_type'           => 'nullable|in:full-time,part-time,contract,intern',
            'work_location'             => 'nullable|string|max:255',
            'probation_period'          => 'nullable|string|max:100',
            'status'                    => 'nullable|in:active,on-leave,resigned,terminated',
            // Bank
            'bank_name'                 => 'nullable|string|max:255',
            'account_holder_name'       => 'nullable|string|max:255',
            'bank_account_number'       => 'nullable|string|max:100',
            'ifsc_code'                 => 'nullable|string|max:20',
            'upi_id'                    => 'nullable|string|max:100',
            'salary_payment_method'     => 'nullable|in:bank-transfer,cash',
            'bank_account_proof_link'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            // Emergency
            'emergency_contact_name'    => 'nullable|string|max:255',
            'emergency_contact_relation'=> 'nullable|string|max:100',
            'emergency_contact_number'  => 'nullable|string|max:20',
            // Documents
            'photo_link'                => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address_proof_link'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_number'             => 'nullable|string|max:20',
            'aadhar_proof_link'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pan_number'                => 'nullable|string|max:20',
            'pan_proof_link'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'resume_link'               => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'education_certificates'    => 'nullable|string|max:2000',
            'experience_certificates'   => 'nullable|string|max:2000',
            // Device
            'device_type'               => 'nullable|string|max:50',
            'device_brand'              => 'nullable|string|max:100',
            'device_model'              => 'nullable|string|max:100',
            'serial_number'             => 'nullable|string|max:100',
            'imei_number'               => 'nullable|string|max:50',
            // System
            'operating_system'          => 'nullable|string|max:100',
            'ram'                       => 'nullable|string|max:50',
            'storage'                   => 'nullable|string|max:50',
            'processor'                 => 'nullable|string|max:100',
            'extra_devices_details'     => 'nullable|string|max:1000',
            'device_location'           => 'nullable|string|max:255',
            // Company Tracking
            'device_assigned_date'      => 'nullable|date',
            'device_condition'          => 'nullable|in:new,good,used',
            'company_asset_id'          => 'nullable|string|max:100',
            'installed_work_apps'       => 'nullable|string|max:1000',
            // Security
            'device_login_email'        => 'nullable|email|max:255',
            'antivirus_installed'       => 'nullable|boolean',
            'remote_access_enabled'     => 'nullable|boolean',
            // Advanced
            'mac_address'               => 'nullable|string|max:50',
            'ip_address'                => 'nullable|ip|max:50',
            // Note
            'additional_note'           => 'nullable|string|max:3000',
        ];
    }
}
