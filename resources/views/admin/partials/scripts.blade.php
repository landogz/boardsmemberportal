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
                    window.location.href = '{{ route("messages") }}';
                    return;
                }
            }
            
            // Desktop behavior: toggle dropdown
            // Close all other dropdowns
            $('.dropdown').not($dropdown).removeClass('show');
            
            // Toggle current dropdown
            $dropdown.toggleClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
            }
        });
        
        // Close dropdown when losing focus (blur event)
        $(document).on('focusout', '.dropdown', function(e) {
            // Check if focus is moving outside the dropdown
            const $dropdown = $(this);
            setTimeout(function() {
                if (!$dropdown.find(':focus').length && !$dropdown.is(':hover')) {
                    $dropdown.removeClass('show');
                }
            }, 100);
        });
        
        // Close dropdown on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.dropdown').removeClass('show');
            }
        });

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
        const IDLE_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
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
                // User has been idle for 30 minutes, show warning
                Swal.fire({
                    title: 'Session Timeout',
                    text: 'You have been idle for 30 minutes. You will be logged out for security.',
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
                        lastActivityTime = Date.now();
                    }
                },
                error: function() {
                    // If ping fails, user might be logged out
                    console.log('Activity ping failed');
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
                pingServer();
                trackActivity();
            }
        });

        // Ping server when window gains focus
        $(window).on('focus', function() {
            pingServer();
            trackActivity();
        });

        // ========== NOTIFICATIONS SYSTEM ==========
        // Load axios if not already loaded
        if (typeof axios === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';
            document.head.appendChild(script);
        }

        // Set axios defaults
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
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
                    const notifications = response.data.notifications.slice(0, 3); // Ensure max 3
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
                                <a href="${notification.url || '#'}" class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 ${bgClass}" data-notification-id="${notification.id}" ${notification.url ? '' : 'onclick="event.preventDefault(); return false;"'}>
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.15);">
                                        <i class="fas ${icon}" style="color: #055498; font-size: 14px;"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm ${fontWeight} text-gray-800 mb-0.5 leading-tight">${notification.title}</p>
                                        <p class="text-xs text-gray-600 truncate leading-relaxed mt-0.5">${notification.message}</p>
                                        <p class="text-xs text-gray-400 mt-1.5">${notification.created_at}</p>
                                    </div>
                                </a>
                            `;
                        });
                        notificationsList.html(html);
                        
                        // Mark as read when clicked
                        $('.notification-item').off('click.notification').on('click.notification', function() {
                            const notificationId = $(this).data('notification-id');
                            if (notificationId && !$(this).hasClass('bg-gray-50')) {
                                axios.post(`/notifications/${notificationId}/mark-as-read`)
                                    .then(() => {
                                        loadNotifications();
                                        updateUnreadCount();
                                    });
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        // Update unread count
        function updateUnreadCount() {
            if (typeof axios === 'undefined') {
                setTimeout(updateUnreadCount, 100);
                return;
            }

            axios.get('{{ route("notifications.unread-count") }}')
                .then(response => {
                    const count = response.data.count;
                    const badge = $('#notificationBadge');
                    if (count > 0) {
                        badge.text(count).removeClass('hidden');
                    } else {
                        badge.addClass('hidden');
                    }
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
            
            // Poll for new notifications every 30 seconds
            setInterval(function() {
                updateUnreadCount();
                // Only reload full list if dropdown is open
                if ($('#notificationsDropdown').hasClass('show')) {
                    loadNotifications();
                }
            }, 30000);
            
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

