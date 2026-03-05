@push('css')
<style>
.toast-notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 280px;
    max-width: 420px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05);
    transform: translateX(calc(100% + 1rem));
    transition: transform .3s ease;
}
.toast-notification.show { transform: translateX(0); }
.toast-success { background:#065f46; color:#fff; }
.toast-error   { background:#991b1b; color:#fff; }
</style>
@endpush

@extends('layouts.app')

@section('title', $user->name . ' – Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Toast Notifications --}}
    @if(session('success'))
    <div id="successToast" class="toast-notification toast-success show">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button onclick="this.closest('.toast-notification').classList.remove('show')" class="ml-auto opacity-70 hover:opacity-100">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('attendance.admin.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}'s Attendance</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-8">{{ $user->email }}</p>
        </div>
        @can('manage-attendance')
        <a href="{{ route('attendance.create', ['user_id' => $user->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Attendance
        </a>
        @endcan
    </div>

    {{-- Monthly Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <div class="inline-flex p-2 bg-green-100 dark:bg-green-900/20 rounded-lg mb-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $monthlyStats['present'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Present</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <div class="inline-flex p-2 bg-red-100 dark:bg-red-900/20 rounded-lg mb-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $monthlyStats['absent'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Absent</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <div class="inline-flex p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg mb-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $monthlyStats['half_day'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Half Day</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <div class="inline-flex p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg mb-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $monthlyStats['on_leave'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">On Leave</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <div class="inline-flex p-2 bg-primary-100 dark:bg-primary-900/20 rounded-lg mb-3">
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $monthlyStats['total_hours'] }}h</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Total Hours</p>
        </div>
    </div>

    {{-- Month Filter --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-5">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('attendance.admin.show', $user) }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Month</label>
                    <select name="month" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Year</label>
                    <select name="year" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    View
                </button>
            </form>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Break Start</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Break End</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Logout</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Break</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record->date->format('D, d M') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-green-600 dark:text-green-400">{{ $record->login_time ? \Carbon\Carbon::createFromTimeString($record->login_time)->format('h:i A') : '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-yellow-600 dark:text-yellow-400">{{ $record->break_start_time ? \Carbon\Carbon::createFromTimeString($record->break_start_time)->format('h:i A') : '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-blue-600 dark:text-blue-400">{{ $record->break_end_time ? \Carbon\Carbon::createFromTimeString($record->break_end_time)->format('h:i A') : '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-red-600 dark:text-red-400">{{ $record->logout_time ? \Carbon\Carbon::createFromTimeString($record->logout_time)->format('h:i A') : '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-500 dark:text-gray-400">{{ $record->break_duration ? number_format($record->break_duration, 1) . 'h' : '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $record->formatted_total_hours }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{!! $record->status_badge !!}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @can('manage-attendance')
                                <a href="{{ route('attendance.edit', $record) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400 font-medium">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No records found for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('successToast');
    if (el) setTimeout(function() { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }, 5000);
});
</script>
@endpush
@endsection
