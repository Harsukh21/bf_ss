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
        border-radius: 1rem;
        padding: 1.5rem;
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

    $hasSearch = request()->has('search') && trim((string) $searchValue) !== '';
    $hasSport = request()->has('sport') && trim((string) $sportValue) !== '';
    $hasTournament = request()->has('tournament') && trim((string) $tournamentValue) !== '';
    $hasDateFrom = request()->has('date_from') && trim((string) $dateFromValue) !== '';
    $hasDateTo = request()->has('date_to') && trim((string) $dateToValue) !== '';

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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
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
                                        <input type="checkbox" class="js-event-toggle" data-event-id="{{ $event->id }}" data-event-name="{{ $event->eventName }}" data-event-data="{{ json_encode([
                                            'eventId' => $event->eventId,
                                            'exEventId' => $event->exEventId,
                                            'eventName' => $event->eventName,
                                            'sportName' => $event->sportName,
                                            'tournamentsName' => $event->tournamentsName,
                                            'inplay_markets_count' => $event->inplay_markets_count,
                                            'formatted_market_time' => $event->formatted_market_time,
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
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                                        {{ $event->inplay_markets_count }} Market(s)
                                    </span>
                                </td>
                            </tr>
                            <tr class="bg-gray-50/60 dark:bg-gray-800/70 text-xs text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700">
                                <td colspan="4" class="px-6 py-3">
                                    <div class="flex flex-wrap items-center gap-6">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Labels:</span>
                                        @foreach($labelConfig as $labelKey => $labelName)
                                            @php
                                                $labelChecked = isset($event->labels[$labelKey]) && (bool)$event->labels[$labelKey] === true;
                                            @endphp
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    class="js-label-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                                    data-event-id="{{ $event->exEventId }}"
                                                    data-label-key="{{ $labelKey }}"
                                                    @checked($labelChecked)
                                                >
                                                <span class="uppercase">{{ $labelName }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
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
        <div class="mt-6 flex justify-end">
            <button onclick="closeEventModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                Close
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
        url.searchParams.delete(param);
        url.searchParams.delete('page'); // Reset to page 1
        window.location.href = url.toString();
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

        // Handle label checkbox changes (UI only - no database updates)
        const checkboxes = document.querySelectorAll('.js-label-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const eventId = this.getAttribute('data-event-id');
                const labelKey = this.getAttribute('data-label-key');
                const checked = this.checked;
                
                // UI only - checkbox toggles visually
                // Actual functionality will be implemented later
                console.log(`Label ${labelKey} for event ${eventId} toggled to: ${checked}`);
            });
        });

        // Handle event toggle switches
        const eventToggles = document.querySelectorAll('.js-event-toggle');
        eventToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    // Open modal when toggle is turned on
                    const eventData = JSON.parse(this.getAttribute('data-event-data'));
                    openEventModal(eventData);
                } else {
                    // Close modal when toggle is turned off
                    closeEventModal();
                }
            });
        });
    });

    // Event Modal Functions
    function openEventModal(eventData) {
        const modal = document.getElementById('eventModal');
        const overlay = document.getElementById('eventModalOverlay');
        const content = document.getElementById('eventModalContent');
        
        // Populate modal content
        content.innerHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Event Name</label>
                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">${eventData.eventName || 'N/A'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Event ID</label>
                    <p class="text-base text-gray-900 dark:text-gray-100">${eventData.eventId || 'N/A'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">External Event ID</label>
                    <p class="text-base text-gray-900 dark:text-gray-100">${eventData.exEventId || 'N/A'}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sport</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200">
                            ${eventData.sportName || 'N/A'}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tournament</label>
                        <p class="text-base text-gray-900 dark:text-gray-100">${eventData.tournamentsName || 'N/A'}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">In-Play Markets</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                            ${eventData.inplay_markets_count || 0} Market(s)
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Event Time</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200">
                            ${eventData.formatted_market_time || 'N/A'}
                        </span>
                    </div>
                </div>
            </div>
        `;
        
        modal.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        const overlay = document.getElementById('eventModalOverlay');
        
        modal.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Uncheck all toggles
        const toggles = document.querySelectorAll('.js-event-toggle');
        toggles.forEach(toggle => {
            toggle.checked = false;
        });
    }

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
</script>
@endpush
@endsection
