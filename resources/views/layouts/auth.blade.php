<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Laravel') }}</title>

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
        }
    </script>

    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    
    <!-- Heroicons CDN removed - using inline SVG icons instead -->

    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        .auth-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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

        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('head')
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
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
                <div class="grid-pattern"></div>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="relative max-w-md w-full">
            <div class="auth-card slide-in rounded-2xl shadow-2xl p-8">
                <!-- Logo/Brand -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <a href="/" class="flex items-center justify-center hover:opacity-80 transition-opacity duration-300 ease-in-out">
                            <!-- Always show light logo -->
                            <img src="{{ asset('assets/img/light_logo.png') }}" 
                                 alt="{{ config('app.name', 'Laravel') }}" 
                                 class="h-20 w-auto max-w-full object-contain">
                        </a>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        @yield('heading', 'Welcome Back')
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        @yield('subheading', 'Sign in to your account to continue')
                    </p>
                </div>

                <!-- Main Content -->
                <div class="space-y-6">
                    @yield('content')
                </div>

                <!-- Footer Links -->
                <div class="mt-6 text-center">
                    @yield('footer')
                </div>
            </div>

            <!-- Additional Info Below Card -->
            <div class="mt-6 text-center">
                <p class="text-white text-sm">
                    @yield('bottom_text', '&copy; 2025 ' . config('app.name', 'Laravel') . '. All rights reserved.')
                </p>
            </div>
        </div>
    </div>

    <!-- Custom Animations -->
    <style>
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

    <!-- Common JavaScript - Temporarily disabled -->
    <!-- <script src="{{ asset('assets/js/app.js') }}"></script> -->
    
    <!-- Auth-specific JavaScript -->
    <script>
        // Prevent share-modal.js errors from blocking execution
        window.addEventListener('error', function(e) {
            if (e.filename && e.filename.includes('share-modal.js')) {
                console.warn('Ignoring share-modal.js error:', e.message);
                e.preventDefault();
                return true;
            }
        });
        
        try {
        // Toggle password functionality using event delegation
        function togglePasswordVisibility(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            
            if (!input || !button) {
                return;
            }
            
            if (input.type === 'password') {
                input.type = 'text';
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
            } else {
                input.type = 'password';
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
            }
        }

        // Global function for showing loading state
        window.showLoadingState = function() {
            const button = document.getElementById('loginButton');
            const spinner = document.getElementById('loginSpinner');
            const icon = document.getElementById('loginIcon');
            const text = document.getElementById('loginText');
            
            if (button) {
                button.disabled = true;
                button.classList.add('opacity-75', 'cursor-not-allowed');
                button.style.pointerEvents = 'none';
            }
            
            if (spinner) {
                spinner.classList.remove('hidden');
            }
            
            if (icon) {
                icon.classList.add('hidden');
            }
            
            if (text) {
                text.textContent = 'Signing in...';
            }
        }

        // Form handling for login page
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus email input if it exists
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
            
            // Backup form submission handler
            const loginFormBackup = document.getElementById('loginForm');
            if (loginFormBackup) {
                loginFormBackup.addEventListener('submit', function(e) {
                    // Don't prevent default - let form submit normally
                    
                    // Show loading state
                    const button = document.getElementById('loginButton');
                    const spinner = document.getElementById('loginSpinner');
                    const icon = document.getElementById('loginIcon');
                    const text = document.getElementById('loginText');
                    
                    if (spinner) {
                        spinner.classList.remove('hidden');
                    }
                    if (icon) {
                        icon.classList.add('hidden');
                    }
                    if (text) {
                        text.textContent = 'Signing in...';
                    }
                    
                    // Disable button after form starts submitting
                    setTimeout(function() {
                        if (button) {
                            button.disabled = true;
                            button.classList.add('opacity-75', 'cursor-not-allowed');
                        }
                    }, 100);
                });
            }
            
            // Debug: Check if elements exist
            const loginButton = document.getElementById('loginButton');
            const loginSpinner = document.getElementById('loginSpinner');
            const loginIcon = document.getElementById('loginIcon');
            const loginText = document.getElementById('loginText');
            
            // Element detection complete
            
            // Toggle password button event delegation
            const togglePasswordBtn = document.getElementById('togglePassword');
            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetInput = this.getAttribute('data-target');
                    const buttonId = this.getAttribute('id');
                    togglePasswordVisibility(targetInput, buttonId);
                });
            }
            
            // Form submission handling for login form
            const loginFormMain = document.getElementById('loginForm');
            if (loginFormMain) {
                // Function to reset button state
                function resetButtonState() {
                    const button = document.getElementById('loginButton');
                    const spinner = document.getElementById('loginSpinner');
                    const icon = document.getElementById('loginIcon');
                    const text = document.getElementById('loginText');
                    
                    if (button) {
                        button.disabled = false;
                        button.classList.remove('opacity-75', 'cursor-not-allowed');
                        button.style.pointerEvents = 'auto';
                    }
                    
                    if (spinner) {
                        spinner.classList.add('hidden');
                    }
                    
                    if (icon) {
                        icon.classList.remove('hidden');
                    }
                    
                    if (text) {
                        text.textContent = 'Sign in';
                    }
                }
                
                loginFormMain.addEventListener('submit', function(e) {
                    // Don't prevent default - let form submit normally
                    const button = document.getElementById('loginButton');
                    const spinner = document.getElementById('loginSpinner');
                    const icon = document.getElementById('loginIcon');
                    const text = document.getElementById('loginText');
                    
                    // Show loading state immediately
                    if (button) {
                        button.disabled = true;
                        button.classList.add('opacity-75', 'cursor-not-allowed');
                        button.style.pointerEvents = 'none';
                    }
                    
                    if (spinner) {
                        spinner.classList.remove('hidden');
                    }
                    
                    if (icon) {
                        icon.classList.add('hidden');
                    }
                    
                    if (text) {
                        text.textContent = 'Signing in...';
                    }
                    
                    // Reset button state after 10 seconds as fallback
                    setTimeout(function() {
                        if (button && button.disabled) {
                            resetButtonState();
                        }
                    }, 10000);
                });
                
                // Reset button state on page load (in case of back button)
                resetButtonState();
                
                // Also add click event to button as fallback
                const submitButton = document.getElementById('loginButton');
                if (submitButton) {
                    submitButton.addEventListener('click', function(e) {
                        // Small delay to allow form validation to complete
                        setTimeout(function() {
                            const button = document.getElementById('loginButton');
                            const spinner = document.getElementById('loginSpinner');
                            const icon = document.getElementById('loginIcon');
                            const text = document.getElementById('loginText');
                            
                            // Only show loading state if button is still enabled (form validation passed)
                            if (button && !button.disabled) {
                                if (button) {
                                    button.disabled = true;
                                    button.classList.add('opacity-75', 'cursor-not-allowed');
                                    button.style.pointerEvents = 'none';
                                }
                                
                                if (spinner) {
                                    spinner.classList.remove('hidden');
                                }
                                
                                if (icon) {
                                    icon.classList.add('hidden');
                                }
                                
                                if (text) {
                                    text.textContent = 'Signing in...';
                                }
                            }
                        }, 100);
                    });
                }
            }
            
            // Alert close functionality - simplified and robust
            setTimeout(function() {
                const alertCloseButtons = document.querySelectorAll('.alert-close');
                alertCloseButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const alert = this.closest('.alert');
                        if (alert) {
                            // Add smooth transition
                            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-10px)';
                            alert.style.pointerEvents = 'none';
                            
                            setTimeout(function() {
                                alert.remove();
                            }, 300);
                        }
                    });
                    
                    // Add hover effect for close button
                    button.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.1)';
                        this.style.transition = 'transform 0.2s ease';
                    });
                    
                    button.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            }, 100);
        });
        
        } catch (error) {
            console.warn('Auth JavaScript error:', error);
        }
    </script>

    @stack('scripts')
    
    <!-- Toast Notification System for Auth Pages -->
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2">
        <!-- Toast notifications will be dynamically inserted here -->
    </div>

    <script>
        // Toast Notification System for Auth Pages
        class ToastNotification {
            static show(message, type = 'info', duration = 5000) {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
                
                // Toast styles based on type
                const typeStyles = {
                    success: 'bg-green-500 border-green-600',
                    error: 'bg-red-500 border-red-600',
                    warning: 'bg-yellow-500 border-yellow-600',
                    info: 'bg-blue-500 border-blue-600'
                };
                
                const iconStyles = {
                    success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>`,
                    error: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>`,
                    warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>`,
                    info: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>`
                };

                toast.innerHTML = `
                    <div class="flex items-center p-4 rounded-lg shadow-lg border-l-4 ${typeStyles[type]} text-white max-w-md">
                        <div class="flex-shrink-0">
                            ${iconStyles[type]}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                }, 100);

                // Auto remove
                if (duration > 0) {
                    setTimeout(() => {
                        this.remove(toast);
                    }, duration);
                }

                return toast;
            }

            static remove(toast) {
                if (!toast) return;
                
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.parentElement.removeChild(toast);
                    }
                }, 300);
            }
        }

        // Show success/error messages as toast notifications
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                ToastNotification.show('{{ session('success') }}', 'success', 6000);
            @endif
            
            @if(session('error'))
                ToastNotification.show('{{ session('error') }}', 'error', 6000);
            @endif
            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    ToastNotification.show('{{ $error }}', 'error', 6000);
                @endforeach
            @endif
        });
    </script>
</body>
</html>

