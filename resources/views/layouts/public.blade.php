<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/notification.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('assets/img/notification.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/notification.svg') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        // Configure Tailwind after CDN loads
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tailwind !== 'undefined') {
                tailwind.config = {
                    darkMode: 'class',
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
                };
            }
        });
    </script>

    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    
    <!-- Smooth Theme Transition Styles -->
    <style>
        /* Smooth transitions for all color changes */
        *,
        *::before,
        *::after {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 500ms;
        }
        
        /* Faster transitions for interactive elements */
        button,
        a,
        input,
        select,
        textarea {
            transition-duration: 300ms;
        }
        
        /* Ensure smooth transitions for dark mode classes */
        html {
            transition: background-color 500ms cubic-bezier(0.4, 0, 0.2, 1), color 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            transition: background-color 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Smooth transitions for all sections */
        section {
            transition: background-color 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Smooth transitions for navigation */
        nav {
            transition: background-color 500ms cubic-bezier(0.4, 0, 0.2, 1), border-color 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    
    <!-- Page-specific CSS -->
    @stack('css')
</head>
<body class="antialiased bg-white dark:bg-gray-900 font-sans">
    <!-- Navigation -->
    <nav id="mainNav" class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center hover:opacity-80 transition-opacity duration-300 ease-in-out">
                        <!-- Light mode logo -->
                        <img src="{{ asset('assets/img/white_logo.svg') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-12 w-auto object-contain dark:hidden">
                        <!-- Dark mode logo -->
                        <img src="{{ asset('assets/img/dark_logo.svg') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-12 w-auto object-contain hidden dark:block">
                    </a>
                </div>
                
                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button id="themeToggle" type="button" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 ease-in-out flex items-center justify-center">
                        <!-- Sun icon for dark mode -->
                        <svg id="sunIcon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon icon for light mode -->
                        <svg id="moonIcon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    
                    <!-- Auth Button -->
                    <a href="{{ route('login') }}" 
                       class="bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-700 dark:to-purple-700 text-white px-6 py-2 rounded-lg hover:from-primary-700 hover:to-purple-700 dark:hover:from-primary-800 dark:hover:to-purple-800 transition-all font-medium shadow-md hover:shadow-lg">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white relative z-50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Dark Mode & Navbar Scripts -->
    <script>
        (function() {
            // Apply theme immediately to prevent flash
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            if (savedTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            
            // Initialize dark mode toggle
            function initThemeToggle() {
                const themeToggle = document.getElementById('themeToggle');
                const html = document.documentElement;
                const sunIcon = document.getElementById('sunIcon');
                const moonIcon = document.getElementById('moonIcon');
                
                if (!themeToggle) return;
                
                function updateIcons() {
                    const isDark = html.classList.contains('dark');
                    if (sunIcon && moonIcon) {
                        if (isDark) {
                            sunIcon.classList.remove('hidden');
                            sunIcon.classList.add('block');
                            moonIcon.classList.remove('block');
                            moonIcon.classList.add('hidden');
                        } else {
                            moonIcon.classList.remove('hidden');
                            moonIcon.classList.add('block');
                            sunIcon.classList.remove('block');
                            sunIcon.classList.add('hidden');
                        }
                    }
                }
                
                updateIcons();
                
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isDark = html.classList.contains('dark');
                    
                    // Ensure smooth transition with longer duration
                    html.style.transition = 'background-color 500ms cubic-bezier(0.4, 0, 0.2, 1), color 500ms cubic-bezier(0.4, 0, 0.2, 1)';
                    document.body.style.transition = 'background-color 500ms cubic-bezier(0.4, 0, 0.2, 1)';
                    
                    // Apply transition to all sections
                    const sections = document.querySelectorAll('section');
                    sections.forEach(function(section) {
                        section.style.transition = 'background-color 500ms cubic-bezier(0.4, 0, 0.2, 1)';
                    });
                    
                    if (isDark) {
                        html.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        html.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    
                    updateIcons();
                    
                    // Clean up after transition
                    setTimeout(function() {
                        html.style.transition = '';
                        document.body.style.transition = '';
                        sections.forEach(function(section) {
                            section.style.transition = '';
                        });
                    }, 500);
                });
            }
            
            // Initialize navbar scroll effect
            function initNavbarScroll() {
                const navbar = document.getElementById('mainNav');
                if (!navbar) return;
                
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('shadow-lg');
                    } else {
                        navbar.classList.remove('shadow-lg');
                    }
                });
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initThemeToggle();
                    initNavbarScroll();
                });
            } else {
                initThemeToggle();
                initNavbarScroll();
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
