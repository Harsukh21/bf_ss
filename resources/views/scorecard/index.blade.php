@extends('layouts.app')

@section('title', 'Scorecard')

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

    /* Custom scrollbar for Market Old Limits */
    .market-limits-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .market-limits-scroll::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 3px;
    }

    .market-limits-scroll::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }

    .market-limits-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.7);
    }

    .dark .market-limits-scroll::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.5);
    }

    .dark .market-limits-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(75, 85, 99, 0.7);
    }

    /* Blinking highlight animation for Market Old Limits - Red Color */
    @keyframes highlightBlink {
        0%, 100% {
            background-color: rgba(239, 68, 68, 0.1);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
        }
        50% {
            background-color: rgba(239, 68, 68, 0.2);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
        }
    }

    .highlight-blink {
        animation: highlightBlink 2s ease-in-out infinite;
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
    }

    .dark .highlight-blink {
        animation: highlightBlinkDark 2s ease-in-out infinite;
        border-color: rgba(239, 68, 68, 0.4);
    }

    @keyframes highlightBlinkDark {
        0%, 100% {
            background-color: rgba(239, 68, 68, 0.15);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
        }
        50% {
            background-color: rgba(239, 68, 68, 0.25);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.3);
        }
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

    /* Event Modal Styles */
    .event-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease;
        z-index: 1050;
    }

    .event-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .event-modal {
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

    .event-modal.active {
        pointer-events: auto;
        opacity: 1;
        visibility: visible;
    }

    .event-modal__content {
        width: 100%;
        max-width: 600px;
        background: #fff;
        border-radius: 0.75rem;
        padding: 1rem;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
        max-height: 90vh;
        overflow-y: auto;
    }

    .dark .event-modal__content {
        background: #1f2937;
    }

    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: #3b82f6;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .dark .toggle-slider {
        background-color: #475569;
    }
</style>
@endpush

@section('content')
@php
    $activeFilters = [];
    $searchValue = request()->input('search');
    $sportValue = request()->input('sport');
    $tournamentValue = request()->input('tournament');
    $dateFromValue = request()->input('date_from');
    $dateToValue = request()->input('date_to');
    $interruptedStatusValue = request()->input('interrupted_status');
    $labelsValue = request()->input('labels', []);

    $hasSearch = request()->has('search') && trim((string) $searchValue) !== '';
    $hasSport = request()->has('sport') && trim((string) $sportValue) !== '';
    $hasTournament = request()->has('tournament') && trim((string) $tournamentValue) !== '';
    $hasDateFrom = request()->has('date_from') && trim((string) $dateFromValue) !== '';
    $hasDateTo = request()->has('date_to') && trim((string) $dateToValue) !== '';
    $hasInterruptedStatus = request()->has('interrupted_status') && in_array($interruptedStatusValue, ['on', 'off']);
    $hasLabels = request()->has('labels') && is_array($labelsValue) && !empty(array_filter($labelsValue, function($v) { return $v === '1' || $v === 'true' || $v === true; }));

    if ($hasSearch) {
        $activeFilters[] = ['label' => 'Search', 'value' => $searchValue, 'query' => 'search'];
    }
    if ($hasSport) {
        $activeFilters[] = ['label' => 'Sport', 'value' => $sportValue, 'query' => 'sport'];
    }
    if ($hasTournament) {
        $activeFilters[] = ['label' => 'Tournament', 'value' => $tournamentValue, 'query' => 'tournament'];
    }
    if ($hasDateFrom) {
        $activeFilters[] = ['label' => 'Date From', 'value' => $dateFromValue, 'query' => 'date_from'];
    }
    if ($hasDateTo) {
        $activeFilters[] = ['label' => 'Date To', 'value' => $dateToValue, 'query' => 'date_to'];
    }
    if ($hasInterruptedStatus) {
        $statusLabel = $interruptedStatusValue === 'on' ? 'Interrupted: ON' : 'Interrupted: OFF';
        $activeFilters[] = ['label' => $statusLabel, 'value' => '', 'query' => 'interrupted_status'];
    }
    if ($hasLabels) {
        $labelConfig = config('labels.labels', []);
        $selectedLabelNames = [];
        foreach ($labelsValue as $key => $value) {
            if ($value === '1' || $value === 'true' || $value === true) {
                $labelName = $labelConfig[$key] ?? strtoupper($key);
                $selectedLabelNames[] = $labelName;
            }
        }
        if (!empty($selectedLabelNames)) {
            $activeFilters[] = ['label' => 'Labels', 'value' => implode(', ', $selectedLabelNames), 'query' => 'labels'];
        }
    }

    $filterCount = count($activeFilters);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Scorecard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Events with in-play markets.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap justify-end">
            <button onclick="toggleScorecardFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 13.414V19l-6 2v-7.586L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
                @if($filterCount)
                    <span class="ml-2 text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $filterCount }}</span>
                @endif
            </button>
            @if($filterCount)
                <a href="{{ route('scorecard.index') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
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
                <a href="{{ route('scorecard.index') }}" class="text-sm text-blue-700 dark:text-blue-300 hover:underline">Clear all</a>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($activeFilters as $filter)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        {{ $filter['label'] }}: {{ $filter['value'] }}
                        <button onclick="removeScorecardFilter('{{ $filter['query'] }}')" class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100">&times;</button>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if($events->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Interrupted</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport / Tournament</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">In-Play Markets</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="js-event-toggle" data-event-id="{{ $event->id }}" data-event-name="{{ $event->eventName }}" {{ ($event->is_interrupted ?? false) ? 'checked' : '' }} data-event-data="{{ json_encode([
                                            'eventId' => $event->eventId,
                                            'exEventId' => $event->exEventId,
                                            'eventName' => $event->eventName,
                                            'sportName' => $event->sportName,
                                            'tournamentsName' => $event->tournamentsName,
                                            'inplay_markets_count' => $event->inplay_markets_count,
                                            'formatted_market_time' => $event->formatted_market_time,
                                            'is_interrupted' => $event->is_interrupted ?? false,
                                            'remind_me_after' => $event->remind_me_after ?? null,
                                            'market_old_limits' => $event->market_old_limits ?? [],
                                        ]) }}">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-2">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $event->eventName }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            ID: {{ $event->eventId }}
                                        </div>
                                        @if($event->formatted_market_time)
                                            <div>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200">
                                                    {{ $event->formatted_market_time }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200">
                                            {{ $event->sportName }}
                                        </span>
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $event->tournamentsName }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                                            {{ $event->inplay_markets_count }} Market(s)
                                        </span>
                                        @if(!empty($event->sc_type))
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-200">
                                                {{ $event->sc_type }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <!-- Labels Row with Market Old Limits and Remind Me After -->
                            @php
                                // Define required labels (first 4)
                                $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
                                
                                // Count how many required labels are checked
                                $requiredCheckedCount = 0;
                                foreach ($requiredLabelKeys as $labelKey) {
                                    if (isset($event->labels[$labelKey]) && (bool)$event->labels[$labelKey] === true) {
                                        $requiredCheckedCount++;
                                    }
                                }
                                $allRequiredChecked = $requiredCheckedCount === count($requiredLabelKeys);
                                $isInterrupted = $event->is_interrupted ?? false;
                                $hasMarketLimits = !empty($event->market_old_limits ?? []);
                                $hasReminder = !empty($event->remind_me_after ?? null);
                                
                                // Always show row - required labels container will be hidden if all checked, but optional labels and market limits should still be visible
                                $showRow = true;
                            @endphp
                            @if($showRow)
                            <tr class="js-labels-row bg-gray-50/60 dark:bg-gray-800/70 text-xs text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700" data-event-id="{{ $event->exEventId }}" data-sc-type="{{ $event->sc_type ?? '' }}">
                                <td colspan="4" class="px-6 py-3">
                                    <div class="flex flex-wrap items-center justify-between gap-6">
                                        <!-- Labels Section (Left Side) -->
                                        <div class="flex flex-wrap items-center gap-6 js-labels-container">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Scorecard:</span>
                                            
                                            <!-- Required Labels (Hide if all checked) -->
                                            <div class="flex flex-wrap items-center gap-6 js-required-labels-container" style="{{ $allRequiredChecked ? 'display: none;' : '' }}">
                                                @foreach($labelConfig as $labelKey => $labelName)
                                                    @php
                                                        $isRequired = in_array($labelKey, $requiredLabelKeys);
                                                        if (!$isRequired) continue; // Skip optional labels here
                                                        $labelChecked = isset($event->labels[$labelKey]) && (bool)$event->labels[$labelKey] === true;
                                                        $labelTimestamp = isset($event->label_timestamps[$labelKey]) ? $event->label_timestamps[$labelKey] : null;
                                                        $formattedTimestamp = $labelTimestamp ? \Carbon\Carbon::parse($labelTimestamp)->format('M d, h:i A') : null;
                                                    @endphp
                                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            class="js-label-checkbox js-required-label rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                                            data-event-id="{{ $event->exEventId }}"
                                                            data-label-key="{{ $labelKey }}"
                                                            data-required="true"
                                                            @checked($labelChecked)
                                                        >
                                                        <span>{{ $labelName }}</span>
                                                        @if($labelChecked && $formattedTimestamp)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400" title="Checked at: {{ $formattedTimestamp }}">({{ $formattedTimestamp }})</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                            
                                            <!-- Optional Labels (Always visible) -->
                                            <div class="flex flex-wrap items-center gap-6 js-optional-labels-container">
                                                @foreach($labelConfig as $labelKey => $labelName)
                                                    @php
                                                        $isRequired = in_array($labelKey, $requiredLabelKeys);
                                                        if ($isRequired) continue; // Skip required labels here
                                                        $labelChecked = isset($event->labels[$labelKey]) && (bool)$event->labels[$labelKey] === true;
                                                        $labelTimestamp = isset($event->label_timestamps[$labelKey]) ? $event->label_timestamps[$labelKey] : null;
                                                        $formattedTimestamp = $labelTimestamp ? \Carbon\Carbon::parse($labelTimestamp)->format('M d, h:i A') : null;
                                                    @endphp
                                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            class="js-label-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                                            data-event-id="{{ $event->exEventId }}"
                                                            data-label-key="{{ $labelKey }}"
                                                            data-required="false"
                                                            @checked($labelChecked)
                                                        >
                                                        <span>{{ $labelName }}</span>
                                                        @if($labelChecked && $formattedTimestamp)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400" title="Checked at: {{ $formattedTimestamp }}">({{ $formattedTimestamp }})</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <!-- Market Old Limits and Remind Me After (Right Side - Only show when interrupted) -->
                                        @if($isInterrupted)
                                        <div class="flex flex-wrap items-center gap-3 highlight-blink">
                                            <!-- Market Old Limits -->
                                            @if(!empty($event->market_old_limits ?? []))
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400 whitespace-nowrap">Market Old Limits:</span>
                                                @foreach($event->market_old_limits as $market)
                                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600 flex-shrink-0">
                                                        <span class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $market->marketName }}:</span>
                                                        <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $market->old_limit }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            <!-- Remind Me After -->
                                            @if($event->remind_me_after ?? null)
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400 whitespace-nowrap">Remind Me After:</span>
                                                <span class="px-2.5 py-1 bg-white dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600 text-xs font-semibold text-primary-600 dark:text-primary-400 whitespace-nowrap">
                                                    {{ $event->remind_me_after }} minutes
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $events->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No in-play events</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($filterCount)
                    No events found for the selected filters.
                @else
                    There are currently no events with in-play markets.
                @endif
            </p>
        </div>
    @endif
</div>

<!-- Filter Overlay and Drawer -->
<div id="scorecard-filter-overlay" class="filter-overlay" onclick="toggleScorecardFilterDrawer(true)"></div>
<div id="scorecard-filter-drawer" class="filter-drawer" aria-hidden="true">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Filter Scorecard</h3>
        <button onclick="toggleScorecardFilterDrawer(true)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto px-5 py-4">
        <form method="GET" action="{{ route('scorecard.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Event or tournament name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tournament</label>
                <select name="tournament" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Tournaments</option>
                    @foreach($tournaments as $tournament)
                        <option value="{{ $tournament }}" @selected(request('tournament') === $tournament)>{{ $tournament }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field-group">
                <div class="filter-field-title">Date Range</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
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
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interrupted Status</label>
                <select name="interrupted_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All</option>
                    <option value="on" @selected(request('interrupted_status') === 'on')>Interrupted (ON)</option>
                    <option value="off" @selected(request('interrupted_status') === 'off')>Not Interrupted (OFF)</option>
                </select>
            </div>
            <div class="filter-field-group">
                <div class="filter-field-title">Labels</div>
                <div class="space-y-2">
                    @php
                        $labelConfig = config('labels.labels', []);
                        $selectedLabels = request()->input('labels', []);
                    @endphp
                    @foreach($labelConfig as $labelKey => $labelName)
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="labels[{{ $labelKey }}]" 
                                   value="1" 
                                   @checked(isset($selectedLabels[$labelKey]) && ($selectedLabels[$labelKey] === '1' || $selectedLabels[$labelKey] === 'true' || $selectedLabels[$labelKey] === true))
                                   class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $labelName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Apply Filters</button>
                <a href="{{ route('scorecard.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModalOverlay" class="event-modal-overlay"></div>
<div id="eventModal" class="event-modal">
    <div class="event-modal__content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Event Details</h3>
            <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="eventModalContent" class="space-y-4">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="mt-3 flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeEventModal()" class="px-3 py-1.5 text-sm rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Cancel
            </button>
            <button onclick="saveEventModal()" class="px-3 py-1.5 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                Save
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Filter Drawer Toggle
    function toggleScorecardFilterDrawer(forceClose = null) {
        const drawer = document.getElementById('scorecard-filter-drawer');
        const overlay = document.getElementById('scorecard-filter-overlay');
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
        drawer.setAttribute('aria-hidden', !shouldOpen);
    }

    // Remove Filter
    function removeScorecardFilter(param) {
        const url = new URL(window.location.href);
        
        // Handle labels filter specially (it's an array)
        if (param === 'labels') {
            // Remove all label parameters
            const params = new URLSearchParams(url.search);
            params.delete('labels');
            // Remove individual label parameters like labels[4x], etc.
            for (const key of params.keys()) {
                if (key.startsWith('labels[')) {
                    params.delete(key);
                }
            }
            url.search = params.toString();
        } else {
            url.searchParams.delete(param);
        }
        
        url.searchParams.delete('page'); // Reset to page 1
        window.location.href = url.toString();
    }

    // Scorecard Confirmation Modal (moved from app.blade.php to avoid loading on every page)
    function showScorecardConfirm(message, confirmText = 'Confirm Turn Off Interruption', cancelText = 'Cancel') {
        return new Promise((resolve) => {
            // Create a full-screen overlay for the confirmation modal
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4';
            overlay.style.transition = 'opacity 0.3s ease-in-out';
            overlay.style.opacity = '0';

            // Create the modal container
            const modal = document.createElement('div');
            modal.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 scale-95';
            modal.style.opacity = '0';
            
            modal.innerHTML = `
                <div class="flex flex-col p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmation Required</h3>
                        </div>
                        <button onclick="handleScorecardConfirm(false, this.closest('.scorecard-confirm-overlay'))" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300">${message}</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="handleScorecardConfirm(false, this.closest('.scorecard-confirm-overlay'))" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            ${cancelText}
                        </button>
                        <button onclick="handleScorecardConfirm(true, this.closest('.scorecard-confirm-overlay'))" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;

            overlay.classList.add('scorecard-confirm-overlay');
            overlay.appendChild(modal);
            document.body.appendChild(overlay);

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';

            // Animate in
            setTimeout(() => {
                overlay.style.opacity = '1';
                modal.style.opacity = '1';
                modal.style.transform = 'scale(1)';
            }, 10);

            // Close on overlay click (outside modal)
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    handleScorecardConfirm(false, overlay);
                }
            });

            // Close on Escape key
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    handleScorecardConfirm(false, overlay);
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);

            // Store resolve function on overlay
            overlay.resolve = resolve;
        });
    }

    // Handle scorecard confirmation responses
    function handleScorecardConfirm(result, overlayElement) {
        if (overlayElement && overlayElement.resolve) {
            overlayElement.resolve(result);
            
            // Animate out
            const modal = overlayElement.querySelector('div[class*="bg-white"], div[class*="bg-gray-800"]');
            if (modal) {
                modal.style.opacity = '0';
                modal.style.transform = 'scale(0.95)';
            }
            overlayElement.style.opacity = '0';
            
            setTimeout(() => {
                // Re-enable body scroll
                document.body.style.overflow = '';
                overlayElement.remove();
            }, 300);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr for date inputs
        const dateInputs = document.querySelectorAll('.js-date-input');
        dateInputs.forEach(input => {
            flatpickr(input, {
                dateFormat: 'd/m/Y',
                allowInput: true,
                parseDate: (datestr, format) => {
                    // Parse DD/MM/YYYY format
                    const parts = datestr.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10) - 1;
                        const year = parseInt(parts[2], 10);
                        return new Date(year, month, day);
                    }
                    return null;
                },
            });
        });

        // Handle label checkbox changes - update labels in events table
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('js-label-checkbox')) {
                const checkbox = e.target;
                const exEventId = checkbox.getAttribute('data-event-id');
                const labelKey = checkbox.getAttribute('data-label-key');
                const checked = checkbox.checked;
                
                // Find the labels row for this event
                const labelsRow = document.querySelector(`tr.js-labels-row[data-event-id="${exEventId}"]`);
                if (!labelsRow) return;
                
                // Get all labels for this event
                const eventLabels = {};
                const eventCheckboxes = labelsRow.querySelectorAll('.js-label-checkbox');
                let requiredCheckedCount = 0;
                let totalRequiredLabels = 0;
                
                eventCheckboxes.forEach(cb => {
                    const key = cb.getAttribute('data-label-key');
                    const isRequired = cb.getAttribute('data-required') === 'true';
                    eventLabels[key] = cb.checked;
                    if (isRequired) {
                        totalRequiredLabels++;
                        if (cb.checked) {
                            requiredCheckedCount++;
                        }
                    }
                });
                
                // Hide only required labels container if all required checkboxes are checked
                const requiredLabelsContainer = labelsRow.querySelector('.js-required-labels-container');
                const optionalLabelsContainer = labelsRow.querySelector('.js-optional-labels-container');
                const labelsContainer = labelsRow.querySelector('.js-labels-container');
                const marketLimitsSection = labelsRow.querySelector('.flex.flex-wrap.items-center.gap-3');
                
                const allRequiredChecked = requiredCheckedCount === totalRequiredLabels;
                
                // Hide/show required labels container based on whether all required are checked
                if (allRequiredChecked) {
                    // Hide only the required labels container
                    if (requiredLabelsContainer) {
                        requiredLabelsContainer.style.display = 'none';
                    }
                    // Keep optional labels visible
                    if (optionalLabelsContainer) {
                        optionalLabelsContainer.style.display = '';
                    }
                    
                    // If no Market Old Limits or Reminder section, and no optional labels checked, hide entire row
                    const hasOptionalChecked = Array.from(optionalLabelsContainer?.querySelectorAll('.js-label-checkbox') || [])
                        .some(cb => cb.checked);
                    
                    if (!marketLimitsSection || marketLimitsSection.children.length === 0) {
                        if (!hasOptionalChecked) {
                            labelsRow.style.display = 'none';
                        } else {
                            labelsRow.style.display = '';
                        }
                    } else {
                        // Keep row visible for Market Old Limits/Reminder
                        labelsRow.style.display = '';
                    }
                } else {
                    // Show required labels section
                    if (requiredLabelsContainer) {
                        requiredLabelsContainer.style.display = '';
                    }
                    // Keep optional labels visible
                    if (optionalLabelsContainer) {
                        optionalLabelsContainer.style.display = '';
                    }
                    // Ensure row is visible
                    labelsRow.style.display = '';
                }
                
                // Check if this checkbox is required and was just checked
                const isRequired = checkbox.getAttribute('data-required') === 'true';
                const wasJustChecked = checked && isRequired;
                
                // Update labels in events table
                updateEventLabels(exEventId, eventLabels).then((success) => {
                    // If all 4 required labels are now checked (just checked the 4th one), show SC Type popup
                    if (wasJustChecked && requiredCheckedCount === 4 && totalRequiredLabels === 4) {
                        // Check if sc_type is already set, if not show modal
                        const currentScType = labelsRow.getAttribute('data-sc-type');
                        if (!currentScType || currentScType === '') {
                            // Store the checkbox that triggered the popup (the one that was just checked)
                            window.lastCheckedCheckbox = checkbox;
                            window.lastCheckedEventId = exEventId;
                            // Show modal and prevent page refresh
                            showScTypeModal(exEventId);
                            // Exit early - don't execute refresh logic below
                            return;
                        }
                    }
                    
                    // If all required labels are checked (last required label was just checked), refresh the page to reorder records
                    // But only if popup was not shown (sc_type already exists) and modal is not open
                    if (success && allRequiredChecked && checked === true && isRequired) {
                        // Check if modal is open - if so, don't refresh
                        if (window.scTypeModalOpen) {
                            return;
                        }
                        
                        const currentScType = labelsRow.getAttribute('data-sc-type');
                        // Only refresh if sc_type is already set (popup was not shown)
                        if (currentScType && currentScType !== '') {
                            // Small delay to ensure DB update completes
                            setTimeout(() => {
                                // Double check modal is still not open before refreshing
                                if (!window.scTypeModalOpen) {
                                    window.location.reload();
                                }
                            }, 300);
                        }
                    }
                });
            }
        });

        // Handle event toggle switches - use event delegation for dynamic content
        document.addEventListener('change', async function(e) {
            if (e.target && e.target.classList.contains('js-event-toggle')) {
                const toggle = e.target;
                
                if (toggle.checked) {
                    // Open modal when toggle is turned on
                    try {
                        const eventDataStr = toggle.getAttribute('data-event-data');
                        if (!eventDataStr) {
                            console.error('Event data not found on toggle');
                            toggle.checked = false; // Reset toggle
                            return;
                        }
                        const eventData = JSON.parse(eventDataStr);
                        console.log('Opening modal for event:', eventData);
                        openEventModal(eventData);
                    } catch (error) {
                        console.error('Error parsing event data:', error);
                        alert('Error opening event details. Please try again.');
                        toggle.checked = false; // Reset toggle if error
                    }
                } else {
                    // Toggle is being turned OFF - show confirmation popup
                    const eventDataStr = toggle.getAttribute('data-event-data');
                    if (!eventDataStr) {
                        console.error('Event data not found on toggle');
                        toggle.checked = true; // Reset toggle
                        return;
                    }
                    
                    const eventData = JSON.parse(eventDataStr);
                    const eventName = eventData.eventName || 'this event';
                    const exEventId = eventData.exEventId;
                    
                    // Format old limits for confirmation message
                    let oldLimitsText = '';
                    if (eventData.market_old_limits && Array.isArray(eventData.market_old_limits) && eventData.market_old_limits.length > 0) {
                        const limitsArray = eventData.market_old_limits.map(market => {
                            return `${market.marketName}: ${market.old_limit}`;
                        });
                        oldLimitsText = ' of old limit ' + limitsArray.join(', ');
                    }
                    
                    // Show confirmation popup
                    const confirmed = await showScorecardConfirm(
                        `Are you sure you want to turn OFF the interruption for "${eventName}"${oldLimitsText}?`,
                        'Confirm Turn Off Interruption'
                    );
                    
                    if (confirmed) {
                        // Update database to set is_interrupted to false
                        try {
                            const response = await fetch(`/scorecard/events/${exEventId}/update`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    is_interrupted: false,
                                }),
                            });

                            const result = await response.json();
                            if (result.success) {
                                // Show success message
                                if (typeof ToastNotification !== 'undefined') {
                                    ToastNotification.show('Interruption turned off successfully!', 'success', 3000);
                                } else {
                                    alert('Interruption turned off successfully!');
                                }
                                
                                // Update the toggle's data attribute to reflect new state
                                const updatedEventData = { ...eventData, is_interrupted: false };
                                toggle.setAttribute('data-event-data', JSON.stringify(updatedEventData));
                                
                                // Ensure toggle is OFF (it should already be OFF since we're in the else block)
                                toggle.checked = false;
                                
                                // Close modal if open
                                closeEventModal();
                                
                                // Reload page to reflect changes
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            } else {
                                // Show error and reset toggle
                                if (typeof ToastNotification !== 'undefined') {
                                    ToastNotification.show('Failed to turn off interruption: ' + (result.message || 'Unknown error'), 'error', 4000);
                                } else {
                                    alert('Failed to turn off interruption: ' + (result.message || 'Unknown error'));
                                }
                                toggle.checked = true; // Reset toggle
                            }
                        } catch (error) {
                            console.error('Error updating interruption status:', error);
                            if (typeof ToastNotification !== 'undefined') {
                                ToastNotification.show('Error updating interruption status. Please try again.', 'error', 4000);
                            } else {
                                alert('Error updating interruption status. Please try again.');
                            }
                            toggle.checked = true; // Reset toggle
                        }
                    } else {
                        // User cancelled - reset toggle back to checked
                        toggle.checked = true;
                    }
                }
            }
        });
    });

    // Store the toggle that opened the modal
    let currentModalToggle = null;
    let currentModalToggleOriginalState = false;

    // Event Modal Functions
    async function openEventModal(eventData) {
        // Store reference to the toggle that opened this modal and its original state
        // Find the toggle that matches the exEventId (the one that was just clicked)
        const allToggles = document.querySelectorAll('.js-event-toggle');
        let activeToggle = null;
        for (let toggle of allToggles) {
            try {
                const toggleEventData = JSON.parse(toggle.getAttribute('data-event-data'));
                if (toggleEventData.exEventId === eventData.exEventId && toggle.checked) {
                    activeToggle = toggle;
                    break;
                }
            } catch (e) {
                continue;
            }
        }
        
        // If not found by exEventId, use the checked toggle as fallback
        if (!activeToggle) {
            activeToggle = document.querySelector('.js-event-toggle:checked');
        }
        
        if (activeToggle) {
            currentModalToggle = activeToggle;
            // Get original state from data attribute (what was in DB before this click)
            const toggleEventData = JSON.parse(activeToggle.getAttribute('data-event-data'));
            currentModalToggleOriginalState = toggleEventData.is_interrupted || false;
        }
        
        console.log('openEventModal called with:', eventData);
        
        const modal = document.getElementById('eventModal');
        const overlay = document.getElementById('eventModalOverlay');
        const content = document.getElementById('eventModalContent');
        
        if (!modal || !overlay || !content) {
            console.error('Modal elements not found:', { modal, overlay, content });
            alert('Modal elements not found. Please refresh the page.');
            return;
        }
        
        // Show loading state
        content.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading markets...</p>
                </div>
            </div>
        `;
        
        modal.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        console.log('Modal elements activated');
        
        // Fetch markets for this event
        try {
            const response = await fetch(`/scorecard/events/${eventData.exEventId}/markets`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            
            const result = await response.json();
            const markets = result.success ? result.markets : [];
            
            // Build markets HTML
            let marketsHtml = '';
            if (markets.length > 0) {
                marketsHtml = markets.map((market, index) => `
                    <div class="flex items-center gap-3 ${index < markets.length - 1 ? 'pb-2 mb-2 border-b border-gray-200 dark:border-gray-700' : ''}">
                        <label for="oldLimit_${market.id}" class="flex-shrink-0 text-xs font-medium text-gray-700 dark:text-gray-300 w-32 truncate" title="${market.marketName || `Market ${index + 1}`}">
                            ${market.marketName || `Market ${index + 1}`}
                        </label>
                        <input type="number" 
                               id="oldLimit_${market.id}" 
                               name="markets[${index}][old_limit]" 
                               data-market-id="${market.id}"
                               value="${market.old_limit || ''}"
                               min="0" 
                               step="1"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               class="flex-1 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="0">
                        <input type="hidden" name="markets[${index}][id]" value="${market.id}">
                    </div>
                `).join('');
            } else {
                marketsHtml = '<p class="text-xs text-gray-500 dark:text-gray-400 text-center py-2">No in-play markets found.</p>';
            }
            
            // Populate modal content - Compact design
            content.innerHTML = `
                <form id="eventModalForm" class="space-y-3">
                    <!-- Event Info - Compact Grid -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Event Name</label>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" title="${eventData.eventName || 'N/A'}">${eventData.eventName || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Exc. Event ID</label>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-mono truncate" title="${eventData.exEventId || 'N/A'}">${eventData.exEventId || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Sport</label>
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200">
                                ${eventData.sportName || 'N/A'}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Tournament</label>
                            <p class="text-xs text-gray-900 dark:text-gray-100 truncate" title="${eventData.tournamentsName || 'N/A'}">${eventData.tournamentsName || 'N/A'}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Event Time</label>
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200">
                                ${eventData.formatted_market_time || 'N/A'}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Form Controls - Compact -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 space-y-2">
                        <!-- Validation Error Messages -->
                        <div id="modalValidationErrors" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 space-y-1">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-xs font-semibold text-red-800 dark:text-red-300">Please fix the following errors:</p>
                            </div>
                            <ul id="modalErrorList" class="text-xs text-red-700 dark:text-red-400 list-disc list-inside ml-6 space-y-0.5"></ul>
                        </div>
                        
                        <!-- Market Old Limits -->
                        <div>
                            <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Market Old Limits
                                <span class="text-red-600 dark:text-red-400">*</span>
                            </h3>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                ${marketsHtml}
                            </div>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 hidden" id="oldLimitsError">At least one market must have an old limit.</p>
                        </div>
                        
                        <!-- Remind Me After -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <label class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    Remind Me After:
                                    <span class="text-red-600 dark:text-red-400">*</span>
                                </label>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 5 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="5" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 5 ? 'checked' : ''}>
                                        <span>5 min</span>
                                    </label>
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 10 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="10" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 10 ? 'checked' : ''}>
                                        <span>10 min</span>
                                    </label>
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 15 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="15" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 15 ? 'checked' : ''}>
                                        <span>15 min</span>
                                    </label>
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 20 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="20" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 20 ? 'checked' : ''}>
                                        <span>20 min</span>
                                    </label>
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 25 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="25" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 25 ? 'checked' : ''}>
                                        <span>25 min</span>
                                    </label>
                                    <label class="remind-me-button-label inline-flex items-center justify-center px-2.5 py-1 text-xs font-medium rounded border cursor-pointer transition-all duration-75 ${eventData.remind_me_after == 30 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600'}">
                                        <input type="checkbox" 
                                               name="remind_me_after" 
                                               value="30" 
                                               class="remind-me-checkbox sr-only"
                                               ${eventData.remind_me_after == 30 ? 'checked' : ''}>
                                        <span>30 min</span>
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 hidden" id="remindMeAfterError">Please select a reminder time.</p>
                        </div>
                    </div>
                </form>
            `;
            
            // Re-attach number-only validation
            attachNumberOnlyValidation();
            
            // Handle remind me checkboxes - only one can be checked at a time (button style)
            const remindMeCheckboxes = document.querySelectorAll('.remind-me-checkbox');
            remindMeCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.closest('.remind-me-button-label');
                    
                    if (this.checked) {
                        // Uncheck all other checkboxes and update their button styles
                        remindMeCheckboxes.forEach(cb => {
                            if (cb !== this) {
                                cb.checked = false;
                                const otherLabel = cb.closest('.remind-me-button-label');
                                if (otherLabel) {
                                    otherLabel.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                                    otherLabel.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'border-gray-300', 'dark:border-gray-600');
                                }
                            }
                        });
                        
                        // Update current button style to active
                        if (label) {
                            label.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'border-gray-300', 'dark:border-gray-600');
                            label.classList.add('bg-primary-600', 'text-white', 'border-primary-600');
                        }
                    } else {
                        // Update button style to inactive
                        if (label) {
                            label.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                            label.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'border-gray-300', 'dark:border-gray-600');
                        }
                    }
                });
            });
        } catch (error) {
            console.error('Error fetching markets:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600 dark:text-red-400">Error loading markets. Please try again.</p>
                    <button onclick="closeEventModal()" class="mt-4 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Close
                    </button>
                </div>
            `;
        }
    }

    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        const overlay = document.getElementById('eventModalOverlay');
        
        if (modal) modal.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Only uncheck the toggle that opened the modal if it wasn't originally checked from DB
        // This prevents unchecking other toggles when opening a new modal
        if (currentModalToggle) {
            // If the toggle was not originally checked from database, uncheck it when closing without submit
            // This means the user just clicked it to open the modal, not that it was saved as interrupted
            if (!currentModalToggleOriginalState) {
                currentModalToggle.checked = false;
            }
            // Reset the stored toggle reference
            currentModalToggle = null;
            currentModalToggleOriginalState = false;
        }
    }

    // Save event modal form
    async function saveEventModal() {
        const form = document.getElementById('eventModalForm');
        if (!form) {
            console.error('Form not found');
            return;
        }

        // Collect market data
        const markets = [];
        const marketInputs = form.querySelectorAll('input[data-market-id]');
        marketInputs.forEach(input => {
            const marketId = input.getAttribute('data-market-id');
            const oldLimit = input.value.trim();
            
            markets.push({
                id: parseInt(marketId),
                old_limit: oldLimit !== '' ? oldLimit : null,
            });
        });

        // Get selected remind me after value (only one checkbox can be checked)
        const remindMeAfterCheckbox = form.querySelector('.remind-me-checkbox:checked');
        const remindMeAfter = remindMeAfterCheckbox ? remindMeAfterCheckbox.value : null;

        // Get is_interrupted from toggle state (only save on form submit)
        // Use the currentModalToggle if available (the one that opened the modal), otherwise find checked toggle
        const activeToggle = currentModalToggle || document.querySelector('.js-event-toggle:checked');
        if (!activeToggle) {
            console.error('No active event selected');
            if (typeof ToastNotification !== 'undefined') {
                ToastNotification.show('No event selected. Please try again.', 'error', 3000);
            } else {
                alert('No event selected. Please try again.');
            }
            return;
        }

        const eventData = JSON.parse(activeToggle.getAttribute('data-event-data'));
        const exEventId = eventData.exEventId;
        
        // Ensure toggle is checked (ON) when submitting form - modal only opens when toggle is ON
        activeToggle.checked = true;
        
        // Get is_interrupted - always true when submitting from modal (since modal only opens when toggle is ON)
        const isInterrupted = true;

        // Get labels from checkboxes on the main page for this event
        const labels = {};
        const eventRow = activeToggle.closest('tr');
        if (eventRow) {
            const labelCheckboxes = eventRow.querySelectorAll('.js-label-checkbox');
            labelCheckboxes.forEach(checkbox => {
                const labelKey = checkbox.getAttribute('data-label-key');
                labels[labelKey] = checkbox.checked;
            });
        }

        const data = {
            markets: markets,
            is_interrupted: isInterrupted,
            labels: labels,
            remind_me_after: remindMeAfter,
        };

        // Validation: Check if at least one market has an old limit
        const hasOldLimit = markets.some(market => market.old_limit !== null && market.old_limit !== '');
        
        // Validation: Check if remind me after is selected
        const hasRemindMeAfter = remindMeAfter !== null && remindMeAfter !== '';
        
        // Clear previous error messages
        const errorContainer = document.getElementById('modalValidationErrors');
        const errorList = document.getElementById('modalErrorList');
        const oldLimitsError = document.getElementById('oldLimitsError');
        const remindMeAfterError = document.getElementById('remindMeAfterError');
        
        if (errorContainer) errorContainer.classList.add('hidden');
        if (oldLimitsError) oldLimitsError.classList.add('hidden');
        if (remindMeAfterError) remindMeAfterError.classList.add('hidden');
        if (errorList) errorList.innerHTML = '';
        
        // Remove error styling from inputs
        const oldLimitInputs = form.querySelectorAll('input[data-market-id]');
        oldLimitInputs.forEach(input => {
            input.classList.remove('border-red-500', 'dark:border-red-500');
        });
        
        const remindMeCheckboxes = form.querySelectorAll('.remind-me-checkbox');
        remindMeCheckboxes.forEach(checkbox => {
            checkbox.closest('label')?.classList.remove('border-red-500', 'dark:border-red-500');
        });
        
        // Validate old limits are numbers if provided
        const validationErrors = [];
        
        if (!hasOldLimit) {
            validationErrors.push('At least one market must have an old limit.');
            if (oldLimitsError) {
                oldLimitsError.classList.remove('hidden');
            }
            // Highlight old limit inputs
            oldLimitInputs.forEach(input => {
                input.classList.add('border-red-500', 'dark:border-red-500');
            });
        } else {
            // Validate old limits are numbers if provided
            for (let market of markets) {
                if (market.old_limit !== null && market.old_limit !== '') {
                    if (isNaN(market.old_limit) || parseFloat(market.old_limit) < 0) {
                        validationErrors.push(`Old Limit must be a valid number (0 or greater) for all markets.`);
                        // Highlight invalid inputs
                        const invalidInput = form.querySelector(`input[data-market-id="${market.id}"]`);
                        if (invalidInput) {
                            invalidInput.classList.add('border-red-500', 'dark:border-red-500');
                        }
                        break; // Only show one error message
                    }
                }
            }
        }
        
        if (!hasRemindMeAfter) {
            validationErrors.push('Please select a reminder time.');
            if (remindMeAfterError) {
                remindMeAfterError.classList.remove('hidden');
            }
            // Highlight remind me after buttons
            remindMeCheckboxes.forEach(checkbox => {
                checkbox.closest('label')?.classList.add('border-red-500', 'dark:border-red-500');
            });
        }
        
        // If there are validation errors, show them and prevent submission
        if (validationErrors.length > 0) {
            if (errorContainer && errorList) {
                errorContainer.classList.remove('hidden');
                validationErrors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
            } else {
                // Fallback to alert if error container not found
                alert(validationErrors.join('\n'));
            }
            
            // Scroll to error container
            if (errorContainer) {
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            
            return; // Prevent form submission
        }

        try {
            const response = await fetch(`/scorecard/events/${exEventId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                // Ensure toggle stays ON after form submission
                if (activeToggle) {
                    activeToggle.checked = true;
                    // Update the toggle's data attribute to reflect that it's now saved as ON
                    const updatedEventData = { ...eventData, is_interrupted: true };
                    activeToggle.setAttribute('data-event-data', JSON.stringify(updatedEventData));
                    // Update the original state to true so it won't be reset when modal closes
                    currentModalToggleOriginalState = true;
                }
                
                // Show success message (you can use toast notification if available)
                if (typeof ToastNotification !== 'undefined') {
                    ToastNotification.show('Event settings saved successfully', 'success', 3000);
                } else {
                    alert('Event settings saved successfully');
                }
                
                // Don't reset currentModalToggle until after page reload - keep it ON
                // Update the original state so it won't be reset if modal is closed
                if (currentModalToggle) {
                    currentModalToggleOriginalState = true;
                }
                
                // Close modal
                closeEventModal();
                
                // Reload page to show updated data (after reload, toggle will reflect DB state: ON)
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (typeof ToastNotification !== 'undefined') {
                    ToastNotification.show(result.message || 'Error saving event settings', 'error');
                } else {
                    alert(result.message || 'Error saving event settings');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof ToastNotification !== 'undefined') {
                ToastNotification.show('Error saving event settings', 'error');
            } else {
                alert('Error saving event settings');
            }
        }
    }

    // Attach number-only validation to old limit fields
    function attachNumberOnlyValidation() {
        document.querySelectorAll('input[data-market-id]').forEach(input => {
            // Only allow numbers on input
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });

            // Prevent paste of non-numeric values
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = paste.replace(/[^0-9]/g, '');
                e.target.value = numericValue;
            });
        });
    }

    // Update labels in events table (auto-save when checkbox changed)
    async function updateEventLabels(exEventId, labels) {
        try {
            const response = await fetch(`/scorecard/events/${exEventId}/update-labels`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ labels: labels }),
            });

            const result = await response.json();
            if (result.success) {
                // Count checked labels
                const checkedCount = Object.values(labels).filter(v => v === true).length;
                const totalLabels = Object.keys(labels).length;
                
                // Show success message
                if (typeof ToastNotification !== 'undefined') {
                    if (checkedCount === totalLabels) {
                        ToastNotification.show('All labels updated successfully!', 'success', 3000);
                    } else {
                        ToastNotification.show('Labels updated successfully!', 'success', 3000);
                    }
                } else {
                    // Fallback to alert if ToastNotification is not available
                    alert('Labels updated successfully!');
                }
                
                // Return success status
                return true;
            } else {
                // Show error message
                if (typeof ToastNotification !== 'undefined') {
                    ToastNotification.show('Failed to update labels: ' + (result.message || 'Unknown error'), 'error', 4000);
                } else {
                    alert('Failed to update labels: ' + (result.message || 'Unknown error'));
                }
                console.error('Error updating labels:', result.message);
                return false;
            }
        } catch (error) {
            // Show error message
            if (typeof ToastNotification !== 'undefined') {
                ToastNotification.show('Error updating labels. Please try again.', 'error', 4000);
            } else {
                alert('Error updating labels. Please try again.');
            }
            console.error('Error updating labels:', error);
            return false;
        }
    }

    // Prevent non-numeric input in old limit fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        attachNumberOnlyValidation();
    });

    // Close modal when clicking overlay
    document.getElementById('eventModalOverlay')?.addEventListener('click', function() {
        closeEventModal();
    });

    // Close drawer on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const drawer = document.getElementById('scorecard-filter-drawer');
            if (drawer && drawer.classList.contains('open')) {
                toggleScorecardFilterDrawer(true);
            }
            // Also close modal on Escape
            const modal = document.getElementById('eventModal');
            if (modal && modal.classList.contains('active')) {
                closeEventModal();
            }
        }
    });

    // SC Type Modal - Global flag to track if modal is open
    window.scTypeModalOpen = false;
    
    function showScTypeModal(exEventId) {
        // Prevent multiple modals
        if (window.scTypeModalOpen) {
            return;
        }
        
        window.scTypeModalOpen = true;
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.id = 'scTypeModalOverlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4';
        overlay.style.transition = 'opacity 0.3s ease-in-out';
        
        // Create modal
        const modal = document.createElement('div');
        modal.id = 'scTypeModal';
        modal.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300';
        
        modal.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select SC Type</h3>
                    <button onclick="closeScTypeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="scTypeForm" onsubmit="submitScType(event, '${exEventId}')">
                    <div class="mb-4">
                        <label for="sc_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            SC Type <span class="text-red-500">*</span>
                        </label>
                        <select id="sc_type" name="sc_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select SC Type</option>
                            <option value="Sportradar">Sportradar</option>
                            <option value="Old SC(Cric)">Old SC(Cric)</option>
                            <option value="SR Premium">SR Premium</option>
                            <option value="SpreadeX">SpreadeX</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="sc_web_pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Web PIN <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="sc_web_pin" name="web_pin" required inputmode="numeric" pattern="[0-9]*" maxlength="20" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500" placeholder="Enter your Web PIN">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter your 6-digit Web PIN</p>
                    </div>
                    <div id="scTypeError" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400" id="scTypeErrorMessage"></p>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeScTypeModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="scTypeSubmitBtn" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        
        // Animate in
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
        
        // Close on overlay click (but prevent accidental closes)
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                // Only close if user explicitly clicks outside (not during initial render)
                const modal = document.getElementById('scTypeModal');
                if (modal && modal.contains(e.target)) {
                    return;
                }
                // Don't auto-close - require explicit cancel button click
            }
        });
        
        // Prevent Escape key from closing (user must use Cancel button)
        const escapeHandler = function(e) {
            if (e.key === 'Escape' && window.scTypeModalOpen) {
                // Don't close on Escape - user must explicitly cancel
                e.preventDefault();
                e.stopPropagation();
            }
        };
        document.addEventListener('keydown', escapeHandler);
        
        // Store handler reference for cleanup
        overlay._escapeHandler = escapeHandler;
        
        // Focus on select
        setTimeout(() => {
            const selectEl = document.getElementById('sc_type');
            if (selectEl) {
                selectEl.focus();
            }
        }, 100);
    }
    
    function closeScTypeModal() {
        window.scTypeModalOpen = false;
        const overlay = document.getElementById('scTypeModalOverlay');
        if (overlay) {
            // Remove escape handler if it exists
            if (overlay._escapeHandler) {
                document.removeEventListener('keydown', overlay._escapeHandler);
            }
            
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
                document.body.style.overflow = '';
                
                // Uncheck the last checkbox that triggered the popup
                if (window.lastCheckedCheckbox && window.lastCheckedEventId) {
                    const checkbox = window.lastCheckedCheckbox;
                    const exEventId = window.lastCheckedEventId;
                    
                    // Uncheck the checkbox
                    checkbox.checked = false;
                    
                    // Find the labels row for this event
                    const labelsRow = document.querySelector(`tr.js-labels-row[data-event-id="${exEventId}"]`);
                    if (labelsRow) {
                        // Get all labels for this event
                        const eventLabels = {};
                        const eventCheckboxes = labelsRow.querySelectorAll('.js-label-checkbox');
                        
                        eventCheckboxes.forEach(cb => {
                            const key = cb.getAttribute('data-label-key');
                            eventLabels[key] = cb.checked;
                        });
                        
                        // Update labels in database
                        updateEventLabels(exEventId, eventLabels).then(() => {
                            // Show notification
                            if (typeof ToastNotification !== 'undefined') {
                                ToastNotification.show('SC Type selection cancelled. Last checkbox unchecked.', 'info', 2000);
                            }
                            // Refresh the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        });
                    }
                    
                    // Clear stored references
                    window.lastCheckedCheckbox = null;
                    window.lastCheckedEventId = null;
                }
            }, 300);
        }
    }
    
    async function submitScType(e, exEventId) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = document.getElementById('scTypeSubmitBtn');
        const errorDiv = document.getElementById('scTypeError');
        const errorMessage = document.getElementById('scTypeErrorMessage');
        const scType = document.getElementById('sc_type').value;
        const webPin = document.getElementById('sc_web_pin').value;
        
        // Hide error
        errorDiv.classList.add('hidden');
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        try {
            const response = await fetch(`/scorecard/events/${exEventId}/update-sc-type`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    sc_type: scType,
                    web_pin: webPin,
                }),
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear stored checkbox references (successful submission)
                window.lastCheckedCheckbox = null;
                window.lastCheckedEventId = null;
                
                // Update the data-sc-type attribute on the labels row
                const labelsRow = document.querySelector(`tr.js-labels-row[data-event-id="${exEventId}"]`);
                if (labelsRow) {
                    labelsRow.setAttribute('data-sc-type', scType);
                }
                
                // Close modal
                closeScTypeModal();
                
                // Show success message
                if (typeof ToastNotification !== 'undefined') {
                    ToastNotification.show('SC Type updated successfully', 'success', 3000);
                } else {
                    alert('SC Type updated successfully');
                }
                
                // Optionally refresh the page or update UI
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Show error
                errorMessage.textContent = data.message || 'Failed to update SC Type';
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            }
        } catch (error) {
            errorMessage.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit';
        }
    }
    
    // Web PIN input validation (numeric only)
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'sc_web_pin') {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            }
        });
    });
</script>
@endpush
@endsection
