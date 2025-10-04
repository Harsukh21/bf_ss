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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Database Stats -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
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

            <!-- Server Resources -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Server Resources</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span id="cpu-usage">Loading...</span> CPU | 
                                        <span id="memory-usage">Loading...</span> RAM
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="text-right">
                            <button onclick="toggleResources()" class="text-xs text-blue-600 hover:text-blue-800">
                                <span id="resources-toggle">Show Details</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Resources Panel (Hidden by default) -->
        <div id="resources-panel" class="bg-white shadow rounded-lg mb-8 hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Real-time Server Resources
                    <span class="ml-2 text-sm font-normal text-gray-500">Last updated: <span id="last-updated">--:--:--</span></span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- CPU Usage -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600" id="cpu-percentage">0%</div>
                        <div class="text-sm text-gray-500">CPU Usage</div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="cpu-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600" id="memory-percentage">0%</div>
                        <div class="text-sm text-gray-500">Memory Usage</div>
                        <div class="text-xs text-gray-400" id="memory-details">0 MB / 0 MB</div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" id="memory-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Disk Usage -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600" id="disk-percentage">0%</div>
                        <div class="text-sm text-gray-500">Disk Usage</div>
                        <div class="text-xs text-gray-400" id="disk-details">0 GB / 0 GB</div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" id="disk-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Database Info -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600" id="db-connections">0</div>
                        <div class="text-sm text-gray-500">Active Connections</div>
                        <div class="text-xs text-gray-400" id="db-size">Database Size: N/A</div>
                        <div class="mt-2 text-xs text-gray-400" id="uptime">Uptime: N/A</div>
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
        <div id="mainForm" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Insert Users</h3>
                
                <form method="POST" action="{{ route('bulk-users.store') }}" id="userForm">
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
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Insert Users
                        </button>
                        
                        <button type="button" 
                                id="clearButton"
                                onclick="confirmClear()"
                                class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
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

<!-- JavaScript Functions -->
<script>
// Resource monitoring variables
let resourceUpdateInterval;
let isResourcesPanelVisible = false;

// Clear Confirmation Modal
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

// Toggle resources panel
function toggleResources() {
    const panel = document.getElementById('resources-panel');
    const toggle = document.getElementById('resources-toggle');
    
    if (isResourcesPanelVisible) {
        panel.classList.add('hidden');
        toggle.textContent = 'Show Details';
        isResourcesPanelVisible = false;
        clearInterval(resourceUpdateInterval);
    } else {
        panel.classList.remove('hidden');
        toggle.textContent = 'Hide Details';
        isResourcesPanelVisible = true;
        startResourceMonitoring();
    }
}

// Start monitoring server resources
function startResourceMonitoring() {
    // Initial load
    fetchResources();
    
    // Update every 2 seconds
    resourceUpdateInterval = setInterval(fetchResources, 2000);
}

// Fetch server resources
function fetchResources() {
    fetch('{{ route("bulk-users.resources") }}')
        .then(response => response.json())
        .then(data => {
            updateResourceDisplay(data);
        })
        .catch(error => {
            console.error('Error fetching resources:', error);
        });
}

// Update resource display
function updateResourceDisplay(data) {
    // Update summary in main card
    document.getElementById('cpu-usage').textContent = data.cpu_usage + '%';
    document.getElementById('memory-usage').textContent = data.memory_usage.percentage + '%';
    
    // Update detailed panel
    document.getElementById('last-updated').textContent = data.timestamp;
    
    // CPU
    document.getElementById('cpu-percentage').textContent = data.cpu_usage + '%';
    document.getElementById('cpu-bar').style.width = data.cpu_usage + '%';
    
    // Memory
    document.getElementById('memory-percentage').textContent = data.memory_usage.percentage + '%';
    document.getElementById('memory-details').textContent = data.memory_usage.used + ' / ' + data.memory_usage.total;
    document.getElementById('memory-bar').style.width = data.memory_usage.percentage + '%';
    
    // Disk
    document.getElementById('disk-percentage').textContent = data.disk_usage.percentage + '%';
    document.getElementById('disk-details').textContent = data.disk_usage.used + ' / ' + data.disk_usage.total;
    document.getElementById('disk-bar').style.width = data.disk_usage.percentage + '%';
    
    // Database
    document.getElementById('db-connections').textContent = data.active_connections;
    document.getElementById('db-size').textContent = 'Database Size: ' + data.database_size;
    document.getElementById('uptime').textContent = 'Uptime: ' + data.uptime;
    
    // Color coding for resource usage
    updateResourceColors(data);
}

// Update resource colors based on usage levels
function updateResourceColors(data) {
    // CPU color coding
    const cpuElement = document.getElementById('cpu-percentage');
    const cpuBar = document.getElementById('cpu-bar');
    if (data.cpu_usage > 80) {
        cpuElement.className = 'text-2xl font-bold text-red-600';
        cpuBar.className = 'bg-red-600 h-2 rounded-full transition-all duration-300';
    } else if (data.cpu_usage > 60) {
        cpuElement.className = 'text-2xl font-bold text-orange-600';
        cpuBar.className = 'bg-orange-600 h-2 rounded-full transition-all duration-300';
    } else {
        cpuElement.className = 'text-2xl font-bold text-blue-600';
        cpuBar.className = 'bg-blue-600 h-2 rounded-full transition-all duration-300';
    }
    
    // Memory color coding
    const memElement = document.getElementById('memory-percentage');
    const memBar = document.getElementById('memory-bar');
    if (data.memory_usage.percentage > 80) {
        memElement.className = 'text-2xl font-bold text-red-600';
        memBar.className = 'bg-red-600 h-2 rounded-full transition-all duration-300';
    } else if (data.memory_usage.percentage > 60) {
        memElement.className = 'text-2xl font-bold text-orange-600';
        memBar.className = 'bg-orange-600 h-2 rounded-full transition-all duration-300';
    } else {
        memElement.className = 'text-2xl font-bold text-green-600';
        memBar.className = 'bg-green-600 h-2 rounded-full transition-all duration-300';
    }
    
    // Disk color coding
    const diskElement = document.getElementById('disk-percentage');
    const diskBar = document.getElementById('disk-bar');
    if (data.disk_usage.percentage > 90) {
        diskElement.className = 'text-2xl font-bold text-red-600';
        diskBar.className = 'bg-red-600 h-2 rounded-full transition-all duration-300';
    } else if (data.disk_usage.percentage > 80) {
        diskElement.className = 'text-2xl font-bold text-orange-600';
        diskBar.className = 'bg-orange-600 h-2 rounded-full transition-all duration-300';
    } else {
        diskElement.className = 'text-2xl font-bold text-orange-600';
        diskBar.className = 'bg-orange-600 h-2 rounded-full transition-all duration-300';
    }
}

// Minimal JavaScript - only for resource monitoring
document.addEventListener('DOMContentLoaded', function() {
    // Start basic resource monitoring (summary only)
    fetchResources();
    setInterval(fetchResources, 5000); // Update every 5 seconds for summary
});
</script>
@endsection
