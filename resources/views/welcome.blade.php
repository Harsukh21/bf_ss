@extends('layouts.public')

@section('title', 'Welcome')

@section('content')
    <!-- Simple Hero Section -->
    <section class="min-h-screen flex items-center justify-center bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Logo/Icon -->
            <div class="flex justify-center mb-8">
                <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-purple-600 rounded-3xl flex items-center justify-center shadow-2xl transform hover:rotate-12 transition-transform duration-300">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6">
                Welcome to
                <span class="gradient-text block mt-2">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </h1>

            <!-- Description -->
            <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-2xl mx-auto">
                Modern web application built with Laravel & Tailwind CSS
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-12">
                <a href="{{ route('login') }}" 
                   class="group bg-gradient-to-r from-primary-600 to-purple-600 text-white px-10 py-4 rounded-full hover:from-primary-700 hover:to-purple-700 transition-all font-bold text-lg transform hover:scale-105 shadow-xl">
                    <span class="flex items-center justify-center">
                        Login
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </a>
                
                <a href="#about" 
                   class="border-2 border-gray-300 text-gray-700 px-10 py-4 rounded-full hover:bg-white hover:border-primary-600 hover:text-primary-600 transition-all font-bold text-lg">
                    Learn More
                </a>
            </div>

            <!-- Simple Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-20">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-200">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast & Secure</h3>
                    <p class="text-gray-600 text-sm">Built with modern security practices</p>
                </div>
                
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-200">
                    <div class="text-4xl mb-4">🎨</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Beautiful Design</h3>
                    <p class="text-gray-600 text-sm">Clean & modern user interface</p>
                </div>
                
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-gray-200">
                    <div class="text-4xl mb-4">⚡</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">High Performance</h3>
                    <p class="text-gray-600 text-sm">Optimized for speed & efficiency</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section (Hidden, revealed on scroll) -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">
                Built with Modern Technology
            </h2>
            <p class="text-xl text-gray-600 mb-12">
                Powered by Laravel, PostgreSQL, and Tailwind CSS for a robust and scalable web application.
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <span class="px-6 py-3 bg-red-100 text-red-700 rounded-full font-semibold">Laravel</span>
                <span class="px-6 py-3 bg-blue-100 text-blue-700 rounded-full font-semibold">PostgreSQL</span>
                <span class="px-6 py-3 bg-cyan-100 text-cyan-700 rounded-full font-semibold">Tailwind CSS</span>
                <span class="px-6 py-3 bg-purple-100 text-purple-700 rounded-full font-semibold">PHP 8.3+</span>
            </div>
        </div>
    </section>
@endsection
