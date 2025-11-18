@extends('layouts.app')

@section('title', 'Create Role')

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
                                <a href="{{ route('roles.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    Roles
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">
                                        Create Role
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Create Role</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new role and assign permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Roles
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.store') }}" id="createRoleForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Role Information Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Role Information
                            </h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name *</label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('name') border-red-500 @enderror" 
                                           required
                                           placeholder="e.g., Administrator, Manager, Editor">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">A human-readable name for this role</p>
                                </div>
                                
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                                    <input type="text" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug') }}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm @error('slug') border-red-500 @enderror"
                                           placeholder="auto-generated from name">
                                    @error('slug')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">A URL-friendly version of the name (auto-generated if left blank)</p>
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea id="description" 
                                              name="description" 
                                              rows="3"
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('description') border-red-500 @enderror"
                                              placeholder="Describe the purpose and responsibilities of this role...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only active roles can be assigned to users</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Assign Permissions
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Select the permissions that this role should have. Permissions are grouped by category for easier management.
                            </p>
                            
                            @if($permissions && $permissions->count() > 0)
                                <!-- Tab Navigation -->
                                <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                                        @foreach($permissions as $group => $groupPermissions)
                                            <button type="button"
                                                    onclick="switchPermissionTab('{{ Str::slug($group ?? 'general') }}')"
                                                    id="tab-{{ Str::slug($group ?? 'general') }}"
                                                    class="permission-tab whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm {{ $loop->first ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                                                {{ $group ?? 'General' }}
                                                <span class="ml-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-xs">
                                                    {{ count($groupPermissions) }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>

                                <!-- Tab Content -->
                                <div>
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div id="content-{{ Str::slug($group ?? 'general') }}" 
                                             class="permission-tab-content {{ $loop->first ? '' : 'hidden' }}">
                                            <div class="mb-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $group ?? 'General' }} Permissions
                                                    </h4>
                                                    <button type="button" 
                                                            onclick="toggleGroup('group-{{ Str::slug($group ?? 'general') }}')"
                                                            class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                                        Toggle All
                                                    </button>
                                                </div>
                                                <div id="group-{{ Str::slug($group ?? 'general') }}" class="grid grid-cols-2 gap-3">
                                                    @foreach($groupPermissions as $permission)
                                                        <label class="flex items-start space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                                            <input type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission->id }}"
                                                                   {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}
                                                                   class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 flex-shrink-0">
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $permission->name }}</div>
                                                                @if($permission->description)
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $permission->description }}</div>
                                                                @endif
                                                                <div class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-1 truncate">{{ $permission->slug }}</div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No permissions available. Please create permissions first.</p>
                                </div>
                            @endif
                            @error('permissions')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-end space-x-4">
                                <a href="{{ route('roles.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Create Role
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Quick Tips Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg sticky top-4">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Quick Tips
                            </h3>
                            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Give roles meaningful names that clearly describe their purpose</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Only assign permissions that are necessary for the role</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You can always edit permissions later if needed</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('css')
<style>
    .permission-tab.active {
        border-color: rgb(99 102 241);
        color: rgb(99 102 241);
    }
    .dark .permission-tab.active {
        border-color: rgb(129 140 248);
        color: rgb(129 140 248);
    }
</style>
@endpush

@push('js')
<script>
    // Auto-generate slug from name
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        if (nameInput && slugInput) {
            // Auto-generate on input if slug is empty or hasn't been manually edited
            nameInput.addEventListener('input', function(e) {
                if (!slugInput.dataset.manual || !slugInput.value) {
                    const slug = e.target.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = slug;
                    slugInput.dataset.prevValue = slug;
                }
            });

            // Track manual slug edits
            slugInput.addEventListener('input', function(e) {
                if (e.target.value) {
                    e.target.dataset.manual = 'true';
                } else {
                    e.target.dataset.manual = 'false';
                    // Auto-regenerate if cleared
                    if (nameInput.value) {
                        const slug = nameInput.value
                            .toLowerCase()
                            .trim()
                            .replace(/[^\w\s-]/g, '')
                            .replace(/[\s_-]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        e.target.value = slug;
                        e.target.dataset.prevValue = slug;
                    }
                }
            });

            // Initialize slug if name has value but slug is empty
            if (nameInput.value && !slugInput.value) {
                const slug = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.prevValue = slug;
            }
        }
    });

    // Switch permission tabs
    function switchPermissionTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.permission-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.permission-tab').forEach(tab => {
            tab.classList.remove('active', 'border-primary-500', 'text-primary-600', 'dark:text-primary-400');
            tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        // Show selected tab content
        const selectedContent = document.getElementById('content-' + tabName);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        
        // Add active class to selected tab
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.classList.add('active', 'border-primary-500', 'text-primary-600', 'dark:text-primary-400');
            selectedTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        }
    }

    // Toggle all checkboxes in a group
    function toggleGroup(groupId) {
        const group = document.getElementById(groupId);
        if (!group) return;
        
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
    }
</script>
@endpush
@endsection

