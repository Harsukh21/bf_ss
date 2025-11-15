@extends('layouts.app')

@section('title', 'Risk - Done')

@push('css')
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
        right: -440px;
        width: 420px;
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

    .filter-drawer form label {
        font-weight: 500;
    }
</style>
@endpush

@section('content')
@php
    $searchValue = request()->input('search');
    $sportValue = request()->input('sport');
    $tournamentValue = request()->input('tournament');
    $limitValue = request()->input('limit');

    $hasSearch = request()->has('search') && trim((string) $searchValue) !== '';
    $hasSport = request()->has('sport') && trim((string) $sportValue) !== '';
    $hasTournament = request()->has('tournament') && trim((string) $tournamentValue) !== '';
    $hasCustomLimit = request()->has('limit') && (int) $limitValue !== 50;

    $activeFilters = [];
    if ($hasSearch) $activeFilters[] = ['label' => 'Search', 'value' => $searchValue, 'query' => 'search'];
    if ($hasSport) $activeFilters[] = ['label' => 'Sport', 'value' => $sportValue, 'query' => 'sport'];
    if ($hasTournament) $activeFilters[] = ['label' => 'Tournament', 'value' => $tournamentValue, 'query' => 'tournament'];
    if ($hasCustomLimit) $activeFilters[] = ['label' => 'Limit', 'value' => $limitValue, 'query' => 'limit'];
    $filterCount = count($activeFilters);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Risk - Completed Markets</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Showing markets where all reviews are complete.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap justify-end">
            <button onclick="toggleDoneFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13.414V19l-6 2v-7.586L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
                @if($filterCount)
                    <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $filterCount }}</span>
                @endif
            </button>
            @if($filterCount)
                <a href="{{ route('risk.done') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </a>
            @endif
            <a href="{{ route('risk.pending', array_merge(request()->query(), ['export' => 'excel'])) }}" class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V8H12l-2-2H4z"></path>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    @if($filterCount)
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13.414V19l-6 2v-7.586L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Active Filters ({{ $filterCount }})
                </div>
                <a href="{{ route('risk.done') }}" class="text-sm text-blue-700 dark:text-blue-300 hover:underline">Clear all</a>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($activeFilters as $filter)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        {{ $filter['label'] }}: {{ $filter['value'] }}
                        <button onclick="removeDoneFilter('{{ $filter['query'] }}')" class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100">&times;</button>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Markets</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary['total'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Settled</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary['settled'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Voided</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary['voided'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Completed Markets</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tournament</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remark</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($markets as $market)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                <div class="font-medium">{{ $market->marketName }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $market->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $market->eventName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $market->tournamentsName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300">
                                    {{ $market->sportName ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusMap = [
                                        4 => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300'],
                                        5 => ['label' => 'Voided', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300'],
                                    ];
                                    $meta = $statusMap[$market->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $market->marketTime ? \Carbon\Carbon::parse($market->marketTime)->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $market->remark ? Str::limit($market->remark, 120) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No completed markets found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="done-filter-overlay" class="filter-overlay" onclick="toggleDoneFilterDrawer(true)"></div>
<div id="done-filter-drawer" class="filter-drawer" aria-hidden="true">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Filter Completed Markets</h3>
        <button onclick="toggleDoneFilterDrawer(true)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto px-5 py-4">
        <form method="GET" action="{{ route('risk.done') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Market or event name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <input type="text" name="sport" value="{{ request('sport') }}" placeholder="Sport name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tournament</label>
                <input type="text" name="tournament" value="{{ request('tournament') }}" placeholder="Tournament name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Limit</label>
                <input type="number" name="limit" value="{{ request('limit', 50) }}" min="10" max="500" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Apply Filters</button>
                <a href="{{ route('risk.done') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleDoneFilterDrawer(forceClose = null) {
        const drawer = document.getElementById('done-filter-drawer');
        const overlay = document.getElementById('done-filter-overlay');
        let shouldOpen;

        if (forceClose === true) {
            shouldOpen = false;
        } else if (forceClose === false) {
            shouldOpen = true;
        } else {
            shouldOpen = !drawer.classList.contains('open');
        }

        drawer.classList.toggle('open', shouldOpen);
        overlay.classList.toggle('active', shouldOpen);
        drawer.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
        document.body.style.overflow = shouldOpen ? 'hidden' : '';
    }

    function removeDoneFilter(param) {
        const url = new URL(window.location.href);
        url.searchParams.delete(param);
        window.location.href = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            toggleDoneFilterDrawer(true);
        }
    });
</script>
@endpush

