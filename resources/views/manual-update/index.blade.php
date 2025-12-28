@extends('layouts.app')

@section('title', 'Manual Market Update')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manual Market Update</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Search markets and update status or winner data.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="font-semibold">Action needed</div>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('manual-update.index') }}" class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ex Market ID</label>
                <input type="text" name="exMarketId" value="{{ $filters['exMarketId'] ?? '' }}"
                       class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ex Event ID</label>
                <input type="text" name="exEventId" value="{{ $filters['exEventId'] ?? '' }}"
                       class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name</label>
                <input type="text" name="eventName" value="{{ $filters['eventName'] ?? '' }}"
                       class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div class="flex flex-wrap items-end gap-3 md:col-span-2 md:justify-end">
                <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-md bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    Search
                </button>
                <a href="{{ route('manual-update.index') }}"
                   class="inline-flex h-11 items-center justify-center rounded-md border border-gray-300 px-6 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg">
        @if(!$hasFilters)
            <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                Enter search criteria to list markets.
            </div>
        @elseif($markets && $markets->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ex Market ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ex Event ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Market</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Winner</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($markets as $market)
                            @php
                                $statusValue = is_null($market->status) ? null : (int) $market->status;
                                $statusLabel = $statusValue && isset($statusLabels[$statusValue])
                                    ? $statusLabels[$statusValue]
                                    : 'UNKNOWN';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $market->exMarketId }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $market->exEventId }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $market->eventName }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $market->marketName }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-semibold text-gray-700 dark:text-gray-200">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    <div>{{ $market->winnerType ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $market->selectionName ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('manual-update.view', ['exMarketId' => $market->exMarketId]) }}"
                                       class="inline-flex items-center rounded-md bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 hover:bg-primary-100">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                {{ $markets->links() }}
            </div>
        @else
            <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                No markets found for the current search.
            </div>
        @endif
    </div>
</div>
@endsection
