@extends('layouts.app')

@section('title', 'Risk - Pending')

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

    .remark-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease;
        z-index: 1050;
    }

    .remark-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .remark-modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1060;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    .remark-modal.active {
        pointer-events: auto;
        opacity: 1;
        visibility: visible;
    }

    .remark-modal__content {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
    }

    .dark .remark-modal__content {
        background: #1f2937;
    }

    .risk-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        padding: 0.85rem 1.1rem;
        border-radius: 0.75rem;
        color: #fff;
        font-size: 0.875rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 2000;
    }

    .risk-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .risk-toast.success {
        background: #16a34a;
    }

    .risk-toast.error {
        background: #dc2626;
    }
</style>
@endpush

@section('content')
@php
    $labelOptions = [
        '4x' => '4x',
        'b2c' => 'B2C',
        'b2b' => 'B2B',
        'usdt' => 'USDT',
    ];

    $activeFilters = [];

    $searchValue = request()->input('search');
    $sportValue = request()->input('sport');
    $tournamentValue = request()->input('tournament');
    $selectedLabels = collect(request()->input('labels', []))
        ->map(fn ($value) => strtolower((string) $value))
        ->filter(fn ($value) => array_key_exists($value, $labelOptions))
        ->unique()
        ->values()
        ->all();

    $hasSearch = request()->has('search') && trim((string) $searchValue) !== '';
    $hasSport = request()->has('sport') && trim((string) $sportValue) !== '';
    $hasTournament = request()->has('tournament') && trim((string) $tournamentValue) !== '';

    if ($hasSearch) {
        $activeFilters[] = ['label' => 'Search', 'value' => $searchValue, 'query' => 'search'];
    }
    if ($hasSport) {
        $activeFilters[] = ['label' => 'Sport', 'value' => $sportValue, 'query' => 'sport'];
    }
    if ($hasTournament) {
        $activeFilters[] = ['label' => 'Tournament', 'value' => $tournamentValue, 'query' => 'tournament'];
    }
    foreach ($selectedLabels as $labelKey) {
        $activeFilters[] = [
            'label' => strtoupper($labelKey),
            'value' => 'Yes',
            'query' => 'labels',
            'query_value' => $labelKey,
        ];
    }

    $filterCount = count($activeFilters);
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Risk - Pending Markets</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Markets that are Settled or Voided and waiting for review.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap justify-end">
            <button onclick="toggleRiskFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13.414V19l-6 2v-7.586L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
                @if($filterCount)
                    <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $filterCount }}</span>
                @endif
            </button>
            @if($filterCount)
                <a href="{{ route('risk.pending') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
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
                <a href="{{ route('risk.pending') }}" class="text-sm text-blue-700 dark:text-blue-300 hover:underline">Clear all</a>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($activeFilters as $filter)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        {{ $filter['label'] }}: {{ $filter['value'] }}
                        <button onclick="removeRiskFilter('{{ $filter['query'] }}', {{ isset($filter['query_value']) ? '\'' . $filter['query_value'] . '\'' : 'null' }})" class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100">&times;</button>
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pending Markets</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tournament</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport & Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($markets as $market)
                        @php
                            $decodedLabels = json_decode($market->labels ?? '{}', true);
                            $labelStates = array_merge([
                                '4x' => false,
                                'b2c' => false,
                                'b2b' => false,
                                'usdt' => false,
                            ], is_array($decodedLabels) ? $decodedLabels : []);

                            $allLabelsChecked = collect($labelStates)->every(fn ($value) => (bool) $value === true);
                            $isDone = (bool) $market->is_done;
                            $buttonDisabled = !$allLabelsChecked || $isDone;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" data-market-row="{{ $market->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button
                                    class="mark-done-button inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full transition disabled:opacity-50 disabled:cursor-not-allowed {{ $isDone ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-300' }}"
                                    data-market-id="{{ $market->id }}"
                                    data-market-name="{{ $market->marketName }}"
                                    data-done-url="{{ route('risk.markets.done', $market->id) }}"
                                    data-is-done="{{ $isDone ? 'true' : 'false' }}"
                                    @if($buttonDisabled) disabled @endif
                                >
                                    {{ $isDone ? 'Completed' : 'Done' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 align-top">
                                <div class="font-medium">{{ $market->marketName }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $market->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
                                @if(!empty($market->completeTime))
                                    <span class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-200">
                                        Complete: {{ \Carbon\Carbon::parse($market->completeTime)->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $market->tournamentsName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300">
                                        {{ $market->sportName ?? 'N/A' }}
                                    </span>
                                    @php
                                        $statusMap = [
                                            4 => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300'],
                                            5 => ['label' => 'Voided', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300'],
                                        ];
                                        $meta = $statusMap[$market->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="bg-gray-50/60 dark:bg-gray-800/70 text-xs text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700">
                            <td colspan="7" class="px-6 py-3">
                                <div class="flex flex-wrap items-center gap-6 market-labels-wrapper" data-market-id="{{ $market->id }}" data-update-url="{{ route('risk.markets.labels', $market->id) }}">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Labels:</span>
                                    @foreach($labelStates as $key => $value)
                                        @php $checkboxId = "market-option-{$market->id}-{$key}"; @endphp
                                        <label for="{{ $checkboxId }}" class="inline-flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                id="{{ $checkboxId }}"
                                                class="market-label-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                                data-market-id="{{ $market->id }}"
                                                data-label-key="{{ $key }}"
                                                @checked((bool) $value)
                                                @disabled($isDone)
                                            >
                                            <span class="uppercase">{{ $key }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No markets found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $markets->links() }}
        </div>
    </div>
</div>

<div id="risk-filter-overlay" class="filter-overlay" onclick="toggleRiskFilterDrawer(true)"></div>
<div id="risk-filter-drawer" class="filter-drawer" aria-hidden="true">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Filter Pending Markets</h3>
        <button onclick="toggleRiskFilterDrawer(true)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto px-5 py-4">
        <form method="GET" action="{{ route('risk.pending') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Market or event name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <select id="pendingSportSelect" name="sport" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Sports</option>
                    @foreach($sports as $sportId => $sportName)
                        <option value="{{ $sportName }}" @selected(request('sport') === $sportName)>{{ $sportName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tournament</label>
                <input type="text" name="tournament" value="{{ request('tournament') }}" placeholder="Tournament name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Labels</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($labelOptions as $labelKey => $labelName)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="labels[]" value="{{ $labelKey }}" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500" @checked(in_array($labelKey, $selectedLabels))>
                            <span class="uppercase">{{ $labelName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Apply Filters</button>
                <a href="{{ route('risk.pending') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>
</div>

<div id="remarkModalOverlay" class="remark-modal-overlay"></div>
<div id="remarkModal" class="remark-modal">
    <div class="remark-modal__content">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Add Remark</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="remarkModalMarketName"></p>
        <textarea id="remarkInput" class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500" rows="4" placeholder="Add remark..."></textarea>
        <div class="mt-4 flex justify-end gap-3">
            <button id="remarkCancelBtn" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
            <button id="remarkSubmitBtn" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700">Submit</button>
        </div>
    </div>
</div>

<div id="riskToast" class="risk-toast"></div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function toggleRiskFilterDrawer(forceClose = null) {
        const drawer = document.getElementById('risk-filter-drawer');
        const overlay = document.getElementById('risk-filter-overlay');
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

    function removeRiskFilter(param, value = null) {
        const url = new URL(window.location.href);
        if (value === null) {
            url.searchParams.delete(param);
        } else {
            const remaining = url.searchParams.getAll(param).filter(v => v !== value);
            url.searchParams.delete(param);
            remaining.forEach(v => url.searchParams.append(param, v));
        }
        window.location.href = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (document.getElementById('remarkModal').classList.contains('active')) {
                closeRemarkModal();
            } else {
                toggleRiskFilterDrawer(true);
            }
        }
    });

    const marketLabelWrappers = document.querySelectorAll('.market-labels-wrapper');
    marketLabelWrappers.forEach(wrapper => {
        wrapper.addEventListener('change', (event) => {
            if (!event.target.classList.contains('market-label-checkbox')) {
                return;
            }
            const updateUrl = wrapper.dataset.updateUrl;
            const marketId = wrapper.dataset.marketId;
            const checkboxes = wrapper.querySelectorAll('.market-label-checkbox');
            const labels = {};
            checkboxes.forEach(box => {
                labels[box.dataset.labelKey] = box.checked;
            });

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ labels }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDoneButtonState(marketId, data.labels);
                    showRiskToast('Labels updated', 'success');
                } else {
                    showRiskToast(data.message || 'Unable to update labels', 'error');
                }
            })
            .catch(() => showRiskToast('Unable to update labels', 'error'));
        });
    });

    const remarkModal = document.getElementById('remarkModal');
    const remarkOverlay = document.getElementById('remarkModalOverlay');
    const remarkInput = document.getElementById('remarkInput');
    const remarkMarketName = document.getElementById('remarkModalMarketName');
    const remarkCancelBtn = document.getElementById('remarkCancelBtn');
    const remarkSubmitBtn = document.getElementById('remarkSubmitBtn');
    const toastElement = document.getElementById('riskToast');
    let activeMarketId = null;
    let activeDoneUrl = null;

    function openRemarkModal(marketId, marketName, doneUrl) {
        activeMarketId = marketId;
        activeDoneUrl = doneUrl;
        remarkMarketName.textContent = `Market: ${marketName}`;
        remarkInput.value = '';
        remarkModal.classList.add('active');
        remarkOverlay.classList.add('active');
    }

    function closeRemarkModal() {
        activeMarketId = null;
        activeDoneUrl = null;
        remarkModal.classList.remove('active');
        remarkOverlay.classList.remove('active');
    }

    remarkCancelBtn.addEventListener('click', closeRemarkModal);
    remarkOverlay.addEventListener('click', closeRemarkModal);

    document.querySelectorAll('.mark-done-button').forEach(button => {
        button.addEventListener('click', () => {
            if (button.disabled) return;
            openRemarkModal(button.dataset.marketId, button.dataset.marketName, button.dataset.doneUrl);
        });
    });

    remarkSubmitBtn.addEventListener('click', () => {
        if (!activeMarketId || !activeDoneUrl) return;
        const remark = remarkInput.value.trim();
        if (!remark.length) {
            showRiskToast('Remark is required', 'error');
            return;
        }

        remarkSubmitBtn.disabled = true;

        fetch(activeDoneUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ remark }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                markMarketAsDone(activeMarketId);
                showRiskToast('Market marked as done', 'success');
                closeRemarkModal();
            } else {
                showRiskToast(data.message || 'Unable to mark as done', 'error');
            }
        })
        .catch(() => showRiskToast('Unable to mark as done', 'error'))
        .finally(() => {
            remarkSubmitBtn.disabled = false;
        });
    });

    function updateDoneButtonState(marketId, labels) {
        const allChecked = Object.values(labels).every(Boolean);
        const button = document.querySelector(`.mark-done-button[data-market-id="${marketId}"]`);
        if (!button) return;

        if (button.dataset.isDone === 'true') {
            button.disabled = true;
            button.textContent = 'Completed';
            return;
        }

        button.disabled = !allChecked;
    }

    function markMarketAsDone(marketId) {
        const button = document.querySelector(`.mark-done-button[data-market-id="${marketId}"]`);
        if (!button) return;

        button.disabled = true;
        button.dataset.isDone = 'true';
        button.textContent = 'Completed';
        button.classList.remove('bg-green-100', 'text-green-700', 'dark:bg-green-900/20', 'dark:text-green-300');
        button.classList.add('bg-gray-200', 'text-gray-600', 'dark:bg-gray-700', 'dark:text-gray-300');

        const checkboxes = document.querySelectorAll(`.market-label-checkbox[data-market-id="${marketId}"]`);
        checkboxes.forEach(box => box.disabled = true);
    }

    function showRiskToast(message, type = 'success') {
        if (!toastElement) return;
        toastElement.textContent = message;
        toastElement.className = `risk-toast ${type} show`;
        setTimeout(() => {
            toastElement.classList.remove('show');
        }, 2500);
    }
</script>
@endpush
@endsection

