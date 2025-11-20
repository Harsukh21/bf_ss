@extends('layouts.app')

@section('title', 'Edit Setting')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Setting</h1>
                <a href="{{ route('settings.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Back
                </a>
            </div>

            <form method="POST" action="{{ route('settings.update', $setting) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="send_freeze_telegram" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Send Freeze Telegram
                    </label>
                    <div class="flex items-center">
                        <input type="hidden" name="send_freeze_telegram" value="0">
                        <input type="checkbox" 
                               id="send_freeze_telegram" 
                               name="send_freeze_telegram" 
                               value="1"
                               {{ old('send_freeze_telegram', $setting->send_freeze_telegram) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 @error('send_freeze_telegram') border-red-500 @enderror">
                        <label for="send_freeze_telegram" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                            Enable sending freeze notifications to Telegram
                        </label>
                    </div>
                    @error('send_freeze_telegram')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        When enabled, freeze notifications will be sent to Telegram.
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-2">
                    <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Update Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

