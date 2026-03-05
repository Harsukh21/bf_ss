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
.toast-success { background:#065f46; color:#fff; }
</style>
@endpush

@extends('layouts.app')

@section('title', 'Holiday Calendar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Toast Notifications --}}
    @if(session('success'))
    <div id="successToast" class="toast-notification toast-success show">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button onclick="this.closest('.toast-notification').classList.remove('show')" class="ml-auto opacity-70 hover:opacity-100">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Holiday Calendar</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Official holidays for {{ $year }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('holidays.index') }}" class="flex gap-2">
                <select name="year" class="mt-1 block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 text-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Go
                </button>
            </form>
            @can('manage-holidays')
                <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Holiday
                </a>
            @endcan
        </div>
    </div>

    @if($holidays->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No holidays defined for {{ $year }}.</p>
            @can('manage-holidays')
                <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Add First Holiday
                </a>
            @endcan
        </div>
    @else
        {{-- Holiday List --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $holidays->count() }} holidays listed for {{ $year }}</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($holidays as $holiday)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-center gap-4">
                            {{-- Date badge --}}
                            <div class="text-center min-w-[56px] bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $holiday->date->format('M') }}</p>
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 leading-none">{{ $holiday->date->format('d') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $holiday->date->format('D') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $holiday->name }}</p>
                                @if($holiday->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $holiday->description }}</p>
                                @endif
                                @if($holiday->is_recurring)
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">Recurring</span>
                                @endif
                            </div>
                        </div>
                        @can('manage-holidays')
                        <div class="flex items-center gap-1 ml-4">
                            <a href="{{ route('holidays.edit', $holiday) }}" class="p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('holidays.destroy', $holiday) }}" onsubmit="return confirm('Delete this holiday?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('successToast');
    if (el) setTimeout(function() { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }, 5000);
});
</script>
@endpush
@endsection
