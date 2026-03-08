{{--
    Reusable file upload field
    Variables:
      $name    — input name attribute
      $label   — field label text
      $accept  — accepted file extensions e.g. '.jpg,.png,.pdf'
      $hint    — helper text shown below input
      $current — current stored path/URL (nullable)
      $isImage — bool, show image preview (default false)
--}}
@php
$isImage = $isImage ?? false;
$hasFile = !empty($current);
$ext     = $hasFile ? strtolower(pathinfo(parse_url($current, PHP_URL_PATH), PATHINFO_EXTENSION)) : '';
$isPdf   = in_array($ext, ['pdf']);
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>

    {{-- Existing file preview --}}
    @if($hasFile)
    <div class="mt-1 mb-2 flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
        @if($isImage && !$isPdf)
            <img src="{{ $current }}" alt="{{ $label }}"
                 class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">
        @elseif($isPdf)
            <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        @else
            <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Current file</p>
            <a href="{{ $current }}" target="_blank"
               class="text-sm text-primary-600 dark:text-primary-400 hover:underline truncate block">
                View / Download ↗
            </a>
        </div>
        <span class="text-xs text-gray-400 italic whitespace-nowrap">Upload new to replace</span>
    </div>
    @endif

    {{-- File input --}}
    <div class="mt-1">
        <label class="flex flex-col items-center justify-center w-full px-4 py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-400 dark:hover:border-primary-500 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-primary-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="emp-file-name-{{ $name }} text-sm text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                {{ $hasFile ? 'Click to replace file' : 'Click to browse or drag & drop' }}
            </span>
            <span class="text-xs text-gray-400 mt-0.5">{{ $hint }}</span>
            <input type="file" name="{{ $name }}" accept="{{ $accept }}"
                   class="sr-only"
                   onchange="document.querySelector('.emp-file-name-{{ $name }}').textContent = this.files[0]?.name || '{{ $hasFile ? 'Click to replace file' : 'Click to browse or drag & drop' }}'">
        </label>
    </div>
    @error($name)<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
