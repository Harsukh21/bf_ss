@extends('layouts.app')

@section('title', 'Add Holiday')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('holidays.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Holiday</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new holiday to the calendar</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('holidays.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Holiday Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Diwali, Christmas"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ old('date') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('date') border-red-500 @enderror">
                @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Optional description..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-primary-600 border-gray-300 dark:border-gray-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recurring Holiday</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Repeats every year on the same date (e.g. national holidays)</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Add Holiday</button>
                <a href="{{ route('holidays.index') }}" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
