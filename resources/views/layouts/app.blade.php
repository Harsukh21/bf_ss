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
    
    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    
    <!-- Heroicons CDN -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
    
    @stack('css')
</head>
<body class="bg-white min-h-screen font-sans">
    <div class="min-h-screen flex">
        <!-- Include Sidebar -->
        @include('layouts.partials.sidebar')
        
        <!-- Main Content Area -->
        <div id="mainContent" class="flex-1 md:ml-64 ml-0 transition-all duration-300 ease-in-out pt-16">
            <!-- Include Top Header -->
            @include('layouts.partials.top-header')
            
            <!-- Main Content -->
            <main class="p-6 bg-white">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden" style="display: none !important;"></div>

    <!-- Common JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    <!-- Sidebar JavaScript -->
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
