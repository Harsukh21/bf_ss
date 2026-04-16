@extends('layouts.app')
@section('title', 'Whitelabel — ' . $label->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-2 py-0.5 rounded-full">{{ $label->name }}</span>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">Whitelabel Management</h1>
            </div>
        </div>
        <button onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Whitelabel
        </button>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('labels.whitelabel', $label) }}" class="mb-4">
        <div class="flex gap-2">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search whitelabels..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Search</button>
            @if(request('search'))
                <a href="{{ route('labels.whitelabel', $label) }}" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">Clear</a>
            @endif
        </div>
    </form>

    {{-- Session messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if($whitelabels->count() > 0)
    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">Logo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Whitelabel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">WhatsApp</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Color</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Domain</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($whitelabels as $wl)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        {{-- Logo --}}
                        <td class="px-4 py-3">
                            @if($wl->logo_link)
                                <img src="{{ $wl->logo_link }}" alt="{{ $wl->name }}" class="w-10 h-10 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                            @else
                                <div class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        {{-- Name --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $wl->name }}</div>
                            @if(!$wl->is_active)
                                <span class="text-xs text-red-500">Inactive</span>
                            @endif
                        </td>
                        {{-- WhatsApp --}}
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $wl->whatsapp_group ?: '—' }}
                        </td>
                        {{-- Color --}}
                        <td class="px-4 py-3">
                            @if($wl->color)
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600 flex-shrink-0" style="background-color: {{ $wl->color }}"></span>
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $wl->color }}</span>
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        {{-- Domain --}}
                        <td class="px-4 py-3">
                            @if($wl->domain)
                                <a href="{{ $wl->domain }}" target="_blank" rel="noopener noreferrer"
                                   class="text-primary-600 dark:text-primary-400 hover:underline truncate max-w-[160px] block">
                                    {{ $wl->domain }}
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                {{-- View --}}
                                <button onclick='openViewModal(@json($wl))'
                                    class="p-1.5 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                {{-- Edit --}}
                                <button onclick='openEditModal(@json($wl))'
                                    class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('labels.whitelabel.destroy', [$label, $wl]) }}"
                                      data-confirm="Delete '{{ $wl->name }}'? This cannot be undone."
                                      data-confirm-text="Delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($whitelabels->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $whitelabels->links() }}
            </div>
        @endif
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Whitelabels Yet</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by adding the first whitelabel for <strong>{{ $label->name }}</strong>.</p>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add First Whitelabel
        </button>
    </div>
    @endif
</div>

{{-- ===================== ADD / EDIT MODAL ===================== --}}
<div id="wlModal" class="fixed inset-0 z-[900] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="wlModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">

            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 id="wlModalTitle" class="text-base font-semibold text-gray-900 dark:text-gray-100">Add Whitelabel</h3>
                <button onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="wlForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="wlMethod" value="POST">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Whitelabel Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="wlName" required placeholder="e.g. Brand Alpha"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- WhatsApp Group --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp Group</label>
                    <input type="text" name="whatsapp_group" id="wlWhatsapp" placeholder="Group name or link"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="wlColorPicker" value="#000000"
                            class="w-10 h-10 cursor-pointer rounded border border-gray-300 dark:border-gray-600 p-0.5 bg-white dark:bg-gray-700"
                            oninput="document.getElementById('wlColor').value=this.value">
                        <input type="text" name="color" id="wlColor" value="#000000" maxlength="20" placeholder="#000000"
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono"
                            oninput="syncColorPicker(this.value)">
                    </div>
                </div>

                {{-- Domain --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Domain / URL</label>
                    <input type="text" name="domain" id="wlDomain" placeholder="https://example.com"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                    <div id="logoDropzone"
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        onclick="document.getElementById('wlLogo').click()">
                        <div id="logoPreviewWrap" class="hidden mb-2">
                            <img id="logoPreview" src="" alt="Preview" class="h-16 object-contain mx-auto rounded">
                        </div>
                        <div id="logoPlaceholder">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Click to upload logo (PNG, JPG, WEBP — max 2MB)</p>
                        </div>
                    </div>
                    <input type="file" name="logo" id="wlLogo" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="handleLogoPreview(this)">
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="resetForm()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Reset</button>
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== VIEW MODAL ===================== --}}
<div id="viewModal" class="fixed inset-0 z-[900] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeViewModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="viewModalBox" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md"
             style="transform:scale(0.93);opacity:0;transition:transform 0.2s ease,opacity 0.2s ease;">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Whitelabel Details</h3>
                <button onclick="closeViewModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div id="viewLogoWrap" class="hidden mb-3 text-center">
                    <img id="viewLogo" src="" alt="Logo" class="h-20 object-contain mx-auto rounded-lg border border-gray-200 dark:border-gray-600 p-1 bg-gray-50 dark:bg-gray-700">
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Name</p>
                        <p id="viewName" class="font-medium text-gray-900 dark:text-gray-100"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Color</p>
                        <div class="flex items-center gap-2">
                            <span id="viewColorSwatch" class="w-5 h-5 rounded border border-gray-300 dark:border-gray-600 flex-shrink-0"></span>
                            <span id="viewColorText" class="font-mono text-gray-700 dark:text-gray-300"></span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">WhatsApp Group</p>
                        <p id="viewWhatsapp" class="text-gray-700 dark:text-gray-300"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Domain</p>
                        <a id="viewDomain" href="#" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline break-all"></a>
                    </div>
                </div>
            </div>
            <div class="px-5 pb-5 flex justify-end">
                <button onclick="closeViewModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Close</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const addUrl   = "{{ route('labels.whitelabel.store', $label) }}";
const editBase = "{{ url('labels/' . $label->id . '/whitelabel') }}";

function openAddModal() {
    resetForm();
    document.getElementById('wlModalTitle').textContent = 'Add Whitelabel';
    document.getElementById('wlForm').action = addUrl;
    document.getElementById('wlMethod').value = 'POST';
    showModal('wlModal', 'wlModalBox');
}

function openEditModal(wl) {
    resetForm();
    document.getElementById('wlModalTitle').textContent = 'Edit Whitelabel';
    document.getElementById('wlForm').action = editBase + '/' + wl.id;
    document.getElementById('wlMethod').value = 'PUT';
    document.getElementById('wlName').value     = wl.name || '';
    document.getElementById('wlWhatsapp').value = wl.whatsapp_group || '';
    const color = wl.color || '#000000';
    document.getElementById('wlColor').value       = color;
    document.getElementById('wlColorPicker').value = color;
    document.getElementById('wlDomain').value   = wl.domain || '';
    if (wl.logo_link) {
        document.getElementById('logoPreview').src = wl.logo_link;
        document.getElementById('logoPreviewWrap').classList.remove('hidden');
        document.getElementById('logoPlaceholder').classList.add('hidden');
    }
    showModal('wlModal', 'wlModalBox');
}

function openViewModal(wl) {
    document.getElementById('viewName').textContent     = wl.name || '—';
    document.getElementById('viewWhatsapp').textContent = wl.whatsapp_group || '—';
    const color = wl.color || '';
    document.getElementById('viewColorSwatch').style.backgroundColor = color || 'transparent';
    document.getElementById('viewColorText').textContent = color || '—';
    const domain = wl.domain || '';
    const domainEl = document.getElementById('viewDomain');
    domainEl.textContent = domain || '—';
    domainEl.href = domain || '#';
    const logoEl = document.getElementById('viewLogo');
    const logoWrap = document.getElementById('viewLogoWrap');
    if (wl.logo_link) {
        logoEl.src = wl.logo_link;
        logoWrap.classList.remove('hidden');
    } else {
        logoWrap.classList.add('hidden');
    }
    showModal('viewModal', 'viewModalBox');
}

function closeModal() {
    hideModal('wlModal', 'wlModalBox');
}
function closeViewModal() {
    hideModal('viewModal', 'viewModalBox');
}

function showModal(overlayId, boxId) {
    const overlay = document.getElementById(overlayId);
    const box     = document.getElementById(boxId);
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        box.style.transform = 'scale(1)';
        box.style.opacity   = '1';
    });
}

function hideModal(overlayId, boxId) {
    const overlay = document.getElementById(overlayId);
    const box     = document.getElementById(boxId);
    box.style.transform = 'scale(0.93)';
    box.style.opacity   = '0';
    setTimeout(() => {
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function resetForm() {
    document.getElementById('wlForm').reset();
    document.getElementById('wlColor').value       = '#000000';
    document.getElementById('wlColorPicker').value = '#000000';
    document.getElementById('logoPreview').src = '';
    document.getElementById('logoPreviewWrap').classList.add('hidden');
    document.getElementById('logoPlaceholder').classList.remove('hidden');
}

function syncColorPicker(hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById('wlColorPicker').value = hex;
    }
}

function handleLogoPreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreviewWrap').classList.remove('hidden');
            document.getElementById('logoPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('wlModal').classList.contains('hidden')) closeModal();
        if (!document.getElementById('viewModal').classList.contains('hidden')) closeViewModal();
    }
});
</script>
@endpush
@endsection
