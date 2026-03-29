@extends('layouts.app')
@section('title', 'Notes — ' . $label->name)
@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $label->name }} Note Management</h1>
        <button onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add {{ $label->name }} Note
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
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($notes->total() > 0)
                    Showing {{ $notes->firstItem() }} to {{ $notes->lastItem() }} of {{ $notes->total() }} entries
                @else
                    No entries found
                @endif
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('labels.notes', $label) }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 w-48">
                    @if(request('search'))
                        <a href="{{ route('labels.notes', $label) }}" class="px-3 py-1.5 text-sm text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">✕</a>
                    @endif
                </form>
            </div>
        </div>

        @if($notes->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Date
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Origin
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Agent
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            User
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            WhatsApp Group
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Note
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($notes as $note)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200 whitespace-nowrap">
                            {{ $note->note_date?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-[200px] truncate">
                            {{ $note->origin ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $note->agent ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $note->user_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $note->whatsapp_group ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-[200px] truncate">
                            {{ $note->note ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                {{-- View --}}
                                <button onclick='openViewModal(@json($note))'
                                    class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                {{-- Edit --}}
                                <button onclick='openEditModal(@json($note))'
                                    class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('labels.notes.destroy', [$label, $note]) }}"
                                      data-confirm="Delete this note? This cannot be undone."
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
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                Show
                <form method="GET" action="{{ route('labels.notes', $label) }}" id="perPageForm">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                        class="px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        @foreach([10,20,50,100] as $n)
                            <option value="{{ $n }}" @selected($perPage==$n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </form>
                entries
            </div>
            @if($notes->hasPages())
            <div class="flex items-center gap-2">
                @if($notes->onFirstPage())
                    <span class="px-2 py-1.5 text-gray-400 bg-primary-800/40 rounded cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $notes->previousPageUrl() }}" class="px-2 py-1.5 bg-primary-700 hover:bg-primary-800 text-white rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                <span class="px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded whitespace-nowrap">
                    Page {{ $notes->currentPage() }} of {{ $notes->lastPage() }}
                </span>
                <form method="GET" action="{{ route('labels.notes', $label) }}" class="flex items-center gap-1">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <input type="number" name="page" min="1" max="{{ $notes->lastPage() }}" placeholder="Go"
                        class="w-14 px-2 py-1.5 text-sm text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <button type="submit" class="px-3 py-1.5 text-sm bg-primary-700 hover:bg-primary-800 text-white rounded transition-colors">Go</button>
                </form>
                @if($notes->hasMorePages())
                    <a href="{{ $notes->nextPageUrl() }}" class="px-2 py-1.5 bg-primary-700 hover:bg-primary-800 text-white rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="px-2 py-1.5 text-gray-400 bg-primary-800/40 rounded cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
            @endif
        </div>

        @else
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-3">No notes found.</p>
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-700 text-white text-sm rounded-lg hover:bg-primary-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Note
            </button>
        </div>
        @endif
    </div>
</div>

{{-- ===== ADD MODAL ===== --}}
<div id="addOverlay" class="fixed inset-0 bg-black/60 z-[900] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Add {{ $label->name }} Note</h3>
        </div>
        <form id="addForm" method="POST" action="{{ route('labels.notes.store', $label) }}">
            @csrf
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date</label>
                    <input type="date" name="note_date" id="add_note_date"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Origin</label>
                    <input type="text" name="origin" id="add_origin" placeholder="Enter origin"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agent</label>
                    <input type="text" name="agent" id="add_agent" placeholder="Enter agent"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">User</label>
                    <input type="text" name="user_name" id="add_user_name" placeholder="Enter user"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp Group</label>
                    <input type="text" name="whatsapp_group" id="add_whatsapp_group" placeholder="Enter WhatsApp group"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Note</label>
                    <textarea name="note" id="add_note" rows="4" placeholder=""
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="resetAddForm()"
                    class="px-5 py-2 text-sm font-medium text-white bg-yellow-400 hover:bg-yellow-500 rounded-lg transition-colors">Reset</button>
                <div class="flex gap-3">
                    <button type="button" onclick="closeAddModal()"
                        class="px-5 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-lg transition-colors">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editOverlay" class="fixed inset-0 bg-black/60 z-[900] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Edit Note</h3>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date</label>
                    <input type="date" name="note_date" id="edit_note_date"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Origin</label>
                    <input type="text" name="origin" id="edit_origin" placeholder="Enter origin"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agent</label>
                    <input type="text" name="agent" id="edit_agent" placeholder="Enter agent"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">User</label>
                    <input type="text" name="user_name" id="edit_user_name" placeholder="Enter user"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp Group</label>
                    <input type="text" name="whatsapp_group" id="edit_whatsapp_group" placeholder="Enter WhatsApp group"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Note</label>
                    <textarea name="note" id="edit_note" rows="4"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="resetEditForm()"
                    class="px-5 py-2 text-sm font-medium text-white bg-yellow-400 hover:bg-yellow-500 rounded-lg transition-colors">Reset</button>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-lg transition-colors">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== VIEW MODAL ===== --}}
<div id="viewOverlay" class="fixed inset-0 bg-black/60 z-[900] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">View Note</h3>
            <button onclick="closeViewModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-6 py-5 space-y-3">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</span>
                    <p id="view_note_date" class="text-gray-900 dark:text-gray-100 mt-0.5"></p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">WhatsApp Group</span>
                    <p id="view_whatsapp_group" class="text-gray-900 dark:text-gray-100 mt-0.5"></p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agent</span>
                    <p id="view_agent" class="text-gray-900 dark:text-gray-100 mt-0.5"></p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</span>
                    <p id="view_user_name" class="text-gray-900 dark:text-gray-100 mt-0.5"></p>
                </div>
            </div>
            <div class="text-sm pt-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Origin</span>
                <p id="view_origin" class="text-gray-900 dark:text-gray-100 mt-0.5 break-all"></p>
            </div>
            <div class="text-sm pt-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Note</span>
                <p id="view_note" class="text-gray-900 dark:text-gray-100 mt-0.5 whitespace-pre-wrap"></p>
            </div>
        </div>
        <div class="px-6 pb-5">
            <button onclick="closeViewModal()"
                class="w-full py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Close</button>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
const _notesUpdateBase = '{{ url()->current() }}';

// ===== Add Modal =====
function openAddModal() {
    resetAddForm();
    const o = document.getElementById('addOverlay');
    o.classList.remove('hidden'); o.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeAddModal() {
    const o = document.getElementById('addOverlay');
    o.classList.add('hidden'); o.classList.remove('flex');
    document.body.style.overflow = '';
}
function resetAddForm() {
    document.getElementById('addForm').reset();
}

// ===== Edit Modal =====
var _editNote = null;
function openEditModal(note) {
    _editNote = note;
    document.getElementById('editForm').action = '{{ route('labels.notes.store', $label) }}'.replace(/\/notes$/, '/notes/' + note.id);
    document.getElementById('edit_note_date').value      = note.note_date ? note.note_date.substring(0,10) : '';
    document.getElementById('edit_origin').value         = note.origin || '';
    document.getElementById('edit_agent').value          = note.agent || '';
    document.getElementById('edit_user_name').value      = note.user_name || '';
    document.getElementById('edit_whatsapp_group').value = note.whatsapp_group || '';
    document.getElementById('edit_note').value           = note.note || '';
    const o = document.getElementById('editOverlay');
    o.classList.remove('hidden'); o.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    const o = document.getElementById('editOverlay');
    o.classList.add('hidden'); o.classList.remove('flex');
    document.body.style.overflow = '';
}
function resetEditForm() {
    if (_editNote) openEditModal(_editNote);
}

// ===== View Modal =====
function openViewModal(note) {
    const fmt = d => d ? d.substring(0,10).split('-').reverse().join('/') : '—';
    document.getElementById('view_note_date').textContent      = fmt(note.note_date);
    document.getElementById('view_origin').textContent         = note.origin || '—';
    document.getElementById('view_agent').textContent          = note.agent || '—';
    document.getElementById('view_user_name').textContent      = note.user_name || '—';
    document.getElementById('view_whatsapp_group').textContent = note.whatsapp_group || '—';
    document.getElementById('view_note').textContent           = note.note || '—';
    const o = document.getElementById('viewOverlay');
    o.classList.remove('hidden'); o.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeViewModal() {
    const o = document.getElementById('viewOverlay');
    o.classList.add('hidden'); o.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeAddModal(); closeEditModal(); closeViewModal(); }
});
</script>
@endpush
