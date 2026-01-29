<!-- Sidebar -->
<nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" style="background-color: #123a60; width: 19rem;">
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
                        <img id="sidebarProfilePicture" class="h-12 w-12 rounded-full object-cover" src="{{ $profilePic }}" alt="Profile" />
                    @else
                        <img id="sidebarProfilePicture" class="h-12 w-12 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" alt="Profile" />
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
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-300 hover:text-white' }} transition-colors" style="{{ request()->routeIs('admin.dashboard') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-tachometer-alt w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    @can('view users')
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.consec.*') || request()->routeIs('admin.board-members.*') || request()->routeIs('admin.pending-registrations.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.consec.*') || request()->routeIs('admin.board-members.*') || request()->routeIs('admin.pending-registrations.*') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-users w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">User Management</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.consec.*') || request()->routeIs('admin.board-members.*') || request()->routeIs('admin.pending-registrations.*') ? 'rotate-180' : '' }}"></i>
                        </a>
                        <ul class="mt-2 ml-4 space-y-1 pl-4 {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.consec.*') || request()->routeIs('admin.board-members.*') || request()->routeIs('admin.pending-registrations.*') ? '' : 'hidden' }}" style="border-left: 2px solid #055498;">
                            @can('view board members')
                            <li>
                                <a href="{{ route('admin.board-members.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.board-members.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.board-members.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-user-tie w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Board Members</span>
                                </a>
                            </li>
                            @endcan
                            @can('view pending registrations')
                            <li>
                                <a href="{{ route('admin.pending-registrations.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.pending-registrations.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.pending-registrations.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-clock w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Pending Registrations</span>
                                </a>
                            </li>
                            @endcan
                            @can('view consec accounts')
                            <li>
                                <a href="{{ route('admin.consec.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.consec.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.consec.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-users-cog w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">CONSEC</span>
                                </a>
                            </li>
                            @endcan
                            @can('view roles')
                            <li>
                                <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-shield-alt w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Role Management</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    @if(Auth::check() && (Auth::user()->hasPermission('view announcements') || Auth::user()->hasPermission('view notices') || Auth::user()->hasPermission('view calendar events') || Auth::user()->hasPermission('view media library')))
                   
                    <li>
                        @php
                            $isContentManagementActive = request()->routeIs('admin.media-library.*') 
                                || request()->routeIs('admin.announcements.*') 
                                || request()->routeIs('admin.notices.*')
                                || request()->routeIs('admin.calendar') 
                                || request()->is('admin/calendar');
                        @endphp
                        <a href="#" class="menu-toggle flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors {{ $isContentManagementActive ? 'text-white' : '' }}" style="{{ $isContentManagementActive ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-edit w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Content Management</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ $isContentManagementActive ? 'rotate-180' : '' }}"></i>
                        </a>
                        <ul class="mt-2 ml-4 space-y-1 pl-4 {{ $isContentManagementActive ? '' : 'hidden' }}" style="border-left: 2px solid #055498;">
                            @can('view announcements')
                            <li>
                                <a href="{{ route('admin.announcements.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.announcements.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.announcements.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-bullhorn w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Announcements</span>
                                </a>
                            </li>
                            @endcan
                            @can('view notices')
                            <li>
                                <a href="{{ route('admin.notices.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.notices.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.notices.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-bell w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Notices</span>
                                </a>
                            </li>
                            @endcan
                            @can('view calendar events')
                            <li>
                                @php
                                    $isCalendarActive = request()->routeIs('admin.calendar') || request()->is('admin/calendar');
                                @endphp
                                <a href="{{ route('admin.calendar') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ $isCalendarActive ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ $isCalendarActive ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-calendar-alt w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Calendar of Activities</span>
                                </a>
                            </li>
                            @endcan

                            @can('view media library')
                            <li>
                                <a href="{{ route('admin.media-library.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.media-library.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.media-library.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-folder-open w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Media Library</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    @can('view attendance confirmation')
                    <li>
                        <a href="{{ route('admin.attendance-confirmations.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.attendance-confirmations.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.attendance-confirmations.*') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-check-circle w-5 transition-colors" style="color: #FBD116;"></i>
                            <span class="ml-3">Attendance Confirmation</span>
                        </a>
                    </li>
                    @endcan
                    @can('view agenda requests')
                    <li style="display: none;">
                        <a href="#" onclick="event.preventDefault(); showNotApprovedModal();" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.agenda-inclusion-requests.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.agenda-inclusion-requests.*') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-clipboard-list w-5 transition-colors" style="color: #FBD116;"></i>
                            <span class="ml-3">Agenda Requests</span>
                        </a>
                    </li>
                    @endcan
                    @can('view reference materials')
                    <li style="display: none;">
                        <a href="#" onclick="event.preventDefault(); showNotApprovedModal();" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reference-materials.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.reference-materials.*') ? 'background-color:#055498;' : '' }}">
                            <i class="fas fa-book w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Reference Materials</span>
                        </a>
                    </li>
                    @endcan
                    @if(Auth::check() && (Auth::user()->hasPermission('view board regulations') || Auth::user()->hasPermission('view board resolutions') || Auth::user()->hasPermission('view referendum')))
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors {{ request()->routeIs('admin.board-resolutions.*') || request()->routeIs('admin.board-regulations.*') || request()->routeIs('admin.referendums.*') ? 'text-white' : '' }}" style="{{ request()->routeIs('admin.board-resolutions.*') || request()->routeIs('admin.board-regulations.*') || request()->routeIs('admin.referendums.*') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-file-alt w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Board Issuances</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('admin.board-resolutions.*') || request()->routeIs('admin.board-regulations.*') || request()->routeIs('admin.referendums.*') ? 'rotate-180' : '' }}"></i>
                        </a>
                        <ul class="mt-2 ml-4 space-y-1 pl-4 {{ request()->routeIs('admin.board-resolutions.*') || request()->routeIs('admin.board-regulations.*') || request()->routeIs('admin.referendums.*') ? '' : 'hidden' }}" style="border-left: 2px solid #055498;">
                            @can('view board regulations')
                            <li>
                                <a href="{{ route('admin.board-regulations.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.board-regulations.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.board-regulations.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-balance-scale w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Board Regulations</span>
                                </a>
                            </li>
                            @endcan
                            @can('view board resolutions')
                            <li>
                                <a href="{{ route('admin.board-resolutions.index') }}" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.board-resolutions.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.board-resolutions.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-file-signature w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Board Resolutions</span>
                                </a>
                            </li>
                            @endcan
                            @can('view referendum')
                            <li style="display: none;">
                                <a href="#" onclick="event.preventDefault(); showNotApprovedModal();" class="flex items-center px-3 py-2 text-sm rounded-md transition-all duration-200 group {{ request()->routeIs('admin.referendums.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.referendums.*') ? 'background-color: #055498;' : '' }}">
                                    <i class="fas fa-vote-yea w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Referendums</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    @can('view reports')
                    <li>
                        <a href="{{ route('admin.report-generation.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.report-generation.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.report-generation.*') ? 'background-color:#055498;' : '' }}">
                            <i class="fas fa-chart-bar w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Report Generation</span>
                        </a>
                    </li>
                    @endcan
                    @if(Auth::check() && Auth::user()->hasPermission('view audit logs'))
                    <li style="display: none;">
                        <a href="#" onclick="event.preventDefault(); showNotApprovedModal();" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.audit-logs.index') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.audit-logs.index') ? 'background-color:#055498;' : '' }}">
                            <i class="fas fa-clipboard-check w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Audit Logs</span>
                        </a>
                    </li>
                    @endif
                    @can('view government agencies')
                    <li>
                        <a href="#" class="menu-toggle flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-building w-5" style="color: #FBD116;"></i>
                            <span class="ml-3 flex-1">Government Agency</span>
                            <i class="fas fa-chevron-down text-xs {{ request()->routeIs('admin.government-agencies.*') ? 'rotate-180' : '' }}"></i>
                        </a>
                        <ul class="mt-2 ml-4 space-y-1 pl-4 {{ request()->routeIs('admin.government-agencies.*') ? '' : 'hidden' }}" style="border-left: 2px solid #055498;">
                            @can('view government agencies')
                            <li>
                                <a href="{{ route('admin.government-agencies.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group {{ request()->routeIs('admin.government-agencies.index') || request()->routeIs('admin.government-agencies.create') || request()->routeIs('admin.government-agencies.edit') ? 'text-white' : '' }}">
                                    <i class="fas fa-building w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Manage Agencies</span>
                                </a>
                            </li>
                            @endcan
                            @can('edit government agencies')
                            <li>
                                <a href="{{ route('admin.government-agencies.settings') }}" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:text-white rounded-md transition-all duration-200 group {{ request()->routeIs('admin.government-agencies.settings') ? 'text-white' : '' }}">
                                    <i class="fas fa-tools w-4 transition-colors" style="color: #FBD116;"></i>
                                    <span class="ml-3">Agency Settings</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    @if(Auth::check() && Auth::user()->privilege === 'admin')
                    <li>
                        <a href="{{ route('admin.address-settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.address-settings.*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ request()->routeIs('admin.address-settings.*') ? 'background-color: #055498;' : '' }}">
                            <i class="fas fa-map-marker-alt w-5" style="color: #FBD116;"></i>
                            <span class="ml-3">Address Settings</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
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
