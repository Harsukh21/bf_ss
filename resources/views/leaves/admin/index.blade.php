@extends('layouts.app')

@section('title', 'Leave Requests — Admin')

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Leave Requests</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Review and manage employee leave requests</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-5">
        <form method="GET" action="{{ route('leaves.admin.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Employee</label>
                <select name="user_id" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Employees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','rejected','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Leave Type</label>
                <select name="leave_type" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $code => $label)
                        <option value="{{ $code }}" @selected(request('leave_type') == $code)>{{ $code }} — {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg">Filter</button>
            <a href="{{ route('leaves.admin.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Clear</a>
        </form>
    </div>

    {{-- Leave Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Period</th>
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
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $leave->user->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-bold rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $leave->leave_type }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $leave->from_date->format('d M') }} – {{ $leave->to_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $leave->total_days }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $leave->reason }}</td>
                            <td class="px-4 py-3">{!! $leave->status_badge !!}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $leave->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($leave->status === 'pending')
                                    <div class="flex gap-2">
                                        {{-- Approve --}}
                                        <form method="POST" action="{{ route('leaves.approve', $leave) }}" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this leave?')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium">Approve</button>
                                        </form>
                                        {{-- Reject --}}
                                        <button onclick="openRejectModal({{ $leave->id }})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg font-medium">Reject</button>
                                    </div>
                                @else
                                    <a href="{{ route('leaves.show', $leave) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No leave requests found.</td>
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

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reject Leave Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for rejection (optional)</label>
                <textarea name="admin_notes" rows="3" placeholder="Provide a reason..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">Reject</button>
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(leaveId) {
    document.getElementById('rejectForm').action = `/leaves/${leaveId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
@endsection
