@extends('layouts.public')

@section('title', 'API Runner')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">API Runner</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Call any API from the server and inspect the response directly in this page.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="px-3 py-1 rounded-full bg-primary-50 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200">Server-side</span>
                        <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700">Postman-style</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <form id="apiRunnerForm" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="methodInput" class="text-sm font-medium text-gray-700 dark:text-gray-200">Method</label>
                            <select id="methodInput" name="method" class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none py-2.5 px-3">
                                <option>GET</option>
                                <option>POST</option>
                                <option>PUT</option>
                                <option>PATCH</option>
                                <option>DELETE</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label for="urlInput" class="text-sm font-medium text-gray-700 dark:text-gray-200">URL</label>
                            <input id="urlInput" name="url" type="url" required placeholder="https://api.example.com/v1/resource" class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none py-2.5 px-3" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <label for="payloadInput" class="text-sm font-medium text-gray-700 dark:text-gray-200">Data</label>
                                <span class="text-xs text-gray-500 dark:text-gray-400">JSON or raw text</span>
                            </div>
                            <textarea id="payloadInput" name="payload" rows="6" placeholder='{"example":"value"}' class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none p-3"></textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <label for="headersInput" class="text-sm font-medium text-gray-700 dark:text-gray-200">Headers</label>
                                <span class="text-xs text-gray-500 dark:text-gray-400">JSON or key:value per line</span>
                            </div>
                            <textarea id="headersInput" name="headers" rows="6" placeholder="Authorization: Bearer token&#10;Accept: application/json" class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none p-3"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <div id="errorBox" class="hidden text-sm text-red-600 dark:text-red-400"></div>
                        <button id="submitButton" type="submit" class="ml-auto inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 text-white text-sm font-semibold rounded-lg shadow hover:from-primary-700 hover:to-purple-700 focus:ring-2 focus:ring-primary-500 focus:outline-none transition-all">
                            Send Request
                        </button>
                    </div>
                </form>

                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Status:</span>
                            <span id="statusBadge" class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs">Pending</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Time:</span>
                            <span id="timeBadge" class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs">-- ms</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between">
                                <label for="responseBody" class="text-sm font-medium text-gray-700 dark:text-gray-200">Response</label>
                                <button id="copyResponseButton" type="button" class="inline-flex items-center gap-2 text-xs font-semibold text-primary-700 dark:text-primary-200 bg-primary-50 dark:bg-primary-900/40 px-3 py-1.5 rounded-md border border-primary-200 dark:border-primary-800 hover:bg-primary-100 dark:hover:bg-primary-800 transition-colors">
                                    Copy
                                    <span id="copyStatus" class="text-[11px] font-normal text-gray-500 dark:text-gray-400 hidden">Copied</span>
                                </button>
                            </div>
                            <textarea id="responseBody" rows="12" readonly class="mt-2 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:outline-none p-3 font-mono"></textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Response Headers</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Read-only</span>
                            </div>
                            <pre id="responseHeaders" class="mt-2 h-[234px] overflow-y-auto rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-xs p-3 font-mono whitespace-pre-wrap"></pre>
                        </div>
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

    const setLoading = (isLoading) => {
        submitButton.disabled = isLoading;
        submitButton.classList.toggle('opacity-70', isLoading);
        submitButton.textContent = isLoading ? 'Sending...' : 'Send Request';
    };

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
