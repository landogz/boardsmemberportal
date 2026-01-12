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
            <div class="flex items-center gap-1 bg-black bg-opacity-60 hover:bg-opacity-70 rounded-lg p-1 backdrop-blur-sm">
                <button id="zoomOutBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition" title="Zoom Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                <button id="resetZoomBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition text-xs font-semibold" title="Reset Zoom">
                    100%
                </button>
                <button id="zoomInBtn" class="w-8 h-8 bg-black bg-opacity-40 hover:bg-opacity-60 rounded flex items-center justify-center text-white transition" title="Zoom In">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                    </svg>
                </button>
            </div>
            <!-- Download Button -->
            <button id="downloadImageViewer" class="w-10 h-10 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full flex items-center justify-center text-white transition backdrop-blur-sm" title="Download Image">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
            </button>
            <!-- Close Button -->
            <button id="closeImageViewer" class="w-10 h-10 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full flex items-center justify-center text-white transition backdrop-blur-sm" title="Close">
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
        const lastSentMessageIds = new Map(); // userId -> last sent message ID for "Seen" indicator
        
        // Storage key for persisting chats
        const STORAGE_KEY = 'activeChats';
        
        // Save active chats to localStorage
        function saveActiveChats() {
            const chatsData = [];
            activeChats.forEach((chatId, userId) => {
                const chatElement = document.getElementById(chatId);
                if (chatElement) {
                    const isExpanded = chatElement.getAttribute('data-expanded') === 'true';
                    chatsData.push({
                        userId: userId,
                        chatId: chatId,
                        isExpanded: isExpanded
                    });
                }
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(chatsData));
        }
        
        // Restore active chats from localStorage
        function restoreActiveChats() {
            try {
                const savedChats = localStorage.getItem(STORAGE_KEY);
                if (savedChats) {
                    const chatsData = JSON.parse(savedChats);
                    // Restore chats in reverse order (most recent first)
                    chatsData.reverse().forEach(chatData => {
                        // Only restore if chat doesn't already exist
                        if (!activeChats.has(chatData.userId)) {
                            // Ensure isExpanded is a boolean
                            const shouldExpand = chatData.isExpanded === true || chatData.isExpanded === 'true';
                            // Fetch user profile and restore chat with saved state
                            fetchUserProfile(chatData.userId, null, null, shouldExpand);
                        }
                    });
                }
            } catch (error) {
                console.error('Error restoring chats:', error);
            }
        }
        
        // Clear saved chats from localStorage
        function clearSavedChats() {
            localStorage.removeItem(STORAGE_KEY);
        }

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
        
        // Get current user profile picture
        let currentUserProfilePicture = null;
        @php
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->profile_picture) {
                $currentUserMedia = \App\Models\MediaLibrary::find($currentUser->profile_picture);
                if ($currentUserMedia) {
                    $currentUserProfilePicUrl = asset('storage/' . $currentUserMedia->file_path);
                } else {
                    $currentUserProfilePicUrl = null;
                }
            } else {
                $currentUserProfilePicUrl = null;
            }
        @endphp
        @if($currentUserProfilePicUrl ?? null)
            currentUserProfilePicture = @json($currentUserProfilePicUrl);
        @endif

        // Create chat popup HTML
        function createChatPopup(userId, user) {
            const chatId = `chat-${userId}-${chatCounter++}`;
            const isExpanded = true;
            
            const chatHTML = `
                <div id="${chatId}" class="messages-chat-popup" data-user-id="${userId}" data-expanded="true">
                    <!-- Expanded Chat Window -->
                    <div class="chat-expanded w-96 bg-white rounded-t-xl shadow-2xl border border-gray-200 flex flex-col" style="max-height: 600px; height: 500px;">
                        <!-- Popup Header -->
                        <div class="messages-chat-header flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 cursor-move">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    ${user.profilePicture ? 
                                        `<img src="${user.profilePicture}" alt="${user.name || 'Chat'}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 chat-avatar">` :
                                        `<div class="w-8 h-8 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs chat-avatar">${user.initials || 'C'}</div>`
                                    }
                                    ${user.isGroup ? 
                                        `<i class="fas fa-users text-xs text-gray-500 absolute -top-1 -right-1 bg-white rounded-full p-1"></i>` :
                                        `<div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white" style="background-color: ${user.status === 'Active now' ? '#3fbb46' : '#9ca3af'};"></div>`
                                    }
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800 chat-name">${user.name || 'Chat'}</h3>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        ${user.privilege ? `<span class="text-xs text-gray-600 font-medium">${user.privilege.charAt(0).toUpperCase() + user.privilege.slice(1)}</span>` : ''}
                                        ${user.position ? `<span class="text-xs text-gray-500">${user.position}</span>` : ''}
                                        ${(user.privilege || user.position) ? '<span class="text-gray-300">·</span>' : ''}
                                        <p class="text-xs text-gray-500 chat-status">${user.status || 'Offline'}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="popup-single-chat-settings-btn hidden p-1 hover:bg-gray-200 rounded transition" data-user-id="${userId}" aria-label="Chat settings" style="cursor: pointer;">
                                    <i class="fas fa-cog text-gray-600 text-base" style="pointer-events: none;"></i>
                                </button>
                                <button type="button" class="popup-group-settings-btn hidden p-1 hover:bg-gray-200 rounded transition" data-user-id="${userId}" aria-label="Group settings" style="cursor: pointer;">
                                    <i class="fas fa-cog text-gray-600 text-base" style="pointer-events: none;"></i>
                                </button>
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
                        <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-4 bg-gray-50 chat-messages" style="max-height: 400px;">
                            <!-- Messages will be loaded dynamically -->
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin"></i> Loading messages...
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
                            <form class="chat-form flex items-center space-x-1 sm:space-x-2 px-2 sm:px-3 flex-nowrap overflow-hidden">
                                <!-- File Attachment Button -->
                                <!-- Compressed Icons Container -->
                                <div class="flex items-center flex-shrink-0" style="gap: 0rem;">
                                    <!-- Attach Files Button -->
                                    <button type="button" class="chat-attach-btn p-1 sm:p-1.5 text-blue-600 hover:bg-gray-100 rounded-full transition min-w-[32px] min-h-[32px] sm:min-w-[36px] sm:min-h-[36px] flex items-center justify-center" title="Attach files">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </button>
                                    <input type="file" class="hidden chat-file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                    <!-- Voice Clip Button -->
                                    <button type="button" class="chat-voice-btn p-1 sm:p-1.5 text-red-500 hover:bg-gray-100 rounded-full transition min-w-[32px] min-h-[32px] sm:min-w-[36px] sm:min-h-[36px] flex items-center justify-center" title="Record voice message">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path>
                                            <path d="M19 11a1 1 0 0 0-2 0 5 5 0 0 1-10 0 1 1 0 0 0-2 0 7.002 7.002 0 0 0 6 6.92V21h-2a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-2v-3.08A7.002 7.002 0 0 0 19 11z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <input type="text" placeholder="Type a message..." class="chat-input flex-1 min-w-0 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <!-- Emoji Button -->
                                <button type="button" class="chat-emoji-btn p-1 sm:p-2 text-yellow-500 hover:bg-gray-100 rounded-full transition min-w-[32px] min-h-[32px] sm:min-w-[36px] sm:min-h-[36px] flex items-center justify-center flex-shrink-0" title="Add emoji">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button type="submit" class="p-1.5 sm:p-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition min-w-[32px] min-h-[32px] sm:min-w-[36px] sm:min-h-[36px] flex items-center justify-center flex-shrink-0" title="Send message">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Minimized Chat (Circular Avatar) -->
                    <div class="chat-minimized-wrapper relative inline-block">
                        <div class="chat-minimized hidden w-14 h-14 rounded-full shadow-lg border-2 border-white cursor-pointer hover:scale-110 transition-transform relative overflow-hidden" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                            ${user.profilePicture ? 
                                `<img src="${user.profilePicture}" alt="${user.name}" class="w-full h-full object-cover rounded-full">` :
                                `<div class="w-full h-full bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    <span class="chat-minimized-avatar">${user.initials}</span>
                                </div>`
                            }
                            <span class="chat-unread-badge absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white flex items-center justify-center hidden">
                                <span class="text-[8px] text-white font-bold">0</span>
                            </span>
                        </div>
                        <button type="button" class="chat-minimized-close absolute -top-1 -right-1 w-5 h-5 bg-red-500 hover:bg-red-600 rounded-full border-2 border-white flex items-center justify-center text-white text-xs font-bold transition-colors z-10 shadow-md" style="display: none;" onclick="event.stopPropagation(); const chatEl = this.closest('.messages-chat-popup'); if(chatEl) { const userId = chatEl.getAttribute('data-user-id'); if(typeof stopMessagePolling === 'function') stopMessagePolling(userId); chatEl.remove(); if(typeof activeChats !== 'undefined') activeChats.delete(userId); if(typeof updateChatZIndex === 'function') updateChatZIndex(); if(typeof updateContainerLayout === 'function') updateContainerLayout(); }">
                            <i class="fas fa-times text-[8px]"></i>
                        </button>
                    </div>
                </div>
            `;

            return chatHTML;
        }

        // Open or restore chat
        window.openMessagesPopup = function(userId = null, userName = 'John Doe', userInitials = 'JD') {
            if (!container) return;

            // Check if chat already exists
            if (activeChats.has(userId)) {
                const existingChat = activeChats.get(userId);
                const chatElement = document.getElementById(existingChat);
                if (chatElement) {
                    // Get current state
                    const currentState = chatElement.getAttribute('data-expanded') === 'true';
                    // If minimized, expand it (user clicked to open it)
                    if (!currentState) {
                        chatElement.setAttribute('data-expanded', 'true');
                        chatElement.querySelector('.chat-expanded').classList.remove('hidden');
                        chatElement.querySelector('.chat-minimized').classList.add('hidden');
                        // Save state change
                        saveActiveChats();
                        // Load conversation if not already loaded
                        const messagesArea = chatElement.querySelector('.chat-messages');
                        if (messagesArea && (!messagesArea.children.length || messagesArea.innerHTML.includes('Loading') || messagesArea.innerHTML.includes('No messages'))) {
                            const user = userData[userId] || { name: userName, initials: userInitials, color: 'from-purple-400 to-purple-600', status: 'Offline', profilePicture: null };
                            loadConversation(chatElement, userId, user);
                        } else {
                            // If messages are already loaded, scroll to bottom
                            setTimeout(() => {
                                if (messagesArea) {
                                    messagesArea.scrollTop = messagesArea.scrollHeight;
                                }
                            }, 100);
                        }
                    } else {
                        // Chat is already expanded, just scroll to bottom
                        const messagesArea = chatElement.querySelector('.chat-messages');
                        if (messagesArea) {
                            setTimeout(() => {
                                messagesArea.scrollTop = messagesArea.scrollHeight;
                            }, 100);
                        }
                    }
                    // Bring to front (move to beginning of container for z-index)
                    container.insertBefore(chatElement, container.firstChild);
                    updateChatZIndex();
                    updateContainerLayout();
                    // Mark messages as read when chat is expanded
                    if (chatElement.getAttribute('data-expanded') === 'true') {
                        markMessagesAsRead(userId);
                    }
                    return;
                } else {
                    activeChats.delete(userId);
                }
            }

            // Fetch user profile first (new chat, always expanded)
            fetchUserProfile(userId, userName, userInitials, true);
        };

        // Fetch user profile and create chat
        function fetchUserProfile(userId, userName, userInitials, isExpanded = true) {
            // Check if this is a group chat
            const isGroup = userId && userId.startsWith('group_');
            
            if (isGroup) {
                // Fetch group details
                const groupId = userId.replace('group_', '');
                fetch(`{{ route('messages.groups.show', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', groupId), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    let user = {
                        name: userName || 'Group Chat',
                        initials: userInitials || 'GC',
                        color: 'from-purple-400 to-purple-600',
                        status: 'Group chat',
                        profilePicture: null,
                        privilege: null,
                        position: null,
                        isGroup: true
                    };
                    
                    if (data.success && data.group) {
                        const group = data.group;
                        user = {
                            name: group.name || 'Group Chat',
                            initials: group.name ? group.name.substring(0, 2).toUpperCase() : 'GC',
                            color: getGradientColor(groupId),
                            status: 'Group chat',
                            profilePicture: group.avatar || null,
                            privilege: null,
                            position: null,
                            isGroup: true
                        };
                    }
                    
                    // Create chat with group profile
                    createChatWithUser(userId, user, isExpanded);
                    
                    // Immediately check admin status after chat is created
                    if (data.success && data.group) {
                        // Check if current user is admin from the response
                        const currentUserId = @json(Auth::id());
                        let isAdmin = false;
                        
                        if (data.is_admin !== undefined) {
                            isAdmin = data.is_admin;
                        } else if (data.group.is_admin !== undefined) {
                            isAdmin = data.group.is_admin;
                        } else if (data.group.members) {
                            const currentUserMember = data.group.members.find(m => m.id === currentUserId);
                            if (currentUserMember) {
                                isAdmin = currentUserMember.is_admin === true;
                            }
                        }
                        
                        // Update admin status immediately
                        if (typeof window !== 'undefined') {
                            window.popupCurrentGroupIsAdmin = isAdmin;
                            window.popupCurrentGroupId = groupId;
                            window.popupCurrentGroupData = data.group;
                        }
                        
                        // Show/hide settings button immediately
                        setTimeout(() => {
                            // Use multiple selectors to find the chat element
                            let chatElement = document.querySelector(`[data-user-id="${userId}"]`);
                            if (!chatElement) {
                                chatElement = document.querySelector(`.messages-chat-popup[data-user-id="${userId}"]`);
                            }
                            if (!chatElement) {
                                const allChats = document.querySelectorAll('.messages-chat-popup');
                                for (let chat of allChats) {
                                    if (chat.getAttribute('data-user-id') === userId) {
                                        chatElement = chat;
                                        break;
                                    }
                                }
                            }
                            if (chatElement) {
                                const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                                if (settingsBtn) {
                                    if (isAdmin === true) {
                                        settingsBtn.classList.remove('hidden');
                                    } else {
                                        settingsBtn.classList.add('hidden');
                                    }
                                }
                            }
                        }, 100);
                    }
                })
                .catch(error => {
                    console.error('Error fetching group profile:', error);
                    // Fallback to default group
                    const user = {
                        name: userName || 'Group Chat',
                        initials: userInitials || 'GC',
                        color: 'from-purple-400 to-purple-600',
                        status: 'Group chat',
                        profilePicture: null,
                        privilege: null,
                        position: null,
                        isGroup: true
                    };
                    createChatWithUser(userId, user, isExpanded);
                });
            } else {
                // Try to get user from users list first
                fetch('{{ route("messages.users") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    let user = { 
                        name: userName, 
                        initials: userInitials, 
                        color: 'from-purple-400 to-purple-600', 
                        status: 'Offline',
                        profilePicture: null
                    };

                    // Find user in the list
                    if (data.success && data.users) {
                        const foundUser = data.users.find(u => u.id === userId);
                        if (foundUser) {
                            user = {
                                name: `${foundUser.first_name} ${foundUser.last_name}`,
                                initials: (foundUser.first_name?.[0] || '') + (foundUser.last_name?.[0] || ''),
                                color: getGradientColor(foundUser.id),
                                status: foundUser.is_online ? 'Active now' : (foundUser.last_activity ? getTimeAgo(foundUser.last_activity) : 'Offline'),
                                profilePicture: foundUser.profile_picture_url,
                                privilege: foundUser.privilege || null,
                                position: foundUser.position || null
                            };
                        }
                    }

                    // Create chat with user profile
                    createChatWithUser(userId, user, isExpanded);
                })
                .catch(error => {
                    console.error('Error fetching user profile:', error);
                    // Fallback to default user
                    const user = { 
                        name: userName, 
                        initials: userInitials, 
                        color: 'from-purple-400 to-purple-600', 
                        status: 'Offline',
                        profilePicture: null,
                        privilege: null,
                        position: null
                    };
                    createChatWithUser(userId, user, isExpanded);
                });
            }
        }

        // Get gradient color based on user ID
        function getGradientColor(userId) {
            const gradients = [
                'from-purple-400 to-purple-600',
                'from-blue-400 to-blue-600',
                'from-green-400 to-green-600',
                'from-indigo-400 to-indigo-600',
                'from-yellow-400 to-orange-500',
                'from-pink-400 to-pink-600',
                'from-red-400 to-red-600',
            ];
            if (!userId) return gradients[0];
            const index = (userId.charCodeAt(0) || 0) % gradients.length;
            return gradients[index];
        }

        // Create chat with user profile
        function createChatWithUser(userId, user, isExpanded = true) {
            // Store user data
            userData[userId] = user;

            // Create new chat
            const chatHTML = createChatPopup(userId, user);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = chatHTML;
            const chatElement = tempDiv.firstElementChild;
            
            // Clear theme if switching to a different chat
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (messagesArea) {
                const previousThemeGroupId = messagesArea.getAttribute('data-theme-group-id');
                const isGroupChat = userId && userId.startsWith('group_');
                const currentGroupId = isGroupChat ? userId.replace('group_', '') : null;
                
                // Clear theme if:
                // 1. Switching to a non-group chat
                // 2. Switching to a different group chat
                if (!isGroupChat || (previousThemeGroupId && previousThemeGroupId !== currentGroupId)) {
                    // Reset to default appearance
                    messagesArea.style.background = '';
                    messagesArea.style.backgroundImage = '';
                    messagesArea.style.backgroundSize = '';
                    messagesArea.style.backgroundPosition = '';
                    messagesArea.style.backgroundRepeat = '';
                    messagesArea.removeAttribute('data-theme-id');
                    messagesArea.removeAttribute('data-theme-group-id');
                    
                    // Reset header
                    const chatHeader = chatElement.querySelector('.messages-chat-header');
                    if (chatHeader) {
                        chatHeader.style.backgroundColor = '';
                        chatHeader.style.borderColor = '';
                        const headerText = chatHeader.querySelectorAll('h3, p, span');
                        headerText.forEach(el => el.style.color = '');
                        const headerIcons = chatHeader.querySelectorAll('.popup-group-settings-btn i');
                        headerIcons.forEach(icon => icon.style.color = '');
                    }
                    
                    // Reset message bubbles to default
                    messagesArea.querySelectorAll('[style*="background"]').forEach(el => {
                        if (el.classList.contains('voice-message-container') || 
                            el.classList.contains('bg-gradient-to-r') || 
                            el.classList.contains('bg-white')) {
                            el.style.background = '';
                            el.style.color = '';
                        }
                    });
                }
            }
            
            // Set initial expanded state
            chatElement.setAttribute('data-expanded', isExpanded ? 'true' : 'false');
            if (!isExpanded) {
                chatElement.querySelector('.chat-expanded').classList.add('hidden');
                chatElement.querySelector('.chat-minimized').classList.remove('hidden');
            }
            
            // Insert at the beginning (so it appears at the bottom, but is on top in DOM for z-index)
            container.insertBefore(chatElement, container.firstChild);
            activeChats.set(userId, chatElement.id);
            
            // Update z-index for all chats (most recent on top)
            updateChatZIndex();
            
            // Update container layout
            updateContainerLayout();

            // Setup event listeners for this chat
            setupChatListeners(chatElement, userId);

            // Check admin status for group chats
            if (userId && userId.startsWith('group_')) {
                const groupId = userId.replace('group_', '');
                // Load group details to check admin status
                if (typeof popupLoadGroupDetails === 'function') {
                    popupLoadGroupDetails(groupId, userId).then(() => {
                        // Show/hide settings button based on admin status
                        setTimeout(() => {
                            const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                            const singleChatSettingsBtn = chatElement.querySelector('.popup-single-chat-settings-btn');
                            if (settingsBtn) {
                                if (window.popupCurrentGroupIsAdmin === true) {
                                    settingsBtn.classList.remove('hidden');
                                } else {
                                    settingsBtn.classList.add('hidden');
                                }
                            }
                            if (singleChatSettingsBtn) {
                                singleChatSettingsBtn.classList.add('hidden');
                            }
                        }, 100);
                    }).catch(error => {
                        console.error('Error loading group details for admin check:', error);
                    });
                }
            } else {
                // Single chat - show single chat settings button, hide group settings button
                setTimeout(() => {
                    const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                    const singleChatSettingsBtn = chatElement.querySelector('.popup-single-chat-settings-btn');
                    if (settingsBtn) {
                        settingsBtn.classList.add('hidden');
                    }
                    if (singleChatSettingsBtn) {
                        singleChatSettingsBtn.classList.remove('hidden');
                        // Load and apply theme for single chat
                        popupLoadSingleChatTheme(userId, chatElement);
                    }
                }, 100);
            }
            
            // Load conversation history only if expanded (don't load if minimized to save resources)
            // Use the parameter value since we just set it
            if (isExpanded) {
                loadConversation(chatElement, userId, user);
                // Note: Scroll to bottom is handled inside loadConversation after messages are loaded
            } else {
                // If minimized, just load the unread count for the badge
                updateMinimizedUnreadBadge(chatElement, userId);
            }
            
            // Save to localStorage after a small delay to ensure DOM is updated
            setTimeout(() => {
                saveActiveChats();
            }, 100);
        }

        // Load conversation history
        function loadConversation(chatElement, userId, user = null) {
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;

            // Show loading
            messagesArea.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>';

            fetch(`{{ route('messages.conversation', ':userId') }}`.replace(':userId', userId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Check if data is encrypted and decrypt it
                let processMessages = (messages) => {
                    if (messages && messages.length > 0) {
                        messagesArea.innerHTML = '';
                        
                        // Track last sent message ID for "Seen" indicator
                        const currentUserId = @json(Auth::id());
                        const sentMessages = messages.filter(m => m.is_sender);
                        if (sentMessages.length > 0) {
                            // Find the most recent sent message
                            const sortedSent = sentMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            lastSentMessageIds.set(userId, sortedSent[0].id);
                        } else {
                            lastSentMessageIds.set(userId, null);
                        }
                        
                        let previousMsg = null;
                        messages.forEach(msg => {
                            appendMessage(chatElement, msg, userId, previousMsg);
                            previousMsg = msg;
                        });
                        
                        // Scroll to bottom after messages are rendered
                        setTimeout(() => {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                        }, 100);
                        
                        // Mark messages as read only if chat is expanded (not minimized)
                        const isExpanded = chatElement.getAttribute('data-expanded') === 'true';
                        if (isExpanded) {
                            markMessagesAsRead(userId);
                        }
                        
                        // Load and apply theme for group chats
                        const isGroupChat = userId && userId.startsWith('group_');
                        if (isGroupChat) {
                            const groupId = userId.replace('group_', '');
                            window.popupActiveGroupChatUserId = userId;
                            window.popupCurrentGroupId = groupId;
                            
                            // Load group details to get theme
                            if (typeof popupLoadGroupDetails === 'function') {
                                popupLoadGroupDetails(groupId, userId).then(() => {
                                    if (window.popupCurrentGroupData && window.popupCurrentGroupData.theme) {
                                        if (window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                                            popupApplyThemeToChat(window.popupCurrentGroupData.theme);
                                        } else {
                                            // Load themes first, then apply
                                            popupLoadThemes().then(() => {
                                                if (window.popupCurrentGroupData?.theme) {
                                                    popupApplyThemeToChat(window.popupCurrentGroupData.theme);
                                                }
                                            });
                                        }
                                    }
                                }).catch(error => {
                                    console.error('Error loading group theme:', error);
                                });
                            }
                        }
                        
                        // Start polling for new messages
                        startMessagePolling(chatElement, userId);
                    } else {
                        // Show empty state with user profile
                        showEmptyConversationState(messagesArea, userId, user);
                    }
                };
                
                if (data.success) {
                    if (data.encrypted && data.data) {
                        // Decrypt the data
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch('{{ route("messages.decrypt") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ encrypted_data: data.data })
                        })
                        .then(response => response.json())
                        .then(decryptedData => {
                            if (decryptedData.success && decryptedData.messages) {
                                processMessages(decryptedData.messages);
                            } else {
                                messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Failed to decrypt messages. Please refresh the page.</div>';
                            }
                        })
                        .catch(error => {
                            messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Failed to decrypt messages. Please refresh the page.</div>';
                        });
                    } else if (data.messages) {
                        // Not encrypted, process directly
                        processMessages(data.messages);
                    } else {
                        // Show empty state with user profile
                        showEmptyConversationState(messagesArea, userId, user);
                    }
                } else {
                    // Show empty state with user profile
                    showEmptyConversationState(messagesArea, userId, user);
                }
            })
            .catch(error => {
                messagesArea.innerHTML = '<div class="p-4 text-center text-red-500">Error loading messages</div>';
            });
        }

        // Show empty conversation state with user profile
        function showEmptyConversationState(messagesArea, userId, user = null) {
            if (!user) {
                user = userData[userId] || {
                    name: 'User',
                    initials: 'U',
                    profilePicture: null
                };
            }

            let avatarHtml = '';
            if (user.profilePicture) {
                avatarHtml = `<img src="${user.profilePicture}" alt="${user.name}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 mx-auto mb-4">`;
            } else {
                avatarHtml = `<div class="w-20 h-20 rounded-full bg-gradient-to-br ${user.color || 'from-purple-400 to-purple-600'} flex items-center justify-center text-white font-semibold text-2xl mx-auto mb-4">${user.initials || 'U'}</div>`;
            }

            messagesArea.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    ${avatarHtml}
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">${user.name || 'User'}</h3>
                    <p class="text-sm text-gray-500 mb-6">No messages yet. Start the conversation!</p>
                    <div class="text-xs text-gray-400">
                        <i class="fas fa-comment-dots mr-1"></i>
                        Type a message below to get started
                    </div>
                </div>
            `;
        }

        // Start polling for new messages
        const pollingIntervals = new Map();
        function startMessagePolling(chatElement, userId) {
            // Clear existing interval if any
            if (pollingIntervals.has(userId)) {
                clearInterval(pollingIntervals.get(userId));
            }

            let lastMessageTime = null;
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (messagesArea && messagesArea.lastElementChild) {
                const lastMsg = messagesArea.lastElementChild;
                const timeAttr = lastMsg.getAttribute('data-created-at');
                if (timeAttr) lastMessageTime = timeAttr;
            }

            const interval = setInterval(() => {
                fetch(`{{ route('messages.new', ':userId') }}?since=${lastMessageTime || ''}`.replace(':userId', userId), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages && data.messages.length > 0) {
                        // Clear empty state if it exists
                        const emptyState = messagesArea.querySelector('.flex.flex-col.items-center');
                        if (emptyState) {
                            messagesArea.innerHTML = '';
                        }
                        
                        // Track last sent message ID for "Seen" indicator
                        const currentUserId = @json(Auth::id());
                        const sentMessages = data.messages.filter(m => m.is_sender);
                        if (sentMessages.length > 0) {
                            // Find the most recent sent message
                            const sortedSent = sentMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            lastSentMessageIds.set(userId, sortedSent[0].id);
                        }
                        
                        // Get all existing messages to find previous message for timestamp separator
                        const existingMessages = Array.from(messagesArea.querySelectorAll('[data-message-id]'));
                        const lastExistingMsg = existingMessages.length > 0 
                            ? existingMessages[existingMessages.length - 1] 
                            : null;
                        const previousMsgData = lastExistingMsg 
                            ? { created_at: lastExistingMsg.getAttribute('data-created-at') }
                            : null;
                        
                        let hasNewMessages = false;
                        let currentPreviousMsg = previousMsgData;
                        data.messages.forEach(msg => {
                            // Check if message already exists before appending
                            const existingMessage = messagesArea.querySelector(`[data-message-id="${msg.id}"]`);
                            if (!existingMessage) {
                                // If this is a sent message, remove any temp messages (they're being replaced by the real message)
                                if (msg.is_sender) {
                                    const tempMessages = messagesArea.querySelectorAll('[data-temp-message="true"]');
                                    tempMessages.forEach(tempMsg => {
                                        removeTempMessage(chatElement, tempMsg.getAttribute('data-message-id'));
                                    });
                                }
                                
                                appendMessage(chatElement, msg, userId, currentPreviousMsg);
                                lastMessageTime = msg.created_at;
                                hasNewMessages = true;
                                currentPreviousMsg = msg;
                            } else {
                                // Update lastMessageTime even if message exists (for proper polling)
                                const existingTime = existingMessage.getAttribute('data-created-at');
                                if (existingTime && msg.created_at > existingTime) {
                                    lastMessageTime = msg.created_at;
                                }
                                currentPreviousMsg = msg;
                            }
                        });
                        
                        if (hasNewMessages) {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                            
                            // Get the latest message to update conversation list
                            const latestMessage = data.messages[data.messages.length - 1];
                            
                            // Mark new messages as read only if chat is expanded (not minimized)
                            const isExpanded = chatElement.getAttribute('data-expanded') === 'true';
                            if (isExpanded) {
                                markMessagesAsRead(userId);
                                
                                // Update header dropdown conversation list
                                if (typeof window.updateDropdownItemBadge === 'function' && latestMessage) {
                                    // Get unread count for this conversation (should be 0 if we just marked as read)
                                    const unreadCount = 0;
                                    // Get total unread count from header badge
                                    const headerBadge = document.getElementById('messagesBadgeCount') || document.getElementById('adminMessagesBadgeCount');
                                    let totalUnread = 0;
                                    if (headerBadge && !headerBadge.classList.contains('hidden')) {
                                        const badgeText = headerBadge.textContent.trim();
                                        totalUnread = parseInt(badgeText) || 0;
                                    }
                                    window.updateDropdownItemBadge(userId, unreadCount, totalUnread);
                                }
                            }
                            
                            // Update unread count badge on minimized chat
                            updateMinimizedUnreadBadge(chatElement, userId);
                            
                            // Update header badge count
                            if (typeof window.loadUnreadCount === 'function') {
                                window.loadUnreadCount();
                            } else if (typeof window.loadAdminUnreadCount === 'function') {
                                window.loadAdminUnreadCount();
                            }
                        }
                    }
                })
                .catch(error => console.error('Error polling messages:', error));
                
                // Also update reactions and read status for visible messages (staggered to avoid overwhelming server)
                setTimeout(() => {
                    updateReactionsForVisibleMessages(userId, chatElement);
                }, 100);
                setTimeout(() => {
                    updateReadStatusForVisibleMessages(userId, chatElement);
                }, 150);
            }, 3000); // Poll every 3 seconds

            pollingIntervals.set(userId, interval);
        }
        
        // Update reactions for all visible messages in the current conversation
        function updateReactionsForVisibleMessages(userId, chatElement) {
            if (!userId || !chatElement) return;
            
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;
            
            // Get all visible message IDs
            const messageElements = messagesArea.querySelectorAll('[data-message-id]');
            if (messageElements.length === 0) return;
            
            const messageIds = Array.from(messageElements).map(el => el.getAttribute('data-message-id'));
            
            // Use batch endpoint to fetch all reactions in a single request
            if (messageIds.length === 0) return;
            
            axios.post(`{{ route('messages.reactions.batch') }}`, {
                message_ids: messageIds
            })
            .then(response => {
                if (response.data.success && response.data.reactions) {
                    // Update reactions for each message
                    Object.keys(response.data.reactions).forEach(messageId => {
                        const reactions = response.data.reactions[messageId];
                        if (reactions && reactions.length > 0) {
                            updateMessageReactions(messageId, reactions, chatElement);
                        } else {
                            // If no reactions, clear the display
                            updateMessageReactions(messageId, [], chatElement);
                        }
                    });
                }
            })
            .catch(error => {
                // Silently fail - reactions might not exist for all messages
                if (error.response && error.response.status !== 404) {
                    console.debug('Error fetching batch reactions:', error);
                }
            });
        }
        
        // Update read status for the last sent message only
        function updateReadStatusForVisibleMessages(userId, chatElement) {
            if (!userId || !chatElement) return;
            const lastSentMessageId = lastSentMessageIds.get(userId);
            if (!lastSentMessageId) return; // No sent messages yet
            
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;
            
            // Only check the last sent message
            axios.get(`{{ route('messages.conversation', ':userId') }}`.replace(':userId', userId))
                .then(response => {
                    if (response.data.success && response.data.messages) {
                        // Find the last sent message
                        const lastSentMessage = response.data.messages
                            .filter(m => m.is_sender)
                            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
                        
                        if (lastSentMessage && lastSentMessage.id == lastSentMessageId) {
                            updateMessageReadStatus(lastSentMessageId, lastSentMessage.is_read, chatElement);
                        }
                    }
                })
                .catch(error => {
                    // Silently fail - conversation might not be accessible
                    if (error.response && error.response.status !== 404) {
                        console.debug('Error fetching read status:', error);
                    }
                });
        }
        
        // Update read status indicator for a specific message (only for last sent message)
        function updateMessageReadStatus(messageId, isRead, chatElement) {
            const lastSentMessageId = lastSentMessageIds.get(chatElement ? chatElement.getAttribute('data-user-id') : null);
            // Only update if this is the last sent message
            if (messageId != lastSentMessageId) return;
            
            const messagesArea = chatElement ? chatElement.querySelector('.chat-messages') : null;
            if (!messagesArea) return;
            
            const messageDiv = messagesArea.querySelector(`[data-message-id="${messageId}"]`);
            if (!messageDiv) return;
            
            // Check if already has seen indicator
            let seenIndicator = messageDiv.querySelector('.message-seen-indicator');
            
            if (isRead) {
                if (!seenIndicator) {
                    // Create and add seen indicator
                    seenIndicator = document.createElement('div');
                    seenIndicator.className = 'flex justify-end mt-1 message-seen-indicator';
                    seenIndicator.innerHTML = '<span class="text-xs text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Seen</span>';
                    
                    // Find where to insert (after reactions, before end of message content)
                    const reactionsDiv = messageDiv.querySelector('.message-reactions');
                    const messageContent = messageDiv.querySelector('.max-w-\\[75\\%\\]') || messageDiv.querySelector('.relative');
                    if (messageContent) {
                        if (reactionsDiv && reactionsDiv.nextSibling) {
                            messageContent.insertBefore(seenIndicator, reactionsDiv.nextSibling);
                        } else {
                            messageContent.appendChild(seenIndicator);
                        }
                    }
                }
            } else {
                // Remove seen indicator if message is unread
                if (seenIndicator) {
                    seenIndicator.remove();
                }
            }
        }
        
        // Update unread count badge on minimized chat
        function updateMinimizedUnreadBadge(chatElement, userId) {
            const minimizedBtn = chatElement.querySelector('.chat-minimized');
            if (!minimizedBtn || minimizedBtn.classList.contains('hidden')) return; // Only update if minimized (visible)
            
            // Get unread count for this conversation
            fetch(`{{ route('messages.conversation', ':userId') }}`.replace(':userId', userId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages) {
                    // Count unread messages (messages sent to current user that are unread)
                    const currentUserId = @json(Auth::id());
                    const unreadCount = data.messages.filter(msg => 
                        !msg.is_sender && !msg.is_read
                    ).length;
                    
                    const unreadBadge = minimizedBtn.querySelector('.chat-unread-badge');
                    const badgeText = unreadBadge?.querySelector('span');
                    
                    if (unreadCount > 0) {
                        if (unreadBadge) {
                            unreadBadge.classList.remove('hidden');
                            if (badgeText) {
                                badgeText.textContent = unreadCount > 99 ? '99+' : unreadCount;
                            }
                        }
                    } else {
                        if (unreadBadge) {
                            unreadBadge.classList.add('hidden');
                        }
                    }
                }
            })
            .catch(error => console.error('Error updating unread badge:', error));
        }

        // Stop polling when chat is closed
        function stopMessagePolling(userId) {
            if (pollingIntervals.has(userId)) {
                clearInterval(pollingIntervals.get(userId));
                pollingIntervals.delete(userId);
            }
        }

        // Format timestamp for separator
        function formatTimestampSeparator(timestamp) {
            if (!timestamp) return '';
            const time = new Date(timestamp);
            const hours = time.getHours();
            const minutes = time.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const displayMinutes = minutes.toString().padStart(2, '0');
            return `${displayHours}:${displayMinutes} ${ampm}`;
        }
        
        // Check if we should show a timestamp separator
        function shouldShowTimestampSeparator(currentMsg, previousMsg) {
            if (!previousMsg) return true; // Show for first message
            
            const currentTime = new Date(currentMsg.created_at);
            const previousTime = new Date(previousMsg.created_at);
            const diffMinutes = (currentTime - previousTime) / (1000 * 60);
            
            // Show separator if messages are more than 5 minutes apart
            return diffMinutes > 5;
        }
        
        // Create timestamp separator element
        function createTimestampSeparator(timestamp) {
            const separatorDiv = document.createElement('div');
            separatorDiv.className = 'flex items-center justify-center my-4';
            separatorDiv.innerHTML = `
                <div class="px-3 py-1 bg-gray-200 rounded-full" style="background-color: #e5e7eb !important;">
                    <span class="text-xs text-gray-600 font-medium" style="color: #4b5563 !important;">${formatTimestampSeparator(timestamp)}</span>
                </div>
            `;
            return separatorDiv;
        }

        // Append a message to the chat
        function appendMessage(chatElement, msg, userId, previousMsg = null) {
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;

            // Check if message already exists to prevent duplicates
            const existingMessage = messagesArea.querySelector(`[data-message-id="${msg.id}"]`);
            if (existingMessage) {
                return; // Message already exists, skip
            }
            
            // Check if there's a temp separator that was added for a temp message
            const tempSeparator = messagesArea.querySelector('[data-temp-separator="true"]');
            let separatorReused = false;
            
            if (tempSeparator) {
                // Check if the timestamps match (within 1 minute) - if so, reuse the separator
                const separatorTimestamp = tempSeparator.getAttribute('data-separator-timestamp');
                const msgTimestamp = formatTimestampSeparator(msg.created_at);
                const msgTime = new Date(msg.created_at);
                const separatorTimeStr = tempSeparator.getAttribute('data-created-at');
                const separatorTime = separatorTimeStr ? new Date(separatorTimeStr) : msgTime;
                const timeDiff = Math.abs(msgTime - separatorTime) / (1000 * 60); // Difference in minutes
                
                if (timeDiff < 1 && separatorTimestamp === msgTimestamp) {
                    // Timestamps match, reuse the separator and remove temp flag
                    tempSeparator.removeAttribute('data-temp-separator');
                    tempSeparator.removeAttribute('data-separator-timestamp');
                    tempSeparator.removeAttribute('data-created-at');
                    separatorReused = true;
                } else {
                    // Timestamps don't match, remove temp separator
                    tempSeparator.remove();
                }
            }
            
            // Only add a new separator if we didn't reuse the temp one
            if (!separatorReused && shouldShowTimestampSeparator(msg, previousMsg)) {
                const separator = createTimestampSeparator(msg.created_at);
                messagesArea.appendChild(separator);
            }

            const isSender = msg.is_sender;
            const senderName = msg.sender ? `${msg.sender.first_name} ${msg.sender.last_name}` : 'User';
            const senderInitials = msg.sender ? (msg.sender.first_name[0] + msg.sender.last_name[0]).toUpperCase() : 'U';
            
            // Check if current chat is a group chat
            const isGroupChat = userId && userId.startsWith('group_');
            
            const messageDiv = document.createElement('div');
            messageDiv.className = isSender 
                ? 'flex items-start space-x-2 justify-end' 
                : 'flex items-start space-x-2';
            messageDiv.setAttribute('data-message-id', msg.id);
            messageDiv.setAttribute('data-created-at', msg.created_at);

            let messageContent = '';
            
            // Handle attachments
            if (msg.attachments && msg.attachments.length > 0) {
                msg.attachments.forEach((attachment, index) => {
                    // Skip if attachment is null or missing required data
                    if (!attachment || (!attachment.url && !attachment.type)) {
                        console.warn('Invalid attachment:', attachment);
                        return;
                    }
                    
                    // Debug: Log attachment info to help diagnose
                    console.log('Processing attachment:', {
                        name: attachment.name,
                        type: attachment.type,
                        url: attachment.url ? 'present' : 'missing',
                        isImageByType: attachment.type && attachment.type.startsWith('image/'),
                        isImageByExt: attachment.name && /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$/i.test(attachment.name)
                    });
                    
                    // Check if it's explicitly a voice/audio message (by name or explicit audio type)
                    // Voice messages might be video/mp4 but have specific naming patterns
                    // Also check for MP3 files by extension or MIME type
                    const isVoiceMessage = (attachment.name && (
                        attachment.name.includes('voice-message') || 
                        attachment.name.includes('voice_') ||
                        attachment.name.includes('recording') ||
                        /\.mp3$/i.test(attachment.name)
                    )) || (attachment.type && (
                        attachment.type.startsWith('audio/') ||
                        attachment.type === 'audio/mpeg'
                    )) || (attachment.url && /\.mp3(\?|$|#)/i.test(attachment.url));
                    
                    // Check if it's a video file (but exclude voice messages)
                    // Prioritize video detection for actual video files
                    const isVideo = !isVoiceMessage && (
                        (attachment.type && attachment.type.startsWith('video/')) || 
                        (attachment.name && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)$/i.test(attachment.name)) ||
                        (attachment.url && /\.(mp4|webm|mov|avi|wmv|flv|mkv|3gp|m4v)(\?|$|#)/i.test(attachment.url))
                    );
                    
                    // Check if it's an audio file (only if not video and not voice message)
                    const isAudio = !isVideo && !isVoiceMessage && (
                        (attachment.type && attachment.type.startsWith('audio/')) ||
                        (attachment.name && /\.(mp3|wav|ogg|m4a|aac)$/i.test(attachment.name)) ||
                        (attachment.url && /\.(mp3|wav|ogg|m4a|aac)(\?|$|#)/i.test(attachment.url))
                    );
                    
                    if (isVideo) {
                        // Video attachment - display with video player
                        if (!attachment.url) {
                            console.warn('Video attachment missing URL:', attachment);
                            return;
                        }
                        const videoName = attachment.name || 'Video';
                        
                        // Determine video MIME type from URL or attachment type
                        let videoType = attachment.type || 'video/mp4';
                        if (!attachment.type && attachment.url) {
                            if (/\.webm(\?|$|#)/i.test(attachment.url)) videoType = 'video/webm';
                            else if (/\.ogg(\?|$|#)/i.test(attachment.url)) videoType = 'video/ogg';
                            else if (/\.mov(\?|$|#)/i.test(attachment.url)) videoType = 'video/quicktime';
                            else if (/\.avi(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-msvideo';
                            else if (/\.wmv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-ms-wmv';
                            else if (/\.flv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-flv';
                            else if (/\.mkv(\?|$|#)/i.test(attachment.url)) videoType = 'video/x-matroska';
                            else videoType = 'video/mp4';
                        }
                        
                        messageContent += `
                            <div class="mb-2 relative group">
                                <video src="${attachment.url}" 
                                       controls 
                                       class="rounded-lg shadow-sm" 
                                       style="max-width: 100%; max-width: 400px; max-height: 500px; width: auto; height: auto; display: block; background: #000;" 
                                       preload="metadata"
                                       playsinline>
                                    <source src="${attachment.url}" type="${videoType}">
                                    <p class="text-gray-500 text-sm p-2">Your browser does not support the video tag. 
                                        <a href="${attachment.url}" download="${videoName}" class="text-blue-500 hover:underline">Download video</a>
                                    </p>
                                </video>
                            </div>
                        `;
                    } else if (isVoiceMessage) {
                        // Voice message - display with voice player
                        if (!attachment.url) {
                            console.warn('Voice message missing URL:', attachment);
                            return;
                        }
                        
                        // Extract duration from filename or use default
                        // Always show in "0:00 / duration" format initially
                        let durationLabel = '0:00 / 0:00';
                        let totalDuration = '0:00';
                        const nameToCheck = attachment.name || attachment.url || '';
                        if (nameToCheck) {
                            // Try to extract from filename format: voice-message-timestamp-m-s.webm or voice-message-timestamp-m-s.mp3
                            const durationMatch = nameToCheck.match(/(\d+)-(\d+)\.(webm|mp3|wav|ogg|m4a|aac|mp4)$/i);
                            if (durationMatch) {
                                const mins = parseInt(durationMatch[1]);
                                const secs = parseInt(durationMatch[2]);
                                totalDuration = `${mins}:${secs.toString().padStart(2, '0')}`;
                                durationLabel = `0:00 / ${totalDuration}`;
                            } else {
                                // Try standard format: m:s
                                const standardMatch = nameToCheck.match(/(\d+):(\d+)/);
                                if (standardMatch) {
                                    totalDuration = `${standardMatch[1]}:${standardMatch[2]}`;
                                    durationLabel = `0:00 / ${totalDuration}`;
                                }
                            }
                        }
                        
                        const voiceBubbleId = `voice-${msg.id}-${index}`;
                        
                        // Get current theme if available
                        const currentThemeId = messagesArea?.getAttribute('data-theme-id');
                        const currentThemeGroupId = messagesArea?.getAttribute('data-theme-group-id');
                        const groupId = isGroupChat ? userId.replace('group_', '') : null;
                        
                        let theme = null;
                        if (currentThemeId && currentThemeGroupId === groupId && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                            theme = window.popupAvailableThemes.find(t => t.id === currentThemeId);
                        }
                        
                        // Use theme colors if available, otherwise use defaults
                        const voiceBubbleBg = isSender 
                            ? (theme ? '' : 'bg-[#FF1F70]') 
                            : 'bg-white border border-gray-200';
                        const voiceBubbleStyle = isSender && theme 
                            ? `background: ${theme.sender_bubble};` 
                            : '';
                        const voiceButtonBg = isSender ? 'bg-white' : 'bg-gray-100';
                        const voiceButtonColor = isSender 
                            ? (theme ? `color: ${theme.sender_bubble};` : 'text-[#FF1F70]') 
                            : 'text-gray-700';
                        const waveformColor = isSender ? 'bg-white' : 'bg-gray-400';
                        const waveformPlayedColor = isSender ? 'bg-white opacity-60' : 'bg-gray-300';
                        const durationTextColor = isSender 
                            ? (theme ? `color: ${theme.sender_text};` : 'text-white') 
                            : 'text-gray-700';
                        
                        messageContent += `
                            <div class="mb-2 rounded-2xl px-4 py-3 shadow-sm flex items-center gap-3 max-w-[280px] sm:max-w-[320px] ${voiceBubbleBg} voice-message-container" style="min-width: 200px; ${voiceBubbleStyle}" data-voice-id="${voiceBubbleId}">
                                <button type="button" class="voice-play-toggle flex items-center justify-center w-9 h-9 rounded-full ${voiceButtonBg} ${voiceButtonColor} hover:opacity-80 transition shadow-sm flex-shrink-0" aria-label="Play voice message" data-voice-id="${voiceBubbleId}">
                                    <svg class="w-4 h-4 play-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                    <svg class="w-4 h-4 pause-icon hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"></path></svg>
                                </button>
                                <div class="flex-1 flex items-center justify-center min-w-0 voice-waveform-container" style="position: relative;">
                                    <div class="flex items-end gap-[2px] voice-waveform" style="position: relative; width: 100%;">
                                        <div class="w-[3px] h-3 rounded-full ${waveformColor} opacity-80 waveform-bar" data-index="0"></div>
                                        <div class="w-[3px] h-5 rounded-full ${waveformColor} opacity-90 waveform-bar" data-index="1"></div>
                                        <div class="w-[3px] h-7 rounded-full ${waveformColor} opacity-95 waveform-bar" data-index="2"></div>
                                        <div class="w-[3px] h-4 rounded-full ${waveformColor} opacity-85 waveform-bar" data-index="3"></div>
                                        <div class="w-[3px] h-6 rounded-full ${waveformColor} opacity-90 waveform-bar" data-index="4"></div>
                                        <div class="w-[3px] h-3 rounded-full ${waveformColor} opacity-80 waveform-bar" data-index="5"></div>
                                    </div>
                                </div>
                                <div class="ml-2 flex flex-col items-end gap-1 flex-shrink-0">
                                    <div class="text-xs font-semibold voice-duration ${durationTextColor}" style="min-width: 50px; text-align: right; ${isSender && theme ? `color: ${theme.sender_text};` : ''}">${durationLabel}</div>
                                    <button type="button" class="voice-speed-toggle text-xs font-semibold ${isSender ? (theme ? 'bg-white' : 'text-[#FF1F70] bg-white') : durationTextColor + ' bg-gray-100'} hover:opacity-80 transition px-1.5 py-0.5 rounded" style="${isSender && theme ? `color: ${theme.sender_bubble};` : ''}" data-voice-id="${voiceBubbleId}" data-speed="1">1x</button>
                                </div>
                                <audio class="hidden voice-audio" id="${voiceBubbleId}" preload="metadata">
                                    ${(() => {
                                        // Detect correct MIME type from URL extension if type is missing or incorrect
                                        let mimeType = attachment.type;
                                        if (!mimeType || mimeType === 'audio/mpeg') {
                                            if (attachment.url.match(/\.mp4(\?|$|#)/i)) {
                                                mimeType = 'audio/mp4';
                                            } else if (attachment.url.match(/\.webm(\?|$|#)/i)) {
                                                mimeType = 'audio/webm';
                                            } else if (attachment.url.match(/\.mp3(\?|$|#)/i)) {
                                                mimeType = 'audio/mpeg';
                                            } else {
                                                mimeType = 'audio/mpeg';
                                            }
                                        }
                                        return `<source src="${attachment.url}" type="${mimeType}">`;
                                    })()}
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        `;
                        
                        // Try to get actual duration from audio element after it loads
                        setTimeout(() => {
                            const audioEl = document.getElementById(voiceBubbleId);
                            if (audioEl) {
                                const updateTotalDuration = function() {
                                    if (audioEl.duration && !isNaN(audioEl.duration)) {
                                        const minutes = Math.floor(audioEl.duration / 60);
                                        const seconds = Math.floor(audioEl.duration % 60);
                                        const totalDurationStr = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                                        const container = audioEl.closest('.voice-message-container');
                                        const durationEl = container?.querySelector('.voice-duration');
                                        if (durationEl) {
                                            // Store total duration on audio element for later use
                                            audioEl.dataset.totalDuration = totalDurationStr;
                                            if (audioEl.currentTime === 0 && audioEl.paused) {
                                                durationEl.textContent = `0:00 / ${totalDurationStr}`;
                                            }
                                        }
                                    }
                                };
                                
                                audioEl.addEventListener('loadedmetadata', updateTotalDuration);
                                
                                // Try to load if not already
                                if (audioEl.readyState === 0) {
                                audioEl.load();
                                } else if (audioEl.readyState >= 1) {
                                    updateTotalDuration();
                                }
                            }
                        }, 100);
                    } else if (isAudio) {
                        // Regular audio file - display with audio player
                        if (!attachment.url) {
                            console.warn('Audio attachment missing URL:', attachment);
                            return;
                        }
                        const audioName = attachment.name || 'Audio';
                        messageContent += `
                            <div class="mb-2 rounded-lg p-3 bg-gray-100 border border-gray-200">
                                <audio src="${attachment.url}" controls class="w-full" preload="metadata">
                                    <source src="${attachment.url}" type="${attachment.type || 'audio/mpeg'}">
                                    Your browser does not support the audio element.
                                    <a href="${attachment.url}" download="${audioName}" class="text-blue-500 hover:underline">Download audio</a>
                                </audio>
                                ${audioName !== 'Audio' ? `<p class="text-xs text-gray-600 mt-1 truncate">${escapeHtml(audioName)}</p>` : ''}
                            </div>
                        `;
                    } else {
                        // Check if it's an image by type, file extension in name, OR file extension in URL
                        // Since name and type might be null, we'll check the URL for image extensions
                        const isImage = (attachment.type && attachment.type.startsWith('image/')) || 
                                       (attachment.name && /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$/i.test(attachment.name)) ||
                                       (attachment.url && /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?|$|#)/i.test(attachment.url));
                        
                        // If we still can't determine, try to detect from URL path patterns
                        // Also check if URL contains common image storage paths
                        const urlHasImagePattern = attachment.url && (
                            /\/images\//i.test(attachment.url) ||
                            /\/photos\//i.test(attachment.url) ||
                            /\/pictures\//i.test(attachment.url) ||
                            /\/media\/.*\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)/i.test(attachment.url) ||
                            /storage\/.*\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)/i.test(attachment.url) ||
                            /storage\/.*\/.*\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)/i.test(attachment.url)
                        );
                        
                        // Extract file extension from URL if name is null
                        let urlExtension = null;
                        if (attachment.url && !attachment.name) {
                            const urlMatch = attachment.url.match(/\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?|$|#)/i);
                            if (urlMatch) {
                                urlExtension = urlMatch[1].toLowerCase();
                            }
                        }
                        
                        const finalIsImage = isImage || urlHasImagePattern || (urlExtension && ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(urlExtension));
                        
                        if (finalIsImage) {
                        // Image attachment - make it clickable to open in viewer
                        if (!attachment.url) {
                            console.warn('Image attachment missing URL:', attachment);
                            return;
                        }
                        const imageName = attachment.name || 'Image';
                        const imageClass = isSender 
                            ? 'max-w-[200px] rounded-lg cursor-pointer hover:opacity-90 transition shadow-sm' 
                            : 'max-w-[200px] rounded-lg cursor-pointer hover:opacity-90 transition shadow-sm';
                        messageContent += `
                                <div class="mb-2 relative group">
                                <img src="${attachment.url}" alt="${imageName}" 
                                     class="${imageClass}"
                                     onclick="if(typeof window.openImageViewer === 'function') window.openImageViewer('${attachment.url}')"
                                         style="max-height: 300px; max-width: 200px; object-fit: contain; display: block;"
                                         onerror="console.error('Failed to load image:', '${attachment.url}'); this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'200\\' height=\\'200\\'%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23999\\'%3EImage not found%3C/text%3E%3C/svg%3E';">
                                    <button onclick="if(typeof window.openImageViewer === 'function') window.openImageViewer('${attachment.url}'); event.stopPropagation();" 
                                            class="absolute top-2 right-2 bg-black bg-opacity-70 hover:bg-opacity-90 text-white rounded-full p-2 transition-all z-10"
                                            title="Open in image viewer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </button>
                            </div>
                        `;
                    } else {
                        // Document/file attachment
                        if (!attachment.url) {
                            console.warn('File attachment missing URL:', attachment);
                            return;
                        }
                        const fileName = attachment.name || 'File';
                        const fileExt = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : '';
                        const fileIcon = getFileIconForAttachment(fileExt);
                            const isPdf = fileExt === 'pdf';
                        const fileBgClass = isSender 
                            ? 'bg-white bg-opacity-20' 
                            : 'bg-gray-100';
                            // PDF files should have black text, other files use the normal styling
                            const fileTextClass = isPdf 
                                ? 'text-black' 
                                : (isSender 
                            ? 'text-white' 
                                    : 'text-gray-700');
                        
                        messageContent += `
                            <div class="mb-2 p-2 ${fileBgClass} rounded-lg">
                                <div class="flex items-center space-x-2">
                                    ${fileIcon}
                                    <a href="${attachment.url}" download="${fileName}" 
                                           class="text-xs ${fileTextClass} hover:underline truncate flex-1 font-medium" 
                                       title="${fileName}">
                                        ${fileName}
                                    </a>
                                </div>
                            </div>
                        `;
                        }
                    }
                });
            }

            // Add text message
            if (msg.message && msg.message.trim()) {
                // Get current theme if available
                const currentThemeId = messagesArea?.getAttribute('data-theme-id');
                const currentThemeGroupId = messagesArea?.getAttribute('data-theme-group-id');
                const currentThemeUserId = messagesArea?.getAttribute('data-theme-user-id');
                const groupId = isGroupChat ? userId.replace('group_', '') : null;
                
                let theme = null;
                if (isGroupChat && currentThemeId && currentThemeGroupId === groupId && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                    theme = window.popupAvailableThemes.find(t => t.id === currentThemeId);
                } else if (!isGroupChat && currentThemeId && currentThemeUserId === userId && popupSingleChatAvailableThemes && popupSingleChatAvailableThemes.length > 0) {
                    theme = popupSingleChatAvailableThemes.find(t => t.id === currentThemeId);
                }
                
                // Apply theme colors for both sender and receiver messages
                let textBubbleBg = '';
                let textBubbleStyle = '';
                let textBubbleClass = '';
                
                if (theme) {
                    // Theme is applied - use theme colors
                    if (isSender) {
                        textBubbleStyle = `background: ${theme.sender_bubble} !important; color: ${theme.sender_text} !important;`;
                        textBubbleBg = ''; // No default classes when theme is applied
                        textBubbleClass = ''; // No default classes when theme is applied
                    } else {
                        textBubbleStyle = `background: ${theme.receiver_bubble} !important; color: ${theme.receiver_text} !important;`;
                        textBubbleBg = ''; // No default classes when theme is applied
                        textBubbleClass = ''; // No default classes when theme is applied
                    }
                } else {
                    // No theme - use default colors
                    if (isSender) {
                        textBubbleBg = 'bg-gradient-to-r from-blue-500 to-purple-600';
                        textBubbleStyle = '';
                        textBubbleClass = 'text-white';
                    } else {
                        textBubbleBg = 'bg-white';
                        textBubbleStyle = '';
                        textBubbleClass = 'text-gray-800';
                    }
                }
                
                if (messageContent) {
                    messageContent += `<div class="${textBubbleBg} ${textBubbleClass} rounded-lg p-2 shadow-sm mt-2" style="${textBubbleStyle}"><p class="text-xs">${escapeHtml(msg.message)}</p></div>`;
                } else {
                    messageContent = `<div class="${textBubbleBg} ${textBubbleClass} rounded-lg p-2 shadow-sm" style="${textBubbleStyle}"><p class="text-xs">${escapeHtml(msg.message)}</p></div>`;
                }
            }

            const timeAgo = getTimeAgo(msg.created_at);
            
            // Get theme for reaction colors
            const messagesAreaForReactions = chatElement.querySelector('.chat-messages');
            const currentThemeIdForReactions = messagesAreaForReactions?.getAttribute('data-theme-id');
            const currentThemeGroupIdForReactions = messagesAreaForReactions?.getAttribute('data-theme-group-id');
            const currentThemeUserIdForReactions = messagesAreaForReactions?.getAttribute('data-theme-user-id');
            const groupIdForReactions = isGroupChat ? userId.replace('group_', '') : null;
            
            let themeForReactions = null;
            if (isGroupChat && currentThemeIdForReactions && currentThemeGroupIdForReactions === groupIdForReactions && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                themeForReactions = window.popupAvailableThemes.find(t => t.id === currentThemeIdForReactions);
            } else if (!isGroupChat && currentThemeIdForReactions && currentThemeUserIdForReactions === userId && popupSingleChatAvailableThemes && popupSingleChatAvailableThemes.length > 0) {
                themeForReactions = popupSingleChatAvailableThemes.find(t => t.id === currentThemeIdForReactions);
            }
            
            // Get reactions display with theme colors
            const reactions = msg.reactions || [];
            let reactionsDisplay = '';
            if (reactions.length > 0) {
                const reactionEmojis = {
                    'like': '👍',
                    'love': '❤️',
                    'haha': '😂',
                    'wow': '😮',
                    'sad': '😢',
                    'angry': '😠'
                };
                const reactionCounts = reactions.map(r => `${reactionEmojis[r.type] || '👍'} ${r.count}`).join(' ');
                // Use theme colors: sender_text for sent messages, receiver_text for received messages
                const reactionColor = themeForReactions 
                    ? (isSender ? themeForReactions.sender_text : themeForReactions.receiver_text)
                    : '#4b5563';
                reactionsDisplay = `<div class="flex justify-end mt-1"><div class="message-reactions flex items-center gap-1 text-xs cursor-pointer hover:opacity-80 transition" data-message-id="${msg.id}" title="View reactions" style="color: ${reactionColor} !important;">${reactionCounts}</div></div>`;
            }
            
            // Check if this is a reply and show reply indicator
            let replyIndicator = '';
            if (msg.parent_id) {
                let parentMessageText = 'Message';
                let parentSenderName = 'User';
                let isReplyingToSelf = false;
                
                // Use parent message data from API if available
                if (msg.parent_message) {
                    parentMessageText = msg.parent_message.message || 'Message';
                    parentSenderName = msg.parent_message.sender_name || 'User';
                    // Check if replying to own message
                    const currentUserId = @json(Auth::id());
                    const parentSenderId = msg.parent_message.sender_id;
                    // Compare as strings to handle UUID comparison
                    isReplyingToSelf = parentSenderId && String(parentSenderId) === String(currentUserId) && isSender;
                } else {
                    // Fallback: Find the parent message in DOM
                    const parentMessage = chatElement.querySelector(`[data-message-id="${msg.parent_id}"]`);
                    if (parentMessage) {
                        const parentBubble = parentMessage.querySelector('.bg-gradient-to-r, .bg-white');
                        if (parentBubble) {
                            parentMessageText = parentBubble.textContent.trim() || 'Message';
                            parentMessageText = parentMessageText.replace(/\s+/g, ' ').trim();
                        }
                        
                        const parentAvatar = parentMessage.querySelector('img[alt], .bg-gradient-to-br');
                        const isParentSender = parentMessage.classList.contains('justify-end');
                        
                        if (isParentSender && isSender) {
                            // Both parent and current message are from the same sender (current user)
                            isReplyingToSelf = true;
                        } else if (parentAvatar && parentAvatar.alt && parentAvatar.alt !== 'You') {
                            parentSenderName = parentAvatar.alt;
                        } else if (isParentSender) {
                            parentSenderName = 'You';
                        } else {
                            const user = userData[userId] || {};
                            parentSenderName = user.name || 'User';
                        }
                    }
                }
                
                const replyLabel = isReplyingToSelf ? 'You replied to yourself' : `${escapeHtml(parentSenderName)}: ${escapeHtml(parentMessageText.substring(0, 40))}${parentMessageText.length > 40 ? '...' : ''}`;
                
                replyIndicator = `
                    <div class="mb-1 flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-gray-600 transition-colors reply-to-message" data-parent-id="${msg.parent_id}" title="Click to view original message">
                        <svg class="w-3 h-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <span class="truncate">${replyLabel}</span>
                    </div>
                `;
            }

            if (isSender) {
                const senderAvatar = currentUserProfilePicture 
                    ? `<img src="${currentUserProfilePicture}" alt="You" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 flex-shrink-0">`
                    : `<div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">${currentUserInitials}</div>`;
                
                // Check if message contains a video
                const hasVideo = messageContent && messageContent.includes('<video');
                
                // Wrap messageContent in a container if it contains attachments
                const contentWrapper = messageContent ? `<div class="space-y-2">${messageContent}</div>` : '';
                
                // React/Reply/Delete buttons HTML
                const actionButtons = `
                    <button class="message-react-btn p-0.5 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" 
                            title="React">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button class="message-reply-btn p-0.5 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" 
                            title="Reply">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </button>
                    <button class="message-delete-btn p-0.5 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" 
                            title="Delete">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                
                const lastSentMessageId = lastSentMessageIds.get(userId) || null;
                const seenIndicator = (isSender && msg.is_read && msg.id == lastSentMessageId) 
                    ? '<div class="flex justify-end mt-1 message-seen-indicator"><span class="text-xs text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Seen</span></div>'
                    : '';
                
                if (hasVideo) {
                    // For videos, place buttons below the video
                messageDiv.innerHTML = `
                        <div class="flex-1 flex justify-end items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                            ${contentWrapper || messageContent}
                                <div class="flex items-center justify-end gap-0.5 mt-2">
                                    ${actionButtons}
                                </div>
                                ${reactionsDisplay}
                                ${seenIndicator}
                        </div>
                    </div>
                    ${senderAvatar}
                `;
                } else {
                    // For non-videos, always show buttons on the side
                    messageDiv.innerHTML = `
                        <div class="flex-1 flex justify-end items-start gap-2 group">
                            <div class="flex items-center gap-0.5">
                                ${actionButtons}
                            </div>
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${contentWrapper || messageContent}
                                ${reactionsDisplay}
                                ${seenIndicator}
                            </div>
                        </div>
                        ${senderAvatar}
                    `;
                }
            } else {
                const user = userData[userId] || { initials: senderInitials, color: 'from-purple-400 to-purple-600', profilePicture: null };
                // Use sender's profile picture from message data if available
                const senderProfilePicture = msg.sender?.profile_picture_url || user.profilePicture || null;
                const senderIsOnline = msg.sender?.is_online || false;
                
                // Create avatar with online indicator
                const onlineIndicatorColor = senderIsOnline ? '#3fbb46' : '#9ca3af';
                const onlineIndicator = `<div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white" style="background-color: ${onlineIndicatorColor};"></div>`;
                
                const receiverAvatar = senderProfilePicture
                    ? `<div class="relative flex-shrink-0">${onlineIndicator}<img src="${senderProfilePicture}" alt="${senderName}" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200"></div>`
                    : `<div class="relative flex-shrink-0">${onlineIndicator}<div class="w-6 h-6 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs">${user.initials || senderInitials}</div></div>`;
                
                // Get theme for received messages
                const messagesAreaForTheme = chatElement.querySelector('.chat-messages');
                const currentThemeIdForName = messagesAreaForTheme?.getAttribute('data-theme-id');
                const currentThemeGroupIdForName = messagesAreaForTheme?.getAttribute('data-theme-group-id');
                const groupIdForName = isGroupChat ? userId.replace('group_', '') : null;
                
                let themeForName = null;
                if (currentThemeIdForName && currentThemeGroupIdForName === groupIdForName && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                    themeForName = window.popupAvailableThemes.find(t => t.id === currentThemeIdForName);
                }
                
                // For group messages, show sender first name only with theme color
                let senderNameDisplay = '';
                if (isGroupChat && !isSender && msg.sender) {
                    const firstName = msg.sender.first_name || senderName.split(' ')[0];
                    const senderNameColor = themeForName ? themeForName.receiver_text : '#374151';
                    senderNameDisplay = `<p class="text-xs font-semibold mb-1" style="color: ${senderNameColor} !important;">${escapeHtml(firstName)}</p>`;
                }
                
                let receivedMessageContent = messageContent;
                if (receivedMessageContent) {
                    // Replace sent message styling with received message styling - always white background
                    receivedMessageContent = receivedMessageContent.replace(/bg-gradient-to-r from-blue-500 to-purple-600 text-white/g, 'bg-white text-gray-800');
                    // Ensure voice messages have white background for received messages
                    receivedMessageContent = receivedMessageContent.replace(/bg-gradient-to-r from-pink-500 to-pink-600/g, 'bg-white');
                    // Wrap in container for proper spacing
                    receivedMessageContent = `<div class="space-y-2">${receivedMessageContent}</div>`;
                } else if (msg.message && msg.message.trim()) {
                    receivedMessageContent = `<div class="bg-white rounded-lg p-2 shadow-sm"><p class="text-xs text-gray-800">${escapeHtml(msg.message)}</p></div>`;
                }
                
                // Check if message contains a video
                const hasVideo = messageContent && messageContent.includes('<video');
                
                // React/Reply buttons HTML
                const actionButtons = `
                    <button class="message-react-btn p-0.5 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" 
                            title="React">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button class="message-reply-btn p-0.5 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" 
                            data-message-id="${msg.id}" 
                            title="Reply">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </button>
                `;
                
                if (hasVideo) {
                    // For videos, place buttons below the video
                messageDiv.innerHTML = `
                    ${receiverAvatar}
                        <div class="flex-1 flex items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${senderNameDisplay}
                        ${receivedMessageContent || ''}
                                <div class="flex items-center gap-0.5 mt-2">
                                    ${actionButtons}
                                </div>
                                ${reactionsDisplay}
                            </div>
                        </div>
                    `;
                } else {
                    // For non-videos, always show buttons on the side
                    messageDiv.innerHTML = `
                        ${receiverAvatar}
                        <div class="flex-1 flex items-start gap-2 group">
                            <div class="max-w-[75%] relative">
                                ${replyIndicator}
                                ${senderNameDisplay}
                                ${receivedMessageContent || ''}
                                ${reactionsDisplay}
                            </div>
                            <div class="flex items-center gap-0.5">
                                ${actionButtons}
                            </div>
                    </div>
                `;
                }
            }

            messagesArea.appendChild(messageDiv);
            
            // Wire up voice message play/pause buttons with progress tracking
            messageDiv.querySelectorAll('.voice-play-toggle').forEach(playBtn => {
                const voiceId = playBtn.getAttribute('data-voice-id');
                if (!voiceId) {
                    console.warn('Voice play button missing data-voice-id');
                    return;
                }
                
                const audioEl = document.getElementById(voiceId);
                if (!audioEl) {
                    console.warn('Audio element not found for voice ID:', voiceId);
                    return;
                }
                
                // Verify audio source and ensure it's set correctly
                const sourceEl = audioEl.querySelector('source');
                if (sourceEl) {
                    const audioUrl = sourceEl.getAttribute('src');
                    
                    // Ensure the audio element has the correct src
                    if (audioUrl && !audioEl.src) {
                        audioEl.src = audioUrl;
                    }
                }
                
                // Ensure audio element is properly set up and loaded
                if (audioEl.readyState === 0) {
                    audioEl.load();
                }
                
                // Force reload if src is set but not loading (for newly sent messages)
                setTimeout(() => {
                    if (audioEl.readyState === 0 && sourceEl && sourceEl.getAttribute('src')) {
                        audioEl.load();
                    }
                }, 500);
                
                // Add error handling
                audioEl.addEventListener('error', function(e) {
                    console.error('Audio playback error:', e);
                    const sourceEl = audioEl.querySelector('source');
                    const audioUrl = sourceEl ? sourceEl.getAttribute('src') : 'No source element';
                    console.error('Audio source URL:', audioUrl);
                    console.error('Audio error code:', audioEl.error?.code);
                    console.error('Audio error message:', audioEl.error?.message);
                    
                    // Try to get more details about the error
                    let errorMessage = 'Failed to load audio.';
                    if (audioEl.error) {
                        switch(audioEl.error.code) {
                            case 1: // MEDIA_ERR_ABORTED
                                errorMessage = 'Audio loading was aborted.';
                                break;
                            case 2: // MEDIA_ERR_NETWORK
                                errorMessage = 'Network error while loading audio.';
                                break;
                            case 3: // MEDIA_ERR_DECODE
                                errorMessage = 'Audio decoding error.';
                                break;
                            case 4: // MEDIA_ERR_SRC_NOT_SUPPORTED
                                errorMessage = 'Audio format not supported.';
                                break;
                        }
                    }
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Audio Error',
                            html: `${errorMessage}<br><small>URL: ${audioUrl}</small>`,
                            timer: 5000,
                            showConfirmButton: true
                        });
                    }
                });
                
                // Add canplay event to verify audio can play
                audioEl.addEventListener('canplay', function() {
                    console.log('Audio can play:', voiceId);
                });
                
                if (audioEl) {
                    const playIcon = playBtn.querySelector('.play-icon');
                    const pauseIcon = playBtn.querySelector('.pause-icon');
                    const container = playBtn.closest('.voice-message-container');
                    const waveformBars = container?.querySelectorAll('.waveform-bar');
                    const durationEl = container?.querySelector('.voice-duration');
                    const speedBtn = container?.querySelector('.voice-speed-toggle');
                    
                    // Initialize playback speed
                    let playbackSpeed = 1;
                    if (speedBtn) {
                        speedBtn.setAttribute('data-speed', '1');
                    }
                    
                    // Update waveform progress
                    const updateWaveformProgress = () => {
                        if (!audioEl.duration || !waveformBars) return;
                        const progress = audioEl.currentTime / audioEl.duration;
                        const barsToHighlight = Math.floor(progress * waveformBars.length);
                        
                        waveformBars.forEach((bar, index) => {
                            if (index < barsToHighlight) {
                                bar.style.opacity = '1';
                            } else {
                                const originalOpacity = bar.getAttribute('data-original-opacity') || '0.8';
                                bar.style.opacity = originalOpacity;
                            }
                        });
                    };
                    
                    // Update duration display
                    const updateDuration = () => {
                        if (!durationEl || !audioEl.duration) return;
                        const total = Math.floor(audioEl.duration);
                        const totalMins = Math.floor(total / 60);
                        const totalSecs = total % 60;
                        const totalDuration = `${totalMins}:${totalSecs.toString().padStart(2, '0')}`;
                        
                        if (!audioEl.paused && audioEl.currentTime > 0) {
                            // Show current time / total time when playing
                            const current = Math.floor(audioEl.currentTime);
                            const currentMins = Math.floor(current / 60);
                            const currentSecs = current % 60;
                            durationEl.textContent = `${currentMins}:${currentSecs.toString().padStart(2, '0')} / ${totalDuration}`;
                        } else if (audioEl.currentTime === 0) {
                            // Show just total duration when not playing
                            durationEl.textContent = totalDuration;
                        } else {
                            // Show current / total when paused
                            const current = Math.floor(audioEl.currentTime);
                            const currentMins = Math.floor(current / 60);
                            const currentSecs = current % 60;
                            durationEl.textContent = `${currentMins}:${currentSecs.toString().padStart(2, '0')} / ${totalDuration}`;
                        }
                    };
                    
                    // Store original opacity values
                    if (waveformBars) {
                        waveformBars.forEach(bar => {
                            const computed = window.getComputedStyle(bar);
                            bar.setAttribute('data-original-opacity', computed.opacity);
                        });
                    }
                    
                    // Load metadata and update total duration
                    audioEl.addEventListener('loadedmetadata', function() {
                        if (audioEl.duration && !isNaN(audioEl.duration)) {
                            const minutes = Math.floor(audioEl.duration / 60);
                            const seconds = Math.floor(audioEl.duration % 60);
                            const totalDuration = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                            if (durationEl && !audioEl.currentTime) {
                                durationEl.textContent = totalDuration;
                            }
                        }
                    });
                    
                    // Play/pause button handler
                    playBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Stop all other audio
                        document.querySelectorAll('.voice-audio').forEach(audio => {
                            if (audio !== audioEl && !audio.paused) {
                                audio.pause();
                                audio.currentTime = 0;
                                const otherBtn = document.querySelector(`[data-voice-id="${audio.id}"]`);
                                if (otherBtn) {
                                    const otherPlayIcon = otherBtn.querySelector('.play-icon');
                                    const otherPauseIcon = otherBtn.querySelector('.pause-icon');
                                    if (otherPlayIcon) otherPlayIcon.classList.remove('hidden');
                                    if (otherPauseIcon) otherPauseIcon.classList.add('hidden');
                                }
                                // Reset other waveforms
                                const otherContainer = audio.closest('.voice-message-container');
                                if (otherContainer) {
                                    const otherBars = otherContainer.querySelectorAll('.waveform-bar');
                                    otherBars.forEach(bar => {
                                        const originalOpacity = bar.getAttribute('data-original-opacity') || '0.8';
                                        bar.style.opacity = originalOpacity;
                                    });
                                }
                            }
                        });
                        
                        if (audioEl.paused) {
                            audioEl.play().catch(err => console.error('Error playing audio:', err));
                            if (playIcon) playIcon.classList.add('hidden');
                            if (pauseIcon) pauseIcon.classList.remove('hidden');
                        } else {
                            audioEl.pause();
                            if (playIcon) playIcon.classList.remove('hidden');
                            if (pauseIcon) pauseIcon.classList.add('hidden');
                        }
                    });
                    
                    // Progress tracking
                    audioEl.addEventListener('timeupdate', function() {
                        updateWaveformProgress();
                        updateDuration();
                    });
                    
                    // When audio ends
                    audioEl.addEventListener('ended', function() {
                        if (playIcon) playIcon.classList.remove('hidden');
                        if (pauseIcon) pauseIcon.classList.add('hidden');
                        audioEl.currentTime = 0;
                        updateWaveformProgress();
                        if (durationEl && audioEl.duration) {
                            const totalMins = Math.floor(audioEl.duration / 60);
                            const totalSecs = Math.floor(audioEl.duration % 60);
                            durationEl.textContent = `${totalMins}:${totalSecs.toString().padStart(2, '0')}`;
                        }
                    });
                    
                    // Playback speed control
                    if (speedBtn) {
                        speedBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const speeds = [1, 1.5, 2];
                            const currentSpeed = parseFloat(this.getAttribute('data-speed')) || 1;
                            const currentIndex = speeds.indexOf(currentSpeed);
                            const nextIndex = (currentIndex + 1) % speeds.length;
                            const nextSpeed = speeds[nextIndex];
                            
                            audioEl.playbackRate = nextSpeed;
                            this.setAttribute('data-speed', nextSpeed.toString());
                            this.textContent = `${nextSpeed}x`;
                        });
                    }
                }
            });
            
            // Wire up reply indicator click to scroll to parent message
            const replyIndicatorEl = messageDiv.querySelector('.reply-to-message');
            if (replyIndicatorEl && msg.parent_id) {
                replyIndicatorEl.addEventListener('click', function(e) {
                    e.stopPropagation();
                    scrollToParentMessage(msg.parent_id, chatElement);
                });
            }
            
            // Wire up react, reply, and delete buttons
            const reactBtn = messageDiv.querySelector('.message-react-btn');
            const replyBtn = messageDiv.querySelector('.message-reply-btn');
            const deleteBtn = messageDiv.querySelector('.message-delete-btn');
            
            if (reactBtn) {
                reactBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageReact(msg.id, chatElement);
                });
            }
            
            if (replyBtn) {
                replyBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageReply(msg.id, chatElement, userId);
                });
            }
            
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    handleMessageDelete(msg.id, messageDiv);
                });
            }
            
            // Wire up reactions click to show modal
            const reactionsDiv = messageDiv.querySelector('.message-reactions');
            if (reactionsDiv) {
                reactionsDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showReactionsModal(msg.id, chatElement, msg.reactions || []);
                });
            }
        }

        // Scroll to parent message when reply indicator is clicked
        function scrollToParentMessage(parentId, chatElement) {
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;
            
            const parentMessage = messagesArea.querySelector(`[data-message-id="${parentId}"]`);
            if (!parentMessage) {
                // Parent message not loaded yet, show a message
                if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Message not found',
                        text: 'The original message may not be loaded yet.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                return;
            }
            
            // Scroll to parent message
            parentMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight the parent message briefly
            parentMessage.style.transition = 'background-color 0.3s ease';
            parentMessage.style.backgroundColor = 'rgba(59, 130, 246, 0.2)';
            
            setTimeout(() => {
                parentMessage.style.backgroundColor = '';
                setTimeout(() => {
                    parentMessage.style.transition = '';
                }, 300);
            }, 2000);
        }
        
        // Handle message react
        function handleMessageReact(messageId, chatElement) {
            // Find the react button that was clicked
            const reactBtn = chatElement.querySelector(`.message-react-btn[data-message-id="${messageId}"]`);
            if (!reactBtn) return;
            
            // Remove any existing picker
            const existingPicker = document.querySelector('.reaction-picker-popup');
            if (existingPicker) {
                existingPicker.remove();
            }
            
            // Show reaction picker
            const reactionTypes = [
                { type: 'like', emoji: '👍', label: 'Like' },
                { type: 'love', emoji: '❤️', label: 'Love' },
                { type: 'haha', emoji: '😂', label: 'Haha' },
                { type: 'wow', emoji: '😮', label: 'Wow' },
                { type: 'sad', emoji: '😢', label: 'Sad' },
                { type: 'angry', emoji: '😠', label: 'Angry' }
            ];
            
            // Create reaction picker popup
            const picker = document.createElement('div');
            picker.className = 'reaction-picker-popup fixed z-[9999] bg-white rounded-xl shadow-2xl p-3 flex gap-2 border border-gray-200';
            picker.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';
            
            reactionTypes.forEach(reaction => {
                const btn = document.createElement('button');
                btn.className = 'w-12 h-12 flex items-center justify-center rounded-lg hover:scale-125 hover:bg-gray-100 transition-all duration-200 cursor-pointer';
                btn.style.fontSize = '1.75rem';
                btn.textContent = reaction.emoji;
                btn.title = reaction.label;
                btn.style.transition = 'transform 0.15s ease-out, background-color 0.15s ease-out';
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sendReaction(messageId, reaction.type, chatElement);
                    picker.remove();
                });
                picker.appendChild(btn);
            });
            
            // Append to body first to measure
            document.body.appendChild(picker);
            
            // Get button position relative to viewport
            const buttonRect = reactBtn.getBoundingClientRect();
            const pickerWidth = picker.offsetWidth;
            const pickerHeight = picker.offsetHeight;
            
            // Position picker above the button, centered horizontally (matching messages.blade.php)
            let top = buttonRect.top - pickerHeight - 8;
            let left = buttonRect.left + (buttonRect.width / 2) - (pickerWidth / 2);
            
            if (left < 8) left = 8;
            if (left + pickerWidth > window.innerWidth - 8) left = window.innerWidth - pickerWidth - 8;
            if (top < 8) top = buttonRect.bottom + 8;
            
            picker.style.top = `${top}px`;
            picker.style.left = `${left}px`;
            
            // Close picker on outside click
            setTimeout(() => {
                const closePicker = function(e) {
                    if (!picker.contains(e.target) && !reactBtn.contains(e.target)) {
                        picker.remove();
                        document.removeEventListener('click', closePicker);
                    }
                };
                document.addEventListener('click', closePicker);
            }, 100);
        }
        
        // Send reaction
        function sendReaction(messageId, reactionType, chatElement) {
            axios.post(`/messages/${messageId}/react`, {
                reaction_type: reactionType
            })
            .then(response => {
                if (response.data.success) {
                    // Update reactions display
                    updateMessageReactions(messageId, response.data.reactions, chatElement);
                }
            })
            .catch(error => {
                console.error('Error reacting to message:', error);
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                        title: 'Error',
                        text: 'Failed to react to message',
                        timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
        
        // Update message reactions display
        function updateMessageReactions(messageId, reactions, chatElement) {
            const messageDiv = chatElement.querySelector(`[data-message-id="${messageId}"]`);
            if (!messageDiv) {
                console.warn('Message div not found for messageId:', messageId);
                return;
            }
            
            // Determine if this is a sent or received message
            const isSender = messageDiv.classList.contains('justify-end');
            
            // Get theme for reaction colors
            const messagesArea = chatElement.querySelector('.chat-messages');
            const currentThemeId = messagesArea?.getAttribute('data-theme-id');
            const currentThemeGroupId = messagesArea?.getAttribute('data-theme-group-id');
            const userId = chatElement.getAttribute('data-user-id');
            const isGroupChat = userId && userId.startsWith('group_');
            const groupId = isGroupChat ? userId.replace('group_', '') : null;
            
            let theme = null;
            if (currentThemeId && currentThemeGroupId === groupId && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                theme = window.popupAvailableThemes.find(t => t.id === currentThemeId);
            }
            
            const reactionEmojis = {
                'like': '👍',
                'love': '❤️',
                'haha': '😂',
                'wow': '😮',
                'sad': '😢',
                'angry': '😠'
            };
            
            let reactionsDisplay = '';
            if (reactions && reactions.length > 0) {
                const reactionCounts = reactions.map(r => `${reactionEmojis[r.type] || '👍'} ${r.count}`).join(' ');
                // Use theme colors: sender_text for sent messages, receiver_text for received messages
                const reactionColor = theme 
                    ? (isSender ? theme.sender_text : theme.receiver_text)
                    : '#4b5563';
                reactionsDisplay = `<div class="flex justify-end mt-1"><div class="message-reactions flex items-center gap-1 text-xs cursor-pointer hover:opacity-80 transition" data-message-id="${messageId}" title="View reactions" style="color: ${reactionColor} !important;">${reactionCounts}</div></div>`;
            }
            
            // Find the message content container - try multiple selectors
            let messageContentContainer = messageDiv.querySelector('.max-w-\\[75\\%\\]');
            if (!messageContentContainer) {
                // Try alternative selector
                messageContentContainer = messageDiv.querySelector('[class*="max-w"]');
            }
            if (!messageContentContainer) {
                // Fallback to relative container
                messageContentContainer = messageDiv.querySelector('.relative');
            }
            if (!messageContentContainer) {
                console.warn('Message content container not found for messageId:', messageId);
                return;
            }
            
            // Find existing reactions wrapper
            const existingReactionsWrapper = messageContentContainer.querySelector('.flex.justify-end.mt-1');
            const timestamp = messageContentContainer.querySelector('.text-xs.text-gray-400, .text-xs.text-gray-500');
            
            if (existingReactionsWrapper) {
                if (reactionsDisplay) {
                    existingReactionsWrapper.outerHTML = reactionsDisplay;
                } else {
                    existingReactionsWrapper.remove();
                }
            } else if (reactionsDisplay) {
                if (timestamp) {
                    timestamp.insertAdjacentHTML('afterend', reactionsDisplay);
                } else {
                    messageContentContainer.insertAdjacentHTML('beforeend', reactionsDisplay);
                }
            }
            
            // Wire up click handler for the new reactions display
            const newReactionsDiv = messageContentContainer.querySelector('.message-reactions');
            if (newReactionsDiv) {
                const newReactionsDivClone = newReactionsDiv.cloneNode(true);
                newReactionsDiv.parentNode.replaceChild(newReactionsDivClone, newReactionsDiv);
                newReactionsDivClone.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showReactionsModal(messageId, chatElement, reactions || []);
                });
            }
        }
        
        // Handle message reply
        function handleMessageReply(messageId, chatElement, userId) {
            const messageInput = chatElement.querySelector('.chat-input');
            const messageForm = chatElement.querySelector('.chat-form');
            if (!messageInput || !messageForm) return;
            
            // Get the parent message
            const parentMessage = chatElement.querySelector(`[data-message-id="${messageId}"]`);
            if (!parentMessage) return;
            
            // Store parent message ID in the input
            messageInput.setAttribute('data-reply-to', messageId);
            
            // Show reply indicator - insert it before the form
            let replyIndicator = chatElement.querySelector('.reply-indicator');
            if (!replyIndicator) {
                replyIndicator = document.createElement('div');
                replyIndicator.className = 'reply-indicator px-3 py-2 border-b border-gray-200 bg-gray-50';
                messageForm.parentElement.insertBefore(replyIndicator, messageForm);
            }
            
            // Extract sender name and message text from parent message
            let senderName = 'User';
            let messageText = 'Message';
            
            // Try to get sender name from message data or DOM
            const parentMessageData = parentMessage.getAttribute('data-message-id');
            if (parentMessageData) {
                // Try to find sender name in the message bubble
                const parentBubble = parentMessage.querySelector('.bg-gradient-to-r, .bg-white');
                if (parentBubble) {
                    messageText = parentBubble.textContent.trim() || 'Message';
                    // Clean up message text (remove emojis and extra spaces for preview)
                    messageText = messageText.replace(/\s+/g, ' ').trim();
                }
                
                // Try to get sender from avatar or message structure
                const parentAvatar = parentMessage.querySelector('img[alt], .bg-gradient-to-br');
                if (parentAvatar && parentAvatar.alt && parentAvatar.alt !== 'You') {
                    senderName = parentAvatar.alt;
                } else {
                    // Check if it's sent by current user
                    const isParentSender = parentMessage.classList.contains('justify-end');
                    if (isParentSender) {
                        senderName = 'You';
                    } else {
                        // Try to get from userData
                        const user = userData[userId] || {};
                        senderName = user.name || 'User';
                    }
                }
            }
            
            replyIndicator.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-700 truncate">
                                <span class="text-gray-500">You replied to </span>
                                <span class="font-medium text-gray-800">${senderName}</span>
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">${escapeHtml(messageText.substring(0, 60))}${messageText.length > 60 ? '...' : ''}</p>
                        </div>
                    </div>
                    <button class="cancel-reply-btn ml-2 text-gray-500 hover:text-gray-700 flex-shrink-0" title="Cancel reply">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            // Focus input
            messageInput.focus();
            
            // Cancel reply handler
            const cancelBtn = replyIndicator.querySelector('.cancel-reply-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    messageInput.removeAttribute('data-reply-to');
                    replyIndicator.remove();
                });
            }
        }
        
        // Show reactions modal
        function showReactionsModal(messageId, chatElement, initialReactions) {
            // Remove any existing modal
            const existingModal = document.getElementById('reactionsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Create modal
            const modal = document.createElement('div');
            modal.id = 'reactionsModal';
            modal.className = 'fixed inset-0 z-[10000] flex items-center justify-center bg-opacity-20 backdrop-blur-sm';
            modal.innerHTML = `
                <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Message reactions</h3>
                        <button class="close-reactions-modal w-8 h-8 rounded-full bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="flex items-center gap-1 px-4 pt-3 border-b border-gray-700 reactions-tabs">
                        <!-- Tabs will be populated here -->
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-4 reactions-content">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close modal handlers
            const closeBtn = modal.querySelector('.close-reactions-modal');
            closeBtn.addEventListener('click', () => modal.remove());
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            // Fetch detailed reactions
            axios.get(`/messages/${messageId}/reactions`)
                .then(response => {
                    if (response.data.success) {
                        renderReactionsModal(modal, response.data.reactions, messageId, chatElement);
                    }
                })
                .catch(error => {
                    console.error('Error fetching reactions:', error);
                    const content = modal.querySelector('.reactions-content');
                    content.innerHTML = '<div class="text-center text-gray-400 py-8">Failed to load reactions</div>';
                });
        }
        
        // Render reactions in modal
        function renderReactionsModal(modal, reactions, messageId, chatElement) {
            const reactionEmojis = {
                'like': '👍',
                'love': '❤️',
                'haha': '😂',
                'wow': '😮',
                'sad': '😢',
                'angry': '😠'
            };
            
            const tabsContainer = modal.querySelector('.reactions-tabs');
            const contentContainer = modal.querySelector('.reactions-content');
            
            // Calculate total count
            const totalCount = reactions.reduce((sum, r) => sum + r.count, 0);
            
            // Create tabs
            let tabsHTML = `
                <button class="reaction-tab px-4 py-2 text-sm font-medium text-white border-b-2 border-blue-500 transition" data-tab="all">
                    All ${totalCount}
                </button>
            `;
            
            reactions.forEach(reaction => {
                tabsHTML += `
                    <button class="reaction-tab px-4 py-2 text-sm font-medium text-gray-400 hover:text-white border-b-2 border-transparent transition" data-tab="${reaction.type}">
                        ${reactionEmojis[reaction.type] || '👍'} ${reaction.count}
                    </button>
                `;
            });
            
            tabsContainer.innerHTML = tabsHTML;
            
            // Render all reactions by default
            renderReactionsContent(contentContainer, reactions, 'all', messageId, chatElement, modal);
            
            // Tab click handlers
            tabsContainer.querySelectorAll('.reaction-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    tabsContainer.querySelectorAll('.reaction-tab').forEach(t => {
                        t.classList.remove('border-blue-500', 'text-white');
                        t.classList.add('border-transparent', 'text-gray-400');
                    });
                    this.classList.add('border-blue-500', 'text-white');
                    this.classList.remove('border-transparent', 'text-gray-400');
                    
                    // Render content for selected tab
                    const tabType = this.getAttribute('data-tab');
                    renderReactionsContent(contentContainer, reactions, tabType, messageId, chatElement, modal);
                });
            });
        }
        
        // Render reactions content
        function renderReactionsContent(container, reactions, filterType, messageId, chatElement, modal) {
            const reactionEmojis = {
                'like': '👍',
                'love': '❤️',
                'haha': '😂',
                'wow': '😮',
                'sad': '😢',
                'angry': '😠'
            };
            
            let usersToShow = [];
            
            if (filterType === 'all') {
                // Flatten all users from all reaction types
                reactions.forEach(reaction => {
                    reaction.users.forEach(user => {
                        if (!usersToShow.find(u => u.id === user.id)) {
                            usersToShow.push({
                                ...user,
                                reaction_type: reaction.type,
                                reaction_emoji: reactionEmojis[reaction.type] || '👍'
                            });
                        }
                    });
                });
            } else {
                // Show users for specific reaction type
                const reaction = reactions.find(r => r.type === filterType);
                if (reaction) {
                    usersToShow = reaction.users.map(user => ({
                        ...user,
                        reaction_type: reaction.type,
                        reaction_emoji: reactionEmojis[reaction.type] || '👍'
                    }));
                }
            }
            
            if (usersToShow.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-400 py-8">No reactions</div>';
                return;
            }
            
            let contentHTML = '<div class="space-y-2">';
            usersToShow.forEach(user => {
                contentHTML += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-700 rounded-lg transition cursor-pointer reaction-user-item" 
                         data-user-id="${user.id}" 
                         data-reaction-id="${user.reaction_id}"
                         data-is-current-user="${user.is_current_user}">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <img src="${user.profile_picture_url}" alt="${user.name}" 
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium truncate">${escapeHtml(user.name)}</p>
                                ${user.is_current_user ? '<p class="text-xs text-gray-400">Click to remove</p>' : ''}
                            </div>
                        </div>
                        <div class="text-2xl flex-shrink-0 ml-3">${user.reaction_emoji}</div>
                    </div>
                `;
            });
            contentHTML += '</div>';
            
            container.innerHTML = contentHTML;
            
            // Add click handlers for removing reactions (only for current user)
            container.querySelectorAll('.reaction-user-item[data-is-current-user="true"]').forEach(item => {
                item.addEventListener('click', function() {
                    const reactionId = this.getAttribute('data-reaction-id');
                    removeReaction(messageId, reactionId, chatElement, modal);
                });
            });
        }
        
        // Remove reaction
        function removeReaction(messageId, reactionId, chatElement, modal) {
            // Find the reaction type from the clicked item
            const clickedItem = modal.querySelector(`[data-reaction-id="${reactionId}"]`);
            if (!clickedItem) return;
            
            // Get reaction type from emoji or data attribute
            const reactionEmoji = clickedItem.querySelector('.text-2xl').textContent.trim();
            const reactionTypes = {
                '👍': 'like',
                '❤️': 'love',
                '😂': 'haha',
                '😮': 'wow',
                '😢': 'sad',
                '😠': 'angry'
            };
            const reactionType = reactionTypes[reactionEmoji] || 'like';
            
            // Send request to remove reaction
            axios.post(`/messages/${messageId}/react`, {
                reaction_type: reactionType
            })
            .then(response => {
                if (response.data.success) {
                    // Update reactions display
                    updateMessageReactions(messageId, response.data.reactions, chatElement);
                    
                    // Refresh modal content
                    axios.get(`/messages/${messageId}/reactions`)
                        .then(res => {
                            if (res.data.success) {
                                renderReactionsModal(modal, res.data.reactions, messageId, chatElement);
                            }
                        });
                }
            })
            .catch(error => {
                console.error('Error removing reaction:', error);
            });
        }

        // Handle message delete
        function handleMessageDelete(messageId, messageDiv) {
            if (!window.Swal) {
                if (confirm('Are you sure you want to delete this message?')) {
                    deleteMessage(messageId, messageDiv);
                }
                return;
            }
            
            Swal.fire({
                title: 'Delete Message?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteMessage(messageId, messageDiv);
                }
            });
        }
        
        // Delete message
        function deleteMessage(messageId, messageDiv) {
            axios.delete(`/messages/${messageId}`)
            .then(response => {
                if (response.data.success) {
                    // Fade out and remove
                    messageDiv.style.transition = 'opacity 0.3s';
                    messageDiv.style.opacity = '0';
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 300);
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Message deleted successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error deleting message:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete message',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper function to get time ago
        function getTimeAgo(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diffMs = now - time;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return time.toLocaleDateString();
        }

        // Helper function to get file icon for attachments
        function getFileIconForAttachment(ext) {
            if (['pdf'].includes(ext)) {
                return `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['doc', 'docx'].includes(ext)) {
                return `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['xls', 'xlsx'].includes(ext)) {
                return `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>`;
            } else if (['zip', 'rar', '7z'].includes(ext)) {
                return `<svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14,17H7V15H14M17,13H7V11H17M17,9H7V7H17M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z"/></svg>`;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
                return `<svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
            } else {
                return `<svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
            }
        }

        // Mark messages as seen when message input is focused/clicked in popup
        function markMessagesAsReadOnInputFocus(userId) {
            if (!userId) return;
            
            // Mark messages as read
            markMessagesAsRead(userId);
        }
        
        function markMessagesAsRead(userId) {
            fetch(`{{ route('messages.mark-as-read', ':userId') }}`.replace(':userId', userId), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload messages dropdown to update unread counts (user side)
                    if (typeof loadMessagesDropdown === 'function') {
                        const messagesList = document.getElementById('messagesDropdownList');
                        if (messagesList) messagesList.dataset.loaded = 'false';
                        loadMessagesDropdown();
                    }
                    // Reload admin messages dropdown
                    if (typeof loadAdminMessagesDropdown === 'function') {
                        $('#adminMessagesDropdownList').data('loaded', false);
                        loadAdminMessagesDropdown();
                    }
                    // Reload unread count (user side)
                    if (typeof loadUnreadCount === 'function') {
                        loadUnreadCount();
                    }
                    // Reload admin unread count
                    if (typeof loadAdminUnreadCount === 'function') {
                        loadAdminUnreadCount();
                    }
                }
            })
            .catch(error => console.error('Error marking messages as read:', error));
        }

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
                    // Update unread badge when minimizing
                    updateMinimizedUnreadBadge(chatElement, userId);
                    // Save state to localStorage
                    saveActiveChats();
                });
            }

            // Close
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    stopMessagePolling(userId);
                    chatElement.remove();
                    activeChats.delete(userId);
                    updateChatZIndex();
                    updateContainerLayout();
                    // Save to localStorage
                    saveActiveChats();
                });
            }

            // Restore from minimized
            const minimizedWrapper = chatElement.querySelector('.chat-minimized-wrapper');
            if (minimizedBtn && minimizedWrapper) {
                minimizedWrapper.addEventListener('click', function(e) {
                    // Don't restore if close button was clicked
                    if (e.target.closest('.chat-minimized-close')) {
                        return;
                    }
                    chatElement.setAttribute('data-expanded', 'true');
                    chatElement.querySelector('.chat-expanded').classList.remove('hidden');
                    chatElement.querySelector('.chat-minimized').classList.add('hidden');
                    // Bring to front
                    container.insertBefore(chatElement, container.firstChild);
                    updateChatZIndex();
                    updateContainerLayout();
                    
                    // Scroll to bottom when expanding from minimized
                    const messagesArea = chatElement.querySelector('.chat-messages');
                    if (messagesArea) {
                        setTimeout(() => {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                        }, 100);
                    }
                    // Mark messages as read when chat is restored/expanded
                    markMessagesAsRead(userId);
                    // Save to localStorage
                    saveActiveChats();
                    // Load conversation if not already loaded or if showing placeholder/loading
                    if (messagesArea) {
                        const hasRealMessages = messagesArea.querySelector('[data-message-id]');
                        const isPlaceholder = messagesArea.innerHTML.includes('Loading messages') || 
                                             messagesArea.innerHTML.includes('Hi! Can we discuss') ||
                                             messagesArea.innerHTML.includes('Sure! I\'ve prepared') ||
                                             messagesArea.innerHTML.includes('That would be great');
                        
                        if (!hasRealMessages || isPlaceholder) {
                            const user = userData[userId] || { name: 'User', initials: 'U', color: 'from-purple-400 to-purple-600', status: 'Offline', profilePicture: null, privilege: null, position: null };
                            loadConversation(chatElement, userId, user);
                        }
                    }
                });
                
                // Show/hide close button on hover
                minimizedWrapper.addEventListener('mouseenter', function() {
                    const closeBtn = minimizedWrapper.querySelector('.chat-minimized-close');
                    const unreadBadge = minimizedBtn.querySelector('.chat-unread-badge');
                    if (closeBtn) closeBtn.style.display = 'flex';
                    if (unreadBadge) unreadBadge.style.display = 'none';
                });
                
                minimizedWrapper.addEventListener('mouseleave', function() {
                    const closeBtn = minimizedWrapper.querySelector('.chat-minimized-close');
                    const unreadBadge = minimizedBtn.querySelector('.chat-unread-badge');
                    if (closeBtn) closeBtn.style.display = 'none';
                    const unreadCount = parseInt(unreadBadge?.querySelector('span')?.textContent || '0');
                    if (unreadBadge && unreadCount > 0) unreadBadge.style.display = 'flex';
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
                                    sendVoiceMessage();
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
                                sendVoiceMessage();
                            }
                        });
                    }
                    
                    // Send voice message to server
                    function sendVoiceMessage() {
                        if (!voiceRecordingBlob || !voiceRecordingUrl) return;
                        
                        // Disable send button
                        if (voiceSendBtn) {
                            voiceSendBtn.disabled = true;
                            voiceSendBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
                        }
                        
                        // Get duration
                        const duration = formatVoiceTime(voiceSeconds || Math.max(1, Math.round(voiceRecordingBlob.size / 16000)));
                        
                        // Create voice file
                        const fileName = `voice-message-${Date.now()}-${duration.replace(':', '-')}.webm`;
                        const voiceFile = new File([voiceRecordingBlob], fileName, { type: 'audio/webm' });
                        
                        // Append temporary "sending" message with voice clip preview
                        const tempId = appendSendingMessage(chatElement, '', [voiceFile], null);
                        
                        // Create FormData for voice message
                        const formData = new FormData();
                        
                        // Check if this is a group chat
                        const isGroup = userId && userId.startsWith('group_');
                        if (isGroup) {
                            const groupId = userId.replace('group_', '').trim();
                            if (!groupId) {
                                console.error('Invalid group ID for voice message');
                                resetVoiceState();
                                if (tempId) removeTempMessage(chatElement, tempId);
                                return;
                            }
                            formData.append('group_id', groupId);
                        } else {
                            const receiverId = userId?.trim();
                            if (!receiverId) {
                                console.error('Invalid receiver ID for voice message');
                                resetVoiceState();
                                if (tempId) removeTempMessage(chatElement, tempId);
                                return;
                            }
                            formData.append('receiver_id', receiverId);
                        }
                        
                        formData.append('voice_duration', duration);
                        formData.append('attachments[]', voiceFile);
                        
                        // Send voice message to server
                        fetch('{{ route("messages.send") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.message) {
                                // Remove temp message - the real message will appear via polling
                                if (tempId) {
                                    removeTempMessage(chatElement, tempId);
                                }
                                
                                // Update last sent message ID for "Seen" indicator
                                if (data.message.is_sender) {
                                    lastSentMessageIds.set(userId, data.message.id);
                                }
                                
                                // Voice message sent successfully, it will appear via polling
                                // Reset voice state
                                resetVoiceState();
                                voiceRecorder.classList.add('hidden');
                                if (messageForm) {
                                    messageForm.classList.remove('hidden');
                                }
                            } else {
                                // Update temp message to show error
                                if (tempId) {
                                    updateTempMessageError(chatElement, tempId, data.message || 'Unknown error');
                                } else {
                                    alert('Failed to send voice message: ' + (data.message || 'Unknown error'));
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error sending voice message:', error);
                            // Update temp message to show error
                            if (tempId) {
                                updateTempMessageError(chatElement, tempId, 'Network error. Please try again.');
                            } else {
                                alert('Error sending voice message. Please try again.');
                            }
                        })
                        .finally(() => {
                            // Re-enable send button
                            if (voiceSendBtn) {
                                voiceSendBtn.disabled = false;
                                voiceSendBtn.innerHTML = `
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3.4 20.4L5 14 14 12 5 10 3.4 3.6 21 12 3.4 20.4Z"></path>
                                    </svg>
                                `;
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
                            ${currentUserProfilePicture 
                                ? `<img src="${currentUserProfilePicture}" alt="You" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 flex-shrink-0">`
                                : `<div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">${currentUserInitials}</div>`
                            }
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

            // Function to append a temporary "sending" message
            function appendSendingMessage(chatElement, messageText, attachments, parentId) {
                const messagesArea = chatElement.querySelector('.chat-messages');
                if (!messagesArea) return null;
                
                const tempId = 'temp-' + Date.now();
                const now = new Date().toISOString();
                
                // Clear empty state if it exists
                const emptyState = messagesArea.querySelector('.flex.flex-col.items-center');
                if (emptyState) {
                    messagesArea.innerHTML = '';
                }
                
                // Get last message for timestamp separator check
                const lastMessage = messagesArea.querySelector('[data-message-id]:last-of-type');
                const previousMsg = lastMessage ? {
                    created_at: lastMessage.getAttribute('data-created-at')
                } : null;
                
                // Check if we need to add a timestamp separator
                if (shouldShowTimestampSeparator && shouldShowTimestampSeparator({ created_at: now }, previousMsg)) {
                    const separator = createTimestampSeparator ? createTimestampSeparator(now) : null;
                    if (separator) {
                        separator.setAttribute('data-temp-separator', 'true');
                        separator.setAttribute('data-separator-timestamp', formatTimestampSeparator ? formatTimestampSeparator(now) : '');
                        separator.setAttribute('data-created-at', now);
                        messagesArea.appendChild(separator);
                    }
                }
                
                const senderAvatar = currentUserProfilePicture 
                    ? `<img src="${currentUserProfilePicture}" alt="You" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 flex-shrink-0">`
                    : `<div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">${currentUserInitials}</div>`;
                
                let messageContent = '';
                
                // Get current theme if available (for group chats)
                const userId = chatElement.getAttribute('data-user-id');
                const isGroupChat = userId && userId.startsWith('group_');
                const groupId = isGroupChat ? userId.replace('group_', '') : null;
                const currentThemeId = messagesArea?.getAttribute('data-theme-id');
                const currentThemeGroupId = messagesArea?.getAttribute('data-theme-group-id');
                
                let theme = null;
                if (currentThemeId && currentThemeGroupId === groupId && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                    theme = window.popupAvailableThemes.find(t => t.id === currentThemeId);
                }
                
                // Handle text message
                if (messageText) {
                    const textBubbleBg = theme ? '' : 'bg-gradient-to-r from-blue-500 to-purple-600';
                    const textBubbleStyle = theme 
                        ? `background: ${theme.sender_bubble}; color: ${theme.sender_text};` 
                        : '';
                    const textBubbleClass = theme ? '' : 'text-white';
                    messageContent = `<div class="${textBubbleBg} ${textBubbleClass} rounded-lg p-2 shadow-sm" style="${textBubbleStyle}"><p class="text-xs">${escapeHtml(messageText)}</p></div>`;
                }
                
                // Handle attachments
                if (attachments && attachments.length > 0) {
                    attachments.forEach((file, index) => {
                        if (file.type && file.type.startsWith('image/')) {
                            const imageUrl = URL.createObjectURL(file);
                            messageContent += `<div class="mt-2"><img src="${imageUrl}" alt="Attachment" class="max-w-[200px] max-h-[200px] rounded-lg object-cover"></div>`;
                        } else if (file.type && file.type.startsWith('video/')) {
                            const videoUrl = URL.createObjectURL(file);
                            messageContent += `<div class="mt-2"><video src="${videoUrl}" controls class="max-w-[200px] max-h-[200px] rounded-lg"></video></div>`;
                        } else if (file.type && (file.type.startsWith('audio/') || file.name && /\.(webm|mp3|wav|ogg|m4a|aac)$/i.test(file.name))) {
                            // Voice message - create a preview with play button
                            const audioUrl = URL.createObjectURL(file);
                            const duration = file.name.match(/(\d+)-(\d+)\./) ? `${file.name.match(/(\d+)-(\d+)\./)[1]}:${file.name.match(/(\d+)-(\d+)\./)[2].padStart(2, '0')}` : '0:00';
                            
                            // Use theme colors if available, otherwise use defaults
                            const voiceBubbleBg = theme ? '' : 'bg-[#FF1F70]';
                            const voiceBubbleStyle = theme 
                                ? `background: ${theme.sender_bubble};` 
                                : '';
                            const voiceButtonColor = theme 
                                ? `color: ${theme.sender_bubble};` 
                                : 'text-[#FF1F70]';
                            const durationTextColor = theme 
                                ? `color: ${theme.sender_text};` 
                                : 'text-white';
                            const speedButtonColor = theme 
                                ? `color: ${theme.sender_bubble};` 
                                : 'text-[#FF1F70]';
                            
                            messageContent += `
                                <div class="mt-2 rounded-2xl px-4 py-3 shadow-sm flex items-center gap-3 max-w-[280px] ${voiceBubbleBg} voice-message-container" style="min-width: 200px; ${voiceBubbleStyle}">
                                    <button type="button" class="voice-play-toggle flex items-center justify-center w-9 h-9 rounded-full bg-white ${voiceButtonColor} hover:opacity-80 transition shadow-sm flex-shrink-0" style="${voiceButtonColor}" aria-label="Play voice message">
                                        <svg class="w-4 h-4 play-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                        <svg class="w-4 h-4 pause-icon hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"></path></svg>
                                    </button>
                                    <div class="flex-1 flex items-center justify-center min-w-0 voice-waveform-container">
                                        <div class="flex items-end gap-[2px] voice-waveform" style="position: relative; width: 100%;">
                                            <div class="w-[3px] h-3 rounded-full bg-white opacity-80 waveform-bar"></div>
                                            <div class="w-[3px] h-5 rounded-full bg-white opacity-90 waveform-bar"></div>
                                            <div class="w-[3px] h-7 rounded-full bg-white opacity-95 waveform-bar"></div>
                                            <div class="w-[3px] h-4 rounded-full bg-white opacity-85 waveform-bar"></div>
                                            <div class="w-[3px] h-6 rounded-full bg-white opacity-90 waveform-bar"></div>
                                            <div class="w-[3px] h-3 rounded-full bg-white opacity-80 waveform-bar"></div>
                                        </div>
                                    </div>
                                    <div class="ml-2 flex flex-col items-end gap-1 flex-shrink-0">
                                        <div class="text-xs font-semibold whitespace-nowrap voice-duration" style="min-width: 50px; text-align: right; ${durationTextColor}">0:00 / ${duration}</div>
                                        <button type="button" class="voice-speed-toggle text-xs font-semibold bg-white hover:opacity-80 transition px-1.5 py-0.5 rounded" style="${speedButtonColor}" data-speed="1">1x</button>
                                    </div>
                                    <audio class="hidden voice-audio" preload="metadata">
                                        <source src="${audioUrl}" type="${file.type || 'audio/webm'}">
                                    </audio>
                                </div>
                            `;
                        } else {
                            messageContent += `<div class="mt-2 bg-gray-100 rounded-lg p-2"><p class="text-xs text-gray-700">📎 ${escapeHtml(file.name)}</p></div>`;
                        }
                    });
                }
                
                // Handle reply indicator
                let replyIndicator = '';
                if (parentId) {
                    const parentMessage = messagesArea.querySelector(`[data-message-id="${parentId}"]`);
                    if (parentMessage) {
                        const parentText = parentMessage.querySelector('.message-content')?.textContent || parentMessage.textContent?.trim() || 'Message';
                        replyIndicator = `<div class="mb-1 flex items-center gap-2 text-xs text-gray-400 cursor-pointer hover:text-gray-600 transition-colors reply-to-message" data-parent-id="${parentId}">
                            <svg class="w-3 h-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            <span class="truncate">You: ${escapeHtml(parentText.substring(0, 30))}${parentText.length > 30 ? '...' : ''}</span>
                        </div>`;
                    }
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start space-x-2 justify-end';
                messageDiv.setAttribute('data-message-id', tempId);
                messageDiv.setAttribute('data-temp-message', 'true');
                messageDiv.setAttribute('data-created-at', now);
                
                messageDiv.innerHTML = `
                    <div class="flex-1 flex justify-end items-start gap-2 group">
                        <div class="flex items-center gap-0.5" style="opacity: 1; visibility: visible;">
                            <button class="message-react-btn p-0.5 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="React" disabled>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                            <button class="message-reply-btn p-0.5 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="Reply" disabled>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </button>
                            <button class="message-delete-btn p-0.5 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-full transition" data-message-id="${tempId}" title="Delete" disabled>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="max-w-[75%] relative">
                            ${replyIndicator}
                            <div class="space-y-2">${messageContent}</div>
                            <div class="flex justify-end mt-1">
                                <div class="text-xs text-gray-400 italic">Sending...</div>
                            </div>
                        </div>
                    </div>
                    ${senderAvatar}
                `;
                
                messagesArea.appendChild(messageDiv);
                
                // Scroll to bottom
                setTimeout(() => {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }, 100);
                
                return tempId;
            }
            
            // Function to update temp message to show error
            function updateTempMessageError(chatElement, tempId, errorMessage) {
                const tempMessage = chatElement.querySelector(`[data-message-id="${tempId}"][data-temp-message="true"]`);
                if (!tempMessage) return;
                
                const sendingDiv = tempMessage.querySelector('.text-xs.text-gray-400, .text-xs.text-gray-500');
                if (sendingDiv) {
                    sendingDiv.className = 'text-xs text-red-500';
                    sendingDiv.textContent = `Failed: ${errorMessage}`;
                }
                
                // Make message semi-transparent to indicate error
                tempMessage.style.opacity = '0.6';
            }
            
            // Function to remove temp message and mark its associated separator if needed
            function removeTempMessage(chatElement, tempId) {
                const tempMessage = chatElement.querySelector(`[data-message-id="${tempId}"][data-temp-message="true"]`);
                if (tempMessage) {
                    // Check if there's a timestamp separator right before this temp message
                    const previousSibling = tempMessage.previousElementSibling;
                    if (previousSibling && previousSibling.classList.contains('flex') && 
                        previousSibling.classList.contains('items-center') && 
                        previousSibling.classList.contains('justify-center') &&
                        previousSibling.classList.contains('my-4')) {
                        // This is likely a timestamp separator - mark it for reuse check
                        const separatorTimestamp = previousSibling.querySelector('span')?.textContent || '';
                        const tempCreatedAt = tempMessage.getAttribute('data-created-at');
                        previousSibling.setAttribute('data-temp-separator', 'true');
                        previousSibling.setAttribute('data-separator-timestamp', separatorTimestamp);
                        if (tempCreatedAt) {
                            previousSibling.setAttribute('data-created-at', tempCreatedAt);
                        }
                    }
                    tempMessage.remove();
                }
            }

            // Handle message input focus/click - mark messages as seen
            const messageInput = chatElement.querySelector('.chat-input');
            if (messageInput) {
                messageInput.addEventListener('focus', function() {
                    markMessagesAsReadOnInputFocus(userId);
                });
                messageInput.addEventListener('click', function() {
                    markMessagesAsReadOnInputFocus(userId);
                });
            }

            // Handle message form submission
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('.chat-input');
                    const files = attachedFiles.length > 0 ? attachedFiles : [];
                    const messageText = input ? input.value.trim() : '';
                    
                    if (messageText || files.length > 0) {
                        // Store values before clearing
                        const textToSend = messageText;
                        const filesToSend = [...files];
                        const parentIdToSend = input ? input.getAttribute('data-reply-to') : null;
                        
                        // Clear input immediately
                        if (input) input.value = '';
                        attachedFiles = [];
                        if (fileInput) {
                            fileInput.value = '';
                        }
                        if (filePreview) {
                            filePreview.classList.add('hidden');
                            filePreview.innerHTML = '';
                        }
                        
                        // Clear reply indicator if exists
                        if (input) {
                            const replyIndicator = chatElement.querySelector('.reply-indicator');
                            if (replyIndicator) {
                                replyIndicator.remove();
                            }
                            input.removeAttribute('data-reply-to');
                        }
                        
                        // Append temporary "sending" message
                        const tempId = appendSendingMessage(chatElement, textToSend, filesToSend, parentIdToSend);

                        // Create FormData for file upload
                        const formData = new FormData();
                        
                        // Check if this is a group chat
                        const isGroup = userId && userId.startsWith('group_');
                        if (isGroup) {
                            const groupId = userId.replace('group_', '').trim();
                            if (!groupId) {
                                console.error('Invalid group ID');
                                if (tempId) updateTempMessageError(chatElement, tempId, 'Invalid group ID');
                                return;
                            }
                            formData.append('group_id', groupId);
                        } else {
                            const receiverId = userId?.trim();
                            if (!receiverId) {
                                console.error('Invalid receiver ID');
                                if (tempId) updateTempMessageError(chatElement, tempId, 'Invalid receiver ID');
                                return;
                            }
                            formData.append('receiver_id', receiverId);
                        }
                        
                        if (textToSend) {
                            formData.append('message', textToSend);
                        }
                        
                        if (parentIdToSend) {
                            formData.append('parent_id', parentIdToSend);
                        }
                        
                        filesToSend.forEach((file, index) => {
                            formData.append(`attachments[${index}]`, file);
                        });

                        // Send message to server
                        fetch('{{ route("messages.send") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.message) {
                                // Remove temp message
                                if (tempId) {
                                    removeTempMessage(chatElement, tempId);
                                }
                                
                                // Update last sent message ID for "Seen" indicator
                                if (data.message.is_sender) {
                                    lastSentMessageIds.set(userId, data.message.id);
                                }
                                
                                // Append the actual message from server response
                                const messagesArea = chatElement.querySelector('.chat-messages');
                                if (messagesArea && data.message) {
                                    // Get last message for timestamp separator check (excluding temp messages)
                                    const allMessages = Array.from(messagesArea.querySelectorAll('[data-message-id]'));
                                    const realMessages = allMessages.filter(msg => !msg.getAttribute('data-temp-message'));
                                    const lastMessage = realMessages.length > 0 ? realMessages[realMessages.length - 1] : null;
                                    const previousMsg = lastMessage ? {
                                        created_at: lastMessage.getAttribute('data-created-at')
                                    } : null;
                                    
                                    // appendMessage will handle temp separator logic internally
                                    appendMessage(chatElement, data.message, userId, previousMsg);
                                    
                                    // Scroll to bottom
                                    setTimeout(() => {
                                        messagesArea.scrollTop = messagesArea.scrollHeight;
                                    }, 100);
                                }
                                
                                // Update header dropdown conversation list when message is sent
                                if (data.message && typeof window.updateDropdownItemBadge === 'function') {
                                    const messageText = data.message.message || '';
                                    // Get unread count (should be 0 for sent messages)
                                    const unreadCount = 0;
                                    // Get total unread count from header badge
                                    const headerBadge = document.getElementById('messagesBadgeCount') || document.getElementById('adminMessagesBadgeCount');
                                    let totalUnread = 0;
                                    if (headerBadge && !headerBadge.classList.contains('hidden')) {
                                        const badgeText = headerBadge.textContent.trim();
                                        totalUnread = parseInt(badgeText) || 0;
                                    }
                                    window.updateDropdownItemBadge(userId, unreadCount, totalUnread);
                                }
                                
                                // Update header badge count
                                if (typeof window.loadUnreadCount === 'function') {
                                    window.loadUnreadCount();
                                } else if (typeof window.loadAdminUnreadCount === 'function') {
                                    window.loadAdminUnreadCount();
                                }
                                
                                // Start polling if not already started (for new chats)
                                if (typeof startMessagePolling === 'function') {
                                    const isPolling = pollingIntervals && pollingIntervals.has(userId);
                                    if (!isPolling) {
                                        startMessagePolling(chatElement, userId);
                                    }
                                }
                            } else {
                                // Update temp message to show error
                                if (tempId) {
                                    const errorMsg = data.message || 'Unknown error';
                                    updateTempMessageError(chatElement, tempId, errorMsg);
                                }
                                
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed to send',
                                        text: data.message || 'Unknown error',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert('Failed to send message: ' + (data.message || 'Unknown error'));
                                }
                            }
                        })
                        .catch(error => {
                            // Update temp message to show error
                            if (tempId) {
                                let errorMessage = 'Network error. Please try again.';
                                if (error.response && error.response.data && error.response.data.message) {
                                    errorMessage = error.response.data.message;
                                } else if (error.message) {
                                    errorMessage = error.message;
                                }
                                updateTempMessageError(chatElement, tempId, errorMessage);
                            }
                            
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to send message. Please try again.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                alert('Error sending message. Please try again.');
                            }
                        })
                        .finally(() => {
                            // Re-enable submit button
                            const submitBtn = messageForm.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                            }
                        });
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
            
            // Ensure expanded chats are ordered to the left, minimized to the right
            chats.forEach((chat) => {
                const isExpanded = chat.getAttribute('data-expanded') === 'true';
                if (isExpanded) {
                    hasExpandedChat = true;
                    chat.style.order = '0'; // open chat(s) on the left
                } else {
                    chat.style.order = '1'; // minimized chats on the right
                }
            });
            
            // Layout:
            // - If there is at least one expanded chat: horizontal row, expanded on left, minimized on right
            // - If all chats are minimized: vertical stack on the right
            container.classList.remove('flex-row-reverse', 'flex-col-reverse', 'flex-row');
            
            if (hasExpandedChat) {
                container.classList.add('flex-row');
            } else {
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
        let isDownloading = false; // Flag to prevent multiple simultaneous downloads
        function downloadImage() {
            // Prevent multiple simultaneous downloads
            if (isDownloading) {
                return;
            }
            
            const img = document.getElementById('viewerImage');
            if (img && img.src) {
                isDownloading = true;
                const link = document.createElement('a');
                link.href = img.src;
                link.download = 'image-' + Date.now() + '.png';
                link.setAttribute('data-pdf-modal', 'false');
                document.body.appendChild(link);
                link.click();
                
                // Remove link and reset flag after a short delay
                setTimeout(() => {
                    document.body.removeChild(link);
                    isDownloading = false;
                }, 100);
            }
        }

        // Initialize image viewer
        // Restore chats when page loads (after a short delay to ensure DOM is ready)
        function initRestoreChats() {
            if (container) {
                restoreActiveChats();
            } else {
                // Retry if container not ready yet
                setTimeout(initRestoreChats, 50);
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initRestoreChats, 100);
            });
        } else {
            // DOM already loaded
            setTimeout(initRestoreChats, 100);
        }
        
        // Flag to track if event listeners have been initialized
        let imageViewerListenersInitialized = false;
        // Store handler references for proper cleanup
        let downloadHandlerRef = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize listeners once
            if (imageViewerListenersInitialized) {
                return;
            }
            imageViewerListenersInitialized = true;
            
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
                // Create handler function and store reference
                downloadHandlerRef = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    downloadImage();
                };
                // Remove any existing listener first to prevent duplicates
                if (downloadHandlerRef) {
                    downloadBtn.removeEventListener('click', downloadHandlerRef);
                }
                downloadBtn.addEventListener('click', downloadHandlerRef);
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
        
        // ========== GROUP SETTINGS FUNCTIONALITY ==========
        // Track active group chat for settings
        let popupCurrentGroupId = null;
        let popupCurrentGroupData = null;
        let popupCurrentGroupIsAdmin = false;
        let popupActiveGroupChatUserId = null; // Track which popup's group is being managed
        let popupAddingMembersToGroup = false; // Track if we're adding members to a group
        
        // Load group details for popup
        function popupLoadGroupDetails(groupId, userId) {
            popupCurrentGroupId = groupId;
            window.popupCurrentGroupId = groupId;
            popupActiveGroupChatUserId = userId;
            
            return axios.get(`{{ route('messages.groups.show', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', groupId))
                .then(response => {
                    if (response.data.success) {
                        popupCurrentGroupData = response.data.group;
                        window.popupCurrentGroupData = response.data.group;
                        
                        // Get is_admin from response - it should be explicitly set
                        // If not, check the group object (formatGroupChat includes is_admin)
                        if (response.data.is_admin !== undefined) {
                            popupCurrentGroupIsAdmin = response.data.is_admin;
                            window.popupCurrentGroupIsAdmin = response.data.is_admin;
                        } else if (response.data.group && response.data.group.is_admin !== undefined) {
                            popupCurrentGroupIsAdmin = response.data.group.is_admin;
                            window.popupCurrentGroupIsAdmin = response.data.group.is_admin;
                        } else {
                            // Fallback: check if current user is in members list as admin
                            const currentUserId = @json(Auth::id());
                            const currentUserMember = response.data.group.members?.find(m => m.id === currentUserId);
                            if (currentUserMember) {
                                popupCurrentGroupIsAdmin = currentUserMember.is_admin === true;
                                window.popupCurrentGroupIsAdmin = currentUserMember.is_admin === true;
                            } else {
                                popupCurrentGroupIsAdmin = false;
                                window.popupCurrentGroupIsAdmin = false;
                            }
                        }
                        
                        // Show/hide settings button in the popup header
                        // Use multiple selectors to find the chat element
                        let chatElement = document.querySelector(`[data-user-id="${userId}"]`);
                        if (!chatElement) {
                            // Try alternative selector
                            chatElement = document.querySelector(`.messages-chat-popup[data-user-id="${userId}"]`);
                        }
                        if (!chatElement) {
                            // Try finding by chat ID pattern
                            const allChats = document.querySelectorAll('.messages-chat-popup');
                            for (let chat of allChats) {
                                if (chat.getAttribute('data-user-id') === userId) {
                                    chatElement = chat;
                                    break;
                                }
                            }
                        }
                        if (chatElement) {
                            const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                            if (settingsBtn) {
                                if (popupCurrentGroupIsAdmin === true) {
                                    settingsBtn.classList.remove('hidden');
                                } else {
                                    settingsBtn.classList.add('hidden');
                                }
                            }
                        }
                        
                        // Apply theme if available
                        if (response.data.group?.theme && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                            popupApplyThemeToChat(response.data.group.theme);
                        } else if (!window.popupAvailableThemes || window.popupAvailableThemes.length === 0) {
                            // Load themes first, then apply
                            popupLoadThemes().then(() => {
                                if (response.data.group?.theme) {
                                    popupApplyThemeToChat(response.data.group.theme);
                                }
                            });
                        }
                        
                        return response.data;
                    }
                })
                .catch(error => {
                    console.error('Error loading group details:', error);
                    const chatElement = document.querySelector(`[data-user-id="${userId}"]`);
                    if (chatElement) {
                        const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                        if (settingsBtn) {
                            settingsBtn.classList.add('hidden');
                        }
                    }
                    throw error;
                });
        }
        
        // Open group settings for popup
        function popupOpenGroupSettings(userId) {
            if (!userId || !userId.startsWith('group_')) {
                return;
            }
            
            const groupId = userId.replace('group_', '');
            popupActiveGroupChatUserId = userId;
            
            if (!popupCurrentGroupData || popupCurrentGroupId !== groupId) {
                popupLoadGroupDetails(groupId, userId).then(() => {
                    popupPopulateGroupSettingsModal();
                    document.getElementById('popupGroupSettingsModal').classList.remove('hidden');
                });
            } else {
                popupPopulateGroupSettingsModal();
                document.getElementById('popupGroupSettingsModal').classList.remove('hidden');
            }
        }
        
        // Populate group settings modal
        function popupPopulateGroupSettingsModal() {
            if (!popupCurrentGroupData) {
                console.error('No group data available');
                return;
            }
            
            const nameInput = document.getElementById('popupGroupSettingsNameInput');
            const descInput = document.getElementById('popupGroupSettingsDescriptionInput');
            if (nameInput) {
                nameInput.value = popupCurrentGroupData.name || '';
            }
            if (descInput) {
                descInput.value = popupCurrentGroupData.description || '';
            }
            
            const avatarPreview = document.getElementById('popupGroupSettingsAvatarPreview');
            if (avatarPreview) {
                if (popupCurrentGroupData.avatar) {
                    avatarPreview.src = popupCurrentGroupData.avatar;
                } else {
                    const name = popupCurrentGroupData.name || 'Group';
                    avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                }
            }
            
            popupRenderGroupMembers();
            
            // Load themes if on theme tab
            const themeTab = document.getElementById('popupGroupSettingsThemeTab');
            if (themeTab && !themeTab.classList.contains('hidden')) {
                if (window.popupAvailableThemes && window.popupAvailableThemes.length === 0) {
                    popupLoadThemes();
                } else if (window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                    // Update theme selection to reflect current theme
                    const currentTheme = popupCurrentGroupData?.theme || 'default';
                    window.popupSelectedThemeId = currentTheme;
                    window.popupCurrentAppliedTheme = currentTheme;
                    popupUpdateThemeSelection();
                }
            }
            
            // Apply theme to chat if available
            if (popupCurrentGroupData?.theme && window.popupAvailableThemes && window.popupAvailableThemes.length > 0) {
                popupApplyThemeToChat(popupCurrentGroupData.theme);
            } else if (!window.popupAvailableThemes || window.popupAvailableThemes.length === 0) {
                // Load themes first, then apply
                popupLoadThemes().then(() => {
                    if (popupCurrentGroupData?.theme) {
                        popupApplyThemeToChat(popupCurrentGroupData.theme);
                    }
                });
            }
        }
        
        // Render group members
        function popupRenderGroupMembers() {
            if (!popupCurrentGroupData || !popupCurrentGroupData.members) {
                const membersList = document.getElementById('popupGroupMembersList');
                if (membersList) {
                    membersList.innerHTML = '<div class="text-center py-8 text-gray-400"><p class="text-sm">No members found</p></div>';
                }
                return;
            }
            
            const membersList = document.getElementById('popupGroupMembersList');
            const memberCountBadge = document.getElementById('popupMemberCountBadge');
            const currentUserId = @json(Auth::id());
            
            if (memberCountBadge) {
                memberCountBadge.textContent = popupCurrentGroupData.members.length;
            }
            
            if (membersList) {
                membersList.innerHTML = popupCurrentGroupData.members.map(member => {
                    const fullName = `${member.first_name || ''} ${member.last_name || ''}`.trim();
                    const isCurrentUser = member.id === currentUserId;
                    const canRemove = popupCurrentGroupIsAdmin && !isCurrentUser;
                    const canToggleAdmin = popupCurrentGroupIsAdmin && !isCurrentUser;
                    
                    return `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="relative">
                                    <img src="${member.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&size=40&background=055498&color=fff'}" 
                                         alt="${fullName}" 
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    ${member.is_admin ? `
                                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full flex items-center justify-center border border-white" style="background: #CE2028;">
                                            <i class="fas fa-crown text-white text-[7px]"></i>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-sm font-medium text-gray-800 truncate">${window.escapeHtml ? window.escapeHtml(fullName) : fullName}</p>
                                        ${isCurrentUser ? '<span class="px-1.5 py-0.5 text-xs font-medium rounded" style="background: rgba(5, 84, 152, 0.1); color: #055498;">You</span>' : ''}
                                    </div>
                                    ${member.is_admin ? '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-xs font-semibold rounded" style="background: rgba(206, 32, 40, 0.1); color: #CE2028;"><i class="fas fa-crown text-[7px]"></i> Admin</span>' : '<span class="text-xs text-gray-500">Member</span>'}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${canToggleAdmin ? `
                                    <button type="button" class="popup-toggle-admin-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-colors duration-200 text-white hover:opacity-90" 
                                            data-user-id="${member.id}" 
                                            data-is-admin="${member.is_admin ? 'true' : 'false'}"
                                            style="${member.is_admin ? 'background: #CE2028;' : 'background: #055498;'} cursor: pointer !important; pointer-events: auto !important;">
                                        <i class="fas ${member.is_admin ? 'fa-user-minus' : 'fa-crown'} mr-1" style="pointer-events: none !important;"></i>
                                        ${member.is_admin ? 'Remove Admin' : 'Make Admin'}
                                    </button>
                                ` : ''}
                                ${canRemove ? `
                                    <button class="popup-remove-member-btn px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors duration-200 hover:opacity-90" 
                                            data-user-id="${member.id}"
                                            style="background: #CE2028;">
                                        <i class="fas fa-user-minus mr-1"></i>
                                        Remove
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Attach event listeners using event delegation
                const newMembersList = membersList.cloneNode(true);
                membersList.parentNode.replaceChild(newMembersList, membersList);
                
                newMembersList.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const removeBtn = e.target.closest('.popup-remove-member-btn');
                    if (removeBtn) {
                        e.preventDefault();
                        const userId = removeBtn.getAttribute('data-user-id');
                        if (userId) {
                            popupRemoveGroupMember(userId);
                        }
                        return;
                    }
                    
                    const toggleBtn = e.target.closest('.popup-toggle-admin-btn');
                    if (toggleBtn) {
                        e.preventDefault();
                        const userId = toggleBtn.getAttribute('data-user-id');
                        const isAdminAttr = toggleBtn.getAttribute('data-is-admin');
                        const isAdmin = isAdminAttr === 'true' || isAdminAttr === true || isAdminAttr === '1' || isAdminAttr === 1;
                        popupToggleGroupAdmin(userId, isAdmin);
                        return;
                    }
                });
            }
        }
        
        // Save group info
        function popupSaveGroupInfo() {
            if (!popupCurrentGroupId) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Group ID not found.'
                    });
                }
                return;
            }
            
            const nameInput = document.getElementById('popupGroupSettingsNameInput');
            const descInput = document.getElementById('popupGroupSettingsDescriptionInput');
            
            if (!nameInput) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Group name input not found.'
                    });
                }
                return;
            }
            
            const name = nameInput.value.trim();
            const description = descInput ? descInput.value.trim() : '';
            
            if (!name) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Group Name Required',
                        text: 'Please enter a group name.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                return;
            }
            
            if (window.Swal) {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we update the group information.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
            
            axios.put(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', popupCurrentGroupId), {
                name: name,
                description: description
            })
            .then(response => {
                if (response.data.success) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Group information updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    
                    if (response.data.group) {
                        popupCurrentGroupData = response.data.group;
                    }
                    
                    // Update popup header name
                    if (popupActiveGroupChatUserId) {
                        const chatElement = document.querySelector(`[data-user-id="${popupActiveGroupChatUserId}"]`);
                        if (chatElement) {
                            const nameEl = chatElement.querySelector('.chat-name');
                            if (nameEl) {
                                nameEl.textContent = name;
                            }
                        }
                    }
                    
                    popupLoadGroupDetails(popupCurrentGroupId, popupActiveGroupChatUserId);
                } else {
                    throw new Error(response.data.message || 'Failed to update group information');
                }
            })
            .catch(error => {
                console.error('Error updating group:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to update group information. Please try again.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Remove group member
        function popupRemoveGroupMember(userId) {
            if (!popupCurrentGroupId) return;
            
            if (window.Swal) {
                Swal.fire({
                    title: 'Remove Member?',
                    text: 'Are you sure you want to remove this member from the group?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`{{ route('messages.groups.members.remove', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', popupCurrentGroupId), {
                            data: { user_ids: [userId] }
                        })
                        .then(response => {
                            if (response.data.success) {
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Removed',
                                        text: 'Member removed successfully!',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                                
                                popupLoadGroupDetails(popupCurrentGroupId, popupActiveGroupChatUserId).then(() => {
                                    popupPopulateGroupSettingsModal();
                                }).catch(() => {
                                    popupPopulateGroupSettingsModal();
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error removing member:', error);
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.response?.data?.message || 'Failed to remove member.'
                                });
                            }
                        });
                    }
                });
            }
        }
        
        // Toggle group admin
        function popupToggleGroupAdmin(userId, isCurrentlyAdmin) {
            if (!popupCurrentGroupId) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Group ID not found.'
                    });
                }
                return;
            }
            
            const endpoint = isCurrentlyAdmin 
                ? `{{ route('messages.groups.admins.revoke', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', popupCurrentGroupId)
                : `{{ route('messages.groups.admins.assign', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', popupCurrentGroupId);
            
            if (window.Swal) {
                Swal.fire({
                    title: isCurrentlyAdmin ? 'Removing Admin...' : 'Assigning Admin...',
                    text: 'Please wait.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
            
            const requestPromise = isCurrentlyAdmin
                ? axios.delete(endpoint, { data: { user_ids: [userId] } })
                : axios.post(endpoint, { user_ids: [userId] });
            
            requestPromise
                .then(response => {
                    if (response.data.success) {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `Admin privileges ${isCurrentlyAdmin ? 'revoked' : 'assigned'} successfully!`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        
                        if (response.data.group) {
                            window.popupCurrentGroupData = response.data.group;
                            popupCurrentGroupData = response.data.group;
                            
                            // Update admin status from response - check multiple sources
                            let newAdminStatus = false;
                            if (response.data.is_admin !== undefined) {
                                newAdminStatus = response.data.is_admin;
                            } else if (response.data.group && response.data.group.is_admin !== undefined) {
                                newAdminStatus = response.data.group.is_admin;
                            } else {
                                // Fallback: check if current user is in members list as admin
                                const currentUserId = @json(Auth::id());
                                const currentUserMember = response.data.group.members?.find(m => m.id === currentUserId);
                                if (currentUserMember) {
                                    newAdminStatus = currentUserMember.is_admin === true;
                                }
                            }
                            
                            popupCurrentGroupIsAdmin = newAdminStatus;
                            window.popupCurrentGroupIsAdmin = newAdminStatus;
                            
                            // Update settings button visibility
                            if (popupActiveGroupChatUserId) {
                                const chatElement = document.querySelector(`[data-user-id="${popupActiveGroupChatUserId}"]`);
                                if (chatElement) {
                                    const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                                    if (settingsBtn) {
                                        if (newAdminStatus) {
                                            settingsBtn.classList.remove('hidden');
                                        } else {
                                            settingsBtn.classList.add('hidden');
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Reload group details to get fresh data from database
                        if (window.popupLoadGroupDetails) {
                            window.popupLoadGroupDetails(window.popupCurrentGroupId, popupActiveGroupChatUserId).then(() => {
                                if (window.popupPopulateGroupSettingsModal) {
                                    window.popupPopulateGroupSettingsModal();
                                }
                            }).catch((error) => {
                                console.error('Error reloading group details:', error);
                                if (window.popupCurrentGroupData) {
                                    if (window.popupPopulateGroupSettingsModal) {
                                        window.popupPopulateGroupSettingsModal();
                                    }
                                }
                            });
                        }
                    } else {
                        throw new Error(response.data.message || 'Failed to update admin privileges');
                    }
                })
                .catch(error => {
                    console.error('Error toggling admin:', error);
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.response?.data?.message || 'Failed to update admin privileges. Please try again.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
        }
        
        // Check if user is admin when loading group chat
        // This will be called when a group chat is opened
        function checkGroupAdminStatus(userId) {
            if (userId && userId.startsWith('group_')) {
                const groupId = userId.replace('group_', '');
                popupLoadGroupDetails(groupId, userId)
                    .then(() => {
                        // Ensure button visibility is updated after loading
                        // Use multiple selectors to find the chat element
                        let chatElement = document.querySelector(`[data-user-id="${userId}"]`);
                        if (!chatElement) {
                            chatElement = document.querySelector(`.messages-chat-popup[data-user-id="${userId}"]`);
                        }
                        if (!chatElement) {
                            const allChats = document.querySelectorAll('.messages-chat-popup');
                            for (let chat of allChats) {
                                if (chat.getAttribute('data-user-id') === userId) {
                                    chatElement = chat;
                                    break;
                                }
                            }
                        }
                        if (chatElement) {
                            const settingsBtn = chatElement.querySelector('.popup-group-settings-btn');
                            if (settingsBtn) {
                                if (window.popupCurrentGroupIsAdmin === true) {
                                    settingsBtn.classList.remove('hidden');
                                } else {
                                    settingsBtn.classList.add('hidden');
                                }
                            }
                        }
                    })
                    .catch(() => {});
            }
        }
        
        // Check admin status when group chat is loaded
        // This is called after fetchUserProfile completes for group chats
        // We'll hook into the createChatWithUser function to check admin status
        const originalCreateChatWithUser = createChatWithUser;
        createChatWithUser = function(userId, user, isExpanded) {
            originalCreateChatWithUser(userId, user, isExpanded);
            // Check admin status for group chats immediately and with retries
            if (userId && userId.startsWith('group_')) {
                // Try immediately
                setTimeout(() => {
                    checkGroupAdminStatus(userId);
                }, 100);
                // Also try after a longer delay in case DOM isn't ready
                setTimeout(() => {
                    checkGroupAdminStatus(userId);
                }, 1000);
            }
        };
        
        // Theme Management for Popup
        window.popupAvailableThemes = window.popupAvailableThemes || [];
        window.popupSelectedThemeId = null;
        window.popupPreviewThemeId = null;
        window.popupCurrentAppliedTheme = null;
        
        // Tab switching functionality
        function popupSwitchGroupSettingsTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.popup-tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.popup-group-settings-tab').forEach(tab => {
                tab.classList.remove('text-gray-800');
                tab.classList.add('text-gray-500', 'border-transparent');
                tab.style.borderColor = 'transparent';
                tab.style.color = '';
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById(`popupGroupSettings${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Tab`);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }
            
            // Activate selected tab button
            const selectedTabBtn = document.querySelector(`.popup-group-settings-tab[data-tab="${tabName}"]`);
            if (selectedTabBtn) {
                selectedTabBtn.classList.remove('text-gray-500', 'border-transparent');
                selectedTabBtn.classList.add('text-gray-800');
                selectedTabBtn.style.borderColor = '#055498';
                selectedTabBtn.style.color = '#055498';
            }
            
            // Toggle footer visibility based on active tab
            const defaultFooter = document.getElementById('popupDefaultModalFooter');
            const themeFooter = document.getElementById('popupThemeModalFooter');
            if (tabName === 'theme') {
                if (defaultFooter) defaultFooter.classList.add('hidden');
                if (themeFooter) themeFooter.classList.remove('hidden');
            } else {
                if (defaultFooter) defaultFooter.classList.remove('hidden');
                if (themeFooter) themeFooter.classList.add('hidden');
            }
            
            // Load themes if switching to theme tab
            if (tabName === 'theme' && (!window.popupAvailableThemes || window.popupAvailableThemes.length === 0)) {
                popupLoadThemes();
            }
        }
        
        // Load available themes
        async function popupLoadThemes() {
            try {
                const response = await axios.get('{{ route("messages.groups.themes") }}');
                if (response.data.success) {
                    window.popupAvailableThemes = response.data.themes;
                    popupRenderThemeSelection();
                    
                    // Set current theme
                    const currentTheme = popupCurrentGroupData?.theme || 'default';
                    window.popupSelectedThemeId = currentTheme;
                    window.popupCurrentAppliedTheme = currentTheme;
                    popupUpdateThemeSelection();
                    
                    // Show preview of current theme
                    if (currentTheme) {
                        popupShowThemePreview(currentTheme);
                    }
                }
            } catch (error) {
                console.error('Error loading themes:', error);
                const themeGrid = document.getElementById('popupThemeSelectionGrid');
                if (themeGrid) {
                    themeGrid.innerHTML = '<div class="text-center py-4 text-red-500 text-sm">Failed to load themes</div>';
                }
            }
        }
        
        // Render theme selection grid
        function popupRenderThemeSelection() {
            const themeGrid = document.getElementById('popupThemeSelectionGrid');
            if (!themeGrid) return;
            
            themeGrid.innerHTML = window.popupAvailableThemes.map(theme => {
                const isSelected = window.popupSelectedThemeId === theme.id;
                const isApplied = window.popupCurrentAppliedTheme === theme.id;
                
                // Create thumbnail preview
                let thumbnailStyle = '';
                if (theme.background_image) {
                    thumbnailStyle = `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};`;
                } else if (theme.background.startsWith('linear-gradient')) {
                    thumbnailStyle = `background: ${theme.background};`;
                } else {
                    thumbnailStyle = `background: ${theme.background};`;
                }
                
                return `
                    <div class="theme-option group relative cursor-pointer bg-white rounded-lg border-2 transition-all duration-200 ${isSelected ? 'border-blue-500 ring-2 ring-blue-200 shadow-lg' : 'border-gray-200 hover:border-gray-300 hover:shadow-md'} overflow-hidden" 
                         data-theme-id="${theme.id}">
                        <div class="flex items-stretch">
                            <!-- Theme Preview Thumbnail -->
                            <div class="w-24 flex-shrink-0 relative overflow-hidden" style="${thumbnailStyle}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                                <!-- Preview Bubbles -->
                                <div class="absolute bottom-2 left-0 right-0 flex items-end justify-center gap-1.5 px-2">
                                    <div class="w-8 h-5 rounded-md shadow-sm border border-white/30" style="background: ${theme.receiver_bubble};"></div>
                                    <div class="w-10 h-6 rounded-md shadow-sm border border-white/30" style="background: ${theme.sender_bubble};"></div>
                                </div>
                            </div>
                            
                            <!-- Theme Info -->
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <h6 class="text-sm font-semibold text-gray-800">${window.escapeHtml ? window.escapeHtml(theme.name) : theme.name}</h6>
                                        ${isSelected ? `
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-white text-[10px]"></i>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed mb-2">${window.escapeHtml ? window.escapeHtml(theme.description) : theme.description}</p>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <!-- Color Swatches -->
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.sender_bubble};"></div>
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.receiver_bubble};"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Applied Badge -->
                                    ${isApplied ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-full">
                                            <i class="fas fa-check-circle text-[9px]"></i>
                                            <span>Active</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hover Effect -->
                        <div class="absolute inset-0 bg-blue-50/0 group-hover:bg-blue-50/30 transition-all duration-200 pointer-events-none"></div>
                    </div>
                `;
            }).join('');
            
            // Add click handlers
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const themeId = this.getAttribute('data-theme-id');
                    popupSelectTheme(themeId);
                });
            });
        }
        
        // Select a theme
        function popupSelectTheme(themeId) {
            window.popupSelectedThemeId = themeId;
            popupUpdateThemeSelection();
            popupShowThemePreview(themeId);
            
            // Apply theme preview in real-time (without saving)
            const theme = window.popupAvailableThemes.find(t => t.id === themeId);
            if (theme && popupActiveGroupChatUserId && popupActiveGroupChatUserId.startsWith('group_')) {
                // Temporarily apply theme for preview (real-time)
                popupApplyThemeToChat(themeId);
                if (theme) {
                    popupUpdateMessageBubblesTheme(theme);
                }
            }
            
            // Show apply and cancel buttons
            const applyBtn = document.getElementById('popupApplyThemeBtn');
            const cancelBtn = document.getElementById('popupCancelThemeBtn');
            if (applyBtn) applyBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.remove('hidden');
        }
        
        // Update theme selection UI
        function popupUpdateThemeSelection() {
            const themeGrid = document.getElementById('popupThemeSelectionGrid');
            if (!themeGrid) return;
            
            themeGrid.querySelectorAll('.theme-option').forEach(option => {
                const themeId = option.getAttribute('data-theme-id');
                const isSelected = window.popupSelectedThemeId === themeId;
                const isApplied = window.popupCurrentAppliedTheme === themeId;
                
                if (isSelected) {
                    option.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.remove('border-gray-200');
                    
                    // Add checkmark if not present
                    if (!option.querySelector('.fa-check')) {
                        const checkmark = document.createElement('div');
                        checkmark.className = 'absolute top-2 right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center';
                        checkmark.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                        option.appendChild(checkmark);
                    }
                } else {
                    option.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                    option.classList.add('border-gray-200');
                    
                    // Remove checkmark
                    const checkmark = option.querySelector('.fa-check');
                    if (checkmark && checkmark.closest('.absolute.top-2')) {
                        checkmark.closest('.absolute.top-2').remove();
                    }
                }
                
                // Update "Applied" badge
                const appliedBadge = option.querySelector('.bg-green-500');
                if (isApplied && !appliedBadge) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute bottom-2 left-2 px-2 py-0.5 bg-green-500 rounded text-white text-[10px] font-semibold';
                    badge.textContent = 'Applied';
                    option.appendChild(badge);
                } else if (!isApplied && appliedBadge) {
                    appliedBadge.remove();
                }
            });
        }
        
        // Show theme preview
        function popupShowThemePreview(themeId) {
            const theme = window.popupAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const previewSection = document.getElementById('popupThemePreviewSection');
            const previewContainer = document.getElementById('popupThemePreviewContainer');
            
            if (!previewSection || !previewContainer) return;
            
            window.popupPreviewThemeId = themeId;
            
            // Helper function to check if color is light (matches backend logic)
            const isLightColor = (color) => {
                if (!color) return false;
                
                // Check for common dark/black color names
                const darkColors = ['black', '#000', '#000000', 'rgb(0,0,0)', 'rgba(0,0,0'];
                const colorLower = color.toLowerCase().trim();
                for (const darkColor of darkColors) {
                    if (colorLower.includes(darkColor)) {
                        return false; // Definitely dark
                    }
                }
                
                let r, g, b;
                if (color.startsWith('#')) {
                    let hex = color.replace('#', '');
                    // Handle 3-digit hex
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    if (hex.length === 6) {
                        r = parseInt(hex.substring(0, 2), 16);
                        g = parseInt(hex.substring(2, 4), 16);
                        b = parseInt(hex.substring(4, 6), 16);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else if (color.startsWith('rgb')) {
                    const matches = color.match(/\d+/g);
                    if (matches && matches.length >= 3) {
                        r = parseInt(matches[0]);
                        g = parseInt(matches[1]);
                        b = parseInt(matches[2]);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                
                // YIQ formula: brightness threshold lowered to 150 (matches backend)
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq > 150; // Lower threshold to catch more dark colors
            };
            
            const headerBgColor = theme.header_color || theme.accent_color;
            const headerTextColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
            const iconColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
            
            // Create full preview with header, messages, and input area
            previewContainer.innerHTML = `
                <!-- Chat Header Preview -->
                <div class="border-b rounded-t-lg" style="background: ${headerBgColor}; border-color: ${headerBgColor};">
                    <div class="px-3 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold truncate" style="color: ${headerTextColor};">Group Chat Preview</h3>
                                <p class="text-xs truncate" style="color: ${headerTextColor}; opacity: 0.8;">Group chat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-1.5 rounded-full hover:bg-white/10 transition" style="color: ${iconColor};">
                                <i class="fas fa-cog text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area Preview -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3 min-h-[200px] max-h-[250px]" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`}">
                    <!-- Received Message -->
                    <div class="flex items-start space-x-2">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs mb-1" style="color: ${theme.receiver_text}; opacity: 0.7;">John Doe</div>
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.receiver_bubble}; color: ${theme.receiver_text};">
                                <p class="text-xs">Hey! How are you doing?</p>
                            </div>
                            <div class="text-[10px] mt-1" style="color: ${theme.receiver_text}; opacity: 0.5;">10:30 AM</div>
                        </div>
                    </div>
                    
                    <!-- Sent Message -->
                    <div class="flex items-start space-x-2 justify-end">
                        <div class="flex-1 flex justify-end">
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.sender_bubble}; color: ${theme.sender_text};">
                                <p class="text-xs">I'm doing great, thanks!</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Input Area Preview -->
                <div class="border-t rounded-b-lg p-2" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`} border-color: ${theme.receiver_bubble};">
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition" style="color: ${theme.accent_color || '#6b7280'};">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <div class="flex-1 rounded-lg border px-3 py-2" style="background: white; border-color: ${theme.receiver_bubble};">
                            <input type="text" placeholder="Type a message..." class="w-full text-xs outline-none" style="color: #1f2937;" disabled>
                        </div>
                        <button class="p-2 rounded-lg transition" style="background: ${theme.accent_color || '#3b82f6'}; color: #ffffff;">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Ensure preview section is visible
            previewSection.classList.remove('hidden');
        }
        
        // Apply theme
        async function popupApplyTheme() {
            if (!window.popupSelectedThemeId || !window.popupCurrentGroupId) return;
            
            try {
                const response = await axios.post(`{{ route('messages.groups.theme.apply', ['groupId' => ':groupId']) }}`.replace(':groupId', window.popupCurrentGroupId), {
                    theme: window.popupSelectedThemeId
                });
                
                if (response.data.success) {
                    // Update current group data
                    if (popupCurrentGroupData) {
                        popupCurrentGroupData.theme = window.popupSelectedThemeId;
                    }
                    
                    window.popupCurrentAppliedTheme = window.popupSelectedThemeId;
                    
                    // Apply theme to chat immediately for real-time update
                    popupApplyThemeToChat(window.popupSelectedThemeId);
                    
                    // Force update all existing messages immediately
                    const theme = window.popupAvailableThemes.find(t => t.id === window.popupSelectedThemeId);
                    if (theme) {
                        popupUpdateMessageBubblesTheme(theme);
                    }
                    
                    // Show preview of applied theme
                    popupShowThemePreview(window.popupSelectedThemeId);
                    
                    // Hide buttons
                    const applyBtn = document.getElementById('popupApplyThemeBtn');
                    const cancelBtn = document.getElementById('popupCancelThemeBtn');
                    if (applyBtn) applyBtn.classList.add('hidden');
                    if (cancelBtn) cancelBtn.classList.add('hidden');
                    
                    // Update theme selection UI
                    popupUpdateThemeSelection();
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Theme Applied',
                            text: 'The chat theme has been updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            } catch (error) {
                console.error('Error applying theme:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to apply theme. Please try again.',
                        confirmButtonColor: '#055498'
                    });
                }
            }
        }
        
        // Cancel theme selection
        function popupCancelThemeSelection() {
            // Reset to current theme
            const currentTheme = popupCurrentGroupData?.theme || 'default';
            window.popupSelectedThemeId = currentTheme;
            popupUpdateThemeSelection();
            
            // Show preview of current theme instead of hiding
            if (currentTheme) {
                popupShowThemePreview(currentTheme);
            }
            
            // Restore original theme in real-time
            if (popupActiveGroupChatUserId && popupActiveGroupChatUserId.startsWith('group_')) {
                popupApplyThemeToChat(currentTheme);
                const theme = window.popupAvailableThemes.find(t => t.id === currentTheme);
                if (theme) {
                    popupUpdateMessageBubblesTheme(theme);
                }
            }
            
            // Hide buttons
            const applyBtn = document.getElementById('popupApplyThemeBtn');
            const cancelBtn = document.getElementById('popupCancelThemeBtn');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
        }
        
        // Apply theme to chat area - ONLY for the current group chat
        function popupApplyThemeToChat(themeId) {
            // Only apply theme if we're currently viewing the group chat that has this theme
            if (!popupActiveGroupChatUserId || !popupActiveGroupChatUserId.startsWith('group_')) {
                return; // Not a group chat, don't apply theme
            }
            
            const currentGroupId = popupActiveGroupChatUserId.replace('group_', '');
            if (window.popupCurrentGroupId && currentGroupId !== window.popupCurrentGroupId.toString()) {
                return; // Different group chat, don't apply theme
            }
            
            const theme = window.popupAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const chatMessagesArea = document.querySelector('.chat-messages');
            if (!chatMessagesArea) return;
            
            // Store theme in data attribute with group ID to ensure isolation
            chatMessagesArea.setAttribute('data-theme-id', themeId);
            chatMessagesArea.setAttribute('data-theme-group-id', currentGroupId);
            
            // Apply background - support both color and image
            if (theme.background_image) {
                chatMessagesArea.style.backgroundImage = `url(${theme.background_image})`;
                chatMessagesArea.style.backgroundSize = 'cover';
                chatMessagesArea.style.backgroundPosition = 'center';
                chatMessagesArea.style.backgroundRepeat = 'no-repeat';
                chatMessagesArea.style.backgroundColor = theme.background; // Fallback color
            } else {
                chatMessagesArea.style.backgroundImage = '';
                chatMessagesArea.style.backgroundSize = '';
                chatMessagesArea.style.backgroundPosition = '';
                chatMessagesArea.style.backgroundRepeat = '';
                chatMessagesArea.style.background = theme.background;
            }
            
            // Apply theme to popup chat header
            const chatHeader = document.querySelector('.messages-chat-popup[data-user-id="' + popupActiveGroupChatUserId + '"] .messages-chat-header');
            if (chatHeader && popupActiveGroupChatUserId && popupActiveGroupChatUserId.startsWith('group_')) {
                const headerBgColor = theme.header_color || theme.accent_color;
                chatHeader.style.setProperty('background-color', headerBgColor, 'important');
                chatHeader.style.setProperty('border-color', headerBgColor, 'important');
                
                // Helper function to check if color is light
                const isLightColor = (color) => {
                    if (!color) return false;
                    // Convert hex to RGB
                    let r, g, b;
                    if (color.startsWith('#')) {
                        r = parseInt(color.slice(1, 3), 16);
                        g = parseInt(color.slice(3, 5), 16);
                        b = parseInt(color.slice(5, 7), 16);
                    } else if (color.startsWith('rgb')) {
                        const matches = color.match(/\d+/g);
                        if (matches && matches.length >= 3) {
                            r = parseInt(matches[0]);
                            g = parseInt(matches[1]);
                            b = parseInt(matches[2]);
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                    // Calculate luminance (YIQ formula)
                    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                    return yiq >= 128;
                };
                
                // Update header text color for readability
                const headerText = chatHeader.querySelectorAll('h3, p, span');
                const textColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
                headerText.forEach(el => {
                    el.style.setProperty('color', textColor, 'important');
                });
                
                // Update all icons in header (settings, minimize, close buttons)
                // Use appropriate color based on background brightness
                const iconColor = isLightColor(headerBgColor) ? '#1f2937' : '#ffffff';
                const headerIcons = chatHeader.querySelectorAll('.popup-group-settings-btn i, .chat-minimize-btn svg, .chat-close-btn svg');
                headerIcons.forEach(icon => {
                    icon.style.setProperty('color', iconColor, 'important');
                });
                
                // Update the buttons themselves
                const headerButtons = chatHeader.querySelectorAll('.popup-group-settings-btn, .chat-minimize-btn, .chat-close-btn');
                headerButtons.forEach(btn => {
                    btn.style.setProperty('color', iconColor, 'important');
                });
            }
            
            // Update send button icon color (if exists in popup) - always use white for contrast on colored background
            const sendBtn = document.querySelector('.messages-chat-popup[data-user-id="' + popupActiveGroupChatUserId + '"] #sendBtn, .messages-chat-popup[data-user-id="' + popupActiveGroupChatUserId + '"] button[type="submit"]');
            if (sendBtn) {
                const sendIcon = sendBtn.querySelector('i, svg');
                if (sendIcon) {
                    sendIcon.style.color = '#ffffff';
                }
                // Also update button background if needed
                if (theme.accent_color) {
                    sendBtn.style.background = `linear-gradient(135deg, ${theme.accent_color} 0%, ${theme.accent_color}dd 100%)`;
                }
            }
            
            // Update voice recorder buttons (if exists in popup)
            const voiceRecorder = document.querySelector('.messages-chat-popup[data-user-id="' + popupActiveGroupChatUserId + '"] .chat-voice-recorder');
            if (voiceRecorder) {
                // Update voice recorder background
                const voiceRecorderBar = voiceRecorder.querySelector('div[style*="background-color"]');
                if (voiceRecorderBar && theme.accent_color) {
                    voiceRecorderBar.style.backgroundColor = theme.accent_color;
                }
                
                // Update voice recorder buttons using class selectors
                const voiceCancelBtn = voiceRecorder.querySelector('.voice-cancel-btn');
                const voiceStopBtn = voiceRecorder.querySelector('.voice-stop-btn');
                const voiceSendBtn = voiceRecorder.querySelector('.voice-send-btn');
                const voiceTimer = voiceRecorder.querySelector('.voice-timer');
                
                if (voiceCancelBtn) {
                    voiceCancelBtn.style.color = '#ffffff';
                }
                if (voiceStopBtn) {
                    const stopIcon = voiceStopBtn.querySelector('span');
                    if (stopIcon && theme.accent_color) {
                        stopIcon.style.backgroundColor = theme.accent_color;
                    }
                }
                if (voiceSendBtn) {
                    voiceSendBtn.style.color = theme.accent_color || '#FF1F70';
                    const sendIcon = voiceSendBtn.querySelector('svg');
                    if (sendIcon) {
                        sendIcon.style.color = theme.accent_color || '#FF1F70';
                    }
                }
                if (voiceTimer && theme.accent_color) {
                    voiceTimer.style.color = theme.accent_color;
                }
            }
            
            // Update existing message bubbles and voice messages
            popupUpdateMessageBubblesTheme(theme);
        }
        
        // Apply theme to a single message element
        function popupApplyThemeToMessage(messageDiv, theme, isSender) {
            if (!messageDiv || !theme) return;
            
            if (isSender) {
                // Text message bubbles
                const textBubbles = messageDiv.querySelectorAll('.bg-gradient-to-r, .bg-\\[\\#FF1F70\\], .bg-\\[\\#055498\\]');
                textBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    bubble.style.color = theme.sender_text;
                });
                
                // Voice message bubbles
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.sender_text;
                    }
                    
                    const playButton = bubble.querySelector('.voice-play-toggle');
                    if (playButton) {
                        playButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        playButton.style.color = theme.sender_bubble;
                    }
                    
                    const speedButton = bubble.querySelector('.voice-speed-toggle');
                    if (speedButton) {
                        speedButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        speedButton.style.color = theme.sender_bubble;
                    }
                    
                    const waveformBars = bubble.querySelectorAll('.waveform-bar');
                    waveformBars.forEach(bar => {
                        bar.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.8)' : 'rgba(255, 255, 255, 0.4)';
                    });
                });
            } else {
                // Receiver bubbles
                const textBubbles = messageDiv.querySelectorAll('.bg-white.text-gray-800, .bg-white');
                textBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.color = theme.receiver_text;
                });
                
                // Receiver voice messages
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.borderColor = theme.receiver_bubble !== '#ffffff' ? theme.receiver_bubble : '#e5e7eb';
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.receiver_text;
                    }
                });
            }
        }
        
        // Update message bubbles with theme
        function popupUpdateMessageBubblesTheme(theme) {
            const chatMessagesArea = document.querySelector('.chat-messages');
            if (!chatMessagesArea) return;
            
            // Update sender bubbles (messages on the right)
            chatMessagesArea.querySelectorAll('.flex.items-start.space-x-2.justify-end').forEach(messageDiv => {
                // Text message bubbles - find by classes OR inline styles
                const textBubbles = messageDiv.querySelectorAll('.space-y-2 > div, .bg-gradient-to-r, .bg-\\[\\#FF1F70\\], .bg-\\[\\#055498\\]');
                textBubbles.forEach(bubble => {
                    // Check if this is a message bubble (has rounded-lg, p-2, or has inline style with background)
                    const hasStyle = bubble.hasAttribute('style') && bubble.getAttribute('style').includes('background');
                    const hasBubbleClasses = bubble.classList.contains('rounded-lg') || bubble.classList.contains('p-2') || 
                                            bubble.classList.contains('bg-gradient-to-r') || 
                                            bubble.classList.contains('bg-[#FF1F70]') || 
                                            bubble.classList.contains('bg-[#055498]');
                    
                    if (hasStyle || hasBubbleClasses) {
                        // Check if it's inside space-y-2 (message content area)
                        const isInMessageContent = bubble.closest('.space-y-2') || bubble.parentElement?.classList.contains('space-y-2');
                        if (isInMessageContent || hasBubbleClasses) {
                            bubble.style.setProperty('background', theme.sender_bubble, 'important');
                            bubble.style.setProperty('color', theme.sender_text, 'important');
                        }
                    }
                });
                
                // Also find any div with inline background style in the message
                const allBubbles = messageDiv.querySelectorAll('div[style*="background"]');
                allBubbles.forEach(bubble => {
                    // Check if it's a message bubble (not a button, icon, etc.)
                    if (bubble.classList.contains('rounded-lg') || 
                        bubble.classList.contains('p-2') || 
                        bubble.closest('.space-y-2')) {
                        const style = bubble.getAttribute('style') || '';
                        // Only update if it looks like a message bubble (has background color)
                        if (style.includes('background') && !bubble.classList.contains('voice-message-container')) {
                            bubble.style.setProperty('background', theme.sender_bubble, 'important');
                            if (style.includes('color')) {
                                bubble.style.setProperty('color', theme.sender_text, 'important');
                            }
                        }
                    }
                });
                
                // Voice message bubbles
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.sender_bubble;
                    
                    const durationText = bubble.querySelector('.voice-duration, .text-white');
                    if (durationText) {
                        durationText.style.color = theme.sender_text;
                    }
                    
                    const playButton = bubble.querySelector('.voice-play-toggle');
                    if (playButton) {
                        playButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        playButton.style.color = theme.sender_bubble;
                    }
                    
                    const speedButton = bubble.querySelector('.voice-speed-toggle');
                    if (speedButton) {
                        speedButton.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.9)' : 'rgba(255, 255, 255, 0.2)';
                        speedButton.style.color = theme.sender_bubble;
                    }
                    
                    const waveformBars = bubble.querySelectorAll('.waveform-bar');
                    waveformBars.forEach(bar => {
                        bar.style.backgroundColor = theme.sender_text === '#ffffff' ? 'rgba(255, 255, 255, 0.8)' : 'rgba(255, 255, 255, 0.4)';
                    });
                });
                
                // Update reaction colors for sent messages
                const reactions = messageDiv.querySelectorAll('.message-reactions');
                reactions.forEach(reaction => {
                    reaction.style.setProperty('color', theme.sender_text, 'important');
                });
            });
            
            // Update receiver bubbles (messages on the left)
            chatMessagesArea.querySelectorAll('.flex.items-start.space-x-2:not(.justify-end)').forEach(messageDiv => {
                const textBubbles = messageDiv.querySelectorAll('.space-y-2 > div, .bg-white.text-gray-800, .bg-white');
                textBubbles.forEach(bubble => {
                    // Check if this is a message bubble
                    const hasStyle = bubble.hasAttribute('style') && bubble.getAttribute('style').includes('background');
                    const hasBubbleClasses = bubble.classList.contains('rounded-lg') || bubble.classList.contains('p-2') || 
                                            bubble.classList.contains('bg-white');
                    
                    if (hasStyle || hasBubbleClasses) {
                        const isInMessageContent = bubble.closest('.space-y-2') || bubble.parentElement?.classList.contains('space-y-2');
                        if (isInMessageContent || hasBubbleClasses) {
                            bubble.style.setProperty('background', theme.receiver_bubble, 'important');
                            bubble.style.setProperty('color', theme.receiver_text, 'important');
                        }
                    }
                });
                
                // Also find any div with inline background style in the message
                const allBubbles = messageDiv.querySelectorAll('div[style*="background"]');
                allBubbles.forEach(bubble => {
                    // Check if it's a message bubble (not a button, icon, etc.)
                    if (bubble.classList.contains('rounded-lg') || 
                        bubble.classList.contains('p-2') || 
                        bubble.closest('.space-y-2')) {
                        const style = bubble.getAttribute('style') || '';
                        // Only update if it looks like a message bubble (has background color)
                        if (style.includes('background') && !bubble.classList.contains('voice-message-container')) {
                            bubble.style.setProperty('background', theme.receiver_bubble, 'important');
                            if (style.includes('color')) {
                                bubble.style.setProperty('color', theme.receiver_text, 'important');
                            }
                        }
                    }
                });
                
                // Receiver voice messages
                const voiceBubbles = messageDiv.querySelectorAll('.voice-message-container');
                voiceBubbles.forEach(bubble => {
                    bubble.style.background = theme.receiver_bubble;
                    bubble.style.borderColor = theme.receiver_bubble !== '#ffffff' ? theme.receiver_bubble : '#e5e7eb';
                    
                    const durationText = bubble.querySelector('.voice-duration');
                    if (durationText) {
                        durationText.style.color = theme.receiver_text;
                    }
                });
                
                // Update sender name colors for received messages (group chats)
                const senderNames = messageDiv.querySelectorAll('p.text-xs.font-semibold.mb-1');
                senderNames.forEach(name => {
                    name.style.setProperty('color', theme.receiver_text, 'important');
                });
                
                // Update reaction colors for received messages
                const reactions = messageDiv.querySelectorAll('.message-reactions');
                reactions.forEach(reaction => {
                    reaction.style.setProperty('color', theme.receiver_text, 'important');
                });
            });
            
            // Update reaction colors for sent messages
            chatMessagesArea.querySelectorAll('.flex.items-start.space-x-2.justify-end').forEach(messageDiv => {
                const reactions = messageDiv.querySelectorAll('.message-reactions');
                reactions.forEach(reaction => {
                    reaction.style.setProperty('color', theme.sender_text, 'important');
                });
            });
        }
        
        // Expose functions and variables to global scope for event listeners
        window.popupOpenGroupSettings = popupOpenGroupSettings;
        window.popupSaveGroupInfo = popupSaveGroupInfo;
        window.popupLoadGroupDetails = popupLoadGroupDetails;
        window.popupPopulateGroupSettingsModal = popupPopulateGroupSettingsModal;
        window.popupApplyTheme = popupApplyTheme;
        window.popupCancelThemeSelection = popupCancelThemeSelection;
        window.popupSwitchGroupSettingsTab = popupSwitchGroupSettingsTab;
        window.escapeHtml = escapeHtml; // Expose escapeHtml for use outside IIFE
        
        // Single Chat Theme Variables
        let popupSingleChatAvailableThemes = [];
        let popupSingleChatCurrentAppliedTheme = null;
        let popupSingleChatSelectedThemeId = null;
        let popupActiveSingleChatUserId = null;
        
        // Load single chat themes
        async function popupLoadSingleChatThemes() {
            if (popupSingleChatAvailableThemes.length > 0) {
                return popupSingleChatAvailableThemes;
            }
            
            try {
                const response = await axios.get('{{ route("messages.themes") }}');
                if (response.data.success && response.data.themes) {
                    popupSingleChatAvailableThemes = response.data.themes;
                    return popupSingleChatAvailableThemes;
                }
            } catch (error) {
                console.error('Error loading single chat themes:', error);
            }
            return [];
        }
        
        // Load current theme for a single chat
        async function popupLoadSingleChatCurrentTheme(userId) {
            try {
                const response = await axios.get(`{{ route("messages.conversation.theme", ["otherUserId" => ":userId"]) }}`.replace(':userId', userId));
                if (response.data.success) {
                    popupSingleChatCurrentAppliedTheme = response.data.theme || null;
                    return popupSingleChatCurrentAppliedTheme;
                }
            } catch (error) {
                console.error('Error loading single chat theme:', error);
            }
            return null;
        }
        
        // Load and apply theme for single chat popup
        async function popupLoadSingleChatTheme(userId, chatElement) {
            if (!userId || userId.startsWith('group_')) return;
            
            try {
                // Load themes if not loaded
                if (popupSingleChatAvailableThemes.length === 0) {
                    await popupLoadSingleChatThemes();
                }
                
                // Load current theme
                const themeId = await popupLoadSingleChatCurrentTheme(userId);
                if (themeId && chatElement) {
                    popupApplySingleChatThemeToChat(themeId, chatElement);
                }
            } catch (error) {
                console.error('Error loading single chat theme:', error);
            }
        }
        
        // Apply single chat theme to popup chat
        function popupApplySingleChatThemeToChat(themeId, chatElement) {
            if (!chatElement || !themeId) return;
            
            const theme = popupSingleChatAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const messagesArea = chatElement.querySelector('.chat-messages');
            if (!messagesArea) return;
            
            // Store theme info
            messagesArea.setAttribute('data-theme-id', themeId);
            messagesArea.setAttribute('data-theme-user-id', chatElement.getAttribute('data-user-id'));
            
            // Apply background
            if (theme.background_image) {
                messagesArea.style.backgroundImage = `url(${theme.background_image})`;
                messagesArea.style.backgroundSize = 'cover';
                messagesArea.style.backgroundPosition = 'center';
                messagesArea.style.backgroundRepeat = 'no-repeat';
            } else {
                messagesArea.style.backgroundImage = 'none';
                messagesArea.style.backgroundColor = theme.background;
            }
            
            // Update existing message bubbles
            popupUpdateSingleChatMessageBubblesTheme(theme, messagesArea);
        }
        
        // Update message bubbles with theme colors
        function popupUpdateSingleChatMessageBubblesTheme(theme, messagesArea) {
            if (!messagesArea || !theme) return;
            
            const messages = messagesArea.querySelectorAll('.message-bubble');
            messages.forEach(message => {
                const isSender = message.classList.contains('sent');
                if (isSender) {
                    message.style.background = theme.sender_bubble;
                    message.style.color = theme.sender_text;
                } else {
                    message.style.background = theme.receiver_bubble;
                    message.style.color = theme.receiver_text;
                }
            });
        }
        
        // Open single chat theme settings
        function popupOpenSingleChatThemeSettings(userId) {
            if (!userId || userId.startsWith('group_')) {
                console.error('Cannot open theme settings: invalid user ID', userId);
                return;
            }
            
            popupActiveSingleChatUserId = userId;
            
            // Reset button states
            const previewSection = document.getElementById('popupSingleChatThemePreviewSection');
            const applyBtn = document.getElementById('popupApplySingleChatThemeBtn');
            const cancelBtn = document.getElementById('popupCancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('popupSingleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('popupSingleChatThemeModalFooter');
            
            if (previewSection) previewSection.classList.add('hidden');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
            if (defaultFooter) defaultFooter.classList.remove('hidden');
            if (modalFooter) modalFooter.classList.add('hidden');
            
            // Reset selected theme
            popupSingleChatSelectedThemeId = null;
            
            // Show modal first
            const modal = document.getElementById('popupSingleChatThemeModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            
            // Load themes if not loaded
            popupLoadSingleChatThemes().then(() => {
                popupLoadSingleChatCurrentTheme(userId).then(() => {
                    popupRenderSingleChatThemeSelection();
                });
            });
        }
        
        // Render theme selection
        function popupRenderSingleChatThemeSelection() {
            const grid = document.getElementById('popupSingleChatThemeSelectionGrid');
            if (!grid) return;
            
            if (popupSingleChatAvailableThemes.length === 0) {
                grid.innerHTML = '<div class="text-center py-12 text-gray-400"><p class="text-sm">No themes available</p></div>';
                return;
            }
            
            grid.innerHTML = popupSingleChatAvailableThemes.map(theme => {
                const isSelected = popupSingleChatSelectedThemeId === theme.id;
                const isApplied = popupSingleChatCurrentAppliedTheme === theme.id;
                
                // Create thumbnail preview
                let thumbnailStyle = '';
                if (theme.background_image) {
                    thumbnailStyle = `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};`;
                } else if (theme.background.startsWith('linear-gradient')) {
                    thumbnailStyle = `background: ${theme.background};`;
                } else {
                    thumbnailStyle = `background: ${theme.background};`;
                }
                
                return `
                    <div class="theme-option group relative cursor-pointer bg-white rounded-lg border-2 transition-all duration-200 ${isSelected ? 'border-blue-500 ring-2 ring-blue-200 shadow-lg' : 'border-gray-200 hover:border-gray-300 hover:shadow-md'} overflow-hidden" 
                         data-theme-id="${theme.id}">
                        <div class="flex items-stretch">
                            <!-- Theme Preview Thumbnail -->
                            <div class="w-24 flex-shrink-0 relative overflow-hidden" style="${thumbnailStyle}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                                <!-- Preview Bubbles -->
                                <div class="absolute bottom-2 left-0 right-0 flex items-end justify-center gap-1.5 px-2">
                                    <div class="w-8 h-5 rounded-md shadow-sm border border-white/30" style="background: ${theme.receiver_bubble};"></div>
                                    <div class="w-10 h-6 rounded-md shadow-sm border border-white/30" style="background: ${theme.sender_bubble};"></div>
                                </div>
                            </div>
                            
                            <!-- Theme Info -->
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <h6 class="text-sm font-semibold text-gray-800">${window.escapeHtml ? window.escapeHtml(theme.name) : (theme.name || '').replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m]))}</h6>
                                        ${isSelected ? `
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-white text-[10px]"></i>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed mb-2">${window.escapeHtml ? window.escapeHtml(theme.description) : (theme.description || '').replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m]))}</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <!-- Color Swatches -->
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.sender_bubble};"></div>
                                            <div class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background: ${theme.receiver_bubble};"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Applied Badge -->
                                    ${isApplied ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-full">
                                            <i class="fas fa-check-circle text-[9px]"></i>
                                            <span>Active</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Hover Effect -->
                        <div class="absolute inset-0 bg-blue-50/0 group-hover:bg-blue-50/30 transition-all duration-200 pointer-events-none"></div>
                    </div>
                `;
            }).join('');
            
            // Add click handlers
            grid.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const themeId = this.getAttribute('data-theme-id');
                    popupSelectSingleChatTheme(themeId);
                });
            });
        }
        
        // Select theme
        function popupSelectSingleChatTheme(themeId) {
            popupSingleChatSelectedThemeId = themeId;
            popupRenderSingleChatThemeSelection();
            popupShowSingleChatThemePreview(themeId);
            
            // Show apply/cancel buttons
            const previewSection = document.getElementById('popupSingleChatThemePreviewSection');
            const applyBtn = document.getElementById('popupApplySingleChatThemeBtn');
            const cancelBtn = document.getElementById('popupCancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('popupSingleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('popupSingleChatThemeModalFooter');
            
            if (previewSection) previewSection.classList.remove('hidden');
            if (applyBtn) applyBtn.classList.remove('hidden');
            if (cancelBtn) cancelBtn.classList.remove('hidden');
            if (defaultFooter) defaultFooter.classList.add('hidden');
            if (modalFooter) modalFooter.classList.remove('hidden');
        }
        
        // Show theme preview
        function popupShowSingleChatThemePreview(themeId) {
            const theme = popupSingleChatAvailableThemes.find(t => t.id === themeId);
            if (!theme) return;
            
            const previewContainer = document.getElementById('popupSingleChatThemePreviewContainer');
            if (!previewContainer) return;
            
            // Helper function to check if color is light (matches group chat preview logic)
            const isLightColor = (color) => {
                if (!color) return false;
                
                // Check for common dark/black color names
                const darkColors = ['black', '#000', '#000000', 'rgb(0,0,0)', 'rgba(0,0,0'];
                const colorLower = color.toLowerCase().trim();
                for (const darkColor of darkColors) {
                    if (colorLower.includes(darkColor)) {
                        return false; // Definitely dark
                    }
                }
                
                let r, g, b;
                if (color.startsWith('#')) {
                    let hex = color.replace('#', '');
                    // Handle 3-digit hex
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    if (hex.length === 6) {
                        r = parseInt(hex.substring(0, 2), 16);
                        g = parseInt(hex.substring(2, 4), 16);
                        b = parseInt(hex.substring(4, 6), 16);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else if (color.startsWith('rgb')) {
                    const matches = color.match(/\d+/g);
                    if (matches && matches.length >= 3) {
                        r = parseInt(matches[0]);
                        g = parseInt(matches[1]);
                        b = parseInt(matches[2]);
                        
                        // Check if it's very dark (close to black)
                        if (r < 50 && g < 50 && b < 50) {
                            return false; // Very dark, use white text
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                
                // YIQ formula: brightness threshold lowered to 150 (matches backend)
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq > 150; // Lower threshold to catch more dark colors
            };
            
            const senderTextColor = theme.sender_text || (isLightColor(theme.sender_bubble) ? '#1f2937' : '#ffffff');
            const receiverTextColor = theme.receiver_text || (isLightColor(theme.receiver_bubble) ? '#1f2937' : '#ffffff');
            
            // Create full preview matching group chat format
            previewContainer.innerHTML = `
                <!-- Chat Header Preview -->
                <div class="border-b rounded-t-lg bg-white">
                    <div class="px-3 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold truncate text-gray-800">Chat Preview</h3>
                                <p class="text-xs truncate text-gray-500">Single chat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-1.5 rounded-full hover:bg-gray-100 transition text-gray-600">
                                <i class="fas fa-cog text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area Preview -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3 min-h-[200px] max-h-[250px]" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`}">
                    <!-- Received Message -->
                    <div class="flex items-start space-x-2">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs mb-1" style="color: ${receiverTextColor}; opacity: 0.7;">John Doe</div>
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.receiver_bubble}; color: ${receiverTextColor};">
                                <p class="text-xs">Hey! How are you doing?</p>
                            </div>
                            <div class="text-[10px] mt-1" style="color: ${receiverTextColor}; opacity: 0.5;">10:30 AM</div>
                        </div>
                    </div>
                    
                    <!-- Sent Message -->
                    <div class="flex items-start space-x-2 justify-end">
                        <div class="flex-1 flex justify-end">
                            <div class="rounded-lg p-2 max-w-[75%] shadow-sm" style="background: ${theme.sender_bubble}; color: ${senderTextColor};">
                                <p class="text-xs">I'm doing great, thanks!</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Input Area Preview -->
                <div class="border-t rounded-b-lg p-2" style="${theme.background_image ? `background-image: url(${theme.background_image}); background-size: cover; background-position: center; background-color: ${theme.background};` : `background: ${theme.background};`} border-color: ${theme.receiver_bubble};">
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition" style="color: ${theme.accent_color || '#6b7280'};">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <div class="flex-1 rounded-lg border px-3 py-2" style="background: white; border-color: ${theme.receiver_bubble};">
                            <input type="text" placeholder="Type a message..." class="w-full text-xs outline-none" style="color: #1f2937;" disabled>
                        </div>
                        <button class="p-2 rounded-lg transition" style="background: ${theme.accent_color || '#3b82f6'}; color: #ffffff;">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Apply single chat theme
        async function popupApplySingleChatTheme() {
            if (!popupSingleChatSelectedThemeId || !popupActiveSingleChatUserId) {
                console.error('Cannot apply theme: missing theme ID or user ID', {
                    themeId: popupSingleChatSelectedThemeId,
                    userId: popupActiveSingleChatUserId
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a theme first.'
                });
                return;
            }
            
            try {
                const url = `{{ route("messages.conversation.theme.apply", ["otherUserId" => ":userId"]) }}`.replace(':userId', popupActiveSingleChatUserId);
                console.log('Applying theme:', { themeId: popupSingleChatSelectedThemeId, userId: popupActiveSingleChatUserId, url });
                
                const response = await axios.post(url, {
                    theme: popupSingleChatSelectedThemeId
                });
                
                if (response.data.success) {
                    popupSingleChatCurrentAppliedTheme = popupSingleChatSelectedThemeId;
                    
                    // Apply theme to chat immediately
                    const chatElement = document.querySelector(`[data-user-id="${popupActiveSingleChatUserId}"]`);
                    if (chatElement) {
                        popupApplySingleChatThemeToChat(popupSingleChatSelectedThemeId, chatElement);
                    }
                    
                    // Hide preview and buttons
                    const previewSection = document.getElementById('popupSingleChatThemePreviewSection');
                    const applyBtn = document.getElementById('popupApplySingleChatThemeBtn');
                    const cancelBtn = document.getElementById('popupCancelSingleChatThemeBtn');
                    const defaultFooter = document.getElementById('popupSingleChatThemeDefaultFooter');
                    const modalFooter = document.getElementById('popupSingleChatThemeModalFooter');
                    
                    if (previewSection) previewSection.classList.add('hidden');
                    if (applyBtn) applyBtn.classList.add('hidden');
                    if (cancelBtn) cancelBtn.classList.add('hidden');
                    if (defaultFooter) defaultFooter.classList.remove('hidden');
                    if (modalFooter) modalFooter.classList.add('hidden');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Theme Applied',
                        text: 'The chat theme has been updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Close modal
                    document.getElementById('popupSingleChatThemeModal').classList.add('hidden');
                }
            } catch (error) {
                console.error('Error applying theme:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to apply theme.'
                });
            }
        }
        
        // Cancel theme selection
        function popupCancelSingleChatThemeSelection() {
            popupSingleChatSelectedThemeId = null;
            const previewSection = document.getElementById('popupSingleChatThemePreviewSection');
            const applyBtn = document.getElementById('popupApplySingleChatThemeBtn');
            const cancelBtn = document.getElementById('popupCancelSingleChatThemeBtn');
            const defaultFooter = document.getElementById('popupSingleChatThemeDefaultFooter');
            const modalFooter = document.getElementById('popupSingleChatThemeModalFooter');
            
            if (previewSection) previewSection.classList.add('hidden');
            if (applyBtn) applyBtn.classList.add('hidden');
            if (cancelBtn) cancelBtn.classList.add('hidden');
            if (defaultFooter) defaultFooter.classList.remove('hidden');
            if (modalFooter) modalFooter.classList.add('hidden');
            
            popupRenderSingleChatThemeSelection();
        }
        
        // Expose single chat theme functions
        window.popupOpenSingleChatThemeSettings = popupOpenSingleChatThemeSettings;
        window.popupLoadSingleChatTheme = popupLoadSingleChatTheme;
        window.popupApplySingleChatThemeToChat = popupApplySingleChatThemeToChat;
        window.popupUpdateSingleChatMessageBubblesTheme = popupUpdateSingleChatMessageBubblesTheme;
        window.popupApplySingleChatTheme = popupApplySingleChatTheme;
        window.popupCancelSingleChatThemeSelection = popupCancelSingleChatThemeSelection;
        window.popupRenderSingleChatThemeSelection = popupRenderSingleChatThemeSelection;
        window.popupSelectSingleChatTheme = popupSelectSingleChatTheme;
        
        // Helper function for escaping HTML (if not already defined)
        if (typeof window.escapeHtml === 'undefined') {
            window.escapeHtml = function(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
            };
        }
        // Note: popupAddSelectedMembersToGroup, popupOpenUserSelectionModal, and popupResetUserSelectionModal
        // are defined outside the IIFE and will be exposed separately
        
        // Expose variables via getters/setters
        Object.defineProperty(window, 'popupCurrentGroupId', {
            get: function() { return popupCurrentGroupId; },
            set: function(value) { popupCurrentGroupId = value; }
        });
        Object.defineProperty(window, 'popupCurrentGroupData', {
            get: function() { return popupCurrentGroupData; },
            set: function(value) { popupCurrentGroupData = value; }
        });
        Object.defineProperty(window, 'popupAddingMembersToGroup', {
            get: function() { return popupAddingMembersToGroup; },
            set: function(value) { popupAddingMembersToGroup = value; }
        });
    })();
</script>

<!-- Group Settings Modal for Popup -->
<div id="popupGroupSettingsModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center backdrop-blur-sm" style="background: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl max-h-[92vh] flex flex-col border border-gray-200 overflow-hidden animate-fade-in">
        <!-- Header with brand color -->
        <div class="relative p-5 flex items-center justify-between" style="background: #055498;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog text-white text-base"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Group Settings</h3>
            </div>
            <button id="popupCloseGroupSettingsModal" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition-colors duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 bg-white">
            <div class="flex">
                <button class="popup-group-settings-tab px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200" data-tab="info" style="border-color: #055498; color: #055498;">
                    <i class="fas fa-info-circle mr-2"></i>Info
                </button>
                <button class="popup-group-settings-tab px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 text-gray-500 border-transparent hover:text-gray-700" data-tab="members">
                    <i class="fas fa-users mr-2"></i>Members
                </button>
                <button class="popup-group-settings-tab px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 text-gray-500 border-transparent hover:text-gray-700" data-tab="theme">
                    <i class="fas fa-palette mr-2"></i>Theme
                </button>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Info Tab Content -->
            <div id="popupGroupSettingsInfoTab" class="popup-tab-content">
                <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-1 h-5 rounded-full" style="background: #055498;"></div>
                        <h4 class="text-sm font-semibold text-gray-800">Group Information</h4>
                    </div>
                    
                    <!-- Group Avatar -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Group Profile Image</label>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img id="popupGroupSettingsAvatarPreview" src="" alt="Group Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 shadow-md">
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full flex items-center justify-center shadow-md border-2 border-white" style="background: #055498;">
                                    <i class="fas fa-users text-white text-[10px]"></i>
                                </div>
                                <input type="file" id="popupGroupAvatarInput" accept="image/*" class="hidden">
                            </div>
                            <div class="flex gap-2">
                                <button id="popupChangeGroupAvatarBtn" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center gap-2" style="background: #055498;">
                                    <i class="fas fa-image text-xs"></i>
                                    <span>Change</span>
                                </button>
                                <button id="popupRemoveGroupAvatarBtn" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Group Name -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
                        <input type="text" id="popupGroupSettingsNameInput" placeholder="Enter group name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 outline-none transition-all duration-200 placeholder:text-gray-400 focus:border-055498 focus:ring-1" style="focus:border-color: #055498; focus:ring-color: #055498;">
                    </div>
                    
                    <!-- Group Description -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="popupGroupSettingsDescriptionInput" placeholder="Enter group description..." rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 outline-none resize-none transition-all duration-200 placeholder:text-gray-400 focus:border-055498 focus:ring-1" style="focus:border-color: #055498; focus:ring-color: #055498;"></textarea>
                    </div>
                    
                    <button id="popupSaveGroupInfoBtn" class="w-full px-4 py-2.5 text-white font-semibold rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center justify-center gap-2" style="background: #055498;">
                        <i class="fas fa-save text-sm"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
            
            <!-- Members Tab Content -->
            <div id="popupGroupSettingsMembersTab" class="popup-tab-content hidden">
                <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5 rounded-full" style="background: #055498;"></div>
                            <h4 class="text-sm font-semibold text-gray-800">Members</h4>
                            <span id="popupMemberCountBadge" class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background: rgba(5, 84, 152, 0.1); color: #055498;">0</span>
                        </div>
                        <button id="popupAddMembersBtn" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center gap-2" style="background: #055498;">
                            <i class="fas fa-user-plus text-xs"></i>
                            <span>Add Members</span>
                        </button>
                    </div>
                    <div id="popupGroupMembersList" class="space-y-2">
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                            <p class="text-sm">Loading members...</p>
                        </div>
                    </div>
                </div>
            </div>
            
                <!-- Theme Tab Content -->
                <div id="popupGroupSettingsThemeTab" class="popup-tab-content hidden">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Header Section -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                                    <i class="fas fa-palette text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-white">Chat Theme</h4>
                                    <p class="text-xs text-white/80 mt-0.5">Customize the visual appearance of this group chat</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-6">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Select a theme to personalize this group chat. Changes apply to all members and persist across sessions. Only group admins can modify themes.
                                </p>
                            </div>
                            
                            <!-- Two Column Layout: Theme Selection and Preview -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Left Column: Theme Selection -->
                                <div class="lg:border-r lg:border-gray-200 lg:pr-6">
                                    <div class="mb-4">
                                        <h5 class="text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
                                            <i class="fas fa-palette text-sm text-blue-600"></i>
                                            Available Themes
                                        </h5>
                                        <p class="text-xs text-gray-500">Choose a theme to customize your group chat</p>
                                    </div>
                                    <div id="popupThemeSelectionGrid" class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                                        <div class="text-center py-12 text-gray-400">
                                            <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                                            <p class="text-sm">Loading themes...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column: Theme Preview -->
                                <div class="lg:pl-6">
                                    <div id="popupThemePreviewSection" class="p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-inner min-h-[400px]">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-eye text-blue-500"></i>
                                                <span class="text-sm font-semibold text-gray-700">Live Preview</span>
                                            </div>
                                        </div>
                                        <div id="popupThemePreviewContainer" class="space-y-3 bg-white rounded-lg p-4 shadow-sm min-h-[350px]">
                                            <div class="text-center py-16 text-gray-400">
                                                <i class="fas fa-mouse-pointer text-3xl mb-3"></i>
                                                <p class="text-sm font-medium">Select a theme to see preview</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        
        <div class="p-4 bg-white border-t border-gray-200">
            <!-- Default Footer (Info and Members tabs) -->
            <div id="popupDefaultModalFooter" class="flex justify-end">
                <button id="popupCloseGroupSettingsBtn" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
            
            <!-- Theme Tab Footer -->
            <div id="popupThemeModalFooter" class="hidden flex gap-3">
                <button id="popupApplyThemeBtn" class="flex-1 px-5 py-3 text-white font-semibold rounded-lg transition-all duration-200 hover:opacity-90 hover:shadow-lg flex items-center justify-center gap-2 hidden" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-check-circle text-base"></i>
                    <span>Apply Theme</span>
                </button>
                <button id="popupCancelThemeBtn" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 hidden border border-gray-300">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Chat Theme Modal for Popup -->
<div id="popupSingleChatThemeModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center backdrop-blur-sm p-2 sm:p-4" style="background: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl max-h-[95vh] sm:max-h-[92vh] flex flex-col border border-gray-200 overflow-hidden animate-fade-in">
        <!-- Header with brand color -->
        <div class="relative p-3 sm:p-5 flex items-center justify-between" style="background: #055498;">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-palette text-white text-sm sm:text-base"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-white">Chat Theme</h3>
            </div>
            <button id="popupCloseSingleChatThemeModal" class="w-8 h-8 min-w-[32px] min-h-[32px] bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition-colors duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 bg-gray-50">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header Section -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="fas fa-palette text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white">Chat Theme</h4>
                            <p class="text-xs text-white/80 mt-0.5">Customize the visual appearance of this conversation</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Select a theme to personalize this conversation. Changes apply to this chat and persist across sessions.
                        </p>
                    </div>
                    
                    <!-- Two Column Layout: Theme Selection and Preview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Left Column: Theme Selection -->
                        <div class="lg:border-r lg:border-gray-200 lg:pr-4 xl:pr-6">
                            <div class="mb-4">
                                <h5 class="text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
                                    <i class="fas fa-palette text-sm text-blue-600"></i>
                                    Available Themes
                                </h5>
                                <p class="text-xs text-gray-500">Choose a theme to customize your conversation</p>
                            </div>
                            <div id="popupSingleChatThemeSelectionGrid" class="space-y-3 max-h-[300px] sm:max-h-[400px] lg:max-h-[500px] overflow-y-auto pr-2 custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                                <div class="text-center py-12 text-gray-400">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                                    <p class="text-sm">Loading themes...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Theme Preview -->
                        <div class="lg:pl-4 xl:pl-6">
                            <div id="popupSingleChatThemePreviewSection" class="p-3 sm:p-4 md:p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-inner min-h-[250px] sm:min-h-[300px] lg:min-h-[400px]">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-eye text-blue-500 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700">Live Preview</span>
                                    </div>
                                </div>
                                <div id="popupSingleChatThemePreviewContainer" class="space-y-3 bg-white rounded-lg p-3 sm:p-4 shadow-sm min-h-[200px] sm:min-h-[250px] lg:min-h-[350px]">
                                    <div class="text-center py-16 text-gray-400">
                                        <i class="fas fa-mouse-pointer text-3xl mb-3"></i>
                                        <p class="text-sm font-medium">Select a theme to see preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-3 sm:p-4 bg-white border-t border-gray-200">
            <!-- Default Footer -->
            <div id="popupSingleChatThemeDefaultFooter" class="flex justify-end">
                <button id="popupCloseSingleChatThemeBtn" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 min-h-[44px]">
                    Close
                </button>
            </div>
            
            <!-- Theme Selection Footer -->
            <div id="popupSingleChatThemeModalFooter" class="hidden flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button id="popupApplySingleChatThemeBtn" class="flex-1 px-4 sm:px-5 py-2.5 sm:py-3 text-white text-sm sm:text-base font-semibold rounded-lg transition-all duration-200 hover:opacity-90 hover:shadow-lg flex items-center justify-center gap-2 hidden min-h-[44px]" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-check-circle text-sm sm:text-base"></i>
                    <span>Apply Theme</span>
                </button>
                <button id="popupCancelSingleChatThemeBtn" class="w-full sm:w-auto px-4 sm:px-5 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-medium rounded-lg transition-colors duration-200 hidden border border-gray-300 min-h-[44px]">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Selection Modal for Popup (for adding members) -->
<div id="popupUserSelectionModal" class="hidden fixed inset-0 z-[160] flex items-center justify-center bg-opacity-20 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Select User(s)</h3>
            <button id="popupCloseUserModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 border-b border-gray-200">
            <input type="text" id="popupUserSearchInput" placeholder="Search users..." class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
        </div>
        <div class="p-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="popupSelectAllUsers" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-600 font-medium">Select All</span>
                    </label>
                </div>
                <span class="text-sm text-gray-600">
                    <span id="popupSelectedCount">0</span> selected
                </span>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button id="popupAddSelectedMembersBtn" class="hidden px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    <i class="fas fa-user-plus mr-2"></i>Add Selected Members
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto" id="popupUsersListContainer">
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Loading users...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Group Settings Event Listeners for Popup
    // Settings button click handlers (using event delegation for dynamically added buttons)
    // Set up outside DOMContentLoaded to ensure it's available immediately
    document.addEventListener('click', function(e) {
        // Check if the clicked element is the button or the icon inside it
        const settingsBtn = e.target.closest('.popup-group-settings-btn');
        if (settingsBtn && !settingsBtn.classList.contains('hidden')) {
            e.preventDefault();
            e.stopPropagation();
            const userId = settingsBtn.getAttribute('data-user-id');
            if (userId) {
                if (window.popupOpenGroupSettings) {
                    window.popupOpenGroupSettings(userId);
                } else {
                    console.error('popupOpenGroupSettings function not available');
                }
            } else {
                console.error('No userId found on settings button');
            }
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Close group settings modal
        const popupCloseGroupSettingsModal = document.getElementById('popupCloseGroupSettingsModal');
        const popupCloseGroupSettingsBtn = document.getElementById('popupCloseGroupSettingsBtn');
        if (popupCloseGroupSettingsModal) {
            popupCloseGroupSettingsModal.addEventListener('click', function() {
                document.getElementById('popupGroupSettingsModal').classList.add('hidden');
            });
        }
        if (popupCloseGroupSettingsBtn) {
            popupCloseGroupSettingsBtn.addEventListener('click', function() {
                document.getElementById('popupGroupSettingsModal').classList.add('hidden');
            });
        }
        
        // Single chat theme modal listeners
        const popupCloseSingleChatThemeModal = document.getElementById('popupCloseSingleChatThemeModal');
        const popupCloseSingleChatThemeBtn = document.getElementById('popupCloseSingleChatThemeBtn');
        if (popupCloseSingleChatThemeModal) {
            popupCloseSingleChatThemeModal.addEventListener('click', function() {
                document.getElementById('popupSingleChatThemeModal').classList.add('hidden');
                if (window.popupCancelSingleChatThemeSelection) {
                    window.popupCancelSingleChatThemeSelection();
                }
            });
        }
        if (popupCloseSingleChatThemeBtn) {
            popupCloseSingleChatThemeBtn.addEventListener('click', function() {
                document.getElementById('popupSingleChatThemeModal').classList.add('hidden');
                if (window.popupCancelSingleChatThemeSelection) {
                    window.popupCancelSingleChatThemeSelection();
                }
            });
        }
        
        // Use event delegation for popup single chat theme buttons (in case they're dynamically shown/hidden)
        document.addEventListener('click', function(e) {
            if (e.target.closest('#popupApplySingleChatThemeBtn')) {
                e.preventDefault();
                e.stopPropagation();
                if (window.popupApplySingleChatTheme) {
                    window.popupApplySingleChatTheme();
                } else {
                    console.error('popupApplySingleChatTheme function not available');
                }
            }
            if (e.target.closest('#popupCancelSingleChatThemeBtn')) {
                e.preventDefault();
                e.stopPropagation();
                if (window.popupCancelSingleChatThemeSelection) {
                    window.popupCancelSingleChatThemeSelection();
                } else {
                    console.error('popupCancelSingleChatThemeSelection function not available');
                }
            }
        });
        
        // Also attach directly for compatibility
        const popupApplySingleChatThemeBtn = document.getElementById('popupApplySingleChatThemeBtn');
        if (popupApplySingleChatThemeBtn) {
            popupApplySingleChatThemeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.popupApplySingleChatTheme) {
                    window.popupApplySingleChatTheme();
                }
            });
        }
        
        const popupCancelSingleChatThemeBtn = document.getElementById('popupCancelSingleChatThemeBtn');
        if (popupCancelSingleChatThemeBtn) {
            popupCancelSingleChatThemeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.popupCancelSingleChatThemeSelection) {
                    window.popupCancelSingleChatThemeSelection();
                }
            });
        }
        
        // Single chat settings button click handlers (delegated)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.popup-single-chat-settings-btn')) {
                const btn = e.target.closest('.popup-single-chat-settings-btn');
                const userId = btn.getAttribute('data-user-id');
                if (userId && !userId.startsWith('group_')) {
                    if (window.popupOpenSingleChatThemeSettings) {
                        window.popupOpenSingleChatThemeSettings(userId);
                    }
                }
            }
        });
        
        // Save group info
        const popupSaveGroupInfoBtn = document.getElementById('popupSaveGroupInfoBtn');
        if (popupSaveGroupInfoBtn) {
            popupSaveGroupInfoBtn.addEventListener('click', function() {
                if (window.popupSaveGroupInfo) {
                    window.popupSaveGroupInfo();
                }
            });
        }
        
        // Tab switching
        document.querySelectorAll('.popup-group-settings-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                popupSwitchGroupSettingsTab(tabName);
            });
        });
        
        // Theme buttons
        const popupApplyThemeBtn = document.getElementById('popupApplyThemeBtn');
        if (popupApplyThemeBtn) {
            popupApplyThemeBtn.addEventListener('click', popupApplyTheme);
        }
        
        const popupCancelThemeBtn = document.getElementById('popupCancelThemeBtn');
        if (popupCancelThemeBtn) {
            popupCancelThemeBtn.addEventListener('click', popupCancelThemeSelection);
        }
        
        const popupCloseThemePreviewBtn = document.getElementById('popupCloseThemePreviewBtn');
        if (popupCloseThemePreviewBtn) {
            popupCloseThemePreviewBtn.addEventListener('click', function() {
                const previewSection = document.getElementById('popupThemePreviewSection');
                if (previewSection) previewSection.classList.add('hidden');
            });
        }
        
        // Add members button
        const popupAddMembersBtn = document.getElementById('popupAddMembersBtn');
        if (popupAddMembersBtn) {
            popupAddMembersBtn.addEventListener('click', function() {
                if (!window.popupCurrentGroupId || !window.popupCurrentGroupData) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Group data not loaded. Please try again.'
                        });
                    }
                    return;
                }
                // Open user selection modal for adding members
                window.popupAddingMembersToGroup = true;
                if (window.popupOpenUserSelectionModal) {
                    window.popupOpenUserSelectionModal();
                }
            });
        }
        
        // Avatar change/remove handlers
        const popupChangeGroupAvatarBtn = document.getElementById('popupChangeGroupAvatarBtn');
        const popupRemoveGroupAvatarBtn = document.getElementById('popupRemoveGroupAvatarBtn');
        const popupGroupAvatarInput = document.getElementById('popupGroupAvatarInput');
        
        if (popupChangeGroupAvatarBtn && popupGroupAvatarInput) {
            popupChangeGroupAvatarBtn.addEventListener('click', function() {
                popupGroupAvatarInput.click();
            });
        }
        
        if (popupGroupAvatarInput) {
            popupGroupAvatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('avatar', file);
                    formData.append('_method', 'PUT');
                    
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Uploading...',
                            text: 'Please wait while we upload the avatar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                    
                    axios.post(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.popupCurrentGroupId), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Uploaded!',
                                    text: 'Avatar updated successfully!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                            
                            if (response.data.group) {
                                window.popupCurrentGroupData = response.data.group;
                                const avatarPreview = document.getElementById('popupGroupSettingsAvatarPreview');
                                if (avatarPreview && response.data.group.avatar) {
                                    avatarPreview.src = response.data.group.avatar;
                                }
                                
                                // Update popup header avatar
                                if (popupActiveGroupChatUserId) {
                                    const chatElement = document.querySelector(`[data-user-id="${popupActiveGroupChatUserId}"]`);
                                    if (chatElement) {
                                        const avatarEl = chatElement.querySelector('.chat-avatar');
                                        if (avatarEl && response.data.group.avatar) {
                                            if (avatarEl.tagName === 'IMG') {
                                                avatarEl.src = response.data.group.avatar;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if (window.popupLoadGroupDetails) {
                                window.popupLoadGroupDetails(window.popupCurrentGroupId, popupActiveGroupChatUserId);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading avatar:', error);
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.response?.data?.message || 'Failed to upload avatar.'
                            });
                        }
                    });
                }
            });
        }
        
        if (popupRemoveGroupAvatarBtn) {
            popupRemoveGroupAvatarBtn.addEventListener('click', function() {
                if (!window.popupCurrentGroupId) return;
                
                if (window.Swal) {
                    Swal.fire({
                        title: 'Remove Avatar?',
                        text: 'Are you sure you want to remove the group avatar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('remove_avatar', '1');
                            formData.append('_method', 'PUT');
                            
                            axios.post(`{{ route('messages.groups.update', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.popupCurrentGroupId), formData)
                            .then(response => {
                                if (response.data.success) {
                                    if (window.Swal) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Removed!',
                                            text: 'Avatar removed successfully!',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    }
                                    
                                    if (response.data.group) {
                                        window.popupCurrentGroupData = response.data.group;
                                        const avatarPreview = document.getElementById('popupGroupSettingsAvatarPreview');
                                        if (avatarPreview) {
                                            const name = window.popupCurrentGroupData.name || 'Group';
                                            avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                                        }
                                        
                                        // Update popup header avatar
                                        if (popupActiveGroupChatUserId) {
                                            const chatElement = document.querySelector(`[data-user-id="${popupActiveGroupChatUserId}"]`);
                                            if (chatElement) {
                                                const avatarEl = chatElement.querySelector('.chat-avatar');
                                                if (avatarEl) {
                                                    const name = window.popupCurrentGroupData.name || 'Group';
                                                    if (avatarEl.tagName === 'IMG') {
                                                        avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=80&background=055498&color=fff`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    if (window.popupLoadGroupDetails) {
                                        window.popupLoadGroupDetails(window.popupCurrentGroupId, popupActiveGroupChatUserId);
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error removing avatar:', error);
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: error.response?.data?.message || 'Failed to remove avatar.'
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }
    });
    
    // User Selection Modal Functions for Popup
    let popupSelectedUsers = [];
    let popupAllUsers = [];
    // Note: popupAddingMembersToGroup is managed via window.popupAddingMembersToGroup (getter/setter from IIFE)
    
    function popupOpenUserSelectionModal() {
        const modal = document.getElementById('popupUserSelectionModal');
        if (!modal) return;
        
        // Don't reset the flag if we're adding members to a group
        const wasAddingToGroup = window.popupAddingMembersToGroup === true;
        
        // Reset UI state but preserve the flag
        popupSelectedUsers = [];
        const modalEl = document.getElementById('popupUserSelectionModal');
        if (modalEl) {
            const checkboxes = modalEl.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('popupSelectAllUsers');
            if (selectAll) selectAll.checked = false;
            const searchInput = document.getElementById('popupUserSearchInput');
            if (searchInput) searchInput.value = '';
        }
        
        // Restore the flag if it was set
        if (wasAddingToGroup) {
            window.popupAddingMembersToGroup = true;
        }
        
        // Fetch users
        fetch('{{ route("messages.users") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.users) {
                popupAllUsers = data.users;
                popupRenderUsersForSelection();
                // Update count after rendering to show/hide button
                setTimeout(() => {
                    popupUpdateSelectedCount();
                }, 100);
            }
        })
        .catch(error => {
            console.error('Error fetching users:', error);
        });
        
        modal.classList.remove('hidden');
    }
    
    function popupResetUserSelectionModal() {
        popupSelectedUsers = [];
        // Only reset the flag if we're not actively adding members
        // This allows the flag to persist when opening the modal for adding members
        if (window.popupAddingMembersToGroup !== true) {
            window.popupAddingMembersToGroup = false;
        }
        const modal = document.getElementById('popupUserSelectionModal');
        if (modal) {
            const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('popupSelectAllUsers');
            if (selectAll) selectAll.checked = false;
            const searchInput = document.getElementById('popupUserSearchInput');
            if (searchInput) searchInput.value = '';
        }
        popupUpdateSelectedCount();
    }
    
    function popupRenderUsersForSelection() {
        const container = document.getElementById('popupUsersListContainer');
        if (!container) return;
        
        // Filter out existing group members if adding to group
        let usersToShow = popupAllUsers;
        if (window.popupAddingMembersToGroup && window.popupCurrentGroupData && window.popupCurrentGroupData.members) {
            const memberIds = window.popupCurrentGroupData.members.map(m => m.id);
            usersToShow = popupAllUsers.filter(u => !memberIds.includes(u.id));
        }
        
        if (usersToShow.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-gray-500"><p class="text-sm">No users available</p></div>';
            return;
        }
        
        container.innerHTML = usersToShow.map(user => {
            const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
            const isSelected = popupSelectedUsers.includes(user.id);
            return `
                <div class="p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="${user.id}" ${isSelected ? 'checked' : ''}>
                        <img src="${user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&size=40&background=055498&color=fff'}" 
                             alt="${fullName}" 
                             class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">${window.escapeHtml ? window.escapeHtml(fullName) : fullName}</p>
                            ${user.privilege ? `<p class="text-xs text-gray-500">${window.escapeHtml ? window.escapeHtml(user.privilege) : user.privilege}</p>` : ''}
                        </div>
                    </label>
                </div>
            `;
        }).join('');
        
        // Attach checkbox change handlers
        container.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const userId = this.value;
                if (this.checked) {
                    if (!popupSelectedUsers.includes(userId)) {
                        popupSelectedUsers.push(userId);
                    }
                } else {
                    popupSelectedUsers = popupSelectedUsers.filter(id => id !== userId);
                }
                popupUpdateSelectedCount();
            });
        });
        
        // Select all handler - remove old listener first to avoid duplicates
        const selectAll = document.getElementById('popupSelectAllUsers');
        if (selectAll) {
            // Clone and replace to remove old listeners
            const newSelectAll = selectAll.cloneNode(true);
            selectAll.parentNode.replaceChild(newSelectAll, selectAll);
            
            newSelectAll.addEventListener('change', function() {
                const checkboxes = container.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    const userId = cb.value;
                    if (this.checked) {
                        if (!popupSelectedUsers.includes(userId)) {
                            popupSelectedUsers.push(userId);
                        }
                    } else {
                        popupSelectedUsers = popupSelectedUsers.filter(id => id !== userId);
                    }
                });
                popupUpdateSelectedCount();
            });
        }
        
        // Search handler
        const searchInput = document.getElementById('popupUserSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const userItems = container.querySelectorAll('.p-3');
                userItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    }
    
    function popupUpdateSelectedCount() {
        const countEl = document.getElementById('popupSelectedCount');
        if (countEl) {
            countEl.textContent = popupSelectedUsers.length;
        }
        
        const addBtn = document.getElementById('popupAddSelectedMembersBtn');
        if (addBtn) {
            // Check if we're adding members to a group and at least one user is selected
            const isAddingToGroup = window.popupAddingMembersToGroup === true;
            const hasSelectedUsers = popupSelectedUsers.length >= 1;
            
            // Debug: log the state
            if (isAddingToGroup) {
                console.log('popupUpdateSelectedCount - isAddingToGroup:', isAddingToGroup, 'hasSelectedUsers:', hasSelectedUsers, 'count:', popupSelectedUsers.length);
            }
            
            if (isAddingToGroup && hasSelectedUsers) {
                addBtn.classList.remove('hidden');
            } else {
                addBtn.classList.add('hidden');
            }
        }
    }
    
    // Add selected members to group
    function popupAddSelectedMembersToGroup() {
        if (!window.popupCurrentGroupId || popupSelectedUsers.length === 0) return;
        
        if (window.Swal) {
            Swal.fire({
                title: 'Adding members...',
                text: 'Please wait.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        axios.post(`{{ route('messages.groups.members.add', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', window.popupCurrentGroupId), {
            user_ids: popupSelectedUsers
        })
        .then(response => {
            if (response.data.success) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: 'Members added successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
                
                // Close user selection modal
                document.getElementById('popupUserSelectionModal').classList.add('hidden');
                if (window.popupResetUserSelectionModal) {
                    window.popupResetUserSelectionModal();
                }
                
                // Reload group details and refresh settings modal
                if (window.popupLoadGroupDetails && window.popupPopulateGroupSettingsModal) {
                    // Get the active group chat userId from the popupCurrentGroupData or use a stored reference
                    const activeGroupUserId = window.popupCurrentGroupData ? `group_${window.popupCurrentGroupId}` : null;
                    if (activeGroupUserId) {
                        window.popupLoadGroupDetails(window.popupCurrentGroupId, activeGroupUserId).then(() => {
                            window.popupPopulateGroupSettingsModal();
                        });
                    } else {
                        // Fallback: just refresh the modal
                        window.popupPopulateGroupSettingsModal();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error adding members:', error);
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to add members.'
                });
            }
        });
    }
    
    // Expose user selection modal functions to global scope
    window.popupOpenUserSelectionModal = popupOpenUserSelectionModal;
    window.popupResetUserSelectionModal = popupResetUserSelectionModal;
    window.popupAddSelectedMembersToGroup = popupAddSelectedMembersToGroup;
    
    // User selection modal event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const popupCloseUserModal = document.getElementById('popupCloseUserModal');
        if (popupCloseUserModal) {
            popupCloseUserModal.addEventListener('click', function() {
                const modal = document.getElementById('popupUserSelectionModal');
                if (modal) {
                    modal.classList.add('hidden');
                    if (window.popupAddingMembersToGroup) {
                        // Reopen group settings modal
                        document.getElementById('popupGroupSettingsModal').classList.remove('hidden');
                    }
                    if (window.popupResetUserSelectionModal) {
                        window.popupResetUserSelectionModal();
                    }
                }
            });
        }
        
        // Close on outside click
        const popupUserSelectionModal = document.getElementById('popupUserSelectionModal');
        if (popupUserSelectionModal) {
            popupUserSelectionModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    if (window.popupAddingMembersToGroup) {
                        document.getElementById('popupGroupSettingsModal').classList.remove('hidden');
                    }
                    if (window.popupResetUserSelectionModal) {
                        window.popupResetUserSelectionModal();
                    }
                }
            });
        }
        
        const popupAddSelectedMembersBtn = document.getElementById('popupAddSelectedMembersBtn');
        if (popupAddSelectedMembersBtn) {
            popupAddSelectedMembersBtn.addEventListener('click', function() {
                if (window.popupAddSelectedMembersToGroup) {
                    window.popupAddSelectedMembersToGroup();
                }
            });
        }
    });
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


    /* Always show action buttons on all devices */
    .message-react-btn,
    .message-reply-btn,
    .message-delete-btn {
        opacity: 1 !important;
        visibility: visible !important;
    }
    /* Show action buttons container - always visible */
    [class*="flex"][class*="items-center"][class*="gap"] {
        opacity: 1 !important;
        visibility: visible !important;
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
    
    /* Prevent horizontal overflow in chat messages */
    .chat-messages {
        overflow-x: hidden !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .chat-messages * {
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .chat-messages video {
        max-width: 100% !important;
        height: auto !important;
    }
    
    .chat-messages .max-w-\[75\%\] {
        max-width: 75% !important;
    }

    @media (max-width: 640px) {
        #messagesPopupContainer {
            right: 0.5rem;
            left: 0.5rem;
        }
        /* Fix chat form on mobile - ensure send button is visible */
        .chat-form {
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
            flex-wrap: nowrap !important;
        }
        .chat-form > * {
            flex-shrink: 0 !important;
        }
        .chat-input {
            min-width: 0 !important;
            flex: 1 1 0% !important;
            max-width: 100% !important;
        }
        .chat-form button[type="submit"] {
            flex-shrink: 0 !important;
            visibility: visible !important;
            display: flex !important;
        }
        .chat-attach-btn,
        .chat-voice-btn,
        .chat-emoji-btn {
            flex-shrink: 0 !important;
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
    
    /* Force light mode for timestamp separator */
    #messagesPopupContainer .flex.items-center.justify-center.my-4 > div {
        background-color: #e5e7eb !important;
    }
    
    #messagesPopupContainer .flex.items-center.justify-center.my-4 span {
        color: #4b5563 !important;
    }
    
    /* Group Settings Modal Styles */
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.98) translateY(-5px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
    #popupGroupSettingsNameInput:focus,
    #popupGroupSettingsDescriptionInput:focus {
        border-color: #055498 !important;
        box-shadow: 0 0 0 1px rgba(5, 84, 152, 0.2) !important;
    }
    
    /* SweetAlert2 button text color - white */
    .swal2-confirm,
    .swal2-cancel,
    .swal2-deny {
        color: white !important;
    }
    
    .swal2-confirm {
        background-color: #055498 !important;
    }
    
    .swal2-confirm:hover {
        background-color: #123a60 !important;
    }
    
    .swal2-cancel {
        background-color: #6b7280 !important;
    }
    
    .swal2-cancel:hover {
        background-color: #4b5563 !important;
    }
    
    .swal2-deny {
        background-color: #CE2028 !important;
    }
    
    .swal2-deny:hover {
        background-color: #a01a20 !important;
    }
</style>
