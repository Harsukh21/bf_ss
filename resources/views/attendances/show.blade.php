@extends('layouts.app')
@section('title', 'Attendance — ' . $attendance->employee->name . ' ' . $attendance->date->format('d M Y'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Success Toast --}}
    @if(session('success'))
    <div id="successToast" class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-lg bg-green-600 text-white shadow-lg min-w-[260px]">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span class="flex-1 text-sm">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">×</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Attendance Detail</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->employee->name }} · {{ $attendance->date->format('d M Y, D') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('emp-attendance.edit', $attendance) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <a href="{{ route('emp-attendance.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    @php
        $sc = [
            'present'  => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300',
            'absent'   => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
            'half_day' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
            'late'     => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
            'on_leave' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
            'holiday'  => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300',
        ][$attendance->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
    @endphp

    {{-- Employee Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-5">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-xl flex-shrink-0">
                {{ $attendance->employee->initials ?? '?' }}
            </div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('employees.show', $attendance->employee) }}" class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                    {{ $attendance->employee->name }}
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->employee->employee_id }} · {{ $attendance->employee->designation }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $sc }}">
                {{ $attendance->status_label }}
            </span>
        </div>
    </div>

    {{-- Time Details --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Time Details</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Start Time</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('h:i A') : '—' }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">End Time</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('h:i A') : '—' }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Break</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($attendance->start_break_time && $attendance->end_break_time)
                        {{ \Carbon\Carbon::parse($attendance->start_break_time)->format('h:i A') }}<br>
                        <span class="text-xs text-gray-500">to</span><br>
                        {{ \Carbon\Carbon::parse($attendance->end_break_time)->format('h:i A') }}
                    @else
                        —
                    @endif
                </p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Working Time</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $attendance->working_time }}
                </p>
            </div>
        </div>
    </div>

    {{-- Meta --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendance->date->format('d M Y, D') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Recorded By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendance->creator?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Created At</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendance->created_at->format('d M Y, h:i A') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendance->updated_at->format('d M Y, h:i A') }}</dd>
            </div>
            @if($attendance->note)
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Note</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendance->note }}</dd>
            </div>
            @endif
        </dl>
    </div>

</div>

@push('scripts')
<script>
setTimeout(() => {
    const t = document.getElementById('successToast');
    if (t) { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }
}, 5000);
</script>
@endpush
@endsection
