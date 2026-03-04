@extends('layouts.app')

@section('title', 'All Attendance Records')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">All Attendance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage attendance for all employees</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leaves.admin.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Leave Requests
            </a>
            <a href="{{ route('holidays.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Holidays
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-5">
        <form method="GET" action="{{ route('attendance.admin.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Employee</label>
                <select name="user_id" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Employees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Month</label>
                <select name="month" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Year</label>
                <select name="year" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Status</option>
                    @foreach(['present','absent','half_day','on_leave','holiday','incomplete'] as $s)
                        <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg">Filter</button>
            <a href="{{ route('attendance.admin.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Clear</a>
        </form>
    </div>

    {{-- Records Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Login</th>
                        <th class="px-4 py-3">Break</th>
                        <th class="px-4 py-3">Logout</th>
                        <th class="px-4 py-3">Net Hours</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('attendance.admin.show', $record->user) }}" class="font-medium text-primary-600 hover:underline">
                                    {{ $record->user->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $record->date->format('D, d M Y') }}</td>
                            <td class="px-4 py-3 font-mono text-green-600 dark:text-green-400">
                                {{ $record->login_time ? \Carbon\Carbon::createFromTimeString($record->login_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                @if($record->break_start_time)
                                    {{ \Carbon\Carbon::createFromTimeString($record->break_start_time)->format('h:i') }}
                                    @if($record->break_end_time)
                                        – {{ \Carbon\Carbon::createFromTimeString($record->break_end_time)->format('h:i') }}
                                    @else
                                        <span class="text-yellow-500">ongoing</span>
                                    @endif
                                @else
                                    --
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-red-600 dark:text-red-400">
                                {{ $record->logout_time ? \Carbon\Carbon::createFromTimeString($record->logout_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-gray-100">
                                {{ $record->formatted_total_hours }}
                            </td>
                            <td class="px-4 py-3">{!! $record->status_badge !!}</td>
                            <td class="px-4 py-3">
                                @can('manage-attendance')
                                <a href="{{ route('attendance.edit', $record) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
