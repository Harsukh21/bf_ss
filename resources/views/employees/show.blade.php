@extends('layouts.app')
@section('title', $employee->name . ' — Employee Profile')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $employee->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->employee_id }}
                @if($employee->designation) · {{ $employee->designation }}@endif
                @if($employee->department) · {{ $employee->department }}@endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.edit', $employee) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <a href="{{ route('employees.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Main Sections -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-5">
                    @if($employee->photo_link)
                        <img src="{{ $employee->photo_link }}" alt="{{ $employee->name }}"
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 flex-shrink-0">
                    @else
                        <div class="w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 text-2xl font-bold flex-shrink-0">
                            {{ $employee->initials }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $employee->name }}</h2>
                            @php
                                $sc = $employee->status_color;
                                $colorMap = [
                                    'green'  => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
                                    'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                                    'gray'   => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                    'red'    => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colorMap[$sc] ?? $colorMap['gray'] }}">
                                {{ $employee->status_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                {{ $employee->employment_type_label }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                        @if($employee->designation || $employee->department)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            @if($employee->designation)<span class="font-medium">{{ $employee->designation }}</span>@endif
                            @if($employee->designation && $employee->department) · @endif
                            @if($employee->department){{ $employee->department }}@endif
                        </p>
                        @endif
                        @if($employee->joining_date)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Joined {{ $employee->joining_date->format('M j, Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Basic Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Employee ID', 'value' => $employee->employee_id])
                    @include('employees.partials.detail-row', ['label' => 'Username', 'value' => $employee->username])
                    @include('employees.partials.detail-row', ['label' => 'Date of Birth', 'value' => $employee->date_of_birth?->format('M j, Y')])
                    @include('employees.partials.detail-row', ['label' => 'Gender', 'value' => $employee->gender ? ucfirst($employee->gender) : null])
                    @include('employees.partials.detail-row', ['label' => 'Marital Status', 'value' => $employee->marital_status ? ucfirst($employee->marital_status) : null])
                    @include('employees.partials.detail-row', ['label' => 'Blood Group', 'value' => $employee->blood_group])
                    <div class="sm:col-span-2">
                        @include('employees.partials.detail-row', ['label' => 'Address', 'value' => $employee->address])
                    </div>
                </dl>
            </div>

            <!-- Job Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Job Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Department', 'value' => $employee->department])
                    @include('employees.partials.detail-row', ['label' => 'Designation', 'value' => $employee->designation])
                    @include('employees.partials.detail-row', ['label' => 'Employment Type', 'value' => $employee->employment_type_label])
                    @include('employees.partials.detail-row', ['label' => 'Work Location', 'value' => $employee->work_location])
                    @include('employees.partials.detail-row', ['label' => 'Joining Date', 'value' => $employee->joining_date?->format('M j, Y')])
                    @include('employees.partials.detail-row', ['label' => 'Probation Period', 'value' => $employee->probation_period])
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Reporting Manager</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($employee->reporting_manager_name)
                                {{ $employee->reporting_manager_name }}
                            @elseif($employee->reportingManager)
                                <a href="{{ route('employees.show', $employee->reportingManager) }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $employee->reportingManager->name }}
                                    <span class="text-gray-400 text-xs">({{ $employee->reportingManager->employee_id }})</span>
                                </a>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Bank & Pay -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Bank & Pay</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Bank Name', 'value' => $employee->bank_name])
                    @include('employees.partials.detail-row', ['label' => 'Account Holder', 'value' => $employee->account_holder_name])
                    @include('employees.partials.detail-row', ['label' => 'Account Number', 'value' => $employee->bank_account_number])
                    @include('employees.partials.detail-row', ['label' => 'IFSC Code', 'value' => $employee->ifsc_code])
                    @include('employees.partials.detail-row', ['label' => 'UPI ID', 'value' => $employee->upi_id])
                    @include('employees.partials.detail-row', ['label' => 'Payment Method', 'value' => $employee->salary_payment_method ? ucwords(str_replace('-', ' ', $employee->salary_payment_method)) : null])
                    @if($employee->bank_account_proof_link)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bank Proof</dt>
                        <dd class="mt-1"><a href="{{ $employee->bank_account_proof_link }}" target="_blank" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">View Document ↗</a></dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Emergency Contact</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Name', 'value' => $employee->emergency_contact_name])
                    @include('employees.partials.detail-row', ['label' => 'Relation', 'value' => $employee->emergency_contact_relation])
                    @include('employees.partials.detail-row', ['label' => 'Phone', 'value' => $employee->emergency_contact_number])
                </dl>
            </div>

            <!-- Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Documents</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Aadhar Number', 'value' => $employee->aadhar_number])
                    @include('employees.partials.detail-row', ['label' => 'PAN Number', 'value' => $employee->pan_number])

                    @foreach([
                        ['label' => 'Photo', 'link' => $employee->photo_link],
                        ['label' => 'Address Proof', 'link' => $employee->address_proof_link],
                        ['label' => 'Aadhar Proof', 'link' => $employee->aadhar_proof_link],
                        ['label' => 'PAN Proof', 'link' => $employee->pan_proof_link],
                        ['label' => 'Resume', 'link' => $employee->resume_link],
                    ] as $doc)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $doc['label'] }}</dt>
                        <dd class="mt-1">
                            @if($doc['link'])
                                <a href="{{ $doc['link'] }}" target="_blank" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">View ↗</a>
                            @else
                                <span class="text-sm text-gray-400 italic">—</span>
                            @endif
                        </dd>
                    </div>
                    @endforeach

                    @if($employee->education_certificates)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Education Certificates</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $employee->education_certificates }}</dd>
                    </div>
                    @endif

                    @if($employee->experience_certificates)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Experience Certificates</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $employee->experience_certificates }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Device & System -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Device & System</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @include('employees.partials.detail-row', ['label' => 'Device Type', 'value' => $employee->device_type])
                    @include('employees.partials.detail-row', ['label' => 'Brand', 'value' => $employee->device_brand])
                    @include('employees.partials.detail-row', ['label' => 'Model', 'value' => $employee->device_model])
                    @include('employees.partials.detail-row', ['label' => 'Serial Number', 'value' => $employee->serial_number])
                    @include('employees.partials.detail-row', ['label' => 'IMEI Number', 'value' => $employee->imei_number])
                    @include('employees.partials.detail-row', ['label' => 'Operating System', 'value' => $employee->operating_system])
                    @include('employees.partials.detail-row', ['label' => 'RAM', 'value' => $employee->ram])
                    @include('employees.partials.detail-row', ['label' => 'Storage', 'value' => $employee->storage])
                    @include('employees.partials.detail-row', ['label' => 'Processor', 'value' => $employee->processor])
                    @include('employees.partials.detail-row', ['label' => 'Device Location', 'value' => $employee->device_location])
                    @include('employees.partials.detail-row', ['label' => 'Asset ID', 'value' => $employee->company_asset_id])
                    @include('employees.partials.detail-row', ['label' => 'Assigned Date', 'value' => $employee->device_assigned_date?->format('M j, Y')])
                    @include('employees.partials.detail-row', ['label' => 'Device Condition', 'value' => $employee->device_condition ? ucfirst($employee->device_condition) : null])
                    @include('employees.partials.detail-row', ['label' => 'Login Email', 'value' => $employee->device_login_email])
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antivirus</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $employee->antivirus_installed ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $employee->antivirus_installed ? 'Installed' : 'Not installed' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Remote Access</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $employee->remote_access_enabled ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $employee->remote_access_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </dd>
                    </div>
                    @include('employees.partials.detail-row', ['label' => 'MAC Address', 'value' => $employee->mac_address])
                    @include('employees.partials.detail-row', ['label' => 'IP Address', 'value' => $employee->ip_address])

                    @if($employee->installed_work_apps)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Installed Work Apps</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $employee->installed_work_apps }}</dd>
                    </div>
                    @endif

                    @if($employee->extra_devices_details)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Extra Device Details</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $employee->extra_devices_details }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($employee->additional_note)
            <!-- Additional Note -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-3">Additional Note</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $employee->additional_note }}</p>
            </div>
            @endif

        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('employees.edit', $employee) }}"
                       class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Employee
                    </a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                          onsubmit="return confirm('Delete {{ addslashes($employee->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Employee
                        </button>
                    </form>
                </div>
            </div>

            <!-- Employment Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Employment Summary</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-0.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colorMap[$sc] ?? $colorMap['gray'] }}">
                                {{ $employee->status_label }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 dark:text-gray-100">{{ $employee->employment_type_label }}</dd>
                    </div>
                    @if($employee->joining_date)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Joining Date</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 dark:text-gray-100">{{ $employee->joining_date->format('M j, Y') }}</dd>
                    </div>
                    @endif
                    @if($employee->work_location)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Work Location</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 dark:text-gray-100">{{ $employee->work_location }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Record Created</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 dark:text-gray-100">{{ $employee->created_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Subordinates -->
            @if($employee->subordinates->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Direct Reports <span class="text-gray-400 font-normal">({{ $employee->subordinates->count() }})</span>
                </h3>
                <ul class="space-y-2">
                    @foreach($employee->subordinates as $sub)
                    <li>
                        <a href="{{ route('employees.show', $sub) }}" class="flex items-center gap-2 text-sm hover:text-primary-600 dark:hover:text-primary-400">
                            <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-semibold text-gray-600 dark:text-gray-300 flex-shrink-0">
                                {{ $sub->initials }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-900 dark:text-gray-100 truncate">{{ $sub->name }}</p>
                                <p class="text-xs text-gray-400">{{ $sub->designation ?? $sub->employee_id }}</p>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
