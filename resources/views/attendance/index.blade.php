@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 dark:text-green-400 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-600 dark:text-red-400 hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Attendance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Track your daily work hours and attendance history</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leaves.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                My Leaves
            </a>
            <a href="{{ route('holidays.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                Holidays
            </a>
        </div>
    </div>

    {{-- Today's Attendance Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Today — {{ now()->format('D, d M Y') }}
        </h2>

        {{-- Time Slots --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
            {{-- Login Time --}}
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Login</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">
                    {{ $today && $today->login_time ? \Carbon\Carbon::createFromTimeString($today->login_time)->format('h:i A') : '--:--' }}
                </p>
            </div>
            {{-- Break Start --}}
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Break Start</p>
                <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $today && $today->break_start_time ? \Carbon\Carbon::createFromTimeString($today->break_start_time)->format('h:i A') : '--:--' }}
                </p>
            </div>
            {{-- Break End --}}
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Break End</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $today && $today->break_end_time ? \Carbon\Carbon::createFromTimeString($today->break_end_time)->format('h:i A') : '--:--' }}
                </p>
            </div>
            {{-- Logout --}}
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Logout</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400">
                    {{ $today && $today->logout_time ? \Carbon\Carbon::createFromTimeString($today->logout_time)->format('h:i A') : '--:--' }}
                </p>
            </div>
        </div>

        {{-- Live Timer --}}
        @if($today && $today->login_time && !$today->logout_time)
            <div class="text-center mb-5 py-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Session Duration</p>
                <p id="liveTimer" class="text-3xl font-mono font-bold text-green-600 dark:text-green-400">00:00:00</p>
            </div>
        @elseif($today && $today->total_hours)
            <div class="text-center mb-5 py-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Hours Worked</p>
                <p class="text-3xl font-mono font-bold text-blue-600 dark:text-blue-400">{{ $today->formatted_total_hours }}</p>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3 justify-center">
            {{-- Clock In --}}
            @if(!$today || !$today->login_time)
                <form method="POST" action="{{ route('attendance.clock-in') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Clock In
                    </button>
                </form>
            @endif

            {{-- Break Start --}}
            @if($today && $today->login_time && !$today->break_start_time && !$today->logout_time)
                <form method="POST" action="{{ route('attendance.break-start') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Start Break
                    </button>
                </form>
            @endif

            {{-- Break End --}}
            @if($today && $today->break_start_time && !$today->break_end_time && !$today->logout_time)
                <form method="POST" action="{{ route('attendance.break-end') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        End Break
                    </button>
                </form>
            @endif

            {{-- Clock Out --}}
            @if($today && $today->login_time && !$today->logout_time)
                <form method="POST" action="{{ route('attendance.clock-out') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Clock Out
                    </button>
                </form>
            @endif

            {{-- All done --}}
            @if($today && $today->logout_time)
                <span class="inline-flex items-center px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Day Completed
                </span>
            @endif
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
        <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Month:</label>
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

    {{-- Attendance History Table --}}
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $record->date->format('D, d M') }}
                            </td>
                            <td class="px-4 py-3 text-green-600 dark:text-green-400 font-mono">
                                {{ $record->login_time ? \Carbon\Carbon::createFromTimeString($record->login_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 text-yellow-600 dark:text-yellow-400 font-mono">
                                {{ $record->break_start_time ? \Carbon\Carbon::createFromTimeString($record->break_start_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 text-blue-600 dark:text-blue-400 font-mono">
                                {{ $record->break_end_time ? \Carbon\Carbon::createFromTimeString($record->break_end_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 text-red-600 dark:text-red-400 font-mono">
                                {{ $record->logout_time ? \Carbon\Carbon::createFromTimeString($record->logout_time)->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono">
                                {{ $record->break_duration ? number_format($record->break_duration, 1) . 'h' : '--' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-gray-100">
                                {{ $record->formatted_total_hours }}
                            </td>
                            <td class="px-4 py-3">{!! $record->status_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                No attendance records for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($today && $today->login_time && !$today->logout_time)
@push('scripts')
<script>
    const loginTime = new Date();
    const [h, m, s] = '{{ $today->login_time }}'.split(':');
    loginTime.setHours(parseInt(h), parseInt(m), parseInt(s), 0);

    function updateTimer() {
        const now = new Date();
        const diff = Math.floor((now - loginTime) / 1000);
        const hrs  = Math.floor(diff / 3600).toString().padStart(2, '0');
        const mins = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
        const secs = (diff % 60).toString().padStart(2, '0');
        const el = document.getElementById('liveTimer');
        if (el) el.textContent = `${hrs}:${mins}:${secs}`;
    }
    updateTimer();
    setInterval(updateTimer, 1000);
</script>
@endpush
@endif
@endsection
