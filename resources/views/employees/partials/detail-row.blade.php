<div>
    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</dt>
    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
        @if(!empty($value))
            {{ $value }}
        @else
            <span class="text-gray-400 italic">—</span>
        @endif
    </dd>
</div>
