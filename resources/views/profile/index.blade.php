@extends('layouts.app')

@section('title', 'Profile')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        min-height: 2.5rem;
    }
    .dark .select2-container--bootstrap-5 .select2-selection {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #111827;
        padding-left: 0.75rem;
    }
    .dark .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #f3f4f6;
    }
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #3b82f6;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .dark .select2-dropdown {
        background-color: #374151;
        border-color: #4b5563;
    }
    .select2-search__field {
        color: #111827;
    }
    .dark .select2-search__field {
        background-color: #374151;
        color: #f3f4f6;
    }
</style>
@endpush

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Profile Settings</h1>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Profile Information</h2>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Active
                        </span>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Email cannot be changed. Contact administrator if needed.</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                        <textarea id="bio" name="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                            <select id="timezone" 
                                    name="timezone" 
                                    class="timezone-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('timezone') border-red-500 @enderror">
                                <option value="">Select Timezone</option>
                                @php
                                    $timezones = config('timezones.timezones', []);
                                    $selectedTimezone = old('timezone', $user->timezone);
                                @endphp
                                @foreach($timezones as $tz => $data)
                                    <option value="{{ $tz }}" 
                                            data-flag="{{ $data['flag'] }}" 
                                            {{ $selectedTimezone == $tz ? 'selected' : '' }}>
                                        {{ $data['flag'] }} {{ $data['name'] }} ({{ $tz }})
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language</label>
                            <select id="language" name="language" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ $user->language === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ $user->language === 'fr' ? 'selected' : '' }}>French</option>
                                <option value="de" {{ $user->language === 'de' ? 'selected' : '' }}>German</option>
                                <option value="zh" {{ $user->language === 'zh' ? 'selected' : '' }}>Chinese</option>
                                <option value="ja" {{ $user->language === 'ja' ? 'selected' : '' }}>Japanese</option>
                            </select>
                            @error('language')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="web_pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Web Pin
                                @if($user->web_pin)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Set
                                    </span>
                                @endif
                            </label>
                            <input type="text" 
                                   id="web_pin" 
                                   name="web_pin" 
                                   value="{{ old('web_pin', '') }}"
                                   pattern="[0-9]*"
                                   inputmode="numeric"
                                   minlength="6"
                                   maxlength="20"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('web_pin') border-red-500 @enderror"
                                   placeholder="{{ $user->web_pin ? 'Enter new PIN to change (leave blank to keep current)' : 'Enter 6+ digit PIN' }}">
                            @error('web_pin')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($user->web_pin)
                                    Leave blank to keep your current PIN. Only numbers, minimum 6 digits.
                                @else
                                    Only numbers, minimum 6 digits
                                @endif
                            </p>
                        </div>

                        <div>
                            <label for="telegram_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Telegram ID
                                @if($user->telegram_chat_id)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified
                                    </span>
                                @elseif($user->telegram_id)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Unverified
                                    </span>
                                @endif
                            </label>
                            <input type="text" 
                                   id="telegram_id" 
                                   name="telegram_id" 
                                   value="{{ old('telegram_id', $user->telegram_id) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('telegram_id') border-red-500 @enderror"
                                   placeholder="@username or numeric ID (e.g., 123456789)">
                            @error('telegram_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            
                            @if(session('telegram_validation_error'))
                                <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-800 dark:text-yellow-200">{!! session('telegram_validation_error') !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Professional Telegram Setup Guide - Compact -->
                            <div class="mt-3 space-y-2.5">
                                @php
                                    $botUsername = config('services.telegram.bot_username');
                                    $botLink = $botUsername ? 'https://t.me/' . ltrim($botUsername, '@') : '#';
                                @endphp
                                
                                <!-- Instructions Card - Compact -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-lg border border-blue-200 dark:border-blue-800 shadow-sm overflow-hidden">
                                    <!-- Header - Compact -->
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-2 border-b border-blue-600 dark:border-blue-700">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-shrink-0 w-6 h-6 bg-white/20 rounded flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xs font-semibold text-white">How to Set Up Telegram Notifications</h3>
                                        </div>
                                    </div>
                                    
                                    <!-- Content - Compact -->
                                    <div class="p-3 space-y-2.5">
                                        <!-- Step 1 -->
                                        <div class="flex gap-2.5">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                                                    1
                                                </div>
                                            </div>
                                            <div class="flex-1 pt-0.5">
                                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100 mb-0.5">
                                                    Connect with our bot:
                                                </p>
                                                @if($botUsername)
                                                    <a href="{{ $botLink }}" 
                                                       target="_blank" 
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 shadow-sm hover:shadow">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.169 1.858-.896 6.728-.896 6.728-.464 2.207-1.153 2.618-1.935 2.681-.786.064-1.288-.415-2.003-1.012l-.897-.674c-.353.131-.692.261-1.031.39-.484.206-1.231.523-1.983.523-.417 0-.652-.074-1.061-.238l-.269-.122c-.512-.232-.679-.38-.679-.784 0-.339.26-.654.719-.962l.24-.16c.487-.324.776-.519 1.154-.817.682-.539 1.194-1.076 1.743-1.715l.012-.015c.484-.564 3.065-2.733 3.065-2.733.364-.339.072-.351-.21-.07l-3.09 3.071c-.17.167-.287.287-.468.287-.17 0-.26-.087-.26-.248l.048-1.664c.048-1.664.03-2.571.03-2.571 0-.277.151-.517.478-.517.26 0 .372.052.51.18l2.076 2.003c.137.137.191.248.191.37 0 .247-.1.417-.338.576l-.155.096c-.174.11-.438.28-.621.399l-.099.065c-.191.131-.338.232-.469.336z"/>
                                                        </svg>
                                                        <span>{{ $botUsername }}</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">@YourBotUsername</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Step 2 -->
                                        <div class="flex gap-2.5">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                                                    2
                                                </div>
                                            </div>
                                            <div class="flex-1 pt-0.5">
                                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                                    Click "Start" or send <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded text-xs font-mono">/start</code> to the bot
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 3 -->
                                        <div class="flex gap-2.5">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                                                    3
                                                </div>
                                            </div>
                                            <div class="flex-1 pt-0.5">
                                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                                    Enter your Telegram username (e.g., <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded text-xs font-mono">@username</code>) above
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 4 -->
                                        <div class="flex gap-2.5">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                                                    4
                                                </div>
                                            </div>
                                            <div class="flex-1 pt-0.5">
                                                <p class="text-xs text-gray-700 dark:text-gray-300 mb-1">
                                                    Click "Update Profile" - system will automatically:
                                                </p>
                                                <ul class="space-y-0.5 ml-3">
                                                    <li class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                                        <svg class="w-3 h-3 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Sync & verify your chat ID</span>
                                                    </li>
                                                    <li class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                                        <svg class="w-3 h-3 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Send verification message</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 5 -->
                                        <div class="flex gap-2.5">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                                                    ✓
                                                </div>
                                            </div>
                                            <div class="flex-1 pt-0.5">
                                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                                    Done! Telegram ID and Chat ID will be saved
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Important Note Card - Compact -->
                                <div class="bg-amber-50 dark:bg-amber-900/10 rounded-lg border-l-4 border-amber-400 dark:border-amber-600">
                                    <div class="p-2.5">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-shrink-0">
                                                <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-semibold text-amber-900 dark:text-amber-100 mb-1">Important:</p>
                                                <ul class="space-y-0.5 text-xs text-amber-800 dark:text-amber-200">
                                                    <li class="flex items-start gap-1.5">
                                                        <span class="text-amber-500 dark:text-amber-400 mt-0.5">•</span>
                                                        <span>Send a message to the bot first before entering your Telegram ID</span>
                                                    </li>
                                                    <li class="flex items-start gap-1.5">
                                                        <span class="text-amber-500 dark:text-amber-400 mt-0.5">•</span>
                                                        <span>System automatically syncs when you update your profile</span>
                                                    </li>
                                                    <li class="flex items-start gap-1.5">
                                                        <span class="text-amber-500 dark:text-amber-400 mt-0.5">•</span>
                                                        <span>Only verified Telegram IDs receive notifications</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Overview -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Account Overview</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            @if($user->email_verified_at)
                                <span class="text-green-600 dark:text-green-400">Verified</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Not Verified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">2FA Status</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            @if($user->two_factor_confirmed_at)
                                <span class="text-green-600 dark:text-green-400">Enabled</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Disabled</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('profile.security') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security Settings
                    </a>
                    
                    <a href="{{ route('profile.two-factor') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Two-Factor Auth
                    </a>
                </div>
            </div>

            <!-- Recent Login Activity -->
            @if(count($recentLogins) > 0)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Activity</h3>
                
                <div class="space-y-3">
                    @foreach($recentLogins as $login)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $login['location'] ?? 'Unknown Location' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($login['login_time'])->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('profile.security') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500">View all activity →</a>
                </div>
            </div>
            @endif
        </div>
    </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Web Pin validation - only allow numbers and minimum 6 digits
    document.addEventListener('DOMContentLoaded', function() {
        const webPinInput = document.getElementById('web_pin');
        
        if (webPinInput) {
            webPinInput.addEventListener('input', function(e) {
                // Remove any non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            webPinInput.addEventListener('blur', function(e) {
                // Validate minimum 6 digits if field has value
                if (this.value && this.value.length < 6) {
                    this.setCustomValidity('Web Pin must be at least 6 digits');
                    this.reportValidity();
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Initialize Select2 for timezone dropdown
        function formatTimezone(option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(option.element);
            var flag = $option.data('flag');
            var text = option.text;
            
            // Extract flag, name, and timezone from text
            var parts = text.match(/^([^\s]+)\s+(.+?)\s+\((.+)\)$/);
            if (parts) {
                var flag = parts[1];
                var name = parts[2];
                var tz = parts[3];
                return $('<span><span class="mr-2">' + flag + '</span>' + name + ' <span class="text-gray-500 text-xs">(' + tz + ')</span></span>');
            }
            return text;
        }

        $('#timezone').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select Timezone',
            allowClear: true,
            templateResult: formatTimezone,
            templateSelection: function(option) {
                if (!option.id) {
                    return option.text;
                }
                var $option = $(option.element);
                var flag = $option.data('flag');
                var text = option.text;
                var parts = text.match(/^([^\s]+)\s+(.+?)\s+\((.+)\)$/);
                if (parts) {
                    var flag = parts[1];
                    var name = parts[2];
                    return $('<span><span class="mr-2">' + flag + '</span>' + name + '</span>');
                }
                return text;
            },
            language: {
                noResults: function() {
                    return "No timezone found";
                },
                searching: function() {
                    return "Searching...";
                }
            }
        });
    });
</script>
@endpush
@endsection
