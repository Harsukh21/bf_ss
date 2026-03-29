@php
$existingOriginals = old('originals', $report?->originals ?? []);
@endphp

<div class="space-y-4">

    {{-- Row 1: Date / User Name --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
            <input type="date" name="report_date"
                value="{{ old('report_date', $report?->report_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Name</label>
            <input type="text" name="user_name" value="{{ old('user_name', $report?->user_name) }}" placeholder="Enter User Name"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
    </div>

    {{-- Row 2: Agent / Origin --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agent</label>
            <input type="text" name="agent" value="{{ old('agent', $report?->agent) }}" placeholder="Enter Agent"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Origin</label>
            <input type="text" name="origin" value="{{ old('origin', $report?->origin) }}" placeholder="Enter Origin"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
    </div>

    {{-- Row 3: Before Void Balance / After Void Balance --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Before Void Balance</label>
            <input type="number" step="0.01" name="before_void_balance" value="{{ old('before_void_balance', $report?->before_void_balance) }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">After Void Balance</label>
            <input type="number" step="0.01" name="after_void_balance" value="{{ old('after_void_balance', $report?->after_void_balance) }}"
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
                <option value="Self" @selected(old('catch_by', $report?->catch_by) === 'Self')>Self</option>
                <option value="Agent" @selected(old('catch_by', $report?->catch_by) === 'Agent')>Agent</option>
                <option value="System" @selected(old('catch_by', $report?->catch_by) === 'System')>System</option>
                <option value="Risk Team" @selected(old('catch_by', $report?->catch_by) === 'Risk Team')>Risk Team</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Type</label>
            <select name="proof_type_id"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Select Proof Type</option>
                @foreach($proofTypes as $pt)
                    <option value="{{ $pt->id }}" @selected(old('proof_type_id', $report?->proof_type_id) == $pt->id)>{{ $pt->name }}</option>
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
                @foreach(['submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected', 'pending' => 'Pending'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('proof_status', $report?->proof_status ?? 'submitted') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Void Status</label>
            <select name="void_status"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">— Select —</option>
                <option value="Void" @selected(old('void_status', $report?->void_status) === 'Void')>Void</option>
                <option value="Not Void" @selected(old('void_status', $report?->void_status) === 'Not Void')>Not Void</option>
                <option value="Partial Void" @selected(old('void_status', $report?->void_status) === 'Partial Void')>Partial Void</option>
            </select>
        </div>
    </div>

    {{-- Remark --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
        <input type="text" name="remark" value="{{ old('remark', $report?->remark) }}" placeholder="Enter Remark"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- ===== ORIGINALS SECTION ===== --}}
    <div class="pt-2">
        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3">Originals</h2>

        <div id="originalsContainer" class="space-y-4">
            @if(!empty($existingOriginals))
                @foreach($existingOriginals as $oi => $orig)
                    @include('labels.modules._original_card', [
                        'oi'    => $oi,
                        'orig'  => $orig,
                        'sports'=> $sports,
                    ])
                @endforeach
            @else
                @include('labels.modules._original_card', [
                    'oi'    => 0,
                    'orig'  => [],
                    'sports'=> $sports,
                ])
            @endif
        </div>

        <button type="button" onclick="addOriginal()"
            class="mt-3 text-sm text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Original
        </button>
    </div>
</div>

{{-- Hidden template for new original --}}
<template id="originalTemplate">
    <div class="original-card border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-white dark:bg-gray-800/50">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Original <span class="orig-num"></span></h3>
            <button type="button" onclick="removeOriginal(this)" class="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sport Name</label>
                <select name="originals[__OI__][sport_name]"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">Select Sport</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Event Name</label>
                <input type="text" name="originals[__OI__][event_name]" placeholder="Enter Event Name"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Market Name</label>
                <input type="text" name="originals[__OI__][market_name]" placeholder="Enter Market Name"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">P&amp;L</label>
                <input type="number" step="0.01" name="originals[__OI__][pl]"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Bet Details</p>
            <div class="bet-details-container space-y-2">
                <div class="bet-detail-row grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label>
                        <input type="number" step="0.01" name="originals[__OI__][bet_details][0][odds]"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label>
                        <input type="number" step="0.01" name="originals[__OI__][bet_details][0][stack]"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label>
                        <input type="text" name="originals[__OI__][bet_details][0][time]" placeholder="HH:MM:SS"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>
            </div>
            <button type="button" onclick="addBetDetail(this)"
                class="mt-2 text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Bet Detail
            </button>
        </div>
    </div>
</template>

@push('js')
<script>
let origIndex = {{ count($existingOriginals) > 0 ? count($existingOriginals) : 1 }};

// Sports options for dynamic injection
const sportsData = @json($sports->pluck('name'));

function buildSportsOptions(selectedValue) {
    let html = '<option value="">Select Sport</option>';
    sportsData.forEach(name => {
        const sel = name === selectedValue ? ' selected' : '';
        html += `<option value="${name}"${sel}>${name}</option>`;
    });
    return html;
}

function addOriginal() {
    const container = document.getElementById('originalsContainer');
    const template  = document.getElementById('originalTemplate');
    const html      = template.innerHTML.replace(/__OI__/g, origIndex);

    const div = document.createElement('div');
    div.innerHTML = html;
    const card = div.firstElementChild;
    card.querySelector('.orig-num').textContent = origIndex + 1;
    // Inject sport options
    const sel = card.querySelector('select[name*="sport_name"]');
    if (sel) sel.innerHTML = buildSportsOptions('');
    container.appendChild(card);
    origIndex++;
    renumberOriginals();
}

function removeOriginal(btn) {
    btn.closest('.original-card').remove();
    renumberOriginals();
}

function renumberOriginals() {
    document.querySelectorAll('#originalsContainer .original-card').forEach((card, i) => {
        card.querySelector('.orig-num').textContent = i + 1;
        // Re-index all name attributes
        card.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/originals\[\d+\]/, `originals[${i}]`);
        });
    });
}

function addBetDetail(btn) {
    const card = btn.closest('.original-card');
    const betContainer = card.querySelector('.bet-details-container');
    const oi = card.querySelectorAll('[name]')[0]?.name.match(/originals\[(\d+)\]/)?.[1] ?? 0;
    const bi = betContainer.querySelectorAll('.bet-detail-row').length;

    const row = document.createElement('div');
    row.className = 'bet-detail-row grid grid-cols-3 gap-3 relative';
    row.innerHTML = `
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label>
            <input type="number" step="0.01" name="originals[${oi}][bet_details][${bi}][odds]"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label>
            <input type="number" step="0.01" name="originals[${oi}][bet_details][${bi}][stack]"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
        <div class="relative">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label>
            <input type="text" name="originals[${oi}][bet_details][${bi}][time]" placeholder="HH:MM:SS"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
            <button type="button" onclick="this.closest('.bet-detail-row').remove()"
                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none">×</button>
        </div>
    `;
    betContainer.appendChild(row);
}
</script>
@endpush
