@extends('layouts.app')
@section('title', 'Labels')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Labels</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage labels used across the system</p>
        </div>
        <a href="{{ route('labels.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Label
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
        {{ session('success') }}
    </div>
    @endif

    <!-- Labels Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                All Labels
                <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ $labels->total() }}</span>
            </h2>
        </div>

        @if($labels->isEmpty())
        <div class="px-5 py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No labels yet.</p>
            <a href="{{ route('labels.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add your first label
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide w-10">#</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Name</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Slug</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Added</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($labels as $label)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3.5 text-gray-400 dark:text-gray-500 text-xs">
                            {{ $loop->iteration + ($labels->currentPage() - 1) * $labels->perPage() }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 font-medium text-gray-900 dark:text-gray-100">
                                <svg class="w-3.5 h-3.5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ $label->name }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <code class="px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-mono">{{ $label->slug }}</code>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                            {{ $label->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button"
                                        onclick="openEditModal({{ $label->id }}, '{{ addslashes($label->name) }}')"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 rounded transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('labels.destroy', $label) }}" method="POST"
                                      data-confirm="Delete label &quot;{{ addslashes($label->name) }}&quot;? This cannot be undone."
                                      data-confirm-text="Delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($labels->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $labels->links() }}
        </div>
        @endif
        @endif

    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center p-4 hidden">
    <div id="editModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm" style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Edit Label</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editForm" method="POST" novalidate>
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Label Name</label>
                    <input type="text" name="name" id="editName"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           autocomplete="off">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Slug <span class="text-xs text-gray-400 font-normal">(auto-generated)</span>
                    </label>
                    <input type="text" id="editSlug" readonly
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg text-sm bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 cursor-not-allowed font-mono">
                </div>
                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                            class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function toSlug(str) {
    return str.toLowerCase().trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function openEditModal(id, name) {
    const modal    = document.getElementById('editModal');
    const modalBox = document.getElementById('editModalBox');
    const form     = document.getElementById('editForm');
    const nameEl   = document.getElementById('editName');
    const slugEl   = document.getElementById('editSlug');

    form.action = '/labels/' + id;
    nameEl.value = name;
    slugEl.value = toSlug(name);

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(() => {
        modalBox.style.transform = 'scale(1)';
        modalBox.style.opacity   = '1';
    });

    setTimeout(() => nameEl.focus(), 220);
}

function closeEditModal() {
    const modal    = document.getElementById('editModal');
    const modalBox = document.getElementById('editModalBox');

    modalBox.style.transform = 'scale(0.93)';
    modalBox.style.opacity   = '0';

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

// Close on backdrop click
document.getElementById('editModal').addEventListener('click', function (e) {
    if (e.target === this) closeEditModal();
});

// Live slug preview in edit modal
document.getElementById('editName').addEventListener('input', function () {
    document.getElementById('editSlug').value = toSlug(this.value);
});
</script>
@endpush
