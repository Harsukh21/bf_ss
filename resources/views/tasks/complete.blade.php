@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Completed Tasks</h1>
        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">View all completed tasks.</p>
    </div>

    <!-- Tasks Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6">
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No completed tasks</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Completed tasks will appear here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
