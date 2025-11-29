@extends('layouts.public')

@section('title', 'Welcome')

@push('css')
<style>
    html {
        scroll-behavior: smooth;
    }
    
    /* Enhanced smooth transitions for theme switching */
    *,
    *::before,
    *::after {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
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
    
    /* Smooth page transitions */
    body {
        transition: background-color 500ms ease-in-out, color 500ms ease-in-out;
    }
    
    section {
        transition: background-color 500ms ease-in-out;
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: background 400ms ease-in-out;
    }
    
    .dark .gradient-text {
        background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .feature-card {
        transition: all 0.3s ease, background-color 500ms cubic-bezier(0.4, 0, 0.2, 1), border-color 500ms cubic-bezier(0.4, 0, 0.2, 1), color 500ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .dark .feature-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    }
    
    /* Statistics Section */
    .statistics-section {
        background: #ffffff;
        padding: 5rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .dark .statistics-section {
        background: #1f2937;
        border-top-color: #374151;
        border-bottom-color: #374151;
    }
    
    .stat-card {
        text-align: center;
        padding: 0;
    }
    
    .stat-number {
        font-size: 4rem;
        font-weight: 700;
        letter-spacing: -0.04em;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: background 500ms cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1.1;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .dark .stat-number {
        background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-label {
        font-size: 0.875rem;
        font-weight: 400;
        color: #4b5563;
        transition: color 500ms cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1.5;
    }
    
    .dark .stat-label {
        color: #9ca3af;
    }
    
    @media (max-width: 1024px) {
        .stat-number {
            font-size: 3rem;
        }
    }
    
    @media (max-width: 768px) {
        .statistics-section {
            padding: 3.5rem 0;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .stat-label {
            font-size: 0.8125rem;
        }
    }
    
    @media (max-width: 640px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
    }
    
    .step-number {
        position: absolute;
        left: 0;
        top: 0;
        width: 2.5rem;
        height: 2.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        transition: background 400ms ease-in-out;
    }
    
    .dark .step-number {
        background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
    }
    
    .section-padding {
        padding: 80px 0;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }
    
    
    .hero-pattern {
        background-image: radial-gradient(circle at 2px 2px, rgba(99, 102, 241, 0.15) 1px, transparent 0);
        background-size: 40px 40px;
    }
    
    .dark .hero-pattern {
        background-image: radial-gradient(circle at 2px 2px, rgba(139, 92, 246, 0.2) 1px, transparent 0);
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 section-padding hero-pattern transition-colors duration-500 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 dark:opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary-500 rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500 rounded-full filter blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center fade-in-up">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <a href="/" class="flex items-center justify-center">
                        <img src="{{ asset('assets/img/white_logo.svg') }}"
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-20 md:h-28 w-auto object-contain dark:hidden">
                        <img src="{{ asset('assets/img/dark_logo.svg') }}"
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-20 md:h-28 w-auto object-contain hidden dark:block">
                    </a>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-6 leading-tight transition-colors duration-500">
                    Professional Sports Betting
                    <span class="gradient-text block mt-3">Management Platform</span>
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed transition-colors duration-500">
                    Manage sports events, track betting markets, monitor real-time odds, and handle risk management with our comprehensive platform. Built for sports betting operators, risk managers, and trading teams.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('login') }}" 
                       class="bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-700 dark:to-purple-700 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-purple-700 dark:hover:from-primary-800 dark:hover:to-purple-800 transition-all font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center">
                        Login to Dashboard
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="#features" 
                       class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-8 py-4 rounded-lg border-2 border-gray-300 dark:border-gray-600 hover:border-primary-600 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-semibold text-lg shadow-md hover:shadow-lg inline-flex items-center">
                        Explore Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics-section transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 lg:gap-16">
                <div class="stat-card">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Events Managed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Active Markets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">System Uptime</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white dark:bg-gray-900 section-padding transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-500">
                    Comprehensive Betting Platform Features
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-500">
                    Everything you need to manage sports betting operations efficiently and effectively
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Event Management -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Event Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Track and manage sports events across multiple sports including Soccer, Tennis, Cricket, Basketball, Boxing, and more. Monitor event status, settlement, and void status in real-time.
                    </p>
                </div>

                <!-- Market Tracking -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Market Tracking</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Monitor betting markets with live rates, pre-bet and in-play status tracking. Filter by sport, tournament, market type, and track market time for comprehensive market analysis.
                    </p>
                </div>

                <!-- Scorecard System -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Scorecard Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Advanced scorecard system with customizable labels (4X, B2C, B2B, USDT, Bookmaker, Unmatch). Track label status with timestamps and manage SC types (Sportradar, Old SC, SR Premium, SpreadeX).
                    </p>
                </div>

                <!-- Risk Management -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Risk Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Comprehensive betlist checking and risk analysis tools. Monitor interrupted events, set market limits, and manage risk parameters with real-time alerts and notifications.
                    </p>
                </div>

                <!-- Market Rates -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Dynamic Market Rates</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Track dynamic market rates with real-time updates. Export data in CSV format, search and filter rates, and analyze market trends with comprehensive reporting tools.
                    </p>
                </div>

                <!-- User & Role Management -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">User & Role Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Complete user management system with role-based access control. Create users, assign roles and permissions, manage user status, and track user activity with comprehensive system logs.
                    </p>
                </div>

                <!-- Real-Time Notifications -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Push Notifications</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Stay informed with real-time push notifications for important events, market changes, risk alerts, and system updates. Never miss critical information.
                    </p>
                </div>

                <!-- Data Export -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Data Export & Reports</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Export events, markets, and market rates data in CSV format. Generate comprehensive reports with advanced filtering and search capabilities for analysis and record-keeping.
                    </p>
                </div>

                <!-- System Logging -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">System Logging</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Complete audit trail with system logs tracking all user actions, admin activities, and system changes. Monitor who did what, when, and from where for compliance and security.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="bg-gray-50 dark:bg-gray-800 section-padding transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-500">
                    How It Works
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-500">
                    Get started with our platform in three simple steps
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="step-card relative pl-12">
                    <div class="step-number">1</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Login & Access</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Login with your credentials using password authentication or Web PIN. Access the dashboard with role-based permissions tailored to your responsibilities.
                    </p>
                </div>

                <div class="step-card relative pl-12">
                    <div class="step-number">2</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Monitor Events & Markets</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Browse sports events, track betting markets, monitor real-time rates, manage scorecards, and check risk parameters. Filter by sport, tournament, status, and date ranges.
                    </p>
                </div>

                <div class="step-card relative pl-12">
                    <div class="step-number">3</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Manage & Export</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Update event status, manage scorecard labels, set market limits, export data, and generate reports. All actions are logged for audit and compliance purposes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bg-white dark:bg-gray-900 section-padding transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-500">
                    Why Choose Our Platform?
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-500">
                    Built specifically for sports betting operations with professional-grade features
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Real-Time Data Processing</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                            Process and display market rates, event updates, and status changes in real-time with optimized database queries and efficient caching.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Secure & Compliant</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                            Enterprise-grade security with role-based access control, Web PIN authentication, comprehensive audit logs, and IP tracking for compliance.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Multi-Sport Support</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                            Support for Soccer, Tennis, Cricket, Basketball, Boxing, Pro Kabaddi, Politics, and more. Easily extensible to add new sports and tournaments.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Advanced Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                            Comprehensive analytics dashboard with event statistics, market trends, performance metrics, and customizable reports for data-driven decisions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="bg-gray-50 dark:bg-gray-800 section-padding transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-500">
                    Perfect For
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-500">
                    Designed for professionals in the sports betting industry
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Sports Betting Operators</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Manage multiple sports events, track betting markets, monitor odds, and handle event settlements efficiently.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Risk Management Teams</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Monitor risk markets, check betlists, set market limits, and manage interrupted events with real-time alerts.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Trading Teams</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Track real-time market rates, analyze trends, manage scorecards, and make data-driven trading decisions.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Operations Managers</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Oversee event operations, manage user permissions, monitor system activity, and ensure smooth day-to-day operations.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Data Analysts</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Export data, generate reports, analyze market trends, and perform comprehensive data analysis for business intelligence.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-500">Compliance Officers</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Access comprehensive audit logs, track user activities, monitor system changes, and ensure regulatory compliance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-white dark:bg-gray-900 section-padding transition-colors duration-500">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-500">
                    Frequently Asked Questions
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto transition-colors duration-500">
                    Everything you need to know about our platform
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">What sports are supported?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Our platform supports multiple sports including Soccer, Tennis, Cricket, Basketball, Boxing, Pro Kabaddi, Politics, and more. New sports can be easily added through configuration.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">How does the scorecard system work?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        The scorecard system allows you to label events with customizable tags (4X, B2C, B2B, USDT, Bookmaker, Unmatch). When all required labels are checked, you can assign an SC Type (Sportradar, Old SC, SR Premium, SpreadeX) with admin verification.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">What is Web PIN authentication?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Web PIN is an alternative authentication method that uses your username and a numeric PIN instead of password. It's designed for quick access and is required for sensitive admin operations like SC Type assignment.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">How does risk management work?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        The risk management module allows you to check betlists, monitor interrupted events, set market limits, and track risk parameters. Events can be marked with scorecard labels to indicate their risk status.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Can I export data for analysis?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Yes, you can export events, markets, and market rates data in CSV format. Advanced filtering options allow you to export specific data sets for analysis and reporting.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">Is there an audit trail?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Yes, all user actions, admin activities, and system changes are logged with timestamps, user information, IP addresses, and descriptions. This provides a complete audit trail for compliance and security purposes.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-colors duration-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500">What are push notifications used for?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                        Push notifications alert you to important events such as market changes, risk alerts, event status updates, and system notifications. You can manage notification preferences in your settings.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-700 dark:to-purple-700 section-padding transition-colors duration-500">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-primary-100 dark:text-primary-200 mb-8 max-w-2xl mx-auto">
                Join professional sports betting operators who trust our platform for event management, risk control, and real-time market tracking.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('login') }}" 
                   class="bg-white text-primary-600 dark:text-primary-700 px-8 py-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-100 transition-all font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center">
                    Login to Dashboard
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="#features" 
                   class="bg-transparent text-white px-8 py-4 rounded-lg border-2 border-white hover:bg-white hover:text-primary-600 dark:hover:text-primary-700 transition-all font-semibold text-lg">
                    Explore Features
                </a>
            </div>
        </div>
    </section>
@endsection
