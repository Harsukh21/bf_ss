<div class="space-y-4">

    {{-- Whitelabel User --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Whitelabel User</label>
        <select name="whitelabel_id" id="whitelabelSelect" onchange="fillWhatsapp(this)"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">— Select Whitelabel —</option>
            @foreach($whitelabels as $wl)
                <option value="{{ $wl->id }}"
                    data-whatsapp="{{ $wl->whatsapp_group }}"
                    @selected(old('whitelabel_id', $proof?->whitelabel_id) == $wl->id)>
                    {{ $wl->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Agent Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agent Name</label>
        <input type="text" name="agent_name" value="{{ old('agent_name', $proof?->agent_name) }}" placeholder="Enter agent name"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- User --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
        <input type="text" name="user_name" value="{{ old('user_name', $proof?->user_name) }}" placeholder="Enter user name, e.g., abcd1234"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Proof Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proof Type</label>
        <select name="proof_type_id"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Select Proof Type</option>
            @foreach($proofTypes as $pt)
                <option value="{{ $pt->id }}" @selected(old('proof_type_id', $proof?->proof_type_id) == $pt->id)>{{ $pt->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Amount --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount <span class="text-xs text-gray-400 font-normal">(Enter stack amount)</span></label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $proof?->amount) }}" placeholder="Enter bet or bets amount"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Sport --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sport</label>
        <select name="sport_id"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Select Sport</option>
            @foreach($sports as $sp)
                <option value="{{ $sp->id }}" @selected(old('sport_id', $proof?->sport_id) == $sp->id)>{{ $sp->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Event Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Name</label>
        <input type="text" name="event_name" value="{{ old('event_name', $proof?->event_name) }}" placeholder="Enter event name"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Market Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Market Name</label>
        <input type="text" name="market_name" value="{{ old('market_name', $proof?->market_name) }}" placeholder="Enter market name"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Profit/Loss --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profit/Loss</label>
        <input type="number" step="0.01" name="profit_loss" value="{{ old('profit_loss', $proof?->profit_loss) }}" placeholder="Enter profit or loss amount"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Date --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
        <input type="date" name="proof_date"
            value="{{ old('proof_date', $proof?->proof_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Navigation --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Navigation</label>
        <input type="text" name="navigation" value="{{ old('navigation', $proof?->navigation) }}" placeholder="Enter navigation details"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Images (Max 6) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Images <span class="text-xs text-gray-400 font-normal">(Max 6)</span></label>
        @if($proof && !empty($proof->images))
            <div class="mb-2 flex flex-wrap gap-2">
                @foreach($proof->images as $imgPath)
                <div class="relative">
                    <img src="{{ Storage::url($imgPath) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                    <label class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center cursor-pointer" title="Remove">
                        <input type="checkbox" name="remove_images[]" value="{{ $imgPath }}" class="hidden">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </label>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mb-1">Check × to remove existing images. Upload new ones below.</p>
        @endif
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
            class="w-full text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 5MB each, up to 6 files</p>
    </div>

    {{-- Navigation 2 --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Navigation 2</label>
        <input type="text" name="navigation2" value="{{ old('navigation2', $proof?->navigation2) }}" placeholder="Enter secondary navigation details"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Navigation 2 Images (Max 6) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Navigation 2 Images <span class="text-xs text-gray-400 font-normal">(Max 6)</span></label>
        @if($proof && !empty($proof->navigation2_images))
            <div class="mb-2 flex flex-wrap gap-2">
                @foreach($proof->navigation2_images as $imgPath)
                <div class="relative">
                    <img src="{{ Storage::url($imgPath) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                    <label class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center cursor-pointer" title="Remove">
                        <input type="checkbox" name="remove_nav2_images[]" value="{{ $imgPath }}" class="hidden">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </label>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mb-1">Check × to remove existing images. Upload new ones below.</p>
        @endif
        <input type="file" name="navigation2_images[]" multiple accept="image/jpeg,image/png,image/webp"
            class="w-full text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 5MB each, up to 6 files</p>
    </div>
</div>

@push('js')
<script>
// When whitelabel changes, show the remove overlay on the × badge
document.querySelectorAll('.relative label').forEach(lbl => {
    lbl.addEventListener('click', function() {
        const cb = this.querySelector('input[type=checkbox]');
        cb.checked = !cb.checked;
        this.closest('.relative').querySelector('img').style.opacity = cb.checked ? '0.4' : '1';
    });
});

function fillWhatsapp(sel) {
    // Could auto-fill a whatsapp display field if needed
}
</script>
@endpush
