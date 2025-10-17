@extends('layouts.app')

@section('title', 'Market Rates List')

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
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .dark .status-active {
        background-color: #064e3b;
        color: #6ee7b7;
    }
    
    .dark .status-inactive {
        background-color: #7f1d1d;
        color: #fca5a5;
    }

    .change-positive {
        color: #10b981;
    }

    .change-negative {
        color: #ef4444;
    }

    .dark .change-positive {
        color: #34d399;
    }

    .dark .change-negative {
        color: #f87171;
    }
</style>
@endpush

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Market Rates List</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and view all market rates</p>
                </div>
                <div class="flex space-x-3">
                    @php
                        $filterCount = 0;
                        if(request('search')) $filterCount++;
                        if(request('market_name')) $filterCount++;
                        if(request('currency_pair')) $filterCount++;
                        if(request('status')) $filterCount++;
                        if(request('date_from')) $filterCount++;
                        if(request('date_to')) $filterCount++;
                    @endphp
                    
                    @if($filterCount > 0)
                        <a href="{{ route('market-rates.index') }}" class="bg-red-500 dark:bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </a>
                    @endif
                    
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
                    <button onclick="clearAllFilters()" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                        Clear All
                    </button>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if(request('search'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Search: {{ request('search') }}
                            <button onclick="removeFilter('search')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('market_name'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Market: {{ request('market_name') }}
                            <button onclick="removeFilter('market_name')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('currency_pair'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Currency: {{ request('currency_pair') }}
                            <button onclick="removeFilter('currency_pair')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            Status: {{ ucfirst(request('status')) }}
                            <button onclick="removeFilter('status')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('date_from'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            From: {{ request('date_from') }}
                            <button onclick="removeFilter('date_from')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                            To: {{ request('date_to') }}
                            <button onclick="removeFilter('date_to')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Market Rates Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($marketRates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Market</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Market ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Runners</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($marketRates as $rate)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('market-rates.show', $rate) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            @php
                                                $runners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                                                $runnerCount = is_array($runners) ? count($runners) : 0;
                                            @endphp
                                            {{ $runnerCount }} runners
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $marketRates->links() }}
                </div>
            @else
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No market rates found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        No market rates are currently available.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Drawer -->
<div id="filter-overlay" class="filter-overlay" onclick="toggleFilterDrawer()"></div>
<div id="filter-drawer" class="filter-drawer">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Market Rates</h2>
            <button onclick="toggleFilterDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="GET" action="{{ route('market-rates.index') }}" class="space-y-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search market rates..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <!-- Market Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Name</label>
                <input type="text" name="market_name" value="{{ request('market_name') }}" placeholder="Market name..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <!-- Currency Pair -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency Pair</label>
                <input type="text" name="currency_pair" value="{{ request('currency_pair') }}" placeholder="e.g., BTC/USD" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('market-rates.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-center">
                    Clear All
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
        {{ session('error') }}
    </div>
@endif

@push('js')
<script>
    function toggleFilterDrawer() {
        const drawer = document.getElementById('filter-drawer');
        const overlay = document.getElementById('filter-overlay');
        
        if (drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('active');
        } else {
            drawer.classList.add('open');
            overlay.classList.add('active');
        }
    }
    
    function clearAllFilters() {
        window.location.href = "{{ route('market-rates.index') }}";
    }
    
    function removeFilter(filterName) {
        const url = new URL(window.location);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }

    
    // Auto-hide success/error messages
    setTimeout(function() {
        const messages = document.querySelectorAll('.fixed.top-4.right-4');
        messages.forEach(message => {
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 300);
        });
    }, 5000);
</script>
@endpush
@endsection
