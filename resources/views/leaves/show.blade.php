@extends('layouts.app')

@section('title', 'Leave Details')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('leaves.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Leave Details</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">

            {{-- Type & Status Header --}}
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ $leave->leave_type }}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $leave->leave_type_label }}</span>
                </div>
                {!! $leave->status_badge !!}
            </div>

            {{-- Details --}}
            <dl class="space-y-1">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $leave->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Working Days</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $leave->total_days }} day(s)</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">From</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $leave->from_date->format('D, d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">To</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ $leave->to_date->format('D, d M Y') }}</dd>
                    </div>
                </div>

                <div class="py-3 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Reason</dt>
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
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Admin Notes</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">{{ $leave->admin_notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($leave->status === 'pending' && $leave->user_id === auth()->id())
        <div class="px-4 py-4 sm:px-6 border-t border-gray-200 dark:border-gray-700">
            <form method="POST" action="{{ route('leaves.cancel', $leave) }}" onsubmit="return confirm('Cancel this leave request?')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel Request
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
