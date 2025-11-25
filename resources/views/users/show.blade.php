@extends('layouts.app')

@section('title', 'User Details')

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
                                        User Details
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View detailed user information and activity</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('users.edit', $user) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit User
                    </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Profile Header Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-6">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $user->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $user->email }}</p>
                                <div class="flex items-center space-x-4">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactive
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Member since {{ $user->created_at->format('F j, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($user->bio)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Bio</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->bio }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Basic Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Basic Information
                        </h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Display Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($user->username)
                                        <span class="font-mono">{{ $user->username }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">First Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->first_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->last_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->date_of_birth?->format('F j, Y') ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Web Pin</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($user->web_pin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Set
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telegram ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($user->telegram_id)
                                        <span class="font-mono">{{ $user->telegram_id }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Not set</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <!-- Account Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Account Information
                        </h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                                <dd class="mt-1">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Not Verified
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('F j, Y g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('F j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <!-- Preferences Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Preferences
                        </h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Timezone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->timezone ?? 'Not set' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Language</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->language ?? 'Not set' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Roles & Permissions Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Roles & Permissions
                            </h3>
                            @php
                                $authorizedEmails = ['harsukh21@gmail.com', 'sam.parkinson7777@gmail.com'];
                                $isAuthorized = in_array(auth()->user()->email, $authorizedEmails);
                            @endphp
                            @if($isAuthorized)
                                <button onclick="openRolesModal()" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Roles Display -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Assigned Roles</h4>
                            @if($user->roles && $user->roles->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No roles assigned</p>
                            @endif
                        </div>

                        <!-- Permissions Display -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions (via Roles)</h4>
                            @php
                                $userPermissions = $user->permissions();
                            @endphp
                            @if($userPermissions && $userPermissions->count() > 0)
                                @if(isset($permissions) && $permissions->count() > 0)
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="mb-4">
                                            <h5 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">{{ $group ?? 'General' }}</h5>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($groupPermissions as $permission)
                                                    @php
                                                        $hasPermission = false;
                                                        foreach($user->roles as $role) {
                                                            if($role->permissions->contains($permission->id)) {
                                                                $hasPermission = true;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    @if($hasPermission)
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                            {{ $permission->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No permissions available</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No permissions assigned</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('users.edit', $user) }}" 
                               class="block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit User
                            </a>
                            @if($user->id !== auth()->id())
                                <button onclick="confirmDeleteUser({{ $user->id }}, {{ json_encode($user->name) }})" 
                                        class="block w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Account Status Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Account Status
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactive
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M j, Y') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Info Card -->
                @if($user->two_factor_confirmed_at)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Security
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Two-Factor Authentication</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Enabled
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Enabled On</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->two_factor_confirmed_at->format('M j, Y') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Roles Management Modal -->
@if(isset($roles) && isset($isAuthorized) && $isAuthorized)
<div id="rolesModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Manage Roles for {{ $user->name }}</h3>
            <button onclick="closeRolesModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="rolesForm" method="POST" action="{{ route('users.update-roles', $user) }}">
            @csrf
            @method('PATCH')
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($roles as $role)
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
                            @if($role->permissions && $role->permissions->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 dark:text-gray-400">
                                            +{{ $role->permissions->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
            
            <div class="mt-6 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeRolesModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Update Roles
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('js')
<script>
    function openRolesModal() {
        const modal = document.getElementById('rolesModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeRolesModal() {
        const modal = document.getElementById('rolesModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('rolesModal');
        if (modal && event.target === modal) {
            closeRolesModal();
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
</script>
@endpush
@endsection
