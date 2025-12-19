@extends('layouts.app')

@section('title', 'Risk Markets')

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

    .filter-field-group {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
    }

    .dark .filter-field-group {
        background-color: rgba(55, 65, 81, 0.6);
        border-color: #4b5563;
    }

    .filter-field-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .dark .filter-field-title {
        color: #e5e7eb;
    }

    .time-range-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .time-input-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .js-time-input-container {
        position: relative;
    }

    .time-input-wrapper {
        position: relative;
        width: 100%;
    }

    .js-time-input-container.open .js-time-dropdown {
        display: block;
    }

    .time-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        width: 100%;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        padding: 1rem;
        z-index: 70;
    }

    .dark .time-dropdown {
        background: #1f2937;
        border-color: #4b5563;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
    }

    .time-dropdown-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .time-dropdown-column {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .time-dropdown-column p {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }

    .dark .time-dropdown-column p {
        color: #9ca3af;
    }

    .time-dropdown-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        max-height: 11rem;
        overflow-y: auto;
    }

    .time-dropdown-options--compact {
        gap: 0.25rem;
    }

    .time-dropdown-options--period {
        gap: 0.5rem;
        flex-wrap: nowrap;
    }

    .time-dropdown-option {
        flex: 0 0 calc(33.33% - 0.35rem);
        padding: 0.5rem 0.4rem;
        background-color: #f3f4f6;
        border-radius: 0.65rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #1f2937;
        border: 1px solid transparent;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
    }

    .time-dropdown-options--compact .time-dropdown-option {
        flex: 0 0 calc(25% - 0.25rem);
    }

    .time-dropdown-options--period .time-dropdown-option {
        flex: 1 1 auto;
        text-transform: uppercase;
    }

    .time-dropdown-option:hover {
        background-color: #e5e7eb;
    }

    .time-dropdown-option.active {
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
    }

    .dark .time-dropdown-option {
        background-color: #374151;
        color: #f3f4f6;
        border-color: #4b5563;
    }

    .dark .time-dropdown-option:hover {
        background-color: #4b5563;
    }

    .dark .time-dropdown-option.active {
        background-color: #2563eb;
        border-color: #2563eb;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    }

    .time-dropdown-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .time-dropdown-action {
        flex: 1;
        padding: 0.55rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
    }

    .time-dropdown-action:hover:not(:disabled) {
        background-color: #e5e7eb;
    }

    .time-dropdown-action.primary {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }

    .time-dropdown-action.primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .dark .time-dropdown-action {
        border-color: #4b5563;
        background-color: #374151;
        color: #f3f4f6;
    }

    .time-input-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }

    .dark .time-input-label {
        color: #9ca3af;
    }

    .time-input-field {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #111827;
        background-color: #ffffff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .time-input-field:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .time-input-field.invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }

    .dark .time-input-field {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }

    .time-input-error {
        font-size: 0.7rem;
        color: #ef4444;
        display: none;
    }

    .time-input-error.active {
        display: block;
    }
</style>
@endpush

@section('content')
@php
    $labelOptions = config('labels.labels', [
        '4x' => '4X',
        'b2c' => 'B2C',
        'b2b' => 'B2B',
        'usdt' => 'USDT',
    ]);

    $activeFilters = [];

    $searchValue = request()->input('search');
    $sportValue = request()->input('sport');
    $riskStatusValue = request()->input('risk_status');
    $statusValue = request()->input('status');
    $dateFromValue = request()->input('date_from');
    $dateToValue = request()->input('date_to');
    $selectedLabels = collect(request()->input('labels', []))
        ->map(fn ($value) => strtolower((string) $value))
        ->filter(fn ($value) => array_key_exists($value, $labelOptions))
        ->unique()
        ->values()
        ->all();

    $hasSearch = request()->has('search') && trim((string) $searchValue) !== '';
    $hasSport = request()->has('sport') && trim((string) $sportValue) !== '';
    $hasRiskStatus = request()->has('risk_status') && trim((string) $riskStatusValue) !== '';
    $hasStatus = request()->has('status') && trim((string) $statusValue) !== '';
    $hasDateFrom = request()->has('date_from') && trim((string) $dateFromValue) !== '';
    $hasDateTo = request()->has('date_to') && trim((string) $dateToValue) !== '';
    $timeFromValue = request()->input('time_from');
    $timeToValue = request()->input('time_to');
    $hasTimeFrom = request()->has('time_from') && trim((string) $timeFromValue) !== '';
    $hasTimeTo = request()->has('time_to') && trim((string) $timeToValue) !== '';

    if ($hasSearch) {
        $activeFilters[] = ['label' => 'Search', 'value' => $searchValue, 'query' => 'search'];
    }
    if ($hasSport) {
        $activeFilters[] = ['label' => 'Sport', 'value' => $sportValue, 'query' => 'sport'];
    }
    if ($hasRiskStatus) {
        $activeFilters[] = ['label' => 'Status', 'value' => ucfirst($riskStatusValue), 'query' => 'risk_status'];
    }
    if ($hasStatus) {
        $statusMap = [4 => 'Settled', 5 => 'Voided'];
        $activeFilters[] = ['label' => 'Market Status', 'value' => $statusMap[$statusValue] ?? $statusValue, 'query' => 'status'];
    }
    if (request('recently_added') == '1') {
        $activeFilters[] = ['label' => 'Recently Added', 'value' => 'Within 30 min', 'query' => 'recently_added'];
    }
    if ($hasDateFrom) {
        $activeFilters[] = ['label' => 'Complete From', 'value' => $dateFromValue . ($hasTimeFrom ? ' ' . $timeFromValue : ''), 'query' => 'date_from'];
    }
    if ($hasDateTo) {
        $activeFilters[] = ['label' => 'Complete To', 'value' => $dateToValue . ($hasTimeTo ? ' ' . $timeToValue : ''), 'query' => 'date_to'];
    }
    if ($hasTimeFrom && !$hasDateFrom) {
        $activeFilters[] = ['label' => 'From Time', 'value' => $timeFromValue, 'query' => 'time_from'];
    }
    if ($hasTimeTo && !$hasDateTo) {
        $activeFilters[] = ['label' => 'To Time', 'value' => $timeToValue, 'query' => 'time_to'];
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
    
    // Group markets by risk_status
    $pendingMarkets = collect($markets->items())->where('risk_status', 'pending')->values();
    $doneMarkets = collect($markets->items())->where('risk_status', 'done')->values();
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Risk Markets</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage pending and completed risk market reviews.</p>
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
                <a href="{{ route('risk.index') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </a>
            @endif
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
                <a href="{{ route('risk.index') }}" class="text-sm text-blue-700 dark:text-blue-300 hover:underline">Clear all</a>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
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
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary['pending']['total'] ?? 0) }}</p>
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Done</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary['done']['total'] ?? 0) }}</p>
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
                    <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(($summary['pending']['settled'] ?? 0) + ($summary['done']['settled'] ?? 0)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Added Toggle -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recently Added</span>
                <form method="GET" action="{{ route('risk.index') }}" id="recentlyAddedForm" class="inline">
                    @foreach(request()->except('recently_added', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $val)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="recently_added" value="1" class="sr-only peer" 
                               @checked(request('recently_added') == '1')
                               onchange="document.getElementById('recentlyAddedForm').submit()">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                    </label>
                </form>
                <span class="text-xs text-gray-500 dark:text-gray-400">(Show markets closing within 30 minutes)</span>
            </div>
        </div>
    </div>

    @if($pendingMarkets->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pending Markets</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event & Market</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport & Tourn.. & Status & Winner</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pendingMarkets as $market)
                            @php
                                $decodedLabels = json_decode($market->labels ?? '{}', true);
                                $labelKeys = array_keys($labelOptions);
                                $defaultLabels = array_fill_keys($labelKeys, false);
                                $labelStates = array_merge($defaultLabels, is_array($decodedLabels) ? array_intersect_key($decodedLabels, $defaultLabels) : []);

                                // Only first 4 labels are required: 4x, b2c, b2b, usdt
                                $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
                                $requiredLabelsChecked = collect($requiredLabelKeys)->every(function($key) use ($labelStates) {
                                    return isset($labelStates[$key]) && (bool) $labelStates[$key] === true;
                                });
                                $isDone = (bool) $market->is_done;
                                $buttonDisabled = !$requiredLabelsChecked || $isDone;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" data-market-row="{{ $market->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        class="mark-done-button inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full transition disabled:opacity-50 disabled:cursor-not-allowed {{ $isDone ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300' }}"
                                        data-market-id="{{ $market->id }}"
                                        data-market-name="{{ $market->marketName }}"
                                        data-done-url="{{ route('risk.markets.done', $market->id) }}"
                                        data-is-done="{{ $isDone ? 'true' : 'false' }}"
                                        @if($buttonDisabled) disabled @endif
                                    >
                                        {{ $isDone ? 'Completed' : 'Pending' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 align-top">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
                                    <div class="mt-2 font-medium">{{ $market->marketName }}</div>
                                    @if(!empty($market->completeTime))
                                        <span class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-200">
                                            Complete: {{ \Carbon\Carbon::parse($market->completeTime)->format('M d, Y h:i A') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 w-fit">
                                            {{ $market->sportName ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 w-fit">
                                            {{ $market->tournamentsName ?? 'N/A' }}
                                        </span>
                                        @php
                                            $statusMap = [
                                                4 => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300'],
                                                5 => ['label' => 'Voided', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300'],
                                            ];
                                            $meta = $statusMap[$market->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $meta['class'] }} w-fit">{{ $meta['label'] }}</span>
                                        @if(!empty($market->selectionName))
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 w-fit">
                                                Winner: {{ $market->selectionName }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="bg-gray-50/60 dark:bg-gray-800/70 text-xs text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700">
                                <td colspan="3" class="px-6 py-3">
                                    <div class="flex flex-wrap items-center gap-6 market-labels-wrapper" data-market-id="{{ $market->id }}" data-update-url="{{ route('risk.markets.labels', $market->id) }}">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Scorecard:</span>
                                        @php
                                            $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
                                            $excludedLabels = ['bookmaker', 'unmatch'];
                                        @endphp
                                        @foreach($labelStates as $key => $value)
                                            @php 
                                                // Skip bookmaker and unmatch labels
                                                if (in_array($key, $excludedLabels)) {
                                                    continue;
                                                }
                                                $checkboxId = "market-option-{$market->id}-{$key}";
                                                $isRequired = in_array($key, $requiredLabelKeys);
                                            @endphp
                                            <label for="{{ $checkboxId }}" class="inline-flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    id="{{ $checkboxId }}"
                                                    class="market-label-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 {{ $isRequired ? 'js-required-label' : '' }}"
                                                    data-market-id="{{ $market->id }}"
                                                    data-label-key="{{ $key }}"
                                                    data-required="{{ $isRequired ? 'true' : 'false' }}"
                                                    @checked((bool) $value)
                                                    @disabled($isDone)
                                                >
                                                <span class="{{ $isRequired ? '' : 'text-gray-500 dark:text-gray-400' }} uppercase">{{ $key }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($doneMarkets->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Completed Markets</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event & Market</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport & Tourn.. & Status & Winner</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remark</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($doneMarkets as $market)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 align-top">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
                                    <div class="mt-2 font-medium">{{ $market->marketName }}</div>
                                    @if(!empty($market->completeTime))
                                        <span class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-200">
                                            Complete: {{ \Carbon\Carbon::parse($market->completeTime)->format('M d, Y h:i A') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 w-fit">
                                            {{ $market->sportName ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 w-fit">
                                            {{ $market->tournamentsName ?? 'N/A' }}
                                        </span>
                                        @php
                                            $statusMap = [
                                                4 => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300'],
                                                5 => ['label' => 'Voided', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300'],
                                            ];
                                            $meta = $statusMap[$market->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $meta['class'] }} w-fit">{{ $meta['label'] }}</span>
                                        @if(!empty($market->selectionName))
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 w-fit">
                                                Winner: {{ $market->selectionName }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <div>{{ $market->name ?: '—' }}</div>
                                    <div class="mt-1">{{ $market->remark ? Str::limit($market->remark, 120) : '—' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($markets->count() === 0 && $pendingMarkets->count() === 0 && $doneMarkets->count() === 0)
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

<div id="risk-filter-overlay" class="filter-overlay" onclick="toggleRiskFilterDrawer(true)"></div>
<div id="risk-filter-drawer" class="filter-drawer" aria-hidden="true">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Filter Risk Markets</h3>
        <button onclick="toggleRiskFilterDrawer(true)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto px-5 py-4">
        <form method="GET" action="{{ route('risk.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="risk_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All (Pending & Done)</option>
                    <option value="pending" @selected(request('risk_status') === 'pending')>Pending Only</option>
                    <option value="done" @selected(request('risk_status') === 'done')>Done Only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Market or event name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <select id="riskSportSelect" name="sport" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Sports</option>
                    @foreach($sports as $sportId => $sportName)
                        <option value="{{ $sportName }}" @selected(request('sport') === $sportName)>{{ $sportName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All</option>
                    <option value="4" @selected(request('status') == 4)>Settled</option>
                    <option value="5" @selected(request('status') == 5)>Voided</option>
                </select>
            </div>
            <div class="filter-field-group">
                <div class="filter-field-title">Complete Date & Time Range</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Complete From (Date)</label>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Complete To (Date)</label>
                        <input type="text" 
                            name="date_to" 
                            value="{{ request('date_to') }}" 
                            placeholder="DD/MM/YYYY"
                            maxlength="10"
                            inputmode="numeric"
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 js-date-input">
                    </div>
                </div>
                
                <!-- Time Range -->
                <div class="time-range-container">
                    <div class="time-input-group js-time-input-container">
                        <span class="time-input-label">From Time</span>
                        <div class="time-input-wrapper">
                            <input type="text" name="time_from" value="{{ request('time_from') ?? '' }}" class="time-input-field js-time-input" placeholder="HH:MM:SS AM/PM" autocomplete="off">
                            <div class="time-dropdown js-time-dropdown">
                                <div class="time-dropdown-grid">
                                    <div class="time-dropdown-column">
                                        <p>Hour</p>
                                        <div class="time-dropdown-options" data-unit="hour">
                                            @for ($i = 1; $i <= 12; $i++)
                                                @php $hourValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="hour" data-value="{{ $hourValue }}">{{ $hourValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>Minute</p>
                                        <div class="time-dropdown-options time-dropdown-options--compact" data-unit="minute">
                                            @for ($i = 0; $i < 60; $i += 5)
                                                @php $minuteValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="minute" data-value="{{ $minuteValue }}">{{ $minuteValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>Second</p>
                                        <div class="time-dropdown-options time-dropdown-options--compact" data-unit="second">
                                            @for ($i = 0; $i < 60; $i += 5)
                                                @php $secondValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="second" data-value="{{ $secondValue }}">{{ $secondValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>AM / PM</p>
                                        <div class="time-dropdown-options time-dropdown-options--period" data-unit="period">
                                            <button type="button" class="time-dropdown-option" data-unit="period" data-value="AM">AM</button>
                                            <button type="button" class="time-dropdown-option" data-unit="period" data-value="PM">PM</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-dropdown-actions">
                                    <button type="button" class="time-dropdown-action js-time-dropdown-clear">Clear</button>
                                    <button type="button" class="time-dropdown-action primary js-time-dropdown-apply" disabled>Apply</button>
                                </div>
                            </div>
                        </div>
                        <p class="time-input-error js-time-error">Please enter time in HH:MM:SS AM/PM format.</p>
                    </div>
                    <div class="time-input-group js-time-input-container">
                        <span class="time-input-label">To Time</span>
                        <div class="time-input-wrapper">
                            <input type="text" name="time_to" value="{{ request('time_to') ?? '' }}" class="time-input-field js-time-input" placeholder="HH:MM:SS AM/PM" autocomplete="off">
                            <div class="time-dropdown js-time-dropdown">
                                <div class="time-dropdown-grid">
                                    <div class="time-dropdown-column">
                                        <p>Hour</p>
                                        <div class="time-dropdown-options" data-unit="hour">
                                            @for ($i = 1; $i <= 12; $i++)
                                                @php $hourValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="hour" data-value="{{ $hourValue }}">{{ $hourValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>Minute</p>
                                        <div class="time-dropdown-options time-dropdown-options--compact" data-unit="minute">
                                            @for ($i = 0; $i < 60; $i += 5)
                                                @php $minuteValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="minute" data-value="{{ $minuteValue }}">{{ $minuteValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>Second</p>
                                        <div class="time-dropdown-options time-dropdown-options--compact" data-unit="second">
                                            @for ($i = 0; $i < 60; $i += 5)
                                                @php $secondValue = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-dropdown-option" data-unit="second" data-value="{{ $secondValue }}">{{ $secondValue }}</button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-dropdown-column">
                                        <p>AM / PM</p>
                                        <div class="time-dropdown-options time-dropdown-options--period" data-unit="period">
                                            <button type="button" class="time-dropdown-option" data-unit="period" data-value="AM">AM</button>
                                            <button type="button" class="time-dropdown-option" data-unit="period" data-value="PM">PM</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-dropdown-actions">
                                    <button type="button" class="time-dropdown-action js-time-dropdown-clear">Clear</button>
                                    <button type="button" class="time-dropdown-action primary js-time-dropdown-apply" disabled>Apply</button>
                                </div>
                            </div>
                        </div>
                        <p class="time-input-error js-time-error">Please enter time in HH:MM:SS AM/PM format.</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Times apply to the selected dates (completeTime).</p>
            </div>
            <div>
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recently Added</span>
                    <div class="relative inline-block w-11 h-6">
                        <input type="checkbox" name="recently_added" value="1" class="sr-only peer" @checked(request('recently_added') == '1')>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                    </div>
                </label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Show markets closing within 30 minutes</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Labels</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($labelOptions as $labelKey => $labelName)
                        @php
                            // Skip bookmaker and unmatch labels
                            if (in_array($labelKey, ['bookmaker', 'unmatch'])) {
                                continue;
                            }
                        @endphp
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="labels[]" value="{{ $labelKey }}" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500" @checked(in_array($labelKey, $selectedLabels))>
                            <span class="uppercase">{{ $labelName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Apply Filters</button>
                <a href="{{ route('risk.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>
</div>

<div id="remarkModalOverlay" class="remark-modal-overlay"></div>
<div id="remarkModal" class="remark-modal">
    <div class="remark-modal__content">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Add Remark</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="remarkModalMarketName"></p>
        <div class="space-y-4">
            <div>
                <label for="nameInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Checker Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nameInput" value="{{ auth()->user()->name }}" readonly class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 cursor-not-allowed" required>
            </div>
            <div>
                <label for="chorIdInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Chor ID <span class="text-red-500">*</span>
                </label>
                <textarea id="chorIdInput" class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500" rows="3" placeholder="Enter Chor ID..." required></textarea>
            </div>
            <div>
                <label for="remarkInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Remark <span class="text-red-500">*</span>
                </label>
                <textarea id="remarkInput" class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500" rows="4" placeholder="Add remark..." required></textarea>
            </div>
            <div>
                <label for="webPinInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Web PIN <span class="text-red-500">*</span>
                </label>
                <input type="password" id="webPinInput" class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500" placeholder="Enter your web PIN..." autocomplete="off" required>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-3">
            <button id="remarkCancelBtn" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
            <button id="remarkSubmitBtn" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700">Submit</button>
        </div>
    </div>
</div>

<div id="riskToast" class="risk-toast"></div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            // If removing date_from, also remove time_from
            if (param === 'date_from') {
                url.searchParams.delete('time_from');
            }
            // If removing date_to, also remove time_to
            if (param === 'date_to') {
                url.searchParams.delete('time_to');
            }
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
    const nameInput = document.getElementById('nameInput');
    const chorIdInput = document.getElementById('chorIdInput');
    const webPinInput = document.getElementById('webPinInput');
    const remarkMarketName = document.getElementById('remarkModalMarketName');
    const remarkCancelBtn = document.getElementById('remarkCancelBtn');
    const remarkSubmitBtn = document.getElementById('remarkSubmitBtn');
    const toastElement = document.getElementById('riskToast');
    let activeMarketId = null;
    let activeDoneUrl = null;

    function openRemarkModal(marketId, marketName, doneUrl) {
        activeMarketId = marketId;
        activeMarketName = marketName;
        activeDoneUrl = doneUrl;
        remarkMarketName.textContent = `Market: ${marketName}`;
        remarkInput.value = '';
        nameInput.value = '{{ auth()->user()->name }}';
        chorIdInput.value = '';
        webPinInput.value = '';
        remarkModal.classList.add('active');
        remarkOverlay.classList.add('active');
    }

    function closeRemarkModal() {
        activeMarketId = null;
        activeDoneUrl = null;
        remarkInput.value = '';
        nameInput.value = '';
        chorIdInput.value = '';
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

    let activeMarketName = null;

    remarkSubmitBtn.addEventListener('click', () => {
        if (!activeMarketId || !activeDoneUrl) return;
        const remark = remarkInput.value.trim();
        const name = nameInput.value.trim();
        const chorId = chorIdInput.value.trim();
        const webPin = webPinInput.value.trim();
        
        if (!name.length) {
            showRiskToast('Checker Name is required', 'error');
            nameInput.focus();
            return;
        }
        
        if (!chorId.length) {
            showRiskToast('Chor ID is required', 'error');
            chorIdInput.focus();
            return;
        }
        
        if (!remark.length) {
            showRiskToast('Remark is required', 'error');
            remarkInput.focus();
            return;
        }

        if (!webPin.length) {
            showRiskToast('Web PIN is required', 'error');
            webPinInput.focus();
            return;
        }

        remarkSubmitBtn.disabled = true;

        // Submit with web_pin
        fetch(activeDoneUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ remark, name, chor_id: chorId, web_pin: webPin }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                markMarketAsDone(activeMarketId);
                showRiskToast('Market marked as done', 'success');
                closeRemarkModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showRiskToast(data.message || 'Unable to mark as done', 'error');
                webPinInput.value = '';
                webPinInput.focus();
            }
        })
        .catch(() => {
            showRiskToast('Unable to mark as done', 'error');
            webPinInput.value = '';
            webPinInput.focus();
        })
        .finally(() => {
            remarkSubmitBtn.disabled = false;
        });
    });

    function updateDoneButtonState(marketId, labels) {
        // Only first 4 labels are required: 4x, b2c, b2b, usdt
        const requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
        const allRequiredChecked = requiredLabelKeys.every(key => labels[key] === true);
        const button = document.querySelector(`.mark-done-button[data-market-id="${marketId}"]`);
        if (!button) return;

        if (button.dataset.isDone === 'true') {
            button.disabled = true;
            button.textContent = 'Completed';
            return;
        }

        button.disabled = !allRequiredChecked;
    }

    function markMarketAsDone(marketId) {
        const button = document.querySelector(`.mark-done-button[data-market-id="${marketId}"]`);
        if (!button) return;

        button.disabled = true;
        button.dataset.isDone = 'true';
        button.textContent = 'Completed';
        button.classList.remove('bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900/20', 'dark:text-yellow-300');
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

    // Date and Time Picker Functions (from market-rates)
    function formatTimeValue(rawValue) {
        if (!rawValue) return '';
        const cleaned = rawValue.toUpperCase().trim();
        const match = cleaned.match(/^(\d{1,2}):(\d{2}):(\d{2})\s*(AM|PM)$/);
        if (!match) return null;
        let hour = parseInt(match[1], 10);
        const minute = parseInt(match[2], 10);
        const second = parseInt(match[3], 10);
        const period = match[4].toUpperCase();
        if (hour < 1 || hour > 12 || isNaN(minute) || minute < 0 || minute > 59 || isNaN(second) || second < 0 || second > 59) return null;
        return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:${String(second).padStart(2, '0')} ${period}`;
    }

    function formatPartialTime(rawValue) {
        if (!rawValue) return '';
        const upperRaw = rawValue.toUpperCase();
        const digits = upperRaw.replace(/[^0-9]/g, '').slice(0, 6);
        const hour = digits.slice(0, 2);
        const minute = digits.slice(2, 4);
        const second = digits.slice(4, 6);
        let result = '';
        if (hour.length) {
            result += hour;
            if (hour.length === 2) result += ':';
        }
        if (minute.length) {
            result += minute;
            if (minute.length === 2) result += ':';
        }
        if (second.length) result += second;
        let suffix = '';
        const suffixRaw = upperRaw.replace(/[^APM]/g, '');
        if (suffixRaw.startsWith('PM')) suffix = 'PM';
        else if (suffixRaw.startsWith('AM')) suffix = 'AM';
        else if (suffixRaw.startsWith('P')) suffix = 'P';
        else if (suffixRaw.startsWith('A')) suffix = 'A';
        if (suffix) {
            let trimmedResult = result.trim();
            while (trimmedResult.endsWith(':')) trimmedResult = trimmedResult.slice(0, -1);
            result = trimmedResult ? `${trimmedResult} ${suffix}` : suffix;
        }
        return result;
    }

    function getTimeTokenCount(value, caretIndex) {
        if (!value || caretIndex <= 0) return 0;
        const preview = value.slice(0, caretIndex);
        return preview.replace(/[^0-9APMapm]/g, '').length;
    }

    function setCaretFromTokenCount(input, tokenCount) {
        if (!input) return;
        if (!tokenCount) {
            input.setSelectionRange(0, 0);
            return;
        }
        const value = input.value;
        let seen = 0;
        let position = value.length;
        for (let i = 0; i < value.length; i++) {
            const char = value[i];
            if (/[0-9APM]/.test(char)) {
                seen++;
                if (seen === tokenCount) {
                    position = i + 1;
                    break;
                }
            }
        }
        input.setSelectionRange(position, position);
    }

    function formatDateInputValue(raw) {
        const digits = (raw || '').replace(/[^0-9]/g, '').slice(0, 8);
        let formatted = '';
        if (digits.length >= 2) formatted = digits.slice(0, 2);
        else formatted = digits;
        if (digits.length >= 3) formatted += '/' + digits.slice(2, 4);
        else if (digits.length > 2) formatted += '/' + digits.slice(2);
        if (digits.length >= 5) formatted += '/' + digits.slice(4, 8);
        else if (digits.length > 4) formatted += '/' + digits.slice(4);
        return formatted;
    }

    function getDateTokenCount(value, caretIndex) {
        if (!value || caretIndex <= 0) return 0;
        const preview = value.slice(0, caretIndex);
        return preview.replace(/[^0-9]/g, '').length;
    }

    function setDateCaretFromTokenCount(input, tokenCount) {
        if (!input) return;
        if (!tokenCount) {
            input.setSelectionRange(0, 0);
            return;
        }
        const value = input.value;
        let seen = 0;
        let position = value.length;
        for (let i = 0; i < value.length; i++) {
            if (/[0-9]/.test(value[i])) {
                seen++;
                if (seen === tokenCount) {
                    position = i + 1;
                    break;
                }
            }
        }
        input.setSelectionRange(position, position);
    }

    function isValidDateValue(value) {
        const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!match) return false;
        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);
        if (month < 1 || month > 12 || day < 1 || day > 31) return false;
        const date = new Date(year, month - 1, day);
        return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day;
    }

    function handleDateInput(event) {
        const input = event.target;
        if (typeof input.__lastValidDateValue === 'undefined') {
            input.__lastValidDateValue = formatDateInputValue(input.value);
            input.__lastValidDateTokens = getDateTokenCount(input.__lastValidDateValue, (input.__lastValidDateValue || '').length);
        }
        const rawValue = input.value;
        const caretIndex = input.selectionStart || 0;
        const caretTokenCount = getDateTokenCount(rawValue, caretIndex);
        const formatted = formatDateInputValue(rawValue);
        input.value = formatted;
        setDateCaretFromTokenCount(input, caretTokenCount);
        const digits = formatted.replace(/[^0-9]/g, '');
        const dayValue = digits.slice(0, 2);
        const monthValue = digits.slice(2, 4);
        const lastChar = rawValue.charAt(caretIndex - 1);
        const maybeInvalid = (monthValue.length === 2 && (parseInt(monthValue, 10) < 1 || parseInt(monthValue, 10) > 12)) || (dayValue.length === 2 && (parseInt(dayValue, 10) < 1 || parseInt(dayValue, 10) > 31));
        if (maybeInvalid && /\d/.test(lastChar)) {
            input.value = input.__lastValidDateValue || '';
            setDateCaretFromTokenCount(input, input.__lastValidDateTokens || 0);
            return;
        }
        input.__lastValidDateValue = input.value;
        input.__lastValidDateTokens = getDateTokenCount(input.value, input.selectionStart || input.value.length);
    }

    function handleDateBlur(event) {
        const input = event.target;
        const value = input.value;
        if (value && !isValidDateValue(value)) {
            input.value = '';
            input.__lastValidDateValue = '';
            input.__lastValidDateTokens = 0;
            return;
        }
        input.__lastValidDateValue = input.value;
        input.__lastValidDateTokens = getDateTokenCount(input.value, input.value.length);
    }

    function setupTimeInputs() {
        if (!window.__timeDropdownOutsideHandler) {
            document.addEventListener('click', event => {
                document.querySelectorAll('.js-time-input-container.open').forEach(container => {
                    if (!container.contains(event.target)) {
                        container.classList.remove('open');
                        const inputEl = container.querySelector('.js-time-input');
                        if (inputEl) inputEl.dispatchEvent(new Event('blur'));
                    }
                });
            });
            window.__timeDropdownOutsideHandler = true;
        }

        document.querySelectorAll('.js-time-input-container').forEach(container => {
            const input = container.querySelector('.js-time-input');
            const dropdown = container.querySelector('.js-time-dropdown');
            const errorEl = container.querySelector('.js-time-error');
            const optionButtons = dropdown ? dropdown.querySelectorAll('.time-dropdown-option') : [];
            const applyBtn = dropdown ? dropdown.querySelector('.js-time-dropdown-apply') : null;
            const clearBtn = dropdown ? dropdown.querySelector('.js-time-dropdown-clear') : null;

            if (!input) return;

            const state = {
                hour: '', minute: '', second: '', period: '',
                lastValidValue: input.value || '',
                lastValidTokenCount: getTimeTokenCount(input.value || '', (input.value || '').length)
            };

            const setLastValid = (value, caretTokens = null) => {
                state.lastValidValue = value || '';
                state.lastValidTokenCount = typeof caretTokens === 'number' ? Math.max(0, caretTokens) : getTimeTokenCount(state.lastValidValue, state.lastValidValue.length);
            };

            const resetState = () => {
                state.hour = ''; state.minute = ''; state.second = ''; state.period = '';
            };

            const showError = () => {
                input.classList.add('invalid');
                if (errorEl) errorEl.classList.add('active');
            };

            const hideError = () => {
                input.classList.remove('invalid');
                if (errorEl) errorEl.classList.remove('active');
            };

            const isStateComplete = () => state.hour && state.minute && state.second && state.period;

            const updateApplyButton = () => {
                if (applyBtn) applyBtn.disabled = !isStateComplete();
            };

            const updateActiveOptions = () => {
                optionButtons.forEach(btn => {
                    const unit = btn.dataset.unit;
                    const value = btn.dataset.value;
                    btn.classList.toggle('active', state[unit] === value);
                });
                updateApplyButton();
            };

            const syncStateFromInput = () => {
                resetState();
                const formatted = formatTimeValue(input.value);
                if (formatted) {
                    const [timePart, period] = formatted.split(' ');
                    const [hour, minute, second] = timePart.split(':');
                    state.hour = hour || ''; state.minute = minute || ''; state.second = second || ''; state.period = period || '';
                }
                updateActiveOptions();
                setLastValid(input.value, getTimeTokenCount(input.value, input.value.length));
            };

            input.addEventListener('input', event => {
                const rawValue = input.value;
                const rawCaret = event.target.selectionStart || 0;
                const caretTokenCount = getTimeTokenCount(rawValue, rawCaret);
                const formatted = formatPartialTime(rawValue);
                input.value = formatted;
                setCaretFromTokenCount(input, caretTokenCount);
                hideError();
                const tokens = input.value.replace(/[^0-9]/g, '');
                const currentHour = tokens.slice(0, 2);
                const currentMinute = tokens.slice(2, 4);
                const currentSecond = tokens.slice(4, 6);
                const lastChar = rawValue.charAt(rawCaret - 1);
                const maybeInvalid = (currentHour.length === 2 && parseInt(currentHour, 10) > 12) || (currentMinute.length === 2 && parseInt(currentMinute, 10) > 59) || (currentSecond.length === 2 && parseInt(currentSecond, 10) > 59);
                if (maybeInvalid) {
                    const isDigit = /\d/.test(lastChar);
                    if (isDigit) {
                        input.value = state.lastValidValue || '';
                        setCaretFromTokenCount(input, state.lastValidTokenCount || 0);
                    }
                    return;
                }
                setLastValid(input.value, getTimeTokenCount(input.value, input.selectionStart || input.value.length));
                if (dropdown && container.classList.contains('open')) {
                    const normalized = formatTimeValue(input.value);
                    if (normalized) {
                        const [timePart, period] = normalized.split(' ');
                        const [hour, minute, second] = timePart.split(':');
                        state.hour = hour; state.minute = minute; state.second = second; state.period = period;
                    } else resetState();
                    updateActiveOptions();
                }
            });

            input.addEventListener('focus', () => {
                if (dropdown) {
                    container.classList.add('open');
                    syncStateFromInput();
                }
            });

            input.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    container.classList.remove('open');
                    input.blur();
                }
            });

            if (dropdown) {
                dropdown.addEventListener('mousedown', event => event.preventDefault());
                optionButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const unit = btn.dataset.unit;
                        const value = btn.dataset.value;
                        if (!unit) return;
                        state[unit] = value;
                        updateActiveOptions();
                    });
                });

                if (applyBtn) {
                    applyBtn.addEventListener('click', () => {
                        if (!isStateComplete()) {
                            showError();
                            return;
                        }
                        const combinedValue = `${state.hour}:${state.minute}:${state.second} ${state.period}`;
                        input.value = combinedValue;
                        hideError();
                        setLastValid(input.value, getTimeTokenCount(input.value, input.value.length));
                        container.classList.remove('open');
                        input.dispatchEvent(new Event('blur'));
                    });
                }

                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        resetState();
                        updateActiveOptions();
                        input.value = '';
                        hideError();
                        setLastValid('', 0);
                        container.classList.remove('open');
                    });
                }
            }

            input.addEventListener('blur', () => {
                setTimeout(() => {
                    if (container.classList.contains('open')) return;
                    const formatted = formatTimeValue(input.value);
                    if (input.value.trim() === '') {
                        hideError();
                        resetState();
                        updateActiveOptions();
                        return;
                    }
                    if (!formatted) {
                        showError();
                        return;
                    }
                    input.value = formatted;
                    hideError();
                    syncStateFromInput();
                    setLastValid(input.value, getTimeTokenCount(input.value, input.value.length));
                }, 120);
            });
        });
    }

    // Initialize date and time pickers on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupTimeInputs();
        
        document.querySelectorAll('.js-date-input').forEach(input => {
            input.value = formatDateInputValue(input.value);
            input.__lastValidDateValue = input.value || '';
            input.__lastValidDateTokens = getDateTokenCount(input.value || '', (input.value || '').length);
            input.addEventListener('input', handleDateInput);
            input.addEventListener('blur', handleDateBlur);

            if (typeof flatpickr !== 'undefined') {
                flatpickr(input, {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: input.value || null,
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr) {
                        input.value = dateStr;
                        input.__lastValidDateValue = dateStr;
                        input.__lastValidDateTokens = getDateTokenCount(dateStr, dateStr.length);
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection

