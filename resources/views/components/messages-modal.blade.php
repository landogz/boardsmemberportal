<!-- Messages Modal - Slides in from right -->
<div id="messagesModal" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-10 transition-opacity" id="messagesModalBackdrop"></div>
    
    <!-- Modal Panel -->
    <div class="fixed right-0 top-0 h-full w-full sm:w-96 bg-white dark:bg-gray-800 shadow-2xl transform transition-transform duration-300 ease-in-out" id="messagesModalPanel" style="transform: translateX(100%);">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Messages</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400" id="onlineCount">0 online</p>
            </div>
            <button type="button" id="closeMessagesModal" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="messagesSearchInput" placeholder="Search users..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
        </div>
        
        <!-- Users List -->
        <div class="flex-1 overflow-y-auto" id="messagesUsersList" style="max-height: calc(100vh - 140px);">
            <!-- Users will be loaded here -->
            <div class="p-4 text-center text-gray-500 dark:text-gray-400" id="messagesLoading">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Loading users...</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('messagesModal');
    const backdrop = document.getElementById('messagesModalBackdrop');
    const panel = document.getElementById('messagesModalPanel');
    const closeBtn = document.getElementById('closeMessagesModal');
    const searchInput = document.getElementById('messagesSearchInput');
    const usersList = document.getElementById('messagesUsersList');
    const onlineCountEl = document.getElementById('onlineCount');
    
    let allUsers = [];
    let filteredUsers = [];
    
    // Open modal
    window.openMessagesModal = function() {
        if (!modal) return;
        
        modal.classList.remove('hidden');
        // Trigger reflow to ensure transition works
        void modal.offsetWidth;
        panel.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';
        
        // Load users if not already loaded
        if (allUsers.length === 0) {
            loadUsers();
        }
    };
    
    // Close modal
    function closeModal() {
        if (!modal || !panel) return;
        
        panel.style.transform = 'translateX(100%)';
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    
    // Close button
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Close on backdrop click
    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
    
    // Load users from server
    function loadUsers() {
        const loadingEl = document.getElementById('messagesLoading');
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }
        
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
                allUsers = data.users;
                filteredUsers = [...allUsers];
                renderUsers();
                updateOnlineCount();
            } else {
                showError('Failed to load users');
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showError('Error loading users. Please try again.');
        })
        .finally(() => {
            if (loadingEl) {
                loadingEl.classList.add('hidden');
            }
        });
    }
    
    // Render users list grouped by privilege and agency
    function renderUsers() {
        if (!usersList) return;
        
        if (filteredUsers.length === 0) {
            usersList.innerHTML = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">No users found</p>
                </div>
            `;
            return;
        }
        
        // Group users by privilege first, then by agency
        const groupedUsers = {};
        filteredUsers.forEach(user => {
            const privilege = user.privilege || 'No Privilege';
            const agency = user.agency || 'No Agency';
            
            if (!groupedUsers[privilege]) {
                groupedUsers[privilege] = {};
            }
            if (!groupedUsers[privilege][agency]) {
                groupedUsers[privilege][agency] = [];
            }
            groupedUsers[privilege][agency].push(user);
        });
        
        // Sort privileges (Admin first, then others alphabetically)
        const privilegeOrder = ['admin', 'consec', 'user'];
        const sortedPrivileges = Object.keys(groupedUsers).sort((a, b) => {
            const aIndex = privilegeOrder.indexOf(a.toLowerCase());
            const bIndex = privilegeOrder.indexOf(b.toLowerCase());
            if (aIndex !== -1 && bIndex !== -1) return aIndex - bIndex;
            if (aIndex !== -1) return -1;
            if (bIndex !== -1) return 1;
            return a.localeCompare(b);
        });
        
        let html = '';
        
        sortedPrivileges.forEach(privilege => {
            const agencies = groupedUsers[privilege];
            const sortedAgencies = Object.keys(agencies).sort((a, b) => {
                if (a === 'No Agency') return 1;
                if (b === 'No Agency') return -1;
                return a.localeCompare(b);
            });
            
            sortedAgencies.forEach(agency => {
                const users = agencies[agency];
                
                // Group header
                const privilegeText = privilege.charAt(0).toUpperCase() + privilege.slice(1);
                const agencyText = agency;
                html += `
                    <div class="sticky top-0 bg-gray-100 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 z-10">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                            ${privilegeText}${agencyText !== 'No Agency' ? ` • ${agencyText}` : ''}
                        </p>
                    </div>
                `;
                
                // Users in this group
                users.forEach(user => {
                    const initials = (user.first_name?.[0] || '') + (user.last_name?.[0] || '');
                    const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
                    const isOnline = user.is_online || false;
                    const statusText = isOnline ? 'Active now' : (user.last_activity ? getTimeAgo(user.last_activity) : 'Offline');
                    const statusColor = isOnline ? 'text-green-500' : 'text-gray-400';
                    const indicatorColor = isOnline ? '#3fbb46' : '#9ca3af';
                    
                    // Get profile picture
                    let avatarHtml = '';
                    if (user.profile_picture_url) {
                        avatarHtml = `<img src="${user.profile_picture_url}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">`;
                    } else {
                        const gradientColors = [
                            'from-purple-400 to-purple-600',
                            'from-blue-400 to-blue-600',
                            'from-green-400 to-green-600',
                            'from-indigo-400 to-indigo-600',
                            'from-yellow-400 to-orange-500',
                            'from-pink-400 to-pink-600',
                            'from-red-400 to-red-600',
                        ];
                        const colorIndex = (user.id?.charCodeAt(0) || 0) % gradientColors.length;
                        avatarHtml = `<div class="w-12 h-12 rounded-full bg-gradient-to-br ${gradientColors[colorIndex]} flex items-center justify-center text-white font-semibold text-sm">${initials}</div>`;
                    }
                    
                    // Format privilege (capitalize first letter)
                    const privilegeText = user.privilege ? user.privilege.charAt(0).toUpperCase() + user.privilege.slice(1) : '';
                    const positionText = user.position || '';
                    
                    // Build info text (privilege and position)
                    let infoText = '';
                    if (privilegeText && positionText) {
                        infoText = `${privilegeText} • ${positionText}`;
                    } else if (privilegeText) {
                        infoText = privilegeText;
                    } else if (positionText) {
                        infoText = positionText;
                    }
                    
                    html += `
                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition border-b border-gray-100 dark:border-gray-700 message-user-item" data-user-id="${user.id}" data-user-name="${fullName}" data-user-initials="${initials}">
                            <div class="relative flex-shrink-0">
                                ${avatarHtml}
                                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800" style="background-color: ${indicatorColor};"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">${fullName}</p>
                                ${infoText ? `<p class="text-xs text-gray-500 dark:text-gray-400 truncate">${infoText}</p>` : ''}
                                <p class="text-xs ${statusColor} truncate">${statusText}</p>
                            </div>
                        </div>
                    `;
                });
            });
        });
        
        usersList.innerHTML = html;
        
        // Add click handlers
        usersList.querySelectorAll('.message-user-item').forEach(item => {
            item.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userInitials = this.getAttribute('data-user-initials');
                
                // Close modal
                closeModal();
                
                // Open chat popup if function exists
                if (typeof window.openMessagesPopup === 'function') {
                    window.openMessagesPopup(userId, userName, userInitials);
                }
            });
        });
    }
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                filteredUsers = [...allUsers];
            } else {
                filteredUsers = allUsers.filter(user => {
                    const fullName = `${user.first_name || ''} ${user.last_name || ''}`.toLowerCase();
                    return fullName.includes(searchTerm);
                });
            }
            
            renderUsers();
        });
    }
    
    // Update online count
    function updateOnlineCount() {
        if (!onlineCountEl) return;
        
        const onlineCount = allUsers.filter(u => u.is_online).length;
        onlineCountEl.textContent = `${onlineCount} online`;
    }
    
    // Get time ago
    function getTimeAgo(timestamp) {
        if (!timestamp) return 'Offline';
        
        const now = new Date();
        const time = new Date(timestamp);
        const diffMs = now - time;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} ${diffMins === 1 ? 'minute' : 'minutes'} ago`;
        if (diffHours < 24) return `${diffHours} ${diffHours === 1 ? 'hour' : 'hours'} ago`;
        if (diffDays < 7) return `${diffDays} ${diffDays === 1 ? 'day' : 'days'} ago`;
        return time.toLocaleDateString();
    }
    
    // Show error
    function showError(message) {
        if (usersList) {
            usersList.innerHTML = `
                <div class="p-4 text-center text-red-500 dark:text-red-400">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p class="text-sm">${message}</p>
                </div>
            `;
        }
    }
})();
</script>

<style>
#messagesModalPanel {
    z-index: 10000;
}

#messagesModalBackdrop {
    z-index: 9999;
}

@media (max-width: 640px) {
    #messagesModalPanel {
        width: 100% !important;
    }
}
</style>

