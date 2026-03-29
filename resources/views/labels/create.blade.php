@extends('layouts.app')
@section('title', 'Add Label')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Label</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create a new label for the system</p>
        </div>
        <a href="{{ route('labels.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <div>
                <p class="text-sm font-medium text-red-700 dark:text-red-300">Please fix the following errors:</p>
                <ul class="mt-1 text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('labels.store') }}" novalidate>
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Label Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="labelName"
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="e.g. Urgent, In Progress, Review..."
                       autocomplete="off" autofocus>
                @error('name')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Slug <span class="text-xs text-gray-400 font-normal">(auto-generated)</span>
                </label>
                <input type="text" id="slugPreview" readonly
                       value="{{ old('name') ? \Illuminate\Support\Str::slug(old('name')) : '' }}"
                       class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg text-sm bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 cursor-not-allowed font-mono"
                       placeholder="auto-generated from name...">
                <p class="mt-1.5 text-xs text-gray-400">Used as a unique identifier. Generated automatically from the label name.</p>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Label
                </button>
                <a href="{{ route('labels.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('js')
<script>
(function () {
    function toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
    const nameEl = document.getElementById('labelName');
    const slugEl = document.getElementById('slugPreview');
    if (nameEl && slugEl) {
        nameEl.addEventListener('input', function () {
            slugEl.value = toSlug(this.value);
        });
    }
})();
</script>
@endpush
