@extends('layouts.app')

@section('title', 'Event List')

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
        padding: 0.75rem 0.85rem;
        padding-right: 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        background-color: #ffffff;
        font-size: 0.9rem;
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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Event List</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and view all events</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @php
                        $filterCount = 0;
                        if(request('search')) $filterCount++;
                        if(request('sport')) $filterCount++;
                        if(request('tournament')) $filterCount++;
                        if(request('status')) $filterCount++;
                        if(request('event_date')) $filterCount++;
                        if((request()->filled('time_from_hour') && request()->filled('time_from_minute') && request()->filled('time_from_second')) || request()->filled('time_from')) $filterCount++;
                        if((request()->filled('time_to_hour') && request()->filled('time_to_minute') && request()->filled('time_to_second')) || request()->filled('time_to')) $filterCount++;

                        $timeFromRaw = request('time_from');
                        if (!$timeFromRaw && request()->filled('time_from_hour') && request()->filled('time_from_minute') && request()->filled('time_from_second')) {
                            $timeFromRaw = sprintf('%02d:%02d:%02d %s',
                                (int) request('time_from_hour'),
                                (int) request('time_from_minute'),
                                (int) request('time_from_second'),
                                strtoupper(request('time_from_ampm', 'AM'))
                            );
                        }

                        $timeToRaw = request('time_to');
                        if (!$timeToRaw && request()->filled('time_to_hour') && request()->filled('time_to_minute') && request()->filled('time_to_second')) {
                            $timeToRaw = sprintf('%02d:%02d:%02d %s',
                                (int) request('time_to_hour'),
                                (int) request('time_to_minute'),
                                (int) request('time_to_second'),
                                strtoupper(request('time_to_ampm', 'PM'))
                            );
                        }

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

                        $timeFromEnabled = request()->filled('time_from') || (request()->filled('time_from_hour') && request()->filled('time_from_minute'));
                        $timeToEnabled = request()->filled('time_to') || (request()->filled('time_to_hour') && request()->filled('time_to_minute'));

                        $dateFromEnabled = request()->filled('event_date_from');
                        $dateToEnabled = request()->filled('event_date_to');
                    @endphp
                    
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
                        <a href="{{ route('events.index') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
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
                        Export CSV
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
                    <a href="{{ route('events.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">Clear All</a>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if(request('search'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Search: "{{ request('search') }}"
                            <button onclick="removeFilter('search')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('sport'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Sport: {{ $sportConfig[request('sport')] ?? 'Unknown Sport (ID: ' . request('sport') . ')' }}
                            <button onclick="removeFilter('sport')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('tournament'))
                        @php
                            $tournamentName = \App\Models\Event::where('tournamentsId', request('tournament'))->first()?->tournamentsName ?? request('tournament');
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Tournament: {{ $tournamentName }}
                            <button onclick="removeFilter('tournament')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Status: {{ ucfirst(request('status')) }}
                            <button onclick="removeFilter('status')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('event_date'))
                        @php
                            $eventDateDisplay = null;
                            try {
                                $eventDateDisplay = \Carbon\Carbon::parse(request('event_date'))->format('M d, Y');
                            } catch (\Exception $e) {
                                $eventDateDisplay = request('event_date');
                            }
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Date: {{ $eventDateDisplay }}
                            <button onclick="removeFilter('event_date')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @php
                        $timeFromDisplay = null;
                        if(request()->filled('time_from_hour') && request()->filled('time_from_minute') && request()->filled('time_from_second')) {
                            $hour = str_pad((int) request('time_from_hour'), 2, '0', STR_PAD_LEFT);
                            $minute = str_pad((int) request('time_from_minute'), 2, '0', STR_PAD_LEFT);
                            $second = str_pad((int) request('time_from_second'), 2, '0', STR_PAD_LEFT);
                            $ampm = strtoupper(request('time_from_ampm', 'AM'));
                            if (!in_array($ampm, ['AM', 'PM'])) {
                                $ampm = 'AM';
                            }
                            $fromString = $hour . ':' . $minute . ':' . $second . ' ' . $ampm;
                            try {
                                $timeFromDisplay = \Carbon\Carbon::createFromFormat('h:i:s A', $fromString)->format('h:i:s A');
                            } catch (\Exception $e) {
                                $timeFromDisplay = $fromString;
                            }
                        }
                        if(!$timeFromDisplay && request()->filled('time_from')) {
                            $fallbackFormats = ['H:i:s', 'h:i:s A', 'H:i', 'h:i A'];
                            foreach ($fallbackFormats as $format) {
                                try {
                                    $timeFromDisplay = \Carbon\Carbon::createFromFormat($format, request('time_from'))->format('h:i:s A');
                                    break;
                                } catch (\Exception $e) {
                                    $timeFromDisplay = request('time_from');
                                }
                            }
                        }
 
                        $timeToDisplay = null;
                        if(request()->filled('time_to_hour') && request()->filled('time_to_minute') && request()->filled('time_to_second')) {
                            $hour = str_pad((int) request('time_to_hour'), 2, '0', STR_PAD_LEFT);
                            $minute = str_pad((int) request('time_to_minute'), 2, '0', STR_PAD_LEFT);
                            $second = str_pad((int) request('time_to_second'), 2, '0', STR_PAD_LEFT);
                            $ampm = strtoupper(request('time_to_ampm', 'PM'));
                            if (!in_array($ampm, ['AM', 'PM'])) {
                                $ampm = 'PM';
                            }
                            $toString = $hour . ':' . $minute . ':' . $second . ' ' . $ampm;
                            try {
                                $timeToDisplay = \Carbon\Carbon::createFromFormat('h:i:s A', $toString)->format('h:i:s A');
                            } catch (\Exception $e) {
                                $timeToDisplay = $toString;
                            }
                        }
                        if(!$timeToDisplay && request()->filled('time_to')) {
                            $fallbackFormats = ['H:i:s', 'h:i:s A', 'H:i', 'h:i A'];
                            foreach ($fallbackFormats as $format) {
                                try {
                                    $timeToDisplay = \Carbon\Carbon::createFromFormat($format, request('time_to'))->format('h:i:s A');
                                    break;
                                } catch (\Exception $e) {
                                    $timeToDisplay = request('time_to');
                                }
                            }
                        }
                    @endphp
                    @if($timeFromDisplay)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Time From: {{ $timeFromDisplay }}
                            <button onclick="removeTimeFilter('from')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if($timeToDisplay)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Time To: {{ $timeToDisplay }}
                            <button onclick="removeTimeFilter('to')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Events</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $paginatedEvents->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Settled</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Event::where('IsSettle', 1)->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unsettled</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Event::where('IsUnsettle', 1)->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Void</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Event::where('IsVoid', 1)->count() }}</p>
                    </div>
                </div>
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
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $event->marketTime ? \Carbon\Carbon::parse($event->marketTime)->format('M d, Y h:i A') : 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $event->tournamentsName }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                            {{ $sportConfig[$event->sportId] ?? 'Unknown Sport (ID: ' . $event->sportId . ')' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->IsSettle)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Settled</span>
                                        @elseif($event->IsVoid)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">Void</span>
                                        @elseif($event->IsUnsettle)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">Unsettled</span>
                                        @endif
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
        
        <form method="GET" action="{{ route('events.index') }}">
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
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="in_play" {{ request('status') == 'in_play' ? 'selected' : '' }}>In-Play</option>
                    <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>Settled</option>
                    <option value="unsettled" {{ request('status') == 'unsettled' ? 'selected' : '' }}>Unsettled</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>Voided</option>
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
                <div class="filter-field-title">Event Time Range (12-hour format)</div>
                <div class="time-range-container space-y-3">
                    <div class="time-block" x-data="timePickerComponent('{{ $timeFromValue }}', {{ $timeFromEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="flex items-center justify-between">
                            <div class="time-block-header">From</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_from_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-time-from-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
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
                    <div class="time-block" x-data="timePickerComponent('{{ $timeToValue }}', {{ $timeToEnabled ? 'true' : 'false' }})" x-init="init()" x-on:keydown.escape.window="close()">
                        <div class="flex items-center justify-between">
                            <div class="time-block-header">To</div>
                            <label class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="time_to_enabled" value="1" x-model="enabled" @change="updateHidden()" class="js-time-to-enabled h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
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
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Times apply to the selected event date.</p>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-600 dark:bg-primary-700 text-white py-2 px-4 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('events.index') }}" class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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

// Pass tournaments data to JavaScript
const tournamentsBySport = @json($tournamentsBySport);

function toggleFilterDrawer() {
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');
    
    drawer.classList.toggle('open');
    overlay.classList.toggle('active');
}

// Remove individual filter
function removeFilter(filterName) {
    const url = new URL(window.location);
    url.searchParams.delete(filterName);
    if (filterName === 'event_date') {
        ['time_from_hour','time_from_minute','time_from_second','time_from_ampm','time_from',
         'time_to_hour','time_to_minute','time_to_second','time_to_ampm','time_to'].forEach(param => url.searchParams.delete(param));
    }
    const exEventId = url.searchParams.get('exEventId');
    if (exEventId) {
        url.searchParams.set('exEventId', exEventId);
    }
    window.location.href = url.toString();
}

// Remove time filter
function removeTimeFilter(type) {
    const url = new URL(window.location);
    if (type === 'from') {
        url.searchParams.delete('time_from_hour');
        url.searchParams.delete('time_from_minute');
        url.searchParams.delete('time_from_second');
        url.searchParams.delete('time_from_ampm');
        url.searchParams.delete('time_from');
    } else { // type === 'to'
        url.searchParams.delete('time_to_hour');
        url.searchParams.delete('time_to_minute');
        url.searchParams.delete('time_to_second');
        url.searchParams.delete('time_to_ampm');
        url.searchParams.delete('time_to');
    }
    const exEventId = url.searchParams.get('exEventId');
    if (exEventId) {
        url.searchParams.set('exEventId', exEventId);
    }
    window.location.href = url.toString();
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

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .tournament-dropdown-scrollable::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
@endpush
