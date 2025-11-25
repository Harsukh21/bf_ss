@extends('layouts.auth')

@section('title', 'Login')
@section('heading', 'Welcome Back')
@section('subheading', 'Sign in to your account to continue')


@section('content')
    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg transition-all duration-300">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">
                        {{ $errors->first() }}
                    </p>
                </div>
                <button type="button" class="alert-close flex-shrink-0 text-red-400 hover:text-red-600" onclick="this.closest('.alert').remove()">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg transition-all duration-300">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">
                        {{ session('success') }}
                    </p>
                </div>
                <button type="button" class="alert-close flex-shrink-0 text-green-400 hover:text-green-600" onclick="this.closest('.alert').remove()">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Professional Login Method Toggle Styles -->
    <style>
        /* Professional Login Method Toggle Styles - Modern Segmented Control */
        .login-method-toggle {
            min-width: 300px;
            background: #f3f4f6;
            border-radius: 12px;
            padding: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), inset 0 1px 2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .login-method-toggle #toggleIndicator {
            position: absolute;
            top: 4px;
            bottom: 4px;
            left: 4px;
            width: calc(50% - 4px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3), 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
        }
        
        .login-method-toggle #toggleIndicator.translate-right {
            transform: translateX(100%);
        }
        
        .login-method-toggle label {
            position: relative;
            z-index: 1;
            flex: 1;
            min-width: 140px;
            padding: 10px 20px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            -webkit-user-select: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .login-method-toggle label:not(.active) {
            color: #6b7280;
        }
        
        .login-method-toggle label:not(.active):hover {
            color: #374151;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .login-method-toggle label.active {
            color: #ffffff;
        }
        
        .login-method-toggle label svg {
            width: 18px;
            height: 18px;
            stroke-width: 2.5;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .login-method-toggle label.active svg {
            transform: scale(1.1);
        }
        
        .login-method-toggle label span {
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        /* Smooth field transitions - Fixed container to prevent layout shifts */
        .auth-field-container {
            position: relative;
            min-height: 100px;
            overflow: hidden;
        }
        
        #emailField,
        #usernameField,
        #passwordField,
        #webPinField {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            opacity: 0;
            visibility: hidden;
            transform: translateY(15px) scale(0.98);
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                        visibility 0s linear 0.4s;
            pointer-events: none;
            z-index: 1;
            will-change: opacity, transform;
        }
        
        #emailField.active,
        #usernameField.active,
        #passwordField.active,
        #webPinField.active {
            position: relative;
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1) 0.1s, 
                        transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) 0.1s,
                        visibility 0s linear 0s;
            z-index: 2;
        }
    </style>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
        @csrf
        
        <!-- Login Method Toggle - Professional Segmented Control -->
        <div class="relative flex justify-center mb-4">
            <div class="login-method-toggle relative inline-flex items-center" role="group">
                <input type="radio" name="login_method" value="password" id="loginMethodPassword" {{ old('login_method', 'password') === 'password' ? 'checked' : '' }} class="sr-only">
                <input type="radio" name="login_method" value="web_pin" id="loginMethodWebPin" {{ old('login_method') === 'web_pin' ? 'checked' : '' }} class="sr-only">
                
                <!-- Sliding Background Indicator -->
                <div id="toggleIndicator" class="{{ old('login_method', 'password') === 'web_pin' ? 'translate-right' : '' }}"></div>
                
                <!-- Password Option -->
                <label for="loginMethodPassword" class="{{ old('login_method', 'password') === 'password' ? 'active' : '' }}" id="passwordLabel">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Password</span>
                </label>
                
                <!-- Web PIN Option -->
                <label for="loginMethodWebPin" class="{{ old('login_method') === 'web_pin' ? 'active' : '' }}" id="webPinLabel">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <span>Web PIN</span>
                </label>
            </div>
        </div>
        
        <!-- Email/Username Field Container -->
        <div class="auth-field-container">
            <!-- Email Field (for Password login) -->
            <div id="emailField" class="{{ old('login_method', 'password') === 'password' ? 'active' : '' }}">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        autocomplete="email"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('email') border-red-500 @enderror"
                        placeholder="Enter your email">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username Field (for Web PIN login) -->
            <div id="usernameField" class="{{ old('login_method') === 'web_pin' ? 'active' : '' }}">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="{{ old('username') }}"
                        autocomplete="username"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('username') border-red-500 @enderror"
                        placeholder="Enter your username">
                </div>
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Auth Field Container - Prevents layout shifts -->
        <div class="auth-field-container">
            <!-- Password Field -->
            <div id="passwordField" class="{{ old('login_method', 'password') === 'password' ? 'active' : '' }}">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        autocomplete="current-password"
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('password') border-red-500 @enderror"
                        placeholder="Enter your password">
                    <button 
                        type="button" 
                        id="togglePassword"
                        data-target="password"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Web PIN Field -->
            <div id="webPinField" class="{{ old('login_method', 'password') === 'web_pin' ? 'active' : '' }}">
                <label for="web_pin" class="block text-sm font-medium text-gray-700 mb-2">
                    Web PIN
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <input 
                        type="password" 
                        name="web_pin" 
                        id="web_pin" 
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="20"
                        autocomplete="off"
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('web_pin') border-red-500 @enderror"
                        placeholder="Enter your 6-digit Web PIN">
                    <button 
                        type="button" 
                        id="toggleWebPin"
                        data-target="web_pin"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter your 6-digit Web PIN</p>
                @error('web_pin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <!-- Submit Button -->
        <div>
            <button 
                type="submit" 
                id="loginButton"
                onclick="
                    
                    // Show loading state immediately but don't disable button yet
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
                    
                    // Disable button after a short delay to allow form submission
                    setTimeout(function() {
                        const button = document.getElementById('loginButton');
                        if (button) {
                            button.disabled = true;
                            button.classList.add('opacity-75', 'cursor-not-allowed');
                        }
                    }, 100);
                    
                    // Allow form to submit normally
                    return true;
                "
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105">
                <svg id="loginSpinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg id="loginIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span id="loginText">Sign in</span>
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginMethodPassword = document.getElementById('loginMethodPassword');
            const loginMethodWebPin = document.getElementById('loginMethodWebPin');
            const emailField = document.getElementById('emailField');
            const usernameField = document.getElementById('usernameField');
            const passwordField = document.getElementById('passwordField');
            const webPinField = document.getElementById('webPinField');
            const emailInput = document.getElementById('email');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const webPinInput = document.getElementById('web_pin');
            const toggleIndicator = document.getElementById('toggleIndicator');
            const passwordLabel = document.getElementById('passwordLabel');
            const webPinLabel = document.getElementById('webPinLabel');
            
            // Toggle between password and web_pin fields with smooth animations
            function toggleLoginMethod() {
                if (loginMethodPassword.checked) {
                    // Move indicator to left (Password)
                    toggleIndicator.classList.remove('translate-right');
                    
                    // Update label classes
                    passwordLabel.classList.add('active');
                    webPinLabel.classList.remove('active');
                    
                    // Smooth field transitions - Hide Web PIN fields first
                    usernameField.classList.remove('active');
                    webPinField.classList.remove('active');
                    
                    // After fade out starts, fade in Password fields
                    setTimeout(() => {
                        emailField.classList.add('active');
                        passwordField.classList.add('active');
                    }, 100);
                    
                    // Update required attributes
                    emailInput.setAttribute('required', 'required');
                    passwordInput.setAttribute('required', 'required');
                    usernameInput.removeAttribute('required');
                    webPinInput.removeAttribute('required');
                    usernameInput.value = ''; // Clear username when switching
                    webPinInput.value = ''; // Clear web_pin when switching
                } else if (loginMethodWebPin.checked) {
                    // Move indicator to right (Web PIN)
                    toggleIndicator.classList.add('translate-right');
                    
                    // Update label classes
                    webPinLabel.classList.add('active');
                    passwordLabel.classList.remove('active');
                    
                    // Smooth field transitions - Hide Password fields first
                    emailField.classList.remove('active');
                    passwordField.classList.remove('active');
                    
                    // After fade out starts, fade in Web PIN fields
                    setTimeout(() => {
                        usernameField.classList.add('active');
                        webPinField.classList.add('active');
                    }, 100);
                    
                    // Update required attributes
                    usernameInput.setAttribute('required', 'required');
                    webPinInput.setAttribute('required', 'required');
                    emailInput.removeAttribute('required');
                    passwordInput.removeAttribute('required');
                    emailInput.value = ''; // Clear email when switching
                    passwordInput.value = ''; // Clear password when switching
                }
            }
            
            // Listen for changes
            loginMethodPassword.addEventListener('change', toggleLoginMethod);
            loginMethodWebPin.addEventListener('change', toggleLoginMethod);
            
            // Initial state - ensure correct fields are active on page load
            if (loginMethodPassword.checked) {
                emailField.classList.add('active');
                passwordField.classList.add('active');
                usernameField.classList.remove('active');
                webPinField.classList.remove('active');
            } else if (loginMethodWebPin.checked) {
                usernameField.classList.add('active');
                webPinField.classList.add('active');
                emailField.classList.remove('active');
                passwordField.classList.remove('active');
            }
            
            // Web PIN input validation - only numbers
            if (webPinInput) {
                webPinInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
            
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const target = document.getElementById(this.dataset.target);
                    if (target.type === 'password') {
                        target.type = 'text';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
                    } else {
                        target.type = 'password';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                    }
                });
            }
            
            // Toggle Web PIN visibility
            const toggleWebPin = document.getElementById('toggleWebPin');
            if (toggleWebPin) {
                toggleWebPin.addEventListener('click', function() {
                    const target = document.getElementById(this.dataset.target);
                    if (target.type === 'password') {
                        target.type = 'text';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
                    } else {
                        target.type = 'password';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                    }
                });
            }
        });
    </script>
    @endpush
@endsection


@section('bottom_text')
    <p class="text-white text-sm">
        &copy; 2025 {{ config('app.name', 'Laravel') }}. All rights reserved.
    </p>
@endsection


