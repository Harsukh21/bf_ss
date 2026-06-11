@extends('layouts.app')
@section('title', 'View Proof — ' . $label->name)
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Action Buttons --}}
    <div class="flex items-center justify-center gap-3 mb-6 flex-wrap">
        <a href="{{ route('labels.proof', $label) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
        <a href="{{ route('labels.proof.edit', [$label, $proof]) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Proof
        </a>
        <button onclick="openDownloadModal()"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download PDF
        </button>
        <button onclick="openReportModal()"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Add to Report
        </button>
    </div>

    {{-- Proof Document Preview (matches PDF layout) --}}
    <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-700">

        {{-- Header: dark bg + logo only --}}
        <div class="px-6 py-4 flex items-center" style="background:#111827;">
            @if($proof->whitelabel?->logo_link)
                <img src="{{ $proof->whitelabel->logo_link }}"
                     class="h-16 w-auto object-contain max-w-[160px]"
                     alt="{{ $proof->whitelabel->name }}">
            @else
                <span class="text-xl font-bold text-white">{{ $proof->whitelabel?->name ?? $label->name }}</span>
            @endif
        </div>

        {{-- Info row: 3 columns --}}
        <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-4">
                {{-- Left: whitelabel / agent / user --}}
                <div class="flex-1 text-sm font-bold text-gray-900 dark:text-gray-100 leading-loose">
                    <div>whitelabel user: {{ $proof->whitelabel?->name ?? '—' }}</div>
                    <div>Agent: {{ $proof->agent_name ?? '—' }}</div>
                    <div>User: {{ $proof->user_name ?? '—' }}</div>
                </div>
                {{-- Center: Total Amount --}}
                <div class="flex-1 text-center text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center justify-center">
                    Total Amount: {{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}
                </div>
                {{-- Right: sport / event / market --}}
                <div class="flex-1 text-right text-sm font-bold text-gray-900 dark:text-gray-100 leading-loose">
                    <div>Sport Name: {{ $proof->sport?->name ?? '—' }}</div>
                    <div>Event Name: {{ $proof->event_name ?? '—' }}</div>
                    <div>Market Name: {{ $proof->market_name ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- Template HTML content: raw, no wrapper box --}}
        @if($templateHtml)
        <div class="bg-white dark:bg-gray-800 px-6 py-5 text-sm text-gray-800 dark:text-gray-200 leading-relaxed proof-content">
            {!! $templateHtml !!}
        </div>
        @endif

        {{-- Navigation --}}
        @if($proof->navigation)
        <div class="bg-white dark:bg-gray-800 px-6 pb-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Navigation</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700 rounded p-3">{{ $proof->navigation }}</p>
        </div>
        @endif
        @if($proof->navigation2)
        <div class="bg-white dark:bg-gray-800 px-6 pb-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Navigation 2</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700 rounded p-3">{{ $proof->navigation2 }}</p>
        </div>
        @endif

        {{-- Images --}}
        @if(!empty($proof->images))
        <div class="bg-white dark:bg-gray-800 px-6 pb-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Screenshots</p>
            <div class="flex flex-wrap gap-2">
                @foreach($proof->images as $img)
                <a href="{{ asset('storage/' . $img) }}" target="_blank">
                    <img src="{{ asset('storage/' . $img) }}" class="h-20 w-auto rounded-lg border border-gray-200 dark:border-gray-600 object-cover hover:opacity-80 transition-opacity" alt="Screenshot">
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @if(!empty($proof->navigation2_images))
        <div class="bg-white dark:bg-gray-800 px-6 pb-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Navigation 2 Screenshots</p>
            <div class="flex flex-wrap gap-2">
                @foreach($proof->navigation2_images as $img)
                <a href="{{ asset('storage/' . $img) }}" target="_blank">
                    <img src="{{ asset('storage/' . $img) }}" class="h-20 w-auto rounded-lg border border-gray-200 dark:border-gray-600 object-cover hover:opacity-80 transition-opacity" alt="Screenshot">
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Footer: dark bg --}}
        <div class="px-6 py-3 flex items-center justify-between text-xs" style="background:#111827;">
            <span style="color:rgba(255,255,255,0.75);">
                {{ $proof->whitelabel?->domain ?? $label->name }}
                @if($proof->whatsapp_group) &bull; {{ $proof->whatsapp_group }}@endif
            </span>
            <span style="color:rgba(255,255,255,0.75);">
                {{ now()->format('d M Y') }} &bull; #{{ $proof->id }}
            </span>
        </div>
    </div>

    <style>
        .proof-content p   { margin-bottom: 8px; }
        .proof-content b, .proof-content strong { font-weight: 700; }
        .proof-content table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .proof-content td, .proof-content th { padding: 5px 8px; border: 1px solid #d1d5db; font-size: 12px; }
        .proof-content th { background: #374151; color: #fff; }
    </style>
</div>

{{-- ===== ADD TO REPORT MODAL (full form) ===== --}}
<div id="reportOverlay" class="fixed inset-0 bg-black/50 z-[900] hidden items-center justify-center p-4" onclick="if(event.target===this)closeReportModal()">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl mx-auto overflow-hidden flex flex-col" style="max-height:90vh;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Add to Report</h3>
            <button onclick="closeReportModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="overflow-y-auto flex-1">
            <form method="POST" action="{{ route('labels.proof.addToReport', [$label, $proof]) }}" novalidate>
                @csrf
                <div class="px-5 py-4 space-y-4">

                    {{-- Row 1: Date / User Name --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" name="report_date"
                                value="{{ $proof->proof_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Name</label>
                            <input type="text" name="user_name" value="{{ $proof->user_name }}" placeholder="Enter User Name"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    {{-- Row 2: Agent / Origin --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agent</label>
                            <input type="text" name="agent" value="{{ $proof->agent_name }}" placeholder="Enter Agent"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Origin</label>
                            <input type="text" name="origin" value="{{ $proof->whitelabel?->name }}" placeholder="Enter Origin"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    {{-- Row 3: Before / After Void Balance --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Before Void Balance</label>
                            <input type="number" step="0.01" name="before_void_balance" placeholder="0.00"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">After Void Balance</label>
                            <input type="number" step="0.01" name="after_void_balance" placeholder="0.00"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    {{-- Row 4: Catch By / Proof Type --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catch By</label>
                            <select name="catch_by"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Catch By</option>
                                <option value="Self">Self</option>
                                <option value="Agent">Agent</option>
                                <option value="System">System</option>
                                <option value="Risk Team">Risk Team</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Type</label>
                            <select name="proof_type_id"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Proof Type</option>
                                @foreach($proofTypes as $pt)
                                    <option value="{{ $pt->id }}" @selected($proof->proof_type_id == $pt->id)>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 5: Proof Status / Void Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Status</label>
                            <select name="proof_status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="submitted" @selected(($proof->status ?? 'submitted') === 'submitted')>Submitted</option>
                                <option value="approved" @selected(($proof->status ?? '') === 'approved')>Approved</option>
                                <option value="rejected" @selected(($proof->status ?? '') === 'rejected')>Rejected</option>
                                <option value="pending" @selected(($proof->status ?? '') === 'pending')>Pending</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Void Status</label>
                            <select name="void_status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">— Select —</option>
                                <option value="Void">Void</option>
                                <option value="Not Void">Not Void</option>
                                <option value="Partial Void">Partial Void</option>
                            </select>
                        </div>
                    </div>

                    {{-- Remark --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                        <input type="text" name="remark" placeholder="Enter Remark"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    {{-- Originals --}}
                    <div class="pt-2">
                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3">Originals</h2>

                        <div id="rptOriginalsContainer" class="space-y-4">
                            {{-- Initial original pre-filled from proof --}}
                            <div class="original-card border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Original <span class="orig-num">1</span></h3>
                                    <button type="button" onclick="rptRemoveOriginal(this)" class="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-4 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sport Name</label>
                                        <select name="originals[0][sport_name]"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            <option value="">Select Sport</option>
                                            @foreach($sports as $sp)
                                                <option value="{{ $sp->name }}" @selected($proof->sport?->name === $sp->name)>{{ $sp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Event Name</label>
                                        <input type="text" name="originals[0][event_name]" value="{{ $proof->event_name }}" placeholder="Enter Event Name"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Market Name</label>
                                        <input type="text" name="originals[0][market_name]" value="{{ $proof->market_name }}" placeholder="Enter Market Name"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">P&amp;L</label>
                                        <input type="number" step="0.01" name="originals[0][pl]" value="{{ $proof->profit_loss }}"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Bet Details</p>
                                    <div class="bet-details-container space-y-2">
                                        <div class="bet-detail-row grid grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label>
                                                <input type="number" step="0.01" name="originals[0][bet_details][0][odds]"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label>
                                                <input type="number" step="0.01" name="originals[0][bet_details][0][stack]"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label>
                                                <input type="text" name="originals[0][bet_details][0][time]" placeholder="HH:MM:SS"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="rptAddBetDetail(this)"
                                        class="mt-2 text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add Bet Detail
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="rptAddOriginal()"
                            class="mt-3 text-sm text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Add Original
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Create Report
                    </button>
                    <button type="button" onclick="closeReportModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== CONFIRM DOWNLOAD MODAL ===== --}}
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
                <input type="text" id="dlWhatsappGroup" value="{{ $proof->whatsapp_group }}"
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

@endsection

@push('js')
<script>
// ── Report modal originals ──────────────────────────────
let rptOrigIndex = 1;
const rptSportsData = @json($sports->pluck('name'));

function rptBuildSportsOptions(selected) {
    let html = '<option value="">Select Sport</option>';
    rptSportsData.forEach(name => {
        html += `<option value="${name}"${name === selected ? ' selected' : ''}>${name}</option>`;
    });
    return html;
}

function rptAddOriginal() {
    const container = document.getElementById('rptOriginalsContainer');
    const oi = rptOrigIndex;
    const card = document.createElement('div');
    card.className = 'original-card border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-800/50';
    card.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Original <span class="orig-num">${oi + 1}</span></h3>
            <button type="button" onclick="rptRemoveOriginal(this)" class="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sport Name</label>
                <select name="originals[${oi}][sport_name]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    ${rptBuildSportsOptions('')}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Event Name</label>
                <input type="text" name="originals[${oi}][event_name]" placeholder="Enter Event Name" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Market Name</label>
                <input type="text" name="originals[${oi}][market_name]" placeholder="Enter Market Name" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">P&amp;L</label>
                <input type="number" step="0.01" name="originals[${oi}][pl]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Bet Details</p>
            <div class="bet-details-container space-y-2">
                <div class="bet-detail-row grid grid-cols-3 gap-3">
                    <div><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label><input type="number" step="0.01" name="originals[${oi}][bet_details][0][odds]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"></div>
                    <div><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label><input type="number" step="0.01" name="originals[${oi}][bet_details][0][stack]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"></div>
                    <div><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label><input type="text" name="originals[${oi}][bet_details][0][time]" placeholder="HH:MM:SS" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"></div>
                </div>
            </div>
            <button type="button" onclick="rptAddBetDetail(this)" class="mt-2 text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Bet Detail
            </button>
        </div>
    `;
    container.appendChild(card);
    rptOrigIndex++;
    rptRenumberOriginals();
}

function rptRemoveOriginal(btn) {
    btn.closest('.original-card').remove();
    rptRenumberOriginals();
}

function rptRenumberOriginals() {
    document.querySelectorAll('#rptOriginalsContainer .original-card').forEach((card, i) => {
        card.querySelector('.orig-num').textContent = i + 1;
        card.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/originals\[\d+\]/, `originals[${i}]`);
        });
    });
}

function rptAddBetDetail(btn) {
    const card = btn.closest('.original-card');
    const betContainer = card.querySelector('.bet-details-container');
    const oi = card.querySelectorAll('[name]')[0]?.name.match(/originals\[(\d+)\]/)?.[1] ?? 0;
    const bi = betContainer.querySelectorAll('.bet-detail-row').length;
    const row = document.createElement('div');
    row.className = 'bet-detail-row grid grid-cols-3 gap-3 relative';
    row.innerHTML = `
        <div><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label><input type="number" step="0.01" name="originals[${oi}][bet_details][${bi}][odds]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"></div>
        <div><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label><input type="number" step="0.01" name="originals[${oi}][bet_details][${bi}][stack]" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"></div>
        <div class="relative"><label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label><input type="text" name="originals[${oi}][bet_details][${bi}][time]" placeholder="HH:MM:SS" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"><button type="button" onclick="this.closest('.bet-detail-row').remove()" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none">×</button></div>
    `;
    betContainer.appendChild(row);
}

// ── Modal open/close ────────────────────────────────────
function openReportModal() {
    const overlay = document.getElementById('reportOverlay');
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeReportModal() {
    const overlay = document.getElementById('reportOverlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
    document.body.style.overflow = '';
}
function openDownloadModal() {
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
}
function submitDownload() {
    const maker = encodeURIComponent(document.getElementById('dlProofMaker').value);
    const wa    = encodeURIComponent(document.getElementById('dlWhatsappGroup').value);
    window.location.href = '{{ route('labels.proof.download', [$label, $proof]) }}?proof_maker=' + maker + '&whatsapp_group=' + wa;
    closeDownloadModal();
}
</script>
@endpush
