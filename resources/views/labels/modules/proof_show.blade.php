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
    </div>

    {{-- Proof Document Preview --}}
    <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-700">

        {{-- Document Header --}}
        <div class="px-6 py-5 flex items-center justify-between"
             style="background-color: {{ $proof->whitelabel?->color ?? '#0f766e' }};">
            <div class="flex items-center gap-4">
                @if($proof->whitelabel?->logo_link)
                    <img src="{{ asset($proof->whitelabel->logo_link) }}"
                         class="h-12 w-auto object-contain bg-white rounded-lg p-1.5 max-w-[120px]"
                         alt="{{ $proof->whitelabel->name }}">
                @else
                    <div class="h-12 w-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-bold text-white leading-tight">{{ $proof->whitelabel?->name ?? $label->name }}</h2>
                    @if($proof->proofType)
                        <p class="text-sm text-white/75">{{ $proof->proofType->name }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right text-white/80 text-sm space-y-0.5">
                @if($proof->proof_date)
                    <div class="font-medium text-white">{{ $proof->proof_date->format('d M Y') }}</div>
                @endif
                @if($proof->whatsapp_group)
                    <div>{{ $proof->whatsapp_group }}</div>
                @endif
            </div>
        </div>

        {{-- Proof Info Grid --}}
        <div class="bg-white dark:bg-gray-800 px-6 py-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3 text-sm mb-4">
                @if($proof->whitelabel)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Whitelabel: </span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $proof->whitelabel->name }}</span>
                </div>
                @endif
                @if($proof->sport)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Sport: </span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $proof->sport->name }}</span>
                </div>
                @endif
                @if($proof->agent_name)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Agent: </span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $proof->agent_name }}</span>
                </div>
                @endif
                @if($proof->event_name)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Event: </span>
                    <span class="text-gray-900 dark:text-gray-100 truncate">{{ $proof->event_name }}</span>
                </div>
                @endif
                @if($proof->user_name)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">User: </span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $proof->user_name }}</span>
                </div>
                @endif
                @if($proof->market_name)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Market: </span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $proof->market_name }}</span>
                </div>
                @endif
                @if($proof->amount !== null)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Amount: </span>
                    <span class="text-gray-900 dark:text-gray-100 font-mono">{{ number_format($proof->amount, 0) }}</span>
                </div>
                @endif
                @if($proof->profit_loss !== null)
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">P/L: </span>
                    <span class="font-mono font-semibold @if($proof->profit_loss > 0) text-green-600 @elseif($proof->profit_loss < 0) text-red-600 @else text-gray-900 dark:text-gray-100 @endif">
                        {{ number_format($proof->profit_loss, 0) }}
                    </span>
                </div>
                @endif
            </div>

            {{-- Proof Type HTML Content --}}
            @if($templateHtml)
            <div class="prose prose-sm max-w-none dark:prose-invert text-sm text-gray-800 dark:text-gray-200 leading-relaxed border-t border-gray-100 dark:border-gray-700 pt-4">
                {!! $templateHtml !!}
            </div>
            @endif

            {{-- Navigation --}}
            @if($proof->navigation)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Navigation</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $proof->navigation }}</p>
            </div>
            @endif

            {{-- Navigation 2 --}}
            @if($proof->navigation2)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Navigation 2</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $proof->navigation2 }}</p>
            </div>
            @endif

            {{-- Images --}}
            @if(!empty($proof->images))
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
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

            {{-- Nav2 Images --}}
            @if(!empty($proof->navigation2_images))
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
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
        </div>

        {{-- Document Footer --}}
        <div class="px-6 py-3 flex items-center justify-between text-xs text-white/75"
             style="background-color: {{ $proof->whitelabel?->color ?? '#0f766e' }};">
            <span>{{ $proof->whitelabel?->domain ?? $label->name }}</span>
            <span>#{{ $proof->id }}</span>
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
