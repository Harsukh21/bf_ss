@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    Users
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">
                                        Create User
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Create New User</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new user to the system</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Create User Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Basic Information
                        </h3>
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            
                            <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name *</label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('name') border-red-500 @enderror" 
                                           required>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address *</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('email') border-red-500 @enderror" 
                                           required>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name *</label>
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('first_name') border-red-500 @enderror" 
                                           required>
                                    @error('first_name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name *</label>
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('last_name') border-red-500 @enderror" 
                                           required>
                                    @error('last_name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                    <input type="text" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                                    <input type="date" 
                                           id="date_of_birth" 
                                           name="date_of_birth" 
                                           value="{{ old('date_of_birth') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('date_of_birth') border-red-500 @enderror">
                                    @error('date_of_birth')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Password Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Password
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password *</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password') border-red-500 @enderror" 
                                       required>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Minimum 8 characters with at least one letter and one number
                                </p>
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password *</label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password_confirmation') border-red-500 @enderror" 
                                       required>
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Additional Information
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                                <select id="timezone" 
                                        name="timezone" 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('timezone') border-red-500 @enderror">
                                    <option value="">Select Timezone</option>
                                    <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                    <option value="Asia/Tokyo" {{ old('timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                    <option value="Asia/Shanghai" {{ old('timezone') == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai</option>
                                </select>
                                @error('timezone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Language</label>
                                <select id="language" 
                                        name="language" 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('language') border-red-500 @enderror">
                                    <option value="">Select Language</option>
                                    <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                                    <option value="it" {{ old('language') == 'it' ? 'selected' : '' }}>Italian</option>
                                    <option value="pt" {{ old('language') == 'pt' ? 'selected' : '' }}>Portuguese</option>
                                    <option value="ru" {{ old('language') == 'ru' ? 'selected' : '' }}>Russian</option>
                                    <option value="ja" {{ old('language') == 'ja' ? 'selected' : '' }}>Japanese</option>
                                    <option value="zh" {{ old('language') == 'zh' ? 'selected' : '' }}>Chinese</option>
                                </select>
                                @error('language')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                            <textarea id="bio" 
                                      name="bio" 
                                      rows="4"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('bio') border-red-500 @enderror"
                                      placeholder="Tell us about this user...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('users.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Create User
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- User Preview Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            User Preview
                        </h3>
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold">
                                ?
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                User details will appear here as you fill out the form
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-blue-900 dark:text-blue-100 mb-2">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tips
                        </h3>
                        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                            <li>• All fields marked with * are required</li>
                            <li>• Password must be at least 8 characters</li>
                            <li>• Email must be unique in the system</li>
                            <li>• Timezone affects user's display preferences</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
