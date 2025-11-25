@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create User</h1>
                <a href="{{ route('users.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Back
                </a>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('username') border-red-500 @enderror" placeholder="Enter username (optional)">
                    @error('username')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Username can only contain letters, numbers, and underscores.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password *</label>
                        <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('password_confirmation') border-red-500 @enderror">
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="web_pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Web Pin</label>
                        <input type="text" 
                               id="web_pin" 
                               name="web_pin" 
                               value="{{ old('web_pin') }}"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               minlength="6"
                               maxlength="20"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('web_pin') border-red-500 @enderror"
                               placeholder="Enter 6+ digit PIN">
                        @error('web_pin')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Only numbers, minimum 6 digits</p>
                    </div>
                    <div>
                        <label for="telegram_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telegram ID</label>
                        <input type="text" 
                               id="telegram_id" 
                               name="telegram_id" 
                               value="{{ old('telegram_id') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('telegram_id') border-red-500 @enderror"
                               placeholder="Enter Telegram Chat ID">
                        @error('telegram_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">For sending notifications</p>
                    </div>
                </div>

                @if(isset($roles) && $roles->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Assign Roles</label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                            <label class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->id }}"
                                       {{ old('roles') && in_array($role->id, old('roles')) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</div>
                                    @if($role->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="flex items-center justify-end space-x-4 pt-2">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
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
</script>
@endpush
@endsection
