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

        <!-- Timestamp -->
        <div class="mb-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Created: {{ $marketRate->created_at ? \Carbon\Carbon::parse($marketRate->created_at)->format('M d, Y H:i:s') : 'N/A' }}
            </span>
        </div>

        <!-- Betfair Style Runners Table -->
        @php
            $runners = is_string($marketRate->runners) ? json_decode($marketRate->runners, true) : $marketRate->runners;
        @endphp
        
        @if(is_array($runners) && count($runners) > 0)
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
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
                                            <td class="px-2 py-4 text-center border-l border-r border-gray-200 dark:border-gray-600 bg-blue-50 dark:bg-blue-900/20">
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
                                            <td class="px-2 py-4 text-center border-l border-r border-gray-200 dark:border-gray-600 bg-pink-50 dark:bg-pink-900/20">
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

        <!-- Raw Data (for debugging) -->
        @if(config('app.debug'))
            <div class="mt-6 bg-gray-50 dark:bg-gray-900 shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Raw Data (Debug)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Raw market rate data for debugging purposes</p>
                </div>
                <div class="px-6 py-4">
                    <pre class="text-xs text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 p-4 rounded overflow-x-auto">{{ json_encode($marketRate->toArray(), JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
