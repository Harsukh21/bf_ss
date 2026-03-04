@extends('layouts.app')

@section('title', 'Add Attendance')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('attendance.admin.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Attendance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manually add an attendance record for any employee</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="hover:text-red-800">&times;</button>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf

            {{-- Employee --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Employee <span class="text-red-500">*</span>
                </label>
                <select name="user_id" id="userSelect"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('user_id') border-red-500 @enderror">
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
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date" value="{{ old('date', today()->toDateString()) }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('date') border-red-500 @enderror">
                @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Time Fields --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Login Time</label>
                    <input type="time" name="login_time" value="{{ old('login_time') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('login_time') border-red-500 @enderror">
                    @error('login_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logout Time</label>
                    <input type="time" name="logout_time" value="{{ old('logout_time') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('logout_time') border-red-500 @enderror">
                    @error('logout_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Break Start</label>
                    <input type="time" name="break_start_time" value="{{ old('break_start_time') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Break End</label>
                    <input type="time" name="break_end_time" value="{{ old('break_end_time') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>

            {{-- Live Hours Preview --}}
            <div id="hoursPreview" class="mb-5 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-800 dark:text-blue-300 hidden">
                Estimated net hours: <span id="hoursVal" class="font-bold"></span>
            </div>

            {{-- Status --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach(['present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day', 'on_leave' => 'On Leave', 'holiday' => 'Holiday', 'incomplete' => 'Incomplete'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', 'present') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional admin notes..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">
                    Add Attendance
                </button>
                <a href="{{ route('attendance.admin.index') }}" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
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
</script>
@endpush
@endsection
