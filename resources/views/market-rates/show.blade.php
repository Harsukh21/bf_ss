@extends('layouts.app')

@section('title', 'Market Rate Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        {{ $marketRate->marketName }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Event: {{ $eventInfo->eventName ?? 'Unknown Event' }}
                    </p>
                </div>
                <div class="flex space-x-3 items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Min: 10 | Max: 25K
                    </span>
                    <!-- Grid Dropdown -->
                    <div class="flex items-center space-x-2">
                        <label for="gridSelect" class="text-sm text-gray-700 dark:text-gray-300">Grid:</label>
                        <select id="gridSelect" class="h-9 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Off</option>
                            <option value="10" {{ isset($gridCountValue) && $gridCountValue == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ isset($gridCountValue) && $gridCountValue == 20 ? 'selected' : '' }}>20</option>
                            <option value="40" {{ isset($gridCountValue) && $gridCountValue == 40 ? 'selected' : '' }}>40</option>
                            <option value="60" {{ isset($gridCountValue) && $gridCountValue == 60 ? 'selected' : '' }}>60</option>
                        </select>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    @unless(isset($gridEnabled) && $gridEnabled)
                    <div class="flex space-x-2">
                        @if($previousMarketRate)
                            <a href="{{ route('market-rates.show', $previousMarketRate->id) . '?exEventId=' . urlencode($selectedEventId) }}" 
                               class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </a>
                        @else
                            <span class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-3 py-2 rounded-lg flex items-center cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </span>
                        @endif
                        
                        @if($nextMarketRate)
                            <a href="{{ route('market-rates.show', $nextMarketRate->id) . '?exEventId=' . urlencode($selectedEventId) }}" 
                               class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors flex items-center">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-3 py-2 rounded-lg flex items-center cursor-not-allowed">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                        
                        <!-- Screenshot Options -->
                        <div class="relative">
                            <button id="screenshotBtn" class="bg-green-600 dark:bg-green-700 text-white px-3 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Screenshot
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="screenshotDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                                <button onclick="takeScreenshot('png')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Download PNG
                                </button>
                                <button onclick="takeScreenshot('jpeg')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Download JPEG
                                </button>
                            </div>
                        </div>
                    </div>
                    @endunless
                    
                    <!-- Screenshot button for grid mode -->
                    @if(isset($gridEnabled) && $gridEnabled)
                    <div class="relative">
                        <button id="screenshotBtnGrid" class="bg-green-600 dark:bg-green-700 text-white px-3 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Screenshot
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="screenshotDropdownGrid" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <button onclick="takeScreenshot('png')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Download PNG
                            </button>
                            <button onclick="takeScreenshot('jpeg')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Download JPEG
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('market-rates.index', ['exEventId' => $selectedEventId]) }}" 
                       class="bg-gray-600 dark:bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-800 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid Mode: show records in 2 columns (2 rates per row) -->
        @if(isset($gridEnabled) && $gridEnabled && isset($gridMarketRates) && $gridMarketRates->count())
        <div id="ratesGridContainer" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
            @foreach($gridMarketRates as $index => $rate)
                @php
                    $gridRunners = is_string($rate->runners) ? json_decode($rate->runners, true) : $rate->runners;
                @endphp
                @if(is_array($gridRunners) && count($gridRunners) > 0)
                <div class="rates-grid-item">
                    <div class="mb-1">
                        <span class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $rate->created_at ? \Carbon\Carbon::parse($rate->created_at)->format('M d, Y H:i:s') : 'N/A' }}
                        </span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mt-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Runner</th>
                                        <th class="px-1 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 w-8"></th>
                                        <th class="px-1 py-2 text-center text-xs font-semibold text-blue-700 dark:text-blue-300 border-l border-r border-gray-300 dark:border-gray-600" colspan="3">BACK</th>
                                        <th class="px-1 py-2 text-center text-xs font-semibold text-pink-700 dark:text-pink-300 border-l border-r border-gray-300 dark:border-gray-600" colspan="3">LAY</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($gridRunners as $runner)
                                        @php
                                            $runner = is_array($runner) ? $runner : (array) $runner;
                                            $runnerName = $runner['runnerName'] ?? 'Unknown Runner';
                                            $exchange = is_array($runner['exchange'] ?? null) ? $runner['exchange'] : (array) ($runner['exchange'] ?? []);
                                            $availableToBack = $exchange['availableToBack'] ?? [];
                                            $availableToLay = $exchange['availableToLay'] ?? [];
                                            $availableToBack = is_array($availableToBack) ? $availableToBack : (array) $availableToBack;
                                            $availableToLay = is_array($availableToLay) ? $availableToLay : (array) $availableToLay;
                                            $backSlots = array_reverse(array_slice($availableToBack, 0, 3));
                                            $laySlots = array_reverse(array_slice($availableToLay, 0, 3));
                                            $isSuspended = empty($availableToBack) && empty($availableToLay);
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-3 py-2">
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $runnerName }}</div>
                                            </td>
                                            <td class="px-1 py-2 text-center"><span class="text-xs text-red-600 dark:text-red-400">0.0</span></td>
                                            @if($isSuspended)
                                                <td colspan="6" class="px-1 py-2 text-center border-l border-r border-gray-300 dark:border-gray-600">
                                                    <span class="text-sm font-bold text-red-600 dark:text-red-400">SUSPEND</span>
                                                </td>
                                            @else
                                                @for($i = 0; $i < 3; $i++)
                                                    <td class="px-1 py-2 text-center border-l border-r border-gray-200 dark:border-gray-600" style="background-color: #E3F2FD;">
                                                        @if(isset($backSlots[$i]))
                                                            @php $slot = is_array($backSlots[$i]) ? $backSlots[$i] : (array) $backSlots[$i]; @endphp
                                                            <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ number_format($slot['price'] ?? 0, 2) }}</div>
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($slot['size'] ?? 0, 2) }}</div>
                                                        @else
                                                            <div class="text-xs text-gray-400 dark:text-gray-500">-</div>
                                                            <div class="text-xs text-gray-400 dark:text-gray-500">25K</div>
                                                        @endif
                                                    </td>
                                                @endfor
                                                @for($i = 0; $i < 3; $i++)
                                                    <td class="px-1 py-2 text-center border-l border-r border-gray-200 dark:border-gray-600" style="background-color: #FCE4EC;">
                                                        @if(isset($laySlots[$i]))
                                                            @php $slot = is_array($laySlots[$i]) ? $laySlots[$i] : (array) $laySlots[$i]; @endphp
                                                            <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ number_format($slot['price'] ?? 0, 2) }}</div>
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($slot['size'] ?? 0, 2) }}</div>
                                                        @else
                                                            <div class="text-xs text-gray-400 dark:text-gray-500">-</div>
                                                            <div class="text-xs text-gray-400 dark:text-gray-500">25K</div>
                                                        @endif
                                                    </td>
                                                @endfor
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
            </div>
        </div>
        @else
        <!-- Betfair Style Runners Table -->
        @php
            $runners = is_string($marketRate->runners) ? json_decode($marketRate->runners, true) : $marketRate->runners;
        @endphp
        
        @if(is_array($runners) && count($runners) > 0)
        <!-- Screenshot Container - includes timestamp and table -->
        <div id="ratesTableContainer">
            <!-- Timestamp for Screenshot -->
            <div class="mb-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Created: {{ $marketRate->created_at ? \Carbon\Carbon::parse($marketRate->created_at)->format('M d, Y H:i:s') : 'N/A' }}
                </span>
            </div>
            
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mt-2">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-48">Runner</th>
                                <th class="px-2 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 w-12"></th>
                                <th class="px-2 py-3 text-center text-xs font-semibold text-blue-700 dark:text-blue-300 border-l border-r border-gray-300 dark:border-gray-600" colspan="3">BACK</th>
                                <th class="px-2 py-3 text-center text-xs font-semibold text-pink-700 dark:text-pink-300 border-l border-r border-gray-300 dark:border-gray-600" colspan="3">LAY</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($runners as $runner)
                                @php
                                    // Handle both array and object structures
                                    $runner = is_array($runner) ? $runner : (array) $runner;
                                    $runnerName = $runner['runnerName'] ?? 'Unknown Runner';
                                    
                                    // Get exchange data
                                    $exchange = is_array($runner['exchange'] ?? null) ? $runner['exchange'] : (array) ($runner['exchange'] ?? []);
                                    $availableToBack = $exchange['availableToBack'] ?? [];
                                    $availableToLay = $exchange['availableToLay'] ?? [];
                                    
                                    // Convert to arrays
                                    $availableToBack = is_array($availableToBack) ? $availableToBack : (array) $availableToBack;
                                    $availableToLay = is_array($availableToLay) ? $availableToLay : (array) $availableToLay;
                                    
                                    // Ensure we have 3 slots for back and lay
                                    $backSlots = array_slice($availableToBack, 0, 3);
                                    $laySlots = array_slice($availableToLay, 0, 3);
                                    
                                    // Reverse the arrays to show big to small from right to left
                                    $backSlots = array_reverse($backSlots);
                                    $laySlots = array_reverse($laySlots);
                                    
                                    // Check if suspended
                                    $isSuspended = empty($availableToBack) && empty($availableToLay);
                                @endphp
                                
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <!-- Runner Name -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $runnerName }}
                                        </div>
                                    </td>
                                    
                                    <!-- 0.0 Column (for potential P/L display) -->
                                    <td class="px-2 py-4 text-center">
                                        <span class="text-sm text-red-600 dark:text-red-400">0.0</span>
                                    </td>
                                    
                                    @if($isSuspended)
                                        <!-- Suspended State - spans across all back and lay columns -->
                                        <td colspan="6" class="px-2 py-4 text-center border-l border-r border-gray-300 dark:border-gray-600">
                                            <span class="text-lg font-bold text-red-600 dark:text-red-400">SUSPEND</span>
                                        </td>
                                    @else
                                        <!-- BACK Columns (3 slots) -->
                                        @for($i = 0; $i < 3; $i++)
                                            <td class="px-2 py-4 text-center border-l border-r border-gray-200 dark:border-gray-600" style="background-color: #E3F2FD;">
                                                @if(isset($backSlots[$i]))
                                                    @php
                                                        $slot = is_array($backSlots[$i]) ? $backSlots[$i] : (array) $backSlots[$i];
                                                        $price = $slot['price'] ?? 0;
                                                        $size = $slot['size'] ?? 0;
                                                    @endphp
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ number_format($price, 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                                        {{ number_format($size, 2) }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-400 dark:text-gray-500">-</div>
                                                    <div class="text-xs text-gray-400 dark:text-gray-500">25K</div>
                                                @endif
                                            </td>
                                        @endfor
                                        
                                        <!-- LAY Columns (3 slots) -->
                                        @for($i = 0; $i < 3; $i++)
                                            <td class="px-2 py-4 text-center border-l border-r border-gray-200 dark:border-gray-600" style="background-color: #FCE4EC;">
                                                @if(isset($laySlots[$i]))
                                                    @php
                                                        $slot = is_array($laySlots[$i]) ? $laySlots[$i] : (array) $laySlots[$i];
                                                        $price = $slot['price'] ?? 0;
                                                        $size = $slot['size'] ?? 0;
                                                    @endphp
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ number_format($price, 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                                        {{ number_format($size, 2) }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-400 dark:text-gray-500">-</div>
                                                    <div class="text-xs text-gray-400 dark:text-gray-500">25K</div>
                                                @endif
                                            </td>
                                        @endfor
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End of Screenshot Container -->

            <!-- Market Info Footer -->
            <div class="mt-4 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="flex space-x-4">
                    <div>
                        <span class="font-medium">Market ID:</span> {{ $marketRate->exMarketId }}
                    </div>
                    <div>
                        <span class="font-medium">Status:</span>
                            @if($marketRate->isCompleted)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Completed</span>
                            @elseif($marketRate->inplay)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span>
                                    In Play
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">Upcoming</span>
                            @endif
                    </div>
                </div>
                <div>
                    <span class="font-medium">Event ID:</span> {{ $selectedEventId }}
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No runners data</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No runners information available for this market.</p>
                </div>
            </div>
        @endif
        @endif

        
    </div>
</div>

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// Wait for html2canvas library to load
let html2canvasReady = false;
let html2canvasCheckInterval;

// Check if html2canvas is loaded
function checkHtml2Canvas() {
    if (typeof html2canvas !== 'undefined') {
        html2canvasReady = true;
        clearInterval(html2canvasCheckInterval);
    }
}

// Start checking
html2canvasCheckInterval = setInterval(checkHtml2Canvas, 100);

// Also try fallback CDN after 2 seconds if not loaded
setTimeout(function() {
    if (!html2canvasReady && typeof html2canvas === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/html2canvas@1.4.1/dist/html2canvas.min.js';
        document.head.appendChild(script);
    }
}, 2000);

document.addEventListener('DOMContentLoaded', function() {
    // Screenshot dropdown toggle
    const screenshotBtn = document.getElementById('screenshotBtn');
    const screenshotDropdown = document.getElementById('screenshotDropdown');
    const screenshotBtnGrid = document.getElementById('screenshotBtnGrid');
    const screenshotDropdownGrid = document.getElementById('screenshotDropdownGrid');
    const gridSelect = document.getElementById('gridSelect');
    
    if (screenshotBtn && screenshotDropdown) {
        screenshotBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            screenshotDropdown.classList.toggle('hidden');
        });
    }

    if (screenshotBtnGrid && screenshotDropdownGrid) {
        screenshotBtnGrid.addEventListener('click', function(e) {
            e.stopPropagation();
            screenshotDropdownGrid.classList.toggle('hidden');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (screenshotBtn && screenshotDropdown) {
            if (!screenshotBtn.contains(e.target) && !screenshotDropdown.contains(e.target)) {
                screenshotDropdown.classList.add('hidden');
            }
        }
        if (screenshotBtnGrid && screenshotDropdownGrid) {
            if (!screenshotBtnGrid.contains(e.target) && !screenshotDropdownGrid.contains(e.target)) {
                screenshotDropdownGrid.classList.add('hidden');
            }
        }
    });

    // Grid select behavior: update grid parameter and reload
    if (gridSelect) {
        gridSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            if (this.value) {
                url.searchParams.set('grid', this.value);
            } else {
                url.searchParams.delete('grid');
            }
            window.location.href = url.toString();
        });
    }
});

// Take screenshot function
function takeScreenshot(format) {
    const dropdown = document.getElementById('screenshotDropdown');
    const dropdownGrid = document.getElementById('screenshotDropdownGrid');
    if (dropdown) {
        dropdown.classList.add('hidden');
    }
    if (dropdownGrid) {
        dropdownGrid.classList.add('hidden');
    }
    
    // Check if library is loaded
    if (typeof html2canvas === 'undefined') {
        showNotification('Loading screenshot library. Please wait...', 'info');
        
        // Wait for library to load (max 5 seconds)
        let attempts = 0;
        const maxAttempts = 50;
        
        const checkLibrary = setInterval(function() {
            attempts++;
            if (typeof html2canvas !== 'undefined') {
                clearInterval(checkLibrary);
                takeScreenshot(format);
            } else if (attempts >= maxAttempts) {
                clearInterval(checkLibrary);
                showNotification('Screenshot library failed to load. Please refresh the page.', 'error');
            }
        }, 100);
        
        return;
    }
    
    showNotification('Taking screenshot...', 'info');
    
    // Hide navigation buttons, grid select, and back button for cleaner screenshot
    const navButtons = document.querySelector('.flex.space-x-2');
    const backButton = document.querySelector('a[href*="market-rates.index"]');
    const gridSelectContainer = document.querySelector('select[id="gridSelect"]')?.parentElement;
    const screenshotButtonContainer = document.getElementById('screenshotBtnGrid')?.parentElement;
    
    const originalNavDisplay = navButtons ? navButtons.style.display : '';
    const originalBackDisplay = backButton ? backButton.style.display : '';
    const originalGridDisplay = gridSelectContainer ? gridSelectContainer.style.display : '';
    const originalScreenshotDisplay = screenshotButtonContainer ? screenshotButtonContainer.style.display : '';
    
    if (navButtons) navButtons.style.display = 'none';
    if (backButton) backButton.style.display = 'none';
    if (gridSelectContainer) gridSelectContainer.style.display = 'none';
    if (screenshotButtonContainer) screenshotButtonContainer.style.display = 'none';
    
    // Target the correct container (grid or single)
    const element = document.getElementById('ratesGridContainer') || document.getElementById('ratesTableContainer');
    
    if (!element) {
        if (navButtons) navButtons.style.display = originalNavDisplay;
        if (backButton) backButton.style.display = originalBackDisplay;
        if (gridSelectContainer) gridSelectContainer.style.display = originalGridDisplay;
        if (screenshotButtonContainer) screenshotButtonContainer.style.display = originalScreenshotDisplay;
        showNotification('Error: Could not find rates table to capture', 'error');
        return;
    }
    
    html2canvas(element, {
        backgroundColor: document.body.classList.contains('dark') ? '#111827' : '#ffffff',
        scale: 2,
        useCORS: true,
        allowTaint: true,
        logging: false
    }).then(canvas => {
        if (navButtons) navButtons.style.display = originalNavDisplay;
        if (backButton) backButton.style.display = originalBackDisplay;
        if (gridSelectContainer) gridSelectContainer.style.display = originalGridDisplay;
        if (screenshotButtonContainer) screenshotButtonContainer.style.display = originalScreenshotDisplay;
        
        const link = document.createElement('a');
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const filename = `rates-table-{{ $marketRate->marketName ?? 'unknown' }}-${timestamp}.${format}`;
        
        link.download = filename;
        link.href = canvas.toDataURL(`image/${format}`, 0.9);
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification(`Rates table downloaded successfully!`, 'success');
    }).catch(error => {
        if (navButtons) navButtons.style.display = originalNavDisplay;
        if (backButton) backButton.style.display = originalBackDisplay;
        if (gridSelectContainer) gridSelectContainer.style.display = originalGridDisplay;
        if (screenshotButtonContainer) screenshotButtonContainer.style.display = originalScreenshotDisplay;
        showNotification('Screenshot failed. Please try again.', 'error');
    });
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Make functions globally accessible
window.takeScreenshot = takeScreenshot;
window.showNotification = showNotification;
</script>
@endpush

@endsection
