@extends('layouts.app')

@section('title', 'Bulk User Management')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Bulk User Management</h1>
            <p class="text-gray-600">Insert thousands of users into the database with high performance bulk operations.</p>
        </div>

        <!-- Stats Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users in Database</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($userCount) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Success!</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>{{ session('success')['message'] }}</p>
                            @if(session('success')['performance'])
                                <p class="mt-1"><strong>Performance:</strong> {{ session('success')['performance'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error!</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Insert Users</h3>
                
                <form method="POST" action="{{ route('bulk-users.store') }}" onsubmit="return validateForm()">
                    @csrf
                    
                    <!-- Number of Users -->
                    <div class="mb-6">
                        <label for="count" class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Users to Insert
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="count" 
                                   name="count" 
                                   value="6000"
                                   min="1" 
                                   max="50000" 
                                   required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">users</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Enter the number of users to insert (1 - 50,000)</p>
                    </div>

                    <!-- Insertion Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Insertion Method</label>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="fast" 
                                           name="method" 
                                           type="radio" 
                                           value="fast"
                                           checked
                                           class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="fast" class="font-medium text-gray-700">Fast Method (Recommended)</label>
                                    <p class="text-gray-500">Optimized for speed - can insert 6000+ users in under 1 second. Uses bulk inserts with large batches.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="regular" 
                                           name="method" 
                                           type="radio" 
                                           value="regular"
                                           class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="regular" class="font-medium text-gray-700">Regular Method</label>
                                    <p class="text-gray-500">Uses Faker for realistic data generation. Slower but produces more varied user data.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Insert Users
                        </button>
                        
                        <button type="button" 
                                onclick="confirmClear()"
                                class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Clear All Users
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3">Performance Tips</h4>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>The <strong>Fast Method</strong> can insert 12,000+ records per second using optimized bulk inserts.</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>For best performance, use PostgreSQL with proper indexing and sufficient memory.</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Large batches (2000+ records) provide optimal performance for bulk operations.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Clear Confirmation Modal -->
<script>
function confirmClear() {
    if (confirm('Are you sure you want to clear all users from the database? This action cannot be undone.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("bulk-users.clear") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
