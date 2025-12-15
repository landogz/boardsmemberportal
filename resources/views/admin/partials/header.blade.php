<!-- Topbar -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-4">
            <button id="sidebarCollapse" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800 hidden sm:block">{{ $pageTitle ?? 'Admin Dashboard' }}</h1>
        </div>
        
        <div class="flex items-center space-x-4">
            @if(isset($headerActions) && !empty($headerActions))
                @foreach($headerActions as $action)
                    <a href="{{ $action['url'] ?? '#' }}" class="{{ $action['class'] ?? 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors' }}">
                        @if(isset($action['icon']))
                            <i class="{{ $action['icon'] }} mr-2"></i>
                        @endif
                        {{ $action['text'] }}
                    </a>
                @endforeach
            @endif
            
            @if(!isset($hideDefaultActions) || !$hideDefaultActions)
            <!-- Notifications Dropdown -->
            <div class="relative dropdown">
                <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dropdown-toggle" data-toggle="dropdown">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 block h-4 w-4 rounded-full text-white text-xs flex items-center justify-center" style="background-color: #CE2028;">2</span>
                </button>
                <div class="dropdown-menu absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="p-3 border-b border-gray-200 bg-gray-50">
                        <h6 class="text-sm font-semibold text-gray-800">Notifications</h6>
                    </div>
                    <div class="py-1">
                        <a href="#" class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.2);">
                                <i class="fas fa-bullhorn" style="color: #055498;"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">New Announcement</p>
                                <p class="text-xs text-gray-600 mt-1 truncate">Quarterly Board Meeting Scheduled</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </a>
                        <a href="#" class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.2);">
                                <i class="fas fa-file-text" style="color: #055498;"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">Meeting Notice</p>
                                <p class="text-xs text-gray-600 mt-1 truncate">Q1 2024 Board Meeting - Please confirm attendance</p>
                                <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                            </div>
                        </a>
                    </div>
                    <div class="border-t border-gray-200">
                        <a href="{{ route('notifications') }}" class="block px-4 py-3 text-center text-sm font-semibold hover:bg-gray-50" style="color: #055498;">
                            See All Notifications
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Messages Dropdown -->
            <div class="relative dropdown">
                <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dropdown-toggle" data-toggle="dropdown">
                    <i class="far fa-envelope text-xl"></i>
                    <span class="absolute top-0 right-0 block h-4 w-4 rounded-full text-white text-xs flex items-center justify-center" style="background-color: #CE2028;">3</span>
                </button>
                <div class="dropdown-menu absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="p-3 border-b border-gray-200 bg-gray-50">
                        <h6 class="text-sm font-semibold text-gray-800">Messages</h6>
                    </div>
                    <div class="py-1">
                        <a href="#" class="message-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="event.preventDefault(); if(typeof window.openMessagesPopup === 'function') { window.openMessagesPopup('jd', 'John Doe', 'JD'); } $('.dropdown').removeClass('show'); return false;">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mr-3 text-white font-semibold text-sm">
                                JD
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-800">John Doe</p>
                                    <span class="text-xs text-gray-400">2m</span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">Can we discuss the agenda for next week?</p>
                            </div>
                        </a>
                        <a href="#" class="message-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="event.preventDefault(); if(typeof window.openMessagesPopup === 'function') { window.openMessagesPopup('js', 'Jane Smith', 'JS'); } $('.dropdown').removeClass('show'); return false;">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-3 text-white font-semibold text-sm">
                                JS
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-800">Jane Smith</p>
                                    <span class="text-xs text-gray-400">1h</span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">Thanks for the update on the resolution</p>
                            </div>
                        </a>
                        <a href="#" class="message-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="event.preventDefault(); if(typeof window.openMessagesPopup === 'function') { window.openMessagesPopup('mj', 'Michael Johnson', 'MJ'); } $('.dropdown').removeClass('show'); return false;">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mr-3 text-white font-semibold text-sm">
                                MJ
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-800">Michael Johnson</p>
                                    <span class="text-xs text-gray-400">3h</span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">I have a question about the meeting schedule</p>
                            </div>
                        </a>
                    </div>
                    <div class="border-t border-gray-200">
                        <a href="{{ route('messages') }}" class="block px-4 py-3 text-center text-sm font-semibold hover:bg-gray-50" style="color: #055498;">
                            See All Messages
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="relative dropdown">
                <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors dropdown-toggle" data-toggle="dropdown">
                    @if(Auth::user()->profile_picture)
                        @php
                            $media = \App\Models\MediaLibrary::find(Auth::user()->profile_picture);
                            $profilePic = $media ? asset('storage/' . $media->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name);
                        @endphp
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $profilePic }}" alt="Profile" />
                    @else
                        <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" alt="Profile" />
                    @endif
                    <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-500 hidden md:block"></i>
                </button>
                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Help</a>
                    <div class="border-t border-gray-200"></div>
                    <a href="#" id="logoutBtn" class="block px-4 py-2 text-sm hover:bg-gray-50" style="color: #CE2028;">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</header>

