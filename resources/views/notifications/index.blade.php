@extends('layouts.app')

@section('title', 'Notifications')

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

    /* Actions Dropdown */
    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-dropdown-btn {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .actions-dropdown-btn:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .dark .actions-dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .actions-dropdown-menu {
        position: fixed;
        min-width: 160px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 1070;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s;
        top: auto;
        left: auto;
        right: auto;
        bottom: auto;
    }

    .dark .actions-dropdown-menu {
        background: #1f2937;
        border-color: #374151;
    }

    .actions-dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .actions-dropdown-item {
        display: block;
        width: 100%;
        padding: 0.5rem 1rem;
        text-align: left;
        color: #374151;
        font-size: 0.875rem;
        transition: background 0.15s;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .actions-dropdown-item:hover {
        background: #f3f4f6;
    }

    .dark .actions-dropdown-item {
        color: #e5e7eb;
    }

    .dark .actions-dropdown-item:hover {
        background: #374151;
    }

    .actions-dropdown-item.text-red-600:hover {
        background: #fee2e2;
    }

    .dark .actions-dropdown-item.text-red-600:hover {
        background: rgba(220, 38, 38, 0.2);
    }

    /* View Modal */
    .view-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .view-modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .view-modal {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.95);
        transition: transform 0.3s;
    }

    .dark .view-modal {
        background: #1f2937;
    }

    .view-modal-overlay.show .view-modal {
        transform: scale(1);
    }

    /* Delete Confirmation Modal */
    .delete-confirm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1060;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .delete-confirm-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .delete-confirm-modal {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 90%;
        transform: scale(0.95);
        transition: transform 0.3s;
    }

    .dark .delete-confirm-modal {
        background: #1f2937;
    }

    .delete-confirm-overlay.show .delete-confirm-modal {
        transform: scale(1);
    }
</style>
@endpush

@section('content')
@php
    $filterCount = 0;
    if (request('search')) $filterCount++;
    if (request('notification_type')) $filterCount++;
    if (request('status')) $filterCount++;
    if (request('date_from') || request('date_to')) $filterCount++;
    if (request('scheduled_from') || request('scheduled_to')) $filterCount++;
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-3 leading-tight">
                        <span>Notifications</span>
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and view all notifications</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleFilterDrawer()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors relative">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters
                        @if($filterCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">{{ $filterCount }}</span>
                        @endif
                    </button>
                    @if(auth()->user()->hasPermission('create-notifications'))
                        <a href="{{ route('notifications.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Notification
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Active Filters Display -->
        @if($filterCount > 0)
        <div class="mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Active Filters ({{ $filterCount }}):</span>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">Clear All</a>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                @if(request('search'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        Search: {{ request('search') }}
                        <button onclick="removeFilter('search')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('notification_type'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        Type: {{ ucfirst(str_replace('_', ' ', request('notification_type'))) }}
                        <button onclick="removeFilter('notification_type')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        Status: {{ ucfirst(request('status')) }}
                        <button onclick="removeFilter('status')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('date_from') || request('date_to'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        Created: {{ request('date_from') ? request('date_from') : 'Any' }} to {{ request('date_to') ? request('date_to') : 'Any' }}
                        <button onclick="removeFilter('date_from'); removeFilter('date_to')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
                @if(request('scheduled_from') || request('scheduled_to'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        Scheduled: {{ request('scheduled_from') ? request('scheduled_from') : 'Any' }} to {{ request('scheduled_to') ? request('scheduled_to') : 'Any' }}
                        <button onclick="removeFilter('scheduled_from'); removeFilter('scheduled_to')" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">×</button>
                    </span>
                @endif
            </div>
            </div>
        </div>
        @endif

        <!-- Notifications Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($notifications->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Read Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scheduled At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($notifications as $notification)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="actions-dropdown">
                                            <button onclick="toggleActionsMenu({{ $notification->id }})" class="actions-dropdown-btn">
                                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                </svg>
                                            </button>
                                            <div id="actions-menu-{{ $notification->id }}" class="actions-dropdown-menu">
                                                @if(auth()->user()->hasPermission('view-notification-details'))
                                                    <button onclick="viewNotification({{ $notification->id }})" class="actions-dropdown-item w-full text-left">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View
                                                    </button>
                                                @endif
                                                @if(auth()->user()->hasPermission('edit-notifications'))
                                                    <a href="{{ route('notifications.edit', $notification->id) }}" class="actions-dropdown-item">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('delete-notifications'))
                                                    <button onclick="confirmDelete({{ $notification->id }}, '{{ addslashes($notification->title) }}')" class="actions-dropdown-item text-red-600 dark:text-red-400">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            @php
                                                $userCount = ($notification->read_count ?? 0) + ($notification->unread_count ?? 0);
                                            @endphp
                                            {{ $userCount }} user(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            @php
                                                $readCount = $notification->read_count ?? 0;
                                                $unreadCount = $notification->unread_count ?? 0;
                                                $totalUsers = $readCount + $unreadCount;
                                            @endphp
                                            @if($totalUsers > 0)
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                        {{ $readCount }} Read
                                                    </span>
                                                    @if($unreadCount > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                                                            {{ $unreadCount }} Unread
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            @if($notification->scheduled_at)
                                                {{ \Carbon\Carbon::parse($notification->scheduled_at)->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($notification->status === 'sent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($notification->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @endif">
                                            {{ ucfirst($notification->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $notification->creator_name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($notification->created_at)
                                                {{ is_object($notification->created_at) ? $notification->created_at->format('M d, Y H:i') : \Carbon\Carbon::parse($notification->created_at)->format('M d, Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new notification.</p>
                    @if(auth()->user()->hasPermission('create-notifications'))
                        <div class="mt-6">
                            <a href="{{ route('notifications.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Notification
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
</div>

<!-- Filter Drawer -->
<div id="filter-overlay" class="filter-overlay" onclick="toggleFilterDrawer()"></div>
<div id="filter-drawer" class="filter-drawer">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Notifications</h2>
            <button onclick="toggleFilterDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="GET" action="{{ route('notifications.index') }}" class="space-y-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or message..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <!-- Notification Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notification Type</label>
                <select name="notification_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Types</option>
                    <option value="instant" {{ request('notification_type') === 'instant' ? 'selected' : '' }}>Instant</option>
                    <option value="after_minutes" {{ request('notification_type') === 'after_minutes' ? 'selected' : '' }}>After Minutes</option>
                    <option value="after_hours" {{ request('notification_type') === 'after_hours' ? 'selected' : '' }}>After Hours</option>
                    <option value="daily" {{ request('notification_type') === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('notification_type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('notification_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            
            <!-- Created Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <!-- Scheduled Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled From</label>
                <input type="date" name="scheduled_from" value="{{ request('scheduled_from') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled To</label>
                <input type="date" name="scheduled_to" value="{{ request('scheduled_to') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('notifications.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-center">
                    Clear All
                </a>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div id="view-modal-overlay" class="view-modal-overlay" onclick="closeViewModal()">
    <div class="view-modal" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Notification Details</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="view-modal-content" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-confirm-overlay" class="delete-confirm-overlay" onclick="closeDeleteConfirm()">
    <div class="delete-confirm-modal" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/20 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-2">Delete Notification</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6" id="delete-confirm-message">
                Are you sure you want to delete this notification? This action cannot be undone.
            </p>
            <div class="flex space-x-3">
                <button onclick="closeDeleteConfirm()" class="flex-1 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <form id="delete-form" method="POST" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let currentNotificationData = {};

    // Filter Drawer Toggle
    function toggleFilterDrawer() {
        const drawer = document.getElementById('filter-drawer');
        const overlay = document.getElementById('filter-overlay');
        
        if (drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('active');
        } else {
            drawer.classList.add('open');
            overlay.classList.add('active');
        }
    }

    // Remove Filter
    function removeFilter(filterName) {
        const url = new URL(window.location.href);
        url.searchParams.delete(filterName);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    // Actions Dropdown Toggle
    function toggleActionsMenu(notificationId) {
        // Close all other dropdowns
        document.querySelectorAll('.actions-dropdown-menu').forEach(menu => {
            if (menu.id !== `actions-menu-${notificationId}`) {
                menu.classList.remove('show');
            }
        });
        
        // Toggle current dropdown
        const menu = document.getElementById(`actions-menu-${notificationId}`);
        const isShowing = menu.classList.contains('show');
        
        if (!isShowing) {
            // Calculate position for fixed dropdown
            const button = menu.previousElementSibling;
            if (button) {
                const rect = button.getBoundingClientRect();
                
                // Set position using fixed positioning
                menu.style.position = 'fixed';
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.style.left = (rect.right - 160) + 'px'; // 160px is min-width
                menu.style.right = 'auto';
                menu.style.bottom = 'auto';
                
                // Ensure dropdown is visible on screen
                menu.classList.add('show'); // Temporarily show to get dimensions
                const menuRect = menu.getBoundingClientRect();
                menu.classList.remove('show');
                
                // Adjust if it goes off screen to the right
                if (menuRect.right > window.innerWidth) {
                    menu.style.left = (window.innerWidth - 160 - 10) + 'px';
                }
                // Adjust if it goes off screen to the left
                if (parseInt(menu.style.left) < 10) {
                    menu.style.left = '10px';
                }
                // Adjust if it goes off screen at bottom
                if (menuRect.bottom > window.innerHeight) {
                    menu.style.top = (rect.top - menuRect.height - 4) + 'px';
                }
                }
        }
        
        menu.classList.toggle('show');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.actions-dropdown')) {
            document.querySelectorAll('.actions-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // View Notification
    async function viewNotification(notificationId) {
        try {
            // Close actions menu
            document.getElementById(`actions-menu-${notificationId}`).classList.remove('show');
            
            // Fetch notification details
            const response = await fetch(`/notifications/${notificationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch notification details');
            }
            
            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch notification details');
            }
            
            const notification = result.notification;
            currentNotificationData = notification;
            
            // Build modal content
            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">${escapeHtml(notification.title || '-')}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                ${getTypeColor(notification.notification_type)}">
                                ${ucfirst(notification.notification_type?.replace(/_/g, ' ') || '-')}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                ${getStatusColor(notification.status)}">
                                ${ucfirst(notification.status || '-')}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requires Web PIN</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.requires_web_pin ? 'Yes' : 'No'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.scheduled_at ? formatDate(notification.scheduled_at) : '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${escapeHtml(notification.creator_name || 'Unknown')}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.created_at ? formatDate(notification.created_at) : '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Users</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${(notification.read_count || 0) + (notification.unread_count || 0)} user(s)</dd>
                    </div>
                </div>
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Message</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">${escapeHtml(notification.message || '-')}</dd>
                </div>
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Delivery Methods</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        ${(notification.delivery_methods || []).map(method => 
                            `<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 mr-2">${ucfirst(method)}</span>`
                        ).join('') || '-'}
                    </dd>
                </div>
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Read Status</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300 mr-2">
                            ${notification.read_count || 0} Read
                        </span>
                        ${(notification.unread_count || 0) > 0 ? `
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                                ${notification.unread_count} Unread
                            </span>
                        ` : ''}
                    </dd>
                </div>
                ${getNotificationTypeDetails(notification)}
                ${getUserList(notification)}
            `;
            
            document.getElementById('view-modal-content').innerHTML = `<dl class="space-y-4">${content}</dl>`;
            document.getElementById('view-modal-overlay').classList.add('show');
        } catch (error) {
            console.error('Error fetching notification:', error);
            alert('Failed to load notification details');
        }
    }

    function closeViewModal() {
        document.getElementById('view-modal-overlay').classList.remove('show');
    }

    // Delete Confirmation
    function confirmDelete(notificationId, title) {
        document.getElementById('delete-confirm-message').textContent = 
            `Are you sure you want to delete the notification "${title}"? This action cannot be undone.`;
        document.getElementById('delete-form').action = `/notifications/${notificationId}`;
        document.getElementById('delete-confirm-overlay').classList.add('show');
        
        // Close actions menu
        document.getElementById(`actions-menu-${notificationId}`).classList.remove('show');
    }

    function closeDeleteConfirm() {
        document.getElementById('delete-confirm-overlay').classList.remove('show');
    }

    // Helper Functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function ucfirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function getTypeColor(type) {
        const colors = {
            'instant': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'after_minutes': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'after_hours': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'daily': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'weekly': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'monthly': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        };
        return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    function getStatusColor(status) {
        const colors = {
            'sent': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'failed': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    function getNotificationTypeDetails(notification) {
        let details = '';
        if (notification.notification_type === 'after_minutes' || notification.notification_type === 'after_hours') {
            details += `
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration Value</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.duration_value || '-'}</dd>
                </div>
            `;
        }
        if (notification.notification_type === 'daily') {
            details += `
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Time</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.daily_time || '-'}</dd>
                </div>
            `;
        }
        if (notification.notification_type === 'weekly') {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            details += `
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Weekly Day</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.weekly_day !== null ? days[notification.weekly_day] : '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Weekly Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.weekly_time || '-'}</dd>
                    </div>
                </div>
            `;
        }
        if (notification.notification_type === 'monthly') {
            details += `
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Day</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.monthly_day || '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${notification.monthly_time || '-'}</dd>
                    </div>
                </div>
            `;
        }
        return details;
    }

    function getUserList(notification) {
        if (!notification.users || notification.users.length === 0) {
            return '';
        }
        
        let userList = '<div class="mt-4"><dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assigned Users</dt><dd class="text-sm text-gray-900 dark:text-gray-100"><div class="space-y-2 max-h-60 overflow-y-auto">';
        
        notification.users.forEach(user => {
            const readStatus = user.is_read ? 
                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">Read</span>' : 
                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">Unread</span>';
            
            const readAt = user.read_at ? `<span class="text-xs text-gray-500 dark:text-gray-400 ml-2">(${formatDate(user.read_at)})</span>` : '';
            
            userList += `
                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <div>
                        <span class="font-medium">${escapeHtml(user.name || 'Unknown')}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">${escapeHtml(user.email || '')}</span>
                    </div>
                    <div class="flex items-center">
                        ${readStatus}
                        ${readAt}
                    </div>
                </div>
            `;
        });
        
        userList += '</div></dd></div>';
        return userList;
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
            closeDeleteConfirm();
        }
    });
</script>
@endpush
