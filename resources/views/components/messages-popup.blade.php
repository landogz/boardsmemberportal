<!-- Messages Popup Container - Multiple Chats Support -->
<div id="messagesPopupContainer" class="fixed bottom-0 right-4 z-[100] flex items-end gap-2" style="max-width: calc(100vw - 2rem);">
    <!-- Chats will be dynamically added here -->
</div>

<!-- Full Screen Image Viewer Modal -->
<div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-90 z-[200] hidden flex items-center justify-center">
    <div class="relative w-full h-full flex items-center justify-center p-4 overflow-hidden">
        <!-- Top Controls Bar -->
        <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
            <!-- Zoom Controls -->
            <div class="flex items-center gap-1 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-1">
                <button id="zoomOutBtn" class="w-8 h-8 bg-white bg-opacity-20 hover:bg-opacity-30 rounded flex items-center justify-center text-white transition" title="Zoom Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                <button id="resetZoomBtn" class="w-8 h-8 bg-white bg-opacity-20 hover:bg-opacity-30 rounded flex items-center justify-center text-white transition text-xs font-semibold" title="Reset Zoom">
                    100%
                </button>
                <button id="zoomInBtn" class="w-8 h-8 bg-white bg-opacity-20 hover:bg-opacity-30 rounded flex items-center justify-center text-white transition" title="Zoom In">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                    </svg>
                </button>
            </div>
            <!-- Download Button -->
            <button id="downloadImageViewer" class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center text-white transition" title="Download Image">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
            </button>
            <!-- Close Button -->
            <button id="closeImageViewer" class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center text-white transition" title="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <!-- Image Container -->
        <div id="imageViewerContainer" class="w-full h-full flex items-center justify-center overflow-hidden" style="cursor: grab;">
            <img id="viewerImage" src="" alt="Full view" class="object-contain rounded-lg transition-transform duration-200" style="transform-origin: center center; max-width: 100vw; max-height: 100vh;">
        </div>
    </div>
</div>

<script>
    // Messages Popup Functionality - Multiple Chats Support
    (function() {
        const container = document.getElementById('messagesPopupContainer');
        const activeChats = new Map(); // userId -> chat element
        let chatCounter = 0;

        // User data mapping (static for now)
        const userData = {
            'jd': { name: 'John Doe', initials: 'JD', color: 'from-purple-400 to-purple-600', status: 'Active now' },
            'js': { name: 'Jane Smith', initials: 'JS', color: 'from-blue-400 to-blue-600', status: 'Active 1 hour ago' },
            'mj': { name: 'Michael Johnson', initials: 'MJ', color: 'from-green-400 to-green-600', status: 'Active 3 hours ago' },
            'sw': { name: 'Sarah Williams', initials: 'SW', color: 'from-indigo-400 to-indigo-600', status: 'Active 5 hours ago' },
            'ab': { name: 'Admin Board', initials: 'AB', color: 'from-yellow-400 to-orange-500', status: 'Active 1 day ago' },
        };

        // Get user initials for current user
        const currentUserInitials = '{{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}';

        // Create chat popup HTML
        function createChatPopup(userId, user) {
            const chatId = `chat-${userId}-${chatCounter++}`;
            const isExpanded = true;
            
            const chatHTML = `
                <div id="${chatId}" class="messages-chat-popup" data-user-id="${userId}" data-expanded="true">
                    <!-- Expanded Chat Window -->
                    <div class="chat-expanded w-96 bg-white rounded-t-xl shadow-2xl border border-gray-200 flex flex-col" style="max-height: 600px; height: 500px;">
                        <!-- Popup Header -->
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 cursor-move">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="w-8 h-8 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs chat-avatar">
                                        ${user.initials}
                                    </div>
                                    <!-- Online Indicator -->
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800 chat-name">${user.name}</h3>
                                    <p class="text-xs text-gray-500 chat-status">${user.status}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="chat-minimize-btn p-1 hover:bg-gray-200 rounded transition">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <button class="chat-close-btn p-1 hover:bg-gray-200 rounded transition">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 chat-messages" style="max-height: 400px;">
                            <!-- Received Message -->
                            <div class="flex items-start space-x-2">
                                <div class="w-6 h-6 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    ${user.initials}
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white rounded-lg p-2 shadow-sm">
                                        <p class="text-xs text-gray-800">Hi! Can we discuss the agenda for next week's board meeting?</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">2 minutes ago</p>
                                </div>
                            </div>

                            <!-- Sent Message -->
                            <div class="flex items-start space-x-2 justify-end">
                                <div class="flex-1 flex justify-end">
                                    <div class="max-w-[75%]">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-2 shadow-sm">
                                            <p class="text-xs">Sure! I've prepared a draft agenda. Let me share it with you.</p>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1 mr-1 text-right">1 minute ago</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    ${currentUserInitials}
                                </div>
                            </div>

                            <!-- Received Message -->
                            <div class="flex items-start space-x-2">
                                <div class="w-6 h-6 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    ${user.initials}
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white rounded-lg p-2 shadow-sm">
                                        <p class="text-xs text-gray-800">That would be great! I'll review it and get back to you with my feedback.</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">Just now</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="p-3 border-t border-gray-200 bg-white relative">
                            <!-- Emoji Picker -->
                            <div class="emoji-picker hidden absolute bottom-full left-0 mb-2 w-80 bg-white rounded-lg shadow-2xl border border-gray-300 z-50 flex flex-col" style="height: 300px;">
                                @include('components.emoji-picker')
                            </div>
                            <!-- File Preview Area -->
                            <div class="chat-file-preview hidden px-3 pb-3 flex items-center gap-3 overflow-x-auto overflow-y-hidden" style="scrollbar-width: thin; scrollbar-color: rgba(107, 114, 128, 0.5) transparent;">
                                <!-- Files will be added here as cards -->
                            </div>
                            <!-- Voice Recorder Bar -->
                            <div class="chat-voice-recorder hidden px-3 pb-3">
                                <div class="flex items-center gap-3 rounded-full px-4 py-2" style="background-color:#FF1F70;">
                                    <!-- Cancel recording -->
                                    <button type="button" class="voice-cancel-btn flex items-center justify-center w-8 h-8 rounded-full text-white text-lg font-bold hover:bg-pink-700/60 transition" aria-label="Cancel recording">
                                        ×
                                    </button>
                                    <!-- Stop icon -->
                                    <button type="button" class="voice-stop-btn flex items-center justify-center w-9 h-9 rounded-full bg-white text-pink-500 hover:bg-gray-100 transition" aria-label="Stop recording">
                                        <span class="w-3 h-3 rounded-sm" style="background-color:#FF1F70;"></span>
                                    </button>
                                    <!-- Timer -->
                                    <div class="flex-1 flex justify-end">
                                        <div class="inline-flex items-center justify-center px-4 py-1 rounded-full bg-white text-pink-600 text-xs font-semibold voice-timer">
                                            0:00
                                        </div>
                                    </div>
                                    <!-- Send voice message -->
                                    <button type="button" class="voice-send-btn flex items-center justify-center w-9 h-9 rounded-full bg-white text-pink-500 hover:bg-gray-100 transition" aria-label="Send voice message">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3.4 20.4L5 14 14 12 5 10 3.4 3.6 21 12 3.4 20.4Z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <form class="chat-form flex items-center space-x-2 px-3">
                                <!-- File Attachment Button -->
                                <!-- Compressed Icons Container -->
                                <div class="flex items-center" style="gap: 0rem;">
                                    <!-- Attach Files Button -->
                                    <button type="button" class="chat-attach-btn p-1.5 text-blue-600 hover:bg-gray-100 rounded-full transition" title="Attach files">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </button>
                                    <input type="file" class="hidden chat-file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                    <!-- Voice Clip Button -->
                                    <button type="button" class="chat-voice-btn p-1.5 text-red-500 hover:bg-gray-100 rounded-full transition" title="Record voice message">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path>
                                            <path d="M19 11a1 1 0 0 0-2 0 5 5 0 0 1-10 0 1 1 0 0 0-2 0 7.002 7.002 0 0 0 6 6.92V21h-2a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-2v-3.08A7.002 7.002 0 0 0 19 11z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <input type="text" placeholder="Type a message..." class="chat-input flex-1 px-3 py-2 text-xs border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <!-- Emoji Button -->
                                <button type="button" class="chat-emoji-btn p-2 text-yellow-500 hover:bg-gray-100 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button type="submit" class="p-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition flex items-center justify-center" title="Send message">
                                    <svg class="w-5 h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Minimized Chat (Circular Avatar) -->
                    <div class="chat-minimized hidden w-14 h-14 bg-gradient-to-br ${user.color} rounded-full shadow-lg border-2 border-white flex items-center justify-center text-white font-semibold text-sm cursor-pointer hover:scale-110 transition-transform relative" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <span class="chat-minimized-avatar">${user.initials}</span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white flex items-center justify-center">
                            <span class="text-[8px] text-white font-bold">1</span>
                        </span>
                    </div>
                </div>
            `;

            return chatHTML;
        }

        // Open or restore chat
        window.openMessagesPopup = function(userId = null, userName = 'John Doe', userInitials = 'JD') {
            if (!container) return;

            const user = userData[userId] || { 
                name: userName, 
                initials: userInitials, 
                color: 'from-purple-400 to-purple-600', 
                status: 'Active now' 
            };

            // Check if chat already exists
            if (activeChats.has(userId)) {
                const existingChat = activeChats.get(userId);
                const chatElement = document.getElementById(existingChat);
                if (chatElement) {
                    // Restore if minimized
                    const isExpanded = chatElement.getAttribute('data-expanded') === 'true';
                    if (!isExpanded) {
                        chatElement.setAttribute('data-expanded', 'true');
                        chatElement.querySelector('.chat-expanded').classList.remove('hidden');
                        chatElement.querySelector('.chat-minimized').classList.add('hidden');
                    }
                    // Bring to front (move to beginning of container for z-index)
                    container.insertBefore(chatElement, container.firstChild);
                    updateChatZIndex();
                    updateContainerLayout();
                    return;
                } else {
                    activeChats.delete(userId);
                }
            }

            // Create new chat
            const chatHTML = createChatPopup(userId, user);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = chatHTML;
            const chatElement = tempDiv.firstElementChild;
            
            // Insert at the beginning (so it appears at the bottom, but is on top in DOM for z-index)
            container.insertBefore(chatElement, container.firstChild);
            activeChats.set(userId, chatElement.id);
            
            // Update z-index for all chats (most recent on top)
            updateChatZIndex();
            
            // Update container layout
            updateContainerLayout();

            // Setup event listeners for this chat
            setupChatListeners(chatElement, userId);

            // Scroll to bottom
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        };

        // Setup event listeners for a chat element
        function setupChatListeners(chatElement, userId) {
            const minimizeBtn = chatElement.querySelector('.chat-minimize-btn');
            const closeBtn = chatElement.querySelector('.chat-close-btn');
            const minimizedBtn = chatElement.querySelector('.chat-minimized');
            const messageForm = chatElement.querySelector('.chat-form');
            const emojiBtn = chatElement.querySelector('.chat-emoji-btn');
            const emojiPicker = chatElement.querySelector('.emoji-picker');
            const attachBtn = chatElement.querySelector('.chat-attach-btn');
            const fileInput = chatElement.querySelector('.chat-file-input');
            const filePreview = chatElement.querySelector('.chat-file-preview');
            const voiceBtn = chatElement.querySelector('.chat-voice-btn');
            const voiceRecorder = chatElement.querySelector('.chat-voice-recorder');
            const voiceCancelBtn = chatElement.querySelector('.voice-cancel-btn');
            const voiceStopBtn = chatElement.querySelector('.voice-stop-btn');
            const voiceSendBtn = chatElement.querySelector('.voice-send-btn');
            const voiceTimerEl = chatElement.querySelector('.voice-timer');

            // Minimize
            if (minimizeBtn) {
                minimizeBtn.addEventListener('click', function() {
                    chatElement.setAttribute('data-expanded', 'false');
                    chatElement.querySelector('.chat-expanded').classList.add('hidden');
                    chatElement.querySelector('.chat-minimized').classList.remove('hidden');
                    updateContainerLayout();
                });
            }

            // Close
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    chatElement.remove();
                    activeChats.delete(userId);
                    updateChatZIndex();
                    updateContainerLayout();
                });
            }

            // Restore from minimized
            if (minimizedBtn) {
                minimizedBtn.addEventListener('click', function() {
                    chatElement.setAttribute('data-expanded', 'true');
                    chatElement.querySelector('.chat-expanded').classList.remove('hidden');
                    chatElement.querySelector('.chat-minimized').classList.add('hidden');
                    // Bring to front
                    container.insertBefore(chatElement, container.firstChild);
                    updateChatZIndex();
                    updateContainerLayout();
                });
            }

            // Emoji picker toggle
            if (emojiBtn && emojiPicker) {
                emojiBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    emojiPicker.classList.toggle('hidden');
                });

                // Hide emoji picker when clicking outside
                document.addEventListener('click', function(e) {
                    if (emojiPicker && !emojiPicker.contains(e.target) && e.target !== emojiBtn) {
                        emojiPicker.classList.add('hidden');
                    }
                });

                // Emoji search functionality
                const emojiSearchInput = emojiPicker.querySelector('.emoji-search-input');
                if (emojiSearchInput) {
                    // Emoji search mapping (common emoji names/keywords)
                    const emojiKeywords = {
                        'smile': '😀😃😄😁😆😅😂🤣☺️😊😇🙂🙃😉😌😍',
                        'happy': '😀😃😄😁😆😅😂🤣☺️😊😇🙂🙃😉😌😍🥰',
                        'sad': '😞😔😟😕🙁☹️😣😖😫😩🥺😢😭',
                        'love': '❤️🧡💛💚💙💜🖤🤍🤎💔❣️💕💞💓💗💖💘💝💟😍🥰😘',
                        'angry': '😠😡🤬🤯😤',
                        'wow': '😮😲😯😦😧🤯',
                        'hand': '👋🤚🖐️✋🖖👌🤌🤏✌️🤞🤟🤘🤙👈👉👆🖕👇☝️👍👎✊👊🤛🤜👏🙌👐🤲🤝🙏',
                        'wave': '👋',
                        'dog': '🐶',
                        'cat': '🐱',
                        'pizza': '🍕',
                        'food': '🍕🍔🍟🌭🍿🧂🥓🥚🍳🥘🥗🍱🍘🍙🍚🍛🍜🍝🍠🍢🍣🍤🍥🥮🍡🥟🥠🥡',
                        'soccer': '⚽',
                        'ball': '⚽🏀🏈⚾🥎🎾🏐🏉🥏🎱🏓🏸🏒🏑🥍🏏',
                        'car': '🚗',
                        'vehicle': '🚗🚕🚙🚌🚎🏎️🚓🚑🚒🚐🛻🚚🚛🚜',
                        'light': '💡',
                        'bulb': '💡',
                        'heart': '❤️🧡💛💚💙💜🖤🤍🤎💔❣️💕💞💓💗💖💘💝💟',
                        'thumbs': '👍👎',
                        'ok': '👌',
                        'fire': '🔥',
                        'star': '⭐🌟',
                        'party': '🎉🎊🥳',
                        'birthday': '🎂🎉🎊🥳',
                        'cake': '🎂',
                        'coffee': '☕',
                        'drink': '☕🫖🍵🍶🍾🍷🍸🍹🍺🍻🥂🥃🥤🧋🧃🧉🧊',
                        'money': '💰💵💴💶💷💳',
                        'clock': '🕛🕧🕐🕜🕑🕝🕒🕞🕓🕟🕔🕠🕕🕡🕖🕢🕗🕣🕘🕤🕙🕥🕚🕦',
                        'time': '🕛🕧🕐🕜🕑🕝🕒🕞🕓🕟🕔🕠🕕🕡🕖🕢🕗🕣🕘🕤🕙🕥🕚🕦⏰⏲️⏱️⌛⏳⌚',
                    };
                    
                    emojiSearchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        const allEmojiItems = emojiPicker.querySelectorAll('.emoji-item');
                        const allCategories = emojiPicker.querySelectorAll('.emoji-category');
                        
                        if (searchTerm === '') {
                            // Show all categories and emojis when search is empty
                            allCategories.forEach(cat => {
                                cat.querySelectorAll('.emoji-item').forEach(item => {
                                    item.style.display = '';
                                });
                            });
                            // Show active category
                            const activeCategory = emojiPicker.querySelector('.emoji-category.active');
                            if (activeCategory) {
                                allCategories.forEach(cat => cat.classList.add('hidden'));
                                activeCategory.classList.remove('hidden');
                            }
                        } else {
                            // Show all categories for search results
                            allCategories.forEach(cat => {
                                cat.classList.remove('hidden');
                                const items = cat.querySelectorAll('.emoji-item');
                                items.forEach(item => {
                                    item.style.display = 'none';
                                });
                            });
                            
                            // Filter emojis based on search term
                            let foundCount = 0;
                            allEmojiItems.forEach(item => {
                                const emoji = item.getAttribute('data-emoji');
                                let shouldShow = false;
                                
                                // Check if search term matches any keyword
                                for (const [keyword, emojiList] of Object.entries(emojiKeywords)) {
                                    if (keyword.includes(searchTerm) || searchTerm.includes(keyword)) {
                                        if (emojiList.includes(emoji)) {
                                            shouldShow = true;
                                            break;
                                        }
                                    }
                                }
                                
                                // Also show if emoji character itself matches
                                if (emoji.toLowerCase().includes(searchTerm) || searchTerm.includes(emoji)) {
                                    shouldShow = true;
                                }
                                
                                if (shouldShow) {
                                    item.style.display = '';
                                    foundCount++;
                                }
                            });
                            
                            // If no results, show a message (optional)
                            if (foundCount === 0) {
                                // Could show "No emojis found" message
                            }
                        }
                    });

                    // Hide emoji picker when search input loses focus (but allow clicking emojis)
                    emojiSearchInput.addEventListener('blur', function(e) {
                        // Delay to allow emoji clicks
                        setTimeout(function() {
                            // Check if focus moved to an emoji item or category button
                            const activeElement = document.activeElement;
                            const isEmojiRelated = activeElement && (
                                activeElement.classList.contains('emoji-item') ||
                                activeElement.classList.contains('emoji-category-btn') ||
                                emojiPicker.contains(activeElement)
                            );
                            
                            if (!isEmojiRelated && !emojiPicker.contains(activeElement)) {
                                emojiPicker.classList.add('hidden');
                            }
                        }, 200);
                    });
                }

                // Emoji selection
                const emojiItems = emojiPicker.querySelectorAll('.emoji-item');
                emojiItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const emoji = this.getAttribute('data-emoji');
                        const input = chatElement.querySelector('.chat-input');
                        if (input) {
                            input.value += emoji;
                            input.focus();
                        }
                        // Clear search after selection
                        if (emojiSearchInput) {
                            emojiSearchInput.value = '';
                            emojiSearchInput.dispatchEvent(new Event('input'));
                        }
                    });
                });

                // Emoji category switching
                const categoryBtns = emojiPicker.querySelectorAll('.emoji-category-btn');
                const categoryDivs = emojiPicker.querySelectorAll('.emoji-category');
                
                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const category = this.getAttribute('data-category');
                        
                        // Remove active class from all buttons
                        categoryBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Hide all categories
                        categoryDivs.forEach(div => {
                            div.classList.add('hidden');
                            div.classList.remove('active');
                        });
                        
                        // Show selected category
                        const selectedCategory = emojiPicker.querySelector(`.emoji-category[data-category="${category}"]`);
                        if (selectedCategory) {
                            selectedCategory.classList.remove('hidden');
                            selectedCategory.classList.add('active');
                        }
                        
                        // Clear search when switching categories
                        if (emojiSearchInput) {
                            emojiSearchInput.value = '';
                        }
                    });
                });
            }

            // File attachment
            // Store attached files for this chat (accessible to all handlers)
            let attachedFiles = [];
            let mediaRecorder = null;
            let recordedChunks = [];
            let voiceRecordingBlob = null;
            let voiceRecordingUrl = null;
            let voiceTimerInterval = null;
            let voiceSeconds = 0;
            let voiceAutoSend = false;
            
            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', function() {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function(e) {
                    const newFiles = Array.from(e.target.files);
                    if (newFiles.length > 0 && filePreview) {
                        const maxFileSize = 25 * 1024 * 1024; // 25MB in bytes
                        const validFiles = [];
                        const invalidFiles = [];
                        
                        // Validate file sizes
                        newFiles.forEach(file => {
                            if (file.size > maxFileSize) {
                                invalidFiles.push({
                                    name: file.name,
                                    size: file.size
                                });
                            } else {
                                validFiles.push(file);
                            }
                        });
                        
                        // Show error messages for invalid files
                        if (invalidFiles.length > 0) {
                            invalidFiles.forEach(invalidFile => {
                                const sizeInMB = (invalidFile.size / (1024 * 1024)).toFixed(2);
                                alert(`File "${invalidFile.name}" (${sizeInMB} MB) exceeds the 25MB limit and was not added.`);
                            });
                        }
                        
                        // Add only valid files to the attached files array
                        if (validFiles.length > 0) {
                            validFiles.forEach(file => {
                                attachedFiles.push(file);
                            });
                            updateFilePreview();
                        }
                        
                        // Update file input with valid files only
                        const dt = new DataTransfer();
                        attachedFiles.forEach(file => {
                            dt.items.add(file);
                        });
                        fileInput.files = dt.files;
                    }
                });

                // Format file size helper function
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
                }

                function updateFilePreview() {
                    if (!filePreview) return;
                    
                    if (attachedFiles.length === 0) {
                        filePreview.classList.add('hidden');
                        filePreview.innerHTML = '';
                        return;
                    }

                    filePreview.classList.remove('hidden');
                    filePreview.innerHTML = '';
                    
                    attachedFiles.forEach((file, index) => {
                        const fileCard = document.createElement('div');
                        fileCard.className = 'relative flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg border border-gray-300 overflow-hidden group';
                        fileCard.setAttribute('data-file-index', index);
                        
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                fileCard.innerHTML = `
                                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                                    <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-gray-800 text-white rounded-full text-sm flex items-center justify-center hover:bg-gray-700 transition opacity-0 group-hover:opacity-100 remove-file shadow-lg" data-file-index="${index}" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-1">
                                        <p class="text-[10px] text-white truncate" title="${file.name}">${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}</p>
                                    </div>
                                `;
                                filePreview.appendChild(fileCard);
                                
                                // Attach remove event listener
                                const removeBtn = fileCard.querySelector('.remove-file');
                                if (removeBtn) {
                                    removeBtn.addEventListener('click', function(e) {
                                        e.stopPropagation();
                                        removeFile(index);
                                    });
                                }
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // Determine file icon based on extension
                            const getFileIcon = (fileName) => {
                                const ext = fileName.split('.').pop().toLowerCase();
                                if (['pdf'].includes(ext)) {
                                    return `<svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
                                } else if (['doc', 'docx'].includes(ext)) {
                                    return `<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
                                } else if (['xls', 'xlsx'].includes(ext)) {
                                    return `<svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
                                } else if (['zip', 'rar'].includes(ext)) {
                                    return `<svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14,17H7V15H14M17,13H7V11H17M17,9H7V7H17M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z"/></svg>`;
                                } else {
                                    return `<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
                                }
                            };
                            
                            fileCard.innerHTML = `
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-200">
                                    ${getFileIcon(file.name)}
                                    <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-gray-800 text-white rounded-full text-sm flex items-center justify-center hover:bg-gray-700 transition opacity-0 group-hover:opacity-100 remove-file shadow-lg" data-file-index="${index}" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-1">
                                        <p class="text-[10px] text-white truncate text-center" title="${file.name}">${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}</p>
                                        <p class="text-[9px] text-gray-300 text-center">${formatFileSize(file.size)}</p>
                                    </div>
                                </div>
                            `;
                            filePreview.appendChild(fileCard);
                            
                            // Attach remove event listener
                            const removeBtn = fileCard.querySelector('.remove-file');
                            if (removeBtn) {
                                removeBtn.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    removeFile(index);
                                });
                            }
                        }
                    });
                }

                function removeFile(index) {
                    // Remove file from attached files array
                    attachedFiles.splice(index, 1);
                    
                    // Update the file input
                    const dt = new DataTransfer();
                    attachedFiles.forEach(file => {
                        dt.items.add(file);
                    });
                    fileInput.files = dt.files;
                    
                    // Update preview
                    updateFilePreview();
                }

                // Voice recording (voice clip) UI + logic
                function resetVoiceState() {
                    if (voiceTimerInterval) {
                        clearInterval(voiceTimerInterval);
                        voiceTimerInterval = null;
                    }
                    voiceSeconds = 0;
                    if (voiceTimerEl) {
                        voiceTimerEl.textContent = '0:00';
                    }
                    voiceRecordingBlob = null;
                }

                function formatVoiceTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${m}:${s.toString().padStart(2, '0')}`;
                }

                if (voiceBtn && voiceRecorder && voiceTimerEl) {
                    voiceBtn.addEventListener('click', async function() {
                        try {
                            // If already recording, ignore (use stop/ send instead)
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                return;
                            }

                            // Request microphone access
                            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                            recordedChunks = [];
                            resetVoiceState();

                            mediaRecorder = new MediaRecorder(stream);

                            mediaRecorder.ondataavailable = function(e) {
                                if (e.data && e.data.size > 0) {
                                    recordedChunks.push(e.data);
                                }
                            };

                            mediaRecorder.onstop = function() {
                                // Stop all tracks
                                stream.getTracks().forEach(t => t.stop());

                                if (recordedChunks.length === 0) {
                                    resetVoiceState();
                                    return;
                                }

                                voiceRecordingBlob = new Blob(recordedChunks, { type: 'audio/webm' });
                                voiceRecordingUrl = URL.createObjectURL(voiceRecordingBlob);

                                // If user pressed Send while recording, auto-send after stop
                                if (voiceAutoSend && voiceRecordingBlob && voiceRecordingUrl) {
                                    createAndAppendVoiceMessage();
                                    voiceAutoSend = false;
                                }
                            };

                            mediaRecorder.start();

                            // Show recorder UI, hide normal form
                            voiceRecorder.classList.remove('hidden');
                            if (messageForm) {
                                messageForm.classList.add('hidden');
                            }

                            // Start timer
                            voiceTimerInterval = setInterval(function() {
                                voiceSeconds++;
                                voiceTimerEl.textContent = formatVoiceTime(voiceSeconds);
                            }, 1000);

                        } catch (err) {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Microphone Error',
                                    text: 'Unable to access microphone. Please check your browser permissions.',
                                });
                            } else {
                                alert('Unable to access microphone. Please check your browser permissions.');
                            }
                        }
                    });

                    // Stop recording button
                    if (voiceStopBtn) {
                        voiceStopBtn.addEventListener('click', function() {
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                                if (voiceTimerInterval) {
                                    clearInterval(voiceTimerInterval);
                                    voiceTimerInterval = null;
                                }
                            }
                        });
                    }

                    // Cancel recording
                    if (voiceCancelBtn) {
                        voiceCancelBtn.addEventListener('click', function() {
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                            }
                            resetVoiceState();
                            voiceRecorder.classList.add('hidden');
                            if (messageForm) {
                                messageForm.classList.remove('hidden');
                            }
                        });
                    }

                    // Send voice message
                    if (voiceSendBtn) {
                        voiceSendBtn.addEventListener('click', function() {
                            // If still recording, stop first and mark for auto-send
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                voiceAutoSend = true;
                                mediaRecorder.stop();
                                if (voiceTimerInterval) {
                                    clearInterval(voiceTimerInterval);
                                    voiceTimerInterval = null;
                                }
                                return;
                            }

                            // If already recorded, send immediately
                            if (voiceRecordingBlob && voiceRecordingUrl) {
                                createAndAppendVoiceMessage();
                            }
                        });
                    }
                }

                // Helper: create and append a voice message bubble using current recording
                function createAndAppendVoiceMessage() {
                    if (!voiceRecordingBlob || !voiceRecordingUrl) return;

                    const messagesArea = chatElement.querySelector('.chat-messages');
                    if (messagesArea) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'flex items-start space-x-2 justify-end';

                        const durationLabel = formatVoiceTime(voiceSeconds || Math.max(1, Math.round(voiceRecordingBlob.size / 16000)));

                        const bubbleHtml = `
                            <div class="flex-1 flex justify-end">
                                <div class="max-w-[75%]">
                                    <div class="rounded-2xl px-4 py-3 shadow-sm flex items-center gap-3" style="background-color:#FF1F70;">
                                        <!-- Play / Pause button -->
                                        <button type="button" class="voice-play-toggle flex items-center justify-center w-8 h-8 rounded-full bg-white text-pink-500 hover:bg-gray-100 transition" aria-label="Play voice message">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"></path>
                                            </svg>
                                        </button>
                                        <!-- Fake waveform -->
                                        <div class="flex-1 flex items-center justify-center">
                                            <div class="flex items-end gap-[2px] text-white">
                                                <div class="w-[3px] h-3 rounded-full bg-white opacity-80"></div>
                                                <div class="w-[3px] h-5 rounded-full bg-white opacity-90"></div>
                                                <div class="w-[3px] h-7 rounded-full bg-white opacity-95"></div>
                                                <div class="w-[3px] h-4 rounded-full bg-white opacity-85"></div>
                                                <div class="w-[3px] h-6 rounded-full bg-white opacity-90"></div>
                                                <div class="w-[3px] h-3 rounded-full bg-white opacity-80"></div>
                                            </div>
                                        </div>
                                        <!-- Duration -->
                                        <div class="ml-2 inline-flex items-center justify-center px-3 py-1 rounded-full bg-white text-pink-600 text-xs font-semibold">
                                            ${durationLabel}
                                        </div>
                                        <!-- Hidden audio element -->
                                        <audio class="hidden voice-audio">
                                            <source src="${voiceRecordingUrl}" type="audio/webm">
                                        </audio>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 mr-1 text-right">Sent</p>
                                </div>
                            </div>
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                ${currentUserInitials}
                            </div>
                        `;

                        messageDiv.innerHTML = bubbleHtml;
                        messagesArea.appendChild(messageDiv);
                        messagesArea.scrollTop = messagesArea.scrollHeight;

                        // Wire up play / pause behaviour
                        const audioEl = messageDiv.querySelector('.voice-audio');
                        const playBtn = messageDiv.querySelector('.voice-play-toggle');
                        if (audioEl && playBtn) {
                            playBtn.addEventListener('click', function() {
                                if (audioEl.paused) {
                                    audioEl.play();
                                    // change to pause icon
                                    playBtn.innerHTML = `
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 4h4v16H6zM14 4h4v16h-4z"></path>
                                        </svg>
                                    `;
                                } else {
                                    audioEl.pause();
                                    // back to play icon
                                    playBtn.innerHTML = `
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    `;
                                }
                            });

                            audioEl.addEventListener('ended', function() {
                                playBtn.innerHTML = `
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"></path>
                                    </svg>
                                `;
                            });
                        }
                    }

                    // Also add file to attachedFiles for potential backend upload
                    const fileName = `voice-message-${Date.now()}.webm`;
                    const voiceFile = new File([voiceRecordingBlob], fileName, { type: 'audio/webm' });
                    attachedFiles.push(voiceFile);
                    const dt = new DataTransfer();
                    attachedFiles.forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;

                    // Reset and hide recorder, show normal form
                    resetVoiceState();
                    voiceRecorder.classList.add('hidden');
                    if (messageForm) {
                        messageForm.classList.remove('hidden');
                    }
                }
            }

            // Handle message form submission
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('.chat-input');
                    const files = attachedFiles.length > 0 ? attachedFiles : [];
                    
                    if ((input && input.value.trim()) || files.length > 0) {
                        const messagesArea = chatElement.querySelector('.chat-messages');
                        if (messagesArea) {
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'flex items-start space-x-2 justify-end';
                            const user = userData[userId] || { initials: 'JD', color: 'from-purple-400 to-purple-600' };
                            
                            let messageContent = '';
                            if (files.length > 0) {
                                messageContent = '<div class="message-content space-y-2"></div>';
                                files.forEach(file => {
                                    if (file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const imgDiv = document.createElement('div');
                                            imgDiv.className = 'mb-2';
                                            const img = document.createElement('img');
                                            img.src = e.target.result;
                                            img.alt = file.name;
                                            img.className = 'max-w-[200px] rounded-lg cursor-pointer hover:opacity-90 transition';
                                            img.style.cursor = 'pointer';
                                            img.addEventListener('click', function() {
                                                if (typeof window.openImageViewer === 'function') {
                                                    window.openImageViewer(e.target.result);
                                                }
                                            });
                                            imgDiv.appendChild(img);
                                            const contentDiv = messageDiv.querySelector('.message-content');
                                            if (contentDiv) {
                                                contentDiv.appendChild(imgDiv);
                                            }
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        const fileDiv = document.createElement('div');
                                        fileDiv.className = 'mb-2 p-2 bg-gray-100 rounded-lg';
                                        fileDiv.innerHTML = `
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs text-gray-700">${file.name}</span>
                                            </div>
                                        `;
                                        const contentDiv = messageDiv.querySelector('.message-content');
                                        if (contentDiv) {
                                            contentDiv.appendChild(fileDiv);
                                        }
                                    }
                                });
                            }
                            
                            if (input.value.trim()) {
                                if (messageContent) {
                                    messageContent += `<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-2 shadow-sm mt-2"><p class="text-xs">${input.value}</p></div>`;
                                } else {
                                    messageContent = `<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-2 shadow-sm"><p class="text-xs">${input.value}</p></div>`;
                                }
                            }
                            
                            messageDiv.innerHTML = `
                                <div class="flex-1 flex justify-end">
                                    <div class="max-w-[75%]">
                                        ${messageContent}
                                        <p class="text-xs text-gray-400 mt-1 mr-1 text-right">Just now</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    ${currentUserInitials}
                                </div>
                            `;
                            messagesArea.appendChild(messageDiv);
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                        }
                        
                        // Clear input and attached files
                        if (input) input.value = '';
                        attachedFiles = [];
                        if (fileInput) {
                            fileInput.value = '';
                        }
                        if (filePreview) {
                            filePreview.classList.add('hidden');
                            filePreview.innerHTML = '';
                        }
                    }
                });
            }
        }

        // Update z-index for all chats (most recent on top)
        function updateChatZIndex() {
            if (!container) return;
            const chats = container.querySelectorAll('.messages-chat-popup');
            chats.forEach((chat, index) => {
                chat.style.zIndex = 100 + index;
            });
        }

        // Update container flex direction based on chat states
        function updateContainerLayout() {
            if (!container) return;
            
            const chats = container.querySelectorAll('.messages-chat-popup');
            let hasExpandedChat = false;
            
            chats.forEach((chat) => {
                const isExpanded = chat.getAttribute('data-expanded') === 'true';
                if (isExpanded) {
                    hasExpandedChat = true;
                }
            });
            
            // If there are expanded chats, use row-reverse (horizontal)
            // If all chats are minimized, use col-reverse (vertical stacking)
            if (hasExpandedChat) {
                container.classList.remove('flex-col-reverse');
                container.classList.add('flex-row-reverse');
            } else {
                container.classList.remove('flex-row-reverse');
                container.classList.add('flex-col-reverse');
            }
        }

        // Image Viewer Functions (exposed globally)
        let currentZoom = 1;
        let isDragging = false;
        let startX = 0;
        let startY = 0;
        let scrollLeft = 0;
        let scrollTop = 0;

        window.openImageViewer = function(imageSrc) {
            const modal = document.getElementById('imageViewerModal');
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            
            if (modal && img && container) {
                img.src = imageSrc;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Reset zoom and position
                currentZoom = 1;
                img.style.transform = 'scale(1)';
                img.style.width = 'auto';
                img.style.height = 'auto';
                img.style.maxWidth = '100vw';
                img.style.maxHeight = '100vh';
                img.style.position = 'relative';
                img.style.left = '0';
                img.style.top = '0';
                
                // Reset container
                container.scrollLeft = 0;
                container.scrollTop = 0;
                container.style.overflow = 'hidden';
                
                // Wait for image to load, then set initial size
                img.onload = function() {
                    const naturalWidth = img.naturalWidth;
                    const naturalHeight = img.naturalHeight;
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    // Calculate initial scale to fit viewport (90% to leave some margin)
                    const scaleX = (viewportWidth * 0.9) / naturalWidth;
                    const scaleY = (viewportHeight * 0.9) / naturalHeight;
                    const initialScale = Math.min(scaleX, scaleY, 1); // Don't zoom in initially
                    
                    currentZoom = initialScale;
                    // Set base dimensions
                    img.style.width = (naturalWidth * initialScale) + 'px';
                    img.style.height = (naturalHeight * initialScale) + 'px';
                    img.style.transform = 'scale(1)';
                    img.style.maxWidth = 'none';
                    img.style.maxHeight = 'none';
                };
            }
        };

        window.closeImageViewer = function() {
            const modal = document.getElementById('imageViewerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentZoom = 1;
            }
        };

        // Zoom functions
        function zoomIn() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = Math.min(currentZoom + 0.25, 5); // Max 5x zoom
                img.style.transform = `scale(${currentZoom})`;
                
                // Enable overflow when zoomed in
                if (currentZoom > 1) {
                    container.classList.add('zoom-enabled');
                    container.style.cursor = 'grab';
                }
                updateZoomDisplay();
            }
        }

        function zoomOut() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = Math.max(currentZoom - 0.25, 0.25); // Min 0.25x zoom
                img.style.transform = `scale(${currentZoom})`;
                
                // Disable overflow when zoomed out to fit
                if (currentZoom <= 1) {
                    container.classList.remove('zoom-enabled');
                    container.style.cursor = 'default';
                    container.scrollLeft = 0;
                    container.scrollTop = 0;
                }
                updateZoomDisplay();
            }
        }

        function resetZoom() {
            const img = document.getElementById('viewerImage');
            const container = document.getElementById('imageViewerContainer');
            if (img && container) {
                currentZoom = 1;
                img.style.transform = 'scale(1)';
                container.classList.remove('zoom-enabled');
                container.style.cursor = 'default';
                container.scrollLeft = 0;
                container.scrollTop = 0;
                updateZoomDisplay();
            }
        }

        function updateZoomDisplay() {
            const resetBtn = document.getElementById('resetZoomBtn');
            if (resetBtn) {
                resetBtn.textContent = Math.round(currentZoom * 100) + '%';
            }
        }

        // Download image
        function downloadImage() {
            const img = document.getElementById('viewerImage');
            if (img && img.src) {
                const link = document.createElement('a');
                link.href = img.src;
                link.download = 'image-' + Date.now() + '.png';
                link.setAttribute('data-pdf-modal', 'false');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        // Initialize image viewer
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('closeImageViewer');
            const downloadBtn = document.getElementById('downloadImageViewer');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const resetZoomBtn = document.getElementById('resetZoomBtn');
            const modal = document.getElementById('imageViewerModal');
            const imageContainer = document.getElementById('imageViewerContainer');
            const img = document.getElementById('viewerImage');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', window.closeImageViewer);
            }
            
            if (downloadBtn) {
                downloadBtn.addEventListener('click', downloadImage);
            }
            
            if (zoomInBtn) {
                zoomInBtn.addEventListener('click', zoomIn);
            }
            
            if (zoomOutBtn) {
                zoomOutBtn.addEventListener('click', zoomOut);
            }
            
            if (resetZoomBtn) {
                resetZoomBtn.addEventListener('click', resetZoom);
            }
            
            if (modal) {
                // Close on background click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal || e.target === imageContainer) {
                        window.closeImageViewer();
                    }
                });
                
                // Close on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        window.closeImageViewer();
                    }
                });
            }

            // Mouse wheel zoom
            if (imageContainer && img) {
                imageContainer.addEventListener('wheel', function(e) {
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        if (e.deltaY < 0) {
                            zoomIn();
                        } else {
                            zoomOut();
                        }
                    }
                }, { passive: false });

                // Drag to pan when zoomed
                imageContainer.addEventListener('mousedown', function(e) {
                    if (currentZoom > 1 && e.target === img) {
                        isDragging = true;
                        startX = e.pageX - imageContainer.offsetLeft;
                        startY = e.pageY - imageContainer.offsetTop;
                        scrollLeft = imageContainer.scrollLeft;
                        scrollTop = imageContainer.scrollTop;
                        imageContainer.style.cursor = 'grabbing';
                    }
                });

                document.addEventListener('mousemove', function(e) {
                    if (isDragging && currentZoom > 1) {
                        e.preventDefault();
                        const x = e.pageX - imageContainer.offsetLeft;
                        const y = e.pageY - imageContainer.offsetTop;
                        const walkX = (x - startX) * 2;
                        const walkY = (y - startY) * 2;
                        imageContainer.scrollLeft = scrollLeft - walkX;
                        imageContainer.scrollTop = scrollTop - walkY;
                    }
                });

                document.addEventListener('mouseup', function() {
                    isDragging = false;
                    if (imageContainer) {
                        imageContainer.style.cursor = currentZoom > 1 ? 'grab' : 'default';
                    }
                });
            }

            // Event delegation for images in chat messages (handles dynamically added images)
            if (container) {
                container.addEventListener('click', function(e) {
                    // Check if clicked element is an image or inside an image container
                    const clickedImg = e.target.closest('img');
                    if (clickedImg && clickedImg.closest('.chat-messages')) {
                        // Only open viewer if it's not a preview thumbnail
                        if (!clickedImg.closest('.chat-file-preview')) {
                            e.preventDefault();
                            window.openImageViewer(clickedImg.src);
                        }
                    }
                });
            }
        });

        // Make images clickable when they're added to messages
        window.makeImageClickable = function(imgElement) {
            if (imgElement && imgElement.tagName === 'IMG') {
                imgElement.style.cursor = 'pointer';
                imgElement.classList.add('hover:opacity-90', 'transition');
                imgElement.addEventListener('click', function() {
                    if (typeof window.openImageViewer === 'function') {
                        window.openImageViewer(this.src);
                    }
                });
            }
        };
    })();
</script>

<style>
    .messages-chat-popup {
        animation: slideUp 0.3s ease-out;
        position: relative;
    }

    .chat-expanded {
        animation: slideUp 0.3s ease-out;
    }

    .chat-minimized {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .emoji-picker {
        height: 300px;
    }

    .emoji-grid {
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .emoji-grid::-webkit-scrollbar {
        width: 6px;
    }

    .emoji-grid::-webkit-scrollbar-track {
        background: rgba(229, 231, 235, 0.5);
        border-radius: 3px;
    }

    .emoji-grid::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }

    .emoji-grid::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.7);
    }

    .emoji-item {
        transition: background-color 0.2s;
    }

    .emoji-item:hover {
        background-color: rgba(229, 231, 235, 0.8);
    }

    .emoji-category-btn.active {
        background-color: rgba(59, 130, 246, 0.2);
    }

    .chat-file-preview {
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        min-height: 60px;
    }


    .chat-file-preview::-webkit-scrollbar {
        height: 6px;
    }

    .chat-file-preview::-webkit-scrollbar-track {
        background: rgba(229, 231, 235, 0.5);
        border-radius: 3px;
    }

    .chat-file-preview::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }

    .chat-file-preview::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.7);
    }

    /* Image Viewer Modal Styles */
    #imageViewerModal {
        backdrop-filter: blur(4px);
    }
    
    #imageViewerContainer {
        overflow: hidden !important;
        position: relative;
    }
    
    #imageViewerContainer.zoom-enabled {
        overflow: auto !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }
    
    #imageViewerContainer.zoom-enabled::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    #imageViewerContainer.zoom-enabled::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #imageViewerContainer.zoom-enabled::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }
    
    #imageViewerContainer.zoom-enabled::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    #viewerImage {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        user-select: none;
        -webkit-user-drag: none;
    }
    
    #closeImageViewer,
    #downloadImageViewer,
    #zoomInBtn,
    #zoomOutBtn,
    #resetZoomBtn {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    
    #closeImageViewer:hover,
    #downloadImageViewer:hover,
    #zoomInBtn:hover,
    #zoomOutBtn:hover,
    #resetZoomBtn:hover {
        transform: scale(1.1);
    }
    
    /* Make chat images clickable */
    .chat-messages img {
        cursor: pointer;
        transition: opacity 0.2s ease;
    }
    
    .chat-messages img:hover {
        opacity: 0.9;
    }

    @media (max-width: 640px) {
        #messagesPopupContainer {
            right: 0.5rem;
            left: 0.5rem;
        }
        
        .chat-expanded {
            width: 100% !important;
            max-width: 100% !important;
        }

        .emoji-picker {
            width: calc(100vw - 2rem) !important;
            left: 0 !important;
            right: 0 !important;
        }
    }
</style>
