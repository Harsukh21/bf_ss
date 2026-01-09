@include('tasks.partials.filter-drawer-styles')

@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Completed Tasks</h1>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">View all completed tasks.</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="toggleFilterDrawer()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </button>
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to All Tasks
                </a>
            </div>
        </div>
    </div>

    <!-- Tasks Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        @if($tasks->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($tasks as $task)
                    @include('tasks.partials.task-card', ['task' => $task])
                @endforeach
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tasks->links() }}
                </div>
            @endif
        @else
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No completed tasks</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Completed tasks will appear here.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@include('tasks.partials.filter-drawer', [
    'filterAction' => route('tasks.complete'),
    'clearRoute' => route('tasks.complete'),
    'showStatus' => false,
])

@include('tasks.partials.filter-drawer-scripts')
@endsection
