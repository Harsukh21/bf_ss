@extends('layouts.public')

@section('title', 'API Runner')

@push('css')
<style>
    #mainNav { display: none; }
    main { padding-top: 0 !important; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4">
    <div class="w-[99%] max-w-full mx-auto px-2 md:px-4">
        <div class="flex gap-4">
            <!-- Left sidebar -->
            <div class="w-64 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wide">api4data</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <button type="button" class="preset-btn w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-primary-50 dark:hover:bg-gray-700 transition"
                        data-url="https://api4data.com/api/client/eventList"
                        data-method="GET">
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-green-100 text-green-700">GET</span>
                        <span class="text-sm text-gray-800 dark:text-gray-100 truncate">/api/client/eventList</span>
                    </button>
                    <button type="button" class="preset-btn w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-primary-50 dark:hover:bg-gray-700 transition"
                        data-url="https://api4data.com/api/client/marketList"
                        data-method="POST">
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-100 text-orange-700">POST</span>
                        <span class="text-sm text-gray-800 dark:text-gray-100 truncate">/api/client/marketList</span>
                    </button>
                    <button type="button" class="preset-btn w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-primary-50 dark:hover:bg-gray-700 transition"
                        data-url="https://api4data.com/api/client/marketPrice"
                        data-method="POST">
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-100 text-orange-700">POST</span>
                        <span class="text-sm text-gray-800 dark:text-gray-100 truncate">/api/client/marketPrice</span>
                    </button>
                </div>
            </div>

            <!-- Right content -->
            <div class="flex-1 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 space-y-4">
                    <form id="apiRunnerForm" class="space-y-4">
                        @csrf
                        <div class="flex flex-wrap items-center gap-3">
                            <select id="methodInput" name="method" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none py-2.5 px-3">
                                <option>GET</option>
                                <option>POST</option>
                                <option>PUT</option>
                                <option>PATCH</option>
                                <option>DELETE</option>
                            </select>
                            <input id="urlInput" name="url" type="url" required placeholder="https://api.example.com/v1/resource" class="flex-1 min-w-[240px] rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none py-2.5 px-3" />
                            <button id="submitButton" type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:outline-none transition-all">
                                Send
                            </button>
                        </div>
                        <div class="flex gap-2 text-sm">
                            <button type="button" id="bodyTab" class="tab-btn px-4 py-2 rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200 font-medium">Body</button>
                            <button type="button" id="headersTab" class="tab-btn px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Headers</button>
                        </div>
                        <div>
                            <div id="bodyPanel">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">raw / JSON</span>
                                </div>
                                <textarea id="payloadInput" name="payload" rows="10" class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none p-3 font-mono"></textarea>
                            </div>
                            <div id="headersPanel" class="hidden">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">JSON or key:value per line</span>
                                </div>
                                <textarea id="headersInput" name="headers" rows="10" placeholder="Authorization: Bearer token&#10;Accept: application/json" class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none p-3 font-mono"></textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div id="errorBox" class="hidden text-sm text-red-600 dark:text-red-400"></div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-2 text-gray-500 dark:text-gray-400">Status: <span id="statusBadge" class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Pending</span></span>
                                <span class="flex items-center gap-2 text-gray-500 dark:text-gray-400">Time: <span id="timeBadge" class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">-- ms</span></span>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Response Body</h3>
                        <button id="copyResponseButton" type="button" class="inline-flex items-center gap-2 text-xs font-semibold text-primary-700 dark:text-primary-200 bg-primary-50 dark:bg-primary-900/40 px-3 py-1.5 rounded-md border border-primary-200 dark:border-primary-800 hover:bg-primary-100 dark:hover:bg-primary-800 transition-colors">
                            Copy
                            <span id="copyStatus" class="text-[11px] font-normal text-gray-500 dark:text-gray-400 hidden">Copied</span>
                        </button>
                    </div>
                    <textarea id="responseBody" rows="10" readonly class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:outline-none p-3 font-mono"></textarea>

                    <div>
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Response Headers</h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Read-only</span>
                        </div>
                        <pre id="responseHeaders" class="mt-2 h-48 overflow-y-auto rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-xs p-3 font-mono whitespace-pre-wrap"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('apiRunnerForm');
    const urlInput = document.getElementById('urlInput');
    const methodInput = document.getElementById('methodInput');
    const payloadInput = document.getElementById('payloadInput');
    const headersInput = document.getElementById('headersInput');
    const responseBody = document.getElementById('responseBody');
    const responseHeaders = document.getElementById('responseHeaders');
    const statusBadge = document.getElementById('statusBadge');
    const timeBadge = document.getElementById('timeBadge');
    const submitButton = document.getElementById('submitButton');
    const errorBox = document.getElementById('errorBox');
    const copyResponseButton = document.getElementById('copyResponseButton');
    const copyStatus = document.getElementById('copyStatus');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const presetButtons = document.querySelectorAll('.preset-btn');
    const bodyTab = document.getElementById('bodyTab');
    const headersTab = document.getElementById('headersTab');
    const bodyPanel = document.getElementById('bodyPanel');
    const headersPanel = document.getElementById('headersPanel');

    const setLoading = (isLoading) => {
        submitButton.disabled = isLoading;
        submitButton.classList.toggle('opacity-70', isLoading);
        submitButton.textContent = isLoading ? 'Sending...' : 'Send';
    };

    const applyPreset = (button) => {
        presetButtons.forEach(btn => btn.classList.remove('bg-primary-50', 'dark:bg-primary-900/30', 'border-l-4', 'border-primary-500'));
        button.classList.add('bg-primary-50', 'dark:bg-primary-900/30', 'border-l-4', 'border-primary-500');

        const presetMethod = button.dataset.method || 'GET';
        const presetUrl = button.dataset.url || '';

        methodInput.value = presetMethod;
        urlInput.value = presetUrl;
    };

    presetButtons.forEach(btn => {
        btn.addEventListener('click', () => applyPreset(btn));
    });

    if (presetButtons.length) {
        applyPreset(presetButtons[0]);
    }

    const activateTab = (tab) => {
        if (tab === 'body') {
            bodyTab.classList.add('bg-primary-50', 'text-primary-700', 'dark:bg-primary-900/40', 'dark:text-primary-200', 'font-medium');
            headersTab.classList.remove('bg-primary-50', 'text-primary-700', 'dark:bg-primary-900/40', 'dark:text-primary-200', 'font-medium');
            bodyPanel.classList.remove('hidden');
            headersPanel.classList.add('hidden');
        } else {
            headersTab.classList.add('bg-primary-50', 'text-primary-700', 'dark:bg-primary-900/40', 'dark:text-primary-200', 'font-medium');
            bodyTab.classList.remove('bg-primary-50', 'text-primary-700', 'dark:bg-primary-900/40', 'dark:text-primary-200', 'font-medium');
            headersPanel.classList.remove('hidden');
            bodyPanel.classList.add('hidden');
        }
    };

    bodyTab.addEventListener('click', () => activateTab('body'));
    headersTab.addEventListener('click', () => activateTab('headers'));
    activateTab('body');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        errorBox.classList.add('hidden');
        errorBox.textContent = '';
        responseBody.value = '';
        responseHeaders.textContent = '';
        statusBadge.textContent = 'Pending';
        timeBadge.textContent = '-- ms';

        setLoading(true);

        try {
            const response = await fetch('{{ route('postman.run') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    url: urlInput.value.trim(),
                    method: methodInput.value,
                    payload: payloadInput.value,
                    headers: headersInput.value,
                }),
            });

            const data = await response.json();
            const statusValue = data.status ?? response.status ?? 'Error';
            const validationMessage = data.errors ? Object.values(data.errors).flat().join(' ') : '';

            statusBadge.textContent = statusValue;
            timeBadge.textContent = data.duration_ms ? `${data.duration_ms} ms` : '-- ms';

            if (!response.ok || data.error || validationMessage) {
                const message = data.error || validationMessage || data.message || 'Request failed. Check the details and try again.';
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            }

            if (data.body_pretty || data.body) {
                responseBody.value = data.body_pretty || data.body;
            }

            if (data.headers) {
                responseHeaders.textContent = JSON.stringify(data.headers, null, 2);
            }
        } catch (error) {
            statusBadge.textContent = 'Error';
            errorBox.textContent = error.message || 'Unexpected error. Please try again.';
            errorBox.classList.remove('hidden');
        } finally {
            setLoading(false);
        }
    });

    copyResponseButton?.addEventListener('click', async () => {
        if (!responseBody.value) {
            return;
        }

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(responseBody.value);
            } else {
                responseBody.select();
                document.execCommand('copy');
                responseBody.setSelectionRange(0, 0);
            }

            if (copyStatus) {
                copyStatus.classList.remove('hidden');
                setTimeout(() => copyStatus.classList.add('hidden'), 1200);
            }
        } catch (error) {
            console.error('Copy failed', error);
        }
    });
});
</script>
@endpush
