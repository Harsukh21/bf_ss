@extends('layouts.app')

@section('title', $user->name . ' – Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('attendance.admin.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}'s Attendance</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400 ml-8">{{ $user->email }}</p>
        </div>
    </div>

    {{-- Monthly Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $monthlyStats['present'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Present</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $monthlyStats['absent'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Absent</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $monthlyStats['half_day'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Half Day</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $monthlyStats['on_leave'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">On Leave</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-primary-600">{{ $monthlyStats['total_hours'] }}h</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Hours</p>
        </div>
    </div>

    {{-- Month Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <form method="GET" action="{{ route('attendance.admin.show', $user) }}" class="flex flex-wrap items-center gap-3">
            <select name="month" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
            <select name="year" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @foreach(range(now()->year - 1, now()->year + 1) as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg">View</button>
        </form>
    </div>

    {{-- Records --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Login</th>
                        <th class="px-4 py-3">Break Start</th>
                        <th class="px-4 py-3">Break End</th>
                        <th class="px-4 py-3">Logout</th>
                        <th class="px-4 py-3">Break</th>
                        <th class="px-4 py-3">Net Hours</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $record->date->format('D, d M') }}</td>
                            <td class="px-4 py-3 font-mono text-green-600 dark:text-green-400">{{ $record->login_time ? \Carbon\Carbon::createFromTimeString($record->login_time)->format('h:i A') : '--' }}</td>
                            <td class="px-4 py-3 font-mono text-yellow-600 dark:text-yellow-400">{{ $record->break_start_time ? \Carbon\Carbon::createFromTimeString($record->break_start_time)->format('h:i A') : '--' }}</td>
                            <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">{{ $record->break_end_time ? \Carbon\Carbon::createFromTimeString($record->break_end_time)->format('h:i A') : '--' }}</td>
                            <td class="px-4 py-3 font-mono text-red-600 dark:text-red-400">{{ $record->logout_time ? \Carbon\Carbon::createFromTimeString($record->logout_time)->format('h:i A') : '--' }}</td>
                            <td class="px-4 py-3 text-gray-500 font-mono">{{ $record->break_duration ? number_format($record->break_duration, 1) . 'h' : '--' }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $record->formatted_total_hours }}</td>
                            <td class="px-4 py-3">{!! $record->status_badge !!}</td>
                            <td class="px-4 py-3">
                                @can('manage-attendance')
                                <a href="{{ route('attendance.edit', $record) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No records found for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
