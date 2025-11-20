@extends('layouts.app')

@section('title', 'Edit Notification')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Notification</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Update notification details and settings</p>
                </div>
                <a href="{{ route('notifications.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form action="{{ route('notifications.update', $notification->id) }}" method="POST" id="notificationForm" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $notification->title) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                           placeholder="Enter notification title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                    <textarea id="message" 
                              name="message" 
                              rows="4"
                              required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('message') border-red-500 @enderror"
                              placeholder="Enter notification message">{{ old('message', $notification->message) }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Users Selection -->
                <div>
                    <label for="user_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Users *</label>
                    <select id="user_ids" 
                            name="user_ids[]" 
                            multiple
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('user_ids') border-red-500 @enderror"
                            style="min-height: 120px;">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_ids') && in_array($user->id, old('user_ids'))) || (isset($notification->assigned_user_ids) && in_array($user->id, $notification->assigned_user_ids)) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</p>
                    @error('user_ids')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notification Type -->
                <div>
                    <label for="notification_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notification Type *</label>
                    <select id="notification_type" 
                            name="notification_type" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('notification_type') border-red-500 @enderror">
                        <option value="instant" {{ old('notification_type', $notification->notification_type) === 'instant' ? 'selected' : '' }}>Send Instant</option>
                        <option value="after_minutes" {{ old('notification_type', $notification->notification_type) === 'after_minutes' ? 'selected' : '' }}>After X Minutes</option>
                        <option value="after_hours" {{ old('notification_type', $notification->notification_type) === 'after_hours' ? 'selected' : '' }}>After X Hours</option>
                        <option value="daily" {{ old('notification_type', $notification->notification_type) === 'daily' ? 'selected' : '' }}>Daily at Particular Time</option>
                        <option value="weekly" {{ old('notification_type', $notification->notification_type) === 'weekly' ? 'selected' : '' }}>Weekly at Particular Time</option>
                        <option value="monthly" {{ old('notification_type', $notification->notification_type) === 'monthly' ? 'selected' : '' }}>Monthly at Particular Time</option>
                    </select>
                    @error('notification_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conditional Fields based on Notification Type -->
                <div id="after_minutes_field" class="hidden">
                    <label for="duration_value_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minutes *</label>
                    <input type="number" 
                           id="duration_value_minutes" 
                           name="duration_value" 
                           value="{{ old('duration_value', $notification->duration_value) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('duration_value') border-red-500 @enderror"
                           placeholder="Enter minutes">
                    @error('duration_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="after_hours_field" class="hidden">
                    <label for="duration_value_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hours *</label>
                    <input type="number" 
                           id="duration_value_hours" 
                           name="duration_value" 
                           value="{{ old('duration_value', $notification->duration_value) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('duration_value') border-red-500 @enderror"
                           placeholder="Enter hours">
                    @error('duration_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="daily_field" class="hidden">
                    <label for="daily_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Daily Time *</label>
                    <input type="time" 
                           id="daily_time" 
                           name="daily_time" 
                           value="{{ old('daily_time', $notification->daily_time ? (is_object($notification->daily_time) ? $notification->daily_time->format('H:i') : \Carbon\Carbon::parse($notification->daily_time)->format('H:i')) : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('daily_time') border-red-500 @enderror">
                    @error('daily_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="weekly_field" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="weekly_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Day of Week *</label>
                            <select id="weekly_day" 
                                    name="weekly_day" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('weekly_day') border-red-500 @enderror">
                                <option value="0" {{ old('weekly_day', $notification->weekly_day) === '0' || old('weekly_day', $notification->weekly_day) === 0 ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ old('weekly_day', $notification->weekly_day) === '1' || old('weekly_day', $notification->weekly_day) === 1 ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('weekly_day', $notification->weekly_day) === '2' || old('weekly_day', $notification->weekly_day) === 2 ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('weekly_day', $notification->weekly_day) === '3' || old('weekly_day', $notification->weekly_day) === 3 ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('weekly_day', $notification->weekly_day) === '4' || old('weekly_day', $notification->weekly_day) === 4 ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('weekly_day', $notification->weekly_day) === '5' || old('weekly_day', $notification->weekly_day) === 5 ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('weekly_day', $notification->weekly_day) === '6' || old('weekly_day', $notification->weekly_day) === 6 ? 'selected' : '' }}>Saturday</option>
                            </select>
                            @error('weekly_day')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="weekly_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                            <input type="time" 
                                   id="weekly_time" 
                                   name="weekly_time" 
                                   value="{{ old('weekly_time', $notification->weekly_time ? (is_object($notification->weekly_time) ? $notification->weekly_time->format('H:i') : \Carbon\Carbon::parse($notification->weekly_time)->format('H:i')) : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('weekly_time') border-red-500 @enderror">
                            @error('weekly_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="monthly_field" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="monthly_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Day of Month * (1-31)</label>
                            <input type="number" 
                                   id="monthly_day" 
                                   name="monthly_day" 
                                   value="{{ old('monthly_day', $notification->monthly_day) }}"
                                   min="1"
                                   max="31"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('monthly_day') border-red-500 @enderror"
                                   placeholder="Enter day (1-31)">
                            @error('monthly_day')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="monthly_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                            <input type="time" 
                                   id="monthly_time" 
                                   name="monthly_time" 
                                   value="{{ old('monthly_time', $notification->monthly_time ? (is_object($notification->monthly_time) ? $notification->monthly_time->format('H:i') : \Carbon\Carbon::parse($notification->monthly_time)->format('H:i')) : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('monthly_time') border-red-500 @enderror">
                            @error('monthly_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Delivery Methods -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Delivery Methods *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="push"
                                   {{ (old('delivery_methods') && in_array('push', old('delivery_methods'))) || (!old('delivery_methods') && in_array('push', $notification->delivery_methods ?? [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Push Notification</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="telegram"
                                   {{ (old('delivery_methods') && in_array('telegram', old('delivery_methods'))) || (!old('delivery_methods') && in_array('telegram', $notification->delivery_methods ?? [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Telegram</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="login_popup"
                                   {{ (old('delivery_methods') && in_array('login_popup', old('delivery_methods'))) || (!old('delivery_methods') && in_array('login_popup', $notification->delivery_methods ?? [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show After Login</span>
                        </label>
                    </div>
                    @error('delivery_methods')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requires Web PIN -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_web_pin" 
                               value="1"
                               {{ old('requires_web_pin', $notification->requires_web_pin) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require Web PIN to close popup</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If checked, users must enter their web PIN to close the notification popup after login</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('notifications.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationType = document.getElementById('notification_type');
        const afterMinutesField = document.getElementById('after_minutes_field');
        const afterHoursField = document.getElementById('after_hours_field');
        const dailyField = document.getElementById('daily_field');
        const weeklyField = document.getElementById('weekly_field');
        const monthlyField = document.getElementById('monthly_field');

        function toggleFields() {
            // Hide all fields first
            afterMinutesField.classList.add('hidden');
            afterHoursField.classList.add('hidden');
            dailyField.classList.add('hidden');
            weeklyField.classList.add('hidden');
            monthlyField.classList.add('hidden');

            // Remove required attributes
            document.getElementById('duration_value_minutes')?.removeAttribute('required');
            document.getElementById('duration_value_hours')?.removeAttribute('required');
            document.getElementById('daily_time')?.removeAttribute('required');
            document.getElementById('weekly_day')?.removeAttribute('required');
            document.getElementById('weekly_time')?.removeAttribute('required');
            document.getElementById('monthly_day')?.removeAttribute('required');
            document.getElementById('monthly_time')?.removeAttribute('required');

            // Show and set required based on selected type
            const selectedType = notificationType.value;
            if (selectedType === 'after_minutes') {
                afterMinutesField.classList.remove('hidden');
                document.getElementById('duration_value_minutes')?.setAttribute('required', 'required');
            } else if (selectedType === 'after_hours') {
                afterHoursField.classList.remove('hidden');
                document.getElementById('duration_value_hours')?.setAttribute('required', 'required');
            } else if (selectedType === 'daily') {
                dailyField.classList.remove('hidden');
                document.getElementById('daily_time')?.setAttribute('required', 'required');
            } else if (selectedType === 'weekly') {
                weeklyField.classList.remove('hidden');
                document.getElementById('weekly_day')?.setAttribute('required', 'required');
                document.getElementById('weekly_time')?.setAttribute('required', 'required');
            } else if (selectedType === 'monthly') {
                monthlyField.classList.remove('hidden');
                document.getElementById('monthly_day')?.setAttribute('required', 'required');
                document.getElementById('monthly_time')?.setAttribute('required', 'required');
            }
        }

        // Initial call
        toggleFields();

        // Listen for changes
        notificationType.addEventListener('change', toggleFields);
    });
</script>
@endpush
@endsection

