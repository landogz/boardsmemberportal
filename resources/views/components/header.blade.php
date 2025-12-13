<!-- Top Bar - 1190x45px - Mandatory, Locked -->
<div class="top-bar sticky top-0 z-40">
    <div class="gov-container flex items-center justify-between w-full">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/republica.png') }}" 
                 alt="Republic of the Philippines" 
                 class="h-8 w-auto object-contain">
            <span class="hidden sm:inline">REPUBLIC OF THE PHILIPPINES</span>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search..." id="searchInput" class="dark:bg-gray-800 dark:text-white dark:border-gray-600">
            <button type="button" onclick="handleSearch()" class="dark:bg-blue-600">Search</button>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="sticky top-[45px] z-50 bg-white/80 dark:bg-[#0F172A]/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center">
                <img src="https://ddb.gov.ph/wp-content/uploads/2021/08/DDB_Website_Header1.png" 
                     alt="Agency Logo" 
                     class="h-10 sm:h-12 md:h-14 lg:h-16 w-auto object-contain max-h-[70px]">
            </div>
            <div class="hidden lg:flex items-center space-x-4 xl:space-x-6 flex-shrink-0">
                @php
                    $currentRoute = request()->route()->getName();
                    $isAuthPage = in_array($currentRoute, ['login', 'register']);
                    $isOtherPage = !in_array($currentRoute, ['landing', 'login', 'register']) && $currentRoute !== null;
                    $landingUrl = route('landing');
                @endphp
                <a href="{{ route('landing') }}" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap nav-link">Home</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#announcements' : '#announcements' }}" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap nav-link">Announcements</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#meetings' : '#meetings' }}" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap nav-link">Meetings</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#about' : '#about' }}" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap nav-link">About</a>
                <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#contact' : '#contact' }}" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap nav-link">Contact</a>
                <!-- Dark Mode Toggle -->
                <button id="themeToggle" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                    <span id="themeIcon" class="text-xl xl:text-2xl">🌙</span>
                </button>
                @auth
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Notifications">
                            <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Notifications</h3>
                            </div>
                            <div class="overflow-y-auto flex-1">
                                <!-- Sample Notifications -->
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">New Announcement</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Quarterly Board Meeting Scheduled</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">2 hours ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Meeting Notice</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Q1 2024 Board Meeting - Please confirm attendance</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">1 day ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Upcoming Event</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Committee Review scheduled for January 22</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">2 days ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">New Resolution</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Resolution #2024-001 has been published</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">3 days ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition" onclick="event.preventDefault(); window.openMessagesPopup && window.openMessagesPopup();">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">New Message</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">You have a new message from John Doe</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">5 days ago</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <a href="{{ route('notifications') }}" class="block text-center text-sm font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition">
                                    See All Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Messages Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Messages">
                            <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Messages</h3>
                            </div>
                            <div class="overflow-y-auto flex-1">
                                <!-- Sample Messages -->
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700" onclick="event.preventDefault(); if(window.openMessagesPopup) window.openMessagesPopup('jd', 'John Doe', 'JD');">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            JD
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">John Doe</p>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">2m</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Can we discuss the agenda for next week?</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700" onclick="event.preventDefault(); if(window.openMessagesPopup) window.openMessagesPopup('js', 'Jane Smith', 'JS');">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            JS
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Jane Smith</p>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">1h</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Thanks for the update on the resolution</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700" onclick="event.preventDefault(); if(window.openMessagesPopup) window.openMessagesPopup('mj', 'Michael Johnson', 'MJ');">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            MJ
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Michael Johnson</p>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">3h</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">I have a question about the meeting schedule</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700" onclick="event.preventDefault(); if(window.openMessagesPopup) window.openMessagesPopup('sw', 'Sarah Williams', 'SW');">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            SW
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Sarah Williams</p>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">5h</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Please review the document I shared</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition" onclick="event.preventDefault(); if(window.openMessagesPopup) window.openMessagesPopup('ab', 'Admin Board', 'AB');">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            AB
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Admin Board</p>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">1d</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">System maintenance scheduled for this weekend</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <a href="{{ route('messages') }}" class="block text-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition">
                                    See All Messages
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 px-3 xl:px-4 py-2 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white hover:shadow-lg transition whitespace-nowrap">
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
                    <a href="/login" class="px-3 xl:px-4 py-2 text-sm xl:text-base rounded-full border border-[#A855F7] hover:bg-[#A855F7] hover:text-white transition whitespace-nowrap">Login</a>
                    <a href="/register" class="px-3 xl:px-4 py-2 text-sm xl:text-base rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white hover:shadow-lg transition whitespace-nowrap">Register</a>
                @endauth
            </div>
            <div class="flex items-center space-x-2 lg:hidden flex-shrink-0">
                <button id="themeToggleMobile" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                    <span id="themeIconMobile" class="text-xl">🌙</span>
                </button>
                <button id="mobileMenuBtn" class="text-2xl min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle menu" aria-expanded="false">☰</button>
            </div>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white dark:bg-[#0F172A] border-t border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4 space-y-3">
            @php
                $currentRoute = request()->route()->getName();
                $isAuthPage = in_array($currentRoute, ['login', 'register']);
                $isOtherPage = !in_array($currentRoute, ['landing', 'login', 'register']) && $currentRoute !== null;
                $landingUrl = route('landing');
            @endphp
            <a href="{{ route('landing') }}" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center nav-link">Home</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#announcements' : '#announcements' }}" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center nav-link">Announcements</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#meetings' : '#meetings' }}" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center nav-link">Meetings</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#about' : '#about' }}" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center nav-link">About</a>
            <a href="{{ ($isAuthPage || $isOtherPage) ? $landingUrl . '#contact' : '#contact' }}" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center nav-link">Contact</a>
            @auth
                <a href="{{ route('notifications') }}" class="block px-4 py-3 rounded-full border border-[#A855F7] text-center min-h-[44px] flex items-center justify-center">
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Notifications</span>
                    </span>
                </a>
                <a href="{{ route('messages') }}" class="block px-4 py-3 rounded-full border border-[#A855F7] text-center min-h-[44px] flex items-center justify-center">
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Messages</span>
                    </span>
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-full border border-[#A855F7] text-center min-h-[44px] flex items-center justify-center">Edit Profile</a>
                <form id="logoutFormMobile" class="inline w-full">
                    <button type="submit" class="w-full block px-4 py-3 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white text-center min-h-[44px] flex items-center justify-center">Logout</button>
                </form>
            @else
                <a href="/login" class="block px-4 py-3 rounded-full border border-[#A855F7] text-center min-h-[44px] flex items-center justify-center">Login</a>
                <a href="/register" class="block px-4 py-3 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white text-center min-h-[44px] flex items-center justify-center">Register</a>
            @endauth
        </div>
    </div>
</nav>

@auth
@include('components.messages-popup')

<script>
    // Handle logout for both desktop and mobile
    function handleLogout(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#A855F7',
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
    });
</script>
@endauth

