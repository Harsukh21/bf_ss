@extends('layouts.public')

@section('title', 'Welcome')

@push('css')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .feature-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .section-padding {
        padding: 80px 0;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .step-card {
        position: relative;
        padding-left: 3rem;
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
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-50 to-gray-100 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <a href="/" class="flex items-center justify-center">
                        <img src="{{ asset('assets/img/light_logo.png') }}" 
                             alt="{{ config('app.name', 'Laravel') }}" 
                             class="h-20 md:h-28 w-auto object-contain">
                    </a>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                    Powerful Event Management
                    <span class="gradient-text block mt-3">
                        Platform
                    </span>
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Manage sports events, markets, and real-time data with an intuitive, feature-rich dashboard designed for professionals.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('login') }}" 
                       class="bg-gradient-to-r from-primary-600 to-purple-600 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-purple-700 transition-all font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center">
                        Get Started
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="#features" 
                       class="bg-white text-gray-700 px-8 py-4 rounded-lg border-2 border-gray-300 hover:border-primary-600 hover:text-primary-600 transition-all font-semibold text-lg shadow-md hover:shadow-lg inline-flex items-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-white border-y border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="stat-number">10K+</div>
                    <div class="text-gray-600 mt-2 font-medium">Events Managed</div>
                </div>
                <div>
                    <div class="stat-number">5K+</div>
                    <div class="text-gray-600 mt-2 font-medium">Active Users</div>
                </div>
                <div>
                    <div class="stat-number">99.9%</div>
                    <div class="text-gray-600 mt-2 font-medium">Uptime</div>
                </div>
                <div>
                    <div class="stat-number">24/7</div>
                    <div class="text-gray-600 mt-2 font-medium">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Everything You Need to Manage Events
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    A comprehensive solution for event tracking, market analysis, and real-time data management.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Event Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Comprehensive event tracking with real-time updates, status management, and detailed event information. Track events from creation to completion with full visibility.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Market Analytics</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Advanced market data analysis with live rates, trend tracking, and comprehensive reporting tools. Make data-driven decisions with powerful insights.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Real-Time Updates</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Get instant notifications and updates on events, market changes, and important alerts via Telegram and in-app notifications.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Access</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Enterprise-grade security with role-based access control and user permission management. Protect your data with granular access controls.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Data Export</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Export your data easily in CSV format with advanced filtering and customization options. Generate reports for analysis and record-keeping.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fast & Reliable</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Built for performance with optimized queries and responsive design for all devices. Lightning-fast load times and 99.9% uptime guarantee.
                    </p>
                </div>

                <!-- Feature 7 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Scorecard Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Track in-play events with comprehensive scorecard features, labels, and interruption management. Monitor live events with real-time status updates.
                    </p>
                </div>

                <!-- Feature 8 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Risk Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Advanced risk market tracking with filters, labels, and status management. Monitor and manage high-risk markets efficiently.
                    </p>
                </div>

                <!-- Feature 9 -->
                <div class="feature-card bg-white rounded-xl p-8 border border-gray-200 shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Time Zone Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Multi-timezone support with user-specific timezone preferences. View all times in your local timezone while maintaining accurate database records.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="bg-gray-50 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    How It Works
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Get started in minutes with our simple, intuitive workflow
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sign Up & Login</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Create your account or login with your credentials. Get instant access to the dashboard with role-based permissions.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Configure Settings</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Set up your preferences, timezone, and notification settings. Customize your dashboard to match your workflow.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Start Managing Events</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Browse events, manage markets, track real-time data, and export reports. Everything you need is at your fingertips.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Why Choose Our Platform?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Built for professionals who demand reliability and performance
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Lightning Fast Performance</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Optimized database queries and efficient caching ensure your dashboard loads instantly, even with thousands of events.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Enterprise Security</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Your data is protected with industry-standard security practices, role-based access control, and regular security updates.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Fully Customizable</h3>
                        <p class="text-gray-600 leading-relaxed">
                            White-label solution that can be customized to match your brand. Configure labels, settings, and workflows to fit your needs.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Get help when you need it with our responsive support team. Comprehensive documentation and quick response times.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="bg-gray-50 section-padding">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Perfect For
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Whether you're a small team or a large enterprise
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Sports Betting Operators</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Manage sports events, track markets, and monitor real-time odds for betting operations.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Event Management Companies</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Track and manage multiple events simultaneously with comprehensive status monitoring.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Data Analysts</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Analyze market trends, export data, and generate reports for business intelligence.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Risk Managers</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Monitor risk markets, set limits, and manage interruptions with real-time alerts.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Trading Platforms</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Integrate real-time market data and event information into trading systems.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Sports Organizations</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Track tournament schedules, manage match data, and monitor event status.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-white section-padding">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Frequently Asked Questions
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Everything you need to know about our platform
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">What is this platform used for?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Our platform is designed for managing sports events, tracking markets, monitoring real-time data, and handling risk management. It's perfect for sports betting operators, event management companies, and data analysts.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Do you offer a free trial?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes! You can sign up for a free trial to explore all features. Contact our sales team to learn more about trial duration and features included.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Can I customize the platform to match my brand?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Absolutely! Our white-label solution allows you to customize the platform with your branding, configure labels, settings, and workflows to match your business needs.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">What kind of support do you provide?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        We offer 24/7 support through multiple channels, comprehensive documentation, and a dedicated support team to help you get the most out of the platform.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Is my data secure?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes, security is our top priority. We use enterprise-grade security practices, role-based access control, encrypted data transmission, and regular security audits to protect your information.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Can I export my data?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Yes, you can export your data in CSV format with advanced filtering options. This allows you to create custom reports and perform offline analysis as needed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-primary-600 to-purple-600 section-padding">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Join thousands of professionals who trust our platform for their event management needs. Start your free trial today.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('login') }}" 
                   class="bg-white text-primary-600 px-8 py-4 rounded-lg hover:bg-gray-50 transition-all font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center">
                    Start Free Trial
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="#features" 
                   class="bg-transparent text-white px-8 py-4 rounded-lg border-2 border-white hover:bg-white hover:text-primary-600 transition-all font-semibold text-lg">
                    Learn More
                </a>
            </div>
        </div>
    </section>
@endsection
