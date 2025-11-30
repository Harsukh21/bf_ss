@extends('layouts.app')

@section('title', 'Database System Logs')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .filter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 1030;
    }

    .filter-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .filter-drawer {
        position: fixed;
        top: 0;
        right: -600px;
        width: 560px;
        max-width: 92vw;
        height: 100vh;
        background: #ffffff;
        color: #0f172a;
        box-shadow: -12px 0 30px rgba(15, 23, 42, 0.25);
        border-left: 1px solid rgba(226, 232, 240, 0.8);
        z-index: 1040;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .dark .filter-drawer {
        background: #1f2937;
        border-color: #374151;
        color: #f3f4f6;
    }

    .filter-drawer.open {
        right: 0;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Database System Logs</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">View and filter all system activity logs</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('system-logs.index') }}" class="bg-gray-600 dark:bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-800 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    File Logs
                </a>
                @if(auth()->user()->hasPermission('delete-database-logs'))
                <button onclick="deleteOldLogs()" class="bg-red-600 dark:bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-700 dark:hover:bg-red-800 transition-colors flex items-center" title="Delete logs older than 15 days">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Old Logs
                </button>
                @endif
                <button onclick="toggleFilterDrawer()" class="bg-primary-600 dark:bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-700 dark:hover:bg-primary-800 transition-colors flex items-center relative">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                    @if(request()->hasAny(['action', 'label_name', 'user_id', 'exEventId', 'date_from', 'date_to', 'search']))
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-xs text-white">!</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['action', 'label_name', 'user_id', 'exEventId', 'date_from', 'date_to', 'search']))
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-blue-900 dark:text-blue-100">Active Filters:</span>
                @if(request('action'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Action: {{ request('action') }}
                        <button onclick="removeFilter('action')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('label_name'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Label: {{ request('label_name') }}
                        <button onclick="removeFilter('label_name')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('user_id'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        User: {{ $users->firstWhere('id', request('user_id'))->name ?? 'Unknown' }}
                        <button onclick="removeFilter('user_id')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('exEventId'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Event ID: {{ request('exEventId') }}
                        <button onclick="removeFilter('exEventId')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        From: {{ request('date_from') }}
                        <button onclick="removeFilter('date_from')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        To: {{ request('date_to') }}
                        <button onclick="removeFilter('date_to')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('search'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Search: {{ request('search') }}
                        <button onclick="removeFilter('search')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
            </div>
            <a href="{{ route('system-logs.database') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                Clear All
            </a>
        </div>
    </div>
    @endif

    <!-- Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Activity Logs</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total: {{ $logs->total() }} logs</p>
        </div>
        
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Change</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->user_name)
                                        <div onclick="showLogDetails({{ $log->id }})" class="text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors">{{ $log->user_name }}</div>
                                        <div onclick="showLogDetails({{ $log->id }})" class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors">{{ $log->user_email }}</div>
                                    @else
                                        <span onclick="showLogDetails({{ $log->id }})" class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->event_name)
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ strlen($log->event_name) > 30 ? substr($log->event_name, 0, 30) . '...' : $log->event_name }}</div>
                                        @if($log->exEventId)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ strlen($log->exEventId) > 20 ? substr($log->exEventId, 0, 20) . '...' : $log->exEventId }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->label_name)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300">
                                            {{ $log->label_name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->old_value && $log->new_value)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs px-2 py-1 rounded bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">{{ $log->old_value }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                            <span class="text-xs px-2 py-1 rounded bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">{{ $log->new_value }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100 max-w-md">
                                        {{ strlen($log->description) > 100 ? substr($log->description, 0, 100) . '...' : $log->description }}
                                    </div>
                                    @if($log->ip_address)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            IP: {{ $log->ip_address }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No logs found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No system logs match your current filters.</p>
            </div>
        @endif
    </div>
</div>

<!-- Filter Drawer -->
<div id="filterOverlay" class="filter-overlay" onclick="toggleFilterDrawer()"></div>
<div id="filterDrawer" class="filter-drawer">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filters</h2>
            <button onclick="toggleFilterDrawer()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="GET" action="{{ route('system-logs.database') }}">
            <!-- Search -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" 
                       placeholder="Search in description, event name, user...">
            </div>
            
            <!-- Action -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Label Name -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Label</label>
                <select name="label_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Labels</option>
                    @foreach($labelNames as $labelName)
                        <option value="{{ $labelName }}" {{ request('label_name') == $labelName ? 'selected' : '' }}>{{ $labelName }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- User -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Event ID -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event ID</label>
                <input type="text" name="exEventId" value="{{ request('exEventId') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" 
                       placeholder="External Event ID">
            </div>
            
            <!-- Date From -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <!-- Date To -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="toggleFilterDrawer()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Log Details</h3>
                <button onclick="closeLogDetailsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="logDetailsContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h3>
                </div>
                <button onclick="closeDeleteConfirmModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">Are you sure you want to delete all logs older than 15 days? This action cannot be undone.</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="closeDeleteConfirmModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDeleteOldLogs()" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilterDrawer() {
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');
    
    drawer.classList.toggle('open');
    overlay.classList.toggle('active');
}

function removeFilter(param) {
    const url = new URL(window.location.href);
    url.searchParams.delete(param);
    window.location.href = url.pathname + (url.searchParams.toString() ? `?${url.searchParams.toString()}` : '');
}

// Close drawer on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const drawer = document.getElementById('filterDrawer');
        const overlay = document.getElementById('filterOverlay');
        const logModal = document.getElementById('logDetailsModal');
        const deleteModal = document.getElementById('deleteConfirmModal');
        
        if (drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('active');
        }
        
        if (logModal && !logModal.classList.contains('hidden')) {
            closeLogDetailsModal();
        }
        
        if (deleteModal && !deleteModal.classList.contains('hidden')) {
            closeDeleteConfirmModal();
        }
    }
});

function deleteOldLogs() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.remove('hidden');
    
    // Animate in
    setTimeout(() => {
        const modalContent = modal.querySelector('.bg-white, .bg-gray-800');
        if (modalContent) {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1)';
        }
    }, 10);
}

function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    const modalContent = modal.querySelector('.bg-white, .bg-gray-800');
    
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
    }
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

async function confirmDeleteOldLogs() {
    closeDeleteConfirmModal();
    
    try {
        const response = await fetch('{{ route("system-logs.database.delete-old") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (typeof ToastNotification !== 'undefined' && typeof ToastNotification.show === 'function') {
                ToastNotification.show(data.message, 'success', 3000);
            } else {
                alert(data.message);
            }
            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            if (typeof ToastNotification !== 'undefined' && typeof ToastNotification.show === 'function') {
                ToastNotification.show(data.message || 'Failed to delete old logs.', 'error', 4000);
            } else {
                alert(data.message || 'Failed to delete old logs.');
            }
        }
    } catch (error) {
        console.error('Error deleting old logs:', error);
        if (typeof ToastNotification !== 'undefined' && typeof ToastNotification.show === 'function') {
            ToastNotification.show('An error occurred while deleting old logs.', 'error', 4000);
        } else {
            alert('An error occurred while deleting old logs.');
        }
    }
}

async function showLogDetails(logId) {
    const modal = document.getElementById('logDetailsModal');
    const content = document.getElementById('logDetailsContent');
    
    // Show loading state
    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div><p class="mt-4 text-gray-600 dark:text-gray-400">Loading...</p></div>';
    modal.classList.remove('hidden');
    
    // Animate in
    setTimeout(() => {
        const modalContent = modal.querySelector('.bg-white, .bg-gray-800');
        if (modalContent) {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1)';
        }
    }, 10);
    
    try {
        const response = await fetch(`{{ url('system-logs/database') }}/${logId}/details`, {
            headers: {
                'Accept': 'application/json',
            },
        });
        
        const data = await response.json();
        
        if (data.success && data.log) {
            const log = data.log;
            const createdAt = new Date(log.created_at).toLocaleString();
            
            let labelChangesHtml = '';
            if (log.label_changes) {
                try {
                    const changes = JSON.parse(log.label_changes);
                    if (Array.isArray(changes) && changes.length > 0) {
                        labelChangesHtml = '<div class="mt-2"><strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Label Changes:</strong><ul class="list-disc list-inside mt-1 space-y-1">';
                        changes.forEach(change => {
                            labelChangesHtml += `<li class="text-sm text-gray-600 dark:text-gray-400">${escapeHtml(change)}</li>`;
                        });
                        labelChangesHtml += '</ul></div>';
                    }
                } catch (e) {
                    // Invalid JSON, skip
                }
            }
            
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Basic Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date & Time</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">${escapeHtml(createdAt)}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Action</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">${escapeHtml(log.action || 'N/A')}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">User Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">User Name</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">${escapeHtml(log.user_name || 'System')}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">${escapeHtml(log.user_email || 'N/A')}</p>
                            </div>
                        </div>
                    </div>
                    
                    ${log.event_name || log.exEventId ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Event Information</h4>
                        <div class="grid grid-cols-1 gap-4">
                            ${log.event_name ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Event Name</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">${escapeHtml(log.event_name)}</p>
                            </div>
                            ` : ''}
                            ${log.exEventId ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Event ID</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 font-mono">${escapeHtml(log.exEventId)}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${log.label_name || log.old_value || log.new_value ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Label Information</h4>
                        <div class="grid grid-cols-1 gap-4">
                            ${log.label_name ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Label Name</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300">${escapeHtml(log.label_name)}</span>
                                </p>
                            </div>
                            ` : ''}
                            ${log.old_value && log.new_value ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Change</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-1 rounded bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">${escapeHtml(log.old_value)}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <span class="text-xs px-2 py-1 rounded bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">${escapeHtml(log.new_value)}</span>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        ${labelChangesHtml}
                    </div>
                    ` : ''}
                    
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Description</h4>
                        <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">${escapeHtml(log.description || 'N/A')}</p>
                    </div>
                    
                    ${log.ip_address || log.user_agent ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Technical Details</h4>
                        <div class="grid grid-cols-1 gap-4">
                            ${log.ip_address ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">IP Address</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 font-mono">${escapeHtml(log.ip_address)}</p>
                            </div>
                            ` : ''}
                            ${log.user_agent ? `
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">User Agent</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 break-all">${escapeHtml(log.user_agent)}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        } else {
            content.innerHTML = '<div class="text-center py-8"><p class="text-red-600 dark:text-red-400">Failed to load log details.</p></div>';
        }
    } catch (error) {
        console.error('Error loading log details:', error);
        content.innerHTML = '<div class="text-center py-8"><p class="text-red-600 dark:text-red-400">An error occurred while loading log details.</p></div>';
    }
}

function closeLogDetailsModal() {
    const modal = document.getElementById('logDetailsModal');
    const modalContent = modal.querySelector('.bg-white, .bg-gray-800');
    
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
    }
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal on overlay click
document.getElementById('logDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogDetailsModal();
    }
});

document.getElementById('deleteConfirmModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteConfirmModal();
    }
});
</script>
@endpush

