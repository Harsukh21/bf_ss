@extends('layouts.app')
@section('title', 'Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Success Toast --}}
    @if(session('success'))
    <div id="successToast" class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-lg bg-green-600 text-white shadow-lg min-w-[260px] opacity-0 -translate-y-2 transition-all duration-200" style="opacity:1;transform:translateY(0)">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span class="flex-1 text-sm">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">×</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Attendance</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage daily attendance records for all employees</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="toggleFilters()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filters
            </button>
            <a href="{{ route('emp-attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Attendance
            </a>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalToday   = \App\Models\Attendance::whereDate('date', today())->count();
        $presentToday = \App\Models\Attendance::whereDate('date', today())->where('status', 'present')->count();
        $absentToday  = \App\Models\Attendance::whereDate('date', today())->where('status', 'absent')->count();
        $leaveToday   = \App\Models\Attendance::whereDate('date', today())->whereIn('status', ['on_leave','half_day'])->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Today's Records</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalToday }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Present Today</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $presentToday }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Absent Today</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $absentToday }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">On Leave Today</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $leaveToday }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div id="filterPanel" class="{{ request()->hasAny(['employee_id','status','date_from','date_to']) ? '' : 'hidden' }} mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('emp-attendance.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Employee</label>
                    <select name="employee_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Statuses</option>
                        @foreach(['present'=>'Present','absent'=>'Absent','half_day'=>'Half Day','late'=>'Late','on_leave'=>'On Leave','holiday'=>'Holiday'] as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3 justify-end">
                <a href="{{ route('emp-attendance.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Clear</a>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Apply Filters</button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @if($attendances->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Start</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">End</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Break</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Working Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($attendances as $att)
                    @php
                        $sc = [
                            'present'  => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300',
                            'absent'   => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
                            'half_day' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                            'late'     => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
                            'on_leave' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                            'holiday'  => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300',
                        ][$att->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center text-primary-700 dark:text-primary-300 font-semibold text-xs flex-shrink-0">
                                    {{ $att->employee->initials ?? '?' }}
                                </div>
                                <div>
                                    <a href="{{ route('emp-attendance.show', $att) }}" class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $att->employee->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $att->employee->employee_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                            {{ $att->date->format('d M Y') }}<br>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $att->date->format('D') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                            {{ $att->start_time ? \Carbon\Carbon::parse($att->start_time)->format('h:i A') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                            {{ $att->end_time ? \Carbon\Carbon::parse($att->end_time)->format('h:i A') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            @if($att->start_break_time && $att->end_break_time)
                                {{ \Carbon\Carbon::parse($att->start_break_time)->format('h:i A') }} –
                                {{ \Carbon\Carbon::parse($att->end_break_time)->format('h:i A') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                            {{ $att->working_time }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                                {{ $att->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('emp-attendance.show', $att) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('emp-attendance.edit', $att) }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('emp-attendance.destroy', $att) }}" method="POST" class="inline" onsubmit="return confirm('Delete this attendance record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 dark:hover:text-red-400" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $attendances->links() }}
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">No attendance records found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Start by adding an attendance record.</p>
            <a href="{{ route('emp-attendance.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Attendance
            </a>
        </div>
        @endif
    </div>

</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('select[name="employee_id"]').select2({
        placeholder: 'All Employees',
        allowClear: true,
        width: '100%',
    });
});
function toggleFilters() {
    document.getElementById('filterPanel').classList.toggle('hidden');
}
setTimeout(() => {
    const t = document.getElementById('successToast');
    if (t) { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }
}, 5000);
</script>
@endpush

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single{border:1px solid #d1d5db;border-radius:.5rem;height:38px;background-color:#fff}
.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px;padding-left:12px;font-size:.875rem;color:#111827}
.select2-container--default .select2-selection--single .select2-selection__arrow{height:36px}
.dark .select2-container--default .select2-selection--single{background-color:#374151;border-color:#4b5563}
.dark .select2-container--default .select2-selection--single .select2-selection__rendered{color:#f3f4f6}
.dark .select2-dropdown{background-color:#1f2937;border-color:#4b5563}
.dark .select2-container--default .select2-results__option{color:#f3f4f6}
.dark .select2-container--default .select2-results__option--highlighted[aria-selected]{background-color:#4f46e5}
.dark .select2-search--dropdown .select2-search__field{background-color:#374151;border-color:#4b5563;color:#f3f4f6}
</style>
@endpush
@endsection
