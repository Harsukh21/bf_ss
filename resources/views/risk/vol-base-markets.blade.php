@extends('layouts.app')

@section('title', 'Vol. Base Markets')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .filter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 1030;
    }

    .filter-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .filter-drawer {
        position: fixed;
        top: 0;
        right: -600px;
        width: 560px;
        max-width: 92vw;
        height: 100vh;
        background: #ffffff;
        color: #0f172a;
        box-shadow: -12px 0 30px rgba(15, 23, 42, 0.25);
        border-left: 1px solid rgba(226, 232, 240, 0.8);
        z-index: 1040;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .dark .filter-drawer {
        background: #1f2937;
        border-color: #374151;
        color: #f3f4f6;
    }

    .filter-drawer.open {
        right: 0;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Vol. Base Markets</h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">Markets with maximum total matched volume across all events</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <button onclick="toggleVolBaseFilterDrawer()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
                @php
                    $hasFilters = request()->has('search') && trim(request('search')) !== '' ||
                                  request()->has('sport') && trim(request('sport')) !== '' ||
                                  (request()->has('volume_value') && trim(request('volume_value')) !== '' && request()->has('volume_operator')) ||
                                  request()->has('date_from') && trim(request('date_from')) !== '' ||
                                  request()->has('date_to') && trim(request('date_to')) !== '';
                    $filterCount = 0;
                    if (request()->has('search') && trim(request('search')) !== '') $filterCount++;
                    if (request()->has('sport') && trim(request('sport')) !== '') $filterCount++;
                    if (request()->has('volume_value') && trim(request('volume_value')) !== '' && request()->has('volume_operator')) $filterCount++;
                    if (request()->has('date_from') && trim(request('date_from')) !== '') $filterCount++;
                    if (request()->has('date_to') && trim(request('date_to')) !== '') $filterCount++;
                @endphp
                @if($filterCount)
                    <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $filterCount }}</span>
                @endif
            </button>
            @if($hasFilters)
                <a href="{{ route('risk.vol-base-markets.index') }}" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </a>
            @endif
        </div>
    </div>

    @if($hasFilters)
        @php
            $activeFilters = [];
            if (request()->has('search') && trim(request('search')) !== '') {
                $activeFilters[] = ['label' => 'Search', 'value' => request('search'), 'query' => 'search'];
            }
            if (request()->has('sport') && trim(request('sport')) !== '') {
                $activeFilters[] = ['label' => 'Sport', 'value' => request('sport'), 'query' => 'sport'];
            }
            if (request()->has('volume_value') && trim(request('volume_value')) !== '' && request()->has('volume_operator')) {
                $operatorLabel = request('volume_operator') === 'greater_than' ? 'Greater Than' : 'Less Than';
                $activeFilters[] = ['label' => 'Volume (' . $operatorLabel . ')', 'value' => request('volume_value'), 'query' => 'volume_value'];
            }
            if (request()->has('date_from') && trim(request('date_from')) !== '') {
                $activeFilters[] = ['label' => 'From Date', 'value' => request('date_from'), 'query' => 'date_from'];
            }
            if (request()->has('date_to') && trim(request('date_to')) !== '') {
                $activeFilters[] = ['label' => 'To Date', 'value' => request('date_to'), 'query' => 'date_to'];
            }
        @endphp
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13.414V19l-6 2v-7.586L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Active Filters ({{ count($activeFilters) }})
                </div>
                <a href="{{ route('risk.vol-base-markets.index') }}" class="text-sm text-blue-700 dark:text-blue-300 hover:underline">Clear all</a>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($activeFilters as $filter)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        {{ $filter['label'] }}: {{ $filter['value'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Markets Table -->
    @if($markets->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Market</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Tournament</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Sport</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Max Volume</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($markets as $market)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $market->marketName }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $market->exMarketId }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $market->tournamentsName ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $market->sportName ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($market->max_total_matched, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusMap = [
                                            1 => ['label' => 'UNSETTLED', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                                            2 => ['label' => 'UPCOMING', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
                                            3 => ['label' => 'INPLAY', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                            4 => ['label' => 'CLOSED', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                                            5 => ['label' => 'VOIDED', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
                                        ];
                                        $status = $statusMap[$market->status ?? 1] ?? $statusMap[1];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 dark:text-gray-400">No markets found for the selected filters.</p>
        </div>
    @endif

    @if($markets->hasPages())
        <div class="mt-6">
            {{ $markets->links() }}
        </div>
    @endif
</div>

<!-- Filter Drawer -->
<div id="volbase-filter-overlay" class="filter-overlay" onclick="toggleVolBaseFilterDrawer()"></div>
<div id="volbase-filter-drawer" class="filter-drawer" aria-hidden="true">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Filter Vol. Base Markets</h3>
        <button onclick="toggleVolBaseFilterDrawer()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto px-5 py-4">
        <form method="GET" action="{{ route('risk.vol-base-markets.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Market or event name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <select name="sport" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Sports</option>
                    @foreach($sports as $sportId => $sportName)
                        <option value="{{ $sportName }}" @selected(request('sport') === $sportName)>{{ $sportName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Volume Filter</label>
                <div class="flex gap-2">
                    <select name="volume_operator" class="w-1/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select</option>
                        <option value="greater_than" @selected(request('volume_operator') === 'greater_than')>Greater Than</option>
                        <option value="less_than" @selected(request('volume_operator') === 'less_than')>Less Than</option>
                    </select>
                    <input type="number" name="volume_value" value="{{ request('volume_value') }}" placeholder="0.00" step="0.01" min="0" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                <input type="text" 
                    name="date_from" 
                    value="{{ request('date_from') }}" 
                    placeholder="DD/MM/YYYY"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 js-date-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                <input type="text" 
                    name="date_to" 
                    value="{{ request('date_to') }}" 
                    placeholder="DD/MM/YYYY"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 js-date-input">
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium">
                    Apply Filters
                </button>
                <a href="{{ route('risk.vol-base-markets.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
function toggleVolBaseFilterDrawer() {
    const overlay = document.getElementById('volbase-filter-overlay');
    const drawer = document.getElementById('volbase-filter-drawer');
    
    if (drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        overlay.classList.remove('active');
    } else {
        drawer.classList.add('open');
        overlay.classList.add('active');
    }
}

// Date input formatting (DD/MM/YYYY)
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('.js-date-input');
    
    dateInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            if (value.length >= 5) {
                value = value.slice(0, 5) + '/' + value.slice(5, 9);
            }
            
            e.target.value = value;
        });
        
        input.addEventListener('keydown', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
