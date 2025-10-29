@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">General Settings</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage application settings and maintenance</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-md mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-md mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Maintenance Actions -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Maintenance</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Clear cache and optimize application</p>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Clear Cache -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Clear Cache</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Clear application cache, configuration cache, and view cache. This will improve performance after updates.
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('general-settings.clear-cache') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors">
                                Clear Cache
                            </button>
                        </form>
                    </div>

                    <!-- Optimize Application -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Optimize Application</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Cache configuration and routes for better performance. Run this after deployment to production.
                                </p>
                            </div>
                        </div>
                        <form id="optimizeForm" action="{{ route('general-settings.optimize') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" id="optimizeBtn" class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="optimizeBtnText">Optimize Now</span>
                                <span id="optimizeBtnLoader" class="hidden inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Optimizing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">System Information</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current application details</p>
                </div>
                <div class="p-6 space-y-4" id="systemInfo">
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">PHP Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ phpversion() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Laravel Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ app()->version() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Environment</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(app()->environment()) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Debug Mode</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Timezone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ config('app.timezone') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cache Driver</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(config('cache.default')) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Stats -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Stats</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Event::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Events</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\User::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Users</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\MarketList::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Markets</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Event::where('IsSettle', 1)->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Settled Events</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const optimizeForm = document.getElementById('optimizeForm');
    const optimizeBtn = document.getElementById('optimizeBtn');
    const optimizeBtnText = document.getElementById('optimizeBtnText');
    const optimizeBtnLoader = document.getElementById('optimizeBtnLoader');
    
    if (optimizeForm && optimizeBtn) {
        optimizeForm.addEventListener('submit', function(e) {
            // Show loading state
            optimizeBtn.disabled = true;
            optimizeBtnText.classList.add('hidden');
            optimizeBtnLoader.classList.remove('hidden');
        });
    }
});
</script>
@endpush

@endsection

