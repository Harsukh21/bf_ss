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
    
    /* Beautiful gradient background from login page */
    .gradient-bg {
        background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }
    
    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    /* Content z-index */
    .content-relative {
        position: relative;
        z-index: 10;
    }
    
    /* Background animations z-index */
    .bg-animations {
        z-index: 1;
        pointer-events: none;
    }
    
    /* Ensure navigation and footer stay on top */
    .nav-z-index {
        z-index: 50;
    }
    
    .footer-z-index {
        z-index: 50;
    }
    
    /* Ensure main content doesn't overlap navigation */
    main {
        position: relative;
        z-index: 1;
    }
    
    /* Prevent background animations from interfering with navigation */
    .min-h-screen {
        margin-top: 0;
        padding-top: 0;
    }
    
    /* Pulse animation for logo */
    .logo-pulse {
        animation: logoPulse 3s ease-in-out infinite;
    }
    
    @keyframes logoPulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 35px 60px -12px rgba(59, 130, 246, 0.6);
        }
    }
    
    /* Text fade-in animation */
    .fade-in {
        opacity: 0;
        animation: fadeIn 1s ease-out forwards;
    }
    
    .fade-in:nth-child(1) {
        animation-delay: 0.2s;
    }
    
    .fade-in:nth-child(2) {
        animation-delay: 0.4s;
    }
    
    .fade-in:nth-child(3) {
        animation-delay: 0.6s;
    }
    
    .fade-in:nth-child(4) {
        animation-delay: 0.8s;
    }
    
        @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Button hover glow effect */
    .btn-glow {
        position: relative;
        overflow: hidden;
    }
    
    .btn-glow::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
        z-index: 1;
    }
    
    .btn-glow:hover::before {
        left: 100%;
    }
    
    .btn-glow span {
        position: relative;
        z-index: 2;
    }

    /* Slow pulse animation */
    .animate-pulse-slow {
        animation: pulse-slow 4s ease-in-out infinite;
    }

    @keyframes pulse-slow {
        0%, 100% {
            opacity: 0.2;
            transform: scale(1);
        }
        50% {
            opacity: 0.4;
            transform: scale(1.05);
        }
    }

    /* Slow bounce animation */
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }

    @keyframes bounce-slow {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    /* Floating animation */
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(12deg);
        }
        50% {
            transform: translateY(-15px) rotate(12deg);
        }
    }

    /* Delayed floating animation */
    .animate-float-delayed {
        animation: float-delayed 8s ease-in-out infinite;
        animation-delay: 2s;
    }

    @keyframes float-delayed {
        0%, 100% {
            transform: translateY(0px) rotate(-45deg);
        }
        50% {
            transform: translateY(-25px) rotate(-45deg);
        }
    }

    /* Slow spin animation */
    .animate-spin-slow {
        animation: spin-slow 20s linear infinite;
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Particle animations */
    .animate-float-particle-1 {
        animation: float-particle-1 12s ease-in-out infinite;
    }

    .animate-float-particle-2 {
        animation: float-particle-2 10s ease-in-out infinite;
        animation-delay: 1s;
    }

    .animate-float-particle-3 {
        animation: float-particle-3 14s ease-in-out infinite;
        animation-delay: 2s;
    }

    .animate-float-particle-4 {
        animation: float-particle-4 11s ease-in-out infinite;
        animation-delay: 3s;
    }

    @keyframes float-particle-1 {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
            opacity: 0.4;
        }
        25% {
            transform: translateY(-30px) translateX(10px);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-15px) translateX(-5px);
            opacity: 0.3;
        }
        75% {
            transform: translateY(-40px) translateX(15px);
            opacity: 0.5;
        }
    }

    @keyframes float-particle-2 {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
            opacity: 0.5;
        }
        33% {
            transform: translateY(-25px) translateX(-10px);
            opacity: 0.7;
        }
        66% {
            transform: translateY(-35px) translateX(5px);
            opacity: 0.3;
        }
    }

    @keyframes float-particle-3 {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
            opacity: 0.3;
        }
        20% {
            transform: translateY(-20px) translateX(8px);
            opacity: 0.5;
        }
        40% {
            transform: translateY(-40px) translateX(-12px);
            opacity: 0.4;
        }
        60% {
            transform: translateY(-25px) translateX(6px);
            opacity: 0.6;
        }
        80% {
            transform: translateY(-35px) translateX(-8px);
            opacity: 0.3;
        }
    }

    @keyframes float-particle-4 {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
            opacity: 0.4;
        }
        50% {
            transform: translateY(-30px) translateX(12px);
            opacity: 0.6;
        }
    }

    /* Grid pattern */
    .grid-pattern {
        background-image: 
            linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: grid-move 20s linear infinite;
    }

    @keyframes grid-move {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: 50px 50px;
        }
        }
    </style>
@endpush

@section('content')
    <!-- Beautiful Animated Hero Section -->
    <section class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden z-0 bg-animations">
            <!-- Animated Geometric Shapes -->
            <div class="absolute top-10 left-10 w-20 h-20 bg-white opacity-20 rounded-lg animate-pulse-slow transform rotate-45"></div>
            <div class="absolute top-32 right-20 w-16 h-16 bg-white opacity-15 rounded-full animate-bounce-slow"></div>
            <div class="absolute bottom-20 left-20 w-24 h-24 bg-white opacity-25 transform rotate-12 animate-float"></div>
            <div class="absolute bottom-32 right-10 w-12 h-12 bg-white opacity-20 rounded-lg animate-spin-slow"></div>
            <div class="absolute top-1/2 left-1/4 w-8 h-8 bg-white opacity-30 rounded-full animate-pulse"></div>
            <div class="absolute top-1/3 right-1/3 w-14 h-14 bg-white opacity-20 transform -rotate-45 animate-float-delayed"></div>
            
            <!-- Floating Particles -->
            <div class="absolute top-1/4 left-1/2 w-2 h-2 bg-white opacity-40 rounded-full animate-float-particle-1"></div>
            <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-white opacity-50 rounded-full animate-float-particle-2"></div>
            <div class="absolute top-1/2 right-1/2 w-3 h-3 bg-white opacity-30 rounded-full animate-float-particle-3"></div>
            <div class="absolute bottom-1/4 left-1/3 w-2 h-2 bg-white opacity-40 rounded-full animate-float-particle-4"></div>
            
            <!-- Grid Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="grid-pattern w-full h-full"></div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center content-relative">
            <!-- Logo/Icon -->
            <div class="flex justify-center mb-8 fade-in">
                <a href="/" class="flex items-center justify-center hover:opacity-90 transition-opacity duration-300 ease-in-out transform hover:scale-105">
                    <!-- Light mode logo -->
                    <img src="{{ asset('assets/img/light_logo.png') }}" 
                         alt="{{ config('app.name', 'Laravel') }}" 
                         class="h-28 md:h-32 w-auto max-w-full object-contain dark:hidden">
                    <!-- Dark mode logo -->
                    <img src="{{ asset('assets/img/dark_logo.png') }}" 
                         alt="{{ config('app.name', 'Laravel') }}" 
                         class="h-28 md:h-32 w-auto max-w-full object-contain hidden dark:block">
                </a>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 fade-in">
                    Welcome to
                <span class="gradient-text block mt-2">
                        {{ config('app.name', 'Laravel') }}
                    </span>
                </h1>

            <!-- Description -->
            <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-2xl mx-auto fade-in">
                Modern web application built with Laravel & Tailwind CSS
            </p>

            <!-- CTA Button -->
            <div class="flex justify-center mb-12 fade-in">
                <a href="{{ route('login') }}" 
                   class="group bg-gradient-to-r from-primary-600 to-purple-600 text-white px-12 py-4 rounded-full hover:from-primary-700 hover:to-purple-700 transition-all font-bold text-lg transform hover:scale-105 shadow-xl btn-glow cursor-pointer hover:cursor-pointer">
                    <span class="flex items-center justify-center relative z-10">
                        Get Started
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endsection
