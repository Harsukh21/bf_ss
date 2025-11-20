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
    
    <!-- Custom Scrollbar Styles -->
    <style>
        /* WebKit Scrollbar (Chrome, Safari, Edge) */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
            transition: background 0.2s ease;
        }
        
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
            background-clip: padding-box;
        }
        
        /* Dark mode scrollbar */
        .dark .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(107, 114, 128, 0.4);
            background-clip: padding-box;
        }
        
        .dark .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.6);
            background-clip: padding-box;
        }
        
        /* Firefox Scrollbar */
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
        }
        
        .dark .sidebar-scrollbar {
            scrollbar-color: rgba(107, 114, 128, 0.4) transparent;
        }
        
        /* Smooth scrolling */
        .sidebar-scrollbar {
            scroll-behavior: smooth;
        }
    </style>
    
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

        // Logout Confirmation Modal
        function showLogoutConfirm(message, confirmText = 'Yes, Logout', cancelText = 'Cancel') {
            return new Promise((resolve) => {
                // Create a full-screen overlay for the confirmation modal
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4';
                overlay.style.transition = 'opacity 0.3s ease-in-out';
                overlay.style.opacity = '0';

                // Create the modal container
                const modal = document.createElement('div');
                modal.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 scale-95';
                modal.style.opacity = '0';
                
                modal.innerHTML = `
                    <div class="flex flex-col p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Logout</h3>
                            </div>
                            <button onclick="handleLogoutConfirm(false, this.closest('.logout-confirm-overlay'))" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">${message}</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="handleLogoutConfirm(false, this.closest('.logout-confirm-overlay'))" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                ${cancelText}
                            </button>
                            <button onclick="handleLogoutConfirm(true, this.closest('.logout-confirm-overlay'))" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                ${confirmText}
                            </button>
                        </div>
                    </div>
                `;

                overlay.classList.add('logout-confirm-overlay');
                overlay.appendChild(modal);
                document.body.appendChild(overlay);

                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';

                // Animate in
                setTimeout(() => {
                    overlay.style.opacity = '1';
                    modal.style.opacity = '1';
                    modal.style.transform = 'scale(1)';
                }, 10);

                // Close on overlay click (outside modal)
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        handleLogoutConfirm(false, overlay);
                    }
                });

                // Close on Escape key
                const escapeHandler = (e) => {
                    if (e.key === 'Escape') {
                        handleLogoutConfirm(false, overlay);
                        document.removeEventListener('keydown', escapeHandler);
                    }
                };
                document.addEventListener('keydown', escapeHandler);

                // Store resolve function on overlay
                overlay.resolve = resolve;
            });
        }

        // Handle logout confirmation responses
        function handleLogoutConfirm(result, overlayElement) {
            if (overlayElement && overlayElement.resolve) {
                overlayElement.resolve(result);
                
                // Animate out
                const modal = overlayElement.querySelector('div[class*="bg-white"], div[class*="bg-gray-800"]');
                if (modal) {
                    modal.style.opacity = '0';
                    modal.style.transform = 'scale(0.95)';
                }
                overlayElement.style.opacity = '0';
                
                setTimeout(() => {
                    // Re-enable body scroll
                    document.body.style.overflow = '';
                    overlayElement.remove();
                }, 300);
            }
        }

        // Handle sidebar logout
        async function handleSidebarLogout() {
            try {
                // Show logout confirmation modal
                const confirmed = await showLogoutConfirm(
                    'Are you sure you want to logout? This will end your current session and redirect you to the login page. Any unsaved work will be lost.',
                    'Yes, Logout'
                );
                
                if (confirmed) {
                    // Show logout progress toast (if available)
                    if (typeof ToastNotification !== 'undefined' && typeof ToastNotification.show === 'function') {
                        ToastNotification.show('Logging out... Please wait.', 'info', 2000);
                    }
                    
                    // Clear all browser storage
                    try {
                        if (typeof(Storage) !== "undefined") {
                            localStorage.clear();
                            sessionStorage.clear();
                        }
                    } catch (e) {
                        console.warn('Could not clear storage:', e);
                    }
                    
                    // Clear browser history
                    try {
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState(null, null, '/login');
                        }
                    } catch (e) {
                        console.warn('Could not update history:', e);
                    }
                    
                    // Submit the logout form
                    const form = document.getElementById('sidebarLogoutForm');
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Logout form not found');
                        // Fallback: redirect manually
                        window.location.href = '/login';
                    }
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Fallback to native confirm if modal fails
                if (confirm('Are you sure you want to logout? This will end your current session and redirect you to the login page. Any unsaved work will be lost.')) {
                    const form = document.getElementById('sidebarLogoutForm');
                    if (form) {
                        form.submit();
                    } else {
                        window.location.href = '/login';
                    }
                }
            }
        }
        
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
