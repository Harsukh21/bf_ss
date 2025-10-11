@extends('layouts.app')

@section('content')
<div class="p-6">
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">External Event ID</label>
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
                                @if($event->IsSettle)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">Settled</span>
                                @elseif($event->IsVoid)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">Void</span>
                                @elseif($event->IsUnsettle)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">Unsettled</span>
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
@endsection
