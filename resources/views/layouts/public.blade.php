<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome') - {{ config('app.name', 'Laravel') }}</title>

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
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
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
    
    <!-- Page-specific CSS -->
    @stack('css')
    
    <!-- Heroicons CDN -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
</head>
<body class="antialiased bg-white font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 relative z-50 nav-z-index">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center hover:opacity-80 transition-opacity duration-300 ease-in-out">
                        <!-- Light mode logo -->
                        <img src="{{ asset('assets/img/light_logo.png') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-12 w-auto object-contain dark:hidden">
                        <!-- Dark mode logo -->
                        <img src="{{ asset('assets/img/dark_logo.png') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-12 w-auto object-contain hidden dark:block">
                    </a>
                </div>
                
                <!-- Auth Button -->
                <div class="flex items-center">
                    <a href="{{ route('login') }}" 
                       class="bg-gradient-to-r from-primary-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-primary-700 hover:to-purple-700 transition-all font-medium cursor-pointer hover:cursor-pointer">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white relative z-50 footer-z-index">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Common JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>

