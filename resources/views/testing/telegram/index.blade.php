@extends('layouts.app')

@section('title', 'Telegram Test')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Telegram Test</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Send test Telegram messages to any user ID</p>
        </div>

        <!-- Test Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form id="telegramTestForm" class="space-y-6">
                    @csrf

                    <!-- Telegram ID -->
                    <div>
                        <label for="telegram_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Telegram ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="telegram_id" 
                               name="telegram_id" 
                               value="{{ old('telegram_id') }}"
                               placeholder="@username or -123456789"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('telegram_id') border-red-500 @enderror"
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Enter Telegram username (e.g., @username) or numeric chat ID (e.g., -123456789)
                        </p>
                        @error('telegram_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="8"
                                  placeholder="Enter your test message here..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('message') border-red-500 @enderror"
                                  required>{{ old('message') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            You can use HTML formatting (e.g., &lt;b&gt;bold&lt;/b&gt;, &lt;i&gt;italic&lt;/i&gt;)
                        </p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Response Message -->
                    <div id="responseMessage" class="hidden">
                        <div id="successMessage" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p id="successText" class="text-sm text-green-800 dark:text-green-200"></p>
                            </div>
                        </div>
                        <div id="errorMessage" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200 mb-1">Error sending message:</p>
                                    <p id="errorText" class="text-sm text-red-700 dark:text-red-300 whitespace-pre-line"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" 
                                onclick="resetForm()" 
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Reset
                        </button>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="submitBtnText">Send Test Message</span>
                            <span id="submitBtnLoader" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Examples Card -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Examples</h2>
                <div class="space-y-3">
                    <button type="button" 
                            onclick="fillExample('simple')" 
                            class="w-full text-left px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white">Simple Text Message</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hello! This is a test message from Laravel.</div>
                    </button>
                    <button type="button" 
                            onclick="fillExample('formatted')" 
                            class="w-full text-left px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white">Formatted Message (HTML)</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">&lt;b&gt;Bold&lt;/b&gt; and &lt;i&gt;italic&lt;/i&gt; text example</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const form = document.getElementById('telegramTestForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnLoader = document.getElementById('submitBtnLoader');
    const responseMessage = document.getElementById('responseMessage');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const successText = document.getElementById('successText');
    const errorText = document.getElementById('errorText');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Hide previous messages
        responseMessage.classList.add('hidden');
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtnText.classList.add('hidden');
        submitBtnLoader.classList.remove('hidden');

        const formData = new FormData(form);
        const telegramId = formData.get('telegram_id');
        const message = formData.get('message');

        try {
            const response = await fetch('{{ route("testing.telegram.send") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            responseMessage.classList.remove('hidden');

            if (data.success) {
                successText.textContent = data.message;
                successMessage.classList.remove('hidden');
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                // Handle multi-line error messages
                errorText.textContent = data.message || 'Failed to send message';
                errorMessage.classList.remove('hidden');
                // Scroll to error message
                errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } catch (error) {
            responseMessage.classList.remove('hidden');
            errorText.textContent = 'An error occurred: ' + error.message;
            errorMessage.classList.remove('hidden');
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtnText.classList.remove('hidden');
            submitBtnLoader.classList.add('hidden');
        }
    });

    function resetForm() {
        form.reset();
        responseMessage.classList.add('hidden');
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
    }

    function fillExample(type) {
        const telegramIdInput = document.getElementById('telegram_id');
        const messageInput = document.getElementById('message');

        if (type === 'simple') {
            telegramIdInput.value = '@harsukh21';
            messageInput.value = 'Hello! This is a test message from Laravel.\n\nIf you received this, your Telegram integration is working correctly! ✅';
        } else if (type === 'formatted') {
            telegramIdInput.value = '@harsukh21';
            messageInput.value = '<b>Bold Text</b>\n<i>Italic Text</i>\n<u>Underlined Text</u>\n\nThis is a <b>formatted</b> test message!';
        }

        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>
@endpush
@endsection

