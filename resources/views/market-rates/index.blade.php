@extends('layouts.app')

@section('title', 'Market Rates')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Market Rates List
                        @if($selectedEventId && $eventInfo)
                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                                - {{ $eventInfo->eventName }}
                            </span>
                        @endif
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        @if($selectedEventId)
                            Viewing market rates for selected event
                        @else
                            Select an event to view market rates
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    @php
                        $filterCount = 0;
                        if(request('search')) $filterCount++;
                        if(request('market_name')) $filterCount++;
                        if(request('status')) $filterCount++;
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
                </div>
            </div>
        </div>

        <!-- Event Selection Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Select Event</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Choose an event to view its market rates</p>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('market-rates.index') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label for="exEventId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Event
                        </label>
                        <select name="exEventId" id="exEventId" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select an event...</option>
                            @foreach($events as $event)
                                <option value="{{ $event->exEventId }}" {{ $selectedEventId == $event->exEventId ? 'selected' : '' }}>
                                    {{ $event->eventName }} ({{ $event->eventId }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-primary-600 dark:bg-primary-700 text-white px-6 py-2 rounded-md hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        View Rates
                    </button>
                </form>
            </div>
        </div>

        @if($selectedEventId)
            @if($eventInfo && $marketRates->count() > 0)
                <!-- Filter Drawer -->
                <div id="filterDrawer" class="hidden mb-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Filters</h3>
                        </div>
                        <div class="p-6">
                            <form method="GET" action="{{ route('market-rates.index') }}">
                                <input type="hidden" name="exEventId" value="{{ $selectedEventId }}">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                               placeholder="Search markets..." 
                                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="market_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Type</label>
                                        <select name="market_name" id="market_name" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">All Markets</option>
                                            <option value="Match Winner" {{ request('market_name') == 'Match Winner' ? 'selected' : '' }}>Match Winner</option>
                                            <option value="Over/Under 2.5 Goals" {{ request('market_name') == 'Over/Under 2.5 Goals' ? 'selected' : '' }}>Over/Under 2.5 Goals</option>
                                            <option value="Total Sets" {{ request('market_name') == 'Total Sets' ? 'selected' : '' }}>Total Sets</option>
                                            <option value="Total Runs" {{ request('market_name') == 'Total Runs' ? 'selected' : '' }}>Total Runs</option>
                                            <option value="Total Points" {{ request('market_name') == 'Total Points' ? 'selected' : '' }}>Total Points</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                        <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">All Status</option>
                                            <option value="inplay" {{ request('status') == 'inplay' ? 'selected' : '' }}>In Play</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-3 mt-4">
                                    <a href="{{ route('market-rates.index', ['exEventId' => $selectedEventId]) }}" 
                                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Clear
                                    </a>
                                    <button type="submit" class="px-4 py-2 bg-primary-600 dark:bg-primary-700 text-white rounded-md hover:bg-primary-700 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Market Rates Table -->
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Market Rates ({{ $marketRates->total() }} total)
                            </h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Market</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Market ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Runners</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($marketRates as $rate)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('market-rates.show', $rate->id, ['exEventId' => $selectedEventId]) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $rate->marketName }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rate->exMarketId }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                                                $runnerCount = is_array($runners) ? count($runners) : 0;
                                            @endphp
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $runnerCount }} runners</div>
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
                                                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                                                            <td class="py-1 pr-2 text-gray-900 dark:text-gray-100 truncate max-w-32" title="{{ $runner['runnerName'] ?? 'Unknown' }}">
                                                                                {{ $runner['runnerName'] ?? 'Unknown' }}
                                                                            </td>
                                                                            <td class="py-1 text-right">
                                                                                @if(isset($runner['availableToBack']) && is_array($runner['availableToBack']) && count($runner['availableToBack']) > 0)
                                                                                    @php
                                                                                        $bestBack = $runner['availableToBack'][0];
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
                                                                                @if(isset($runner['availableToLay']) && is_array($runner['availableToLay']) && count($runner['availableToLay']) > 0)
                                                                                    @php
                                                                                        $bestLay = $runner['availableToLay'][0];
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
                                            @if($rate->isCompleted)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Completed</span>
                                            @elseif($rate->inplay)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span>
                                                    In Play
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">Upcoming</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $rate->created_at ? \Carbon\Carbon::parse($rate->created_at)->format('M d, Y H:i') : 'N/A' }}
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
            @elseif($eventInfo)
                <!-- No Data Message -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No market rates found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No market rates data available for <strong>{{ $eventInfo->eventName }}</strong>.
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            The market rates table for this event may not exist yet.
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
                        Please select an event from the dropdown above to view its market rates.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .dark .select2-container--default .select2-selection--single {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f9fafb !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }
    .dark .select2-dropdown {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
    }
    .dark .select2-results__option {
        background-color: #374151 !important;
        color: #f9fafb !important;
    }
    .dark .select2-results__option--highlighted {
        background-color: #4b5563 !important;
    }
</style>
@endpush

@push('js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function toggleFilterDrawer() {
    const drawer = document.getElementById('filterDrawer');
    drawer.classList.toggle('hidden');
}

// Initialize Select2 for event dropdown
document.addEventListener('DOMContentLoaded', function() {
    $('#exEventId').select2({
        placeholder: 'Search and select an event...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#exEventId').parent()
    });
});
</script>
@endpush
@endsection