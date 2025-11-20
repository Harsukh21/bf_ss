@push('css')
<style>
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 260px;
    padding: 12px 16px;
    border-radius: 8px;
    color: #fff;
    background: rgba(31, 41, 55, 0.95);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 2000;
    opacity: 0;
    transform: translateY(-10px);
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.toast-notification.show {
    opacity: 1;
    transform: translateY(0);
}

.toast-notification.toast-success {
    background: rgba(5, 150, 105, 0.95);
}

.toast-notification.toast-error {
    background: rgba(220, 38, 38, 0.95);
}

.toast-notification button {
    margin-left: auto;
    background: transparent;
    border: none;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

/* Page Info Icon Styles */
.page-info-icon {
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.2s ease;
    display: block;
    vertical-align: middle;
}

.page-info-icon circle {
    fill: #eab308;
    transition: fill 0.2s ease;
}

.page-info-icon text {
    fill: #ffffff;
    font-weight: bold;
    font-family: Arial, sans-serif;
}

.page-info-icon:hover circle {
    fill: #ca8a04;
}

/* Rules Modal Styles */
.rules-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease;
    z-index: 1050;
}

.rules-modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.rules-modal {
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

.rules-modal.active {
    pointer-events: auto;
    opacity: 1;
    visibility: visible;
}

.rules-modal__content {
    width: 100%;
    max-width: 600px;
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
    max-height: 90vh;
    overflow-y: auto;
}

.dark .rules-modal__content {
    background: #1f2937;
}
</style>
@endpush
@extends('layouts.app')

@section('title', $pageTitle ?? 'Event List')

@push('css')
<style>
    .filter-drawer {
        position: fixed;
        top: 0;
        right: -500px;
        width: 500px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        transition: right 0.3s ease-in-out;
        z-index: 1000;
        overflow-y: auto;
    }
    
    .dark .filter-drawer {
        background: #1f2937;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.3);
    }
    
    .filter-drawer.open {
        right: 0;
    }
    
    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }
    
    .filter-overlay.active {
        opacity: 1;
        visibility: visible;
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

    .filter-field-row {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .filter-field-label {
        width: 3.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }

    .dark .filter-field-label {
        color: #9ca3af;
    }

    .filter-field-apply {
        display: inline-flex;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 500;
        color: #4b5563;
    }

    .dark .filter-field-apply {
        color: #d1d5db;
    }

    .filter-field-apply input {
        margin-right: 0.4rem;
    }

    .time-range-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .time-block {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .time-block-header {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }

    .dark .time-block-header {
        color: #9ca3af;
    }

    .time-block-inputs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .time-select-ampm {
        min-width: 70px;
    }

    .time-picker-panel {
        position: relative;
    }

    .time-picker-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        padding-right: 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background-color: #ffffff;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    .time-picker-button:hover {
        border-color: #2563eb;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.12);
    }

    .time-picker-button.placeholder {
        color: #9ca3af;
    }

    .time-picker-button:disabled {
        cursor: not-allowed;
        background-color: #f3f4f6;
        color: #9ca3af;
    }

    .time-picker-icon {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #9ca3af;
        pointer-events: none;
    }

    .dark .time-picker-icon {
        color: #d1d5db;
    }

    .dark .time-picker-button {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    .dark .time-picker-button.placeholder span {
        color: #9ca3af;
    }

    .time-picker-dropdown {
        position: absolute;
        top: calc(100% + 0.6rem);
        left: 0;
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.15);
        padding: 1rem 1.1rem 1.2rem;
        z-index: 60;
    }

    .dark .time-picker-dropdown {
        background-color: #1f2937;
        border-color: #374151;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.45);
    }

    .time-picker-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .time-picker-column {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .time-picker-column p {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        margin-bottom: 0.1rem;
    }

    .dark .time-picker-column p {
        color: #9ca3af;
    }

    .time-picker-options {
        display: grid;
        gap: 0.3rem;
        max-height: 190px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.85rem;
        background-color: #ffffff;
        padding: 0.4rem;
        scrollbar-width: thin;
    }

    .time-picker-options-hour {
        grid-template-columns: repeat(2, minmax(0, 70px));
    }

    .time-picker-options-minute,
    .time-picker-options-second {
        grid-template-columns: repeat(3, minmax(0, 70px));
    }

    .time-picker-options-period {
        grid-template-columns: repeat(2, minmax(0, 70px));
    }

    .dark .time-picker-options {
        border-color: #374151;
        background-color: #111827;
    }

    .time-picker-option {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.4rem;
        border-radius: 0.65rem;
        font-weight: 600;
        font-size: 0.82rem;
        color: #1f2937;
        background: transparent;
        transition: all 0.15s ease-in-out;
        border: none;
        cursor: pointer;
    }

    .time-picker-option:hover {
        background-color: #2563eb;
        color: #ffffff;
    }

    .time-picker-option.active {
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 15px 28px rgba(37, 99, 235, 0.25);
    }

    .dark .time-picker-option {
        color: #e5e7eb;
    }

    .dark .time-picker-option:hover,
    .dark .time-picker-option.active {
        background-color: #2563eb;
        color: #ffffff;
    }

    .time-picker-actions {
        margin-top: 1.1rem;
        display: flex;
        gap: 0.75rem;
    }

    .time-picker-action {
        flex: 1;
        padding: 0.55rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.15s ease-in-out;
    }

    .time-picker-action:hover {
        background-color: #e5e7eb;
    }

    .time-picker-action.primary {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }

    .time-picker-action.primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .dark .time-picker-action {
        border-color: #4b5563;
        background-color: #374151;
        color: #f3f4f6;
    }

    .dark .time-picker-action:hover {
        background-color: #4b5563;
    }

    .dark .time-picker-action.primary {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .timepicker-hint {
        font-size: 0.7rem;
        color: #9ca3af;
        margin: 0;
    }

    .dark .timepicker-hint {
        color: #9ca3af;
    }

    [x-cloak] {
        display: none !important;
    }

</style>
@endpush

@section('content')
@php
    $statusOptions = $statusOptions ?? [
        1 => 'Unsettled',
        2 => 'Upcoming',
        3 => 'In Play',
        4 => 'Settled',
        5 => 'Voided',
        6 => 'Removed',
    ];

    $statusSummary = $statusSummary ?? [];

    $statusBadgeMeta = [
        1 => ['label' => 'Unsettled', 'class' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300'],
        2 => ['label' => 'Upcoming', 'class' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300'],
        3 => ['label' => 'In Play', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300'],
        4 => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300'],
        5 => ['label' => 'Voided', 'class' => 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200'],
        6 => ['label' => 'Removed', 'class' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300'],
    ];

    $statusCardStyles = [
        1 => ['iconBg' => 'bg-purple-100 dark:bg-purple-900/20', 'iconColor' => 'text-purple-600 dark:text-purple-300'],
        2 => ['iconBg' => 'bg-yellow-100 dark:bg-yellow-900/20', 'iconColor' => 'text-yellow-600 dark:text-yellow-300'],
        3 => ['iconBg' => 'bg-red-100 dark:bg-red-900/20', 'iconColor' => 'text-red-600 dark:text-red-300'],
        4 => ['iconBg' => 'bg-green-100 dark:bg-green-900/20', 'iconColor' => 'text-green-600 dark:text-green-300'],
        5 => ['iconBg' => 'bg-gray-200 dark:bg-gray-700/50', 'iconColor' => 'text-gray-700 dark:text-gray-200'],
        6 => ['iconBg' => 'bg-orange-100 dark:bg-orange-900/20', 'iconColor' => 'text-orange-600 dark:text-orange-300'],
    ];
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-3 leading-tight">
                        <span>{{ $pageHeading ?? 'Event List' }}</span>
                        @if(request()->routeIs('events.all'))
                            <button onclick="openEventRulesModal()" class="flex-shrink-0 flex items-center justify-center" title="Event Rules">
                                <svg class="page-info-icon" width="24" height="24" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="12"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="14" font-weight="bold" fill="#ffffff">i</text>
                                </svg>
                            </button>
                        @endif
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $pageSubheading ?? 'Events for today and tomorrow are shown here.' }}</p>
                </div>
                @php
                    $isAllRoute = request()->routeIs('events.all');
                    $baseRoute = $isAllRoute ? route('events.all') : route('events.index');
                    $recentlyAddedActive = request()->boolean('recently_added');

                    $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
                    $defaultPickerTime = \Carbon\Carbon::now(config('app.timezone', 'UTC'))->format('h:i:s A');

                    $formatTime = function ($value) use ($timeFormats) {
                        if (!$value) {
                            return null;
                        }

                        foreach ($timeFormats as $format) {
                            try {
                                return \Carbon\Carbon::createFromFormat($format, $value)->format('h:i:s A');
                            } catch (\Exception $e) {
                                continue;
                            }
                        }

                        return $value;
                    };

                    $rawTimeFrom = request('time_from');
                    $rawTimeTo = request('time_to');

                    $timeFromValue = $formatTime($rawTimeFrom) ?: $defaultPickerTime;
                    $timeToValue = $formatTime($rawTimeTo) ?: $defaultPickerTime;

                    $timeFromEnabled = request()->boolean('time_from_enabled') && !empty($rawTimeFrom);
                    $timeToEnabled = request()->boolean('time_to_enabled') && !empty($rawTimeTo);

                    $dateFromEnabled = request()->boolean('event_date_from_enabled') && request()->filled('event_date_from');
                    $dateToEnabled = request()->boolean('event_date_to_enabled') && request()->filled('event_date_to');

                    $activeFilters = [];

                    if (request('search')) {
                        $activeFilters[] = ['label' => 'Search', 'value' => request('search'), 'remove' => ['search']];
                    }

                    if (request('sport')) {
                        $activeFilters[] = ['label' => 'Sport', 'value' => $sportConfig[request('sport')] ?? ('ID: ' . request('sport')), 'remove' => ['sport']];
                    }

                    if (request('tournament')) {
                        $tournamentName = \App\Models\Event::where('tournamentsId', request('tournament'))->value('tournamentsName');
                        $activeFilters[] = ['label' => 'Tournament', 'value' => $tournamentName ?? request('tournament'), 'remove' => ['tournament']];
                    }

                    if (request()->filled('status')) {
                        $statusValue = (int) request('status');
                        $statusLabel = $statusOptions[$statusValue] ?? request('status');
                        $activeFilters[] = ['label' => 'Status', 'value' => $statusLabel, 'remove' => ['status']];
                    }

                    if ($dateFromEnabled) {
                        try {
                            $formattedDate = \Carbon\Carbon::parse(request('event_date_from'))->format('M d, Y');
                        } catch (\Exception $e) {
                            $formattedDate = request('event_date_from');
                        }
                        $activeFilters[] = ['label' => 'From Date', 'value' => $formattedDate, 'remove' => ['event_date_from', 'event_date_from_enabled']];
                    }

                    if ($dateToEnabled) {
                        try {
                            $formattedDate = \Carbon\Carbon::parse(request('event_date_to'))->format('M d, Y');
                        } catch (\Exception $e) {
                            $formattedDate = request('event_date_to');
                        }
                        $activeFilters[] = ['label' => 'To Date', 'value' => $formattedDate, 'remove' => ['event_date_to', 'event_date_to_enabled']];
                    }

                    if ($timeFromEnabled) {
                        $activeFilters[] = ['label' => 'From Time', 'value' => $formatTime($rawTimeFrom), 'remove' => ['time_from', 'time_from_enabled']];
                    }

                    if ($timeToEnabled) {
                        $activeFilters[] = ['label' => 'To Time', 'value' => $formatTime($rawTimeTo), 'remove' => ['time_to', 'time_to_enabled']];
                    }

                    if ($recentlyAddedActive) {
                        $activeFilters[] = ['label' => 'Recently Added', 'value' => 'On', 'remove' => ['recently_added']];
                    }

                    if (request()->has('highlight')) {
                        $activeFilters[] = ['label' => 'Highlight', 'value' => request()->boolean('highlight') ? 'Yes' : 'No', 'remove' => ['highlight']];
                    }

                    if (request()->has('popular')) {
                        $activeFilters[] = ['label' => 'Popular', 'value' => request()->boolean('popular') ? 'Yes' : 'No', 'remove' => ['popular']];
                    }

                    $filterCount = count($activeFilters);
                @endphp
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="toggleFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center relative">
                         <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                         </svg>
                         Filters
                         @if($filterCount > 0)
                             <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">{{ $filterCount }}</span>
                         @endif
                     </button>

                    @if($filterCount > 0)
                        <a href="{{ $baseRoute }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </a>
                    @endif

                    <a 
                        href="{{ route('events.export', request()->query()) }}"
                        class="ml-auto bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($filterCount > 0)
        <div class="mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Active Filters ({{ $filterCount }}):</span>
                    </div>
                    <a href="{{ $baseRoute }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">Clear All</a>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($activeFilters as $filter)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            {{ $filter['label'] }}: {{ $filter['value'] }}
                            @php
                                $removals = implode(',', $filter['remove']);
                            @endphp
                            <button type="button" onclick="removeFilter('{{ $removals }}')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 md:gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">All Events</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($paginatedEvents->total()) }}</p>
                    </div>
                </div>
            </div>
            @foreach($statusBadgeMeta as $statusId => $meta)
                @php
                    $count = $statusSummary[$statusId] ?? 0;
                    $cardStyle = $statusCardStyles[$statusId] ?? ['iconBg' => 'bg-gray-200 dark:bg-gray-700/50', 'iconColor' => 'text-gray-600 dark:text-gray-300'];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg {{ $cardStyle['iconBg'] }}">
                            <svg class="w-5 h-5 {{ $cardStyle['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $meta['label'] }}</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($count) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recently Added Switcher -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Recently Added</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Show only events flagged as recently added.</p>
                </div>
                <form method="GET" action="{{ $baseRoute }}" class="flex items-center gap-3">
                    @foreach(request()->except(['page', 'recently_added']) as $param => $value)
                        @if(is_array($value))
                            @foreach($value as $singleValue)
                                <input type="hidden" name="{{ $param }}[]" value="{{ $singleValue }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    @unless($recentlyAddedActive)
                        <input type="hidden" name="recently_added" value="1">
                    @endunless
                    <button type="submit" class="relative inline-flex items-center h-7 rounded-full w-14 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 {{ $recentlyAddedActive ? 'bg-primary-600 dark:bg-primary-500' : 'bg-gray-300 dark:bg-gray-600' }}" aria-pressed="{{ $recentlyAddedActive ? 'true' : 'false' }}">
                        <span class="sr-only">Toggle recently added filter</span>
                        <span class="absolute left-1 text-xs font-semibold uppercase tracking-wide {{ $recentlyAddedActive ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">On</span>
                        <span class="absolute right-1 text-xs font-semibold uppercase tracking-wide {{ $recentlyAddedActive ? 'text-white/60' : 'text-white' }}">Off</span>
                        <span class="inline-block w-6 h-6 transform bg-white dark:bg-gray-200 rounded-full transition-transform duration-200 ease-in-out {{ $recentlyAddedActive ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Events Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Events</h3>
            </div>
            
            @if($paginatedEvents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tournament</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($paginatedEvents as $event)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="relative inline-block text-left" x-data="{ 
                                            open: false,
                                            position: { x: 0, y: 0 },
                                            calculatePosition(event) {
                                                const rect = event.target.closest('td').getBoundingClientRect();
                                                this.position.x = rect.right - 224; // 224px is width of dropdown (w-56)
                                                this.position.y = rect.bottom + 8;
                                            }
                                        }">
                                            <div>
                                                <button @click="calculatePosition($event); open = !open" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="options-menu-{{ $event->id }}" aria-expanded="false" aria-haspopup="true">
                                                    <span class="sr-only">Open options menu</span>
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" 
                                                 :style="`position: fixed; left: ${position.x}px; top: ${position.y}px; z-index: 9999;`"
                                                 class="w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="options-menu-{{ $event->id }}">
                                                <div class="py-1" role="none">
                                                    <a href="{{ route('events.show', $event->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View Details
                                                    </a>
                                                    
                                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit Event
                                                    </a>
                                                    
                                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                        </svg>
                                                        Share Event
                                                    </a>
                                                    
                                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                                    
                                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Delete Event
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $event->eventName }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $event->eventId }}</div>
                                        @if(!empty($event->exEventId))
                                            <div class="text-xs text-gray-400 dark:text-gray-500">Exch Event ID: {{ $event->exEventId }}</div>
                                        @endif
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @if($event->marketTime)
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-white rounded-full bg-indigo-500 updated-market-time">
                                                        {{ \Carbon\Carbon::parse($event->marketTime)->format('M d, Y h:i A') }}
                                                    </span>
                                                @else
                                                    <button 
                                                        type="button"
                                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 update-market-time-btn"
                                                        data-event-id="{{ $event->id }}"
                                                        data-update-url="{{ route('events.update-market-time', $event->id) }}">
                                                        Update Time
                                                    </button>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $event->tournamentsName }}</div>
                                        @if(isset($event->market_count))
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">
                                                    {{ number_format($event->market_count) }} markets
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                            {{ $sportConfig[$event->sportId] ?? 'Unknown Sport (ID: ' . $event->sportId . ')' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $matchOddsStatus = isset($event->matchOddsStatus) ? (int) $event->matchOddsStatus : null;

                                            if (!$matchOddsStatus) {
                                                if ($event->IsSettle) {
                                                    $matchOddsStatus = 4;
                                                } elseif ($event->IsVoid) {
                                                    $matchOddsStatus = 5;
                                                } elseif ($event->IsUnsettle) {
                                                    $matchOddsStatus = 1;
                                                }
                                            }

                                            $statusInfo = $matchOddsStatus && isset($statusBadgeMeta[$matchOddsStatus])
                                                ? $statusBadgeMeta[$matchOddsStatus]
                                                : null;
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                            {{ $statusInfo['label'] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $paginatedEvents->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No events found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new event.</p>
                </div>
            @endif
        </div>

<!-- Filter Drawer -->
<div id="filterOverlay" class="filter-overlay" onclick="toggleFilterDrawer()"></div>
<div id="filterDrawer" class="filter-drawer">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filters</h2>
            <button onclick="toggleFilterDrawer()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="GET" action="{{ $baseRoute }}">
            @if($recentlyAddedActive)
                <input type="hidden" name="recently_added" value="1">
            @endif
            <!-- Search -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" 
                       placeholder="Search events or tournaments...">
            </div>
            
            <!-- Sport -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <select name="sport" id="sportSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">-- Select Sport --</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport }}" {{ request('sport') == $sport ? 'selected' : '' }}>
                            {{ $sportConfig[$sport] ?? 'Unknown Sport (ID: ' . $sport . ')' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Tournament -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tournament</label>
                <div class="relative">
                    <input type="text" id="tournamentSearch" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Click to see all tournaments or search..." autocomplete="off">
                    <select name="tournament" id="tournamentSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 absolute inset-0 opacity-0 pointer-events-none">
                        <option value="">-- Select Tournament --</option>
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament->tournamentsId }}" data-sport="{{ $tournament->sportId }}" data-name="{{ $tournament->tournamentsName }}" {{ request('tournament') == $tournament->tournamentsId ? 'selected' : '' }}>{{ $tournament->tournamentsName }}</option>
                        @endforeach
                    </select>
                    <div id="tournamentDropdown" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-y-auto hidden tournament-dropdown-scrollable">
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ (string) request('status') === (string) $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Event Date Range -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">Event Date Range</div>
                <div class="space-y-3">
                    <div class="filter-field-row">
                        <span class="filter-field-label">From</span>
                        <input type="date" name="event_date_from" value="{{ request('event_date_from') }}" class="js-event-date-from flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                        <label class="filter-field-apply">
                            <input type="checkbox" name="event_date_from_enabled" value="1" {{ $dateFromEnabled ? 'checked' : '' }} class="js-event-date-from-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            Apply
                        </label>
                    </div>
                    <div class="filter-field-row">
                        <span class="filter-field-label">To</span>
                        <input type="date" name="event_date_to" value="{{ request('event_date_to') }}" class="js-event-date-to flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                        <label class="filter-field-apply">
                            <input type="checkbox" name="event_date_to_enabled" value="1" {{ $dateToEnabled ? 'checked' : '' }} class="js-event-date-to-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            Apply
                        </label>
                    </div>
                </div>
            </div>

            <!-- Event Time Range -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title mb-2">Event Time Range (12-hour format)</div>
                <div class="time-range-container">
                    <div class="time-block" x-data="timePickerComponent('{{ $timeFromValue }}', {{ $timeFromEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                            <div class="flex items-center justify-between">
                            <div class="time-block-header">From</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_from_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-time-from-enabled h-3.5 w-3.5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-1.5 text-xs">Apply</span>
                            </label>
                        </div>
                        <div class="time-picker-panel">
                            <input type="hidden" name="time_from" x-ref="hidden" :disabled="!enabled">
                            <button type="button" class="time-picker-button" :class="{ 'placeholder': !isComplete, 'opacity-60': !enabled }" :disabled="!enabled" @click="toggle">
                                <span x-text="display"></span>
                                <svg class="time-picker-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                            <div class="time-picker-dropdown" x-cloak x-show="open" x-transition @click.away="close()">
                                <div class="time-picker-grid">
                                    <div class="time-picker-column">
                                        <p>Hour</p>
                                        <div class="time-picker-options time-picker-options-hour" x-ref="hourOptions">
                                            <template x-for="hour in hours" :key="'from-hour-' + hour">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.hour === hour }" @click="setHour(hour)" x-text="hour"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Minute</p>
                                        <div class="time-picker-options time-picker-options-minute" x-ref="minuteOptions">
                                            <template x-for="minute in minutes" :key="'from-minute-' + minute">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.minute === minute }" @click="setMinute(minute)" x-text="minute"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Second</p>
                                        <div class="time-picker-options time-picker-options-second" x-ref="secondOptions">
                                            <template x-for="second in seconds" :key="'from-second-' + second">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.second === second }" @click="setSecond(second)" x-text="second"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>AM / PM</p>
                                        <div class="time-picker-options time-picker-options-period" x-ref="periodOptions">
                                            <template x-for="period in periods" :key="'from-period-' + period">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.period === period }" @click="setPeriod(period)" x-text="period"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-picker-actions">
                                    <button type="button" class="time-picker-action" @click="clear()">Clear</button>
                                    <button type="button" class="time-picker-action primary" :disabled="!isComplete" @click="confirm()">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="time-block" x-data="timePickerComponent('{{ $timeToValue }}', {{ $timeToEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                            <div class="flex items-center justify-between">
                            <div class="time-block-header">To</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_to_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-time-to-enabled h-3.5 w-3.5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-1.5 text-xs">Apply</span>
                            </label>
                        </div>
                        <div class="time-picker-panel">
                            <input type="hidden" name="time_to" x-ref="hidden" :disabled="!enabled">
                            <button type="button" class="time-picker-button" :class="{ 'placeholder': !isComplete, 'opacity-60': !enabled }" :disabled="!enabled" @click="toggle">
                                <span x-text="display"></span>
                                <svg class="time-picker-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                            <div class="time-picker-dropdown" x-cloak x-show="open" x-transition @click.away="close()">
                                <div class="time-picker-grid">
                                    <div class="time-picker-column">
                                        <p>Hour</p>
                                        <div class="time-picker-options time-picker-options-hour" x-ref="hourOptions">
                                            <template x-for="hour in hours" :key="'to-hour-' + hour">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.hour === hour }" @click="setHour(hour)" x-text="hour"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Minute</p>
                                        <div class="time-picker-options time-picker-options-minute" x-ref="minuteOptions">
                                            <template x-for="minute in minutes" :key="'to-minute-' + minute">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.minute === minute }" @click="setMinute(minute)" x-text="minute"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Second</p>
                                        <div class="time-picker-options time-picker-options-second" x-ref="secondOptions">
                                            <template x-for="second in seconds" :key="'to-second-' + second">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.second === second }" @click="setSecond(second)" x-text="second"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>AM / PM</p>
                                        <div class="time-picker-options time-picker-options-period" x-ref="periodOptions">
                                            <template x-for="period in periods" :key="'to-period-' + period">
                                                <button type="button" class="time-picker-option" :class="{ 'active': selection.period === period }" @click="setPeriod(period)" x-text="period"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-picker-actions">
                                    <button type="button" class="time-picker-action" @click="clear()">Clear</button>
                                    <button type="button" class="time-picker-action primary" :disabled="!isComplete" @click="confirm()">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-600 dark:bg-primary-700 text-white py-2 px-4 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ $baseRoute }}" class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Rules Modal -->
<div id="eventRulesModalOverlay" class="rules-modal-overlay"></div>
<div id="eventRulesModal" class="rules-modal">
    <div class="rules-modal__content">
        <div class="flex items-center justify-between mb-4">
            <h3 id="eventRulesModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Event Rules</h3>
            <button onclick="closeEventRulesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="eventRulesModalContent" class="space-y-4">
            <!-- Content will be populated dynamically -->
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeEventRulesModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEventRulesModal() {
    const modal = document.getElementById('eventRulesModal');
    const overlay = document.getElementById('eventRulesModalOverlay');
    const modalTitle = document.getElementById('eventRulesModalTitle');
    const modalContent = document.getElementById('eventRulesModalContent');
    
    modalTitle.textContent = 'Event Rules';
    modalContent.innerHTML = ''; // Empty content for now
    
    modal.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEventRulesModal() {
    const modal = document.getElementById('eventRulesModal');
    const overlay = document.getElementById('eventRulesModalOverlay');
    
    modal.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal when clicking overlay
document.getElementById('eventRulesModalOverlay')?.addEventListener('click', function() {
    closeEventRulesModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('eventRulesModal');
        if (modal && modal.classList.contains('active')) {
            closeEventRulesModal();
        }
    }
});

window.timePickerComponent = function(initialValue, initiallyEnabled = false) {
    return {
        open: false,
        hours: Array.from({ length: 12 }, (_, i) => String(i + 1).padStart(2, '0')),
        minutes: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')),
        seconds: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')),
        periods: ['AM', 'PM'],
        selection: { hour: '', minute: '', second: '', period: 'AM' },
        enabled: initiallyEnabled,
        init() {
            this.setFromString(initialValue);
            this.$watch('enabled', () => {
                this.updateHidden();
            });
        },
        get display() {
            if (this.selection.hour && this.selection.minute) {
                const second = this.selection.second || '00';
                return `${this.selection.hour}:${this.selection.minute}:${second} ${this.selection.period}`;
            }
            return 'Select time';
        },
        get isComplete() {
            return this.selection.hour && this.selection.minute && this.selection.period;
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.scrollToActive();
            }
        },
        close() {
            this.open = false;
        },
        setHour(hour) {
            this.selection.hour = hour;
            this.ensureDefaults();
            this.updateHidden();
        },
        setMinute(minute) {
            this.selection.minute = minute;
            this.ensureDefaults();
            this.updateHidden();
        },
        setSecond(second) {
            this.selection.second = second;
            this.ensureDefaults();
            this.updateHidden();
        },
        setPeriod(period) {
            this.selection.period = period;
            this.ensureDefaults();
            this.updateHidden();
        },
        ensureDefaults() {
            if (!this.selection.period) {
                this.selection.period = 'AM';
            }
            if (!this.selection.second) {
                this.selection.second = '00';
            }
        },
        updateHidden() {
            if (this.enabled && this.isComplete) {
                const value = `${this.selection.hour}:${this.selection.minute}:${this.selection.second || '00'} ${this.selection.period}`;
                this.$refs.hidden.value = value;
            } else {
                this.$refs.hidden.value = '';
            }
        },
        confirm() {
            if (this.isComplete) {
                this.updateHidden();
                this.close();
            }
        },
        clear(skipClose = false) {
            this.selection = { hour: '', minute: '', second: '', period: 'AM' };
            this.$refs.hidden.value = '';
            if (!skipClose) {
                this.close();
            }
            this.updateHidden();
        },
        setFromString(value) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                this.clear(true);
                return;
            }

            const match = trimmed.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?$/i);
            if (match) {
                let hour = parseInt(match[1], 10);
                const minute = match[2];
                const second = (match[3] || '00').padStart(2, '0');
                let period = match[4] ? match[4].toUpperCase() : null;

                if (!period) {
                    period = hour >= 12 ? 'PM' : 'AM';
                }

                if (hour === 0) {
                    hour = 12;
                } else if (hour > 12) {
                    hour -= 12;
                }

                this.selection.hour = String(hour).padStart(2, '0');
                this.selection.minute = minute;
                this.selection.second = second;
                this.selection.period = period;
                this.updateHidden();
            } else {
                this.clear(true);
            }
        },
        scrollToActive() {
            this.$nextTick(() => {
                ['hourOptions', 'minuteOptions', 'secondOptions', 'periodOptions'].forEach(refName => {
                    const container = this.$refs[refName];
                    if (container) {
                        const active = container.querySelector('.time-picker-option.active');
                        if (active && active.scrollIntoView) {
                            active.scrollIntoView({ block: 'center' });
                        }
                    }
                });
            });
        }
    };
};

// Pass tournaments data to JavaScript
const tournamentsBySport = @json($tournamentsBySport);

function toggleFilterDrawer() {
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');
    
    drawer.classList.toggle('open');
    overlay.classList.toggle('active');
}

// Remove individual filter
function removeFilter(paramList) {
    const url = new URL(window.location.href);
    const paramsToRemove = (paramList || '').split(',').map(param => param.trim()).filter(Boolean);

    const exEventId = url.searchParams.get('exEventId');

    paramsToRemove.forEach(param => {
        url.searchParams.delete(param);
    });

    if (exEventId) {
        url.searchParams.set('exEventId', exEventId);
    }

    url.searchParams.delete('page');

    window.location.href = url.pathname + (url.searchParams.toString() ? `?${url.searchParams.toString()}` : '');
}

// Close drawer on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const drawer = document.getElementById('filterDrawer');
        const overlay = document.getElementById('filterOverlay');
        
        if (drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('active');
        }
    }
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const sportSelect = document.getElementById('sportSelect');
    const tournamentSelect = document.getElementById('tournamentSelect');
    const tournamentSearch = document.getElementById('tournamentSearch');
    const tournamentDropdown = document.getElementById('tournamentDropdown');
    
    let isFirstLoad = true;
    
    // Initialize tournament input display
    function updateTournamentInputDisplay() {
        const selectedOption = tournamentSelect.options[tournamentSelect.selectedIndex];
        if (selectedOption && selectedOption.value !== '') {
            tournamentSearch.value = selectedOption.getAttribute('data-name');
            tournamentSearch.classList.add('text-gray-900', 'dark:text-gray-100');
            tournamentSearch.classList.remove('text-gray-400', 'dark:text-gray-500');
        } else {
            tournamentSearch.value = '';
        }
    }
    
    updateTournamentInputDisplay();
    
    // Filter tournaments based on selected sport
    function filterTournamentsBySport(sportId, preserveSelection = false) {
        const allTournaments = Array.from(tournamentSelect.options);
        
        // Show all tournaments if no sport is selected
        if (!sportId) {
            allTournaments.forEach(option => {
                option.style.display = '';
            });
            if (!preserveSelection && !isFirstLoad) {
                tournamentSelect.value = '';
                updateTournamentInputDisplay();
            }
            return;
        }
        
        // Filter tournaments by sport
        allTournaments.forEach(option => {
            const dataSport = option.getAttribute('data-sport');
            if (dataSport === sportId || option.value === '') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Clear tournament selection when sport changes
        if (!preserveSelection && !isFirstLoad) {
            const selectedTournament = tournamentSelect.options[tournamentSelect.selectedIndex];
            const selectedTournamentSport = selectedTournament ? selectedTournament.getAttribute('data-sport') : null;
            
            if (!selectedTournamentSport || selectedTournamentSport !== sportId) {
                tournamentSelect.value = '';
                updateTournamentInputDisplay();
            }
        }
    }
    
    // Handle sport selection change
    sportSelect.addEventListener('change', function() {
        const selectedSport = this.value;
        isFirstLoad = false;
        filterTournamentsBySport(selectedSport, false);
        // Clear and refocus tournament search
        tournamentSearch.value = '';
        tournamentDropdown.classList.add('hidden');
    });
    
    // Search and dropdown functionality
    tournamentSearch.addEventListener('focus', function() {
        showTournamentDropdown();
    });
    
    tournamentSearch.addEventListener('input', function() {
        showTournamentDropdown();
    });
    
    function showTournamentDropdown() {
        const searchTerm = tournamentSearch.value.trim().toLowerCase();
        const selectedSport = sportSelect.value;
        
        let filteredOptions = Array.from(tournamentSelect.options).filter(option => {
            // First, filter by sport if a sport is selected
            if (selectedSport) {
                const optionSport = option.getAttribute('data-sport');
                if (optionSport !== selectedSport && option.value !== '') {
                    return false;
                }
            }
            
            // Only filter by search term if user has typed something
            if (searchTerm) {
                const optionName = option.text.toLowerCase();
                if (!optionName.includes(searchTerm)) {
                    return false;
                }
            }
            
            return option.value === '' || option.style.display !== 'none';
        });
        
        // If no tournaments found, show a message
        if (filteredOptions.length === 0) {
            tournamentDropdown.innerHTML = `
                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No tournaments found
                </div>
            `;
            tournamentDropdown.classList.remove('hidden');
            return;
        }
        
        // Build dropdown HTML
        let dropdownHTML = '';
        filteredOptions.forEach(option => {
            const optionValue = option.value;
            const optionName = option.text;
            const isSelected = tournamentSelect.value === optionValue;
            dropdownHTML += `
                <div class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer ${isSelected ? 'bg-blue-100 dark:bg-blue-900/20 font-medium' : ''}" data-value="${optionValue}" data-name="${optionName}">
                    ${optionName}
                </div>
            `;
        });
        
        tournamentDropdown.innerHTML = dropdownHTML;
        tournamentDropdown.classList.remove('hidden');
        
        // Add click handlers to dropdown items
        tournamentDropdown.querySelectorAll('div[data-value]').forEach(item => {
            item.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const name = this.getAttribute('data-name');
                
                tournamentSelect.value = value;
                tournamentSearch.value = name;
                tournamentDropdown.classList.add('hidden');
            });
        });
    }
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!tournamentSearch.contains(event.target) && !tournamentDropdown.contains(event.target)) {
            tournamentDropdown.classList.add('hidden');
        }
    });
    
    // Apply initial filter if a sport is selected
    const initialSport = sportSelect.value;
    if (initialSport) {
        filterTournamentsBySport(initialSport, true);
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function showToast(message, type = 'error') {
        let toast = document.querySelector('.toast-notification');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <span class="toast-message"></span>
                <button type="button" aria-label="Close">&times;</button>
            `;
            document.body.appendChild(toast);
        }

        toast.classList.remove('toast-success', 'toast-error');
        toast.classList.add(type === 'success' ? 'toast-success' : 'toast-error');
        toast.querySelector('.toast-message').textContent = message;

        const closeBtn = toast.querySelector('button');
        closeBtn.onclick = () => {
            toast.classList.remove('show');
        };

        toast.classList.add('show');

        clearTimeout(toast.hideTimeout);
        toast.hideTimeout = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    document.body.addEventListener('click', async function(event) {
        const button = event.target.closest('.update-market-time-btn');
        if (!button) {
            return;
        }

        event.preventDefault();
        if (button.dataset.loading === 'true') {
            return;
        }

        const eventId = button.getAttribute('data-event-id');
        if (!eventId) {
            return;
        }

        const updateUrl = button.getAttribute('data-update-url') || `/events/${eventId}/update-market-time`;

        button.dataset.loading = 'true';
        const originalText = button.textContent;
        button.textContent = 'Updating...';
        button.classList.add('opacity-70', 'pointer-events-none');

        try {
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Failed to update time');
            }

            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center px-3 py-1 text-xs font-semibold text-white rounded-full bg-indigo-500 updated-market-time';
            badge.textContent = data.marketTime;
            button.replaceWith(badge);
            showToast('Market time updated successfully.', 'success');
        } catch (error) {
            console.error(error);
            button.textContent = 'Retry';
            showToast(error.message || 'Unable to update time. Please try again.', 'error');
            button.classList.remove('opacity-70', 'pointer-events-none');
            button.dataset.loading = 'false';
            return;
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const dateFromInput = document.querySelector('.js-event-date-from');
    const dateFromCheckbox = document.querySelector('.js-event-date-from-enabled');
    const dateToInput = document.querySelector('.js-event-date-to');
    const dateToCheckbox = document.querySelector('.js-event-date-to-enabled');
    const timeFromCheckbox = document.querySelector('.js-time-from-enabled');
    const timeToCheckbox = document.querySelector('.js-time-to-enabled');

    function syncDate(input, checkbox) {
        if (!input || !checkbox) return;
        const hasValue = Boolean(input.value);
        checkbox.disabled = !hasValue;
        if (!hasValue && checkbox.checked) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        }
    }

    function syncTime(timeCheckbox, relatedDateInput, relatedDateCheckbox) {
        if (!timeCheckbox || !relatedDateInput || !relatedDateCheckbox) return;
        const allowed = Boolean(relatedDateInput.value) && relatedDateCheckbox.checked && !relatedDateCheckbox.disabled;
        timeCheckbox.disabled = !allowed;
        if (!allowed && timeCheckbox.checked) {
            timeCheckbox.checked = false;
            timeCheckbox.dispatchEvent(new Event('change'));
        }
    }

    if (dateFromInput && dateFromCheckbox) {
        dateFromInput.addEventListener('input', () => {
            syncDate(dateFromInput, dateFromCheckbox);
            syncTime(timeFromCheckbox, dateFromInput, dateFromCheckbox);
        });
        dateFromCheckbox.addEventListener('change', () => {
            syncTime(timeFromCheckbox, dateFromInput, dateFromCheckbox);
        });
        syncDate(dateFromInput, dateFromCheckbox);
    }

    if (dateToInput && dateToCheckbox) {
        dateToInput.addEventListener('input', () => {
            syncDate(dateToInput, dateToCheckbox);
            syncTime(timeToCheckbox, dateToInput, dateToCheckbox);
        });
        dateToCheckbox.addEventListener('change', () => {
            syncTime(timeToCheckbox, dateToInput, dateToCheckbox);
        });
        syncDate(dateToInput, dateToCheckbox);
    }

    syncTime(timeFromCheckbox, dateFromInput, dateFromCheckbox);
    syncTime(timeToCheckbox, dateToInput, dateToCheckbox);
});
</script>

<style>
/* Custom tournament dropdown styles */
.tournament-dropdown-scrollable {
    scrollbar-width: auto;
    scrollbar-color: #888 #f1f1f1;
}

.tournament-dropdown-scrollable::-webkit-scrollbar {
    width: 10px;
}

.tournament-dropdown-scrollable::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-track {
    background: #374151;
}

.tournament-dropdown-scrollable::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.tournament-dropdown-scrollable::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.updated-market-time {
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
@endpush
