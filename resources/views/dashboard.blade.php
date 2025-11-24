@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Welcome Message -->
        @if(session('success'))
            <div class="alert mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-md transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.style.display='none'" class="text-green-400 hover:text-green-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Dashboard</h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">Welcome back, {{ Auth::user()->name }}! Here's your event and market overview.</p>
        </div>

        <!-- User Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6 mb-6">
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xl md:text-2xl font-bold flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @php
                            $user = Auth::user();
                            $userRoles = $user->roles;
                        @endphp
                        @if($userRoles && $userRoles->count() > 0)
                            @foreach($userRoles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                No Role Assigned
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Total Events</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($eventStats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-sky-100 dark:bg-sky-900/20 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 004-4V9a4 4 0 00-4-4h-3l-1-2H8L7 5H4a1 1 0 00-1 1v9z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Events</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($eventStats['upcoming']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">In-Play Events</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($eventStats['in_play']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4 min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Total Markets</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($marketStats['total']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Live: {{ number_format($marketStats['live']) }} · Completed: {{ number_format($marketStats['completed']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events & Quick Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6">
            <!-- Recent Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Events</h3>
                </div>
                <div class="p-4 md:p-6">
                    @if($recentEvents->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentEvents as $event)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $event->eventName }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->tournamentsName }}</p>
                                            @if($event->display_time)
                                                <p class="text-xs text-gray-400 dark:text-gray-500">Starts {{ $event->display_time }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $event->status_class }}">{{ $event->status_label }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('events.index') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">View All Events →</a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No events yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first event.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Event Status Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md h-full flex flex-col">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-gray-100">Event Status Overview</h3>
                </div>
                <div class="p-4 md:p-6 flex-1">
                    <div class="space-y-4">
                        @php $totalEvents = max(1, $eventStats['total']); @endphp
                        @foreach($statusBreakdown as $status)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 {{ $status['color'] }} rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $status['label'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($status['count']) }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ $eventStats['total'] > 0 ? number_format(($status['count'] / $eventStats['total']) * 100, 1) : '0.0' }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Highlighted</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($flagCounts['highlight']) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Popular</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($flagCounts['popular']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 md:mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <a href="{{ route('events.index') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-gray-100">View Events</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage all events</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Market List</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View all markets</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-gray-100">Analytics</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View reports</p>
                    </div>
                </a>
            </div>
        </div>
</div>
@endsection