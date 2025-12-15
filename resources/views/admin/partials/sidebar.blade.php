<!-- Sidebar -->
<nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" style="background-color: #123a60;">
    <div class="flex flex-col h-full">
        <!-- User Info -->
        <div class="p-4" style="border-bottom: 1px solid #055498;">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    @if(Auth::user()->profile_picture)
                        @php
                            $media = \App\Models\MediaLibrary::find(Auth::user()->profile_picture);
                            $profilePic = $media ? asset('storage/' . $media->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name);
                        @endphp
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ $profilePic }}" alt="Profile" />
                    @else
                        <img class="h-12 w-12 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" alt="Profile" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="online_animation mr-1"></span>
                        <span class="text-xs text-gray-400">Online</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ Auth::user()->privilege === 'admin' ? '#FBD116' : '#055498' }}; color: {{ Auth::user()->privilege === 'admin' ? '#123a60' : '#ffffff' }};">
                            {{ ucfirst(Auth::user()->privilege ?? 'user') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="p-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Admin Panel</h4>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-300 hover:text-white' }} transition-colors" style="{{ request()->routeIs('admin.dashboard') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-tachometer-alt w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-users w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">User Management</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <ul class="hidden mt-2 ml-4 space-y-1 pl-4" style="border-left: 2px solid #055498;">
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-user-tie w-4 text-gray-500 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Board Members</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-user-shield w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Authorized Representatives</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-clock w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Pending Registrations</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-user-cog w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">User Roles</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('admin.portal-manager') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.portal-manager') ? 'text-white' : 'text-gray-300 hover:text-white' }} transition-colors" style="{{ request()->routeIs('admin.portal-manager') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-cog w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Portal Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-edit w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Content Management</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <ul class="hidden mt-2 ml-4 space-y-1 pl-4" style="border-left: 2px solid #055498;">
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-bullhorn w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Announcements</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-bell w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Notices</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-file-alt w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Templates</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-folder-open w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Media Library</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-check-circle w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Attendance Confirmation</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-file-alt w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Board Issuances</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <ul class="hidden mt-2 ml-4 space-y-1 pl-4" style="border-left: 2px solid #055498;">
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-file-contract w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Board Resolutions</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-file-signature w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Official Documents</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-book w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Reference Materials</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-clipboard-list w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Request for Inclusion in the Agenda</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-chart-bar w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Report Generation</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-building w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Government Agency</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <ul class="hidden mt-2 ml-4 space-y-1 pl-4" style="border-left: 2px solid #055498;">
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-building w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Manage Agencies</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group">
                                    <i class="fas fa-tools w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Agency Settings</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

