@extends('layouts.app')

@section('title', 'Market Rate Details')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Market Rate Details
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Detailed view of market rate information
                    </p>
                </div>
                <div class="flex space-x-3">
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

        <!-- Market Rate Details -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $marketRate->marketName }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Event: {{ $eventInfo->eventName ?? 'Unknown Event' }}
                </p>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $marketRate->exMarketId }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $marketRate->marketName }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
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
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedEventId }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Runners Details -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Runners</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Market runners and their details</p>
            </div>
            <div class="px-6 py-4">
                @php
                    $runners = is_string($marketRate->runners) ? json_decode($marketRate->runners, true) : $marketRate->runners;
                @endphp
                
                @if(is_array($runners) && count($runners) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Runner Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available To Back</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available To Lay</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Summary</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($runners as $runner)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $runner['runnerName'] ?? 'Unknown Runner' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($runner['exchange']['availableToBack']) && is_array($runner['exchange']['availableToBack']))
                                                <div class="text-xs space-y-1">
                                                    @foreach($runner['exchange']['availableToBack'] as $back)
                                                        <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">
                                                            <span class="text-green-700 dark:text-green-300">£{{ number_format($back['price'], 2) }}</span>
                                                            <span class="text-green-600 dark:text-green-400">{{ number_format($back['size'], 2) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">No back data</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($runner['exchange']['availableToLay']) && is_array($runner['exchange']['availableToLay']))
                                                <div class="text-xs space-y-1">
                                                    @foreach($runner['exchange']['availableToLay'] as $lay)
                                                        <div class="flex justify-between items-center bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded">
                                                            <span class="text-red-700 dark:text-red-300">£{{ number_format($lay['price'], 2) }}</span>
                                                            <span class="text-red-600 dark:text-red-400">{{ number_format($lay['size'], 2) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">No lay data</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                @php
                                                    $backCount = isset($runner['exchange']['availableToBack']) ? count($runner['exchange']['availableToBack']) : 0;
                                                    $layCount = isset($runner['exchange']['availableToLay']) ? count($runner['exchange']['availableToLay']) : 0;
                                                    $backBest = $backCount > 0 ? max(array_column($runner['exchange']['availableToBack'], 'price')) : 0;
                                                    $layBest = $layCount > 0 ? min(array_column($runner['exchange']['availableToLay'], 'price')) : 0;
                                                @endphp
                                                <div><strong>{{ $backCount }}</strong> back odds</div>
                                                <div><strong>{{ $layCount }}</strong> lay odds</div>
                                                @if($backBest > 0)
                                                    <div class="text-green-600">Best Back: £{{ number_format($backBest, 2) }}</div>
                                                @endif
                                                @if($layBest > 0)
                                                    <div class="text-red-600">Best Lay: £{{ number_format($layBest, 2) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No runners data</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No runners information available for this market.</p>
                    </div>
                @endif
            </div>
        </div>

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
