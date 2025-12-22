@extends('layouts.app')

@section('title', $pageTitle ?? 'Market List')

@push('css')
<style>
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

    .time-picker-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.55rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
        color: #111827;
        font-size: 0.875rem;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    .time-picker-button:disabled {
        cursor: not-allowed;
        background-color: #f3f4f6;
        color: #9ca3af;
    }

    .time-picker-button.placeholder {
        color: #9ca3af;
    }

    .time-picker-button:hover:not(:disabled) {
        border-color: #2563eb;
        background-color: #eff6ff;
    }

    .time-picker-icon {
        width: 1rem;
        height: 1rem;
        color: #9ca3af;
    }

    .time-picker-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        width: 100%;
        z-index: 50;
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.15);
        padding: 1rem;
    }

    .dark .time-picker-dropdown {
        background-color: #1f2937;
        border-color: rgba(59, 130, 246, 0.35);
        box-shadow: 0 15px 35px rgba(30, 64, 175, 0.35);
    }

    .time-picker-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .time-picker-column p {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .dark .time-picker-column p {
        color: #9ca3af;
    }

    .time-picker-options {
        display: grid;
        gap: 0.35rem;
        max-height: 160px;
        overflow-y: auto;
        padding-right: 0.35rem;
    }

    .time-picker-option {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.5rem;
        border-radius: 0.65rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #1f2937;
        background-color: #f3f4f6;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .time-picker-option:hover {
        background-color: #e0ecff;
        color: #1d4ed8;
    }

    .time-picker-option.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .dark .time-picker-option {
        background-color: rgba(55, 65, 81, 0.6);
        color: #e5e7eb;
    }

    .dark .time-picker-option:hover {
        background-color: rgba(59, 130, 246, 0.35);
        color: #bfdbfe;
    }

    .dark .time-picker-option.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(29, 78, 216, 0.95));
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }

    .time-picker-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .time-picker-action {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 0.65rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .time-picker-action:hover {
        border-color: #2563eb;
        background-color: #eff6ff;
        color: #1d4ed8;
    }

    .time-picker-action.primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
    }

    .time-picker-action.primary:hover:not(:disabled) {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
    }

    .time-picker-action.primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
    }

    .timepicker-hint {
        font-size: 0.7rem;
        color: #9ca3af;
        margin: 0;
    }

    .dark .timepicker-hint {
        color: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    @php
        $isMarketsAllRoute = request()->routeIs('markets.all');
        $marketsBaseRoute = $isMarketsAllRoute ? route('markets.all') : route('markets.index');
        $marketsRecentlyAddedActive = request()->boolean('recently_added');
    @endphp
        <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3 leading-tight">
                <span>{{ $pageHeading ?? 'Market List' }}</span>
                @if(request()->routeIs('markets.all'))
                    <button onclick="openMarketRulesModal()" class="flex-shrink-0 flex items-center justify-center" title="Market Rules">
                        <svg class="page-info-icon" width="24" height="24" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="12"/>
                            <text x="12" y="16.5" text-anchor="middle" font-size="14" font-weight="bold" fill="#ffffff">i</text>
                        </svg>
                    </button>
                @endif
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $pageSubheading ?? 'Market for today and tomorrow are shown here.' }}</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3 w-full sm:w-auto">
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="toggleFilter()"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                    </svg>
                    Filters
                    @if(count($activeFilters) > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ count($activeFilters) }}
                        </span>
                    @endif
                </button>
                @if(count($activeFilters) > 0)
                <button 
                    type="button"
                    onclick="clearAllFilters()"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </button>
                @endif
            </div>

            <a 
                href="{{ route('markets.export', request()->query()) }}"
                class="ml-auto inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

        <!-- Active Filters Display -->
        @if(count($activeFilters) > 0)
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Active Filters ({{ count($activeFilters) }}):</span>
                    </div>
                    <button 
                        type="button" 
                        onclick="clearAllFilters()"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                        Clear All
                    </button>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($activeFilters as $key => $value)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            {{ $key }}: {{ $value }}
                            <button onclick="removeFilter('{{ $key }}')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 md:gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">All</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($paginatedMarkets->total()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unsettled</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('status', 1)->count()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/20">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('status', 2)->count()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900/20">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In-Play</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('status', 3)->count()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/20">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pre-bet</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('isPreBet', true)->count()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Settled</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('status', 4)->count()) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-5">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700/50">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Voided</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(DB::table('market_lists')->where('status', 5)->count()) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recently Added Switcher -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Recently Added</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Show only markets flagged as recently added.</p>
                </div>
                <form method="GET" action="{{ $marketsBaseRoute }}">
                    @foreach(request()->except(['page', 'recently_added']) as $param => $value)
                        @if(is_array($value))
                            @foreach($value as $singleValue)
                                <input type="hidden" name="{{ $param }}[]" value="{{ $singleValue }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    @unless($marketsRecentlyAddedActive)
                        <input type="hidden" name="recently_added" value="1">
                    @endunless
                    <button type="submit" class="relative inline-flex items-center h-7 rounded-full w-14 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 {{ $marketsRecentlyAddedActive ? 'bg-primary-600 dark:bg-primary-500' : 'bg-gray-300 dark:bg-gray-600' }}" aria-pressed="{{ $marketsRecentlyAddedActive ? 'true' : 'false' }}">
                        <span class="sr-only">Toggle recently added filter</span>
                        <span class="absolute left-1 text-xs font-semibold uppercase tracking-wide {{ $marketsRecentlyAddedActive ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">On</span>
                        <span class="absolute right-1 text-xs font-semibold uppercase tracking-wide {{ $marketsRecentlyAddedActive ? 'text-white/60' : 'text-white' }}">Off</span>
                        <span class="inline-block w-6 h-6 transform bg-white dark:bg-gray-200 rounded-full transition-transform duration-200 ease-in-out {{ $marketsRecentlyAddedActive ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Markets Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Markets</h3>
            </div>
            
            @if($paginatedMarkets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event & Market Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market & Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport & Tournament</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status, Winner & Result</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($paginatedMarkets as $market)
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
                                                <button @click="calculatePosition($event); open = !open" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="options-menu-{{ $market->id }}" aria-expanded="false" aria-haspopup="true">
                                                    <span class="sr-only">Open options menu</span>
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" 
                                                 :style="`position: fixed; left: ${position.x}px; top: ${position.y}px; z-index: 9999;`"
                                                 class="w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="options-menu-{{ $market->id }}">
                                                <div class="py-1" role="none">
                                                    <a href="{{ route('markets.show', $market->id) }}" @click="open = false" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View Details
                                                    </a>
                                                    
                                                    <a href="{{ route('market-rates.index', ['exEventId' => $market->exEventId]) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                        </svg>
                                                        View Rates
                                                    </a>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="#" 
                                                class="hover:text-primary-600 dark:hover:text-primary-400 cursor-pointer transition-colors view-market-modal"
                                                data-market='{!! json_encode([
                                                    'id' => $market->id,
                                                    '_id' => $market->_id ?? null,
                                                    'eventName' => $market->eventName ?? 'N/A',
                                                    'exEventId' => $market->exEventId ?? 'N/A',
                                                    'exMarketId' => $market->exMarketId ?? 'N/A',
                                                    'marketName' => $market->marketName ?? 'N/A',
                                                    'marketTime' => $market->marketTime ? \Carbon\Carbon::parse($market->marketTime)->format('M j, Y g:i A') : 'N/A',
                                                    'sportName' => $market->sportName ?? 'N/A',
                                                    'tournamentsName' => $market->tournamentsName ?? 'N/A',
                                                    'type' => $market->type ?? 'N/A',
                                                    'status' => $market->status,
                                                    'isLive' => $market->isLive ?? false,
                                                    'isPreBet' => $market->isPreBet ?? false,
                                                    'labels' => $market->labels ?? null,
                                                    'selectionName' => $market->selectionName ?? null,
                                                    'winnerType' => $market->winnerType ?? null,
                                                ]) !!}'>
                                                {{ $market->eventName }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Exch Event ID: {{ $market->exEventId }}</div>
                                        <div class="mt-1">
                                            @if($market->marketTime)
                                                @php
                                                    $marketTimeDate = \Carbon\Carbon::parse($market->marketTime);
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $marketTimeDate->format('j M Y h:i:s A') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $market->marketName }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $market->exMarketId ?? 'N/A' }}</div>
                                        @if(isset($market->completeTime) && $market->completeTime)
                                            <div class="mt-1">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 w-fit">
                                                    Complete: {{ \Carbon\Carbon::parse($market->completeTime)->format('M d, Y h:i A') }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 mb-1 w-fit">
                                                {{ $market->sportName }}
                                            </span>
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $market->tournamentsName }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @php
                                                $normalizedStatus = null;
                                                if (!is_null($market->status)) {
                                                    $intStatus = (int) $market->status;
                                                    $normalizedStatus = match ($intStatus) {
                                                        1 => 'UNSETTLED',
                                                        2 => 'UPCOMING',
                                                        3 => 'INPLAY',
                                                        4 => 'SETTLED',
                                                        5 => 'VOIDED',
                                                        6 => 'REMOVED',
                                                        default => null,
                                                    };
                                                }

                                                $statusMeta = [
                                                    'UNSETTLED' => ['label' => 'Unsettled', 'class' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300'],
                                                    'UPCOMING' => ['label' => 'Upcoming', 'class' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300'],
                                                    'INPLAY' => ['label' => 'In Play', 'class' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300'],
                                                    'SETTLED' => ['label' => 'Settled', 'class' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300'],
                                                    'VOIDED' => ['label' => 'Voided', 'class' => 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200'],
                                                    'REMOVED' => ['label' => 'Removed', 'class' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300'],
                                                ];

                                                $statusBadgeClass = $normalizedStatus && isset($statusMeta[$normalizedStatus])
                                                    ? $statusMeta[$normalizedStatus]['class']
                                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';

                                                $statusLabel = $normalizedStatus && isset($statusMeta[$normalizedStatus])
                                                    ? $statusMeta[$normalizedStatus]['label']
                                                    : 'Unknown';
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full w-fit {{ $statusBadgeClass }}">
                                                Status: {{ $statusLabel }}
                                            </span>
                                            @if($market->selectionName)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 w-fit">
                                                    Winner: {{ $market->selectionName }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $paginatedMarkets->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No markets found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Try adjusting your filters or check back later for new markets.
                    </p>
                </div>
            @endif
        </div>

<!-- Filter Overlay -->
<div id="filterOverlay" class="filter-overlay" onclick="toggleFilter()"></div>

<!-- Filter Drawer -->
<div id="filterDrawer" class="filter-drawer">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Filter Markets</h2>
            <button 
                type="button" 
                onclick="toggleFilter()"
                class="rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ $marketsBaseRoute }}">
            @if($marketsRecentlyAddedActive)
                <input type="hidden" name="recently_added" value="1">
            @endif
            @php
                $timeFromRaw = request('time_from');
                $timeToRaw = request('time_to');

                $timeFormats = ['h:i:s A', 'H:i:s', 'h:i A', 'H:i'];

                $timeFromDisplay = null;
                if ($timeFromRaw) {
                    foreach ($timeFormats as $format) {
                        try {
                            $timeFromDisplay = \Carbon\Carbon::createFromFormat($format, $timeFromRaw)->format('h:i:s A');
                            break;
                        } catch (\Exception $e) {
                            $timeFromDisplay = $timeFromRaw;
                        }
                    }
                }

                $timeToDisplay = null;
                if ($timeToRaw) {
                    foreach ($timeFormats as $format) {
                        try {
                            $timeToDisplay = \Carbon\Carbon::createFromFormat($format, $timeToRaw)->format('h:i:s A');
                            break;
                        } catch (\Exception $e) {
                            $timeToDisplay = $timeToRaw;
                        }
                    }
                }

                $timeFromValue = $timeFromDisplay;
                $timeToValue = $timeToDisplay;

                $defaultPickerTime = \Carbon\Carbon::now(config('app.timezone', 'UTC'))->format('h:i:s A');

                if(!$timeFromValue) {
                    $timeFromValue = $defaultPickerTime;
                }

                if(!$timeToValue) {
                    $timeToValue = $defaultPickerTime;
                }

                $dateFromEnabled = request()->boolean('date_from_enabled') && request()->filled('date_from');
                $dateToEnabled = request()->boolean('date_to_enabled') && request()->filled('date_to');

                $timeFromEnabled = request()->boolean('time_from_enabled') && $dateFromEnabled && !empty($timeFromRaw);
                $timeToEnabled = request()->boolean('time_to_enabled') && $dateToEnabled && !empty($timeToRaw);
            @endphp
            <!-- Search -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" 
                       placeholder="Search Events or Markets By Name and Ex. ID">
            </div>
            
            <!-- Sport -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sport</label>
                <select name="sport" id="sportSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">-- Select Sport --</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport }}" {{ request('sport') == $sport ? 'selected' : '' }}>
                            {{ $sport }}
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
                            <option value="{{ $tournament->tournamentsName }}" data-sport="{{ $tournament->sportName }}" {{ request('tournament') == $tournament->tournamentsName ? 'selected' : '' }}>{{ $tournament->tournamentsName }}</option>
                        @endforeach
                    </select>
                    <div id="tournamentDropdown" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-y-auto hidden tournament-dropdown-scrollable">
                    </div>
                </div>
            </div>
            
            <!-- Status Select -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Unsettled</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Upcoming</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>In Play</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Settled</option>
                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Voided</option>
                    <option value="6" {{ request('status') == '6' ? 'selected' : '' }}>Removed</option>
                </select>
            </div>
            
            <!-- Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_live" value="1" {{ request('is_live') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In-Play Markets</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_prebet" value="1" {{ request('is_prebet') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pre-bet Markets</span>
                    </label>
                </div>
            </div>
            
            <!-- Date Range -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">Event Date Range</div>
                <div class="space-y-3">
                    <div class="filter-field-row">
                        <span class="filter-field-label">From</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Select start date" class="js-market-date-from flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                        <label class="filter-field-apply">
                            <input type="checkbox" name="date_from_enabled" value="1" {{ $dateFromEnabled ? 'checked' : '' }} class="js-market-date-from-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            Apply
                        </label>
                    </div>
                    <div class="filter-field-row">
                        <span class="filter-field-label">To</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Select end date" class="js-market-date-to flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                        <label class="filter-field-apply">
                            <input type="checkbox" name="date_to_enabled" value="1" {{ $dateToEnabled ? 'checked' : '' }} class="js-market-date-to-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            Apply
                        </label>
                    </div>
                </div>
            </div>

            <!-- Event Time Range (12-hour format) -->
            <div class="mb-4 filter-field-group">
                <div class="filter-field-title">Event Time Range (12-hour format)</div>
                <div class="time-range-container space-y-3">
                    <div class="time-block" x-data="timePickerComponent('{{ $timeFromValue }}', {{ $timeFromEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="flex items-center justify-between">
                            <div class="time-block-header">From</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_from_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-market-time-from-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2">Apply</span>
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
                            <div class="time-picker-dropdown" x-show="open">
                                <div class="time-picker-grid">
                                    <div class="time-picker-column">
                                        <p>Hour</p>
                                        <div class="time-picker-options">
                                            <template x-for="hour in hours" :key="hour">
                                                <button type="button" class="time-picker-option" @click="setHour(hour)" :class="{ 'active': selection.hour === hour }">
                                                    <span x-text="hour"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Minute</p>
                                        <div class="time-picker-options">
                                            <template x-for="minute in minutes" :key="minute">
                                                <button type="button" class="time-picker-option" @click="setMinute(minute)" :class="{ 'active': selection.minute === minute }">
                                                    <span x-text="minute"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Second</p>
                                        <div class="time-picker-options">
                                            <template x-for="second in seconds" :key="second">
                                                <button type="button" class="time-picker-option" @click="setSecond(second)" :class="{ 'active': selection.second === second }">
                                                    <span x-text="second"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Period</p>
                                        <div class="time-picker-options">
                                            <template x-for="period in periods" :key="period">
                                                <button type="button" class="time-picker-option" @click="setPeriod(period)" :class="{ 'active': selection.period === period }">
                                                    <span x-text="period"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-picker-actions">
                                    <button type="button" class="time-picker-action" @click="confirm">OK</button>
                                    <button type="button" class="time-picker-action" @click="clear">Clear</button>
                                </div>
                            </div>
                        </div>
                        <p class="timepicker-hint">Example: 02:30:00 PM</p>
                    </div>
                    <div class="time-block" x-data="timePickerComponent('{{ $timeToValue }}', {{ $timeToEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="flex items-center justify-between">
                            <div class="time-block-header">To</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_to_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-market-time-to-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2">Apply</span>
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
                            <div class="time-picker-dropdown" x-show="open">
                                <div class="time-picker-grid">
                                    <div class="time-picker-column">
                                        <p>Hour</p>
                                        <div class="time-picker-options">
                                            <template x-for="hour in hours" :key="hour">
                                                <button type="button" class="time-picker-option" @click="setHour(hour)" :class="{ 'active': selection.hour === hour }">
                                                    <span x-text="hour"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Minute</p>
                                        <div class="time-picker-options">
                                            <template x-for="minute in minutes" :key="minute">
                                                <button type="button" class="time-picker-option" @click="setMinute(minute)" :class="{ 'active': selection.minute === minute }">
                                                    <span x-text="minute"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Second</p>
                                        <div class="time-picker-options">
                                            <template x-for="second in seconds" :key="second">
                                                <button type="button" class="time-picker-option" @click="setSecond(second)" :class="{ 'active': selection.second === second }">
                                                    <span x-text="second"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="time-picker-column">
                                        <p>Period</p>
                                        <div class="time-picker-options">
                                            <template x-for="period in periods" :key="period">
                                                <button type="button" class="time-picker-option" @click="setPeriod(period)" :class="{ 'active': selection.period === period }">
                                                    <span x-text="period"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-picker-actions">
                                    <button type="button" class="time-picker-action" @click="confirm">OK</button>
                                    <button type="button" class="time-picker-action" @click="clear">Clear</button>
                                </div>
                            </div>
                        </div>
                        <p class="timepicker-hint">Example: 11:45:30 PM</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Times apply to the selected date range.</p>
            </div>

            <!-- Filter Actions -->
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 font-medium text-sm transition-colors">
                    Apply Filters
                </button>
                <button type="button" onclick="clearAllFilters()" class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 font-medium text-sm transition-colors">
                    Clear All
                </button>
            </div>
        </form>
    </div>
</div>
    </div>
</div>

<!-- Rules Modal -->
<div id="marketRulesModalOverlay" class="rules-modal-overlay"></div>
<div id="marketRulesModal" class="rules-modal">
    <div class="rules-modal__content">
        <div class="flex items-center justify-between mb-4">
            <h3 id="marketRulesModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Market Rules</h3>
            <button onclick="closeMarketRulesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="marketRulesModalContent" class="space-y-4">
            <!-- Content will be populated dynamically -->
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeMarketRulesModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openMarketRulesModal() {
    const modal = document.getElementById('marketRulesModal');
    const overlay = document.getElementById('marketRulesModalOverlay');
    const modalTitle = document.getElementById('marketRulesModalTitle');
    const modalContent = document.getElementById('marketRulesModalContent');
    
    modalTitle.textContent = 'Market Rules';
    modalContent.innerHTML = ''; // Empty content for now
    
    modal.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMarketRulesModal() {
    const modal = document.getElementById('marketRulesModal');
    const overlay = document.getElementById('marketRulesModalOverlay');
    
    modal.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal when clicking overlay
document.getElementById('marketRulesModalOverlay')?.addEventListener('click', function() {
    closeMarketRulesModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('marketRulesModal');
        if (modal && modal.classList.contains('active')) {
            closeMarketRulesModal();
        }
    }
});
</script>
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

// Pass data to JavaScript
const tournamentsBySport = @json($tournamentsBySport);
const marketTypesByTournament = @json($marketTypesByTournament);
const eventsByTournament = @json($eventsByTournament);
const marketTypesByEvent = @json($marketTypesByEvent);
const marketsBaseRoute = '{{ $marketsBaseRoute }}';

function toggleFilter() {
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');
    
    drawer.classList.toggle('open');
    overlay.classList.toggle('active');
    
    // Prevent body scroll when drawer is open
    if (drawer.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

function clearAllFilters() {
    window.location.href = marketsBaseRoute;
}

function removeFilter(filterKey) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Remove the specific filter parameter
    switch(filterKey) {
        case 'Sport':
            params.delete('sport');
            break;
        case 'Tournament':
            params.delete('tournament');
            break;
        case 'Live':
            params.delete('is_live');
            break;
        case 'Pre-bet':
            params.delete('is_prebet');
            break;
        case 'From Date':
            params.delete('date_from');
            params.delete('date_from_enabled');
            params.delete('time_from');
            params.delete('time_from_enabled');
            break;
        case 'To Date':
            params.delete('date_to');
            params.delete('date_to_enabled');
            params.delete('time_to');
            params.delete('time_to_enabled');
            break;
        case 'From Time':
            params.delete('time_from');
            params.delete('time_from_enabled');
            break;
        case 'To Time':
            params.delete('time_to');
            params.delete('time_to_enabled');
            break;
        case 'Status':
            params.delete('status');
            break;
        case 'Search':
            params.delete('search');
            break;
        case 'Recently Added':
            params.delete('recently_added');
            break;
    }
    
    params.delete('page');
    
    // Redirect with updated parameters
    window.location.href = url.pathname + (params.toString() ? '?' + params.toString() : '');
}

// Close drawer on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const drawer = document.getElementById('filterDrawer');
        if (drawer.classList.contains('open')) {
            toggleFilter();
        }
    }
});

// Initialize dependent dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const sportSelect = document.getElementById('sportSelect');
    const tournamentSelect = document.getElementById('tournamentSelect');
    const tournamentSearch = document.getElementById('tournamentSearch');
    const tournamentDropdown = document.getElementById('tournamentDropdown');
    
    let isFirstLoad = true;
    
    // Update tournament input display
    function updateTournamentInputDisplay() {
        const selectedOption = tournamentSelect.options[tournamentSelect.selectedIndex];
        if (selectedOption && selectedOption.value !== '') {
            tournamentSearch.value = selectedOption.text;
            tournamentSearch.classList.add('text-gray-900', 'dark:text-gray-100');
            tournamentSearch.classList.remove('text-gray-400', 'dark:text-gray-500');
        } else {
            tournamentSearch.value = '';
        }
    }
    
    updateTournamentInputDisplay();
    
    // Filter tournaments based on selected sport
    function filterTournamentsBySport(sportName, preserveSelection = false) {
        const allTournaments = Array.from(tournamentSelect.options);
        
        if (!sportName) {
            allTournaments.forEach(option => {
                option.style.display = '';
            });
            if (!preserveSelection && !isFirstLoad) {
                tournamentSelect.value = '';
                updateTournamentInputDisplay();
            }
            return;
        }
        
        allTournaments.forEach(option => {
            const optionSport = option.getAttribute('data-sport');
            if (optionSport === sportName || option.value === '') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        if (!preserveSelection && !isFirstLoad) {
            const selectedTournament = tournamentSelect.options[tournamentSelect.selectedIndex];
            const selectedTournamentSport = selectedTournament ? selectedTournament.getAttribute('data-sport') : null;
            
            if (!selectedTournamentSport || selectedTournamentSport !== sportName) {
                tournamentSelect.value = '';
                updateTournamentInputDisplay();
            }
        }
    }
    
    
    // Tournament dropdown functionality
    function showTournamentDropdown() {
        const searchTerm = tournamentSearch.value.trim().toLowerCase();
        const selectedSport = sportSelect.value;
        
        let filteredOptions = Array.from(tournamentSelect.options).filter(option => {
            if (selectedSport) {
                const optionSport = option.getAttribute('data-sport');
                if (optionSport !== selectedSport && option.value !== '') {
                    return false;
                }
            }
            
            if (searchTerm) {
                const optionName = option.text.toLowerCase();
                if (!optionName.includes(searchTerm)) {
                    return false;
                }
            }
            
            return option.value === '' || option.style.display !== 'none';
        });
        
        if (filteredOptions.length === 0) {
            tournamentDropdown.innerHTML = `
                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No tournaments found
                </div>
            `;
            tournamentDropdown.classList.remove('hidden');
            return;
        }
        
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
    
    // Event listeners
    sportSelect.addEventListener('change', function() {
        const selectedSport = this.value;
        isFirstLoad = false;
        filterTournamentsBySport(selectedSport, false);
        tournamentSearch.value = '';
        tournamentDropdown.classList.add('hidden');
    });
    
    tournamentSearch.addEventListener('focus', function() {
        showTournamentDropdown();
    });
    
    tournamentSearch.addEventListener('input', function() {
        showTournamentDropdown();
    });
    
    // Hide dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!tournamentSearch.contains(event.target) && !tournamentDropdown.contains(event.target)) {
            tournamentDropdown.classList.add('hidden');
        }
    });
    
    // Apply initial filters
    const initialSport = sportSelect.value;
    const initialTournament = tournamentSelect.value;
    
    if (initialSport) {
        filterTournamentsBySport(initialSport, true);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const dateFromInput = document.querySelector('.js-market-date-from');
    const dateFromCheckbox = document.querySelector('.js-market-date-from-enabled');
    const dateToInput = document.querySelector('.js-market-date-to');
    const dateToCheckbox = document.querySelector('.js-market-date-to-enabled');
    const timeFromCheckbox = document.querySelector('.js-market-time-from-enabled');
    const timeToCheckbox = document.querySelector('.js-market-time-to-enabled');

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

// Market Details Modal Functions
window.openMarketModal = function(market) {
    const modal = document.getElementById('marketDetailsModal');
    const overlay = document.getElementById('marketDetailsModalOverlay');
    
    if (!modal || !overlay) {
        console.error('Modal elements not found');
        return;
    }
    
    // Populate modal with market data
    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value || 'N/A';
    };
    
    setText('modalTitle', market.marketName || 'Market Details');
    setText('modalMarketNameText', market.marketName);
    setText('modalEventName', market.eventName);
    setText('modalExEventId', market.exEventId);
    setText('modalExMarketId', market.exMarketId);
    setText('modalMarketId', market._id);
    setText('modalMarketTime', market.marketTime);
    setText('modalSportName', market.sportName);
    setText('modalTournamentName', market.tournamentsName);
    setText('modalType', market.type);
    
    // Winner badge
    const winnerEl = document.getElementById('modalWinner');
    if (winnerEl) {
        winnerEl.textContent = market.selectionName || 'N/A';
    }
    
    // Result badge
    const resultEl = document.getElementById('modalResult');
    if (resultEl) {
        resultEl.textContent = market.winnerType || 'N/A';
    }
    
    // Labels
    const labelsContainer = document.getElementById('modalLabelsContainer');
    if (labelsContainer) {
        if (market.labels) {
            try {
                const labels = typeof market.labels === 'string' ? JSON.parse(market.labels) : market.labels;
                const labelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
                const labelNames = {
                    '4x': '4X',
                    'b2c': 'B2C',
                    'b2b': 'B2B',
                    'usdt': 'USDT'
                };
                
                let labelsHtml = '';
                
                labelKeys.forEach(key => {
                    const value = labels[key];
                    const isChecked = typeof value === 'boolean' ? value : (value && value.checked === true);
                    
                    if (isChecked) {
                        const checkerName = typeof value === 'object' && value.checker_name ? value.checker_name : null;
                        const chorId = typeof value === 'object' && value.chor_id ? value.chor_id : null;
                        const remark = typeof value === 'object' && value.remark ? value.remark : null;
                        const checkedAt = typeof value === 'object' && value.checked_at ? value.checked_at : null;
                        
                        // Format timestamp
                        let formattedTime = '';
                        if (checkedAt) {
                            try {
                                const date = new Date(checkedAt);
                                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                const month = monthNames[date.getMonth()];
                                const day = date.getDate();
                                const year = date.getFullYear();
                                let hours = date.getHours();
                                const minutes = date.getMinutes().toString().padStart(2, '0');
                                const ampm = hours >= 12 ? 'PM' : 'AM';
                                hours = hours % 12;
                                hours = hours ? hours : 12;
                                formattedTime = `${month} ${day}, ${year}, ${hours}:${minutes} ${ampm}`;
                            } catch (e) {
                                formattedTime = checkedAt;
                            }
                        }
                        
                        // Build label name with checker name
                        let labelDisplay = labelNames[key];
                        if (checkerName) {
                            labelDisplay = `${labelNames[key]} - ${checkerName}`;
                        }
                        
                        // Build card for checked label
                        labelsHtml += `
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-5 h-5 bg-blue-600 rounded border-2 border-blue-600 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm mb-2">${labelDisplay}</div>
                                        ${chorId ? `<div class="text-xs text-gray-600 dark:text-gray-300 mb-1">Froude IDs: ${chorId}</div>` : ''}
                                        ${remark ? `<div class="text-xs text-gray-600 dark:text-gray-300 mb-1">remark: ${remark}</div>` : ''}
                                        ${formattedTime ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${formattedTime}</div>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Build card for unchecked label
                        labelsHtml += `
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-5 h-5 bg-gray-200 dark:bg-gray-600 rounded border-2 border-gray-300 dark:border-gray-500"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm mb-2">${labelNames[key]}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Not checked</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                
                labelsContainer.innerHTML = labelsHtml;
            } catch (e) {
                labelsContainer.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">No labels checked</p>';
            }
        } else {
            labelsContainer.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">No labels checked</p>';
        }
    }
    
    // Status
    let statusText = 'Unknown';
    let statusClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
    if (market.status !== null && market.status !== undefined) {
        const statusMap = {
            1: 'UNSETTLED',
            2: 'UPCOMING',
            3: 'INPLAY',
            4: 'SETTLED',
            5: 'VOIDED',
            6: 'REMOVED'
        };
        statusText = statusMap[market.status] || 'Unknown';
        
        if (market.status == 3 || market.isLive) {
            statusClass = 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300';
        } else if (market.status == 2 || market.isPreBet) {
            statusClass = 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300';
        } else if (market.status == 4) {
            statusClass = 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300';
        }
    }
    
    const statusEl = document.getElementById('modalStatus');
    if (statusEl) {
        statusEl.textContent = statusText;
        statusEl.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}`;
    }
    
    // Show modal
    overlay.classList.add('active');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

window.closeMarketModal = function() {
    const modal = document.getElementById('marketDetailsModal');
    const overlay = document.getElementById('marketDetailsModalOverlay');
    
    if (overlay) overlay.classList.remove('active');
    if (modal) modal.classList.remove('active');
    document.body.style.overflow = '';
};

// Setup modal overlay click handler
function setupMarketModalOverlayHandler() {
    const overlay = document.getElementById('marketDetailsModalOverlay');
    const modal = document.getElementById('marketDetailsModal');
    const modalContent = document.querySelector('#marketDetailsModal > div');
    
    if (overlay && !overlay.dataset.handlerAttached) {
        overlay.dataset.handlerAttached = 'true';
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeMarketModal();
            }
        });
    }
    
    // Add click handler to modal container (but not content)
    if (modal && !modal.dataset.handlerAttached) {
        modal.dataset.handlerAttached = 'true';
        modal.addEventListener('click', function(e) {
            // Close if clicking on the modal container itself (not the content)
            if (e.target === modal) {
                closeMarketModal();
            }
        });
    }
    
    // Prevent clicks inside modal content from closing it
    if (modalContent && !modalContent.dataset.handlerAttached) {
        modalContent.dataset.handlerAttached = 'true';
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Handle event name clicks to open modal
document.addEventListener('click', function(e) {
    const eventNameLink = e.target.closest('.view-market-modal');
    if (eventNameLink) {
        e.preventDefault();
        e.stopPropagation();
        
        const marketData = eventNameLink.getAttribute('data-market');
        if (marketData) {
            try {
                const market = JSON.parse(marketData);
                openMarketModal(market);
            } catch (error) {
                console.error('Error parsing market data:', error);
                console.error('Raw data:', marketData);
            }
        }
    }
});

// Close modal on overlay click and setup event listeners
document.addEventListener('DOMContentLoaded', function() {
    setupMarketModalOverlayHandler();
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('marketDetailsModal');
            if (modal && modal.classList.contains('active')) {
                closeMarketModal();
            }
        }
    });
});

// Ensure overlay handler is set up when modal is opened
const originalOpenMarketModal = window.openMarketModal;
window.openMarketModal = function(market) {
    setupMarketModalOverlayHandler();
    return originalOpenMarketModal(market);
};
</script>

<!-- Market Details Modal -->
<div id="marketDetailsModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 opacity-0 invisible transition-opacity duration-200"></div>
<div id="marketDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center opacity-0 invisible transition-opacity duration-200 pointer-events-none" style="display: flex;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalTitle">Market Details</h3>
            <button onclick="closeMarketModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5">
            <!-- Status & Results -->
            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Status, Winner & Result</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200" id="modalStatus">Unknown</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Winner</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300" id="modalWinner">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Result</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300" id="modalResult">-</span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Market Information -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Market Information</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Market Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium" id="modalMarketNameText">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Market Type</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200" id="modalType">-</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Market ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono" id="modalMarketId">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Exch Market ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono break-all" id="modalExMarketId">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Market Time</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" id="modalMarketTime">-</p>
                    </div>
                </div>
                
                <!-- Event Information -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Event Information</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Event Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium" id="modalEventName">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Exch Event ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono break-all" id="modalExEventId">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sport</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300" id="modalSportName">-</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tournament</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" id="modalTournamentName">-</p>
                    </div>
                </div>
            </div>
            
            <!-- Labels Information -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Betlist Check Details</h4>
                <div id="modalLabelsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full">No labels checked</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom tournament and market type dropdown styles */
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

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Market Details Modal Styles */
#marketDetailsModalOverlay.active {
    opacity: 1;
    visibility: visible;
}

#marketDetailsModal.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}
</style>
@endpush
