<div class="original-card border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-white dark:bg-gray-800/50">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Original <span class="orig-num">{{ $oi + 1 }}</span></h3>
        <button type="button" onclick="removeOriginal(this)" class="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>

    {{-- Sport / Event / Market / P&L --}}
    <div class="grid grid-cols-4 gap-3 mb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sport Name</label>
            <select name="originals[{{ $oi }}][sport_name]"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="">Select Sport</option>
                @foreach($sports as $sp)
                    <option value="{{ $sp->name }}" @selected(($orig['sport_name'] ?? '') === $sp->name)>{{ $sp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Event Name</label>
            <input type="text" name="originals[{{ $oi }}][event_name]"
                value="{{ $orig['event_name'] ?? '' }}" placeholder="Enter Event Name"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Market Name</label>
            <input type="text" name="originals[{{ $oi }}][market_name]"
                value="{{ $orig['market_name'] ?? '' }}" placeholder="Enter Market Name"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">P&amp;L</label>
            <input type="number" step="0.01" name="originals[{{ $oi }}][pl]"
                value="{{ $orig['pl'] ?? '' }}"
                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>
    </div>

    {{-- Bet Details --}}
    <div>
        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Bet Details</p>
        <div class="bet-details-container space-y-2">
            @php $bets = $orig['bet_details'] ?? [[]]; @endphp
            @foreach($bets as $bi => $bet)
            <div class="bet-detail-row grid grid-cols-3 gap-3 @if($bi > 0) relative @endif">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Odds</label>
                    <input type="number" step="0.01" name="originals[{{ $oi }}][bet_details][{{ $bi }}][odds]"
                        value="{{ $bet['odds'] ?? '' }}"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Stack</label>
                    <input type="number" step="0.01" name="originals[{{ $oi }}][bet_details][{{ $bi }}][stack]"
                        value="{{ $bet['stack'] ?? '' }}"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
                <div class="relative">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Time</label>
                    <input type="text" name="originals[{{ $oi }}][bet_details][{{ $bi }}][time]"
                        value="{{ $bet['time'] ?? '' }}" placeholder="HH:MM:SS"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    @if($bi > 0)
                    <button type="button" onclick="this.closest('.bet-detail-row').remove()"
                        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none">×</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" onclick="addBetDetail(this)"
            class="mt-2 text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Bet Detail
        </button>
    </div>
</div>
