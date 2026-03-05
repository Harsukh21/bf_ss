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
.toast-error { background:#991b1b; color:#fff; }
</style>
@endpush

@extends('layouts.app')

@section('title', 'Add Attendance')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    @if(session('error'))
    <div id="errorToast" class="toast-notification toast-error show">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
        <button onclick="this.closest('.toast-notification').classList.remove('show')" class="ml-auto opacity-70 hover:opacity-100">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('attendance.admin.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Attendance</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manually add an attendance record for any employee</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <div class="px-4 py-5 sm:p-6 space-y-5">

                {{-- Employee --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Employee <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="userSelect"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('user_id') border-red-500 @enderror">
                        <option value="">— Select Employee —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id', $selectedId) == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', today()->toDateString()) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('date') border-red-500 @enderror">
                    @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Time Fields --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login Time</label>
                        <input type="time" name="login_time" value="{{ old('login_time') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('login_time') border-red-500 @enderror">
                        @error('login_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logout Time</label>
                        <input type="time" name="logout_time" value="{{ old('logout_time') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('logout_time') border-red-500 @enderror">
                        @error('logout_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Break Start</label>
                        <input type="time" name="break_start_time" value="{{ old('break_start_time') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Break End</label>
                        <input type="time" name="break_end_time" value="{{ old('break_end_time') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                </div>

                {{-- Live Hours Preview --}}
                <div id="hoursPreview" class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-800 dark:text-blue-300 hidden">
                    Estimated net hours: <span id="hoursVal" class="font-bold"></span>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                        @foreach(['present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day', 'on_leave' => 'On Leave', 'holiday' => 'Holiday', 'incomplete' => 'Incomplete'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', 'present') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional admin notes..."
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 px-4 py-4 sm:px-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('attendance.admin.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Add Attendance
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calcHours() {
    const login   = document.querySelector('[name="login_time"]').value;
    const logout  = document.querySelector('[name="logout_time"]').value;
    const bStart  = document.querySelector('[name="break_start_time"]').value;
    const bEnd    = document.querySelector('[name="break_end_time"]').value;
    const preview = document.getElementById('hoursPreview');
    const hoursEl = document.getElementById('hoursVal');

    if (!login || !logout) { preview.classList.add('hidden'); return; }

    const toMins = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };
    let gross = toMins(logout) - toMins(login);
    if (gross <= 0) { preview.classList.add('hidden'); return; }

    let brk = 0;
    if (bStart && bEnd) {
        brk = Math.max(0, toMins(bEnd) - toMins(bStart));
    }

    const net  = Math.max(0, gross - brk);
    const hrs  = Math.floor(net / 60);
    const mins = net % 60;
    hoursEl.textContent = `${hrs}h ${mins}m`;
    preview.classList.remove('hidden');
}

document.querySelectorAll('[name="login_time"],[name="logout_time"],[name="break_start_time"],[name="break_end_time"]')
    .forEach(el => el.addEventListener('change', calcHours));

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('errorToast');
    if (el) setTimeout(function() { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }, 5000);
});
</script>
@endpush
@endsection
