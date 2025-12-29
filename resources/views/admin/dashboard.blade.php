@extends('admin.layout')

@section('title', 'Dashboard')

@php
    $pageTitle = 'Dashboard';
    
    // Calculate media statistics
    $mediaFilesCount = \App\Models\MediaLibrary::count();
    $totalStorageBytes = 0;
    
    // Calculate total storage size using Storage facade
    $mediaFiles = \App\Models\MediaLibrary::all();
    foreach ($mediaFiles as $media) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path)) {
            $totalStorageBytes += \Illuminate\Support\Facades\Storage::disk('public')->size($media->file_path);
        }
    }
    
    // Convert bytes to MB
    $totalStorageMB = round($totalStorageBytes / (1024 * 1024), 2);
    
    // Calculate Board Members count (users with privilege = 'user' or representative_type = 'Board Member')
    $boardMembersCount = \App\Models\User::where(function($query) {
        $query->where('privilege', 'user')
              ->orWhere('representative_type', 'Board Member');
    })->count();
    
    // Calculate Authorized Reps count
    $authorizedRepsCount = \App\Models\User::where('representative_type', 'Authorized Representative')->count();
    
    // Calculate Audit Logs count
    $auditLogsCount = \App\Models\AuditLog::count();
    
    // Calculate Today's Activities count
    $todayActivitiesCount = \App\Models\AuditLog::whereDate('created_at', \Carbon\Carbon::today())->count();
@endphp

@section('content')
<div class="p-4 sm:p-6">
                <!-- Page Title -->
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard</h2>
                </div>
                
                <!-- Secondary Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 sm:mb-6">
        <div class="rounded-lg shadow-sm p-4 sm:p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='{{ route('admin.board-members.index') }}'">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <i class="fas fa-users text-xl sm:text-2xl opacity-80"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Board Members</span>
                                <strong class="text-lg sm:text-xl" id="boardMembers">{{ $boardMembersCount }}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Authorized Reps</span>
                                <strong class="text-lg sm:text-xl" id="authorizedReps">{{ $authorizedRepsCount }}</strong>
                </div>
            </div>
        </div>
                    
        <div class="rounded-lg shadow-sm p-4 sm:p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='#'">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <i class="fas fa-check-square text-xl sm:text-2xl opacity-80"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Attendance Records</span>
                                <strong class="text-lg sm:text-xl" id="attendanceRecords">0</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Pending Confirmations</span>
                                <strong class="text-lg sm:text-xl" id="pendingConfirmations">0</strong>
                            </div>
        </div>
                    </div>
                    
        <div class="rounded-lg shadow-sm p-4 sm:p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='{{ route('admin.media-library.index') }}'">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <i class="fas fa-folder-open text-xl sm:text-2xl opacity-80"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Media Files</span>
                    <strong class="text-lg sm:text-xl" id="mediaFiles">{{ $mediaFilesCount }}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">MB Storage</span>
                    <strong class="text-lg sm:text-xl" id="totalStorage">{{ $totalStorageMB }}</strong>
                </div>
            </div>
                    </div>
                    
        <div class="rounded-lg shadow-sm p-4 sm:p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #FBD116 0%, #FBD116 100%); color: #123a60;" onclick="window.location.href='{{ route('admin.audit-logs.index') }}'">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <i class="fas fa-history text-xl sm:text-2xl opacity-80"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Audit Logs</span>
                                <strong class="text-lg sm:text-xl" id="auditLogs">{{ $auditLogsCount }}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs sm:text-sm opacity-90">Today's Activities</span>
                                <strong class="text-lg sm:text-xl" id="todayActivities">{{ $todayActivitiesCount }}</strong>
                </div>
            </div>
                    </div>
                </div>
                
    <!-- Activities Calendar Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="mb-3 sm:mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2" style="color: #055498;"></i>
                    Activities Calendar
                                </h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">View meetings, announcements, and scheduled events</p>
                            </div>
                            <button id="toggleFilterBtn" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors inline-flex items-center" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <i class="fas fa-filter mr-2"></i>
                                <span>Filter</span>
                            </button>
            </div>

        <!-- Advanced Filter Panel -->
        <div id="filterPanel" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Event Type Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Event Type</label>
                    <select id="filterEventType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm">
                        <option value="all">All Types</option>
                        <option value="meeting">Meetings</option>
                        <option value="announcement">Announcements</option>
                        <option value="resolution">Resolutions</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <!-- Date From Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" id="filterDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm">
            </div>

                <!-- Date To Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" id="filterDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm">
                    </div>
                
                <!-- Search Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="filterSearch" placeholder="Search events..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm">
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex flex-wrap items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                <button id="clearFiltersBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </button>
                <button id="applyFiltersBtn" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-check mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </div>
        
        <div id="calendar" class="calendar-container"></div>
    </div>

    <!-- Bottom Section removed (Today Tasks and Updates cards) -->
</div>
@endsection
    
@push('scripts')
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
    <script>
    // Initialize Calendar
    (function() {
        // Store all events
        const allEvents = [
            {
                title: 'Board Meeting - Q1 Review',
                start: new Date().toISOString().split('T')[0],
                backgroundColor: '#055498',
                borderColor: '#055498',
                textColor: '#ffffff',
                extendedProps: {
                    type: 'meeting',
                    description: 'Quarterly board meeting to review Q1 performance and discuss upcoming initiatives.'
                }
            },
            {
                title: 'New Announcement: Policy Update',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0],
                backgroundColor: '#FBD116',
                borderColor: '#FBD116',
                textColor: '#123a60',
                extendedProps: {
                    type: 'announcement',
                    description: 'Important policy update announcement for all board members.'
                }
            },
            {
                title: 'Resolution Review Meeting',
                start: new Date(Date.now() + 2 * 86400000).toISOString().split('T')[0],
                backgroundColor: '#CE2028',
                borderColor: '#CE2028',
                textColor: '#ffffff',
                extendedProps: {
                    type: 'meeting',
                    description: 'Review and approve pending board resolutions.'
                }
            },
            {
                title: 'Announcement: Annual Report',
                start: new Date(Date.now() + 5 * 86400000).toISOString().split('T')[0],
                backgroundColor: '#FBD116',
                borderColor: '#FBD116',
                textColor: '#123a60',
                extendedProps: {
                    type: 'announcement',
                    description: 'Annual report publication announcement.'
                }
            },
            {
                title: 'Committee Meeting',
                start: new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0],
                backgroundColor: '#055498',
                borderColor: '#055498',
                textColor: '#ffffff',
                extendedProps: {
                    type: 'meeting',
                    description: 'Scheduled committee meeting to discuss ongoing projects.'
                }
            },
            {
                title: 'Board Resolution #2024-001',
                start: new Date(Date.now() + 3 * 86400000).toISOString().split('T')[0],
                backgroundColor: '#CE2028',
                borderColor: '#CE2028',
                textColor: '#ffffff',
                extendedProps: {
                    type: 'resolution',
                    description: 'New board resolution for approval.'
                }
            },
            {
                title: 'Special Event',
                start: new Date(Date.now() + 10 * 86400000).toISOString().split('T')[0],
                backgroundColor: '#6B7280',
                borderColor: '#6B7280',
                textColor: '#ffffff',
                extendedProps: {
                    type: 'other',
                    description: 'Special event for board members.'
                }
            }
        ];
        
        let calendar = null;
        
        // Filter events function
        function filterEvents() {
            const eventType = document.getElementById('filterEventType').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const searchTerm = document.getElementById('filterSearch').value.toLowerCase();
            
            return allEvents.filter(event => {
                // Filter by event type
                if (eventType !== 'all' && event.extendedProps.type !== eventType) {
                    return false;
                }
                
                // Filter by date range
                const eventDate = new Date(event.start);
                if (dateFrom && eventDate < new Date(dateFrom)) {
                    return false;
                }
                if (dateTo && eventDate > new Date(dateTo + 'T23:59:59')) {
                    return false;
                }
                
                // Filter by search term
                if (searchTerm && !event.title.toLowerCase().includes(searchTerm) && 
                    !event.extendedProps.description.toLowerCase().includes(searchTerm)) {
                    return false;
                }
                
                return true;
            });
        }
        
        // Apply filters to calendar
        function applyFilters() {
            if (calendar) {
                const filteredEvents = filterEvents();
                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
                
                // Navigate calendar to date range if dates are set
                const dateFrom = document.getElementById('filterDateFrom').value;
                const dateTo = document.getElementById('filterDateTo').value;
                
                if (dateFrom || dateTo) {
                    // If both dates are set
                    if (dateFrom && dateTo) {
                        const fromDate = new Date(dateFrom);
                        const toDate = new Date(dateTo);
                        const daysDiff = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24));
                        
                        // Navigate to the start date
                        calendar.gotoDate(dateFrom);
                        
                        // Adjust view based on date range
                        const isMobile = window.innerWidth < 768;
                        if (isMobile) {
                            // On mobile, use listWeek view
                            if (calendar.view.type !== 'listWeek') {
                                calendar.changeView('listWeek');
                            }
                        } else {
                            // On desktop, adjust view based on range
                            if (daysDiff <= 7) {
                                // For ranges up to a week, use week view
                                if (calendar.view.type !== 'timeGridWeek' && calendar.view.type !== 'timeGridDay') {
                                    calendar.changeView('timeGridWeek');
                                }
                            } else if (daysDiff <= 31) {
                                // For ranges up to a month, use month view
                                if (calendar.view.type !== 'dayGridMonth') {
                                    calendar.changeView('dayGridMonth');
                                }
                            } else {
                                // For longer ranges, stay in month view
                                if (calendar.view.type !== 'dayGridMonth') {
                                    calendar.changeView('dayGridMonth');
                                }
                            }
                        }
                    } 
                    // If only From Date is set
                    else if (dateFrom) {
                        calendar.gotoDate(dateFrom);
                    } 
                    // If only To Date is set
                    else if (dateTo) {
                        calendar.gotoDate(dateTo);
                    }
                }
            }
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('filterEventType').value = 'all';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('filterSearch').value = '';
            
            // Reset calendar to today's date
            if (calendar) {
                calendar.gotoDate(new Date());
                // Reset to default view based on screen size
                const isMobile = window.innerWidth < 768;
                if (isMobile && calendar.view.type !== 'listWeek') {
                    calendar.changeView('listWeek');
                } else if (!isMobile && calendar.view.type === 'listWeek') {
                    calendar.changeView('dayGridMonth');
                }
            }
            
            applyFilters();
        }
        
        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                return;
            }
            
            // Check for FullCalendar availability
            let FC = null;
            if (typeof FullCalendar !== 'undefined' && FullCalendar.Calendar) {
                FC = FullCalendar;
            } else if (typeof window.FullCalendar !== 'undefined' && window.FullCalendar.Calendar) {
                FC = window.FullCalendar;
            } else if (typeof window.FC !== 'undefined' && window.FC.Calendar) {
                FC = window.FC;
            }
            
            if (!FC || typeof FC.Calendar === 'undefined') {
                setTimeout(initCalendar, 200);
                return;
            }
            
            try {
                const isMobile = window.innerWidth < 768;
                
                calendar = new FC.Calendar(calendarEl, {
                    initialView: isMobile ? 'listWeek' : 'dayGridMonth',
                    headerToolbar: {
                        left: isMobile ? 'prev,next' : 'prev,next today',
                        center: 'title',
                        right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    editable: false,
                    selectable: false,
                    views: {
                        listWeek: {
                            listDayFormat: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' },
                            listDaySideFormat: false
                        }
                    },
                    events: allEvents,
                    eventClick: function(info) {
                        const eventType = info.event.extendedProps.type || 'event';
                        const description = info.event.extendedProps.description || 'No description available.';
                        const eventDate = info.event.start.toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        
                    Swal.fire({
                            title: info.event.title,
                            html: `
                                <div class="text-left">
                                    <p class="mb-2"><strong>Type:</strong> <span class="capitalize">${eventType}</span></p>
                                    <p class="mb-2"><strong>Date:</strong> ${eventDate}</p>
                                    ${info.event.start.toLocaleTimeString ? `<p class="mb-2"><strong>Time:</strong> ${info.event.start.toLocaleTimeString()}</p>` : ''}
                                    <p class="mb-2"><strong>Description:</strong></p>
                                    <p class="text-sm text-gray-600">${description}</p>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#055498'
                    });
                    },
                    eventDisplay: 'block',
                    dayMaxEvents: true,
                    moreLinkClick: 'popover'
                });
                
                calendar.render();
                
                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        const isMobile = window.innerWidth < 768;
                        const currentView = calendar.view.type;
                        
                        if (isMobile && currentView === 'dayGridMonth') {
                            calendar.changeView('listWeek');
                        } else if (!isMobile && currentView === 'listWeek') {
                            calendar.changeView('dayGridMonth');
                        }
                        
                        calendar.setOption('headerToolbar', {
                            left: isMobile ? 'prev,next' : 'prev,next today',
                            center: 'title',
                            right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                        });
                    }, 250);
                });
                
                // Filter panel toggle
                const toggleFilterBtn = document.getElementById('toggleFilterBtn');
                const filterPanel = document.getElementById('filterPanel');
                
                if (toggleFilterBtn && filterPanel) {
                    toggleFilterBtn.addEventListener('click', function() {
                        filterPanel.classList.toggle('hidden');
                        const icon = toggleFilterBtn.querySelector('i');
                        if (filterPanel.classList.contains('hidden')) {
                            icon.className = 'fas fa-filter mr-2';
                        } else {
                            icon.className = 'fas fa-filter mr-2';
                        }
                    });
                }
                
                // Apply filters button
                const applyFiltersBtn = document.getElementById('applyFiltersBtn');
                if (applyFiltersBtn) {
                    applyFiltersBtn.addEventListener('click', applyFilters);
                }
                
                // Clear filters button
                const clearFiltersBtn = document.getElementById('clearFiltersBtn');
                if (clearFiltersBtn) {
                    clearFiltersBtn.addEventListener('click', clearFilters);
                }
                
                // Auto-apply filters on input change (debounced)
                let filterTimeout;
                const filterInputs = ['filterEventType', 'filterDateFrom', 'filterDateTo', 'filterSearch'];
                filterInputs.forEach(inputId => {
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.addEventListener('change', function() {
                            clearTimeout(filterTimeout);
                            filterTimeout = setTimeout(applyFilters, 300);
                        });
                        input.addEventListener('input', function() {
                            if (inputId === 'filterSearch') {
                                clearTimeout(filterTimeout);
                                filterTimeout = setTimeout(applyFilters, 500);
                            }
                        });
                    }
                });
            } catch(error) {
                console.error('Error initializing calendar:', error);
            }
        }
        
        // Load FullCalendar script dynamically if not already loaded
        if (typeof FullCalendar === 'undefined' && typeof window.FullCalendar === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js';
            script.onload = function() {
                setTimeout(initCalendar, 100);
            };
            script.onerror = function() {
                const altScript = document.createElement('script');
                altScript.src = 'https://unpkg.com/fullcalendar@6.1.10/index.global.min.js';
                altScript.onload = function() {
                    setTimeout(initCalendar, 100);
                };
                document.head.appendChild(altScript);
            };
            document.head.appendChild(script);
                } else {
            // FullCalendar already loaded, initialize immediately
            setTimeout(initCalendar, 100);
        }
    })();
</script>

<style>
    /* FullCalendar Custom Styling */
    .calendar-container {
        min-height: 500px;
        width: 100%;
        overflow-x: auto;
                }
    
    #calendar .fc {
        font-family: inherit;
    }
    
    #calendar .fc-header-toolbar {
        margin-bottom: 1.5rem;
        padding: 0.5rem;
    }
    
    #calendar .fc-button {
        background-color: #055498 !important;
        border-color: #055498 !important;
        color: white !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.375rem !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
    }
    
    #calendar .fc-button:hover {
        background-color: #123a60 !important;
        border-color: #123a60 !important;
    }
    
    #calendar .fc-button-active {
        background-color: #123a60 !important;
        border-color: #123a60 !important;
    }
    
    #calendar .fc-today-button {
        background-color: #FBD116 !important;
        border-color: #FBD116 !important;
        color: #123a60 !important;
    }
    
    #calendar .fc-today-button:hover {
        background-color: #facc15 !important;
        border-color: #facc15 !important;
    }
    
    #calendar .fc-day-today {
        background-color: rgba(5, 84, 152, 0.1) !important;
    }
    
    #calendar .fc-event {
        border-radius: 0.25rem !important;
        padding: 0.25rem 0.5rem !important;
        cursor: pointer !important;
    }
    
    #calendar .fc-event:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    #calendar .fc-daygrid-day-number {
        color: #374151 !important;
        font-weight: 500 !important;
    }
    
    #calendar .fc-col-header-cell {
        background-color: #f9fafb !important;
        padding: 0.75rem 0 !important;
    }
    
    #calendar .fc-col-header-cell-cushion {
        color: #374151 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
    }
    
    #calendar .fc-daygrid-day {
        border-color: #e5e7eb !important;
    }
    
    #calendar .fc-daygrid-day-frame {
        min-height: 100px !important;
    }
    
    @media (max-width: 640px) {
        .calendar-container {
            min-height: 400px;
            padding: 0;
        }
        
        #calendar .fc-header-toolbar {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }
        
        #calendar .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
            flex-wrap: wrap;
                }
        
        #calendar .fc-button {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.75rem !important;
        }
        
        #calendar .fc-toolbar-title {
            font-size: 1rem !important;
            margin: 0.5rem 0 !important;
        }
        
        #calendar .fc-col-header-cell-cushion {
            font-size: 0.625rem !important;
            padding: 0.5rem 0.25rem !important;
        }
        
        #calendar .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            padding: 0.25rem !important;
        }
        
        #calendar .fc-event {
            font-size: 0.75rem !important;
            padding: 0.125rem 0.375rem !important;
            margin: 0.125rem 0 !important;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 60px !important;
        }
        
        #calendar .fc-list-event {
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-list-event-title {
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-list-day-text {
            padding-left: 0.5rem !important;
        }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
        .calendar-container {
            min-height: 450px;
                }
        
        #calendar .fc-header-toolbar {
            padding: 0.75rem 0;
        }
        
        #calendar .fc-button {
            padding: 0.5rem 0.875rem !important;
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-toolbar-title {
            font-size: 1.125rem !important;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 80px !important;
        }
        
        #calendar .fc-event {
            font-size: 0.8125rem !important;
        }
    }
    
    @media (min-width: 1025px) {
        .calendar-container {
            min-height: 600px;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 120px !important;
        }
    }
    
    @media (max-width: 1024px) {
        #calendar .fc-scroller {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        #calendar .fc-daygrid-body {
            min-width: 100% !important;
        }
    }
    
    @media (hover: none) and (pointer: coarse) {
        #calendar .fc-button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        #calendar .fc-event {
            min-height: 32px !important;
        }
        
        #calendar .fc-daygrid-day-number {
            min-height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
                }
    }
    
    @media (max-width: 640px) {
        #calendar .fc-more-link {
            font-size: 0.75rem !important;
        }
        
        #calendar .fc-popover {
            max-width: 90vw !important;
        }
    }
</style>

<script>
    $(document).ready(function() {
            // Menu item handlers
            $('.manage-users, .manage-roles, .manage-announcements, .manage-meetings, .manage-attendance, .manage-resolutions, .manage-media, .manage-audit').on('click', function(e) {
                e.preventDefault();
                const title = $(this).closest('li').find('a').first().text().trim() || 'Feature';
                Swal.fire({
                    title: title,
                    html: '<p>This feature will be available soon.</p>',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });

            // Task item handlers
            $('.task-item').on('click', function(e) {
                e.preventDefault();
                const taskText = $(this).find('a').text();
                Swal.fire({
                    title: 'Task Details',
                    html: '<p>' + taskText + '</p><p>This feature will be available soon.</p>',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
    
@endpush

