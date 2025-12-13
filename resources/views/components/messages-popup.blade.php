<!-- Messages Popup Container - Multiple Chats Support -->
<div id="messagesPopupContainer" class="fixed bottom-0 right-4 z-[100] flex items-end gap-2" style="max-width: calc(100vw - 2rem);">
    <!-- Chats will be dynamically added here -->
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
                    <div class="chat-expanded w-96 bg-white dark:bg-gray-800 rounded-t-xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col" style="max-height: 600px; height: 500px;">
                        <!-- Popup Header -->
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cursor-move">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs chat-avatar">
                                    ${user.initials}
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white chat-name">${user.name}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 chat-status">${user.status}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="chat-minimize-btn p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <button class="chat-close-btn p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900/30 chat-messages" style="max-height: 400px;">
                            <!-- Received Message -->
                            <div class="flex items-start space-x-2">
                                <div class="w-6 h-6 bg-gradient-to-br ${user.color} rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    ${user.initials}
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-2 shadow-sm">
                                        <p class="text-xs text-gray-800 dark:text-gray-200">Hi! Can we discuss the agenda for next week's board meeting?</p>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-1">2 minutes ago</p>
                                </div>
                            </div>

                            <!-- Sent Message -->
                            <div class="flex items-start space-x-2 justify-end">
                                <div class="flex-1 flex justify-end">
                                    <div class="max-w-[75%]">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-2 shadow-sm">
                                            <p class="text-xs">Sure! I've prepared a draft agenda. Let me share it with you.</p>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mr-1 text-right">1 minute ago</p>
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
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-2 shadow-sm">
                                        <p class="text-xs text-gray-800 dark:text-gray-200">That would be great! I'll review it and get back to you with my feedback.</p>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-1">Just now</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 relative">
                            <!-- Emoji Picker -->
                            <div class="emoji-picker hidden absolute bottom-full left-0 mb-2 w-80 bg-gray-800 dark:bg-gray-900 rounded-lg shadow-2xl border border-gray-700 dark:border-gray-600 z-50 flex flex-col" style="height: 300px;">
                                @include('components.emoji-picker')
                            </div>
                            <!-- File Preview Area -->
                            <div class="chat-file-preview hidden px-3 pb-3 flex items-center gap-3 overflow-x-auto overflow-y-hidden" style="scrollbar-width: thin; scrollbar-color: rgba(107, 114, 128, 0.5) transparent;">
                                <!-- Files will be added here as cards -->
                            </div>
                            <form class="chat-form flex items-center space-x-2 px-3">
                                <!-- File Attachment Button -->
                                <button type="button" class="chat-attach-btn p-2 text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                <input type="file" class="hidden chat-file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                <input type="text" placeholder="Type a message..." class="chat-input flex-1 px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <!-- Emoji Button -->
                                <button type="button" class="chat-emoji-btn p-2 text-yellow-500 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-xs font-semibold hover:from-blue-700 hover:to-purple-700 transition">
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Minimized Chat (Circular Avatar) -->
                    <div class="chat-minimized hidden w-14 h-14 bg-gradient-to-br ${user.color} rounded-full shadow-lg border-2 border-white dark:border-gray-800 flex items-center justify-center text-white font-semibold text-sm cursor-pointer hover:scale-110 transition-transform relative" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <span class="chat-minimized-avatar">${user.initials}</span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
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
                    });
                });

                // Emoji category switching
                const categoryBtns = emojiPicker.querySelectorAll('.emoji-category-btn');
                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        categoryBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        // Category switching logic can be added here
                    });
                });
            }

            // File attachment
            // Store attached files for this chat (accessible to all handlers)
            let attachedFiles = [];
            
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
                        fileCard.className = 'relative flex-shrink-0 w-12 h-12 bg-gray-800 dark:bg-gray-700 rounded-lg border border-gray-700 dark:border-gray-600 overflow-hidden group';
                        fileCard.setAttribute('data-file-index', index);
                        
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                fileCard.innerHTML = `
                                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                                    <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-gray-800 dark:bg-gray-900 text-white rounded-full text-sm flex items-center justify-center hover:bg-gray-700 dark:hover:bg-gray-800 transition opacity-0 group-hover:opacity-100 remove-file shadow-lg" data-file-index="${index}" title="Remove">
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
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-800 dark:bg-gray-700">
                                    ${getFileIcon(file.name)}
                                    <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-gray-800 dark:bg-gray-900 text-white rounded-full text-sm flex items-center justify-center hover:bg-gray-700 dark:hover:bg-gray-800 transition opacity-0 group-hover:opacity-100 remove-file shadow-lg" data-file-index="${index}" title="Remove">
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
                                            imgDiv.innerHTML = `<img src="${e.target.result}" alt="${file.name}" class="max-w-[200px] rounded-lg">`;
                                            const contentDiv = messageDiv.querySelector('.message-content');
                                            if (contentDiv) {
                                                contentDiv.appendChild(imgDiv);
                                            }
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        const fileDiv = document.createElement('div');
                                        fileDiv.className = 'mb-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg';
                                        fileDiv.innerHTML = `
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs text-gray-700 dark:text-gray-300">${file.name}</span>
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
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mr-1 text-right">Just now</p>
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
        background: rgba(55, 65, 81, 0.3);
        border-radius: 3px;
    }

    .emoji-grid::-webkit-scrollbar-thumb {
        background: rgba(107, 114, 128, 0.5);
        border-radius: 3px;
    }

    .emoji-grid::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 0.7);
    }

    .emoji-item {
        transition: background-color 0.2s;
    }

    .emoji-item:hover {
        background-color: rgba(107, 114, 128, 0.3);
    }

    .emoji-category-btn.active {
        background-color: rgba(59, 130, 246, 0.2);
    }

    .chat-file-preview {
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        min-height: 60px;
    }

    .dark .chat-file-preview {
        border-bottom-color: rgba(55, 65, 81, 0.5);
    }

    .chat-file-preview::-webkit-scrollbar {
        height: 6px;
    }

    .chat-file-preview::-webkit-scrollbar-track {
        background: rgba(55, 65, 81, 0.3);
        border-radius: 3px;
    }

    .chat-file-preview::-webkit-scrollbar-thumb {
        background: rgba(107, 114, 128, 0.5);
        border-radius: 3px;
    }

    .chat-file-preview::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 0.7);
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
