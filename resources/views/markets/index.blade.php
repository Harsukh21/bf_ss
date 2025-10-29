@extends('layouts.app')

@section('title', 'Market List')

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
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Market List</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Manage and filter betting markets
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button 
                type="button" 
                onclick="toggleFilter()"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Markets</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $paginatedMarkets->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Live Markets</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ DB::table('market_lists')->where('isLive', true)->count() }}</p>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pre-bet Markets</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ DB::table('market_lists')->where('isPreBet', true)->count() }}</p>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Scheduled Markets</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ DB::table('market_lists')->where('isLive', false)->where('isPreBet', false)->count() }}</p>
                    </div>
                </div>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sport</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tournament</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Market Time</th>
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
                                                    <a href="{{ route('markets.show', $market->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
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
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $market->marketName }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $market->_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Event ID: {{ $market->exEventId }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                            {{ $market->sportName }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $market->tournamentsName }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $market->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($market->isLive)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                                <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span>
                                                Live
                                            </span>
                                        @elseif($market->isPreBet)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">Pre-bet</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Scheduled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($market->marketTime)->format('M d, Y H:i:s') }}
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

        <form method="GET" action="{{ route('markets.index') }}">
            <!-- Search -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" 
                       placeholder="Search markets or events...">
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
            
            <!-- Market Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Type</label>
                <div class="relative">
                    <input type="text" id="marketTypeSearch" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Click to see all types or search..." autocomplete="off">
                    <select name="type" id="marketTypeSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 absolute inset-0 opacity-0 pointer-events-none">
                        <option value="">-- Select Type --</option>
                        @foreach($marketTypes as $type)
                            <option value="{{ $type->type }}" data-tournament="{{ $type->tournamentsName }}" {{ request('type') == $type->type ? 'selected' : '' }}>{{ $type->type }}</option>
                        @endforeach
                    </select>
                    <div id="marketTypeDropdown" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-y-auto hidden tournament-dropdown-scrollable">
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_live" value="1" {{ request('is_live') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Live Markets</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_prebet" value="1" {{ request('is_prebet') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pre-bet Markets</span>
                    </label>
                </div>
            </div>
            
            <!-- Date Range -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    </div>
                    <div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    </div>
                </div>
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
@endsection

@push('scripts')
<script>
// Pass data to JavaScript
const tournamentsBySport = @json($tournamentsBySport);
const marketTypesByTournament = @json($marketTypesByTournament);

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
    // Redirect to markets index without any query parameters
    window.location.href = '{{ route("markets.index") }}';
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
        case 'Type':
            params.delete('type');
            break;
        case 'Live':
            params.delete('is_live');
            break;
        case 'Pre-bet':
            params.delete('is_prebet');
            break;
        case 'From Date':
            params.delete('date_from');
            break;
        case 'To Date':
            params.delete('date_to');
            break;
        case 'Search':
            params.delete('search');
            break;
    }
    
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
    const marketTypeSelect = document.getElementById('marketTypeSelect');
    const marketTypeSearch = document.getElementById('marketTypeSearch');
    const marketTypeDropdown = document.getElementById('marketTypeDropdown');
    
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
    
    // Update market type input display
    function updateMarketTypeInputDisplay() {
        const selectedOption = marketTypeSelect.options[marketTypeSelect.selectedIndex];
        if (selectedOption && selectedOption.value !== '') {
            marketTypeSearch.value = selectedOption.text;
            marketTypeSearch.classList.add('text-gray-900', 'dark:text-gray-100');
            marketTypeSearch.classList.remove('text-gray-400', 'dark:text-gray-500');
        } else {
            marketTypeSearch.value = '';
        }
    }
    
    updateMarketTypeInputDisplay();
    
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
            // Also clear market type when sport is cleared
            marketTypeSelect.value = '';
            updateMarketTypeInputDisplay();
            marketTypeSearch.value = '';
            marketTypeDropdown.classList.add('hidden');
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
                // Also clear market type when tournament is cleared
                marketTypeSelect.value = '';
                updateMarketTypeInputDisplay();
                marketTypeSearch.value = '';
                marketTypeDropdown.classList.add('hidden');
            }
        }
    }
    
    // Filter market types based on selected tournament
    function filterMarketTypesByTournament(tournamentName, preserveSelection = false) {
        const allMarketTypes = Array.from(marketTypeSelect.options);
        
        if (!tournamentName) {
            allMarketTypes.forEach(option => {
                option.style.display = '';
            });
            if (!preserveSelection && !isFirstLoad) {
                marketTypeSelect.value = '';
                updateMarketTypeInputDisplay();
            }
            return;
        }
        
        allMarketTypes.forEach(option => {
            const optionTournament = option.getAttribute('data-tournament');
            if (optionTournament === tournamentName || option.value === '') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        if (!preserveSelection && !isFirstLoad) {
            const selectedType = marketTypeSelect.options[marketTypeSelect.selectedIndex];
            const selectedTypeTournament = selectedType ? selectedType.getAttribute('data-tournament') : null;
            
            if (!selectedTypeTournament || selectedTypeTournament !== tournamentName) {
                marketTypeSelect.value = '';
                updateMarketTypeInputDisplay();
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
                
                // Filter market types based on selected tournament
                isFirstLoad = true;
                filterMarketTypesByTournament(value, false);
                isFirstLoad = false;
                
                // Clear market type selection if it doesn't belong to this tournament
                marketTypeSelect.value = '';
                updateMarketTypeInputDisplay();
                marketTypeSearch.value = '';
                marketTypeDropdown.classList.add('hidden');
            });
        });
    }
    
    // Market Type dropdown functionality
    function showMarketTypeDropdown() {
        const searchTerm = marketTypeSearch.value.trim().toLowerCase();
        const selectedTournament = tournamentSelect.value;
        
        let filteredOptions = Array.from(marketTypeSelect.options).filter(option => {
            if (selectedTournament) {
                const optionTournament = option.getAttribute('data-tournament');
                if (optionTournament !== selectedTournament && option.value !== '') {
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
            marketTypeDropdown.innerHTML = `
                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No market types found
                </div>
            `;
            marketTypeDropdown.classList.remove('hidden');
            return;
        }
        
        let dropdownHTML = '';
        filteredOptions.forEach(option => {
            const optionValue = option.value;
            const optionName = option.text;
            const isSelected = marketTypeSelect.value === optionValue;
            dropdownHTML += `
                <div class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer ${isSelected ? 'bg-blue-100 dark:bg-blue-900/20 font-medium' : ''}" data-value="${optionValue}" data-name="${optionName}">
                    ${optionName}
                </div>
            `;
        });
        
        marketTypeDropdown.innerHTML = dropdownHTML;
        marketTypeDropdown.classList.remove('hidden');
        
        marketTypeDropdown.querySelectorAll('div[data-value]').forEach(item => {
            item.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const name = this.getAttribute('data-name');
                
                marketTypeSelect.value = value;
                marketTypeSearch.value = name;
                marketTypeDropdown.classList.add('hidden');
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
    
    marketTypeSearch.addEventListener('focus', function() {
        showMarketTypeDropdown();
    });
    
    marketTypeSearch.addEventListener('input', function() {
        showMarketTypeDropdown();
    });
    
    // Hide dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!tournamentSearch.contains(event.target) && !tournamentDropdown.contains(event.target)) {
            tournamentDropdown.classList.add('hidden');
        }
        if (!marketTypeSearch.contains(event.target) && !marketTypeDropdown.contains(event.target)) {
            marketTypeDropdown.classList.add('hidden');
        }
    });
    
    // Apply initial filters
    const initialSport = sportSelect.value;
    const initialTournament = tournamentSelect.value;
    
    if (initialSport) {
        filterTournamentsBySport(initialSport, true);
    }
    
    if (initialTournament) {
        filterMarketTypesByTournament(initialTournament, true);
    }
});
</script>

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
</style>
@endpush
