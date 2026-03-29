@extends('layouts.app')
@section('title', 'Reports — ' . $label->name)
@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Report Management</h1>
        <button id="filterBtn" onclick="toggleFilter()"
            class="p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700" title="Filters">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </button>
    </div>

    {{-- Session messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex-wrap">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @if($reports->total() > 0)
                    Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                @else
                    No entries found
                @endif
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('labels.reports', $label) }}" class="flex gap-2">
                    @foreach(request()->except(['search','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 w-44">
                    @if(request('search'))
                        <a href="{{ route('labels.reports', $label) }}" class="px-3 py-1.5 text-sm text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">✕</a>
                    @endif
                </form>
                <a href="{{ route('labels.reports.export', array_merge(['label' => $label->id], request()->all())) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-teal-500 hover:bg-teal-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Records
                </a>
                <a href="{{ route('labels.reports.create', $label) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gray-900 dark:bg-primary-600 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Record
                </a>
            </div>
        </div>

        @if($reports->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-3 py-3 text-left whitespace-nowrap">Date
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">User Name
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Agent
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Origin
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Sport Name
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Event Name
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Market Name
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">P&amp;L
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Odds
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Stack
                            <svg class="inline w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Time</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($reports as $report)
                        @php
                            $originals = $report->originals ?? [];
                            // Flatten to rows: [orig_index, bet_index]
                            $rows = [];
                            foreach ($originals as $oi => $orig) {
                                $bets = $orig['bet_details'] ?? [[]];
                                foreach ($bets as $bi => $bet) {
                                    $rows[] = ['orig' => $orig, 'bet' => $bet, 'first' => ($oi === 0 && $bi === 0)];
                                }
                            }
                            if (empty($rows)) $rows[] = ['orig' => [], 'bet' => [], 'first' => true];
                            $rowCount = count($rows);
                        @endphp
                        @foreach($rows as $ri => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            @if($ri === 0)
                            {{-- Main report data shown on first row, spanning all bet rows --}}
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap text-xs">
                                {{ $report->report_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap font-medium">
                                {{ $report->user_name ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $report->agent ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $report->origin ?? '—' }}
                            </td>
                            @else
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2"></td>
                            @endif
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $row['orig']['sport_name'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 max-w-[170px] truncate">
                                {{ $row['orig']['event_name'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $row['orig']['market_name'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap font-mono @if(($row['orig']['pl'] ?? 0) > 0) text-green-600 @elseif(($row['orig']['pl'] ?? 0) < 0) text-red-600 @else text-gray-600 dark:text-gray-300 @endif">
                                {{ isset($row['orig']['pl']) ? number_format($row['orig']['pl'], 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap font-mono text-gray-700 dark:text-gray-200">
                                {{ $row['bet']['odds'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap font-mono text-gray-700 dark:text-gray-200">
                                {{ isset($row['bet']['stack']) ? number_format($row['bet']['stack'], 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs font-mono">
                                {{ $row['bet']['time'] ?? '—' }}
                            </td>
                            @if($ri === 0)
                            <td class="px-3 py-2" rowspan="{{ $rowCount }}">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('labels.reports.edit', [$label, $report]) }}"
                                        class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('labels.reports.destroy', [$label, $report]) }}"
                                          data-confirm="Delete this report? This cannot be undone."
                                          data-confirm-text="Delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}</div>
            <div class="flex items-center gap-1">
                @if($reports->onFirstPage())
                    <span class="px-2 py-1.5 text-gray-400 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                @else
                    <a href="{{ $reports->previousPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                @endif
                <span class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded">Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}</span>
                @if($reports->hasMorePages())
                    <a href="{{ $reports->nextPageUrl() }}" class="px-2 py-1.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                @else
                    <span class="px-2 py-1.5 text-gray-400 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                @endif
            </div>
        </div>
        @endif

        @else
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-3">No report records found.</p>
            <a href="{{ route('labels.reports.create', $label) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-primary-600 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Record
            </a>
        </div>
        @endif
    </div>
</div>

{{-- ========= FILTER PANEL (right slide-in) ========= --}}
<div id="filterOverlay" class="fixed inset-0 z-[800] hidden" onclick="closeFilter()"></div>
<div id="filterPanel"
    class="fixed top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-2xl z-[801] overflow-y-auto"
    style="transform:translateX(100%);transition:transform 0.25s ease;">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Filters</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('labels.reports', $label) }}" class="px-3 py-1.5 text-xs font-medium bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">Reset Filters</a>
            <button onclick="closeFilter()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <form method="GET" action="{{ route('labels.reports', $label) }}" class="p-5 space-y-4">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">User Name</label>
                <input type="text" name="f_user" value="{{ request('f_user') }}" placeholder=""
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Agent</label>
                <input type="text" name="f_agent" value="{{ request('f_agent') }}" placeholder=""
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Origin</label>
                <input type="text" name="f_origin" value="{{ request('f_origin') }}" placeholder=""
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sport Name</label>
                <select name="f_sport"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach($sports as $sp)
                        <option value="{{ $sp->name }}" @selected(request('f_sport') == $sp->name)>{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Event Name</label>
                <input type="text" name="f_event" value="{{ request('f_event') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Market Name</label>
                <input type="text" name="f_market" value="{{ request('f_market') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">P&amp;L Min</label>
                <input type="number" step="0.01" name="pl_min" value="{{ request('pl_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">P&amp;L Max</label>
                <input type="number" step="0.01" name="pl_max" value="{{ request('pl_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Odds Min</label>
                <input type="number" step="0.01" name="odds_min" value="{{ request('odds_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Odds Max</label>
                <input type="number" step="0.01" name="odds_max" value="{{ request('odds_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Stack Min</label>
                <input type="number" step="0.01" name="stack_min" value="{{ request('stack_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Stack Max</label>
                <input type="number" step="0.01" name="stack_max" value="{{ request('stack_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Before Void Balance Min</label>
                <input type="number" step="0.01" name="bvb_min" value="{{ request('bvb_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Before Void Balance Max</label>
                <input type="number" step="0.01" name="bvb_max" value="{{ request('bvb_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">After Void Balance Min</label>
                <input type="number" step="0.01" name="avb_min" value="{{ request('avb_min') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">After Void Balance Max</label>
                <input type="number" step="0.01" name="avb_max" value="{{ request('avb_max') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catch By</label>
                <input type="text" name="f_catch_by" value="{{ request('f_catch_by') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Type</label>
                <select name="f_proof_type"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach($proofTypes as $pt)
                        <option value="{{ $pt->id }}" @selected(request('f_proof_type') == $pt->id)>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Apply Filters</button>
        </div>
    </form>
</div>

@push('js')
<script>
function toggleFilter() {
    const panel = document.getElementById('filterPanel');
    const overlay = document.getElementById('filterOverlay');
    const isOpen = panel.style.transform === 'translateX(0%)';
    if (isOpen) {
        closeFilter();
    } else {
        overlay.classList.remove('hidden');
        panel.style.transform = 'translateX(0%)';
        document.body.style.overflow = 'hidden';
    }
}
function closeFilter() {
    document.getElementById('filterPanel').style.transform = 'translateX(100%)';
    document.getElementById('filterOverlay').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFilter(); });

// Auto-open filter panel if any filter is active
@if(request()->hasAny(['date_from','date_to','f_user','f_agent','f_origin','f_sport','f_event','f_market','pl_min','pl_max','odds_min','odds_max','stack_min','stack_max','bvb_min','bvb_max','avb_min','avb_max','f_catch_by','f_proof_type']))
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(toggleFilter, 100);
    });
@endif
</script>
@endpush
@endsection
