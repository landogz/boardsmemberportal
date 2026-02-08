<script>
    $(document).ready(function() {
        // Sidebar Toggle
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('-translate-x-full');
            $('#sidebarOverlay').toggleClass('hidden');
        });
        
        $('#sidebarOverlay').on('click', function() {
            $('#sidebar').addClass('-translate-x-full');
            $('#sidebarOverlay').addClass('hidden');
        });

        // Menu Toggle
        $('.menu-toggle').on('click', function(e) {
            e.preventDefault();
            const $menuItem = $(this).parent('li');
            const $submenu = $menuItem.find('> ul');
            $submenu.slideToggle();
            $(this).find('.fa-chevron-down').toggleClass('rotate-180');
        });
        
        // Dropdown Toggle
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if mobile device (screen width <= 640px)
            const isMobile = window.innerWidth <= 640;
            
            // Get the dropdown and check if it's notifications or messages
            const $dropdown = $(this).parent('.dropdown');
            const isNotifications = $dropdown.attr('id') === 'notificationsDropdown';
            const isMessages = $(this).find('.fa-envelope').length > 0;
            
            // On mobile, redirect to full pages instead of opening dropdown
            if (isMobile) {
                if (isNotifications) {
                    window.location.href = '{{ route("admin.notifications.index") }}';
                    return;
                } else if (isMessages) {
                    window.location.href = '{{ route("admin.messages") }}';
                    return;
                }
            }
            
            // Desktop behavior: toggle dropdown
            // Close all other dropdowns
            $('.dropdown').not($dropdown).removeClass('show');
            
            // Toggle current dropdown
            $dropdown.toggleClass('show');
            
            // Load messages if messages dropdown is opened
            if (isMessages && $dropdown.hasClass('show')) {
                loadAdminMessagesDropdown();
                // Set up periodic refresh when dropdown is open (every 10 seconds)
                if (window.adminMessagesDropdownInterval) {
                    clearInterval(window.adminMessagesDropdownInterval);
                }
                window.adminMessagesDropdownInterval = setInterval(function() {
                    if ($dropdown.hasClass('show')) {
                        $('#adminMessagesDropdownList').data('loaded', false);
                        loadAdminMessagesDropdown();
                    } else {
                        clearInterval(window.adminMessagesDropdownInterval);
                        window.adminMessagesDropdownInterval = null;
                    }
                }, 10000); // Refresh every 10 seconds
            } else if (window.adminMessagesDropdownInterval) {
                // Clear interval when dropdown is closed
                clearInterval(window.adminMessagesDropdownInterval);
                window.adminMessagesDropdownInterval = null;
            }
        });
        
        // Load admin messages dropdown
        function loadAdminMessagesDropdown() {
            const messagesList = $('#adminMessagesDropdownList');
            if (!messagesList.length) return;
            
            // Check if already loaded
            if (messagesList.data('loaded') === true) return;
            
            fetch('{{ route("messages.recent") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.conversations) {
                    renderAdminMessagesDropdown(data.conversations);
                    messagesList.data('loaded', true);
                } else {
                    messagesList.html(`
                        <div class="px-4 py-8 text-center">
                            <i class="fas fa-comment-slash text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-500">No messages yet</p>
                        </div>
                    `);
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                messagesList.html(`
                    <div class="px-4 py-8 text-center">
                        <p class="text-sm text-red-500">Error loading messages</p>
                    </div>
                `);
            });
        }
        
        function renderAdminMessagesDropdown(conversations) {
            const messagesList = $('#adminMessagesDropdownList');
            if (!messagesList.length) return;
            
            if (conversations.length === 0) {
                messagesList.html(`
                    <div class="px-4 py-8 text-center">
                        <i class="fas fa-comment-slash text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">No messages yet</p>
                    </div>
                `);
                updateAdminMessagesBadge(0);
                return;
            }
            
            let html = '';
            let totalUnread = 0;
            conversations.forEach(conv => {
                const initials = conv.user_initials || 'U';
                const userName = conv.user_name || 'User';
                const lastMessage = conv.last_message || '';
                const timeAgo = getAdminTimeAgo(conv.last_message_time);
                const unreadCount = conv.unread_count || 0;
                totalUnread += unreadCount;
                
                // Get avatar HTML
                let avatarHtml = '';
                if (conv.profile_picture_url) {
                    avatarHtml = `<img src="${conv.profile_picture_url}" alt="${userName}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">`;
                } else {
                    avatarHtml = `<div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mr-3 text-white font-semibold text-sm shadow-sm">${initials}</div>`;
                }
                
                // Store conversation data in data attributes
                const convDataJson = JSON.stringify({
                    user_id: conv.user_id,
                    profile_picture_url: conv.profile_picture_url || null,
                    user_initials: initials,
                    is_online: conv.is_online || false
                });
                
                html += `
                    <a href="#" class="message-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer transition-colors relative" 
                       data-user-id="${conv.user_id}" 
                       data-user-name="${userName.replace(/"/g, '&quot;')}" 
                       data-conv-data='${convDataJson.replace(/'/g, "&#39;")}'
                       onclick="event.preventDefault(); 
                        // Check if we're on messages page
                        if (window.location.pathname === '/messages' || window.location.pathname === '/admin/messages') {
                            // If on messages page, open chat and select conversation
                            if (typeof window.openChat === 'function') {
                                // Get conversation data from data attribute
                                const convDataAttr = this.getAttribute('data-conv-data');
                                const convData = convDataAttr ? JSON.parse(convDataAttr) : {user_id: this.getAttribute('data-user-id')};
                                const userName = this.getAttribute('data-user-name');
                                const userId = this.getAttribute('data-user-id');
                                window.openChat(userId, userName, convData);
                            } else {
                                // Fallback: navigate to messages page
                                window.location.href = window.location.pathname.includes('/admin') ? '/admin/messages' : '/messages';
                            }
                        } else {
                            // If not on messages page, open popup
                            if(typeof window.openMessagesPopup === 'function') {
                                const userId = this.getAttribute('data-user-id');
                                const userName = this.getAttribute('data-user-name');
                                const convDataAttr = this.getAttribute('data-conv-data');
                                const convData = convDataAttr ? JSON.parse(convDataAttr) : {};
                                window.openMessagesPopup(userId, userName, convData.user_initials || 'U');
                            }
                        }
                        $('.dropdown').removeClass('show'); 
                        return false;">
                        ${avatarHtml}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-gray-800">${userName}</p>
                                <span class="text-xs text-gray-400 ml-2 flex-shrink-0">${timeAgo}</span>
                            </div>
                            <p class="text-xs text-gray-600 truncate leading-relaxed">${escapeAdminHtml(lastMessage)}</p>
                        </div>
                        ${unreadCount > 0 ? `<span class="absolute top-3 right-3 h-5 min-w-[20px] px-1.5 rounded-full text-white text-[10px] font-bold flex items-center justify-center" style="background-color: #CE2028;">${unreadCount > 99 ? '99+' : unreadCount}</span>` : ''}
                    </a>
                `;
            });
            
            messagesList.html(html);
            updateAdminMessagesBadge(totalUnread);
        }

        // Update admin messages badge count
        function updateAdminMessagesBadge(count) {
            const badgeCount = $('#adminMessagesBadgeCount');
            if (count > 0) {
                badgeCount.text(count > 99 ? '99+' : count).removeClass('hidden');
            } else {
                badgeCount.addClass('hidden');
            }
        }

        // Load admin unread count
        function loadAdminUnreadCount() {
            fetch('{{ route("messages.unread-count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateAdminMessagesBadge(data.count);
                }
            })
            .catch(error => console.error('Error loading unread count:', error));
        }
        
        // Make function globally accessible for real-time updates
        window.loadAdminUnreadCount = loadAdminUnreadCount;

        // ========== LARAVEL BROADCASTING + REVERB ==========
        @auth
        // Initialize Echo/Reverb connection
        let adminEcho = null;
        
        // Load Laravel Echo and Reverb
        function loadAdminBroadcastingScripts() {
            // Load Pusher first (required by Echo)
            if (typeof window.Pusher === 'undefined') {
                const pusherScript = document.createElement('script');
                pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
                pusherScript.onload = function() {
                    // After Pusher loads, load Echo
                    loadAdminEcho();
                };
                pusherScript.onerror = function() {
                    console.error('Failed to load Pusher');
                };
                document.head.appendChild(pusherScript);
            } else {
                loadAdminEcho();
            }
        }

        function loadAdminEcho() {
            if (typeof window.Echo === 'undefined') {
                // Use cdnjs which provides UMD build for browsers
                const echoScript = document.createElement('script');
                echoScript.type = 'text/javascript';
                echoScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.16.1/echo.iife.js';
                echoScript.onload = function() {
                    initializeAdminEcho();
                };
                echoScript.onerror = function() {
                    // Fallback to unpkg if cdnjs fails
                    console.warn('cdnjs failed, trying unpkg...');
                    const fallbackScript = document.createElement('script');
                    fallbackScript.type = 'text/javascript';
                    fallbackScript.src = 'https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js';
                    fallbackScript.onload = function() {
                        initializeAdminEcho();
                    };
                    fallbackScript.onerror = function() {
                        console.error('Failed to load Laravel Echo from all sources');
                    };
                    document.head.appendChild(fallbackScript);
                };
                document.head.appendChild(echoScript);
            } else {
                initializeAdminEcho();
            }
        }

        function initializeAdminEcho() {
            if (typeof window.Pusher === 'undefined') {
                console.error('Pusher not loaded');
                return;
            }

            if (typeof window.Echo === 'undefined') {
                console.error('Echo not loaded');
                return;
            }

            const userId = '{{ Auth::id() }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Initialize Echo with Reverb (reuse if already initialized from header)
            if (!window.echoInstance || !window.echoInstance.connector) {
                try {
                    const reverbScheme = '{{ config("reverb.apps.apps.0.options.scheme", "http") }}';
                    const reverbHost = '{{ config("reverb.apps.apps.0.options.host", "127.0.0.1") }}';
                    const reverbPort = {{ config("reverb.apps.apps.0.options.port", 8080) }};
                    const useTLS = reverbScheme === 'https';
                    
                    window.echoInstance = new window.Echo({
                        broadcaster: 'reverb',
                        key: '{{ config("reverb.apps.apps.0.key") }}',
                        cluster: '',
                        wsHost: reverbHost,
                        wsPort: reverbPort,
                        wssPort: reverbPort,
                        forceTLS: useTLS,
                        enabledTransports: ['ws'],
                        disableStats: true,
                        authEndpoint: '/broadcasting/auth',
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                            }
                        }
                    });

                    adminEcho = window.echoInstance;

                    // Add connection event listeners
                    adminEcho.connector.pusher.connection.bind('connected', function() {
                        console.log('✅ Reverb WebSocket connected successfully (admin)');
                    });

                    adminEcho.connector.pusher.connection.bind('disconnected', function() {
                        console.warn('⚠️ Reverb WebSocket disconnected (admin)');
                    });

                    adminEcho.connector.pusher.connection.bind('error', function(err) {
                        console.error('❌ Reverb WebSocket error (admin):', err);
                    });

                    adminEcho.connector.pusher.connection.bind('state_change', function(states) {
                        console.log('🔄 Reverb connection state (admin):', states.current);
                    });

                    // Listen to message unread count updates
                    adminEcho.private(`user.${userId}`)
                        .listen('.message.unread-count.updated', (e) => {
                            updateAdminMessagesBadge(e.count);
                            // Reload dropdown if it's currently open/visible
                            const messagesDropdown = $('.dropdown').has('#adminMessagesDropdownList');
                            if (messagesDropdown.hasClass('show')) {
                                // Reset loaded state and reload
                                $('#adminMessagesDropdownList').data('loaded', false);
                                loadAdminMessagesDropdown();
                            }
                        });

                    // Listen to notification unread count updates
                    adminEcho.private(`user.${userId}`)
                        .listen('.notification.unread-count.updated', (e) => {
                            updateAdminNotificationBadge(e.count);
                        });

                    // Message unsent elsewhere: dispatch so admin messages page can show trail in real time
                    adminEcho.private(`user.${userId}`)
                        .listen('.message.content.deleted', (e) => {
                            try {
                                window.dispatchEvent(new CustomEvent('message-content-deleted', { detail: e }));
                            } catch (err) {
                                console.warn('message-content-deleted handler:', err);
                            }
                        });

                    console.log('Laravel Echo initialized successfully (admin)');
                    console.log('Connecting to Reverb at:', reverbScheme + '://' + reverbHost + ':' + reverbPort);
                    
                    // Check connection status after 3 seconds (silently, only log if in development)
                    setTimeout(function() {
                        const state = adminEcho.connector.pusher.connection.state;
                        if (state !== 'connected') {
                            // Only show warning in development mode
                            const isDevelopment = '{{ env("APP_ENV", "production") }}' === 'local';
                            if (isDevelopment) {
                                console.warn('⚠️ Reverb connection not established. Current state:', state);
                                console.log('💡 Make sure:');
                                console.log('   1. Reverb server is running: php artisan reverb:start');
                                console.log('   2. Your .env has correct REVERB_APP_KEY, REVERB_APP_ID, REVERB_APP_SECRET');
                                console.log('   3. REVERB_SCHEME=http for local development');
                            }
                            // Silently handle connection failure in production
                            // Broadcasting will work via polling/fallback methods
                        }
                    }, 3000);
                } catch (error) {
                    console.error('Error initializing Echo:', error);
                }
            } else {
                adminEcho = window.echoInstance;
                
                // Re-attach listeners if Echo was already initialized
                const userId = '{{ Auth::id() }}';
                adminEcho.private(`user.${userId}`)
                    .listen('.message.unread-count.updated', (e) => {
                        updateAdminMessagesBadge(e.count);
                        // Reload dropdown if it's currently open/visible
                        const messagesDropdown = $('.dropdown').has('#adminMessagesDropdownList');
                        if (messagesDropdown.hasClass('show')) {
                            // Reset loaded state and reload
                            $('#adminMessagesDropdownList').data('loaded', false);
                            loadAdminMessagesDropdown();
                        }
                    });

                adminEcho.private(`user.${userId}`)
                    .listen('.notification.unread-count.updated', (e) => {
                        updateAdminNotificationBadge(e.count);
                    });

                adminEcho.private(`user.${userId}`)
                    .listen('.message.content.deleted', (e) => {
                        try {
                            window.dispatchEvent(new CustomEvent('message-content-deleted', { detail: e }));
                        } catch (err) {
                            console.warn('message-content-deleted handler:', err);
                        }
                    });
            }
        }

        // Start loading scripts when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadAdminBroadcastingScripts);
        } else {
            loadAdminBroadcastingScripts();
        }

        @endauth

        // Update admin notification badge helper (available globally)
        function updateAdminNotificationBadge(count) {
            const badge = $('#notificationBadge');
            const badgeText = count > 99 ? '99+' : count;
            if (count > 0) {
                badge.text(badgeText).removeClass('hidden');
            } else {
                badge.addClass('hidden');
            }
        }
        
        function getAdminTimeAgo(timestamp) {
            if (!timestamp) return '';
            const now = new Date();
            const time = new Date(timestamp);
            const diffMs = now - time;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m`;
            if (diffHours < 24) return `${diffHours}h`;
            if (diffDays < 7) return `${diffDays}d`;
            return time.toLocaleDateString();
        }
        
        function escapeAdminHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
                // Reset messages dropdown loaded state when closed
                $('#adminMessagesDropdownList').data('loaded', false);
                // Clear refresh interval
                if (window.adminMessagesDropdownInterval) {
                    clearInterval(window.adminMessagesDropdownInterval);
                    window.adminMessagesDropdownInterval = null;
                }
            }
        });
        
        // Close dropdown when losing focus (blur event)
        $(document).on('focusout', '.dropdown', function(e) {
            // Check if focus is moving outside the dropdown
            const $dropdown = $(this);
            setTimeout(function() {
                if (!$dropdown.find(':focus').length && !$dropdown.is(':hover')) {
                    $dropdown.removeClass('show');
                    // Reset messages dropdown loaded state when closed
                    $('#adminMessagesDropdownList').data('loaded', false);
                    // Clear refresh interval
                    if (window.adminMessagesDropdownInterval) {
                        clearInterval(window.adminMessagesDropdownInterval);
                        window.adminMessagesDropdownInterval = null;
                    }
                }
            }, 100);
        });
        
        // Close dropdown on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.dropdown').removeClass('show');
            }
        });

        // Handle new message button click from admin header dropdown
        function handleAdminHeaderNewMessageClick(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Check if we're on messages page
            if (window.location.pathname === '/admin/messages') {
                // If on messages page, trigger new message modal
                const newMessageBtn = document.getElementById('newMessageBtn');
                if (newMessageBtn) {
                    newMessageBtn.click();
                } else {
                    // Fallback: try to open user selection modal directly
                    const userModal = document.getElementById('userSelectionModal');
                    if (userModal) {
                        userModal.classList.remove('hidden');
                        if (typeof loadUsersForSelection === 'function') {
                            loadUsersForSelection();
                        }
                    }
                }
            } else {
                // Navigate to messages page with hash to trigger new message
                window.location.href = '{{ route('admin.messages') }}#new-message';
            }
            
            $('.dropdown').removeClass('show');
            return false;
        }

        // Attach event listener to admin new message button when DOM is ready
        $(document).ready(function() {
            $('#adminHeaderNewMessageBtn').on('click', handleAdminHeaderNewMessageClick);
        });

        // Load unread count on page load (broadcasting will handle updates)
        loadAdminUnreadCount();

        // Logout functionality
        $('#logoutBtn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("logout") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.redirect || '/';
                            }
                        },
                        error: function() {
                            window.location.href = '/';
                        }
                    });
                }
            });
        });

        // User Activity Tracking for Online Status
        let activityTimeout;
        let lastActivityTime = Date.now();
        const IDLE_TIMEOUT = 15 * 60 * 1000; // 15 minutes in milliseconds
        const PING_INTERVAL = 5 * 60 * 1000; // Ping server every 5 minutes

        // Track user activity
        function trackActivity() {
            lastActivityTime = Date.now();
            
            // Clear existing timeout
            clearTimeout(activityTimeout);
            
            // Set new timeout to check for idle
            activityTimeout = setTimeout(function() {
                checkIdleStatus();
            }, IDLE_TIMEOUT);
        }

        // Check if user is idle
        function checkIdleStatus() {
            const timeSinceLastActivity = Date.now() - lastActivityTime;
            
            if (timeSinceLastActivity >= IDLE_TIMEOUT) {
                // User has been idle for 15 minutes, show warning
                Swal.fire({
                    title: 'Session Timeout',
                    text: 'You have been idle for 15 minutes. You will be logged out for security.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    // Logout user
                    $.ajax({
                        url: '{{ route("logout") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            window.location.href = '/';
                        },
                        error: function() {
                            window.location.href = '/';
                        }
                    });
                });
            }
        }

        // Ping server to update activity
        function pingServer() {
            $.ajax({
                url: '{{ route("api.track-activity") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Reset activity tracking when ping succeeds
                        trackActivity();
                    }
                },
                error: function(xhr) {
                    // If ping fails with 401, user might be logged out
                    if (xhr.status === 401) {
                        console.log('User session expired');
                        window.location.href = '/login';
                    } else {
                        console.log('Activity ping failed');
                    }
                }
            });
        }

        // Track various user activities
        $(document).on('mousemove keydown click scroll touchstart', function() {
            trackActivity();
        });

        // Initial activity tracking
        trackActivity();

        // Ping server every 5 minutes
        setInterval(pingServer, PING_INTERVAL);

        // Ping server on page visibility change (when user switches tabs)
        $(document).on('visibilitychange', function() {
            if (!document.hidden) {
                // User came back, immediately ping and track activity
                pingServer();
                trackActivity();
            }
        });

        // Ping server when window gains focus
        $(window).on('focus', function() {
            // Window gained focus, immediately ping and track activity
            pingServer();
            trackActivity();
        });
        
        // Also track activity on page load/refresh
        $(document).ready(function() {
            trackActivity();
            pingServer();
        });

        // ========== NOTIFICATIONS SYSTEM ==========
        // Load axios if not already loaded
        if (typeof axios === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';
            document.head.appendChild(script);
        }

        // Set axios defaults and 419 (session/CSRF expired) interceptor
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            if (!window._axios419InterceptorAdded) {
                window._axios419InterceptorAdded = true;
                axios.interceptors.response.use(function(res) { return res; }, function(err) {
                    if (err.response && err.response.status === 419) {
                        var url = (err.response.data && err.response.data.redirect) || '{{ route("login") }}';
                        window.location.href = url;
                    }
                    return Promise.reject(err);
                });
            }
        }

        // Notification icon mapping
        const notificationIcons = {
            'pending_registration': 'fa-clock',
            'announcement': 'fa-bullhorn',
            'notice': 'fa-file-text',
            'default': 'fa-bell'
        };

        // Load notifications
        function loadNotifications() {
            if (typeof axios === 'undefined') {
                setTimeout(loadNotifications, 100);
                return;
            }

            axios.get('{{ route("notifications.recent") }}', { params: { limit: 3 } })
                .then(response => {
                    const notifications = (response.data && response.data.notifications) ? response.data.notifications.slice(0, 3) : []; // Ensure max 3
                    const notificationsList = $('#notificationsList');
                    const notificationBadge = $('#notificationBadge');
                    const markAllReadBtn = $('#markAllReadBtn');
                    
                    // Update badge count (use full count from server, not just displayed ones)
                    axios.get('{{ route("notifications.unread-count") }}')
                        .then(countResponse => {
                            const count = countResponse.data.count;
                            if (count > 0) {
                                notificationBadge.text(count).removeClass('hidden');
                                markAllReadBtn.removeClass('hidden');
                            } else {
                                notificationBadge.addClass('hidden');
                                markAllReadBtn.addClass('hidden');
                            }
                        });
                    
                    // Render notifications (max 3)
                    if (notifications.length === 0) {
                        notificationsList.html(`
                            <div class="px-4 py-8 text-center">
                                <i class="fas fa-bell-slash text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">No notifications</p>
                            </div>
                        `);
                    } else {
                        let html = '';
                        notifications.forEach(notification => {
                            const icon = notificationIcons[notification.type] || notificationIcons.default;
                            const bgClass = notification.is_read ? '' : 'bg-blue-50';
                            const fontWeight = notification.is_read ? 'font-normal' : 'font-semibold';
                            
                            html += `
                                <div class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 ${bgClass} relative group" data-notification-id="${notification.id}">
                                    <a href="${notification.url || '#'}" class="flex items-start flex-1 min-w-0" ${notification.url ? '' : 'onclick="event.preventDefault(); return false;"'}>
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.15);">
                                            <i class="fas ${icon}" style="color: #055498; font-size: 14px;"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm ${fontWeight} text-gray-800 mb-0.5 leading-tight">${notification.title}</p>
                                            <p class="text-xs text-gray-600 truncate leading-relaxed mt-0.5">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1.5">${notification.created_at}</p>
                                        </div>
                                    </a>
                                    ${!notification.is_read ? '<div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                                    <div class="notification-menu-container relative flex-shrink-0 ml-2">
                                        <button type="button" class="notification-menu-btn w-8 h-8 rounded-full hover:bg-gray-200 flex items-center justify-center transition-opacity opacity-0 group-hover:opacity-100" data-notification-id="${notification.id}">
                                            <i class="fas fa-ellipsis-h text-gray-600 text-sm"></i>
                                        </button>
                                        <div class="notification-menu-dropdown absolute right-0 top-8 bg-gray-800 rounded-lg shadow-xl border border-gray-700 min-w-[180px]" data-menu-id="${notification.id}" style="z-index: 9999 !important; display: none;">
                                            <div class="py-1">
                                                ${!notification.is_read ? `
                                                <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
                                                    <i class="fas fa-check text-sm"></i>
                                                    Mark as read
                                                </button>
                                                ` : `
                                                <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
                                                    <i class="fas fa-check text-sm"></i>
                                                    Mark as unread
                                                </button>
                                                `}
                                                <button type="button" class="notification-delete-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
                                                    <i class="fas fa-times text-sm"></i>
                                                    Delete this notification
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        notificationsList.html(html);
                        
                        // Mark as read when clicked and handle announcement modal
                        $('.notification-item a').off('click.notification').on('click.notification', function(e) {
                            const $item = $(this).closest('.notification-item');
                            const notificationId = $item.data('notification-id');
                            const notificationUrl = $(this).attr('href');
                            
                            // If URL is for an announcement, open modal instead
                            if (notificationUrl && notificationUrl.includes('/announcements/')) {
                                e.preventDefault();
                                
                                // Extract announcement ID from URL
                                const announcementIdMatch = notificationUrl.match(/\/announcements\/(\d+)/);
                                if (announcementIdMatch && announcementIdMatch[1]) {
                                    const announcementId = announcementIdMatch[1];
                                    
                                    // Close dropdown
                                    $('.dropdown').removeClass('show');
                                    
                                    // Mark as read first
                                    if (notificationId && !$item.hasClass('bg-gray-50')) {
                                        axios.post(`/notifications/${notificationId}/mark-as-read`)
                                            .then(() => {
                                                loadNotifications();
                                                updateUnreadCount();
                                                
                                                // Trigger event to update notifications page if open (real-time sync)
                                                window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                                                    detail: { notificationId: notificationId } 
                                                }));
                                            });
                                    }
                                    
                                    // Open announcement modal if function exists (on landing page)
                                    if (typeof window.openAnnouncementModal === 'function') {
                                        window.openAnnouncementModal(parseInt(announcementId));
                                    } else {
                                        // Fallback: navigate to announcement page
                                        window.location.href = notificationUrl;
                                    }
                                }
                                
                                return false;
                            }
                            // If URL contains a comment hash, handle scrolling
                            else if (notificationUrl && notificationUrl.includes('#comment-')) {
                                // Extract comment ID from URL
                                const commentId = notificationUrl.split('#comment-')[1];
                                
                                // Close dropdown
                                $('.dropdown').removeClass('show');
                                
                                // Navigate to the URL
                                window.location.href = notificationUrl;
                                
                                // Mark as read
                                if (notificationId && !$item.hasClass('bg-gray-50')) {
                                    axios.post(`/notifications/${notificationId}/mark-as-read`)
                                        .then(() => {
                                            loadNotifications();
                                            updateUnreadCount();
                                            
                                            // Trigger event to update notifications page if open (real-time sync)
                                            window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                                                detail: { notificationId: notificationId } 
                                            }));
                                        });
                                }
                                
                                // Scroll to comment after page loads
                                setTimeout(() => {
                                    const commentElement = document.getElementById('comment-' + commentId);
                                    if (commentElement) {
                                        commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        // Highlight the comment briefly
                                        commentElement.style.backgroundColor = 'rgba(24, 119, 242, 0.1)';
                                        setTimeout(() => {
                                            commentElement.style.backgroundColor = '';
                                        }, 2000);
                                    }
                                }, 500);
                                
                                e.preventDefault();
                                return false;
                            } 
                            else {
                                // Normal notification click - mark as read
                                if (notificationId && !$item.hasClass('bg-gray-50')) {
                                    axios.post(`/notifications/${notificationId}/mark-as-read`)
                                        .then(() => {
                                            loadNotifications();
                                            updateUnreadCount();
                                            
                                            // Trigger event to update notifications page if open (real-time sync)
                                            window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                                                detail: { notificationId: notificationId } 
                                            }));
                                        });
                                }
                            }
                        });
                        
                        // Three-dot menu button click handler
                        $(document).off('click', '.notification-menu-btn').on('click', '.notification-menu-btn', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            
                            // Find the menu within the same notification item container
                            const $btn = $(this);
                            const $menuContainer = $btn.closest('.notification-menu-container');
                            const $menu = $menuContainer.find('.notification-menu-dropdown');
                            
                            // Check if this menu is currently visible
                            const isVisible = $menu.css('display') === 'block' || $menu.is(':visible');
                            
                            // Close ALL menus first
                            $('.notification-menu-dropdown').css('display', 'none');
                            
                            // If this menu was not visible, show it. If it was visible, it will stay closed (toggled)
                            if (!isVisible) {
                                $menu.css('display', 'block');
                            }
                        });
                        
                        // Mark as read from menu
                        $(document).off('click', '.notification-mark-read-menu').on('click', '.notification-mark-read-menu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const $btn = $(this);
                            const $menuContainer = $btn.closest('.notification-menu-container');
                            const $menu = $menuContainer.find('.notification-menu-dropdown');
                            $menu.css('display', 'none');
                            
                            const notificationId = $btn.data('notification-id');
                            
                            axios.post(`/notifications/${notificationId}/mark-as-read`)
                                .then(response => {
                                    if (response.data.success) {
                                        loadNotifications();
                                        updateUnreadCount();
                                        
                                        // Trigger event to update notifications page if open (real-time sync)
                                        window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                                            detail: { notificationId: notificationId } 
                                        }));
                                        
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Notification marked as read',
                                                showConfirmButton: false,
                                                timer: 2000
                                            });
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error marking notification as read:', error);
                                });
                        });
                        
                        // Mark as unread from menu
                        $(document).off('click', '.notification-mark-unread-menu').on('click', '.notification-mark-unread-menu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const $btn = $(this);
                            const $menuContainer = $btn.closest('.notification-menu-container');
                            const $menu = $menuContainer.find('.notification-menu-dropdown');
                            $menu.css('display', 'none');
                            
                            const notificationId = $btn.data('notification-id');
                            
                            axios.post(`/notifications/${notificationId}/mark-as-unread`)
                                .then(response => {
                                    if (response.data.success) {
                                        loadNotifications();
                                        updateUnreadCount();
                                        
                                        // Trigger event to update notifications page if open (real-time sync)
                                        window.dispatchEvent(new CustomEvent('notificationMarkedAsUnread', { 
                                            detail: { notificationId: notificationId } 
                                        }));
                                        
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Notification marked as unread',
                                                showConfirmButton: false,
                                                timer: 2000
                                            });
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error marking notification as unread:', error);
                                });
                        });
                        
                        // Delete notification from menu
                        $(document).off('click', '.notification-delete-menu').on('click', '.notification-delete-menu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const $btn = $(this);
                            const $menuContainer = $btn.closest('.notification-menu-container');
                            const $menu = $menuContainer.find('.notification-menu-dropdown');
                            $menu.css('display', 'none');
                            
                            const notificationId = $btn.data('notification-id');
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Delete notification?',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, delete it',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        axios.delete(`/notifications/${notificationId}`)
                                            .then(response => {
                                                if (response.data.success) {
                                                    // Remove the notification item from DOM immediately
                                                    const $notificationItem = $(`.notification-item[data-notification-id="${notificationId}"]`);
                                                    $notificationItem.fadeOut(300, function() {
                                                        $(this).remove();
                                                        
                                                        // Reload notifications to refresh the list
                                                        loadNotifications();
                                                        updateUnreadCount();
                                                        
                                                        // Trigger a custom event that the notifications page can listen to (real-time sync)
                                                        window.dispatchEvent(new CustomEvent('notificationDeleted', { 
                                                            detail: { notificationId: notificationId } 
                                                        }));
                                                    });
                                                    
                                                    Swal.fire({
                                                        toast: true,
                                                        position: 'top-end',
                                                        icon: 'success',
                                                        title: 'Notification deleted',
                                                        showConfirmButton: false,
                                                        timer: 2000
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to delete notification.',
                                                });
                                            });
                                    }
                                });
                            }
                        });
                        
                        // Close menu when clicking outside
                        $(document).off('click.notification-menu').on('click.notification-menu', function(e) {
                            if (!$(e.target).closest('.notification-menu-container').length) {
                                $('.notification-menu-dropdown').css('display', 'none');
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        // Update unread count (initial load only, broadcasting will handle updates)
        function updateUnreadCount() {
            if (typeof axios === 'undefined') {
                setTimeout(updateUnreadCount, 100);
                return;
            }

            axios.get('{{ route("notifications.unread-count") }}')
                .then(response => {
                    const count = response.data.count;
                    updateAdminNotificationBadge(count);
                })
                .catch(error => {
                    console.error('Error updating unread count:', error);
                });
        }

        // Mark all as read
        $(document).on('click', '#markAllReadBtn', function(e) {
            e.stopPropagation();
            if (typeof axios === 'undefined') return;
            
            axios.post('{{ route("notifications.mark-all-read") }}')
                .then(() => {
                    loadNotifications();
                    updateUnreadCount();
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
        });

        // Load notifications on page load
        setTimeout(function() {
            loadNotifications();
            updateUnreadCount();
            
            // Reload notifications when dropdown is opened
            $('#notificationsBtn').on('click', function() {
                setTimeout(() => {
                    if ($('#notificationsDropdown').hasClass('show')) {
                        loadNotifications();
                    }
                }, 100);
            });
        }, 500);
    });
</script>

