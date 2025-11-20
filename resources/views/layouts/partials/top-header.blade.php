<!-- Top Header -->
<header id="topHeader" class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 fixed top-0 right-0 left-0 z-50 w-full transition-all duration-300 ease-in-out">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 md:h-16">
            <!-- Left Side: Sidebar Toggle -->
            <div class="flex items-center space-x-3">
                <button id="sidebarToggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                    </svg>
                </button>
                <!-- Mobile Logo -->
                <a href="/" class="md:hidden flex items-center hover:opacity-80 transition-opacity duration-300 ease-in-out">
                    <!-- Light mode logo -->
                    <img src="{{ asset('assets/img/light_logo.png') }}" 
                         alt="{{ config('app.name', 'Laravel') }}" 
                         class="h-10 w-auto object-contain dark:hidden">
                    <!-- Dark mode logo -->
                    <img src="{{ asset('assets/img/dark_logo.png') }}" 
                         alt="{{ config('app.name', 'Laravel') }}" 
                         class="h-10 w-auto object-contain hidden dark:block">
                </a>
            </div>

            <!-- Right Side: Actions -->
            <div class="flex items-center gap-3">
                <!-- IST Time Display -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300 whitespace-nowrap">
                        <span class="font-semibold">IST:</span> 
                        <span id="istTime">{{ now()->setTimezone('Asia/Kolkata')->format('d M Y, h:i:s A') }}</span>
                    </span>
                </div>
                
                <!-- Dark Mode Toggle -->
                <button id="themeToggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 ease-in-out flex items-center justify-center">
                    <!-- Sun icon for dark mode -->
                    <svg id="sunIcon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon icon for light mode -->
                    <svg id="moonIcon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
