@extends('layouts.app')

@section('title', 'Edit User')

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
                                        Edit User
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user information and settings</p>
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

        <!-- Edit User Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Info Header Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold mr-4">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Member since {{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" id="editUserForm">
                    @csrf
                    @method('PUT')
            
                    <!-- Basic Information Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Basic Information
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name *</label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}"
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
                                           value="{{ old('email', $user->email) }}"
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
                                           value="{{ old('first_name', $user->first_name) }}"
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
                                           value="{{ old('last_name', $user->last_name) }}"
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
                                           value="{{ old('phone', $user->phone) }}"
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
                                           value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('date_of_birth') border-red-500 @enderror">
                                    @error('date_of_birth')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="web_pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('web_pin') border-red-500 @enderror"
                                           placeholder="{{ $user->web_pin ? 'Enter new PIN to change (leave blank to keep current)' : 'Enter 6+ digit PIN' }}">
                                    @error('web_pin')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        @if($user->web_pin)
                                            Leave blank to keep the current PIN. Only numbers, minimum 6 digits.
                                        @else
                                            Only numbers, minimum 6 digits
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="telegram_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telegram ID</label>
                                    <input type="text" 
                                           id="telegram_id" 
                                           name="telegram_id" 
                                           value="{{ old('telegram_id', $user->telegram_id) }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('telegram_id') border-red-500 @enderror"
                                           placeholder="Enter Telegram Chat ID">
                                    @error('telegram_id')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">For sending notifications</p>
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
                            
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Leave password fields blank to keep the current password unchanged.
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Minimum 8 characters with at least one letter and one number
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                                    <input type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password_confirmation') border-red-500 @enderror">
                                    @error('password_confirmation')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles & Permissions Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Roles & Permissions
                            </h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Assign Roles</label>
                                <div class="space-y-2">
                                    @forelse($roles as $role)
                                        <label class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="roles[]" 
                                                   value="{{ $role->id }}"
                                                   {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</div>
                                                @if($role->description)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No roles available. Please create roles first.</p>
                                    @endforelse
                                </div>
                                @error('roles')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
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
                                            class="timezone-select mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('timezone') border-red-500 @enderror">
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
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Language</label>
                                    <select id="language" 
                                            name="language" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('language') border-red-500 @enderror">
                                        <option value="">Select Language</option>
                                        <option value="en" {{ old('language', $user->language) == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="es" {{ old('language', $user->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                        <option value="fr" {{ old('language', $user->language) == 'fr' ? 'selected' : '' }}>French</option>
                                        <option value="de" {{ old('language', $user->language) == 'de' ? 'selected' : '' }}>German</option>
                                        <option value="it" {{ old('language', $user->language) == 'it' ? 'selected' : '' }}>Italian</option>
                                        <option value="pt" {{ old('language', $user->language) == 'pt' ? 'selected' : '' }}>Portuguese</option>
                                        <option value="ru" {{ old('language', $user->language) == 'ru' ? 'selected' : '' }}>Russian</option>
                                        <option value="ja" {{ old('language', $user->language) == 'ja' ? 'selected' : '' }}>Japanese</option>
                                        <option value="zh" {{ old('language', $user->language) == 'zh' ? 'selected' : '' }}>Chinese</option>
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
                                          placeholder="Tell us about this user...">{{ old('bio', $user->bio) }}</textarea>
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
                                    Update User
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- User Status Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            User Status
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Account Status</dt>
                                <dd class="mt-1">
                                    <select name="email_verified_at_status" form="editUserForm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                        <option value="inactive" {{ !$user->email_verified_at ? 'selected' : '' }}>Inactive</option>
                                        <option value="active" {{ $user->email_verified_at ? 'selected' : '' }}>Active</option>
                                    </select>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('M j, Y g:i A') }}
                                    @else
                                        Never
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $user->created_at->format('M j, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('users.show', $user) }}" 
                               class="block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                View User Details
                            </a>
                            @php
                                $authorizedEmails = ['harsukh21@gmail.com', 'sam.parkinson7777@gmail.com'];
                                $isAuthorized = in_array(auth()->user()->email, $authorizedEmails);
                                $protectedEmails = ['harsukh21@gmail.com', 'sam.parkinson7777@gmail.com'];
                            @endphp
                            @if($isAuthorized && $user->id !== auth()->id() && !in_array($user->email, $protectedEmails))
                                <button onclick="confirmDeleteUser({{ $user->id }}, {{ json_encode($user->name) }})" 
                                        class="block w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #fff;
    }
    .dark .select2-container--bootstrap-5 .select2-selection {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        line-height: 36px;
        color: #1f2937;
    }
    .dark .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #f9fafb;
    }
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #3b82f6;
        color: #fff;
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
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        background-color: #fff;
    }
    .dark .select2-search__field {
        background-color: #4b5563;
        border-color: #6b7280;
        color: #f9fafb;
    }
</style>
@endpush

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
    });

    function confirmDeleteUser(userId, userName) {
        try {
            // Validate parameters
            if (!userId || !userName) {
                alert('Error: Invalid user data');
                return;
            }
            
            // Create a custom confirmation modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Delete User</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete <strong>${userName}</strong>? This action cannot be undone.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button onclick="deleteUser(${userId})" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                            Yes, Delete User
                        </button>
                        <button onclick="closeModal()" 
                                class="mt-3 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
        } catch (error) {
            alert('Error creating delete confirmation dialog');
        }
    }

    function deleteUser(userId) {
        try {
            // Validate userId
            if (!userId) {
                alert('Error: Invalid user ID');
                return;
            }
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/users/${userId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        } catch (error) {
            console.error('Error in deleteUser:', error);
            alert('Error submitting delete request');
        }
    }

    function closeModal() {
        const modal = document.querySelector('.fixed.inset-0');
        if (modal) {
            modal.remove();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
            closeModal();
        }
    });

    // Initialize Select2 for timezone dropdown
    $(document).ready(function() {
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
