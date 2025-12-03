@extends('layouts.public')

@section('title', 'Welcome')

@push('css')
<style>
    /* World-Class Landing Page Styles */
    
    /* Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }
    
    /* Gradient Text Animation */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
    
    /* World-Class Hero Section with White Background & Advanced Animations */
    .hero-section {
        position: relative;
        overflow: hidden;
        min-height: 600px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 30%, #f0f4ff 60%, #e8f0ff 100%);
        background-size: 400% 400%;
        animation: heroGradient 20s ease infinite;
    }
    
    /* Center Radar Wave Animation */
    .hero-radar-waves {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }
    
    .hero-radar-wave {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 2px solid rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        animation: radarWave 3s ease-out infinite;
    }
    
    .hero-radar-wave:nth-child(2) {
        animation-delay: 1s;
        border-color: rgba(118, 75, 162, 0.3);
    }
    
    .hero-radar-wave:nth-child(3) {
        animation-delay: 2s;
        border-color: rgba(102, 126, 234, 0.3);
    }
    
    .dark .hero-radar-wave {
        border-color: rgba(139, 92, 246, 0.4);
    }
    
    .dark .hero-radar-wave:nth-child(2) {
        border-color: rgba(99, 102, 241, 0.4);
    }
    
    @keyframes radarWave {
        0% {
            transform: translate(-50%, -50%) scale(0.5);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(4);
            opacity: 0;
        }
    }
    
    .dark .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #334155 60%, #475569 100%);
        background-size: 400% 400%;
    }
    
    @keyframes heroGradient {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
    
    
    
    /* Premium Feature Cards */
    .feature-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .feature-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .feature-card:hover::before {
        opacity: 1;
    }
    
    .feature-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -12px rgba(102, 126, 234, 0.3);
    }
    
    .dark .feature-card:hover {
        box-shadow: 0 20px 40px -12px rgba(139, 92, 246, 0.4);
    }
    
    .feature-card svg {
        transition: transform 0.4s ease;
    }
    
    .feature-card:hover svg {
        transform: scale(1.1) rotate(5deg);
    }
    
    /* Section Padding */
    .section-padding {
        padding: 100px 0;
    }
    
    /* Animated Statistics */
    .stat-number {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
        line-height: 1.2;
    }
    
    .stat-card {
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card:hover .stat-number {
        transform: scale(1.1);
    }
    
    /* Premium Step Cards */
    .step-card {
        position: relative;
        padding-left: 4rem;
        transition: transform 0.3s ease;
    }
    
    .step-card:hover {
        transform: translateX(10px);
    }
    
    .step-number {
        position: absolute;
        left: 0;
        top: 0;
        width: 3rem;
        height: 3rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .step-card:hover .step-number {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }
    
    /* Scroll Reveal Animation */
    .scroll-reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .scroll-reveal.revealed {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Premium Use Case Cards */
    .use-case-card {
        transition: all 0.3s ease;
    }
    
    .use-case-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .dark .use-case-card:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    /* FAQ Cards */
    .faq-card {
        transition: all 0.3s ease;
    }
    
    .faq-card:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .dark .faq-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }
    
    /* CTA Section Enhancement */
    .cta-section {
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: ctaShimmer 10s ease-in-out infinite;
    }
    
    @keyframes ctaShimmer {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.6;
        }
    }
    
    /* Smooth Fade In */
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
        animation: fadeInUp 0.8s ease-out;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .section-padding {
            padding: 60px 0;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section section-padding">
        <!-- Center Radar Wave Animation -->
        <div class="hero-radar-waves">
            <div class="hero-radar-wave"></div>
            <div class="hero-radar-wave"></div>
            <div class="hero-radar-wave"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center fade-in-up">
                <!-- Logo -->
                <div class="flex justify-center mb-10">
                    <a href="/" class="flex items-center justify-center transform hover:scale-110 transition-transform duration-300">
                        <!-- Light mode: white_logo.svg (has dark text for light background) -->
                        <img src="{{ asset('assets/img/white_logo.svg') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-24 md:h-32 w-auto object-contain drop-shadow-2xl dark:hidden">
                        <!-- Dark mode: dark_logo.svg (has light text for dark background) -->
                        <img src="{{ asset('assets/img/dark_logo.svg') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-24 md:h-32 w-auto object-contain drop-shadow-2xl hidden dark:block">
                    </a>
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Powerful Event
                    <span class="block mt-2 gradient-text">Management Platform</span>
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-700 dark:text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-light">
                    Manage sports events, markets, and real-time data with an intuitive, feature-rich dashboard designed for professionals.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="{{ route('login') }}" 
                       class="group bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-700 dark:to-purple-700 text-white px-10 py-5 rounded-xl hover:from-primary-700 hover:to-purple-700 dark:hover:from-primary-800 dark:hover:to-purple-800 transition-all font-bold text-lg shadow-2xl hover:shadow-primary-500/30 transform hover:scale-110 inline-flex items-center">
                        Get Started
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="#features" 
                       class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-10 py-5 rounded-xl border-2 border-gray-300 dark:border-gray-600 hover:border-primary-600 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-110 inline-flex items-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div class="stat-card scroll-reveal">
                    <div class="stat-number" data-target="10000" data-suffix="K+">0</div>
                    <div class="text-gray-600 dark:text-gray-300 mt-3 font-semibold text-lg">Events Managed</div>
                </div>
                <div class="stat-card scroll-reveal" style="transition-delay: 0.1s">
                    <div class="stat-number" data-target="5000" data-suffix="K+">0</div>
                    <div class="text-gray-600 dark:text-gray-300 mt-3 font-semibold text-lg">Active Users</div>
                </div>
                <div class="stat-card scroll-reveal" style="transition-delay: 0.2s">
                    <div class="stat-number" data-target="99.9" data-suffix="%">0</div>
                    <div class="text-gray-600 dark:text-gray-300 mt-3 font-semibold text-lg">Uptime</div>
                </div>
                <div class="stat-card scroll-reveal" style="transition-delay: 0.3s">
                    <div class="stat-number">24/7</div>
                    <div class="text-gray-600 dark:text-gray-300 mt-3 font-semibold text-lg">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white dark:bg-gray-900 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    Everything You Need
                </h2>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                    A comprehensive solution for event tracking, market analysis, and real-time data management.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Event Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Comprehensive event tracking with real-time updates, status management, and detailed event information.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.1s">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Market Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Advanced market data analysis with live rates, trend tracking, and comprehensive reporting tools.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Real-Time Updates</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Get instant notifications and updates on events, market changes, and important alerts.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.3s">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 dark:from-yellow-600 dark:to-yellow-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Secure Access</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Enterprise-grade security with role-based access control and user permission management.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Data Export</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Export your data easily in CSV format with advanced filtering and customization options.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.5s">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Fast & Reliable</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Built for performance with optimized queries and responsive design for all devices.
                    </p>
                </div>

                <!-- Feature 7 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 dark:from-teal-600 dark:to-teal-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Scorecard Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Track in-play events with comprehensive scorecard features, labels, and interruption management.
                    </p>
                </div>

                <!-- Feature 8 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.7s">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 dark:from-pink-600 dark:to-pink-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Risk Management</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Advanced risk market tracking with filters, labels, and status management.
                    </p>
                </div>

                <!-- Feature 9 -->
                <div class="feature-card bg-white dark:bg-gray-800 rounded-2xl p-10 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.8s">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Time Zone Support</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Multi-timezone support with user-specific timezone preferences.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="bg-gray-50 dark:bg-gray-800 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    How It Works
                </h2>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                    Get started in minutes with our simple, intuitive workflow
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-6xl mx-auto">
                <!-- Step 1 -->
                <div class="step-card scroll-reveal">
                    <div class="step-number">1</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sign Up & Login</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Create your account or login with your credentials. Get instant access to the dashboard with role-based permissions.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card scroll-reveal" style="transition-delay: 0.2s">
                    <div class="step-number">2</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Configure Settings</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Set up your preferences, timezone, and notification settings. Customize your dashboard to match your workflow.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card scroll-reveal" style="transition-delay: 0.4s">
                    <div class="step-number">3</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Start Managing Events</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Browse events, manage markets, track real-time data, and export reports. Everything you need is at your fingertips.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bg-white dark:bg-gray-900 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    Why Choose Us?
                </h2>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                    Built for professionals who demand reliability and performance
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-6xl mx-auto">
                <div class="flex gap-6 scroll-reveal">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Lightning Fast Performance</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                            Optimized database queries and efficient caching ensure your dashboard loads instantly, even with thousands of events.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6 scroll-reveal" style="transition-delay: 0.1s">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Enterprise Security</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                            Your data is protected with industry-standard security practices, role-based access control, and regular security updates.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6 scroll-reveal" style="transition-delay: 0.2s">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Fully Customizable</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                            White-label solution that can be customized to match your brand. Configure labels, settings, and workflows to fit your needs.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6 scroll-reveal" style="transition-delay: 0.3s">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 dark:from-yellow-600 dark:to-yellow-700 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">24/7 Support</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                            Get help when you need it with our responsive support team. Comprehensive documentation and quick response times.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="bg-gray-50 dark:bg-gray-800 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    Perfect For
                </h2>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                    Whether you're a small team or a large enterprise
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sports Betting Operators</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Manage sports events, track markets, and monitor real-time odds for betting operations.
                    </p>
                </div>

                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.1s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Event Management Companies</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Track and manage multiple events simultaneously with comprehensive status monitoring.
                    </p>
                </div>

                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.2s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Data Analysts</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Analyze market trends, export data, and generate reports for business intelligence.
                    </p>
                </div>

                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.3s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Risk Managers</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Monitor risk markets, set limits, and manage interruptions with real-time alerts.
                    </p>
                </div>

                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.4s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Trading Platforms</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Integrate real-time market data and event information into trading systems.
                    </p>
                </div>

                <div class="use-case-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.5s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sports Organizations</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Track tournament schedules, manage match data, and monitor event status.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-white dark:bg-gray-900 section-padding">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    Frequently Asked Questions
                </h2>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-light">
                    Everything you need to know about our platform
                </p>
            </div>

            <div class="space-y-6">
                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">What is this platform used for?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Our platform is designed for managing sports events, tracking markets, monitoring real-time data, and handling risk management. It's perfect for sports betting operators, event management companies, and data analysts.
                    </p>
                </div>

                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.1s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Do you offer a free trial?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Yes! You can sign up for a free trial to explore all features. Contact our sales team to learn more about trial duration and features included.
                    </p>
                </div>

                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.2s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Can I customize the platform to match my brand?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Absolutely! Our white-label solution allows you to customize the platform with your branding, configure labels, settings, and workflows to match your business needs.
                    </p>
                </div>

                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.3s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">What kind of support do you provide?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        We offer 24/7 support through multiple channels, comprehensive documentation, and a dedicated support team to help you get the most out of the platform.
                    </p>
                </div>

                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.4s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Is my data secure?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Yes, security is our top priority. We use enterprise-grade security practices, role-based access control, encrypted data transmission, and regular security audits to protect your information.
                    </p>
                </div>

                <div class="faq-card bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg scroll-reveal" style="transition-delay: 0.5s">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Can I export my data?</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        Yes, you can export your data in CSV format with advanced filtering options. This allows you to create custom reports and perform offline analysis as needed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-700 dark:to-purple-700 section-padding relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="scroll-reveal">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6">
                    Ready to Get Started?
                </h2>
                <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto font-light">
                    Join thousands of professionals who trust our platform for their event management needs. Start your free trial today.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="{{ route('login') }}" 
                       class="group bg-white text-primary-600 px-10 py-5 rounded-xl hover:bg-gray-50 transition-all font-bold text-lg shadow-2xl hover:shadow-white/20 transform hover:scale-110 inline-flex items-center">
                        Start Free Trial
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="#features" 
                       class="bg-white/10 backdrop-blur-md text-white px-10 py-5 rounded-xl border-2 border-white/30 hover:border-white/50 hover:bg-white/20 transition-all font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-110 inline-flex items-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Scroll Reveal Animation
    (function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all scroll-reveal elements
        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });
    })();

    // Animated Number Counter
    (function() {
        const animateCounter = (element) => {
            const target = parseFloat(element.getAttribute('data-target'));
            const suffix = element.getAttribute('data-suffix') || '';
            const duration = 2000; // 2 seconds
            const start = 0;
            const increment = target / (duration / 16); // 60fps
            let current = start;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    if (suffix === '%') {
                        element.textContent = current.toFixed(1) + suffix;
                    } else if (suffix.includes('K+')) {
                        element.textContent = Math.floor(current / 1000) + suffix;
                    } else {
                        element.textContent = Math.floor(current) + suffix;
                    }
                    requestAnimationFrame(updateCounter);
                } else {
                    if (suffix === '%') {
                        element.textContent = target.toFixed(1) + suffix;
                    } else if (suffix.includes('K+')) {
                        element.textContent = Math.floor(target / 1000) + suffix;
                    } else {
                        element.textContent = Math.floor(target) + suffix;
                    }
                }
            };

            updateCounter();
        };

        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number[data-target]');
                    if (statNumber && !statNumber.classList.contains('animated')) {
                        statNumber.classList.add('animated');
                        animateCounter(statNumber);
                    }
                    statObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-card').forEach(card => {
            statObserver.observe(card);
        });
    })();

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });



</script>
@endpush
