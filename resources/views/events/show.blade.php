@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Event Details</h1>
                <p class="text-gray-600 dark:text-gray-400">View detailed information about this event</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Events
                </a>
            </div>
        </div>
    </div>

    <!-- Event Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Event Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $event->eventName }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Event ID: {{ $event->eventId }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    @if($event->IsSettle)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Settled</span>
                    @elseif($event->IsVoid)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">Void</span>
                    @elseif($event->IsUnsettle)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">Unsettled</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Event Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Basic Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $event->eventName }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event ID</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $event->eventId }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exch Event ID:</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $event->exEventId }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">MongoDB ID</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $event->_id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tournament & Sport Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tournament & Sport</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tournament</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $event->tournamentsName }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tournament ID</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $event->tournamentsId }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sport</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                                {{ $sportConfig[$event->sportId] ?? 'Unknown Sport' }} (ID: {{ $event->sportId }})
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status & Flags -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status & Flags</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <div class="mt-1">
                                @php
                                    $eventStatus = isset($event->status) ? (int) $event->status : null;
                                    $statusInfo = $eventStatus && isset($statusBadgeMeta[$eventStatus])
                                        ? $statusBadgeMeta[$eventStatus]
                                        : null;
                                @endphp
                                @if($statusInfo)
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        Unknown
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Flags</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @if($event->highlight)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">Highlight</span>
                                @endif
                                @if($event->popular)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">Popular</span>
                                @endif
                                @if($event->quicklink)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300">Quicklink</span>
                                @endif
                                @if($event->dataSwitch)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300">Data Switch</span>
                                @endif
                                @if(!$event->highlight && !$event->popular && !$event->quicklink && !$event->dataSwitch)
                                    <span class="text-sm text-gray-500 dark:text-gray-400">No flags set</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scorecard Labels -->
            @if(isset($labelConfig) && !empty($labelConfig))
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Scorecard Labels</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($labelConfig as $labelKey => $labelName)
                        @php
                            $labelChecked = isset($event->parsedLabels[$labelKey]) && (bool)$event->parsedLabels[$labelKey] === true;
                            $labelTimestamp = isset($event->parsedLabelTimestamps[$labelKey]) ? $event->parsedLabelTimestamps[$labelKey] : null;
                            $formattedTimestamp = $labelTimestamp ? \Carbon\Carbon::parse($labelTimestamp)->format('M d, Y h:i A') : null;
                            $labelLog = isset($labelLogs[$labelKey]) ? $labelLogs[$labelKey] : null;
                            // Show log info for all checked labels
                            $showLogInfo = $labelChecked && $labelLog;
                        @endphp
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center {{ $labelChecked ? 'bg-primary-600 border-primary-600' : 'border-gray-300 dark:border-gray-500' }}">
                                    @if($labelChecked)
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $labelName }}</span>
                            </div>
                            @if($labelChecked && $formattedTimestamp)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $formattedTimestamp }}</div>
                            @else
                                <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">Not checked</div>
                            @endif
                            @if($showLogInfo)
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 space-y-1">
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ $labelLog['name'] }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $labelLog['email'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($labelLog['time'])->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- SC Type Information -->
            @if(!empty($event->sc_type) || !empty($event->new_limit))
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">SC Type Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($event->sc_type))
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">SC Type</label>
                                <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300">
                                    {{ $event->sc_type }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($event->new_limit))
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">New Limit</label>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($event->new_limit) }}</p>
                            </div>
                        </div>
                        @if(!empty($newLimitLogs) && count($newLimitLogs) > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Change History</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($newLimitLogs as $log)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-medium">{{ $log['name'] }}</span>
                                                <span class="text-gray-500 dark:text-gray-500">{{ \Carbon\Carbon::parse($log['time'])->format('M d, Y h:i A') }}</span>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-500 text-xs">
                                                {{ $log['email'] }}
                                            </div>
                                            @if($log['old_value'] !== null)
                                                <div class="mt-1 text-xs">
                                                    <span class="text-gray-500 dark:text-gray-500">Changed from </span>
                                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ number_format($log['old_value']) }}</span>
                                                    <span class="text-gray-500 dark:text-gray-500"> to </span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($log['new_value']) }}</span>
                                                </div>
                                            @else
                                                <div class="mt-1 text-xs">
                                                    <span class="text-gray-500 dark:text-gray-500">Set to </span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($log['new_value']) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Timestamps</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($event->createdAt)->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database Created</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($event->updated_at)->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
