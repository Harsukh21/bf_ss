@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('attendance.admin.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Attendance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $attendance->user->name }} — {{ $attendance->date->format('D, d M Y') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('attendance.update', $attendance) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Login Time</label>
                    <input type="time" name="login_time" value="{{ $attendance->login_time ? substr($attendance->login_time, 0, 5) : '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('login_time') border-red-500 @enderror">
                    @error('login_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logout Time</label>
                    <input type="time" name="logout_time" value="{{ $attendance->logout_time ? substr($attendance->logout_time, 0, 5) : '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('logout_time') border-red-500 @enderror">
                    @error('logout_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Break Start</label>
                    <input type="time" name="break_start_time" value="{{ $attendance->break_start_time ? substr($attendance->break_start_time, 0, 5) : '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Break End</label>
                    <input type="time" name="break_end_time" value="{{ $attendance->break_end_time ? substr($attendance->break_end_time, 0, 5) : '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach(['present','absent','half_day','on_leave','holiday','incomplete'] as $s)
                        <option value="{{ $s }}" @selected($attendance->status == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional admin notes..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">{{ $attendance->notes }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Save Changes</button>
                <a href="{{ route('attendance.admin.index') }}" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
