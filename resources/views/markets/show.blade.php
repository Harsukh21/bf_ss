@extends('layouts.app')

@section('title', 'Market Details')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('markets.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                Markets
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">
                                    Market Details
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $market->marketName }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Market ID: {{ $market->_id }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a 
                    href="{{ route('markets.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Markets
                </a>
            </div>
        </div>
    </div>

    <!-- Market Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Market Details Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Market Information
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $market->marketName }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market Type</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $market->type }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $market->eventName }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $market->exEventId }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $market->exMarketId }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($market->marketTime)->format('M j, Y g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Sport & Tournament Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Sport & Tournament
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sport</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $market->sportName }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tournament</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $market->tournamentsName }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Market Status
                    </h3>
                    <div class="space-y-3">
                        @if($market->isLive)
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2 animate-pulse"></span>
                                    Live Market
                                </span>
                            </div>
                        @endif
                        @if($market->isPreBet)
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Pre-bet Market
                                </span>
                            </div>
                        @endif
                        @if(!$market->isLive && !$market->isPreBet)
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Scheduled Market
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Timestamps
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($market->created_at)->format('M j, Y g:i A') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($market->updated_at)->format('M j, Y g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Scorecard Labels -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Scorecard Labels
                    </h3>
                    @php
                        $decodedLabels = json_decode($market->labels ?? '{}', true);
                        $labelKeys = ['4x', 'b2c', 'b2b', 'usdt'];
                        $labelNames = [
                            '4x' => '4X',
                            'b2c' => 'B2C',
                            'b2b' => 'B2B',
                            'usdt' => 'USDT'
                        ];
                        
                        $isLabelChecked = function($value) {
                            if (is_bool($value)) {
                                return $value === true;
                            }
                            if (is_array($value) && isset($value['checked'])) {
                                return (bool) $value['checked'];
                            }
                            return false;
                        };
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($labelKeys as $key)
                            @php
                                $value = $decodedLabels[$key] ?? null;
                                $isChecked = $isLabelChecked($value);
                            @endphp
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if($isChecked)
                                            <div class="w-5 h-5 bg-blue-600 rounded border-2 border-blue-600 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-5 h-5 bg-gray-200 dark:bg-gray-600 rounded border-2 border-gray-300 dark:border-gray-500"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm mb-2">{{ $labelNames[$key] }}</div>
                                        @if($isChecked)
                                            @php
                                                $checkerName = is_array($value) && isset($value['checker_name']) && !empty($value['checker_name']) ? $value['checker_name'] : null;
                                                $checkedAt = is_array($value) && isset($value['checked_at']) && !empty($value['checked_at']) ? $value['checked_at'] : null;
                                                $checkedBy = is_array($value) && isset($value['checked_by']) && !empty($value['checked_by']) ? $value['checked_by'] : null;
                                                
                                                $formattedTime = '';
                                                if ($checkedAt) {
                                                    try {
                                                        $formattedTime = \Carbon\Carbon::parse($checkedAt)->format('M j, Y, g:i A');
                                                    } catch (\Exception $e) {
                                                        $formattedTime = $checkedAt;
                                                    }
                                                }
                                                
                                                $userEmail = null;
                                                if ($checkedBy) {
                                                    $user = \App\Models\User::find($checkedBy);
                                                    $userEmail = $user ? $user->email : null;
                                                }
                                            @endphp
                                            @if($formattedTime)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $formattedTime }}</div>
                                            @endif
                                            @if($checkerName)
                                                <div class="text-sm text-gray-900 dark:text-white font-medium mb-1">{{ $checkerName }}</div>
                                            @endif
                                            @if($userEmail)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $userEmail }}</div>
                                            @endif
                                            @if($formattedTime)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $formattedTime }}</div>
                                            @endif
                                        @else
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Not checked</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <button 
                            type="button"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Edit Market
                        </button>
                        <button 
                            type="button"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share Market
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
