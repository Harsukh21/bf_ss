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

{{-- ===== ADD TO REPORT MODAL ===== --}}
<div id="reportOverlay" class="fixed inset-0 bg-black/50 z-[900] hidden items-center justify-center" onclick="if(event.target===this)closeReportModal()">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Add Proof to Report</h3>
            <button onclick="closeReportModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Pre-filled summary --}}
        <div class="px-5 pt-4 pb-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 space-y-1">
            <div><span class="font-semibold">User:</span> {{ $proof->user_name ?? '—' }}</div>
            <div><span class="font-semibold">Agent:</span> {{ $proof->agent_name ?? '—' }}</div>
            <div><span class="font-semibold">Origin:</span> {{ $proof->whitelabel?->name ?? '—' }}</div>
            <div><span class="font-semibold">Date:</span> {{ $proof->proof_date?->format('d M Y') ?? '—' }}</div>
            <div><span class="font-semibold">Amount:</span> {{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}</div>
        </div>

        <form method="POST" action="{{ route('labels.proof.addToReport', [$label, $proof]) }}">
            @csrf
            <div class="px-5 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Before Void Balance</label>
                        <input type="number" step="0.01" name="before_void_balance" placeholder="0.00"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">After Void Balance</label>
                        <input type="number" step="0.01" name="after_void_balance" placeholder="0.00"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catch By</label>
                    <input type="text" name="catch_by" placeholder="Enter name..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Void Status</label>
                    <select name="void_status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">— Select —</option>
                        <option value="voided">Voided</option>
                        <option value="not_voided">Not Voided</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                    <textarea name="remark" rows="2" placeholder="Optional remark..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 px-5 pb-5 pt-1">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Add to Report
                </button>
                <button type="button" onclick="closeReportModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
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
