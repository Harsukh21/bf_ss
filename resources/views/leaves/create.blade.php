@push('css')
<style>
.toast-notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 280px;
    max-width: 420px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05);
    transform: translateX(calc(100% + 1rem));
    transition: transform .3s ease;
}
.toast-notification.show { transform: translateX(0); }
.toast-error { background:#991b1b; color:#fff; }
</style>
@endpush

@extends('layouts.app')

@section('title', 'Apply for Leave')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    @if(session('error'))
    <div id="errorToast" class="toast-notification toast-error show">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
        <button onclick="this.closest('.toast-notification').classList.remove('show')" class="ml-auto opacity-70 hover:opacity-100">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('leaves.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Apply for Leave</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Submit a new leave request</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form method="POST" action="{{ route('leaves.store') }}">
            @csrf
            <div class="px-4 py-5 sm:p-6 space-y-5">

                {{-- Leave Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($leaveTypes as $code => $label)
                            <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition
                                {{ old('leave_type') === $code ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600' }}">
                                <input type="radio" name="leave_type" value="{{ $code }}" class="text-primary-600 focus:ring-primary-500" {{ old('leave_type') === $code ? 'checked' : '' }}>
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $code }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">— {{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('leave_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            From Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="from_date" value="{{ old('from_date') }}" min="{{ today()->toDateString() }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('from_date') border-red-500 @enderror"
                            onchange="calcDays()">
                        @error('from_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            To Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="to_date" value="{{ old('to_date') }}" min="{{ today()->toDateString() }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('to_date') border-red-500 @enderror"
                            onchange="calcDays()">
                        @error('to_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Working Days Preview --}}
                <div id="daysInfo" class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-800 dark:text-blue-300 hidden">
                    <span id="daysCount" class="font-bold"></span> working day(s) applied
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" placeholder="Briefly explain the reason for your leave request..."
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 px-4 py-4 sm:px-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('leaves.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calcDays() {
    const from = document.querySelector('[name="from_date"]').value;
    const to   = document.querySelector('[name="to_date"]').value;
    const info = document.getElementById('daysInfo');
    const cnt  = document.getElementById('daysCount');
    if (!from || !to) { info.classList.add('hidden'); return; }
    const start = new Date(from), end = new Date(to);
    if (end < start) { info.classList.add('hidden'); return; }
    let days = 0;
    const cur = new Date(start);
    while (cur <= end) {
        const dow = cur.getDay();
        if (dow !== 0 && dow !== 6) days++;
        cur.setDate(cur.getDate() + 1);
    }
    cnt.textContent = days;
    info.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('errorToast');
    if (el) setTimeout(function() { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }, 5000);
});
</script>
@endpush
@endsection
