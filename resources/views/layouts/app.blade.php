<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS for alert animations -->
    <style>
        .alert {
            transition: all 0.3s ease-in-out;
        }
        .alert-close-btn {
            cursor: pointer;
        }
        .alert-close-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <a href="/" class="text-xl font-bold text-gray-900 hover:text-primary-600 transition-colors">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="/#features" class="text-gray-600 hover:text-primary-600 transition-colors">Features</a>
                        <a href="/#about" class="text-gray-600 hover:text-primary-600 transition-colors">About</a>
                        <a href="/#contact" class="text-gray-600 hover:text-primary-600 transition-colors">Contact</a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('bulk-users.index') }}" 
                           class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                            Get Started
                        </a>
                        <a href="{{ route('single-insert.index') }}" 
                           class="text-orange-600 hover:text-orange-700 transition-colors font-medium">
                            Single Insert Test
                        </a>
                        <a href="{{ route('sports.home') }}" 
                           class="text-green-600 hover:text-green-700 transition-colors font-medium">
                            🏆 Sports
                        </a>
                        <a href="{{ route('users.index') }}" 
                           class="text-gray-600 hover:text-primary-600 transition-colors">
                            View Users
                        </a>
                        <a href="{{ route('database.test') }}" 
                           class="text-gray-600 hover:text-primary-600 transition-colors">
                            DB Test
                        </a>
                        
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-primary-600 transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 transition-colors">Login</a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        // Manual close functionality for alerts
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up alert close functionality');
            
            // Add close functionality to alerts with close buttons
            document.addEventListener('click', function(e) {
                console.log('Click detected on:', e.target);
                
                // Check if clicked element or its parent has the close button class
                const closeBtn = e.target.closest('.alert-close-btn');
                if (closeBtn) {
                    console.log('Close button clicked');
                    const alert = closeBtn.closest('.alert');
                    if (alert) {
                        console.log('Alert found, closing...');
                        // Add smooth fade out animation
                        alert.style.transition = 'all 0.3s ease-in-out';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            alert.remove();
                            console.log('Alert removed');
                        }, 300);
                    }
                }
            });
            
            // Also add direct event listeners to existing close buttons
            const closeButtons = document.querySelectorAll('.alert-close-btn');
            console.log('Found', closeButtons.length, 'close buttons');
            closeButtons.forEach((btn, index) => {
                btn.addEventListener('click', function(e) {
                    console.log('Direct close button click', index);
                    e.preventDefault();
                    e.stopPropagation();
                    const alert = this.closest('.alert');
                    if (alert) {
                        alert.style.transition = 'all 0.3s ease-in-out';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                });
            });
        });

        // Form validation
        function validateForm() {
            const count = document.getElementById('count').value;
            const method = document.querySelector('input[name="method"]:checked');
            
            if (!count || count < 1 || count > 50000) {
                alert('Please enter a valid count between 1 and 50,000');
                return false;
            }
            
            if (!method) {
                alert('Please select an insertion method');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>
