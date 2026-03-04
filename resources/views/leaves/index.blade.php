@extends('layouts.app')

@section('title', 'My Leaves')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="hover:text-green-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Leaves</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your leave requests</p>
        </div>
        <a href="{{ route('leaves.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Apply Leave
        </a>
    </div>

    {{-- Leave Balance Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach(['L' => ['label' => 'Casual Leave', 'color' => 'blue'], 'SL' => ['label' => 'Sick Leave', 'color' => 'red'], 'H' => ['label' => 'Holiday', 'color' => 'purple'], 'CO' => ['label' => 'Comp Off', 'color' => 'green']] as $type => $info)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $info['label'] }}</p>
            <p class="text-3xl font-bold text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-400 mt-1">{{ $leaveCounts[$type] }}</p>
            <p class="text-xs text-gray-400 mt-1">days used this year</p>
        </div>
        @endforeach
    </div>

    {{-- Leave Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">From</th>
                        <th class="px-4 py-3">To</th>
                        <th class="px-4 py-3">Days</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Applied</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs font-bold rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $leave->leave_type }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ $leave->leave_type_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $leave->from_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $leave->to_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $leave->total_days }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $leave->reason }}</td>
                            <td class="px-4 py-3">{!! $leave->status_badge !!}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $leave->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 flex items-center gap-2">
                                <a href="{{ route('leaves.show', $leave) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">View</a>
                                @if($leave->status === 'pending')
                                    <form method="POST" action="{{ route('leaves.cancel', $leave) }}" onsubmit="return confirm('Cancel this leave request?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No leave requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
