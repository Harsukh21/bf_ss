<div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('tasks.show', $task) }}" class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                    {{ $task->title }}
                </a>

                <!-- Priority Badge -->
                @php
                    $priorityColors = [
                        'low' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                        'medium' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                        'high' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
                        'urgent' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$task->priority] }}">
                    {{ ucfirst($task->priority) }}
                </span>

                <!-- Status Badge -->
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                        'in_progress' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
                        'completed' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$task->status] }}">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>

            @if($task->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($task->description, 150) }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <!-- Created by -->
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Created by: {{ $task->creator->name }}</span>
                </div>

                <!-- Assigned to -->
                @if($task->assignedUser)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Assigned to: {{ $task->assignedUser->name }}</span>
                    </div>
                @endif

                <!-- Due date -->
                @if($task->due_date)
                    <div class="flex items-center {{ $task->isOverdue() ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                        @if($task->isOverdue())
                            <span class="ml-1">(Overdue)</span>
                        @endif
                    </div>
                @endif

                <!-- Progress -->
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Progress: {{ $task->progress }}%</span>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="mt-3">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full transition-all" style="width: {{ $task->progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="ml-4 flex items-center gap-2">
            <a href="{{ route('tasks.show', $task) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
            <a href="{{ route('tasks.edit', $task) }}" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
