@extends('layouts.app')

@section('title', 'Manual Update Market')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manual Update</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Update market status and winner data.</p>
        </div>
        <a href="{{ route('manual-update.index', ['exMarketId' => $market->exMarketId]) }}"
           class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
            Back to Search
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <div class="font-semibold">Action needed</div>
            <ul class="mt-2 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusValue = is_null($market->status) ? null : (int) $market->status;
        $statusLabel = $statusValue && isset($statusLabels[$statusValue]) ? $statusLabels[$statusValue] : 'UNKNOWN';
    @endphp

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Ex Market ID</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $market->exMarketId }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Ex Event ID</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $market->exEventId }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Event</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $market->eventName }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Market</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $market->marketName }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Current Status</div>
                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-semibold text-gray-700 dark:text-gray-200">
                    {{ $statusLabel }}
                </span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Winner</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $market->winnerType ?? 'N/A' }}</div>
                <div class="text-xs text-gray-500">{{ $market->selectionName ?? '' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Update Market</h2>
        <form method="POST" action="{{ route('manual-update.update') }}" class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
            @csrf
            <input type="hidden" name="exMarketId" value="{{ $market->exMarketId }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Status</label>
                <select name="status"
                        class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ (int) old('status', $market->status ?? 0) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Winner Type</label>
                <input type="text" name="winnerType" value="{{ old('winnerType', $market->winnerType) }}"
                       class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                       placeholder="settle or void">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Winner Selection Name</label>
                <input type="text" name="selectionName" value="{{ old('selectionName', $market->selectionName) }}"
                       class="mt-2 h-11 w-full rounded-md border border-gray-300 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                       placeholder="Winner name">
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 md:col-span-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Submitting will also clear this market from Redis cache.</p>
                <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-md bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    Update Market
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
