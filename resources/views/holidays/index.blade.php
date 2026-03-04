@extends('layouts.app')

@section('title', 'Holiday Calendar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="hover:text-green-800">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Holiday Calendar</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Official holidays for {{ $year }}</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Year Selector --}}
            <form method="GET" action="{{ route('holidays.index') }}" class="flex gap-2">
                <select name="year" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600">Go</button>
            </form>
            @can('manage-holidays')
                <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Holiday
                </a>
            @endcan
        </div>
    </div>

    @if($holidays->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No holidays defined for {{ $year }}.</p>
            @can('manage-holidays')
                <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm">Add First Holiday</a>
            @endcan
        </div>
    @else
        {{-- Holiday List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $holidays->count() }} holidays listed</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($holidays as $holiday)
                    <div class="flex items-center justify-between px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-center gap-4">
                            {{-- Date badge --}}
                            <div class="text-center min-w-[60px]">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $holiday->date->format('M') }}</p>
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $holiday->date->format('d') }}</p>
                                <p class="text-xs text-gray-400">{{ $holiday->date->format('D') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $holiday->name }}</p>
                                @if($holiday->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $holiday->description }}</p>
                                @endif
                                @if($holiday->is_recurring)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300">Recurring</span>
                                @endif
                            </div>
                        </div>
                        @can('manage-holidays')
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('holidays.edit', $holiday) }}" class="p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('holidays.destroy', $holiday) }}" onsubmit="return confirm('Delete this holiday?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
