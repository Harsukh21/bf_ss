@extends('layouts.app')
@section('title', 'Proof Types — ' . $label->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Proof Type Management</h1>
        <button onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-gray-100 hover:bg-gray-800 dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Proof Type
        </button>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('labels.prooftype', $label) }}" class="mb-4">
        <div class="flex gap-2">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search proof types..."
                    class="pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Search</button>
            @if(request('search'))
                <a href="{{ route('labels.prooftype', $label) }}" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Clear</a>
            @endif
        </div>
    </form>

    {{-- Session messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if($proofTypes->count() > 0)
    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-56">Proof Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Proof Content</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($proofTypes as $pt)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors align-top">
                    <td class="px-6 py-4">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $pt->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 prose prose-sm dark:prose-invert max-w-none">
                        {!! $pt->description !!}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            {{-- View --}}
                            <button onclick='openViewModal(@json($pt))'
                                class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            {{-- Edit --}}
                            <button onclick='openEditModal(@json($pt))'
                                class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('labels.prooftype.destroy', [$label, $pt]) }}"
                                  data-confirm="Delete '{{ $pt->name }}'? This cannot be undone."
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
        @if($proofTypes->hasPages())
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $proofTypes->links() }}
            </div>
        @endif
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Proof Types Yet</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add the first proof type for <strong>{{ $label->name }}</strong>.</p>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Proof Type
        </button>
    </div>
    @endif
</div>

{{-- ===================== ADD / EDIT MODAL ===================== --}}
<div id="ptModal" class="fixed inset-0 z-[900] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="ptModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">

            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 id="ptModalTitle" class="text-base font-semibold text-gray-900 dark:text-gray-100">Add Proof Type</h3>
                <button onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="ptForm" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="ptMethod" value="POST">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Proof Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="ptName" required placeholder="e.g. Odds Manipulating and Hedging"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Description / HTML Content --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Proof Content <span class="text-xs text-gray-400 font-normal">(Accept Only HTML Code)</span>
                    </label>
                    <textarea name="description" id="ptDescription" rows="8" placeholder="<p>Enter HTML content here...</p>"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono resize-y"></textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-2">
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
        <div id="viewModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Proof Type Details</h3>
                    <p id="viewName" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"></p>
                </div>
                <button onclick="closeViewModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3 uppercase tracking-wider font-medium">Proof Content (Rendered)</p>
                <div id="viewContent" class="prose prose-sm dark:prose-invert max-w-none border border-gray-100 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/30"></div>
            </div>
            <div class="px-5 pb-5 flex justify-end">
                <button onclick="closeViewModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Close</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const addUrl   = "{{ route('labels.prooftype.store', $label) }}";
const editBase = "{{ url('labels/' . $label->id . '/prooftype') }}";

function openAddModal() {
    resetForm();
    document.getElementById('ptModalTitle').textContent = 'Add Proof Type';
    document.getElementById('ptForm').action = addUrl;
    document.getElementById('ptMethod').value = 'POST';
    showModal('ptModal', 'ptModalBox');
}

function openEditModal(pt) {
    resetForm();
    document.getElementById('ptModalTitle').textContent = 'Edit Proof Type';
    document.getElementById('ptForm').action = editBase + '/' + pt.id;
    document.getElementById('ptMethod').value = 'PUT';
    document.getElementById('ptName').value        = pt.name || '';
    document.getElementById('ptDescription').value = pt.description || '';
    showModal('ptModal', 'ptModalBox');
}

function openViewModal(pt) {
    document.getElementById('viewName').textContent    = pt.name || '';
    document.getElementById('viewContent').innerHTML   = pt.description || '<em class="text-gray-400">No content</em>';
    showModal('viewModal', 'viewModalBox');
}

function closeModal()     { hideModal('ptModal', 'ptModalBox'); }
function closeViewModal() { hideModal('viewModal', 'viewModalBox'); }

function showModal(overlayId, boxId) {
    const overlay = document.getElementById(overlayId);
    const box     = document.getElementById(boxId);
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        box.style.transform = 'scale(1)';
        box.style.opacity   = '1';
    });
}

function hideModal(overlayId, boxId) {
    const overlay = document.getElementById(overlayId);
    const box     = document.getElementById(boxId);
    box.style.transform = 'scale(0.93)';
    box.style.opacity   = '0';
    setTimeout(() => {
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function resetForm() {
    document.getElementById('ptName').value        = '';
    document.getElementById('ptDescription').value = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('ptModal').classList.contains('hidden'))   closeModal();
        if (!document.getElementById('viewModal').classList.contains('hidden')) closeViewModal();
    }
});
</script>
@endpush
@endsection
