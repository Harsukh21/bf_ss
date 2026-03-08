@extends('layouts.app')

@section('title', 'Employees')

@push('css')
<style>
.toast-notification {
    position: fixed; top: 20px; right: 20px; min-width: 260px;
    padding: 12px 16px; border-radius: 8px; color: #fff;
    background: rgba(31,41,55,0.95); box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    display: flex; align-items: center; gap: 10px; z-index: 2000;
    opacity: 0; transform: translateY(-10px);
    transition: opacity .2s ease, transform .2s ease;
}
.toast-notification.show   { opacity: 1; transform: translateY(0); }
.toast-notification.toast-success { background: rgba(5,150,105,0.95); }
.toast-notification.toast-error   { background: rgba(220,38,38,0.95); }
/* Filter Drawer */
#filterDrawer {
    position: fixed; top: 0; right: -420px; width: 400px; max-width: 95vw;
    height: 100vh; background: white; z-index: 1050;
    box-shadow: -4px 0 24px rgba(0,0,0,0.12);
    transition: right .3s ease; overflow-y: auto;
}
#filterDrawer.open { right: 0; }
.dark #filterDrawer { background: #1f2937; }
#filterOverlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.4);
    z-index: 1049; display: none;
}
#filterOverlay.show { display: block; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    @if(session('success'))
    <div id="successToast" class="toast-notification toast-success show">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white opacity-70 hover:opacity-100">×</button>
    </div>
    @endif

    @if(session('error'))
    <div id="errorToast" class="toast-notification toast-error show">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white opacity-70 hover:opacity-100">×</button>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Employees</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage all employee records</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openFilter()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filters
                @php $activeFilters = collect(['search','status','department','employment_type','gender','joining_from','joining_to'])->filter(fn($k)=>request()->filled($k))->count(); @endphp
                @if($activeFilters > 0)
                <span class="ml-2 px-1.5 py-0.5 text-xs font-bold rounded-full bg-primary-600 text-white">{{ $activeFilters }}</span>
                @endif
            </button>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Active</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">On Leave</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['on_leave'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wider">Resigned/Terminated</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['resigned'] }}</p>
        </div>
    </div>

    <!-- Active Filters -->
    @if($activeFilters > 0)
    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
        @if(request()->filled('search'))
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
            Search: {{ request('search') }}
        </span>
        @endif
        @if(request()->filled('status'))
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
            Status: {{ ucfirst(request('status')) }}
        </span>
        @endif
        @if(request()->filled('department'))
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
            Dept: {{ request('department') }}
        </span>
        @endif
        @if(request()->filled('employment_type'))
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
            Type: {{ ucfirst(request('employment_type')) }}
        </span>
        @endif
        <a href="{{ route('employees.index') }}" class="text-xs text-red-500 hover:text-red-700 underline">Clear all</a>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Designation</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joining Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($emp->photo_link)
                                    <img src="{{ $emp->photo_link }}" alt="{{ $emp->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-primary-600 dark:text-primary-400">{{ $emp->initials }}</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $emp->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $emp->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $emp->employee_id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $emp->department ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $emp->designation ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($emp->employment_type)
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                {{ $emp->employment_type_label }}
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colors = ['active'=>'green','on-leave'=>'yellow','resigned'=>'gray','terminated'=>'red'];
                                $c = $colors[$emp->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $c }}-100 dark:bg-{{ $c }}-900/30 text-{{ $c }}-700 dark:text-{{ $c }}-300">
                                {{ $emp->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $emp->joining_date ? $emp->joining_date->format('d M Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('employees.show', $emp) }}" class="p-1.5 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 rounded" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('employees.edit', $emp) }}" class="p-1.5 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($emp->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No employees found</p>
                            <a href="{{ route('employees.create') }}" class="mt-3 inline-flex items-center text-sm text-primary-600 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add your first employee
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Filter Overlay -->
<div id="filterOverlay" onclick="closeFilter()"></div>

<!-- Filter Drawer -->
<div id="filterDrawer" class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Employees</h3>
        <button onclick="closeFilter()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <form method="GET" action="{{ route('employees.index') }}" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, ID, email, dept..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All Statuses</option>
                <option value="active"     {{ request('status')=='active'     ? 'selected':'' }}>Active</option>
                <option value="on-leave"   {{ request('status')=='on-leave'   ? 'selected':'' }}>On Leave</option>
                <option value="resigned"   {{ request('status')=='resigned'   ? 'selected':'' }}>Resigned</option>
                <option value="terminated" {{ request('status')=='terminated' ? 'selected':'' }}>Terminated</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
            <select name="department" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department')==$dept ? 'selected':'' }}>{{ $dept }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employment Type</label>
            <select name="employment_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All Types</option>
                <option value="full-time" {{ request('employment_type')=='full-time' ? 'selected':'' }}>Full-time</option>
                <option value="part-time" {{ request('employment_type')=='part-time' ? 'selected':'' }}>Part-time</option>
                <option value="contract"  {{ request('employment_type')=='contract'  ? 'selected':'' }}>Contract</option>
                <option value="intern"    {{ request('employment_type')=='intern'    ? 'selected':'' }}>Intern</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
            <select name="gender" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                <option value="male"   {{ request('gender')=='male'   ? 'selected':'' }}>Male</option>
                <option value="female" {{ request('gender')=='female' ? 'selected':'' }}>Female</option>
                <option value="other"  {{ request('gender')=='other'  ? 'selected':'' }}>Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Joining Date From</label>
            <input type="date" name="joining_from" value="{{ request('joining_from') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Joining Date To</label>
            <input type="date" name="joining_to" value="{{ request('joining_to') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Apply Filters</button>
            <a href="{{ route('employees.index') }}" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Clear All</a>
        </div>
    </form>
</div>

@push('js')
<script>
function openFilter()  { document.getElementById('filterDrawer').classList.add('open'); document.getElementById('filterOverlay').classList.add('show'); }
function closeFilter() { document.getElementById('filterDrawer').classList.remove('open'); document.getElementById('filterOverlay').classList.remove('show'); }
setTimeout(() => { ['successToast','errorToast'].forEach(id => { const el = document.getElementById(id); if(el) setTimeout(()=>el.remove(), 4000); }); }, 100);
@if($activeFilters > 0) openFilter(); @endif
</script>
@endpush
@endsection
