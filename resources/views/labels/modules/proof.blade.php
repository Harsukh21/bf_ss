@extends('layouts.app')
@section('title', 'Proof — ' . $label->name)
@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Proof Management</h1>
        <div class="flex items-center gap-2">
            {{-- Filter toggle --}}
            <button id="filterToggle" onclick="toggleFilters()"
                class="p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700" title="Toggle Filters">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </button>
            <a href="{{ route('labels.proof.create', $label) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-primary-600 hover:bg-gray-800 dark:hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Proof
            </a>
        </div>
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
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                @if($proofs->total() > 0)
                    <span>Showing {{ $proofs->firstItem() }} to {{ $proofs->lastItem() }} of {{ $proofs->total() }} entries</span>
                @else
                    <span>No entries found</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('labels.proof', $label) }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by any field..."
                        class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 w-52">
                    @if(request('search'))
                        <a href="{{ route('labels.proof', $label) }}" class="px-3 py-1.5 text-sm text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">✕</a>
                    @endif
                </form>
            </div>
        </div>

        @if($proofs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-3 py-3 w-8">
                            <input type="checkbox" id="checkAll" class="rounded border-gray-300 dark:border-gray-600"
                                onclick="document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked)">
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Whitelabel User(WL)
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            WhatsApp Group
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Agent Name
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            User
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Amount
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Proof Type
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Sport
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Event Name</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Market Name</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">P/L</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Date</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($proofs as $proof)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-3 py-3">
                            <input type="checkbox" class="row-check rounded border-gray-300 dark:border-gray-600" value="{{ $proof->id }}">
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                            {{ $proof->whitelabel?->name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 max-w-[160px] truncate">
                            {{ $proof->whatsapp_group ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $proof->agent_name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $proof->user_name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-700 dark:text-gray-200 whitespace-nowrap font-mono">
                            {{ $proof->amount ? number_format($proof->amount, 0) : '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 max-w-[180px]">
                            {{ $proof->proofType?->name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $proof->sport?->name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap max-w-[140px] truncate">
                            {{ $proof->event_name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap max-w-[130px] truncate">
                            {{ $proof->market_name ?? '—' }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap font-mono @if($proof->profit_loss > 0) text-green-600 @elseif($proof->profit_loss < 0) text-red-600 @else text-gray-600 dark:text-gray-300 @endif">
                            {{ $proof->profit_loss !== null ? number_format($proof->profit_loss, 0) : '—' }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap text-xs">
                            {{ $proof->proof_date?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-1">
                                {{-- View --}}
                                <a href="{{ route('labels.proof.show', [$label, $proof]) }}"
                                    class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('labels.proof.edit', [$label, $proof]) }}"
                                    class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('labels.proof.destroy', [$label, $proof]) }}"
                                      data-confirm="Delete this proof? This cannot be undone."
                                      data-confirm-text="Delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                {{-- Download PDF --}}
                                <button onclick='openDownloadModal(@json($proof))'
                                    class="p-1.5 text-green-600 hover:text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Download PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($proofs->hasPages())
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Page {{ $proofs->currentPage() }} of {{ $proofs->lastPage() }}
            </div>
            <div class="flex items-center gap-1">
                @if($proofs->onFirstPage())
                    <span class="px-2 py-1.5 text-gray-400 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $proofs->previousPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                <span class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded">
                    Page {{ $proofs->currentPage() }} of {{ $proofs->lastPage() }}
                </span>
                @if($proofs->hasMorePages())
                    <a href="{{ $proofs->nextPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-3">No proofs found.</p>
            <a href="{{ route('labels.proof.create', $label) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-primary-600 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Proof
            </a>
        </div>
        @endif
    </div>
</div>

{{-- ========= CONFIRM DOWNLOAD MODAL ========= --}}
<div id="downloadOverlay" class="fixed inset-0 bg-black/50 z-[900] hidden items-center justify-center" onclick="if(event.target===this)closeDownloadModal()">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Confirm Download</h3>
            <button onclick="closeDownloadModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-5 py-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Proof Maker</label>
                <input type="text" id="dlProofMaker" placeholder="Enter proof maker name..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp Group</label>
                <input type="text" id="dlWhatsappGroup" placeholder="WhatsApp group name..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div class="flex items-center gap-3 px-5 pb-5 pt-1">
            <button onclick="submitDownload()"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </button>
            <button onclick="closeDownloadModal()"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- ========= FILTER PANEL (right slide-in) ========= --}}
<div id="filterOverlay" class="fixed inset-0 z-[800] hidden" onclick="closeFilter()"></div>
<div id="filterPanel"
    class="fixed top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-2xl z-[801] overflow-y-auto"
    style="transform:translateX(100%);transition:transform 0.25s ease;">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Filters</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('labels.proof', $label) }}" class="px-3 py-1.5 text-xs font-medium bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">Reset Filters</a>
            <button onclick="closeFilter()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <form method="GET" action="{{ route('labels.proof', $label) }}" class="p-5 space-y-4">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif

        <div class="grid grid-cols-2 gap-3">
            {{-- Date range --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Whitelabel --}}
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Whitelabel</label>
                <select name="f_whitelabel"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach($whitelabels as $wl)
                        <option value="{{ $wl->id }}" @selected(request('f_whitelabel') == $wl->id)>{{ $wl->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Proof Type --}}
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Type</label>
                <select name="f_proof_type"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach($proofTypes as $pt)
                        <option value="{{ $pt->id }}" @selected(request('f_proof_type') == $pt->id)>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sport --}}
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sport</label>
                <select name="f_sport"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach($sports as $sp)
                        <option value="{{ $sp->id }}" @selected(request('f_sport') == $sp->id)>{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Agent / User --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Agent Name</label>
                <input type="text" name="f_agent" value="{{ request('f_agent') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                <input type="text" name="f_user" value="{{ request('f_user') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Event / Market --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Event Name</label>
                <input type="text" name="f_event" value="{{ request('f_event') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Market Name</label>
                <input type="text" name="f_market" value="{{ request('f_market') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Amount range --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Min</label>
                <input type="number" step="0.01" name="amount_min" value="{{ request('amount_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Max</label>
                <input type="number" step="0.01" name="amount_max" value="{{ request('amount_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- P&L range --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">P/L Min</label>
                <input type="number" step="0.01" name="pl_min" value="{{ request('pl_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">P/L Max</label>
                <input type="number" step="0.01" name="pl_max" value="{{ request('pl_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Apply Filters</button>
        </div>
    </form>
</div>

@push('js')
<script>
// ===== Download modal =====
var _dlProof = null;
var _dlBaseUrls = @json($proofs->mapWithKeys(fn($p) => [$p->id => route('labels.proof.download', [$label, $p])]));

function openDownloadModal(proof) {
    _dlProof = proof;
    document.getElementById('dlProofMaker').value = '';
    document.getElementById('dlWhatsappGroup').value = proof.whatsapp_group || '';
    const overlay = document.getElementById('downloadOverlay');
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('dlProofMaker').focus(), 50);
}
function closeDownloadModal() {
    const overlay = document.getElementById('downloadOverlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
    document.body.style.overflow = '';
    _dlProof = null;
}
function submitDownload() {
    if (!_dlProof) return;
    const maker = document.getElementById('dlProofMaker').value;
    const wa    = document.getElementById('dlWhatsappGroup').value;
    const base  = _dlBaseUrls[_dlProof.id];
    const url   = base + '?proof_maker=' + encodeURIComponent(maker) + '&whatsapp_group=' + encodeURIComponent(wa);
    window.location.href = url;
    closeDownloadModal();
}

// ===== Filter panel =====
function toggleFilters() {
    const panel   = document.getElementById('filterPanel');
    const overlay = document.getElementById('filterOverlay');
    const isOpen  = panel.style.transform === 'translateX(0%)';
    if (isOpen) {
        closeFilter();
    } else {
        overlay.classList.remove('hidden');
        panel.style.transform = 'translateX(0%)';
        document.body.style.overflow = 'hidden';
    }
}
function closeFilter() {
    document.getElementById('filterPanel').style.transform = 'translateX(100%)';
    document.getElementById('filterOverlay').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFilter(); });

// Auto-open if any filter is active
@if(request()->hasAny(['date_from','date_to','f_whitelabel','f_proof_type','f_sport','f_agent','f_user','f_event','f_market','amount_min','amount_max','pl_min','pl_max']))
    document.addEventListener('DOMContentLoaded', () => setTimeout(toggleFilters, 100));
@endif
</script>
@endpush
@endsection
