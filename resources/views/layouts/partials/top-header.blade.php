<!-- Top Header -->
<header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 right-0 left-64 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Sidebar Toggle Button -->
                <div class="flex items-center">
                        <button id="sidebarToggle" class="p-2 rounded-lg text-gray-600 hover:text-primary-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                            </svg>
                        </button>
                </div>

            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <!-- User Info -->
                @auth
                    <div class="flex items-center space-x-3">
                        <img class="h-8 w-8 rounded-full object-cover" 
                             src="{{ Auth::user()->avatar ?? 'https://i.pravatar.cc/150?img=68' }}" 
                             alt="{{ Auth::user()->name ?? 'User' }}">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                        </svg>
                        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700 transition-colors font-medium text-sm">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 transition-colors">
                        Login
                    </a>
                    <a href="#" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
