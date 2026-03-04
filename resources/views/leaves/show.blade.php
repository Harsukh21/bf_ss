@extends('layouts.app')

@section('title', 'Leave Details')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('leaves.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Leave Details</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <span class="inline-block px-3 py-1 text-sm font-bold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ $leave->leave_type }}</span>
                <span class="ml-2 text-gray-600 dark:text-gray-400">{{ $leave->leave_type_label }}</span>
            </div>
            {!! $leave->status_badge !!}
        </div>

        <dl class="space-y-4">
            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Employee</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $leave->user->name }}</dd>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Period</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $leave->from_date->format('d M Y') }} – {{ $leave->to_date->format('d M Y') }}
                </dd>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Working Days</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $leave->total_days }} day(s)</dd>
            </div>
            <div class="py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400 mb-1">Reason</dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $leave->reason }}</dd>
            </div>
            @if($leave->approved_at)
            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($leave->status) }} By</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $leave->approver?->name ?? 'N/A' }} on {{ $leave->approved_at->format('d M Y, h:i A') }}
                </dd>
            </div>
            @endif
            @if($leave->admin_notes)
            <div class="py-3">
                <dt class="text-sm text-gray-500 dark:text-gray-400 mb-1">Admin Notes</dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">{{ $leave->admin_notes }}</dd>
            </div>
            @endif
        </dl>

        @if($leave->status === 'pending' && $leave->user_id === auth()->id())
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <form method="POST" action="{{ route('leaves.cancel', $leave) }}" onsubmit="return confirm('Cancel this leave request?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Cancel Request</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
