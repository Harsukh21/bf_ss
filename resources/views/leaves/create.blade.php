@extends('layouts.app')

@section('title', 'Apply for Leave')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('leaves.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Apply for Leave</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Submit a new leave request</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('leaves.store') }}">
            @csrf

            {{-- Leave Type --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Leave Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($leaveTypes as $code => $label)
                        <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition
                            {{ old('leave_type') === $code ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-primary-300' }}">
                            <input type="radio" name="leave_type" value="{{ $code }}" class="text-primary-600" {{ old('leave_type') === $code ? 'checked' : '' }}>
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
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date <span class="text-red-500">*</span></label>
                    <input type="date" name="from_date" value="{{ old('from_date') }}" min="{{ today()->toDateString() }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('from_date') border-red-500 @enderror"
                        onchange="calcDays()">
                    @error('from_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date <span class="text-red-500">*</span></label>
                    <input type="date" name="to_date" value="{{ old('to_date') }}" min="{{ today()->toDateString() }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('to_date') border-red-500 @enderror"
                        onchange="calcDays()">
                    @error('to_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Working Days Display --}}
            <div id="daysInfo" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-800 dark:text-blue-300 hidden">
                <span id="daysCount" class="font-bold"></span> working day(s) applied
            </div>

            {{-- Reason --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="4" placeholder="Briefly explain the reason for your leave request..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Submit Request</button>
                <a href="{{ route('leaves.index') }}" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
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
</script>
@endpush
@endsection
