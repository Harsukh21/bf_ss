{{-- Tabbed form partial — used by create.blade.php and edit.blade.php --}}
{{-- Variables expected:
     $employee      — Employee model (null on create)
     $managers      — Collection of employees for manager dropdown
     $nextEmployeeId — Auto-generated ID (create only)
     $submitLabel   — Button label: 'Create Employee' / 'Save Changes'
     $cancelUrl     — URL for Cancel link
     $deleteLabel   — Optional: label for delete button (edit only)
     $deleteConfirm — Optional: confirmation message for delete (edit only)
--}}

@php
$e           = $employee ?? null;
$submitLabel = $submitLabel ?? 'Submit';
$cancelUrl   = $cancelUrl ?? route('employees.index');
$tabIds      = ['basic', 'job', 'bank', 'emergency', 'documents', 'device', 'note'];
$lastTab     = 'note';

$inp    = 'mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 text-sm';
$inpErr = 'mt-1 block w-full px-3 py-2 border border-red-500 dark:border-red-500 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-gray-100 text-sm';
$lbl    = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
@endphp

<!-- ── Tab Nav ─────────────────────────────────── -->
<div class="border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
    <nav class="flex -mb-px min-w-max" id="empTabNav">
        @php
        $tabDefs = [
            ['id' => 'basic',     'label' => 'Basic Info'],
            ['id' => 'job',       'label' => 'Job Details'],
            ['id' => 'bank',      'label' => 'Bank & Pay'],
            ['id' => 'emergency', 'label' => 'Emergency'],
            ['id' => 'documents', 'label' => 'Documents'],
            ['id' => 'device',    'label' => 'Device'],
            ['id' => 'note',      'label' => 'Note'],
        ];
        @endphp
        @foreach($tabDefs as $tab)
        <button type="button"
            class="emp-tab-btn px-5 py-3 text-sm border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 whitespace-nowrap transition-colors"
            data-tab="{{ $tab['id'] }}">
            {{ $tab['label'] }}
        </button>
        @endforeach
    </nav>
</div>

<!-- ══════════════════════════════════════════
     TAB 1 — Basic Information
══════════════════════════════════════════ -->
<div id="tab-basic" class="emp-tab-panel">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Basic Information</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Employee ID <span class="text-red-500">*</span></label>
            <input type="text" name="employee_id"
                value="{{ old('employee_id', $e?->employee_id ?? $nextEmployeeId ?? '') }}"
                class="{{ $errors->has('employee_id') ? $inpErr : $inp }}"
                required placeholder="EMP-0001">
            @error('employee_id')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Email ID <span class="text-red-500">*</span></label>
            <input type="email" name="email"
                value="{{ old('email', $e?->email) }}"
                class="{{ $errors->has('email') ? $inpErr : $inp }}"
                required placeholder="employee@company.com">
            @error('email')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name"
                value="{{ old('name', $e?->name) }}"
                class="{{ $errors->has('name') ? $inpErr : $inp }}"
                required placeholder="John Doe">
            @error('name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Username</label>
            <input type="text" name="username"
                value="{{ old('username', $e?->username) }}"
                class="{{ $errors->has('username') ? $inpErr : $inp }}"
                placeholder="johndoe">
            @error('username')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Date of Birth</label>
            <input type="date" name="date_of_birth"
                value="{{ old('date_of_birth', $e?->date_of_birth?->format('Y-m-d')) }}"
                class="{{ $errors->has('date_of_birth') ? $inpErr : $inp }}">
            @error('date_of_birth')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Gender</label>
            <select name="gender" class="{{ $errors->has('gender') ? $inpErr : $inp }}">
                <option value="">Select Gender</option>
                <option value="male"   {{ old('gender', $e?->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $e?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other"  {{ old('gender', $e?->gender) == 'other'  ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Marital Status</label>
            <select name="marital_status" class="{{ $inp }}">
                <option value="">Select</option>
                @foreach(['single', 'married', 'divorced', 'widowed'] as $ms)
                <option value="{{ $ms }}" {{ old('marital_status', $e?->marital_status) == $ms ? 'selected' : '' }}>{{ ucfirst($ms) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Blood Group</label>
            <select name="blood_group" class="{{ $inp }}">
                <option value="">Select</option>
                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                <option value="{{ $bg }}" {{ old('blood_group', $e?->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-4">
        <label class="{{ $lbl }}">Address</label>
        <textarea name="address" rows="3"
            class="{{ $errors->has('address') ? $inpErr : $inp }}"
            placeholder="Full address...">{{ old('address', $e?->address) }}</textarea>
        @error('address')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    <!-- Tab Navigation -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end">
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="basic">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 2 — Job Details
══════════════════════════════════════════ -->
<div id="tab-job" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Job Details</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Joining Date</label>
            <input type="date" name="joining_date"
                value="{{ old('joining_date', $e?->joining_date?->format('Y-m-d')) }}"
                class="{{ $inp }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Department</label>
            <input type="text" name="department"
                value="{{ old('department', $e?->department) }}"
                class="{{ $inp }}" placeholder="Engineering, HR, Finance...">
        </div>
        <div>
            <label class="{{ $lbl }}">Designation / Job Title</label>
            <input type="text" name="designation"
                value="{{ old('designation', $e?->designation) }}"
                class="{{ $inp }}" placeholder="Software Engineer, Manager...">
        </div>
        <div>
            <label class="{{ $lbl }}">Reporting Manager</label>
            <select name="reporting_manager_id" class="{{ $inp }}">
                <option value="">None</option>
                @foreach($managers as $mgr)
                <option value="{{ $mgr->id }}" {{ old('reporting_manager_id', $e?->reporting_manager_id) == $mgr->id ? 'selected' : '' }}>
                    {{ $mgr->name }} ({{ $mgr->employee_id }})
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Employment Type</label>
            <select name="employment_type" class="{{ $inp }}">
                <option value="full-time" {{ old('employment_type', $e?->employment_type ?? 'full-time') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                <option value="part-time" {{ old('employment_type', $e?->employment_type) == 'part-time' ? 'selected' : '' }}>Part-time</option>
                <option value="contract"  {{ old('employment_type', $e?->employment_type) == 'contract'  ? 'selected' : '' }}>Contract</option>
                <option value="intern"    {{ old('employment_type', $e?->employment_type) == 'intern'    ? 'selected' : '' }}>Intern</option>
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Work Location / Branch</label>
            <input type="text" name="work_location"
                value="{{ old('work_location', $e?->work_location) }}"
                class="{{ $inp }}" placeholder="Head Office, Remote...">
        </div>
        <div>
            <label class="{{ $lbl }}">Probation Period</label>
            <input type="text" name="probation_period"
                value="{{ old('probation_period', $e?->probation_period) }}"
                class="{{ $inp }}" placeholder="3 months, 6 months...">
        </div>
        <div>
            <label class="{{ $lbl }}">Employee Status</label>
            <select name="status" class="{{ $inp }}">
                <option value="active"     {{ old('status', $e?->status ?? 'active') == 'active'     ? 'selected' : '' }}>Active</option>
                <option value="on-leave"   {{ old('status', $e?->status) == 'on-leave'   ? 'selected' : '' }}>On Leave</option>
                <option value="resigned"   {{ old('status', $e?->status) == 'resigned'   ? 'selected' : '' }}>Resigned</option>
                <option value="terminated" {{ old('status', $e?->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
        <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="job">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Previous
        </button>
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="job">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 3 — Bank & Pay
══════════════════════════════════════════ -->
<div id="tab-bank" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Bank Details</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Bank Name</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $e?->bank_name) }}" class="{{ $inp }}" placeholder="State Bank of India...">
        </div>
        <div>
            <label class="{{ $lbl }}">Account Holder Name</label>
            <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $e?->account_holder_name) }}" class="{{ $inp }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Bank Account Number</label>
            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $e?->bank_account_number) }}" class="{{ $inp }}" placeholder="XXXXXXXXXXXX">
        </div>
        <div>
            <label class="{{ $lbl }}">IFSC Code</label>
            <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $e?->ifsc_code) }}" class="{{ $inp }}" placeholder="SBIN0001234">
        </div>
        <div>
            <label class="{{ $lbl }}">UPI ID</label>
            <input type="text" name="upi_id" value="{{ old('upi_id', $e?->upi_id) }}" class="{{ $inp }}" placeholder="name@upi">
        </div>
        <div>
            <label class="{{ $lbl }}">Salary Payment Method</label>
            <select name="salary_payment_method" class="{{ $inp }}">
                <option value="">Select</option>
                <option value="bank-transfer" {{ old('salary_payment_method', $e?->salary_payment_method) == 'bank-transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="cash"          {{ old('salary_payment_method', $e?->salary_payment_method) == 'cash'          ? 'selected' : '' }}>Cash</option>
            </select>
        </div>
    </div>
    <div class="mt-4">
        @include('employees.partials.file-field', [
            'name'    => 'bank_account_proof_link',
            'label'   => 'Bank Account Proof',
            'accept'  => '.jpg,.jpeg,.png,.pdf',
            'hint'    => 'JPG, PNG or PDF — max 5MB',
            'current' => $e?->bank_account_proof_link,
        ])
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
        <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="bank">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Previous
        </button>
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="bank">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 4 — Emergency Contact
══════════════════════════════════════════ -->
<div id="tab-emergency" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Emergency Contact</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Contact Name</label>
            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $e?->emergency_contact_name) }}" class="{{ $inp }}" placeholder="Jane Doe">
        </div>
        <div>
            <label class="{{ $lbl }}">Relation</label>
            <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $e?->emergency_contact_relation) }}" class="{{ $inp }}" placeholder="Spouse, Parent, Sibling...">
        </div>
        <div>
            <label class="{{ $lbl }}">Contact Number</label>
            <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number', $e?->emergency_contact_number) }}" class="{{ $inp }}" placeholder="+91 9876543210">
        </div>
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
        <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="emergency">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Previous
        </button>
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="emergency">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 5 — Documents
══════════════════════════════════════════ -->
<div id="tab-documents" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Documents</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Photo --}}
        <div class="sm:col-span-2">
            @include('employees.partials.file-field', [
                'name'    => 'photo_link',
                'label'   => 'Employee Photo',
                'accept'  => '.jpg,.jpeg,.png,.webp',
                'hint'    => 'JPG, PNG or WebP — max 2MB',
                'current' => $e?->photo_link,
                'isImage' => true,
            ])
        </div>
        {{-- Aadhar --}}
        <div>
            <label class="{{ $lbl }}">Aadhar Number</label>
            <input type="text" name="aadhar_number" value="{{ old('aadhar_number', $e?->aadhar_number) }}" class="{{ $inp }}" placeholder="XXXX XXXX XXXX">
        </div>
        @include('employees.partials.file-field', [
            'name'    => 'aadhar_proof_link',
            'label'   => 'Aadhar Proof',
            'accept'  => '.jpg,.jpeg,.png,.pdf',
            'hint'    => 'JPG, PNG or PDF — max 5MB',
            'current' => $e?->aadhar_proof_link,
        ])
        {{-- PAN --}}
        <div>
            <label class="{{ $lbl }}">PAN Number</label>
            <input type="text" name="pan_number" value="{{ old('pan_number', $e?->pan_number) }}" class="{{ $inp }}" placeholder="ABCDE1234F">
        </div>
        @include('employees.partials.file-field', [
            'name'    => 'pan_proof_link',
            'label'   => 'PAN Proof',
            'accept'  => '.jpg,.jpeg,.png,.pdf',
            'hint'    => 'JPG, PNG or PDF — max 5MB',
            'current' => $e?->pan_proof_link,
        ])
        {{-- Address Proof --}}
        @include('employees.partials.file-field', [
            'name'    => 'address_proof_link',
            'label'   => 'Address Proof',
            'accept'  => '.jpg,.jpeg,.png,.pdf',
            'hint'    => 'JPG, PNG or PDF — max 5MB',
            'current' => $e?->address_proof_link,
        ])
        {{-- Resume --}}
        @include('employees.partials.file-field', [
            'name'    => 'resume_link',
            'label'   => 'Resume / CV',
            'accept'  => '.jpg,.jpeg,.png,.pdf,.doc,.docx',
            'hint'    => 'PDF, DOC or Image — max 5MB',
            'current' => $e?->resume_link,
        ])
    </div>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Education Certificates <span class="text-xs font-normal text-gray-400">(links, one per line)</span></label>
            <textarea name="education_certificates" rows="3" class="{{ $inp }}" placeholder="https://drive.google.com/...">{{ old('education_certificates', $e?->education_certificates) }}</textarea>
        </div>
        <div>
            <label class="{{ $lbl }}">Experience Certificates <span class="text-xs font-normal text-gray-400">(links, one per line)</span></label>
            <textarea name="experience_certificates" rows="3" class="{{ $inp }}" placeholder="https://drive.google.com/...">{{ old('experience_certificates', $e?->experience_certificates) }}</textarea>
        </div>
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
        <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="documents">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Previous
        </button>
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="documents">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 6 — Device & System
══════════════════════════════════════════ -->
<div id="tab-device" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Basic Device Details</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Device Type</label>
            <select name="device_type" class="{{ $inp }}">
                <option value="">Select</option>
                @foreach(['Laptop', 'Desktop', 'Mobile', 'Tablet'] as $dt)
                <option value="{{ $dt }}" {{ old('device_type', $e?->device_type) == $dt ? 'selected' : '' }}>{{ $dt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Device Brand</label>
            <input type="text" name="device_brand" value="{{ old('device_brand', $e?->device_brand) }}" class="{{ $inp }}" placeholder="Dell, HP, Apple, Samsung...">
        </div>
        <div>
            <label class="{{ $lbl }}">Device Model</label>
            <input type="text" name="device_model" value="{{ old('device_model', $e?->device_model) }}" class="{{ $inp }}" placeholder="MacBook Pro 14, XPS 15...">
        </div>
        <div>
            <label class="{{ $lbl }}">Serial Number</label>
            <input type="text" name="serial_number" value="{{ old('serial_number', $e?->serial_number) }}" class="{{ $inp }}">
        </div>
        <div>
            <label class="{{ $lbl }}">IMEI Number</label>
            <input type="text" name="imei_number" value="{{ old('imei_number', $e?->imei_number) }}" class="{{ $inp }}">
        </div>
    </div>

    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mt-6 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">System Information</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Operating System</label>
            <input type="text" name="operating_system" value="{{ old('operating_system', $e?->operating_system) }}" class="{{ $inp }}" placeholder="Windows 11, macOS Sonoma...">
        </div>
        <div>
            <label class="{{ $lbl }}">RAM</label>
            <input type="text" name="ram" value="{{ old('ram', $e?->ram) }}" class="{{ $inp }}" placeholder="8GB, 16GB, 32GB...">
        </div>
        <div>
            <label class="{{ $lbl }}">Storage</label>
            <input type="text" name="storage" value="{{ old('storage', $e?->storage) }}" class="{{ $inp }}" placeholder="256GB SSD, 512GB...">
        </div>
        <div>
            <label class="{{ $lbl }}">Processor</label>
            <input type="text" name="processor" value="{{ old('processor', $e?->processor) }}" class="{{ $inp }}" placeholder="Intel i7-12th Gen, Apple M2...">
        </div>
        <div>
            <label class="{{ $lbl }}">Device Location</label>
            <input type="text" name="device_location" value="{{ old('device_location', $e?->device_location) }}" class="{{ $inp }}" placeholder="Office, Remote, Warehouse...">
        </div>
    </div>
    <div class="mt-4">
        <label class="{{ $lbl }}">Extra Devices Details</label>
        <textarea name="extra_devices_details" rows="2" class="{{ $inp }}" placeholder="Mouse, keyboard, monitor specs...">{{ old('extra_devices_details', $e?->extra_devices_details) }}</textarea>
    </div>

    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mt-6 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Company Tracking</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Device Assigned Date</label>
            <input type="date" name="device_assigned_date" value="{{ old('device_assigned_date', $e?->device_assigned_date?->format('Y-m-d')) }}" class="{{ $inp }}">
        </div>
        <div>
            <label class="{{ $lbl }}">Device Condition</label>
            <select name="device_condition" class="{{ $inp }}">
                <option value="">Select</option>
                <option value="new"  {{ old('device_condition', $e?->device_condition) == 'new'  ? 'selected' : '' }}>New</option>
                <option value="good" {{ old('device_condition', $e?->device_condition) == 'good' ? 'selected' : '' }}>Good</option>
                <option value="used" {{ old('device_condition', $e?->device_condition) == 'used' ? 'selected' : '' }}>Used</option>
            </select>
        </div>
        <div>
            <label class="{{ $lbl }}">Company Asset ID</label>
            <input type="text" name="company_asset_id" value="{{ old('company_asset_id', $e?->company_asset_id) }}" class="{{ $inp }}" placeholder="ASSET-001">
        </div>
    </div>
    <div class="mt-4">
        <label class="{{ $lbl }}">Installed Work Apps</label>
        <textarea name="installed_work_apps" rows="2" class="{{ $inp }}" placeholder="Slack, Zoom, VS Code, Jira...">{{ old('installed_work_apps', $e?->installed_work_apps) }}</textarea>
    </div>

    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mt-6 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Security</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $lbl }}">Device Login Email / User ID</label>
            <input type="email" name="device_login_email" value="{{ old('device_login_email', $e?->device_login_email) }}" class="{{ $inp }}" placeholder="user@company.com">
        </div>
        <div>
            <label class="{{ $lbl }}">MAC Address</label>
            <input type="text" name="mac_address" value="{{ old('mac_address', $e?->mac_address) }}" class="{{ $inp }}" placeholder="AA:BB:CC:DD:EE:FF">
        </div>
        <div>
            <label class="{{ $lbl }}">IP Address (Office Device)</label>
            <input type="text" name="ip_address" value="{{ old('ip_address', $e?->ip_address) }}" class="{{ $inp }}" placeholder="192.168.1.100">
        </div>
    </div>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <input type="hidden" name="antivirus_installed" value="0">
            <input type="checkbox" name="antivirus_installed" value="1" id="antivirus_installed"
                class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500"
                {{ old('antivirus_installed', $e?->antivirus_installed) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Antivirus Installed</span>
        </label>
        <label class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <input type="hidden" name="remote_access_enabled" value="0">
            <input type="checkbox" name="remote_access_enabled" value="1" id="remote_access_enabled"
                class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500"
                {{ old('remote_access_enabled', $e?->remote_access_enabled) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Remote Access Enabled</span>
        </label>
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
        <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="device">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Previous
        </button>
        <button type="button" class="emp-nav-next inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg" data-current="device">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TAB 7 — Additional Note  (LAST TAB — submit here)
══════════════════════════════════════════ -->
<div id="tab-note" class="emp-tab-panel hidden">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Additional Note</h3>
    <textarea name="additional_note" rows="8"
        class="{{ $inp }}"
        placeholder="Any additional information about this employee...">{{ old('additional_note', $e?->additional_note) }}</textarea>

    <!-- Final Actions -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <!-- Left: Delete (edit only) -->
        <div>
            @if(isset($deleteLabel) && isset($deleteConfirm))
            <button type="button"
                onclick="if(confirm('{{ $deleteConfirm }}')) document.getElementById('emp-delete-form').submit()"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ $deleteLabel }}
            </button>
            @else
            <div></div>
            @endif
        </div>
        <!-- Right: Prev + Cancel + Submit -->
        <div class="flex items-center gap-3">
            <button type="button" class="emp-nav-prev inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-current="note">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Previous
            </button>
            <a href="{{ $cancelUrl }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</div>

@push('js')
<script>
(function () {
    var tabIds = ['basic', 'job', 'bank', 'emergency', 'documents', 'device', 'note'];
    var nav    = document.getElementById('empTabNav');
    var btns   = nav.querySelectorAll('.emp-tab-btn');

    function activateTab(id) {
        btns.forEach(function (b) {
            var on = b.dataset.tab === id;
            b.classList.toggle('border-primary-600', on);
            b.classList.toggle('dark:border-primary-400', on);
            b.classList.toggle('text-primary-600', on);
            b.classList.toggle('dark:text-primary-400', on);
            b.classList.toggle('font-semibold', on);
            b.classList.toggle('border-transparent', !on);
            b.classList.toggle('text-gray-500', !on);
            b.classList.toggle('dark:text-gray-400', !on);
        });
        document.querySelectorAll('.emp-tab-panel').forEach(function (p) {
            p.classList.toggle('hidden', p.id !== 'tab-' + id);
        });
        try { localStorage.setItem('emp_active_tab', id); } catch (e) {}
    }

    // Top-nav clicks
    btns.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(btn.dataset.tab); });
    });

    // Next buttons
    document.querySelectorAll('.emp-nav-next').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var idx = tabIds.indexOf(btn.dataset.current);
            if (idx < tabIds.length - 1) activateTab(tabIds[idx + 1]);
        });
    });

    // Prev buttons
    document.querySelectorAll('.emp-nav-prev').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var idx = tabIds.indexOf(btn.dataset.current);
            if (idx > 0) activateTab(tabIds[idx - 1]);
        });
    });

    // On load: jump to tab with error, or restore saved tab
    var errPanel = [...document.querySelectorAll('.emp-tab-panel')].find(function (p) {
        return p.querySelector('.border-red-500');
    });
    if (errPanel) { activateTab(errPanel.id.replace('tab-', '')); return; }

    var saved = 'basic';
    try { saved = localStorage.getItem('emp_active_tab') || 'basic'; } catch (e) {}
    var valid = tabIds.indexOf(saved) !== -1;
    activateTab(valid ? saved : 'basic');
})();
</script>
@endpush
