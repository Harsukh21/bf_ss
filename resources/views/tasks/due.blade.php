@extends('layouts.app')

@section('title', 'Due Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Due Tasks</h1>
        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">View tasks that are due or overdue.</p>
    </div>

    <!-- Tasks Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No due tasks</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tasks that are due will appear here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
