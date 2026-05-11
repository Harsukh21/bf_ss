<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/notification.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('assets/img/notification.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/notification.svg') }}">

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
        <!-- Flower clusters: all 5 flowers layered on each other, centred on each corner -->
        <div class="absolute inset-0 overflow-hidden" aria-hidden="true">
            <!-- Cluster 1 – top-left corner -->
            <div class="fl-cluster" id="cl-tl">
                <img class="fl-c fl-blue_flower" src="{{ asset('assets/img/flowers/blue_flower.png') }}"  alt="">
                <img class="fl-c fl-blue_leaves"  src="{{ asset('assets/img/flowers/blue_leaves.png') }}" alt="">
                <img class="fl-c fl-blue_leaf"    src="{{ asset('assets/img/flowers/blue_leaf.png') }}"   alt="">
                <img class="fl-c fl-red_b"        src="{{ asset('assets/img/flowers/red_flower_b.png') }}" alt="">
                <img class="fl-c fl-red_a"        src="{{ asset('assets/img/flowers/red_flower_a.png') }}" alt="">
            </div>
            <!-- Cluster 2 – bottom-right corner -->
            <div class="fl-cluster" id="cl-br">
                <img class="fl-c fl-blue_flower" src="{{ asset('assets/img/flowers/blue_flower.png') }}"  alt="">
                <img class="fl-c fl-blue_leaves"  src="{{ asset('assets/img/flowers/blue_leaves.png') }}" alt="">
                <img class="fl-c fl-blue_leaf"    src="{{ asset('assets/img/flowers/blue_leaf.png') }}"   alt="">
                <img class="fl-c fl-red_b"        src="{{ asset('assets/img/flowers/red_flower_b.png') }}" alt="">
                <img class="fl-c fl-red_a"        src="{{ asset('assets/img/flowers/red_flower_a.png') }}" alt="">
            </div>
        </div>

        <!-- Auth Card -->
        <div class="relative max-w-md w-full">
            <div class="auth-card slide-in rounded-2xl shadow-2xl p-8">
                <!-- Logo/Brand -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <a href="/" class="flex items-center justify-center hover:opacity-80 transition-opacity duration-300 ease-in-out">
                            <!-- Light mode logo -->
                            <img src="{{ asset('assets/img/white_logo.svg') }}" 
                                 alt="{{ config('app.name', 'Laravel') }}" 
                                 class="h-20 w-auto max-w-full object-contain dark:hidden">
                            <!-- Dark mode logo -->
                            <img src="{{ asset('assets/img/dark_logo.svg') }}" 
                                 alt="{{ config('app.name', 'Laravel') }}" 
                                 class="h-20 w-auto max-w-full object-contain hidden dark:block">
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

    <style>
        /*
         * Each cluster is a fixed-size box positioned so ~25% hangs off the
         * corner (the remaining ~75% is visible).  All flower images are
         * placed at the box centre with  top:50%; left:50%  in CSS.
         * GSAP adds  xPercent:-50; yPercent:-50  so every image—regardless
         * of its own width—is centred on the SAME point (the box centre).
         * GSAP then owns all further transforms (scale, rotation, opacity).
         */
        .fl-cluster {
            position: absolute;
            width: 420px;
            height: 420px;
            pointer-events: none;
        }
        /* Hang ~25% off each corner so 75% is inside the viewport */
        #cl-tl { top:    -105px; left:  -105px; }
        #cl-br { bottom: -105px; right: -105px; }

        .fl-c {
            position: absolute;
            top:  50%;
            left: 50%;
            display: block;
        }
        /* Outer blue layers are biggest; red inner flowers are smallest */
        .fl-blue_leaf   { width: 380px; }
        .fl-blue_flower { width: 350px; }
        .fl-blue_leaves { width: 320px; }
        .fl-red_b       { width: 180px; }
        .fl-red_a       { width: 130px; }

        @media (max-width: 900px) {
            .fl-cluster     { width: 280px; height: 280px; }
            #cl-tl          { top: -70px;  left:  -70px;  }
            #cl-br          { bottom: -70px; right: -70px; }
            .fl-blue_leaf   { width: 250px; }
            .fl-blue_flower { width: 230px; }
            .fl-blue_leaves { width: 210px; }
            .fl-red_b       { width: 120px; }
            .fl-red_a       { width:  90px; }
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
            
            // Toggle password button event delegation - Removed to avoid conflicts with login page implementation
            
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

    <!-- GSAP for flower cluster animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        /*
         * Layer config: each flower class, its spin direction and speed.
         * All flowers share the same cluster centre — CSS puts them at
         * top:50%;left:50% and GSAP shifts them xPercent:-50,yPercent:-50
         * so every image (regardless of width) centres on the same point.
         */
        var layers = [
            { cls: '.fl-blue_leaf',   dir:  1, dur: 18 },
            { cls: '.fl-blue_flower', dir: -1, dur: 15 },
            { cls: '.fl-blue_leaves', dir:  1, dur: 12 },
            { cls: '.fl-red_b',       dir: -1, dur: 14 },
            { cls: '.fl-red_a',       dir:  1, dur: 10 },
        ];

        /* 1. Set initial state for every flower image */
        document.querySelectorAll('.fl-c').forEach(function (el) {
            gsap.set(el, {
                xPercent: -50,   /* centres on the CSS top:50%;left:50% anchor */
                yPercent: -50,
                scale: 0,
                opacity: 0,
                transformOrigin: '50% 50%'
            });
        });

        /* 2. Animate each cluster (both #cl-tl and #cl-br) */
        document.querySelectorAll('.fl-cluster').forEach(function (cluster, ci) {
            layers.forEach(function (layer, li) {
                var el = cluster.querySelector(layer.cls);
                if (!el) return;

                /* Pop-in with spring ease, staggered per cluster + per layer */
                gsap.to(el, {
                    scale: 1,
                    opacity: 0.94,
                    duration: 0.8,
                    delay: 0.15 + ci * 0.25 + li * 0.12,
                    ease: 'back.out(1.7)',
                    onComplete: function () {
                        gsap.to(el, {
                            rotation: layer.dir * 360,
                            repeat: -1,
                            ease: 'none',
                            duration: layer.dur
                        });
                    }
                });
            });
        });
    });
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

