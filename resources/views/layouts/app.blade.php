<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Bulk User Management')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Heroicons CDN -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
    
    @stack('css')
</head>
<body class="bg-white dark:bg-gray-900 min-h-screen font-sans dark-mode-transition">
    <div class="min-h-screen flex">
        <!-- Include Sidebar -->
        @include('layouts.partials.sidebar')
        
        <!-- Main Content Area -->
        <div id="mainContent" class="flex-1 w-full transition-all duration-300 ease-in-out">
            <!-- Include Top Header -->
            @include('layouts.partials.top-header')
            
            <!-- Main Content -->
            <main class="pt-14 md:pt-16 bg-white dark:bg-gray-900 dark-mode-transition">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Common JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    <!-- Sidebar JavaScript -->
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    
    @stack('scripts')
    
    <!-- Toast Notification System -->
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2">
        <!-- Toast notifications will be dynamically inserted here -->
    </div>

    <!-- Toast Notification System & Back Button Prevention -->
    <script>
        // Toast Notification System
        class ToastNotification {
            static show(message, type = 'info', duration = 5000) {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
                
                // Toast styles based on type
                const typeStyles = {
                    success: 'bg-green-500 border-green-600',
                    error: 'bg-red-500 border-red-600',
                    warning: 'bg-yellow-500 border-yellow-600',
                    info: 'bg-blue-500 border-blue-600',
                    confirm: 'bg-purple-500 border-purple-600'
                };
                
                const iconStyles = {
                    success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>`,
                    error: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>`,
                    warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>`,
                    info: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>`,
                    confirm: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>`
                };

                toast.innerHTML = `
                    <div class="flex items-center p-4 rounded-lg shadow-lg border-l-4 ${typeStyles[type]} text-white max-w-md">
                        <div class="flex-shrink-0">
                            ${iconStyles[type]}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                }, 100);

                // Auto remove
                if (duration > 0) {
                    setTimeout(() => {
                        this.remove(toast);
                    }, duration);
                }

                return toast;
            }

            static remove(toast) {
                if (!toast) return;
                
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.parentElement.removeChild(toast);
                    }
                }, 300);
            }

            static confirm(message, confirmText = 'Confirm', cancelText = 'Cancel') {
                return new Promise((resolve) => {
                    const container = document.getElementById('toast-container');
                    if (!container) {
                        resolve(false);
                        return;
                    }

                    const toast = document.createElement('div');
                    toast.className = `transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
                    
                    toast.innerHTML = `
                        <div class="flex flex-col p-6 rounded-lg shadow-xl border-l-4 bg-purple-500 border-purple-600 text-white max-w-md">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-lg font-semibold">Confirmation Required</h3>
                                </div>
                                <button onclick="this.closest('.toast-confirm').remove()" class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm">${message}</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="handleToastConfirm(false, this.closest('.toast-confirm'))" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    ${cancelText}
                                </button>
                                <button onclick="handleToastConfirm(true, this.closest('.toast-confirm'))" class="flex-1 bg-white hover:bg-gray-100 text-purple-600 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    `;

                    toast.classList.add('toast-confirm');
                    container.appendChild(toast);

                    // Animate in
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                        toast.classList.add('translate-x-0', 'opacity-100');
                    }, 100);

                    // Store resolve function
                    toast.resolve = resolve;
                });
            }
        }

        // Handle toast confirm responses
        function handleToastConfirm(result, toastElement) {
            if (toastElement && toastElement.resolve) {
                toastElement.resolve(result);
            }
            ToastNotification.remove(toastElement);
        }

        // Make ToastNotification globally available
        window.ToastNotification = ToastNotification;

        // Prevent back button access after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache, redirect to login
                window.location.href = '/login';
            }
        });
        
        // Clear browser history on logout
        function clearHistory() {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Clear history when page loads
        document.addEventListener('DOMContentLoaded', function() {
            clearHistory();
        });
        
        // Prevent caching of this page
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function(event) {
                window.history.pushState(null, null, window.location.href);
            });
        }
    </script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- IST Time Script -->
    <script src="{{ asset('assets/js/ist-time.js') }}"></script>
    
    @stack('js')
</body>
</html>
