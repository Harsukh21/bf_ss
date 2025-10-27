@extends('layouts.app')

@section('title', 'Server Performance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Server Performance</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Real-time server performance monitoring and statistics</p>
                </div>
                <form action="{{ route('performance.refresh') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    </button>
                </form>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div id="success-alert" class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" onclick="closeAlert('success-alert')" class="inline-flex bg-green-50 dark:bg-green-900/20 rounded-md p-1.5 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/20 focus:ring-green-600">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- System Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- System Uptime -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Uptime</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $systemInfo['uptime'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Load Average -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Load Average</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100" id="load-average">
                            @if(is_array($systemInfo['load_average']))
                                {{ implode(', ', array_map(function($load) { return number_format($load, 2); }, $systemInfo['load_average'])) }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- CPU Cores -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CPU Cores</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100" id="cpu-count">{{ $systemInfo['cpu_count'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Memory Usage</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100" id="memory-usage">{{ $memoryInfo['usage_percentage'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- CPU Usage Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            CPU Usage 
                            <span class="text-lg font-medium text-blue-600 dark:text-blue-400" id="cpu-current-usage">0%</span>
                        </h2>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400" id="cpu-timestamp">{{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="cpuChart" width="400" height="200" class="bg-white dark:bg-gray-800 rounded"></canvas>
                </div>
            </div>

            <!-- Memory Usage Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            Memory Usage 
                            <span class="text-lg font-medium text-green-600 dark:text-green-400" id="memory-current-usage">0%</span>
                        </h2>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400" id="memory-timestamp">{{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="memoryChart" width="400" height="200" class="bg-white dark:bg-gray-800 rounded"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- System Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">System Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Operating System</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $systemInfo['os'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hostname</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $systemInfo['hostname'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Timezone</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $systemInfo['timezone'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['version'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP SAPI</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['sapi'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Memory Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['memory_limit'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Memory Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Memory Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Usage</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['current_usage'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Peak Usage</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['peak_usage'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Memory Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['limit'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage Percentage</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($memoryInfo['limit'] === 'Unlimited')
                                    <span class="text-green-600 dark:text-green-400 font-medium">Unlimited Memory</span>
                                @else
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ $memoryInfo['usage_percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $memoryInfo['usage_percentage'] }}%</span>
                                    </div>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if(isset($memoryInfo['system_memory']))
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">System Memory</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['system_memory']['total'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Available</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['system_memory']['available'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Used</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $memoryInfo['system_memory']['used'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 dark:bg-green-500 h-2 rounded-full" style="width: {{ $memoryInfo['system_memory']['usage_percentage'] }}%"></div>
                                            </div>
                                            <span class="text-xs">{{ $memoryInfo['system_memory']['usage_percentage'] }}%</span>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Disk Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Disk Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Space</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $diskInfo['total'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Used Space</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $diskInfo['used'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Free Space</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $diskInfo['free'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-600 dark:bg-yellow-500 h-2 rounded-full" style="width: {{ $diskInfo['usage_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ $diskInfo['usage_percentage'] }}%</span>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Database Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Database Information</h2>
                </div>
                <div class="p-6">
                    @if(isset($databaseInfo['error']))
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $databaseInfo['error'] }}</div>
                    @else
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Driver</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['driver'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Version</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['version'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Database</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['database'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Host</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['host'] }}:{{ $databaseInfo['port'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Connections</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['connections'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Running Queries</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['running_queries'] }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Uptime</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $databaseInfo['uptime'] }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>

            <!-- Network & Process Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Network & Process</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Server IP</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $networkInfo['server_ip'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client IP</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $networkInfo['client_ip'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Protocol</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $networkInfo['protocol'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Process ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $processInfo['pid'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $processInfo['user'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Method</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $processInfo['request_method'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- PHP Configuration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">PHP Configuration</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Execution Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['max_execution_time'] }}s</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Input Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['max_input_time'] }}s</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Post Max Size</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['post_max_size'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Upload Max Filesize</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['upload_max_filesize'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max File Uploads</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $phpInfo['max_file_uploads'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">OPcache Enabled</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $phpInfo['opcache_enabled'] ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300' }}">
                                    {{ $phpInfo['opcache_enabled'] ? 'Yes' : 'No' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Function to close alerts
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

// Chart.js dark mode configuration
Chart.defaults.color = function(context) {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#374151';
};

Chart.defaults.borderColor = function(context) {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? '#374151' : '#E5E7EB';
};

// Override chart defaults for dark mode
const originalChartDefaults = Chart.defaults;
Chart.defaults = {
    ...originalChartDefaults,
    color: function(context) {
        return document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151';
    },
    borderColor: function(context) {
        return document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
    },
    backgroundColor: function(context) {
        return document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF';
    }
};
document.addEventListener('DOMContentLoaded', function() {
    // Chart configurations
    const isDarkMode = document.documentElement.classList.contains('dark');
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Usage (%)',
                    color: isDarkMode ? '#9CA3AF' : '#374151'
                },
                ticks: {
                    color: isDarkMode ? '#9CA3AF' : '#374151',
                    callback: function(value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: isDarkMode ? '#374151' : '#E5E7EB'
                },
                border: {
                    color: isDarkMode ? '#374151' : '#E5E7EB'
                }
            },
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Time (mm:ss)',
                    color: isDarkMode ? '#9CA3AF' : '#374151'
                },
                ticks: {
                    color: isDarkMode ? '#9CA3AF' : '#374151',
                    maxTicksLimit: 10, // Show fewer time labels for readability
                    callback: function(value, index) {
                        // Show every 30th label to avoid crowding
                        return index % 30 === 0 ? this.getLabelForValue(value) : '';
                    }
                },
                grid: {
                    color: isDarkMode ? '#374151' : '#E5E7EB'
                },
                border: {
                    color: isDarkMode ? '#374151' : '#E5E7EB'
                }
            }
        },
        elements: {
            point: {
                radius: 0
            },
            line: {
                tension: 0.4
            }
        }
    };

    // CPU Chart
    const cpuCtx = document.getElementById('cpuChart').getContext('2d');
    const cpuChart = new Chart(cpuCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'CPU Usage',
                data: [],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                y: {
                    ...chartOptions.scales.y,
                    max: 100, // Show as percentage
                    title: {
                        display: true,
                        text: 'CPU Usage (%)'
                    }
                }
            }
        }
    });

    // Memory Chart
    const memoryCtx = document.getElementById('memoryChart').getContext('2d');
    const memoryChart = new Chart(memoryCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Memory Usage',
                data: [],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                y: {
                    ...chartOptions.scales.y,
                    title: {
                        display: true,
                        text: 'Memory Usage (%)'
                    }
                }
            }
        }
    });

    // Data arrays to store historical data (5 minutes = 300 seconds)
    const maxDataPoints = 300; // 5 minutes of data at 1-second intervals
    let cpuData = [];
    let memoryData = [];
    let timeLabels = [];
    let startTime = Date.now();

    // Function to add data point to charts
    function addDataPoint(cpuValue, memoryValue, timestamp) {
        // Add new data point
        cpuData.push(cpuValue);
        memoryData.push(memoryValue);
        
        // Create time label showing seconds elapsed
        const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsedSeconds / 60);
        const seconds = elapsedSeconds % 60;
        const timeLabel = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        timeLabels.push(timeLabel);

        // Remove old data points if we exceed max (keep last 5 minutes)
        if (cpuData.length > maxDataPoints) {
            cpuData.shift();
            memoryData.shift();
            timeLabels.shift();
        }

        // Update charts
        cpuChart.data.labels = timeLabels;
        cpuChart.data.datasets[0].data = cpuData;
        cpuChart.update('none');

        memoryChart.data.labels = timeLabels;
        memoryChart.data.datasets[0].data = memoryData;
        memoryChart.update('none');
    }

    // Function to fetch live data
    function fetchLiveData() {
        fetch('{{ route("performance.live-data") }}')
            .then(response => response.json())
            .then(data => {
                // Update overview cards
                document.getElementById('load-average').textContent = 
                    Array.isArray(data.cpu.load_average) ? 
                    data.cpu.load_average.map(load => load.toFixed(2)).join(', ') : 
                    'N/A';
                
                document.getElementById('cpu-count').textContent = data.cpu.cpu_count;
                document.getElementById('memory-usage').textContent = data.memory.usage_percentage + '%';
                
                // Update timestamps
                document.getElementById('cpu-timestamp').textContent = data.timestamp;
                document.getElementById('memory-timestamp').textContent = data.timestamp;

                // Update current usage percentages in chart headers
                document.getElementById('cpu-current-usage').textContent = data.cpu.usage_percentage.toFixed(1) + '%';
                document.getElementById('memory-current-usage').textContent = data.memory.usage_percentage.toFixed(1) + '%';

                // Use CPU usage percentage from backend
                let cpuUsage = data.cpu.usage_percentage || 0;

                // Add data points to charts
                addDataPoint(cpuUsage, data.memory.usage_percentage, data.timestamp);
            })
            .catch(error => {
                console.error('Error fetching live data:', error);
            });
    }

    // Initial data fetch
    fetchLiveData();

    // Set up interval to fetch data every second
    const interval = setInterval(fetchLiveData, 1000);

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        clearInterval(interval);
    });

    // Handle theme changes
    function updateChartsForTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        // Update chart colors
        const chartColors = {
            text: isDarkMode ? '#9CA3AF' : '#374151',
            grid: isDarkMode ? '#374151' : '#E5E7EB',
            border: isDarkMode ? '#374151' : '#E5E7EB'
        };
        
        // Update CPU chart
        if (typeof cpuChart !== 'undefined') {
            cpuChart.options.scales.y.ticks.color = chartColors.text;
            cpuChart.options.scales.y.title.color = chartColors.text;
            cpuChart.options.scales.y.grid.color = chartColors.grid;
            cpuChart.options.scales.y.border.color = chartColors.border;
            cpuChart.options.scales.x.ticks.color = chartColors.text;
            cpuChart.options.scales.x.title.color = chartColors.text;
            cpuChart.options.scales.x.grid.color = chartColors.grid;
            cpuChart.options.scales.x.border.color = chartColors.border;
            cpuChart.update('none');
        }
        
        // Update Memory chart
        if (typeof memoryChart !== 'undefined') {
            memoryChart.options.scales.y.ticks.color = chartColors.text;
            memoryChart.options.scales.y.title.color = chartColors.text;
            memoryChart.options.scales.y.grid.color = chartColors.grid;
            memoryChart.options.scales.y.border.color = chartColors.border;
            memoryChart.options.scales.x.ticks.color = chartColors.text;
            memoryChart.options.scales.x.title.color = chartColors.text;
            memoryChart.options.scales.x.grid.color = chartColors.grid;
            memoryChart.options.scales.x.border.color = chartColors.border;
            memoryChart.update('none');
        }
    }

    // Listen for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateChartsForTheme();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});
</script>
@endsection
