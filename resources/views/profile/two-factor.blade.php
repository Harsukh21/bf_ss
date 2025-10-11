@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Secure your account with 2FA</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Add an extra layer of security to your account</p>
        </div>
        <a href="{{ route('profile.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Profile
        </a>
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

    <div class="max-w-2xl mx-auto">
        @if($user->two_factor_confirmed_at)
            <!-- 2FA Enabled -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Two-Factor Authentication</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Your account is protected with 2FA</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Enabled
                    </span>
                </div>

                <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">2FA is enabled</h3>
                            <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                <p>Your account is now protected with two-factor authentication. You'll need to enter a verification code from your authenticator app each time you log in.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recovery Codes -->
                @if(session('recovery_codes'))
                <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Save your recovery codes</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>These recovery codes can be used to access your account if you lose your authenticator device. Store them in a safe place.</p>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    @foreach(session('recovery_codes') as $code)
                                    <div class="font-mono text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-100">{{ $code }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Disable 2FA -->
                <form action="{{ route('profile.two-factor.disable') }}" method="POST" class="space-y-4" onsubmit="return confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.')">
                    @csrf
                    
                    <div>
                        <label for="disable_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <input type="password" id="disable_password" name="password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Disable 2FA
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- 2FA Setup -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Set Up Two-Factor Authentication</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Secure your account with 2FA</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Disabled
                    </span>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">How it works</h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Install an authenticator app like Google Authenticator or Authy</li>
                                    <li>Scan the QR code below with your authenticator app</li>
                                    <li>Enter the verification code to complete setup</li>
                                    <li>Save your recovery codes in a safe place</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                @if($qrCodeUrl)
                <!-- QR Code -->
                <div class="text-center mb-6">
                    <div class="inline-block p-4 bg-white dark:bg-white rounded-lg border border-gray-200 dark:border-gray-300 shadow-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-700 mb-2 font-medium">Scan with your authenticator app:</div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="mx-auto">
                    </div>
                </div>

                <!-- Manual Entry -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Can't scan? Enter this code manually:</h4>
                    <div class="font-mono text-sm bg-white dark:bg-gray-800 px-3 py-2 rounded border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-100 break-all">{{ $user->two_factor_secret }}</div>
                </div>
                @endif

                <!-- Enable 2FA -->
                <form action="{{ route('profile.two-factor.enable') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter Verification Code</label>
                        <input type="text" id="verification_code" name="code" required maxlength="6" pattern="[0-9]{6}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-center text-lg font-mono" placeholder="000000">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the 6-digit code from your authenticator app</p>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Enable 2FA
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Recommended Apps -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recommended Authenticator Apps</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="text-2xl mb-2">📱</div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Google Authenticator</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Free, secure, and easy to use</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="text-2xl mb-2">🔐</div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Authy</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Cloud backup and multi-device sync</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="text-2xl mb-2">🛡️</div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Microsoft Authenticator</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Integrated with Microsoft services</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
