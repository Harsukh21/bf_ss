@push('css')
<style>
    .toast-notification {
        position: fixed; top: 20px; right: 20px; min-width: 260px;
        padding: 12px 16px; border-radius: 8px; color: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        display: flex; align-items: center; gap: 10px; z-index: 2000;
        opacity: 0; transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .toast-notification.show  { opacity: 1; transform: translateY(0); }
    .toast-notification.toast-success { background: rgba(5,150,105,0.95); }
    .toast-notification.toast-error   { background: rgba(220,38,38,0.95); }
</style>
@endpush

@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    @if(session('success'))
        <div id="successToast" class="toast-notification toast-success show">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
    @if(session('error'))
        <div id="errorToast" class="toast-notification toast-error show">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Attendance</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track your daily work hours — {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('leaves.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    My Leaves
                </a>
                <a href="{{ route('holidays.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    Holidays
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Present</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $monthlyStats['present'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Absent</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $monthlyStats['absent'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Half Day</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $monthlyStats['half_day'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">On Leave</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $monthlyStats['on_leave'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Hours</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $monthlyStats['total_hours'] }}h</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Today — {{ now()->format('l, d M Y') }}</h2>

            <!-- Time Slots Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                @foreach([
                    ['label' => 'Login',       'val' => $today?->login_time,       'color' => 'text-green-600 dark:text-green-400'],
                    ['label' => 'Break Start', 'val' => $today?->break_start_time, 'color' => 'text-yellow-600 dark:text-yellow-400'],
                    ['label' => 'Break End',   'val' => $today?->break_end_time,   'color' => 'text-blue-600 dark:text-blue-400'],
                    ['label' => 'Logout',      'val' => $today?->logout_time,      'color' => 'text-red-600 dark:text-red-400'],
                ] as $slot)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ $slot['label'] }}</p>
                    <p class="text-xl font-bold font-mono {{ $slot['color'] }}">
                        {{ $slot['val'] ? \Carbon\Carbon::createFromTimeString($slot['val'])->format('h:i A') : '--:--' }}
                    </p>
                </div>
                @endforeach
            </div>

            <!-- Live Timer / Total -->
            @if($today && $today->login_time && !$today->logout_time)
                <div class="mb-5 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center border border-green-200 dark:border-green-800">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Session Duration</p>
                    <p id="liveTimer" class="text-3xl font-mono font-bold text-green-600 dark:text-green-400">00:00:00</p>
                </div>
            @elseif($today && $today->total_hours)
                <div class="mb-5 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-center border border-primary-200 dark:border-primary-800">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Hours Worked</p>
                    <p class="text-3xl font-mono font-bold text-primary-600 dark:text-primary-400">{{ $today->formatted_total_hours }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 justify-center">
                @if(!$today || !$today->login_time)
                    <form method="POST" action="{{ route('attendance.clock-in') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Clock In
                        </button>
                    </form>
                @endif
                @if($today && $today->login_time && !$today->break_start_time && !$today->logout_time)
                    <form method="POST" action="{{ route('attendance.break-start') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Start Break
                        </button>
                    </form>
                @endif
                @if($today && $today->break_start_time && !$today->break_end_time && !$today->logout_time)
                    <form method="POST" action="{{ route('attendance.break-end') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            End Break
                        </button>
                    </form>
                @endif
                @if($today && $today->login_time && !$today->logout_time)
                    <form method="POST" action="{{ route('attendance.clock-out') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Clock Out
                        </button>
                    </form>
                @endif
                @if($today && $today->logout_time)
                    <span class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Day Completed
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-4 sm:px-6">
            <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-center gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Month:</label>
                <select name="month" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
                <select name="year" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    View
                </button>
            </form>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        @if($records->count() > 0)
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
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($records as $record)
                            @php
                                $statusColors = [
                                    'present'    => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300',
                                    'absent'     => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
                                    'half_day'   => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                                    'on_leave'   => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                                    'holiday'    => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300',
                                    'incomplete' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record->date->format('D, d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-green-600 dark:text-green-400">{{ $record->login_time ? \Carbon\Carbon::createFromTimeString($record->login_time)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-yellow-600 dark:text-yellow-400">{{ $record->break_start_time ? \Carbon\Carbon::createFromTimeString($record->break_start_time)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-600 dark:text-blue-400">{{ $record->break_end_time ? \Carbon\Carbon::createFromTimeString($record->break_end_time)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-red-600 dark:text-red-400">{{ $record->logout_time ? \Carbon\Carbon::createFromTimeString($record->logout_time)->format('h:i A') : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">{{ $record->break_duration ? number_format($record->break_duration, 1) . 'h' : '--' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $record->formatted_total_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucwords(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No attendance records</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No records found for this month.</p>
                </div>
            </div>
        @endif
    </div>

</div>

@if($today && $today->login_time && !$today->logout_time)
@push('scripts')
<script>
const loginParts = '{{ $today->login_time }}'.split(':');
const loginBase  = new Date();
loginBase.setHours(parseInt(loginParts[0]), parseInt(loginParts[1]), parseInt(loginParts[2] ?? 0), 0);
function updateTimer() {
    const diff = Math.floor((new Date() - loginBase) / 1000);
    const h = Math.floor(diff/3600).toString().padStart(2,'0');
    const m = Math.floor((diff%3600)/60).toString().padStart(2,'0');
    const s = (diff%60).toString().padStart(2,'0');
    const el = document.getElementById('liveTimer');
    if (el) el.textContent = `${h}:${m}:${s}`;
}
updateTimer();
setInterval(updateTimer, 1000);
</script>
@endpush
@endif

@push('scripts')
<script>
setTimeout(() => {
    ['successToast','errorToast'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }
    });
}, 5000);
</script>
@endpush
@endsection
