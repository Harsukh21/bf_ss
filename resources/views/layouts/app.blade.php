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
    
    <!-- Notification Popup Modal -->
    <div id="notification-popup-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
        <div id="notification-popup-modal" class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 dark:border-red-600 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Notification</h3>
                    </div>
                    <button id="notification-popup-close-btn" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 transition-colors hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="notification-popup-content" class="mb-4">
                    <!-- Notification content will be loaded here -->
                </div>
                
                <div id="notification-popup-web-pin-section" class="hidden">
                    <label for="notification-web-pin-input" class="block text-sm font-medium text-red-800 dark:text-red-200 mb-2">Enter Web PIN to close</label>
                    <input type="text" 
                           id="notification-web-pin-input" 
                           pattern="[0-9]*"
                           inputmode="numeric"
                           maxlength="20"
                           class="w-full px-3 py-2 border-2 border-red-400 dark:border-red-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-red-500 focus:border-red-500"
                           placeholder="Enter your Web PIN">
                    <p id="notification-web-pin-error" class="mt-1 text-sm text-red-700 dark:text-red-300 font-medium hidden"></p>
                </div>
                
                <div id="notification-popup-actions" class="flex justify-end space-x-3 mt-4">
                    <button id="notification-popup-submit-btn" type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition-colors shadow-md">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- IST Time Script -->
    <script src="{{ asset('assets/js/ist-time.js') }}"></script>
    
    <!-- Notification Popup Script -->
    <script>
        let currentNotificationId = null;
        let currentNotificationRequiresPin = false;
        let notificationQueue = [];

        // Load pending notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPendingNotifications();
        });

        function loadPendingNotifications() {
            fetch('{{ route("notifications.pending") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                credentials: 'same-origin',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.notifications && data.notifications.length > 0) {
                    notificationQueue = data.notifications;
                    showNextNotification();
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function showNextNotification() {
            if (notificationQueue.length === 0) {
                return;
            }

            const notification = notificationQueue[0];
            currentNotificationId = notification.id;
            currentNotificationRequiresPin = notification.requires_web_pin;

            // Update popup content
            document.getElementById('notification-popup-content').innerHTML = `
                <div class="mb-4 bg-red-100 dark:bg-red-900/30 rounded-lg p-4 border border-red-300 dark:border-red-700">
                    <h4 class="text-base font-semibold text-red-900 dark:text-red-100 mb-2">${escapeHtml(notification.title)}</h4>
                    <p class="text-sm text-red-800 dark:text-red-200 whitespace-pre-wrap">${escapeHtml(notification.message)}</p>
                </div>
            `;

            // Show/hide web PIN section
            const pinSection = document.getElementById('notification-popup-web-pin-section');
            const pinInput = document.getElementById('notification-web-pin-input');
            const closeBtn = document.getElementById('notification-popup-close-btn');
            const submitBtn = document.getElementById('notification-popup-submit-btn');

            if (currentNotificationRequiresPin) {
                pinSection.classList.remove('hidden');
                closeBtn.classList.add('hidden');
                pinInput.value = '';
                pinInput.focus();
                submitBtn.textContent = 'Verify PIN';
            } else {
                pinSection.classList.add('hidden');
                closeBtn.classList.remove('hidden');
                submitBtn.textContent = 'Close';
            }

            // Show popup
            const overlay = document.getElementById('notification-popup-overlay');
            const modal = document.getElementById('notification-popup-modal');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                modal.style.transform = 'scale(1)';
                modal.style.opacity = '1';
            }, 10);

            // Handle close button (only if no PIN required)
            closeBtn.onclick = function() {
                if (!currentNotificationRequiresPin) {
                    closeNotification();
                }
            };

            // Handle submit button
            submitBtn.onclick = function() {
                if (currentNotificationRequiresPin) {
                    verifyWebPinAndClose();
                } else {
                    closeNotification();
                }
            };

            // Handle Enter key on PIN input
            pinInput.onkeypress = function(e) {
                if (e.key === 'Enter') {
                    verifyWebPinAndClose();
                }
            };

            // Prevent closing by clicking overlay if PIN required
            overlay.onclick = function(e) {
                if (e.target === overlay && !currentNotificationRequiresPin) {
                    closeNotification();
                }
            };

            // Prevent closing with Escape if PIN required
            const escapeHandler = function(e) {
                if (e.key === 'Escape' && !currentNotificationRequiresPin) {
                    closeNotification();
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);
        }

        function verifyWebPinAndClose() {
            const pinInput = document.getElementById('notification-web-pin-input');
            const errorMsg = document.getElementById('notification-web-pin-error');
            const webPin = pinInput.value.trim();

            if (!webPin) {
                errorMsg.textContent = 'Please enter your Web PIN';
                errorMsg.classList.remove('hidden');
                return;
            }

            // Verify PIN via API
            fetch(`/notifications/${currentNotificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    web_pin: webPin,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    errorMsg.classList.add('hidden');
                    closeNotification();
                } else {
                    errorMsg.textContent = data.message || 'Invalid Web PIN';
                    errorMsg.classList.remove('hidden');
                    pinInput.value = '';
                    pinInput.focus();
                }
            })
            .catch(error => {
                console.error('Error verifying PIN:', error);
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.classList.remove('hidden');
            });
        }

        function closeNotification() {
            const overlay = document.getElementById('notification-popup-overlay');
            const modal = document.getElementById('notification-popup-modal');
            const errorMsg = document.getElementById('notification-web-pin-error');
            
            modal.style.transform = 'scale(0.95)';
            modal.style.opacity = '0';

            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                errorMsg.classList.add('hidden');
                
                // Remove current notification from queue
                notificationQueue.shift();
                
                // Show next notification if any
                if (notificationQueue.length > 0) {
                    setTimeout(showNextNotification, 300);
                }
            }, 300);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
    
    <!-- Push Notifications -->
    <script>
        (function() {
            let pushNotificationCheckInterval = null;
            let lastCheckedTime = new Date().toISOString();
            let notificationPermission = null;

            // Request notification permission on page load
            function requestNotificationPermission() {
                if (!('Notification' in window)) {
                    console.log('This browser does not support desktop notifications');
                    return false;
                }

                if (Notification.permission === 'granted') {
                    notificationPermission = 'granted';
                    return true;
                }

                if (Notification.permission !== 'denied') {
                    Notification.requestPermission().then(function(permission) {
                        notificationPermission = permission;
                        if (permission === 'granted') {
                            console.log('Notification permission granted');
                            startPushNotificationPolling();
                        } else {
                            console.log('Notification permission denied');
                        }
                    });
                } else {
                    notificationPermission = 'denied';
                    console.log('Notification permission was previously denied');
                }
                
                return notificationPermission === 'granted';
            }

            // Show browser push notification
            function showPushNotification(notification) {
                if (notificationPermission !== 'granted') {
                    return;
                }

                const options = {
                    body: notification.message,
                    icon: '{{ asset("assets/img/notification_icon.png") }}',
                    badge: '{{ asset("assets/img/notification_icon.png") }}',
                    tag: 'notification-' + notification.id, // Prevent duplicate notifications
                    requireInteraction: false,
                    silent: false,
                };

                const browserNotification = new Notification(notification.title, options);

                // Mark as delivered when notification is clicked
                browserNotification.onclick = function() {
                    window.focus();
                    markPushNotificationDelivered(notification.id);
                    browserNotification.close();
                };

                // Mark as delivered when notification is closed
                browserNotification.onclose = function() {
                    markPushNotificationDelivered(notification.id);
                };

                // Auto close after 10 seconds
                setTimeout(() => {
                    browserNotification.close();
                }, 10000);
            }

            // Mark push notification as delivered
            function markPushNotificationDelivered(notificationId) {
                fetch(`{{ url('notifications') }}/push/${notificationId}/mark-delivered`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    credentials: 'same-origin',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Push notification marked as delivered:', notificationId);
                    }
                })
                .catch(error => {
                    console.error('Error marking push notification as delivered:', error);
                });
            }

            // Check for new push notifications
            function checkPushNotifications() {
                if (notificationPermission !== 'granted') {
                    return;
                }

                fetch('{{ route("notifications.push.pending") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    credentials: 'same-origin',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notifications && data.notifications.length > 0) {
                        // Show each notification
                        data.notifications.forEach(notification => {
                            showPushNotification(notification);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking push notifications:', error);
                });
            }

            // Start polling for push notifications
            function startPushNotificationPolling() {
                if (pushNotificationCheckInterval) {
                    clearInterval(pushNotificationCheckInterval);
                }

                // Check immediately
                checkPushNotifications();

                // Check every 30 seconds
                pushNotificationCheckInterval = setInterval(checkPushNotifications, 30000);
            }

            // Stop polling for push notifications
            function stopPushNotificationPolling() {
                if (pushNotificationCheckInterval) {
                    clearInterval(pushNotificationCheckInterval);
                    pushNotificationCheckInterval = null;
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Request permission and start polling if granted
                if (requestNotificationPermission()) {
                    startPushNotificationPolling();
                }

                // Also check when page becomes visible (user switches back to tab)
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && notificationPermission === 'granted') {
                        checkPushNotifications();
                    }
                });

                // Stop polling when page is hidden
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        // Keep polling but at a reduced rate
                    }
                });
            });

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                stopPushNotificationPolling();
            });
        })();
    </script>
    
    <!-- Push Notification Permission Alert -->
    <div id="pushNotificationAlert" class="fixed bottom-4 right-4 z-50 max-w-sm hidden animate-slide-up">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header with gradient -->
            <div id="notificationAlertHeader" class="bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 px-3 py-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-white/20 dark:bg-white/10 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 id="notificationAlertTitle" class="text-sm font-semibold text-white leading-tight">
                                Enable Notifications
                            </h3>
                            <p class="text-xs text-blue-100 dark:text-blue-200 leading-tight">Stay updated</p>
                        </div>
                    </div>
                    <button onclick="hidePushNotificationAlert()" class="flex-shrink-0 text-white/80 hover:text-white transition-colors p-0.5 rounded hover:bg-white/10">
                        <span class="sr-only">Close</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-3 py-2.5">
                <p id="notificationAlertMessage" class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed mb-2.5">
                    Please allow notifications from your browser to receive important updates and alerts.
                </p>
                
                <!-- Instructions for denied state -->
                <div id="notificationInstructions" class="hidden space-y-1.5 mb-2.5">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Browser Settings:</p>
                    <div class="space-y-1.5">
                        <div class="flex items-start space-x-2 p-1.5 bg-gray-50 dark:bg-gray-700/50 rounded">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">C</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100">Chrome/Edge</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-tight">Lock → Site settings → Notifications</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2 p-1.5 bg-gray-50 dark:bg-gray-700/50 rounded">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-5 h-5 bg-orange-100 dark:bg-orange-900/30 rounded flex items-center justify-center">
                                    <span class="text-xs font-bold text-orange-600 dark:text-orange-400">F</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100">Firefox</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-tight">Lock → More info → Permissions</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2 p-1.5 bg-gray-50 dark:bg-gray-700/50 rounded">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-5 h-5 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">S</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100">Safari</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-tight">Preferences → Websites → Notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div id="notificationAlertActions" class="flex gap-2">
                    <button onclick="requestNotificationPermission()" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enable
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }
    </style>

    <script>
    (function() {
        // Update alert message based on permission status
        function updateAlertMessage(permission) {
            const titleElement = document.getElementById('notificationAlertTitle');
            const messageElement = document.getElementById('notificationAlertMessage');
            const actionsElement = document.getElementById('notificationAlertActions');
            const instructionsElement = document.getElementById('notificationInstructions');
            const headerElement = document.getElementById('notificationAlertHeader');
            const alertElement = document.getElementById('pushNotificationAlert');

            if (!titleElement || !messageElement || !actionsElement || !alertElement) {
                return;
            }

            if (permission === 'denied') {
                // Update header to warning style
                if (headerElement) {
                    headerElement.className = 'bg-gradient-to-r from-amber-500 to-orange-600 dark:from-amber-600 dark:to-orange-700 px-5 py-4';
                }
                
                titleElement.textContent = 'Notifications Disabled';
                messageElement.textContent = 'Notification permission was previously denied. Please enable it in your browser settings to receive important updates.';
                
                // Show instructions
                if (instructionsElement) {
                    instructionsElement.classList.remove('hidden');
                }
                
                // Hide the button since user needs to enable in browser settings
                actionsElement.innerHTML = '';
            } else {
                // Reset to default blue style
                if (headerElement) {
                    headerElement.className = 'bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 px-5 py-4';
                }
                
                titleElement.textContent = 'Enable Notifications';
                messageElement.textContent = 'Please allow notifications from your browser to receive important updates and alerts.';
                
                // Hide instructions
                if (instructionsElement) {
                    instructionsElement.classList.add('hidden');
                }
                
                // Show the button
                actionsElement.innerHTML = '<button onclick="requestNotificationPermission()" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow">' +
                    '<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' +
                    '</svg>Enable</button>';
            }
        }

        // Check notification permission and show/hide alert
        function checkNotificationPermission() {
            if (!('Notification' in window)) {
                // Browser doesn't support notifications
                const alertElement = document.getElementById('pushNotificationAlert');
                if (alertElement) {
                    alertElement.classList.add('hidden');
                }
                return;
            }

            const permission = Notification.permission;
            const alertElement = document.getElementById('pushNotificationAlert');

            if (!alertElement) {
                return;
            }

            if (permission === 'granted') {
                // Permission granted, hide alert
                alertElement.classList.add('hidden');
            } else {
                // Permission not granted, show alert and update message
                updateAlertMessage(permission);
                alertElement.classList.remove('hidden');
            }
        }

        // Request notification permission
        window.requestNotificationPermission = function() {
            if (!('Notification' in window)) {
                updateAlertMessage('unsupported');
                const messageElement = document.getElementById('notificationAlertMessage');
                if (messageElement) {
                    messageElement.textContent = 'This browser does not support desktop notifications.';
                }
                return;
            }

            if (Notification.permission === 'granted') {
                checkNotificationPermission();
                return;
            }

            if (Notification.permission === 'denied') {
                // Update alert to show instructions instead of browser alert
                updateAlertMessage('denied');
                checkNotificationPermission(); // Ensure alert is visible
                return;
            }

            // Request permission
            Notification.requestPermission().then(function(permission) {
                if (permission === 'denied') {
                    updateAlertMessage('denied');
                }
                checkNotificationPermission();
                
                if (permission === 'granted') {
                    // Reload page to start notification polling
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            });
        };

        // Hide alert temporarily (will show again on next page load if permission not granted)
        window.hidePushNotificationAlert = function() {
            const alertElement = document.getElementById('pushNotificationAlert');
            if (alertElement) {
                alertElement.classList.add('hidden');
                // Show again after 1 hour if permission still not granted
                setTimeout(() => {
                    checkNotificationPermission();
                }, 3600000); // 1 hour
            }
        };

        // Check permission on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkNotificationPermission();
        });

        // Also check when page becomes visible (user switches back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkNotificationPermission();
            }
        });
    })();
    </script>
    
    @stack('js')
</body>
</html>
