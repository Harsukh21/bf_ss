@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
    <!-- Success Message Toast -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof ToastNotification !== 'undefined') {
                    ToastNotification.show('{{ session('success') }}', 'success');
                }
            });
        </script>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h1>

                        <!-- Priority Badge -->
                        @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                'medium' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                                'high' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
                                'urgent' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityColors[$task->priority] }}">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                    </div>

                    @if($task->description)
                        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $task->description }}</p>
                    @endif
                </div>

                <div class="flex items-center space-x-3 ml-4">
                    <a href="{{ route('tasks.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Tasks
                    </a>
                    @if(auth()->user()->hasRole('super-admin') || $task->created_by == auth()->id())
                        <a href="{{ route('tasks.edit', $task) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Task
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Badge -->
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                    'in_progress' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                    'completed' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800',
                ];
            @endphp
            <div class="mb-6 p-4 border-l-4 {{ $statusColors[$task->status] }} rounded">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}</h3>
                        <p class="text-sm mt-1">Progress: {{ $task->progress }}%</p>
                    </div>

                    @if($task->assigned_to == auth()->id() && $task->status !== 'completed')
                        <div class="flex items-center space-x-2">
                            <button onclick="showStatusModal()" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Update Status
                            </button>
                            <button onclick="showProgressModal()" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Update Progress
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                    <div class="bg-primary-600 h-4 rounded-full transition-all flex items-center justify-center" style="width: {{ $task->progress }}%">
                        @if($task->progress > 10)
                            <span class="text-xs font-medium text-white">{{ $task->progress }}%</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Task Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Created By -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created By</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/20 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ substr($task->creator->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->creator->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Assigned To -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assigned To</h3>
                    @if($task->assignedUser)
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 dark:text-green-400 font-semibold">{{ substr($task->assignedUser->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->assignedUser->name }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Not assigned</p>
                    @endif
                </div>

                <!-- Due Date -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Due Date</h3>
                    @if($task->due_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 {{ $task->isOverdue() ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->due_date->format('M d, Y h:i A') }}</p>
                                @if($task->isOverdue())
                                    <p class="text-xs text-red-600 dark:text-red-400 font-semibold">Overdue</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No due date set</p>
                    @endif
                </div>

                <!-- Completed At -->
                @if($task->completed_at)
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Completed At</h3>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->completed_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Notes Section -->
            @if($task->notes)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Notes</h3>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $task->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Delete Button (Only for Super Admin and Creator) -->
            @if(auth()->user()->hasRole('super-admin') || $task->created_by == auth()->id())
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" data-confirm="Are you sure you want to delete this task? This action cannot be undone." data-confirm-text="Delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Task
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update Task Status</h3>
        </div>
        <div class="px-6 py-4">
            <label for="status_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Select Status
            </label>
            <select id="status_select" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
            <button onclick="hideStatusModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="updateStatus()" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update
            </button>
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update Task Progress</h3>
        </div>
        <div class="px-6 py-4">
            <label for="progress_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Progress Percentage (0-100)
            </label>
            <input type="number" id="progress_input" min="0" max="100" value="{{ $task->progress }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Setting progress to 100% will automatically mark the task as completed.</p>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
            <button onclick="hideProgressModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="updateProgress()" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showStatusModal() {
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function hideStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    function showProgressModal() {
        document.getElementById('progressModal').classList.remove('hidden');
    }

    function hideProgressModal() {
        document.getElementById('progressModal').classList.add('hidden');
    }

    function updateStatus() {
        const status = document.getElementById('status_select').value;

        fetch('{{ route('tasks.update-status', $task) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ToastNotification.show(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                ToastNotification.show(data.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            ToastNotification.show('An error occurred while updating status', 'error');
        })
        .finally(() => {
            hideStatusModal();
        });
    }

    function updateProgress() {
        const progress = document.getElementById('progress_input').value;

        if (progress < 0 || progress > 100) {
            ToastNotification.show('Progress must be between 0 and 100', 'error');
            return;
        }

        fetch('{{ route('tasks.update-progress', $task) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ progress: parseInt(progress) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ToastNotification.show(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                ToastNotification.show(data.message || 'Failed to update progress', 'error');
            }
        })
        .catch(error => {
            ToastNotification.show('An error occurred while updating progress', 'error');
        })
        .finally(() => {
            hideProgressModal();
        });
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideStatusModal();
            hideProgressModal();
        }
    });
</script>
@endpush
