@extends('admin.layout')

@section('title', 'Dashboard')

@php
    $pageTitle = 'Dashboard';
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalUsers">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: rgba(251, 209, 22, 0.2);">
                    <i class="fas fa-users text-xl" style="color: #FBD116;"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Announcements</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalAnnouncements">0</p>
                </div>
                <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: rgba(5, 84, 152, 0.2);">
                    <i class="fas fa-bullhorn text-xl" style="color: #055498;"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Meetings</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalMeetings">0</p>
                </div>
                <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: rgba(5, 84, 152, 0.2);">
                    <i class="fas fa-calendar text-xl" style="color: #055498;"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Resolutions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalResolutions">0</p>
                </div>
                <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: rgba(206, 32, 40, 0.2);">
                    <i class="fas fa-file-text text-xl" style="color: #CE2028;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='#'">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-users text-2xl opacity-80"></i>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Board Members</span>
                    <strong class="text-xl" id="boardMembers">0</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Authorized Reps</span>
                    <strong class="text-xl" id="authorizedReps">0</strong>
                </div>
            </div>
        </div>
        
        <div class="rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='#'">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-check-square text-2xl opacity-80"></i>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Attendance Records</span>
                    <strong class="text-xl" id="attendanceRecords">0</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Pending Confirmations</span>
                    <strong class="text-xl" id="pendingConfirmations">0</strong>
                </div>
            </div>
        </div>
        
        <div class="rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onclick="window.location.href='#'">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-folder-open text-2xl opacity-80"></i>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Media Files</span>
                    <strong class="text-xl" id="mediaFiles">0</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">MB Storage</span>
                    <strong class="text-xl" id="totalStorage">0</strong>
                </div>
            </div>
        </div>
        
        <div class="rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" style="background: linear-gradient(135deg, #FBD116 0%, #FBD116 100%); color: #123a60;" onclick="window.location.href='#'">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-history text-2xl opacity-80"></i>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Audit Logs</span>
                    <strong class="text-xl" id="auditLogs">0</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm opacity-90">Today's Activities</span>
                    <strong class="text-xl" id="todayActivities">0</strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activities Calendar Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-calendar-alt mr-2" style="color: #055498;"></i>
                Activities Calendar
            </h3>
            <p class="text-sm text-gray-600 mt-1">View meetings, announcements, and scheduled events</p>
        </div>
        <div id="calendar" class="calendar-container"></div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tasks -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-calendar mr-2" style="color: #055498;"></i>
                    {{ date('d F Y') }}
                </h3>
                <button style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Today Tasks for {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
            <ul class="space-y-3">
                <li class="task-item pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded" style="border-left: 4px solid #055498;">
                    <a href="#" class="text-sm font-medium text-gray-800">Review pending attendance confirmations</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>10:00 AM</strong></p>
                </li>
                <li class="task-item pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded" style="border-left: 4px solid #055498;">
                    <a href="#" class="text-sm font-medium text-gray-800">Approve new board resolution</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>11:00 AM</strong></p>
                </li>
                <li class="task-item pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded" style="border-left: 4px solid #055498;">
                    <a href="#" class="text-sm font-medium text-gray-800">Schedule next board meeting</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>02:00 PM</strong></p>
                </li>
            </ul>
        </div>
        
        <!-- Updates -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-comments mr-2" style="color: #055498;"></i>
                    Updates
                </h3>
                            <button style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">User confirmation</p>
                        <ul class="space-y-3">
                            <li class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgba(5, 84, 152, 0.2);">
                                    <i class="fas fa-user" style="color: #055498;"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">System</p>
                                    <p class="text-xs text-gray-600 mt-1">New board member registration pending approval.</p>
                                    <p class="text-xs text-gray-400 mt-1">12 min ago</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgba(5, 84, 152, 0.2);">
                                    <i class="fas fa-check" style="color: #055498;"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">System</p>
                        <p class="text-xs text-gray-600 mt-1">Meeting attendance confirmation received.</p>
                        <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script>
    // Initialize Calendar
    (function() {
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
                
                const calendar = new FC.Calendar(calendarEl, {
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
                    events: [
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
                        }
                    ],
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

<!-- Messages Popup Component -->
@include('components.messages-popup')
@endpush

