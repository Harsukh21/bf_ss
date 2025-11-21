@extends('layouts.app')

@section('title', 'Create Notification')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Notification</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Send notifications to users via multiple delivery methods</p>
                </div>
                <a href="{{ route('notifications.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Back
                </a>
            </div>
        </div>

        <!-- Create Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form action="{{ route('notifications.store') }}" method="POST" id="notificationForm" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                           placeholder="Enter notification title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                    <!-- Hidden textarea for form submission -->
                    <textarea id="message" 
                              name="message" 
                              required
                              class="hidden">{{ old('message') }}</textarea>
                    
                    <!-- Rich Text Editor Container -->
                    <div class="border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 @error('message') border-red-500 @enderror">
                        <!-- Toolbar -->
                        <div class="flex items-center gap-1 p-2 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-750 flex-wrap">
                            <!-- Text Formatting -->
                            <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-1">
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="bold" 
                                        title="Bold (Ctrl+B)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="italic" 
                                        title="Italic (Ctrl+I)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="underline" 
                                        title="Underline (Ctrl+U)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19h14M5 5h14"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="strikethrough" 
                                        title="Strikethrough">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19h14M5 5h14M8 12h8"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Text Alignment -->
                            <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-1">
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="justifyLeft" 
                                        title="Align Left">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4h14v1.5H3V4zm0 5h14v1.5H3V9zm0 5h14v1.5H3v-1.5z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="justifyCenter" 
                                        title="Align Center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4h14v1.5H3V4zm3 5h8v1.5H6V9zm-3 5h14v1.5H3v-1.5z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="justifyRight" 
                                        title="Align Right">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4h14v1.5H3V4zm6 5h8v1.5H9V9zm-6 5h14v1.5H3v-1.5z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Lists -->
                            <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-1">
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="insertUnorderedList" 
                                        title="Bullet List">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4h14v1.5H3V4zm0 5h14v1.5H3V9zm0 5h14v1.5H3v-1.5z"></path>
                                        <circle cx="1.5" cy="4.75" r="0.75" fill="currentColor"></circle>
                                        <circle cx="1.5" cy="9.75" r="0.75" fill="currentColor"></circle>
                                        <circle cx="1.5" cy="14.75" r="0.75" fill="currentColor"></circle>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="insertOrderedList" 
                                        title="Numbered List">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4h14v1.5H3V4zm0 5h14v1.5H3V9zm0 5h14v1.5H3v-1.5z"></path>
                                        <text x="0.5" y="6" font-size="4" fill="currentColor">1.</text>
                                        <text x="0.5" y="11" font-size="4" fill="currentColor">2.</text>
                                        <text x="0.5" y="16" font-size="4" fill="currentColor">3.</text>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Other Options -->
                            <div class="flex items-center gap-1">
                                <!-- Icon/Emoji Picker -->
                                <div class="relative">
                                    <button type="button" 
                                            class="rich-editor-btn" 
                                            id="iconPickerBtn"
                                            title="Insert Icon/Emoji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <!-- Icon Picker Dropdown -->
                                    <div id="iconPickerDropdown" class="hidden absolute z-50 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg p-3 w-80 max-h-96 overflow-y-auto">
                                        <div class="mb-2">
                                            <input type="text" 
                                                   id="iconSearch" 
                                                   placeholder="Search icons..." 
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        </div>
                                        <div class="grid grid-cols-8 gap-1" id="iconGrid">
                                            <!-- Common Emojis -->
                                            <button type="button" class="icon-emoji-btn" data-icon="😀">😀</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😃">😃</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😄">😄</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😁">😁</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😅">😅</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😂">😂</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤣">🤣</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😊">😊</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😇">😇</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🙂">🙂</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🙃">🙃</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😉">😉</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😌">😌</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😍">😍</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥰">🥰</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😘">😘</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😗">😗</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😙">😙</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😚">😚</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😋">😋</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😛">😛</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😝">😝</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😜">😜</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤪">🤪</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤨">🤨</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🧐">🧐</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤓">🤓</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😎">😎</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤩">🤩</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥳">🥳</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😏">😏</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😒">😒</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😞">😞</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😔">😔</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😟">😟</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😕">😕</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🙁">🙁</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="☹️">☹️</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😣">😣</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😖">😖</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😫">😫</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😩">😩</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥺">🥺</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😢">😢</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😭">😭</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😤">😤</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😠">😠</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😡">😡</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤬">🤬</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤯">🤯</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😳">😳</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥵">🥵</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥶">🥶</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😱">😱</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😨">😨</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😰">😰</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😥">😥</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😓">😓</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤗">🤗</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤔">🤔</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤭">🤭</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤫">🤫</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤥">🤥</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😶">😶</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😐">😐</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😑">😑</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😬">😬</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🙄">🙄</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😯">😯</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😦">😦</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😧">😧</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😮">😮</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😲">😲</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥱">🥱</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😴">😴</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤤">🤤</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😪">😪</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😵">😵</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤐">🤐</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🥴">🥴</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤢">🤢</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤮">🤮</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤧">🤧</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="😷">😷</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤒">🤒</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤕">🤕</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤑">🤑</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🤠">🤠</button>
                                            
                                            <!-- Symbols & Icons -->
                                            <button type="button" class="icon-emoji-btn" data-icon="✅">✅</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="❌">❌</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="⚠️">⚠️</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="❗">❗</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="❓">❓</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💯">💯</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔥">🔥</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="⭐">⭐</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🌟">🌟</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💡">💡</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💪">💪</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🎉">🎉</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🎊">🎊</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🎈">🎈</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🎁">🎁</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🏆">🏆</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🏅">🏅</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🎖️">🎖️</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💰">💰</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💎">💎</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💵">💵</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="💸">💸</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📱">📱</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📞">📞</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📧">📧</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📨">📨</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📩">📩</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📬">📬</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📭">📭</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📮">📮</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📯">📯</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📪">📪</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📫">📫</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔔">🔔</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔕">🔕</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📢">📢</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📣">📣</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="📯">📯</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔊">🔊</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔉">🔉</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔇">🔇</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔈">🔈</button>
                                            <button type="button" class="icon-emoji-btn" data-icon="🔇">🔇</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="createLink" 
                                        title="Insert Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="rich-editor-btn" 
                                        data-command="removeFormat" 
                                        title="Clear Formatting">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Editor Content -->
                        <div id="messageEditor" 
                             contenteditable="true"
                             class="min-h-[150px] max-h-[400px] overflow-y-auto px-3 py-2 text-gray-900 dark:text-gray-100 focus:outline-none"
                             data-placeholder="Enter notification message...">{{ old('message') }}</div>
                    </div>
                    
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        You can format your message using the toolbar above. HTML formatting is supported.
                    </p>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Users Selection -->
                <div>
                    <label for="user_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Users *</label>
                    <div class="relative">
                        <!-- Hidden select for form submission -->
                        <select id="user_ids" 
                                name="user_ids[]" 
                                multiple
                                required
                                class="hidden">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_ids') && in_array($user->id, old('user_ids')) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Custom Multi-Select Dropdown -->
                        <div class="relative">
                            <div id="multiSelectTrigger" 
                                 class="w-full px-3 py-2.5 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('user_ids') border-red-500 @enderror min-h-[42px] flex items-center flex-wrap gap-2 relative">
                                <div class="flex-1 flex items-center flex-wrap gap-2 min-h-[24px]">
                                    <span id="selectedCount" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ old('user_ids') && count(old('user_ids')) > 0 ? count(old('user_ids')) . ' user(s) selected' : 'Select users...' }}
                                    </span>
                                    <div id="selectedTags" class="flex flex-wrap gap-1"></div>
                                </div>
                                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            
                            <!-- Dropdown Menu -->
                            <div id="multiSelectDropdown" 
                                 class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-64 overflow-hidden">
                                <!-- Search Input -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input type="text" 
                                           id="multiSelectSearch" 
                                           placeholder="Search users..." 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                
                                <!-- Options List -->
                                <div id="multiSelectOptions" class="overflow-y-auto max-h-52 p-1">
                                    @foreach($users as $user)
                                        <label class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded cursor-pointer group" data-user-id="{{ $user->id }}">
                                            <input type="checkbox" 
                                                   value="{{ $user->id }}" 
                                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                                   {{ old('user_ids') && in_array($user->id, old('user_ids')) ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm text-gray-900 dark:text-gray-100 flex-1">
                                                <span class="font-medium">{{ $user->name }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">({{ $user->email }})</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                
                                <!-- Select All / Clear All -->
                                <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex justify-between text-xs">
                                    <button type="button" id="selectAllUsers" class="text-primary-600 dark:text-primary-400 hover:underline">Select All</button>
                                    <button type="button" id="clearAllUsers" class="text-red-600 dark:text-red-400 hover:underline">Clear All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Click to select multiple users</p>
                    @error('user_ids')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notification Type -->
                <div>
                    <label for="notification_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notification Type *</label>
                    <select id="notification_type" 
                            name="notification_type" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('notification_type') border-red-500 @enderror">
                        <option value="instant" {{ old('notification_type') === 'instant' ? 'selected' : '' }}>Send Instant</option>
                        <option value="after_minutes" {{ old('notification_type') === 'after_minutes' ? 'selected' : '' }}>After X Minutes</option>
                        <option value="after_hours" {{ old('notification_type') === 'after_hours' ? 'selected' : '' }}>After X Hours</option>
                        <option value="daily" {{ old('notification_type') === 'daily' ? 'selected' : '' }}>Daily at Particular Time</option>
                        <option value="weekly" {{ old('notification_type') === 'weekly' ? 'selected' : '' }}>Weekly at Particular Time</option>
                        <option value="monthly" {{ old('notification_type') === 'monthly' ? 'selected' : '' }}>Monthly at Particular Time</option>
                    </select>
                    @error('notification_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conditional Fields based on Notification Type -->
                <div id="after_minutes_field" class="hidden">
                    <label for="duration_value_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minutes *</label>
                    <input type="number" 
                           id="duration_value_minutes" 
                           name="duration_value" 
                           value="{{ old('duration_value') }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('duration_value') border-red-500 @enderror"
                           placeholder="Enter minutes">
                    @error('duration_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="after_hours_field" class="hidden">
                    <label for="duration_value_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hours *</label>
                    <input type="number" 
                           id="duration_value_hours" 
                           name="duration_value" 
                           value="{{ old('duration_value') }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('duration_value') border-red-500 @enderror"
                           placeholder="Enter hours">
                    @error('duration_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="daily_field" class="hidden">
                    <label for="daily_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Daily Time *</label>
                    <input type="time" 
                           id="daily_time" 
                           name="daily_time" 
                           value="{{ old('daily_time') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('daily_time') border-red-500 @enderror">
                    @error('daily_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="weekly_field" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="weekly_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Day of Week *</label>
                            <select id="weekly_day" 
                                    name="weekly_day" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('weekly_day') border-red-500 @enderror">
                                <option value="0" {{ old('weekly_day') === '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ old('weekly_day') === '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('weekly_day') === '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('weekly_day') === '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('weekly_day') === '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('weekly_day') === '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('weekly_day') === '6' ? 'selected' : '' }}>Saturday</option>
                            </select>
                            @error('weekly_day')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="weekly_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                            <input type="time" 
                                   id="weekly_time" 
                                   name="weekly_time" 
                                   value="{{ old('weekly_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('weekly_time') border-red-500 @enderror">
                            @error('weekly_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="monthly_field" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="monthly_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Day of Month * (1-31)</label>
                            <input type="number" 
                                   id="monthly_day" 
                                   name="monthly_day" 
                                   value="{{ old('monthly_day') }}"
                                   min="1"
                                   max="31"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('monthly_day') border-red-500 @enderror"
                                   placeholder="Enter day (1-31)">
                            @error('monthly_day')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="monthly_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                            <input type="time" 
                                   id="monthly_time" 
                                   name="monthly_time" 
                                   value="{{ old('monthly_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('monthly_time') border-red-500 @enderror">
                            @error('monthly_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Delivery Methods -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Delivery Methods *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="push"
                                   {{ old('delivery_methods') && in_array('push', old('delivery_methods')) ? 'checked' : 'checked' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Push Notification</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="telegram"
                                   {{ old('delivery_methods') && in_array('telegram', old('delivery_methods')) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Telegram</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="delivery_methods[]" 
                                   value="login_popup"
                                   {{ old('delivery_methods') && in_array('login_popup', old('delivery_methods')) ? 'checked' : 'checked' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show After Login</span>
                        </label>
                    </div>
                    @error('delivery_methods')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requires Web PIN -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_web_pin" 
                               value="1"
                               {{ old('requires_web_pin', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require Web PIN to close popup</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If checked, users must enter their web PIN to close the notification popup after login</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('notifications.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Create Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
    .rich-editor-btn {
        @apply p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500;
    }
    
    .rich-editor-btn.active {
        @apply bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300;
    }
    
    #messageEditor:empty:before {
        content: attr(data-placeholder);
        @apply text-gray-400 dark:text-gray-500;
    }
    
    #messageEditor:focus {
        @apply outline-none;
    }
    
    #messageEditor ul,
    #messageEditor ol {
        @apply ml-6 my-2;
    }
    
    #messageEditor ul {
        list-style-type: disc;
    }
    
    #messageEditor ol {
        list-style-type: decimal;
    }
    
    #messageEditor a {
        @apply text-primary-600 dark:text-primary-400 underline;
    }
    
    #messageEditor strong {
        @apply font-bold;
    }
    
    #messageEditor em {
        @apply italic;
    }
    
    #messageEditor u {
        @apply underline;
    }
    
    .icon-emoji-btn {
        @apply p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-xl transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 cursor-pointer;
    }
    
    .icon-emoji-btn:hover {
        @apply transform scale-110;
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Rich Text Editor Functionality
        const messageEditor = document.getElementById('messageEditor');
        const messageTextarea = document.getElementById('message');
        const editorButtons = document.querySelectorAll('.rich-editor-btn');
        
        // Sync content to textarea
        function syncToTextarea() {
            messageTextarea.value = messageEditor.innerHTML;
        }
        
        // Initialize with existing value
        if (messageTextarea.value) {
            messageEditor.innerHTML = messageTextarea.value;
        }
        
        // Update textarea on input
        messageEditor.addEventListener('input', syncToTextarea);
        messageEditor.addEventListener('blur', syncToTextarea);
        
        // Handle toolbar buttons
        editorButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.dataset.command;
                
                // Focus editor first
                messageEditor.focus();
                
                // Handle special commands
                if (command === 'createLink') {
                    const url = prompt('Enter URL:');
                    if (url) {
                        document.execCommand('createLink', false, url);
                    }
                } else {
                    document.execCommand(command, false, null);
                }
                
                // Update active state
                updateButtonStates();
                
                // Sync to textarea
                syncToTextarea();
            });
        });
        
        // Update button active states
        function updateButtonStates() {
            editorButtons.forEach(button => {
                const command = button.dataset.command;
                let isActive = false;
                
                if (['bold', 'italic', 'underline', 'strikethrough'].includes(command)) {
                    isActive = document.queryCommandState(command);
                }
                
                button.classList.toggle('active', isActive);
            });
        }
        
        // Update on selection change
        messageEditor.addEventListener('keyup', updateButtonStates);
        messageEditor.addEventListener('mouseup', updateButtonStates);
        
        // Keyboard shortcuts
        messageEditor.addEventListener('keydown', function(e) {
            // Ctrl+B for Bold
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                document.execCommand('bold', false, null);
                updateButtonStates();
                syncToTextarea();
            }
            // Ctrl+I for Italic
            if (e.ctrlKey && e.key === 'i') {
                e.preventDefault();
                document.execCommand('italic', false, null);
                updateButtonStates();
                syncToTextarea();
            }
            // Ctrl+U for Underline
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
                document.execCommand('underline', false, null);
                updateButtonStates();
                syncToTextarea();
            }
        });
        
        // Sync before form submission
        const form = document.getElementById('notificationForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                syncToTextarea();
                // Validate that content is not empty
                if (!messageEditor.textContent.trim()) {
                    e.preventDefault();
                    alert('Please enter a message.');
                    messageEditor.focus();
                    return false;
                }
            });
        }
        
        // Initial button state update
        updateButtonStates();
        
        // Icon/Emoji Picker Functionality
        const iconPickerBtn = document.getElementById('iconPickerBtn');
        const iconPickerDropdown = document.getElementById('iconPickerDropdown');
        const iconSearch = document.getElementById('iconSearch');
        const iconEmojiBtns = document.querySelectorAll('.icon-emoji-btn');
        
        // Toggle icon picker dropdown
        iconPickerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            iconPickerDropdown.classList.toggle('hidden');
            if (!iconPickerDropdown.classList.contains('hidden')) {
                iconSearch.focus();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!iconPickerBtn.contains(e.target) && !iconPickerDropdown.contains(e.target)) {
                iconPickerDropdown.classList.add('hidden');
            }
        });
        
        // Insert icon/emoji into editor
        iconEmojiBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.dataset.icon;
                
                // Focus editor
                messageEditor.focus();
                
                // Insert icon at cursor position
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    range.deleteContents();
                    const textNode = document.createTextNode(icon);
                    range.insertNode(textNode);
                    range.setStartAfter(textNode);
                    range.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else {
                    // If no selection, append at the end
                    messageEditor.appendChild(document.createTextNode(icon));
                }
                
                // Sync to textarea
                syncToTextarea();
                
                // Close dropdown
                iconPickerDropdown.classList.add('hidden');
            });
        });
        
        // Search functionality for icons
        iconSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            iconEmojiBtns.forEach(btn => {
                const icon = btn.dataset.icon;
                if (icon.toLowerCase().includes(searchTerm) || searchTerm === '') {
                    btn.style.display = 'block';
                } else {
                    btn.style.display = 'none';
                }
            });
        });
        // Multi-Select Dropdown Functionality
        const trigger = document.getElementById('multiSelectTrigger');
        const dropdown = document.getElementById('multiSelectDropdown');
        const searchInput = document.getElementById('multiSelectSearch');
        const optionsContainer = document.getElementById('multiSelectOptions');
        const hiddenSelect = document.getElementById('user_ids');
        const selectedCount = document.getElementById('selectedCount');
        const selectedTags = document.getElementById('selectedTags');
        const selectAllBtn = document.getElementById('selectAllUsers');
        const clearAllBtn = document.getElementById('clearAllUsers');
        
        let userData = {};
        const checkboxes = optionsContainer.querySelectorAll('input[type="checkbox"]');
        
        // Build user data map
        checkboxes.forEach(checkbox => {
            const label = checkbox.closest('label');
            const userId = checkbox.value;
            const textContent = label.querySelector('span').textContent.trim();
            userData[userId] = {
                name: label.querySelector('.font-medium').textContent.trim(),
                email: label.querySelector('.text-gray-500').textContent.replace(/[()]/g, ''),
                fullText: textContent
            };
        });
        
        // Toggle dropdown
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                searchInput.focus();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Search functionality
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const labels = optionsContainer.querySelectorAll('label');
            
            labels.forEach(label => {
                const text = label.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });
        });
        
        // Update selected items
        function updateSelectedItems() {
            const selectedCheckboxes = Array.from(checkboxes).filter(cb => cb.checked);
            const selectedValues = selectedCheckboxes.map(cb => cb.value);
            
            // Update hidden select
            Array.from(hiddenSelect.options).forEach(option => {
                option.selected = selectedValues.includes(option.value);
            });
            
            // Update selected count
            const count = selectedValues.length;
            if (count === 0) {
                selectedCount.textContent = 'Select users...';
                selectedCount.classList.remove('hidden');
                selectedTags.innerHTML = '';
            } else {
                selectedCount.classList.add('hidden');
                
                // Show selected tags
                selectedTags.innerHTML = '';
                selectedValues.forEach(userId => {
                    const user = userData[userId];
                    if (user) {
                        const tag = document.createElement('span');
                        tag.className = 'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded';
                        tag.innerHTML = `
                            ${user.name}
                            <button type="button" 
                                    class="hover:text-primary-600 dark:hover:text-primary-400" 
                                    onclick="removeUser('${userId}'); event.stopPropagation();">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        `;
                        selectedTags.appendChild(tag);
                    }
                });
            }
            
            // Validate required field
            hiddenSelect.setCustomValidity(count === 0 ? 'Please select at least one user' : '');
        }
        
        // Remove user function (global for onclick)
        window.removeUser = function(userId) {
            const checkbox = document.querySelector(`#multiSelectOptions input[value="${userId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedItems();
            }
        };
        
        // Handle checkbox changes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedItems);
        });
        
        // Select All
        selectAllBtn.addEventListener('click', function() {
            const visibleLabels = Array.from(optionsContainer.querySelectorAll('label')).filter(
                label => label.style.display !== 'none'
            );
            visibleLabels.forEach(label => {
                const checkbox = label.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = true;
            });
            updateSelectedItems();
        });
        
        // Clear All
        clearAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedItems();
        });
        
        // Initialize
        updateSelectedItems();
        
        // Notification Type Toggle
        const notificationType = document.getElementById('notification_type');
        const afterMinutesField = document.getElementById('after_minutes_field');
        const afterHoursField = document.getElementById('after_hours_field');
        const dailyField = document.getElementById('daily_field');
        const weeklyField = document.getElementById('weekly_field');
        const monthlyField = document.getElementById('monthly_field');

        function toggleFields() {
            // Hide all fields first
            afterMinutesField.classList.add('hidden');
            afterHoursField.classList.add('hidden');
            dailyField.classList.add('hidden');
            weeklyField.classList.add('hidden');
            monthlyField.classList.add('hidden');

            // Remove required attributes
            document.getElementById('duration_value_minutes')?.removeAttribute('required');
            document.getElementById('duration_value_hours')?.removeAttribute('required');
            document.getElementById('daily_time')?.removeAttribute('required');
            document.getElementById('weekly_day')?.removeAttribute('required');
            document.getElementById('weekly_time')?.removeAttribute('required');
            document.getElementById('monthly_day')?.removeAttribute('required');
            document.getElementById('monthly_time')?.removeAttribute('required');

            // Show and set required based on selected type
            const selectedType = notificationType.value;
            if (selectedType === 'after_minutes') {
                afterMinutesField.classList.remove('hidden');
                document.getElementById('duration_value_minutes')?.setAttribute('required', 'required');
            } else if (selectedType === 'after_hours') {
                afterHoursField.classList.remove('hidden');
                document.getElementById('duration_value_hours')?.setAttribute('required', 'required');
            } else if (selectedType === 'daily') {
                dailyField.classList.remove('hidden');
                document.getElementById('daily_time')?.setAttribute('required', 'required');
            } else if (selectedType === 'weekly') {
                weeklyField.classList.remove('hidden');
                document.getElementById('weekly_day')?.setAttribute('required', 'required');
                document.getElementById('weekly_time')?.setAttribute('required', 'required');
            } else if (selectedType === 'monthly') {
                monthlyField.classList.remove('hidden');
                document.getElementById('monthly_day')?.setAttribute('required', 'required');
                document.getElementById('monthly_time')?.setAttribute('required', 'required');
            }
        }

        // Initial call
        toggleFields();

        // Listen for changes
        notificationType.addEventListener('change', toggleFields);
    });
</script>
@endpush
@endsection

