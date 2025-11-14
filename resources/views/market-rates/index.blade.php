@extends('layouts.app')

@section('title', 'SS Rates List')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .filter-drawer {
        position: fixed;
        top: 0;
        right: -600px;
        width: 560px;
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
    
    
    .animate-slide-in {
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
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

    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        SS Rates List
                        @if($selectedEventId && $eventInfo)
                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                                - {{ $eventInfo->eventName }}
                            </span>
                        @endif
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        @if($selectedEventId)
                            Viewing SS rates for selected event
                        @else
                            Select an event to view SS rates
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="toggleFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center relative" @if(!$selectedEventId) disabled @endif>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters
                        @if($filterCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">{{ $filterCount }}</span>
                        @endif
                    </button>
                    @if($filterCount > 0 && $selectedEventId)
                        <a href="{{ route('market-rates.index', ['exEventId' => $selectedEventId]) }}" class="bg-red-600 dark:bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-700 dark:hover:bg-red-800 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </a>
                    @endif
                    @if($selectedEventId && $marketRates->count() > 0)
                        <a href="{{ route('market-rates.export', request()->all()) }}" class="ml-auto bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($filterCount > 0 && $selectedEventId)
        <div class="mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Active Filters ({{ $filterCount }}):</span>
                    </div>
                    <a href="{{ route('market-rates.index', ['exEventId' => $selectedEventId]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">Clear All</a>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if(request('market_name'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Market: {{ request('market_name') }}
                            <button onclick="removeFilter('market_name')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('filter_date'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Date: {{ request('filter_date') }}
                            <button onclick="removeFilter('filter_date')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('time_from'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            From Time: {{ request('time_from') }}
                            <button onclick="removeFilter('time_from')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('time_to'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            To Time: {{ request('time_to') }}
                            <button onclick="removeFilter('time_to')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Validation Alert -->
        <div id="validation-alert" class="hidden mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200 px-4 py-3 rounded-md animate-slide-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">Please select a market event first</p>
                    <p class="text-xs mt-1 text-yellow-700 dark:text-yellow-300">Type or select an event from the dropdown above to view its market rates.</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" onclick="closeValidationAlert()" class="inline-flex bg-yellow-50 dark:bg-yellow-900/20 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 dark:focus:ring-offset-yellow-900/20 focus:ring-yellow-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Selection Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Select Event</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Choose an event to view its SS rates</p>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('market-rates.index') }}" id="eventSelectionForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-3 relative">
                        <label for="eventSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Event
                        </label>
                        <input type="text" 
                               id="eventSearch" 
                               placeholder="Search events..." 
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                               value="{{ $eventInfo ? trim($eventInfo->eventName . ($eventInfo->formattedDate ?? false ? ' - ' . $eventInfo->formattedDate : '')) : '' }}"
                               autocomplete="off">
                        <input type="hidden" name="exEventId" id="exEventId" value="{{ $selectedEventId }}" required>
                        
                        <!-- Searchable dropdown -->
                        <div id="eventDropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <!-- Options will be populated by JavaScript -->
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 opacity-0 pointer-events-none">
                            Action
                        </label>
                        <button type="submit" id="viewRatesBtn" class="w-full bg-primary-600 dark:bg-primary-700 text-white px-6 py-2 rounded-md hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors flex items-center justify-center">
                            <span id="viewRatesBtnText">View Rates</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedEventId)
            @if($eventInfo && $marketRates->count() > 0)

                <!-- Market Rates Table -->
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                SS Rates ({{ $marketRates->total() }} total)
                            </h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Runners</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Market & Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SS TIME</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($marketRates as $rate)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('market-rates.show', $rate->id) . '?exEventId=' . urlencode($selectedEventId) }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 min-w-[300px]">
                                            @php
                                                $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                                                $runnerCount = is_array($runners) ? count($runners) : 0;
                                            @endphp
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-900 dark:text-gray-100 mb-2">VOL: {{ number_format($rate->totalMatched ?? 0, 2) }}</div>
                                                @if(is_array($runners) && count($runners) > 0)
                                                    <div class="min-w-0">
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-full text-xs">
                                                                <thead>
                                                                    <tr class="border-b border-gray-200 dark:border-gray-600">
                                                                        <th class="text-left py-1 text-gray-500 dark:text-gray-400 font-medium">Runner</th>
                                                                        <th class="text-right py-1 text-gray-500 dark:text-gray-400 font-medium">Back</th>
                                                                        <th class="text-right py-1 text-gray-500 dark:text-gray-400 font-medium">Lay</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($runners as $runner)
                                                                        @php
                                                                            // Handle both array and object structures
                                                                            $runner = is_array($runner) ? $runner : (array) $runner;
                                                                            $runnerName = $runner['runnerName'] ?? 'Unknown';
                                                                            
                                                                            // Get exchange data
                                                                            $exchange = is_array($runner['exchange'] ?? null) ? $runner['exchange'] : (array) ($runner['exchange'] ?? []);
                                                                            $availableToBack = $exchange['availableToBack'] ?? [];
                                                                            $availableToLay = $exchange['availableToLay'] ?? [];
                                                                            
                                                                            // Convert to arrays
                                                                            $availableToBack = is_array($availableToBack) ? $availableToBack : (array) $availableToBack;
                                                                            $availableToLay = is_array($availableToLay) ? $availableToLay : (array) $availableToLay;
                                                                        @endphp
                                                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                                                            <td class="py-1 pr-2 text-gray-900 dark:text-gray-100 truncate max-w-56" title="{{ $runnerName }}">
                                                                                {{ $runnerName }}
                                                                            </td>
                                                                            <td class="py-1 text-right">
                                                                                @if(is_array($availableToBack) && count($availableToBack) > 0)
                                                                                    @php
                                                                                        $bestBack = is_array($availableToBack[0]) ? $availableToBack[0] : (array) $availableToBack[0];
                                                                                        $backOdds = $bestBack['price'] ?? 0;
                                                                                        $backSize = $bestBack['size'] ?? 0;
                                                                                    @endphp
                                                                                    <div class="text-green-600 dark:text-green-400 font-medium">{{ number_format($backOdds, 2) }}</div>
                                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($backSize, 2) }}</div>
                                                                                @else
                                                                                    <div class="text-gray-400 dark:text-gray-500">-</div>
                                                                                @endif
                                                                            </td>
                                                                            <td class="py-1 text-right">
                                                                                @if(is_array($availableToLay) && count($availableToLay) > 0)
                                                                                    @php
                                                                                        // Find the LAY entry with maximum price value (best lay odds)
                                                                                        $maxLayPrice = 0;
                                                                                        $bestLay = null;
                                                                                        foreach ($availableToLay as $lay) {
                                                                                            $lay = is_array($lay) ? $lay : (array) $lay;
                                                                                            $layPriceValue = $lay['price'] ?? 0;
                                                                                            if ($layPriceValue > $maxLayPrice) {
                                                                                                $maxLayPrice = $layPriceValue;
                                                                                                $bestLay = $lay;
                                                                                            }
                                                                                        }
                                                                                        $layOdds = $bestLay['price'] ?? 0;
                                                                                        $laySize = $bestLay['size'] ?? 0;
                                                                                    @endphp
                                                                                    <div class="text-red-600 dark:text-red-400 font-medium">{{ number_format($layOdds, 2) }}</div>
                                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($laySize, 2) }}</div>
                                                                                @else
                                                                                    <div class="text-gray-400 dark:text-gray-500">-</div>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 italic">No runner data available</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="space-y-2">
                                                <!-- Market Name Badge -->
                                                <div>
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                                        {{ $rate->marketName ?? 'Unknown Market' }}
                                                    </span>
                                                </div>
                                                <!-- Status Badge -->
                                                <div>
                                                    @php
                                                        $winnerType = $rate->marketListWinnerType ?? null;
                                                        $statusLabel = $rate->marketListStatus ?? null;
                                    $filteredVolumeMax = request('volume_max');
                                                    @endphp
                                                    @if(!empty($winnerType))
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300">
                                                            Winner: {{ $rate->marketListSelectionName ?? $winnerType }}
                                                        </span>
                                                    @elseif(!empty($statusLabel))
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 dark:bg-gray-900/30 text-gray-800 dark:text-gray-200">
                                                            {{ ucfirst(strtolower($statusLabel)) }}
                                                        </span>
                                                    @elseif($rate->inplay)
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                                            In Play
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">Not In Play</span>
                                                    @endif
                                @if($filteredVolumeMax !== null)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Volume: {{ number_format($rate->totalMatched ?? 0, 2) }}
                                    </div>
                                @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $rate->created_at ? \Carbon\Carbon::parse($rate->created_at)->format('M d, Y h:i:s A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($marketRates->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $marketRates->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @elseif($eventInfo && $ratesTableNotFound)
                <!-- Rates Table Not Found Message -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">SS Rates Table Not Found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            The SS rates table for <strong>{{ $eventInfo->eventName }}</strong> does not exist in the database.
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            Event ID: {{ $selectedEventId }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                            The table <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">market_rates_{{{ $selectedEventId }}}</code> was not found.
                        </p>
                    </div>
                </div>
            @elseif($eventInfo && $marketRates->count() === 0)
                <!-- No Data Message -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No SS rates found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No SS rates data available for <strong>{{ $eventInfo->eventName }}</strong>.
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            The SS rates table exists but contains no data.
                        </p>
                    </div>
                </div>
            @else
                <!-- Invalid Event -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Event not found
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <p>The selected event ({{ $selectedEventId }}) was not found in the system.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No Event Selected -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Select an Event</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Please select an event from the dropdown above to view its SS rates.
                    </p>
                </div>
            </div>
        @endif

<!-- Filter Drawer -->
@if($selectedEventId)
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
        
        <form method="GET" action="{{ route('market-rates.index') }}">
            <input type="hidden" name="exEventId" value="{{ $selectedEventId }}">
            @php
                $timeFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
                $timeFromValue = request('time_from');
                $timeToValue = request('time_to');

                if ($timeFromValue) {
                    foreach ($timeFormats as $format) {
                        try {
                            $timeFromValue = \Carbon\Carbon::createFromFormat($format, $timeFromValue)->format('h:i:s A');
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                if ($timeToValue) {
                    foreach ($timeFormats as $format) {
                        try {
                            $timeToValue = \Carbon\Carbon::createFromFormat($format, $timeToValue)->format('h:i:s A');
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            @endphp
            
            <!-- Market Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Type</label>
                <select name="market_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Markets</option>
                    @foreach($availableMarketNames as $marketName)
                        <option value="{{ $marketName }}" {{ request('market_name') == $marketName ? 'selected' : '' }}>{{ $marketName }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Date Picker -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">Select Date</div>
                <input
                    type="text"
                    name="filter_date"
                    value="{{ request('filter_date', $defaultFilterDate) }}"
                    placeholder="DD/MM/YYYY"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 js-date-input">
            </div>

            <!-- Time Range -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">SS Time Range (12-hour format)</div>
                <div class="time-range-container">
                    <div class="time-input-group js-time-input-container">
                        <span class="time-input-label">From</span>
                        <div class="time-input-wrapper">
                            <input type="text" name="time_from" value="{{ $timeFromValue ?? '' }}" class="time-input-field js-time-input" placeholder="HH:MM:SS AM/PM" autocomplete="off">
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
                        <span class="time-input-label">To</span>
                        <div class="time-input-wrapper">
                            <input type="text" name="time_to" value="{{ $timeToValue ?? '' }}" class="time-input-field js-time-input" placeholder="HH:MM:SS AM/PM" autocomplete="off">
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
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Times apply to the selected date.</p>
            </div>

            <!-- Volume Filter -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">Volume (Total Matched)</div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Less Than or Equal</label>
                <input
                    type="number"
                    step="any"
                    min="0"
                    name="volume_max"
                    value="{{ request('volume_max') }}"
                    placeholder="Enter max volume"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="flex-1 bg-primary-600 dark:bg-primary-700 text-white py-2 px-4 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('market-rates.index', ['exEventId' => $selectedEventId]) }}" class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>
@endif

@push('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function formatTimeValue(rawValue) {
    if (!rawValue) {
        return '';
    }

    const cleaned = rawValue.toUpperCase().trim();
    const match = cleaned.match(/^(\d{1,2}):(\d{2}):(\d{2})\s*(AM|PM)$/);

    if (!match) {
        return null;
    }

    let hour = parseInt(match[1], 10);
    const minute = parseInt(match[2], 10);
    const second = parseInt(match[3], 10);
    const period = match[4].toUpperCase();

    if (hour < 1 || hour > 12) {
        return null;
    }

    if (isNaN(minute) || minute < 0 || minute > 59) {
        return null;
    }

    if (isNaN(second) || second < 0 || second > 59) {
        return null;
    }

    return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:${String(second).padStart(2, '0')} ${period}`;
}

function formatPartialTime(rawValue) {
    if (!rawValue) {
        return '';
    }

    const upperRaw = rawValue.toUpperCase();
    const digits = upperRaw.replace(/[^0-9]/g, '').slice(0, 6);

    const hour = digits.slice(0, 2);
    const minute = digits.slice(2, 4);
    const second = digits.slice(4, 6);

    let result = '';

    if (hour.length) {
        result += hour;
        if (hour.length === 2) {
            result += ':';
        }
    }

    if (minute.length) {
        result += minute;
        if (minute.length === 2) {
            result += ':';
        }
    }

    if (second.length) {
        result += second;
    }

    let suffix = '';
    const suffixRaw = upperRaw.replace(/[^APM]/g, '');
    if (suffixRaw.startsWith('PM')) {
        suffix = 'PM';
    } else if (suffixRaw.startsWith('AM')) {
        suffix = 'AM';
    } else if (suffixRaw.startsWith('P')) {
        suffix = 'P';
    } else if (suffixRaw.startsWith('A')) {
        suffix = 'A';
    }

    if (suffix) {
        let trimmedResult = result.trim();
        while (trimmedResult.endsWith(':')) {
            trimmedResult = trimmedResult.slice(0, -1);
        }

        result = trimmedResult ? `${trimmedResult} ${suffix}` : suffix;
    }

    return result;
}

function getTimeTokenCount(value, caretIndex) {
    if (!value || caretIndex <= 0) {
        return 0;
    }

    const preview = value.slice(0, caretIndex);
    return preview.replace(/[^0-9APMapm]/g, '').length;
}

function setCaretFromTokenCount(input, tokenCount) {
    if (!input) {
        return;
    }

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

function setupTimeInputs() {
    if (!window.__timeDropdownOutsideHandler) {
        document.addEventListener('click', event => {
            document.querySelectorAll('.js-time-input-container.open').forEach(container => {
                if (!container.contains(event.target)) {
                    container.classList.remove('open');
                    const inputEl = container.querySelector('.js-time-input');
                    if (inputEl) {
                        inputEl.dispatchEvent(new Event('blur'));
                    }
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

        if (!input) {
            return;
        }

        const state = {
            hour: '',
            minute: '',
            second: '',
            period: '',
            lastValidValue: input.value || '',
            lastValidTokenCount: getTimeTokenCount(input.value || '', (input.value || '').length)
        };

        const setLastValid = (value, caretTokens = null) => {
            state.lastValidValue = value || '';
            if (typeof caretTokens === 'number') {
                state.lastValidTokenCount = Math.max(0, caretTokens);
            } else {
                state.lastValidTokenCount = getTimeTokenCount(state.lastValidValue, state.lastValidValue.length);
            }
        };

        const resetState = () => {
            state.hour = '';
            state.minute = '';
            state.second = '';
            state.period = '';
        };

        const showError = () => {
            input.classList.add('invalid');
            if (errorEl) {
                errorEl.classList.add('active');
            }
        };

        const hideError = () => {
            input.classList.remove('invalid');
            if (errorEl) {
                errorEl.classList.remove('active');
            }
        };

        const isStateComplete = () => state.hour && state.minute && state.second && state.period;

        const updateApplyButton = () => {
            if (applyBtn) {
                applyBtn.disabled = !isStateComplete();
            }
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
                state.hour = hour || '';
                state.minute = minute || '';
                state.second = second || '';
                state.period = period || '';
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

            // Basic range validation on partial input
            const tokens = input.value.replace(/[^0-9]/g, '');
            const currentHour = tokens.slice(0, 2);
            const currentMinute = tokens.slice(2, 4);
            const currentSecond = tokens.slice(4, 6);
            const lastChar = rawValue.charAt(rawCaret - 1);

            const maybeInvalid =
                (currentHour.length === 2 && parseInt(currentHour, 10) > 12) ||
                (currentMinute.length === 2 && parseInt(currentMinute, 10) > 59) ||
                (currentSecond.length === 2 && parseInt(currentSecond, 10) > 59);

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
                    state.hour = hour;
                    state.minute = minute;
                    state.second = second;
                    state.period = period;
                } else {
                    resetState();
                }
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
            dropdown.addEventListener('mousedown', event => {
                event.preventDefault();
            });

            optionButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const unit = btn.dataset.unit;
                    const value = btn.dataset.value;
                    if (!unit) {
                        return;
                    }

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
                if (container.classList.contains('open')) {
                    return;
                }

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

function formatDateInputValue(raw) {
    const digits = (raw || '').replace(/[^0-9]/g, '').slice(0, 8);
    let formatted = '';

    if (digits.length >= 2) {
        formatted = digits.slice(0, 2);
    } else {
        formatted = digits;
    }

    if (digits.length >= 3) {
        formatted += '/' + digits.slice(2, 4);
    } else if (digits.length > 2) {
        formatted += '/' + digits.slice(2);
    }

    if (digits.length >= 5) {
        formatted += '/' + digits.slice(4, 8);
    } else if (digits.length > 4) {
        formatted += '/' + digits.slice(4);
    }

    return formatted;
}

function getDateTokenCount(value, caretIndex) {
    if (!value || caretIndex <= 0) {
        return 0;
    }
    const preview = value.slice(0, caretIndex);
    return preview.replace(/[^0-9]/g, '').length;
}

function setDateCaretFromTokenCount(input, tokenCount) {
    if (!input) {
        return;
    }

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
    if (!match) {
        return false;
    }

    const day = parseInt(match[1], 10);
    const month = parseInt(match[2], 10);
    const year = parseInt(match[3], 10);

    if (month < 1 || month > 12 || day < 1 || day > 31) {
        return false;
    }

    const date = new Date(year, month - 1, day);
    return (
        date.getFullYear() === year &&
        date.getMonth() === month - 1 &&
        date.getDate() === day
    );
}

function handleDateInput(event) {
    const input = event.target;
    if (typeof input.__lastValidDateValue === 'undefined') {
        input.__lastValidDateValue = formatDateInputValue(input.value);
        input.__lastValidDateTokens = getDateTokenCount(
            input.__lastValidDateValue,
            (input.__lastValidDateValue || '').length
        );
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

    const maybeInvalid =
        (monthValue.length === 2 && (parseInt(monthValue, 10) < 1 || parseInt(monthValue, 10) > 12)) ||
        (dayValue.length === 2 && (parseInt(dayValue, 10) < 1 || parseInt(dayValue, 10) > 31));

    if (maybeInvalid && /\d/.test(lastChar)) {
        input.value = input.__lastValidDateValue || '';
        setDateCaretFromTokenCount(input, input.__lastValidDateTokens || 0);
        return;
    }

    input.__lastValidDateValue = input.value;
    input.__lastValidDateTokens = getDateTokenCount(
        input.value,
        input.selectionStart || input.value.length
    );
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
    input.__lastValidDateTokens = getDateTokenCount(
        input.value,
        input.value.length
    );
}

const events = @json($events);

function toggleFilterDrawer() {
    const btn = event.target.closest('button');
    if (btn && btn.disabled) return;
    
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');
    
    if (drawer && overlay) {
        drawer.classList.toggle('open');
        overlay.classList.toggle('active');
    }
}

// Function to show validation alert
function showValidationAlert() {
    const alert = document.getElementById('validation-alert');
    if (alert) {
        alert.classList.remove('hidden');
        
        // Scroll to top to show the alert
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            closeValidationAlert();
        }, 5000);
    }
}

// Function to close validation alert
function closeValidationAlert() {
    const alert = document.getElementById('validation-alert');
    if (alert) {
        alert.classList.add('hidden');
    }
}

// Close drawer on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const drawer = document.getElementById('filterDrawer');
        const overlay = document.getElementById('filterOverlay');
        
        if (drawer && overlay && drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('active');
        }
    }
});

// Searchable Event Dropdown & Date Input handlers
document.addEventListener('DOMContentLoaded', function() {
    const eventSearch = document.getElementById('eventSearch');
    const eventDropdown = document.getElementById('eventDropdown');
    const exEventIdInput = document.getElementById('exEventId');
    
    if (!eventSearch || !eventDropdown || !exEventIdInput) return;
    
    setupTimeInputs();

    function populateDropdown(searchTerm = '') {
        eventDropdown.innerHTML = '';
        
        const filteredEvents = events.filter(event => {
            const searchLower = searchTerm.toLowerCase();
            return event.eventName.toLowerCase().includes(searchLower) || 
                   event.eventId.toString().includes(searchTerm);
        });
        
        if (filteredEvents.length === 0) {
            eventDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">No events found</div>';
            return;
        }
        
        filteredEvents.slice(0, 20).forEach(event => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm text-gray-900 dark:text-gray-100';
            div.innerHTML = `
                <div class="flex flex-col">
                    <span class="font-medium">${event.eventName}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">ID: ${event.eventId}</span>
                    ${event.formattedDate ? `<span class="text-xs text-gray-500 dark:text-gray-400">${event.formattedDate}</span>` : ''}
                </div>
            `;

            div.addEventListener('click', function() {
                exEventIdInput.value = event.exEventId;
                const displayValue = event.formattedDate ? `${event.eventName} - ${event.formattedDate}` : event.eventName;
                eventSearch.value = displayValue;
                eventDropdown.classList.add('hidden');
            });

            eventDropdown.appendChild(div);
        });
    }
    
    eventSearch.addEventListener('focus', function() {
        eventDropdown.classList.remove('hidden');
        populateDropdown();
    });
    
    eventSearch.addEventListener('input', function() {
        populateDropdown(this.value);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!eventSearch.contains(e.target) && !eventDropdown.contains(e.target)) {
            eventDropdown.classList.add('hidden');
        }
    });

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

// Remove individual filter
function removeFilter(filterName) {
    const url = new URL(window.location);
    const exEventId = url.searchParams.get('exEventId');
    
    if (filterName === 'filter_date') {
        url.searchParams.delete('time_from');
        url.searchParams.delete('time_to');
    }

    // Remove the filter parameter
    url.searchParams.delete(filterName);
    
    // Ensure exEventId is preserved
    if (exEventId) {
        url.searchParams.set('exEventId', exEventId);
    }
    
    window.location.href = url.toString();
}
</script>
@endpush
@endsection