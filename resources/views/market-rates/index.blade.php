@extends('layouts.app')

@section('title', 'SS Rates List')

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

    .time-block {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .time-block-header {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }

    .dark .time-block-header {
        color: #9ca3af;
    }

    .time-picker-panel {
        position: relative;
    }

    .time-picker-input-wrapper {
        position: relative;
    }

    .time-picker-input {
        width: 100%;
        display: flex;
        align-items: center;
        padding: 0.75rem 2.75rem 0.75rem 0.9rem;
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-weight: 600;
        color: #111827;
        transition: all 0.15s ease-in-out;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .time-picker-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .time-picker-input::placeholder {
        color: #9ca3af;
        font-weight: 500;
    }

    .dark .time-picker-input {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }

    .dark .time-picker-input::placeholder {
        color: #9ca3af;
    }

    .time-picker-input-icon {
        position: absolute;
        top: 50%;
        right: 0.9rem;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        transition: all 0.15s ease-in-out;
    }

    .time-picker-input-icon:hover {
        background-color: #e5e7eb;
    }

    .dark .time-picker-input-icon {
        background-color: #4b5563;
        border-color: #4b5563;
        color: #d1d5db;
    }

    .time-picker-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 0.9rem;
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-weight: 600;
        color: #111827;
        transition: all 0.15s ease-in-out;
    }

    .time-picker-button.placeholder {
        color: #9ca3af;
        font-weight: 500;
    }

    .time-picker-button:hover {
        border-color: #2563eb;
    }

    .dark .time-picker-button {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }

    .dark .time-picker-button.placeholder {
        color: #9ca3af;
        font-weight: 500;
    }

    .time-picker-icon {
        width: 1rem;
        height: 1rem;
        color: #6b7280;
    }

    .time-picker-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        width: 100%;
        background: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        padding: 1rem;
        z-index: 50;
    }

    .dark .time-picker-dropdown {
        background: #1f2937;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        border: 1px solid #374151;
    }

    .time-picker-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .time-picker-column {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .time-picker-column p {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6b7280;
        margin: 0;
    }

    .dark .time-picker-column p {
        color: #9ca3af;
    }

    .time-picker-options {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.35rem;
        max-height: 12rem;
        overflow-y: auto;
        padding-right: 0.25rem;
    }

    .time-picker-options-minute,
    .time-picker-options-second {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .time-picker-options-period {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .time-picker-option {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 0.65rem;
        background-color: #f3f4f6;
        border-radius: 0.65rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.15s ease-in-out;
    }

    .time-picker-option:hover {
        background-color: #e5e7eb;
    }

    .time-picker-option.active {
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
    }

    .dark .time-picker-option {
        background-color: #374151;
        color: #f3f4f6;
        border: 1px solid #4b5563;
    }

    .dark .time-picker-option:hover {
        background-color: #4b5563;
    }

    .dark .time-picker-option.active {
        background-color: #2563eb;
        border-color: #2563eb;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    }

    .time-picker-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
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
                            Export CSV
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
                                                    @if($rate->inplay)
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                                            In Play
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">Not In Play</span>
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
                    <div class="time-block" x-data="timePickerComponent('{{ $timeFromValue ?? '' }}', true)" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="time-block-header">From</div>
                        <div class="time-picker-panel">
                            <input type="hidden" name="time_from" x-ref="hidden">
                            <div class="time-picker-input-wrapper">
                                <input
                                    type="text"
                                    x-ref="input"
                                    x-model="textValue"
                                    placeholder="HH:MM:SS AM/PM"
                                    class="time-picker-input"
                                    @focus="open = true; scrollToActive()"
                                    @input="handleManualInput"
                                    @keydown.enter.prevent="confirm()"
                                />
                                <button type="button" class="time-picker-input-icon" @click.prevent="toggle()">
                                    <svg class="time-picker-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
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
                        <p class="timepicker-hint">Example: 02:30:00 PM</p>
                    </div>
                    <div class="time-block" x-data="timePickerComponent('{{ $timeToValue ?? '' }}', true)" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="time-block-header">To</div>
                        <div class="time-picker-panel">
                            <input type="hidden" name="time_to" x-ref="hidden">
                            <div class="time-picker-input-wrapper">
                                <input
                                    type="text"
                                    x-ref="input"
                                    x-model="textValue"
                                    placeholder="HH:MM:SS AM/PM"
                                    class="time-picker-input"
                                    @focus="open = true; scrollToActive()"
                                    @input="handleManualInput"
                                    @keydown.enter.prevent="confirm()"
                                />
                                <button type="button" class="time-picker-input-icon" @click.prevent="toggle()">
                                    <svg class="time-picker-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
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
                        <p class="timepicker-hint">Example: 11:45:30 PM</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Times apply to the selected date.</p>
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
<script>
window.timePickerComponent = function(initialValue, initiallyEnabled = false) {
    return {
        open: false,
        hours: Array.from({ length: 12 }, (_, i) => String(i + 1).padStart(2, '0')),
        minutes: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')),
        seconds: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')),
        periods: ['AM', 'PM'],
        selection: { hour: '', minute: '', second: '', period: 'AM' },
        enabled: initiallyEnabled,
        textValue: '',
        init() {
            const normalized = (initialValue || '').toString();
            if (normalized) {
                this.setFromString(normalized, { preserveOnInvalid: true });
            } else if (this.$refs.hidden) {
                this.$refs.hidden.value = '';
            }

            this.$watch('enabled', () => {
                this.updateHidden();
            });
        },
        get display() {
            return this.formatValue() || 'Select time';
        },
        get isComplete() {
            return this.selection.hour && this.selection.minute && this.selection.period;
        },
        formatValue() {
            if (!this.selection.hour || !this.selection.minute || !this.selection.period) {
                return '';
            }
            const second = this.selection.second || '00';
            return `${this.selection.hour}:${this.selection.minute}:${second} ${this.selection.period}`;
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    if (this.$refs.input) {
                        this.$refs.input.focus();
                    }
                    this.scrollToActive();
                });
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
                const value = this.formatValue();
                this.$refs.hidden.value = value;
                this.textValue = value;
                return value;
            } else {
                this.$refs.hidden.value = '';
            }
            return null;
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
            this.textValue = '';
            if (!skipClose) {
                this.close();
            }
            this.updateHidden();
        },
        setFromString(value, options = {}) {
            const { preserveOnInvalid = false } = options;
            const trimmed = (value || '').trim();
            if (!trimmed) {
                this.clear(true);
                if (!preserveOnInvalid) {
                    this.textValue = '';
                }
                return true;
            }

            const normalized = trimmed.toUpperCase();
            const match = normalized.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?$/);
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

                this.ensureDefaults();
                const formatted = this.updateHidden() || this.formatValue();
                if (formatted) {
                    this.textValue = formatted;
                }
                return true;
            }

            if (!preserveOnInvalid) {
                this.clear(true);
            }
            return false;
        },
        handleManualInput() {
            let raw = (this.textValue || '').toUpperCase();

            // Keep only digits, spaces, A, P, M, and colons
            raw = raw.replace(/[^0-9APM: ]/g, '');

            // Determine desired period if user typed AM/PM (partial allowed)
            let period = '';
            if (raw.includes('PM')) {
                period = 'PM';
            } else if (raw.includes('AM')) {
                period = 'AM';
            } else {
                const letterTrail = raw.replace(/[^AP]/g, '');
                if (letterTrail.endsWith('P')) {
                    period = 'PM';
                } else if (letterTrail.endsWith('A')) {
                    period = 'AM';
                }
            }

            // Extract up to 6 digits for HHMMSS
            const digits = raw.replace(/[^0-9]/g, '').slice(0, 6);
            let formatted = '';

            if (digits.length >= 2) {
                formatted = digits.slice(0, 2);
                const minutePart = digits.slice(2, 4);
                formatted += ':' + minutePart;

                const secondPart = digits.slice(4, 6);
                if (digits.length > 4) {
                    formatted += ':' + secondPart;
                }
            } else {
                formatted = digits;
            }

            // Handle partial seconds formatting (e.g., HH:MM:S)
            if (digits.length === 5) {
                formatted = `${digits.slice(0, 2)}:${digits.slice(2, 4)}:${digits.slice(4)}`;
            }

            if (digits.length >= 6 && formatted.length && period) {
                formatted = formatted.split(' ')[0] + ' ' + period;
            } else {
                formatted = formatted.split(' ')[0];
            }

            this.textValue = formatted.trim();

            if (!this.setFromString(this.textValue, { preserveOnInvalid: true })) {
                if (this.$refs.hidden) {
                    this.$refs.hidden.value = '';
                }
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
    const formatted = formatDateInputValue(event.target.value);
    event.target.value = formatted;
}

function handleDateBlur(event) {
    const value = event.target.value;
    if (value && !isValidDateValue(value)) {
        event.target.value = '';
    }
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
        input.addEventListener('input', handleDateInput);
        input.addEventListener('blur', handleDateBlur);
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