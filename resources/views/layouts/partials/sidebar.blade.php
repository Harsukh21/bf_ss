<!-- Sidebar -->
<aside id="sidebar" class="sidebar-open bg-white dark:bg-gray-900 shadow-lg border-r border-gray-200 dark:border-gray-700 w-64 min-h-screen fixed left-0 top-0 z-40 sidebar-transition">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="px-4 h-16 border-b border-gray-200 dark:border-gray-700 flex items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 dark:bg-primary-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path>
                            </svg>
                        </div>
                <a href="/" class="text-xl font-bold text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 ease-in-out">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
        </div>
        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                </svg>
                Dashboard
            </a>


            <!-- Events Section -->
            <div class="mt-8">
                <h3 class="px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Main</h3>
                
                <!-- Users Dropdown -->
                <div class="relative">
                    @php
                        $isUsersActive = request()->routeIs('users.*');
                    @endphp
                    <button onclick="toggleDropdown('users')" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ $isUsersActive ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Users
                        </div>
                        <svg id="users-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isUsersActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="users-dropdown" class="space-y-1 ml-4 {{ $isUsersActive ? '' : 'hidden' }}">
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('users.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            User List
                        </a>
                    </div>
                </div>
                
                <!-- Events Dropdown -->
                <div class="relative">
                    @php
                        $isEventsActive = request()->routeIs('events.*');
                    @endphp
                    <button onclick="toggleDropdown('events')" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ $isEventsActive ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Events
                        </div>
                        <svg id="events-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isEventsActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="events-dropdown" class="space-y-1 ml-4 {{ $isEventsActive ? '' : 'hidden' }}">
                        <a href="{{ route('events.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('events.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Event List
                        </a>
                    </div>
                </div>
                <!-- Markets Dropdown -->
                <div class="relative">
                    @php
                        $isMarketsActive = request()->routeIs('markets.*');
                    @endphp
                    <button onclick="toggleDropdown('markets')" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ $isMarketsActive ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Markets
                        </div>
                        <svg id="markets-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isMarketsActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="markets-dropdown" class="space-y-1 ml-4 {{ $isMarketsActive ? '' : 'hidden' }}">
                        <a href="{{ route('markets.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('markets.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Market List
                        </a>
                    </div>
                </div>
                
                <!-- Market Rates Dropdown -->
                <div class="relative">
                    @php
                        $isMarketRatesActive = request()->routeIs('market-rates.*');
                    @endphp
                    <button onclick="toggleDropdown('market-rates')" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ $isMarketRatesActive ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Market Rates
                        </div>
                        <svg id="market-rates-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isMarketRatesActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="market-rates-dropdown" class="space-y-1 ml-4 {{ $isMarketRatesActive ? '' : 'hidden' }}">
                        <a href="{{ route('market-rates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('market-rates.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Market Rates List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="mt-6">
                <h3 class="px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Settings</h3>
                
                <!-- Settings Dropdown -->
                <div class="relative">
                    @php
                        $isSettingsActive = request()->routeIs('general-settings.*') || request()->routeIs('system-logs.*') || request()->routeIs('performance.*');
                        $settingsDropdownOpen = $isSettingsActive ? 'open' : '';
                    @endphp
                    <button onclick="toggleDropdown('settings')" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ $isSettingsActive ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-r-2 border-primary-600 dark:border-primary-400' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </div>
                        <svg id="settings-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isSettingsActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="settings-dropdown" class="mt-2 space-y-1 ml-4 {{ $isSettingsActive ? '' : 'hidden' }}">
                        <a href="{{ route('general-settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('general-settings.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            General
                        </a>
                        <a href="{{ route('system-logs.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('system-logs.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            System Log
                        </a>
                        <a href="{{ route('performance.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('performance.*') ? 'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-r-3 border-primary-600 dark:border-primary-400 font-semibold' : '' }}">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Performance
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        @auth
            <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <img class="h-8 w-8 rounded-full object-cover" 
                         src="{{ Auth::user()->avatar ?? 'https://i.pravatar.cc/150?img=68' }}" 
                         alt="{{ Auth::user()->name ?? 'User' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email ?? 'user@example.com' }}</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</aside>
