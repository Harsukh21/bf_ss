@extends('layouts.app')
@section('title', 'Sports — ' . $label->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $label->name }} Sports Management</h1>
        <button onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-primary-600 hover:bg-gray-800 dark:hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add {{ $label->name }} Sport
        </button>
    </div>

    {{-- Session messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap-3 px-5 py-3 border-b border-gray-100 dark:border-gray-700">
            {{-- Show entries --}}
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span>Show</span>
                <form method="GET" action="{{ route('labels.sports', $label) }}" id="perPageForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                        class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </form>
                <span>entries</span>
                @if($sports->total() > 0)
                    <span class="ml-2 text-gray-400">— Showing {{ $sports->firstItem() }} to {{ $sports->lastItem() }} of {{ $sports->total() }} entries</span>
                @endif
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('labels.sports', $label) }}" class="flex gap-2">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by {{ $label->name }} Sport Name"
                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 w-56">
                @if(request('search'))
                    <a href="{{ route('labels.sports', $label) }}" class="px-3 py-1.5 text-sm text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">✕</a>
                @endif
            </form>
        </div>

        @if($sports->count() > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/40 border-b border-gray-200 dark:border-gray-700">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $label->name }} Sport Name
                        <svg class="inline w-3 h-3 ml-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($sports as $sport)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $sport->name }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-1">
                            {{-- View --}}
                            <button onclick='openViewModal(@json($sport))'
                                class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            {{-- Edit --}}
                            <button onclick='openEditModal(@json($sport))'
                                class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('labels.sports.destroy', [$label, $sport]) }}"
                                  data-confirm="Delete '{{ $sport->name }}'? This cannot be undone."
                                  data-confirm-text="Delete">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($sports->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Page {{ $sports->currentPage() }} of {{ $sports->lastPage() }}
            </div>
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if($sports->onFirstPage())
                    <span class="px-2 py-1.5 text-gray-400 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $sports->previousPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                <span class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded">
                    Page {{ $sports->currentPage() }} of {{ $sports->lastPage() }}
                </span>

                {{-- Go to page --}}
                <form method="GET" action="{{ route('labels.sports', $label) }}" class="flex items-center gap-1">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <input type="number" name="page" min="1" max="{{ $sports->lastPage() }}" placeholder="Go"
                        class="w-14 px-2 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 text-center">
                    <button type="submit" class="px-3 py-1.5 text-sm bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors">Go</button>
                </form>

                {{-- Next --}}
                @if($sports->hasMorePages())
                    <a href="{{ $sports->nextPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="px-2 py-1.5 text-gray-400 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif

        @else
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-3">No sports found.</p>
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-primary-600 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Sport
            </button>
        </div>
        @endif
    </div>
</div>

{{-- ===================== ADD / EDIT MODAL ===================== --}}
<div id="sportModal" class="fixed inset-0 z-[900] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="sportModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">

            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 id="sportModalTitle" class="text-base font-semibold text-gray-900 dark:text-gray-100">Add {{ $label->name }} Sport</h3>
                <button onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="sportForm" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="sportMethod" value="POST">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ $label->name }} Sport Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="sportName" required placeholder="Enter {{ strtolower($label->name) }} sport name"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="button" onclick="resetForm()"
                        class="px-5 py-2 text-sm font-medium bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg transition-colors">Reset</button>
                    <button type="button" onclick="closeModal()"
                        class="px-5 py-2 text-sm font-medium bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== VIEW MODAL ===================== --}}
<div id="viewModal" class="fixed inset-0 z-[900] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeViewModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="viewModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Sport Details</h3>
                <button onclick="closeViewModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5 uppercase tracking-wider">Sport Name</p>
                    <p id="viewName" class="text-base font-semibold text-gray-900 dark:text-gray-100"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5 uppercase tracking-wider">Slug</p>
                    <p id="viewSlug" class="text-sm font-mono text-gray-600 dark:text-gray-300"></p>
                </div>
            </div>
            <div class="px-5 pb-5 flex justify-end">
                <button onclick="closeViewModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Close</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const addUrl   = "{{ route('labels.sports.store', $label) }}";
const editBase = "{{ url('labels/' . $label->id . '/sports') }}";

function openAddModal() {
    resetForm();
    document.getElementById('sportModalTitle').textContent = 'Add {{ $label->name }} Sport';
    document.getElementById('sportForm').action = addUrl;
    document.getElementById('sportMethod').value = 'POST';
    showModal('sportModal', 'sportModalBox');
}

function openEditModal(sport) {
    resetForm();
    document.getElementById('sportModalTitle').textContent = 'Edit Sport';
    document.getElementById('sportForm').action = editBase + '/' + sport.id;
    document.getElementById('sportMethod').value = 'PUT';
    document.getElementById('sportName').value = sport.name || '';
    showModal('sportModal', 'sportModalBox');
}

function openViewModal(sport) {
    document.getElementById('viewName').textContent = sport.name || '—';
    document.getElementById('viewSlug').textContent = sport.slug || '—';
    showModal('viewModal', 'viewModalBox');
}

function closeModal()     { hideModal('sportModal', 'sportModalBox'); }
function closeViewModal() { hideModal('viewModal', 'viewModalBox'); }

function showModal(overlayId, boxId) {
    document.getElementById(overlayId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        document.getElementById(boxId).style.transform = 'scale(1)';
        document.getElementById(boxId).style.opacity   = '1';
    });
}

function hideModal(overlayId, boxId) {
    const box = document.getElementById(boxId);
    box.style.transform = 'scale(0.93)';
    box.style.opacity   = '0';
    setTimeout(() => {
        document.getElementById(overlayId).classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function resetForm() {
    document.getElementById('sportName').value = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('sportModal').classList.contains('hidden')) closeModal();
        if (!document.getElementById('viewModal').classList.contains('hidden'))  closeViewModal();
    }
});
</script>
@endpush
@endsection
