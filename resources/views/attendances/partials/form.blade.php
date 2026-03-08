@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single{border:1px solid #d1d5db;border-radius:.5rem;height:38px;background-color:#fff}
.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px;padding-left:12px;font-size:.875rem;color:#111827}
.select2-container--default .select2-selection--single .select2-selection__arrow{height:36px}
.dark .select2-container--default .select2-selection--single{background-color:#374151;border-color:#4b5563}
.dark .select2-container--default .select2-selection--single .select2-selection__rendered{color:#f3f4f6}
.dark .select2-dropdown{background-color:#1f2937;border-color:#4b5563}
.dark .select2-container--default .select2-results__option{color:#f3f4f6}
.dark .select2-container--default .select2-results__option--highlighted[aria-selected]{background-color:#4f46e5}
.dark .select2-search--dropdown .select2-search__field{background-color:#374151;border-color:#4b5563;color:#f3f4f6}
</style>
@endpush

@php
$att = $attendance ?? null;
$inp    = 'w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent';
$inpErr = 'w-full rounded-lg border border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent';
$lbl    = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
@endphp

{{-- Employee --}}
<div class="mb-5">
    <label class="{{ $lbl }}">Employee <span class="text-red-500">*</span></label>
    <select name="employee_id" id="employee_id" class="{{ $errors->has('employee_id') ? $inpErr : $inp }}">
        <option value="">— Select Employee —</option>
        @foreach($employees as $emp)
            <option value="{{ $emp->id }}"
                {{ old('employee_id', $att?->employee_id) == $emp->id ? 'selected' : '' }}>
                {{ $emp->name }} ({{ $emp->employee_id }})
            </option>
        @endforeach
    </select>
    @error('employee_id')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>

{{-- Date & Status --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label class="{{ $lbl }}">Date <span class="text-red-500">*</span></label>
        <input type="date" name="date" value="{{ old('date', $att?->date?->format('Y-m-d')) }}"
               class="{{ $errors->has('date') ? $inpErr : $inp }}">
        @error('date')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $lbl }}">Status <span class="text-red-500">*</span></label>
        <select name="status" class="{{ $errors->has('status') ? $inpErr : $inp }}">
            @foreach(['present'=>'Present','absent'=>'Absent','half_day'=>'Half Day','late'=>'Late','on_leave'=>'On Leave','holiday'=>'Holiday'] as $val => $label)
                <option value="{{ $val }}" {{ old('status', $att?->status ?? 'present') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Time Section --}}
<div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 mb-5">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Work Hours</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="{{ $lbl }}">Start Time</label>
            <input type="time" name="start_time" value="{{ old('start_time', $att?->start_time ? \Carbon\Carbon::parse($att->start_time)->format('H:i') : '') }}"
                   class="{{ $errors->has('start_time') ? $inpErr : $inp }}"
                   onchange="calcWorkingTime()">
            @error('start_time')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">End Time</label>
            <input type="time" name="end_time" value="{{ old('end_time', $att?->end_time ? \Carbon\Carbon::parse($att->end_time)->format('H:i') : '') }}"
                   class="{{ $errors->has('end_time') ? $inpErr : $inp }}"
                   onchange="calcWorkingTime()">
            @error('end_time')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Break Section --}}
<div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 mb-5">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Break Time <span class="text-xs font-normal text-gray-500">(optional)</span></h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="{{ $lbl }}">Break Start</label>
            <input type="time" name="start_break_time" value="{{ old('start_break_time', $att?->start_break_time ? \Carbon\Carbon::parse($att->start_break_time)->format('H:i') : '') }}"
                   class="{{ $errors->has('start_break_time') ? $inpErr : $inp }}"
                   onchange="calcWorkingTime()">
            @error('start_break_time')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $lbl }}">Break End</label>
            <input type="time" name="end_break_time" value="{{ old('end_break_time', $att?->end_break_time ? \Carbon\Carbon::parse($att->end_break_time)->format('H:i') : '') }}"
                   class="{{ $errors->has('end_break_time') ? $inpErr : $inp }}"
                   onchange="calcWorkingTime()">
            @error('end_break_time')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Live Working Time Preview --}}
<div id="workingTimePreview" class="hidden mb-5 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">Calculated Working Time</p>
            <p id="workingTimeValue" class="text-lg font-bold text-primary-700 dark:text-primary-300">—</p>
        </div>
    </div>
</div>

{{-- Note --}}
<div class="mb-6">
    <label class="{{ $lbl }}">Note</label>
    <textarea name="note" rows="3" placeholder="Any remarks or notes..."
              class="{{ $errors->has('note') ? $inpErr : $inp }}">{{ old('note', $att?->note) }}</textarea>
    @error('note')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>

{{-- Actions --}}
<div class="flex items-center {{ isset($deleteConfirm) ? 'justify-between' : 'justify-end' }} gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
    @if(isset($deleteConfirm))
    <button type="button"
            onclick="if(confirm('{{ $deleteConfirm }}')) document.getElementById('att-delete-form').submit()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Delete
    </button>
    @endif
    <div class="flex items-center gap-3">
        <a href="{{ $cancelUrl }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
        </a>
        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
            {{ $submitLabel }}
        </button>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#employee_id').select2({
        placeholder: '— Search Employee —',
        allowClear: true,
        width: '100%',
    });
});

function calcWorkingTime() {
    const start = document.querySelector('[name="start_time"]').value;
    const end   = document.querySelector('[name="end_time"]').value;
    const bs    = document.querySelector('[name="start_break_time"]').value;
    const be    = document.querySelector('[name="end_break_time"]').value;
    const preview = document.getElementById('workingTimePreview');
    const display = document.getElementById('workingTimeValue');

    if (!start || !end) { preview.classList.add('hidden'); return; }

    const toMin = t => { const [h,m] = t.split(':').map(Number); return h*60+m; };
    let total = toMin(end) - toMin(start);
    if (total <= 0) { preview.classList.add('hidden'); return; }

    if (bs && be) {
        const brk = toMin(be) - toMin(bs);
        if (brk > 0) total -= brk;
    }

    total = Math.max(0, total);
    const h = Math.floor(total / 60);
    const m = total % 60;
    display.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':00';
    preview.classList.remove('hidden');
}
// Run on page load for edit form
document.addEventListener('DOMContentLoaded', calcWorkingTime);
</script>
@endpush
