<!-- Top Bar - 1190x45px - Mandatory, Locked -->
<div class="top-bar sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between w-full" style="padding-left:50px;">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/republica.png') }}" alt="Republic of the Philippines" class="h-8 w-auto object-contain">
            <span class="hidden sm:inline">REPUBLIC OF THE PHILIPPINES</span>
        </div>
        <!-- <div class="search-bar">
            <input type="text" placeholder="Search..." id="searchInput" class="dark:bg-gray-800 dark:text-white dark:border-gray-600">
            <button type="button" onclick="handleSearch()" class="dark:bg-blue-600">Search</button>
        </div> -->
    </div>
</div>

<!-- Navigation -->
<nav class="sticky top-[45px] z-50 bg-white/80 dark:bg-[#0F172A]/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between w-full min-h-[56px]">
            <div class="flex items-center gap-4">
                <a href="{{ route('landing') }}" class="flex items-center">
                    <img src="{{ asset('images/DDB_Website_Header1.png') }}" alt="Agency Logo" class="h-10 sm:h-12 md:h-14 lg:h-16 w-auto object-contain max-h-[70px]">
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-4 xl:space-x-6 flex-shrink-0 self-center">
                @php
                    $currentRoute = request()->route()->getName();
                    $isAuthPage = in_array($currentRoute, ['login', 'register']);
                    $isOtherPage = !in_array($currentRoute, ['landing', 'login', 'register']) && $currentRoute !== null;
                    $landingUrl = route('landing');
                @endphp
                <a href="{{ route('landing') }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Home</a>
                @auth
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#announcements' : '#announcements' }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Announcements</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#calendar-activities' : '#calendar-activities' }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Calendar of Activities</a>
                <a href="{{ route('board-issuances') }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Board Issuances</a>
                <a href="{{ route('referendums.index') }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Referendums</a>
                <a href="{{ route('notices.index') }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Notices</a>
                @endauth
                @guest
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#about' : '#about' }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">About</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#contact' : '#contact' }}" class="inline-flex items-center text-sm xl:text-base transition whitespace-nowrap nav-link" style="color: inherit; hover:color: #055498;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Contact</a>
                @endguest
                <!-- Dark Mode Toggle -->
                <button id="themeToggle" type="button" class="hidden p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                    <span id="themeIcon" class="text-xl xl:text-2xl">🌙</span>
                </button>
                @auth
                    <!-- Notifications Dropdown -->
                    <div class="relative dropdown" id="notificationsDropdown">
                        <button class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center dropdown-toggle" data-toggle="dropdown" id="notificationsBtn" aria-label="Notifications">
                            <i class="far fa-bell text-xl text-gray-700 dark:text-gray-300"></i>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white dark:border-gray-800 text-white text-[10px] font-bold flex items-center justify-center hidden" style="background-color: #CE2028;"></span>
                        </button>
                        <div class="dropdown-menu absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 flex flex-col" id="notificationsMenu" style="max-height: 400px; overflow-y: auto; overflow-x: visible;">
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between flex-shrink-0">
                                <h6 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h6>
                                <button id="markAllReadBtn" class="text-xs text-[#055498] hover:underline hidden">Mark all as read</button>
                            </div>
                            <div class="py-1 overflow-y-auto overflow-x-visible custom-scrollbar flex-1" id="notificationsList" style="overflow-x: visible !important;">
                                <div class="px-4 py-8 text-center">
                                    <i class="fas fa-spinner fa-spin text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
                                        </div>
                                        </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 text-center text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition" style="color: #055498;">
                                    See All Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Messages Dropdown (unique id for Alpine 3 and closing from item click) -->
                    <div id="messagesDropdownContainer" class="relative" x-data="{ open: false }" x-init="$watch('open', value => typeof handleMessagesDropdownToggle === 'function' && handleMessagesDropdownToggle(value, $el))">
                        <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Messages">
                            <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span id="messagesBadgeCount" class="absolute -top-1 -right-1 h-[18px] min-w-[18px] px-1 rounded-full text-white text-[10px] font-bold flex items-center justify-center hidden" style="background-color: #CE2028; border: 2px solid white; z-index: 10;">0</span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Messages</h3>
                                <button id="userHeaderNewMessageBtn" class="p-1.5 text-[#055498] dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors" title="New Message">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                            </div>
                            <div class="overflow-y-auto flex-1" id="messagesDropdownList">
                                <!-- Messages will be loaded here -->
                                <div class="px-4 py-8 text-center">
                                    <i class="fas fa-spinner fa-spin text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading messages...</p>
                                        </div>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <a href="{{ route('messages') }}" class="block text-center text-sm font-semibold transition" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                                    See All Messages
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 px-3 xl:px-4 py-2 rounded-full text-white hover:shadow-lg transition whitespace-nowrap" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            @if(Auth::user()->profile_picture)
                                @php
                                    $media = \App\Models\MediaLibrary::find(Auth::user()->profile_picture);
                                    $profilePic = $media ? asset('storage/' . $media->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name);
                                @endphp
                                <img src="{{ $profilePic }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline">{{ Auth::user()->first_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Edit Profile</a>
                                <form id="logoutFormNav" class="inline w-full">
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="/login" class="inline-flex items-center justify-center min-h-[44px] px-4 md:px-5 py-2.5 text-sm font-medium rounded-full transition whitespace-nowrap" style="border: 1px solid #055498; color: #055498;" onmouseover="this.style.backgroundColor='#055498'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#055498';">Login</a>
                    <a href="/register" class="inline-flex items-center justify-center min-h-[44px] px-4 md:px-5 py-2.5 text-sm font-medium rounded-full text-white hover:shadow-lg transition whitespace-nowrap" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">Register</a>
                @endauth
            </div>
            <div class="flex items-center space-x-2 md:hidden flex-shrink-0">
                @auth
                    <!-- Notifications Icon (Mobile) -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Notifications">
                        <i class="far fa-bell text-xl text-gray-700 dark:text-gray-300"></i>
                        <span id="notificationBadgeMobile" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white dark:border-gray-800 text-white text-[10px] font-bold flex items-center justify-center hidden" style="background-color: #CE2028;"></span>
                    </a>
                    <!-- Messages Icon (Mobile) -->
                    <a href="{{ route('messages') }}" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Messages">
                        <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span id="messagesBadgeCountMobile" class="absolute -top-1 -right-1 h-[18px] min-w-[18px] px-1 rounded-full text-white text-[10px] font-bold flex items-center justify-center hidden" style="background-color: #CE2028; border: 2px solid white; z-index: 10;">0</span>
                    </a>
                @endauth
                <button id="themeToggleMobile" type="button" class="hidden p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                    <span id="themeIconMobile" class="text-xl">🌙</span>
                </button>
                <button id="mobileMenuBtn" class="text-2xl min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle menu" aria-expanded="false">☰</button>
            </div>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white dark:bg-[#0F172A] border-t border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4 space-y-3">
            @php
                $currentRoute = request()->route()->getName();
                $isAuthPage = in_array($currentRoute, ['login', 'register']);
                $isOtherPage = !in_array($currentRoute, ['landing', 'login', 'register']) && $currentRoute !== null;
                $landingUrl = route('landing');
            @endphp
            <a href="{{ route('landing') }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Home</a>
            @auth
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#announcements' : '#announcements' }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Announcements</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#calendar-activities' : '#calendar-activities' }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Calendar of Activities</a>
            <a href="{{ route('board-issuances') }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Board Issuances</a>
            <a href="{{ route('referendums.index') }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Referendums</a>
            <a href="{{ route('notices.index') }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Notices</a>
            @endauth
            @guest
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#about' : '#about' }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">About</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#contact' : '#contact' }}" class="block py-2 transition text-base min-h-[44px] flex items-center nav-link" style="color: inherit;" onmouseover="this.style.color='#055498'" onmouseout="this.style.color='inherit'">Contact</a>
            @endguest
            @auth
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-full text-center min-h-[44px] flex items-center justify-center" style="border: 1px solid #055498; color: #055498;">Edit Profile</a>
                <form id="logoutFormMobile" class="inline w-full">
                    <button type="submit" class="w-full block px-4 py-3 rounded-full text-white text-center min-h-[44px] flex items-center justify-center" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">Logout</button>
                </form>
            @else
                <a href="/login" class="block px-4 py-3 rounded-full text-center min-h-[44px] flex items-center justify-center" style="border: 1px solid #055498; color: #055498;">Login</a>
                <a href="/register" class="block px-4 py-3 rounded-full text-white text-center min-h-[44px] flex items-center justify-center" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">Register</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle functionality - jQuery + fallback, works on all pages
    (function() {
        function bindMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');

            if (!mobileMenuBtn || !mobileMenu) {
                return;
            }

            // jQuery handler (preferred)
            if (typeof $ !== 'undefined') {
                $('#mobileMenuBtn').off('click.mobileMenu').on('click.mobileMenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    $('#mobileMenu').toggleClass('hidden');
                    $(this).attr('aria-expanded', (!isExpanded).toString());
                });
            } else {
                // Vanilla JS fallback
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
                    mobileMenu.classList.toggle('hidden');
                    mobileMenuBtn.setAttribute('aria-expanded', (!isExpanded).toString());
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindMobileMenu);
        } else {
            bindMobileMenu();
        }
    })();
</script>

@auth
@if(request()->route()->getName() !== 'messages')
@include('components.messages-popup')
@endif
@include('components.pending-notices-alert')

<script>
    // Handle logout for both desktop and mobile
    function handleLogout(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#055498',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set up axios defaults
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                
                axios.post('{{ route("logout") }}')
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.data.redirect;
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while logging out.',
                        });
                    });
            }
        });
    }

    // ========== MESSAGES DROPDOWN SYSTEM ==========
    function loadMessagesDropdown() {
        const messagesList = document.getElementById('messagesDropdownList');
        if (!messagesList) return;

        // Check if already loaded (avoid multiple loads)
        if (messagesList.dataset.loaded === 'true') return;

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
                renderMessagesDropdown(data.conversations);
                messagesList.dataset.loaded = 'true';
            } else {
                messagesList.innerHTML = `
                    <div class="px-4 py-8 text-center">
                        <i class="fas fa-comment-slash text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No messages yet</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            messagesList.innerHTML = `
                <div class="px-4 py-8 text-center">
                    <p class="text-sm text-red-500 dark:text-red-400">Error loading messages</p>
                </div>
            `;
        });
    }

    function renderMessagesDropdown(conversations) {
        const messagesList = document.getElementById('messagesDropdownList');
        if (!messagesList) return;

        if (conversations.length === 0) {
            messagesList.innerHTML = `
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-comment-slash text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No messages yet</p>
                </div>
            `;
            return;
        }

        let html = '';
        let totalUnread = 0;
        conversations.forEach(conv => {
            const initials = conv.user_initials || 'U';
            const userName = conv.user_name || 'User';
            const lastMessage = conv.last_message || '';
            const timeAgo = getTimeAgo(conv.last_message_time);
            const unreadCount = conv.unread_count || 0;
            totalUnread += unreadCount;
            
            // Get avatar HTML with badge
            let avatarHtml = '';
            if (conv.profile_picture_url) {
                avatarHtml = `<div class="relative flex-shrink-0">
                    <img src="${conv.profile_picture_url}" alt="${userName}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                    ${unreadCount > 0 ? `<span class="absolute top-0 left-0 h-5 min-w-[20px] px-1.5 rounded-full text-white text-[10px] font-bold flex items-center justify-center" style="background-color: #CE2028;">${unreadCount > 99 ? '99+' : unreadCount}</span>` : ''}
                </div>`;
            } else {
                avatarHtml = `<div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">${initials}</div>
                    ${unreadCount > 0 ? `<span class="absolute top-0 left-0 h-5 min-w-[20px] px-1.5 rounded-full text-white text-[10px] font-bold flex items-center justify-center" style="background-color: #CE2028;">${unreadCount > 99 ? '99+' : unreadCount}</span>` : ''}
                </div>`;
            }

            // Store conversation data in data attributes
            const convDataJson = JSON.stringify({
                user_id: conv.user_id,
                profile_picture_url: conv.profile_picture_url || null,
                user_initials: initials,
                is_online: conv.is_online || false
            });
            
            html += `
                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 relative" 
                   data-user-id="${conv.user_id}" 
                   data-user-name="${userName.replace(/"/g, '&quot;')}" 
                   data-conv-data='${convDataJson.replace(/'/g, "&#39;")}'
                   onclick="event.preventDefault(); 
                    if (window.location.pathname === '/messages' || window.location.pathname === '/admin/messages') {
                        if (typeof window.openChat === 'function') {
                            const convDataAttr = this.getAttribute('data-conv-data');
                            const convData = convDataAttr ? JSON.parse(convDataAttr) : {user_id: this.getAttribute('data-user-id')};
                            const userName = this.getAttribute('data-user-name');
                            const userId = this.getAttribute('data-user-id');
                            window.openChat(userId, userName, convData);
                        } else {
                            window.location.href = window.location.pathname.includes('/admin') ? '/admin/messages' : '/messages';
                        }
                    } else {
                        if(window.openMessagesPopup) {
                            const userId = this.getAttribute('data-user-id');
                            const userName = this.getAttribute('data-user-name');
                            const convDataAttr = this.getAttribute('data-conv-data');
                            const convData = convDataAttr ? JSON.parse(convDataAttr) : {};
                            window.openMessagesPopup(userId, userName, convData.user_initials || 'U');
                        }
                    }
                    var _md = document.getElementById('messagesDropdownContainer');
                    if (_md && typeof setMessagesDropdownOpen === 'function') setMessagesDropdownOpen(_md, false);
                    return false;">
                    <div class="flex items-start space-x-3">
                        ${avatarHtml}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1 gap-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white truncate flex-1 min-w-0">${userName}</p>
                                <span class="text-xs text-gray-600 dark:text-gray-400 flex-shrink-0 whitespace-nowrap font-medium">${timeAgo}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${escapeHtml(lastMessage)}</p>
                        </div>
                    </div>
                </a>
            `;
        });

        messagesList.innerHTML = html;
        
        // Use unread-count API for badge (single source of truth; avoids stale total from conversations)
        loadUnreadCount();
    }

    // Update messages badge count (0 = hide badge and clear text)
    function updateMessagesBadge(count) {
        const badgeCount = document.getElementById('messagesBadgeCount');
        const badgeCountMobile = document.getElementById('messagesBadgeCountMobile');
        const n = Number(count);
        const badgeText = n > 99 ? '99+' : String(n);
        
        if (n > 0) {
            if (badgeCount) {
                badgeCount.textContent = badgeText;
                badgeCount.classList.remove('hidden');
            }
            if (badgeCountMobile) {
                badgeCountMobile.textContent = badgeText;
                badgeCountMobile.classList.remove('hidden');
            }
        } else {
            if (badgeCount) {
                badgeCount.textContent = '';
                badgeCount.classList.add('hidden');
            }
            if (badgeCountMobile) {
                badgeCountMobile.textContent = '';
                badgeCountMobile.classList.add('hidden');
            }
        }
    }
    window.updateMessagesBadge = updateMessagesBadge;

    // Update dropdown item badge for a specific user (header badge always from API)
    function updateDropdownItemBadge(userId, unreadCount, totalUnread) {
        const messagesList = document.getElementById('messagesDropdownList');
        if (!messagesList) {
            if (typeof loadUnreadCount === 'function') loadUnreadCount(true);
            return;
        }
        
        const dropdownItem = messagesList.querySelector(`[data-user-id="${userId}"]`);
        if (!dropdownItem) {
            if (typeof window.reloadMessagesDropdown === 'function') window.reloadMessagesDropdown();
            if (typeof loadUnreadCount === 'function') loadUnreadCount(true);
            return;
        }
        
        const avatarContainer = dropdownItem.querySelector('.relative.flex-shrink-0');
        if (!avatarContainer) {
            if (typeof loadUnreadCount === 'function') loadUnreadCount(true);
            return;
        }
        
        // Find existing badge - try multiple selectors
        let badge = avatarContainer.querySelector('span[style*="#CE2028"]') || 
                   avatarContainer.querySelector('span[style*="background-color: #CE2028"]') ||
                   avatarContainer.querySelector('span[style*="background-color:#CE2028"]') ||
                   avatarContainer.querySelector('span.absolute.top-0.left-0');
        
        // If still not found, check all spans in the container
        if (!badge) {
            const allSpans = avatarContainer.querySelectorAll('span');
            allSpans.forEach(span => {
                const bgColor = span.style.backgroundColor;
                const styleAttr = span.getAttribute('style') || '';
                if (bgColor === 'rgb(206, 32, 40)' || 
                    bgColor === '#CE2028' ||
                    styleAttr.includes('#CE2028') ||
                    (span.classList.contains('absolute') && span.classList.contains('top-0') && span.classList.contains('left-0'))) {
                    badge = span;
                }
            });
        }
        
        if (unreadCount > 0) {
            const badgeText = unreadCount > 99 ? '99+' : unreadCount;
            if (badge) {
                badge.textContent = badgeText;
                badge.style.display = '';
                badge.style.backgroundColor = '#CE2028';
            } else {
                // Create new badge
                badge = document.createElement('span');
                badge.className = 'absolute top-0 left-0 h-5 min-w-[20px] px-1.5 rounded-full text-white text-[10px] font-bold flex items-center justify-center';
                badge.style.backgroundColor = '#CE2028';
                badge.textContent = badgeText;
                avatarContainer.appendChild(badge);
            }
        } else {
            // Remove badge if count is 0
            if (badge) {
                badge.remove();
            }
        }
        
        // Header badge: always from API so it never shows stale count
        if (typeof loadUnreadCount === 'function') loadUnreadCount(true);
    }
    
    // Make function globally accessible
    window.updateDropdownItemBadge = updateDropdownItemBadge;

    // Reload dropdown messages (make it globally accessible)
    window.reloadMessagesDropdown = function() {
        const messagesList = document.getElementById('messagesDropdownList');
        if (messagesList) {
            messagesList.dataset.loaded = 'false';
            loadMessagesDropdown();
        }
    };

    // Load unread count (always use cache-bust so badge is never stale)
    function loadUnreadCount(forceRefresh) {
        const url = '{{ route("messages.unread-count") }}?t=' + Date.now();
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
            },
            credentials: 'same-origin',
            cache: 'no-store'
        })
        .then(response => response.json())
        .then(data => {
            const count = (data && data.success && typeof data.count !== 'undefined') ? Number(data.count) : 0;
            updateMessagesBadge(count);
        })
        .catch(error => {
            console.error('Error loading unread count:', error);
            updateMessagesBadge(0);
        });
    }
    // Ensure badge hides when count is 0 (defensive)
    function refreshMessagesBadgeFromServer() {
        loadUnreadCount(true);
    }
    window.refreshMessagesBadgeFromServer = refreshMessagesBadgeFromServer;
    
    // Make function globally accessible for real-time updates
    window.loadUnreadCount = loadUnreadCount;

    // Alpine 3 compatible: get open state from messages dropdown element
    function getMessagesDropdownOpen(dropdownEl) {
        if (!dropdownEl) return false;
        if (typeof Alpine !== 'undefined' && Alpine.$data) {
            try {
                var d = Alpine.$data(dropdownEl);
                return d && d.open === true;
            } catch (e) { return false; }
        }
        if (dropdownEl.__x && dropdownEl.__x.$data) return dropdownEl.__x.$data.open === true;
        return false;
    }
    function setMessagesDropdownOpen(dropdownEl, open) {
        if (!dropdownEl) return;
        if (typeof Alpine !== 'undefined' && Alpine.$data) {
            try {
                var d = Alpine.$data(dropdownEl);
                if (d && typeof d.open !== 'undefined') d.open = open;
            } catch (e) {}
            return;
        }
        if (dropdownEl.__x && dropdownEl.__x.$data) dropdownEl.__x.$data.open = open;
    }
    window.setMessagesDropdownOpen = setMessagesDropdownOpen;
    window.getMessagesDropdownOpen = getMessagesDropdownOpen;

    // Handle messages dropdown toggle with periodic refresh
    function handleMessagesDropdownToggle(isOpen, dropdownElement) {
        if (isOpen) {
            setTimeout(() => loadMessagesDropdown(), 100);
            // Set up periodic refresh when dropdown is open (every 10 seconds)
            if (window.userMessagesDropdownInterval) {
                clearInterval(window.userMessagesDropdownInterval);
            }
            window.userMessagesDropdownInterval = setInterval(function() {
                if (getMessagesDropdownOpen(dropdownElement)) {
                    const messagesList = document.getElementById('messagesDropdownList');
                    if (messagesList) {
                        messagesList.dataset.loaded = 'false';
                        loadMessagesDropdown();
                    }
                } else {
                    clearInterval(window.userMessagesDropdownInterval);
                    window.userMessagesDropdownInterval = null;
                }
            }, 10000); // Refresh every 10 seconds
        } else {
            const list = document.getElementById('messagesDropdownList');
            if (list) {
                list.dataset.loaded = 'false';
            }
            // Clear refresh interval when dropdown is closed
            if (window.userMessagesDropdownInterval) {
                clearInterval(window.userMessagesDropdownInterval);
                window.userMessagesDropdownInterval = null;
            }
        }
    }

    // Handle new message button click from header dropdown
    function handleHeaderNewMessageClick(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Check if we're on messages page
        if (window.location.pathname === '/messages') {
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
            window.location.href = '{{ route('messages') }}#new-message';
        }
        
        // Close messages dropdown (Alpine 3 compatible)
        const messagesDropdown = document.getElementById('messagesDropdownContainer');
        if (messagesDropdown) setMessagesDropdownOpen(messagesDropdown, false);
        
        return false;
    }

    // Attach event listener to new message button when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const newMessageBtn = document.getElementById('userHeaderNewMessageBtn');
        if (newMessageBtn) {
            newMessageBtn.addEventListener('click', handleHeaderNewMessageClick);
        }
    });

    // ========== LARAVEL BROADCASTING + REVERB ==========
    @auth
    // Initialize Echo/Reverb connection
    let echo = null;
    
    // Load Laravel Echo and Reverb
    function loadBroadcastingScripts() {
        // Load Pusher first (required by Echo)
        if (typeof window.Pusher === 'undefined') {
            const pusherScript = document.createElement('script');
            pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
            pusherScript.onload = function() {
                // After Pusher loads, load Echo
                loadEcho();
            };
            pusherScript.onerror = function() {
                console.error('Failed to load Pusher');
            };
            document.head.appendChild(pusherScript);
        } else {
            loadEcho();
        }
    }

    function loadEcho() {
        if (typeof window.Echo === 'undefined') {
            // Use cdnjs which provides UMD build for browsers
            const echoScript = document.createElement('script');
            echoScript.type = 'text/javascript';
            echoScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.16.1/echo.iife.js';
            echoScript.onload = function() {
                initializeEcho();
            };
            echoScript.onerror = function() {
                // Fallback to unpkg if cdnjs fails
                console.warn('cdnjs failed, trying unpkg...');
                const fallbackScript = document.createElement('script');
                fallbackScript.type = 'text/javascript';
                fallbackScript.src = 'https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js';
                fallbackScript.onload = function() {
                    initializeEcho();
                };
                fallbackScript.onerror = function() {
                    console.error('Failed to load Laravel Echo from all sources');
                };
                document.head.appendChild(fallbackScript);
            };
            document.head.appendChild(echoScript);
        } else {
            initializeEcho();
        }
    }

    function initializeEcho() {
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
        
        // Initialize Echo with Reverb (only if not already initialized)
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
                    enabledTransports: useTLS ? ['wss'] : ['ws'],
                    disableStats: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    }
                });

                echo = window.echoInstance;

                // Add connection event listeners
                echo.connector.pusher.connection.bind('connected', function() {
                    // Reverb WebSocket connected successfully
                });

                echo.connector.pusher.connection.bind('disconnected', function() {
                    console.warn('⚠️ Reverb WebSocket disconnected');
                });

                echo.connector.pusher.connection.bind('error', function(err) {
                    console.error('❌ Reverb WebSocket error:', err);
                });

                echo.connector.pusher.connection.bind('state_change', function(states) {
                    // Reverb connection state changed: states.current
                });

                // Listen to message unread count updates (realtime badge)
                echo.private(`user.${userId}`)
                    .listen('.message.unread-count.updated', (e) => {
                        const count = typeof e.count !== 'undefined' ? Number(e.count) : 0;
                        updateMessagesBadge(count);
                        if (count === 0) loadUnreadCount(true);
                        const messagesDropdown = document.getElementById('messagesDropdownContainer');
                        if (messagesDropdown && getMessagesDropdownOpen(messagesDropdown)) {
                            const messagesList = document.getElementById('messagesDropdownList');
                            if (messagesList) {
                                messagesList.dataset.loaded = 'false';
                                loadMessagesDropdown();
                            }
                        }
                    });

                // Listen to notification unread count updates
                echo.private(`user.${userId}`)
                    .listen('.notification.unread-count.updated', (e) => {
                        updateNotificationBadge(e.count);
                        loadNotifications();
                    });

                // Message unsent elsewhere: dispatch so messages page can show "This message was deleted" in real time
                echo.private(`user.${userId}`)
                    .listen('.message.content.deleted', (e) => {
                        try {
                            window.dispatchEvent(new CustomEvent('message-content-deleted', { detail: e }));
                        } catch (err) {
                            console.warn('message-content-deleted handler:', err);
                        }
                    });

                // Laravel Echo initialized successfully
                // Connecting to Reverb at: reverbScheme + '://' + reverbHost + ':' + reverbPort
                
                // Check connection status after 3 seconds (silently, only log if in development)
                setTimeout(function() {
                    const state = echo.connector.pusher.connection.state;
                    if (state !== 'connected') {
                        // Only show warning in development mode
                        const isDevelopment = '{{ env("APP_ENV", "production") }}' === 'local';
                        if (isDevelopment) {
                            console.warn('⚠️ Reverb connection not established. Current state:', state);
                            // Tips:
                            // 1. Reverb server is running: php artisan reverb:start
                            // 2. .env has correct REVERB_APP_KEY, REVERB_APP_ID, REVERB_APP_SECRET
                            // 3. REVERB_SCHEME=http for local development
                        }
                        // Silently handle connection failure in production
                        // Broadcasting will work via polling/fallback methods
                    }
                }, 3000);
            } catch (error) {
                console.error('Error initializing Echo:', error);
            }
        } else {
            echo = window.echoInstance;
        }
    }

    // Start loading scripts when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadBroadcastingScripts);
    } else {
        loadBroadcastingScripts();
    }

    @endauth

    // Update notification badge helper (available globally); count 0 = hide and clear
    function updateNotificationBadge(count) {
        const badge = $('#notificationBadge');
        const badgeMobile = $('#notificationBadgeMobile');
        const n = Number(count);
        if (n > 0) {
            const badgeText = n > 99 ? '99+' : String(n);
            badge.text(badgeText).removeClass('hidden');
            badgeMobile.text(badgeText).removeClass('hidden');
        } else {
            badge.text('').addClass('hidden');
            badgeMobile.text('').addClass('hidden');
        }
    }
    window.updateNotificationBadge = updateNotificationBadge;

    function getTimeAgo(timestamp) {
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

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Attach logout handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const logoutFormNav = document.getElementById('logoutFormNav');
        const logoutFormMobile = document.getElementById('logoutFormMobile');
        
        if (logoutFormNav) {
            logoutFormNav.addEventListener('submit', handleLogout);
        }
        if (logoutFormMobile) {
            logoutFormMobile.addEventListener('submit', handleLogout);
        }
        
        // Load unread message count on initial page load (always fetch fresh so badge is correct)
        loadUnreadCount(true);
        // Run again after short delay to override any stale badge set by Echo or dropdown
        setTimeout(function() { loadUnreadCount(true); }, 600);
    });

    // ========== NOTIFICATIONS DROPDOWN SYSTEM ==========
    $(document).ready(function() {
        // Dropdown Toggle
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if mobile device (screen width <= 640px)
            const isMobile = window.innerWidth <= 640;
            
            // Get the dropdown and check if it's notifications
            const $dropdown = $(this).parent('.dropdown');
            const isNotifications = $dropdown.attr('id') === 'notificationsDropdown';
            
            // On mobile, redirect to full page instead of opening dropdown
            if (isMobile && isNotifications) {
                window.location.href = '{{ route("notifications.index") }}';
                return;
            }
            
            // Desktop behavior: toggle dropdown
            // Close all other dropdowns (both jQuery and Alpine.js)
            $('.dropdown').not($dropdown).removeClass('show');
            
            // Close Alpine.js message dropdown if it's open
            closeAlpineMessagesDropdown();
            
            // Toggle current dropdown
            $dropdown.toggleClass('show');
        });
        
        // Function to close Alpine.js messages dropdown (Alpine 3 compatible)
        function closeAlpineMessagesDropdown() {
            const messagesContainer = document.getElementById('messagesDropdownContainer');
            if (messagesContainer) setMessagesDropdownOpen(messagesContainer, false);
        }
        
        // Close jQuery dropdowns when messages dropdown button is clicked
        $(document).on('click', '#messagesDropdownContainer button', function(e) {
            setTimeout(function() {
                $('.dropdown').removeClass('show');
            }, 10);
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length && !$(e.target).closest('#messagesDropdownContainer').length) {
                $('.dropdown').removeClass('show');
                closeAlpineMessagesDropdown();
            }
        });
        
        // Close dropdown on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.dropdown').removeClass('show');
            }
        });
        
        // Close dropdown when it loses focus
        $(document).on('focusout', '.dropdown', function(e) {
            const $dropdown = $(this);
            const relatedTarget = e.relatedTarget;
            
            // Use setTimeout to allow click events to fire first
            setTimeout(function() {
                // Check if focus moved to an element outside the dropdown
                if (relatedTarget && !$dropdown[0].contains(relatedTarget)) {
                    $dropdown.removeClass('show');
                } else {
                    // Check current active element
                    const activeElement = document.activeElement;
                    if (activeElement && !$dropdown[0].contains(activeElement)) {
                        $dropdown.removeClass('show');
                    }
                }
            }, 150);
        });
        
        // Also handle focusout on the dropdown menu content
        $(document).on('focusout', '.dropdown-menu', function(e) {
            const $menu = $(this);
            const $dropdown = $menu.closest('.dropdown');
            const relatedTarget = e.relatedTarget;
            
            setTimeout(function() {
                // If focus moved outside the dropdown container, close it
                if (relatedTarget && !$dropdown[0].contains(relatedTarget)) {
                    $dropdown.removeClass('show');
                } else {
                    const activeElement = document.activeElement;
                    if (activeElement && !$dropdown[0].contains(activeElement)) {
                        $dropdown.removeClass('show');
                    }
                }
            }, 150);
        });

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
            'group_chat_added': 'fa-user-plus',
            'default': 'fa-bell'
        };

        // Load notifications
        function loadNotifications() {
            if (typeof axios === 'undefined') {
                setTimeout(loadNotifications, 100);
                return;
            }

            axios.get('{{ route("notifications.recent") }}', { params: { limit: 10, t: Date.now() } })
                .then(response => {
                    const notifications = (response.data && response.data.notifications) ? response.data.notifications.slice(0, 10) : [];
                    const notificationsList = $('#notificationsList');
                    const notificationBadge = $('#notificationBadge');
                    const markAllReadBtn = $('#markAllReadBtn');
                    
                    // Update badge count (use shared helper so 0 reliably hides badge)
                    axios.get('{{ route("notifications.unread-count") }}', { headers: { 'Cache-Control': 'no-cache' } })
                        .then(countResponse => {
                            const count = countResponse.data && typeof countResponse.data.count !== 'undefined' ? Number(countResponse.data.count) : 0;
                            if (typeof updateNotificationBadge === 'function') {
                                updateNotificationBadge(count);
                            } else {
                                const notificationBadgeMobile = $('#notificationBadgeMobile');
                                if (count > 0) {
                                    const badgeText = count > 99 ? '99+' : String(count);
                                    notificationBadge.text(badgeText).removeClass('hidden');
                                    notificationBadgeMobile.text(badgeText).removeClass('hidden');
                                } else {
                                    notificationBadge.text('').addClass('hidden');
                                    notificationBadgeMobile.text('').addClass('hidden');
                                }
                            }
                            if (count > 0) markAllReadBtn.removeClass('hidden'); else markAllReadBtn.addClass('hidden');
                        });
                    
                    // Render notifications (max 3)
                    if (notifications.length === 0) {
                        notificationsList.html(`
                            <div class="px-4 py-8 text-center">
                                <i class="fas fa-bell-slash text-gray-400 dark:text-gray-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                            </div>
                        `);
                    } else {
                        let html = '';
                        notifications.forEach(notification => {
                            const icon = notificationIcons[notification.type] || notificationIcons.default;
                            const bgClass = notification.is_read ? '' : 'bg-blue-50 dark:bg-blue-900/20';
                            const fontWeight = notification.is_read ? 'font-normal' : 'font-semibold';
                            
                            html += `
                                <div class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 ${bgClass} relative group" data-notification-id="${notification.id}">
                                    <a href="${notification.url || '#'}" class="flex items-start flex-1 min-w-0" ${notification.url ? '' : 'onclick="event.preventDefault(); return false;"'}>
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.15);">
                                            <i class="fas ${icon}" style="color: #055498; font-size: 14px;"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm ${fontWeight} text-gray-800 dark:text-white mb-0.5 leading-tight">${notification.title}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 truncate leading-relaxed mt-0.5">${notification.message}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">${notification.created_at}</p>
                                        </div>
                                    </a>
                                    ${!notification.is_read ? '<div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                                    <div class="notification-menu-container relative flex-shrink-0 ml-2">
                                        <button type="button" class="notification-menu-btn w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-opacity opacity-0 group-hover:opacity-100" data-notification-id="${notification.id}">
                                            <i class="fas fa-ellipsis-h text-gray-600 dark:text-gray-400 text-sm"></i>
                                        </button>
                                        <div class="notification-menu-dropdown absolute right-0 top-8 bg-gray-800 dark:bg-gray-700 rounded-lg shadow-xl border border-gray-700 dark:border-gray-600 min-w-[180px]" data-menu-id="${notification.id}" style="z-index: 9999 !important; display: none;">
                                            <div class="py-1">
                                                ${!notification.is_read ? `
                                                <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
                                                    <i class="fas fa-check text-sm"></i>
                                                    Mark as read
                                                </button>
                                                ` : `
                                                <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
                                                    <i class="fas fa-check text-sm"></i>
                                                    Mark as unread
                                                </button>
                                                `}
                                                <button type="button" class="notification-delete-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notification.id}">
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
                        $(document).on('click', function(e) {
                            if (!$(e.target).closest('.notification-menu-container').length) {
                                $('.notification-menu-dropdown').css('display', 'none');
                            }
                        });
                        
                        // Mark as read when clicked and handle comment scrolling
                        $('.notification-item a').off('click.notification').on('click.notification', function(e) {
                            const $item = $(this).closest('.notification-item');
                            const notificationId = $item.data('notification-id');
                            const notificationUrl = $(this).attr('href');
                            
                            // If URL contains a comment hash, handle scrolling
                            if (notificationUrl && notificationUrl.includes('#comment-')) {
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
                            // If URL is for an announcement, open modal instead
                            else if (notificationUrl && notificationUrl.includes('/announcements/')) {
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
                            else {
                                // Normal notification click
                                const $item = $(this).closest('.notification-item');
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
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        // Listen for all notifications marked as read event
        window.addEventListener('allNotificationsMarkedAsRead', function() {
            // Reload notifications to refresh the popup
            loadNotifications();
            updateUnreadCount();
        });
        
        // Update unread count (initial load only, broadcasting will handle updates)
        function updateUnreadCount() {
            if (typeof axios === 'undefined') {
                setTimeout(updateUnreadCount, 100);
                return;
            }

            axios.get('{{ route("notifications.unread-count") }}', { headers: { 'Cache-Control': 'no-cache' }, params: { t: Date.now() } })
                .then(response => {
                    const count = response.data && typeof response.data.count !== 'undefined' ? Number(response.data.count) : 0;
                    updateNotificationBadge(count);
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

    // Function to show "Not Approved" modal
    function showNotApprovedModal() {
        Swal.fire({
            icon: 'info',
            title: 'Function Not Yet Approved',
            text: 'This function is currently under development and has not been approved yet.',
            confirmButtonColor: '#055498',
            confirmButtonText: 'OK'
        });
    }
</script>

<style>
    /* Dropdown styles */
    .dropdown-menu {
        display: none !important;
    }
    
    .dropdown.show .dropdown-menu {
        display: flex !important;
    }
    
    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Notification items */
    .notification-menu-dropdown {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 9999 !important;
    }
    
    .notification-menu-dropdown button {
        cursor: pointer;
    }
    
    .notification-menu-dropdown button:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .notification-menu-container {
        z-index: 100;
        position: relative;
    }
    
    .notification-menu-dropdown[style*="display: block"] {
        display: block !important;
    }
    
    #notificationsList .notification-item {
        position: relative;
        overflow: visible;
    }
    
    .notification-item {
        transition: background-color 0.2s ease;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        #notificationsMenu {
            max-width: calc(100vw - 1rem) !important;
            max-height: 60vh !important;
            right: 0.5rem !important;
        }
    }
</style>
@endauth

<!-- Announcement Modal - Available on all pages -->
@include('components.announcement-modal')

