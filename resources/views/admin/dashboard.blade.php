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
    
    // Widget Data - Announcements
    $recentAnnouncements = \App\Models\Announcement::where('status', 'published')
        ->with(['creator'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    $totalAnnouncements = \App\Models\Announcement::where('status', 'published')->count();
    $draftAnnouncements = \App\Models\Announcement::where('status', 'draft')->count();
    
    // Widget Data - Messages/Chats (connected to admin account)
    $currentUserId = Auth::id();
    
    // Get recent messages where admin is sender or receiver (individual chats)
    $recentIndividualMessages = \App\Models\Chat::with(['sender', 'receiver'])
        ->where(function($query) use ($currentUserId) {
            $query->where('sender_id', $currentUserId)
                  ->orWhere('receiver_id', $currentUserId);
        })
        ->whereNull('group_id')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Get recent group messages where admin is a member
    $recentGroupMessages = \App\Models\Chat::with(['sender', 'group'])
        ->whereNotNull('group_id')
        ->whereHas('group.members', function($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId);
        })
        ->orderBy('created_at', 'desc')
        ->limit(2)
        ->get();
    
    // Combine and sort by created_at
    $recentMessages = $recentIndividualMessages->merge($recentGroupMessages)
        ->sortByDesc('created_at')
        ->take(5)
        ->values();
    
    // Count total messages
    $totalMessages = \App\Models\Chat::where(function($query) use ($currentUserId) {
        $query->where('sender_id', $currentUserId)
              ->orWhere('receiver_id', $currentUserId)
              ->orWhereHas('group.members', function($q) use ($currentUserId) {
                  $q->where('user_id', $currentUserId);
              });
    })->count();
    
    // Count unread messages
    $unreadMessages = \App\Models\Chat::where(function($query) use ($currentUserId) {
        $query->where('receiver_id', $currentUserId)
              ->where('is_read', false)
              ->whereNull('group_id');
    })->orWhere(function($query) use ($currentUserId) {
        $query->whereHas('group.members', function($q) use ($currentUserId) {
            $q->where('user_id', $currentUserId);
        })
        ->where('is_read', false)
        ->whereNotNull('group_id');
    })->count();
    
    // Count group chats
    $groupChatsCount = \App\Models\GroupChat::whereHas('members', function($query) use ($currentUserId) {
        $query->where('user_id', $currentUserId);
    })->count();
    
    // Widget Data - Board Resolutions & Regulations
    $totalResolutions = \App\Models\OfficialDocument::count();
    $totalRegulations = \App\Models\BoardRegulation::count();
    $recentResolutions = \App\Models\OfficialDocument::with(['uploader'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    $recentRegulations = \App\Models\BoardRegulation::with(['uploader'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Widget Data - Referendums
    $activeReferendums = \App\Models\Referendum::where('expires_at', '>', now())->count();
    $totalReferendums = \App\Models\Referendum::count();
    $recentReferendums = \App\Models\Referendum::with(['creator'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Widget Data - Online Users
    $onlineUsers = \App\Models\User::where('is_online', true)->count();
    $totalUsers = \App\Models\User::count();
    $activeUsers = \App\Models\User::where('is_active', true)->count();
    
    // Widget Data - Recent Activities
    $recentActivities = \App\Models\AuditLog::with(['user'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Widget Data - Government Agencies
    $activeAgencies = \App\Models\GovernmentAgency::where('is_active', true)->count();
    $totalAgencies = \App\Models\GovernmentAgency::count();
    
    // Chart Data - Activity Over Time (Last 30 days)
    $activityChartLabels = [];
    $activityChartData = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = \Carbon\Carbon::now()->subDays($i);
        $activityChartLabels[] = $date->format('M d');
        $activityChartData[] = \App\Models\AuditLog::whereDate('created_at', $date->toDateString())->count();
    }
    
    // Chart Data - User Distribution
    $userDistribution = [
        'admin' => \App\Models\User::where('privilege', 'admin')->count(),
        'consec' => \App\Models\User::where('privilege', 'consec')->count(),
        'board_members' => \App\Models\User::where(function($query) {
            $query->where('privilege', 'user')
                  ->orWhere('representative_type', 'Board Member');
        })->count(),
        'authorized_reps' => \App\Models\User::where('representative_type', 'Authorized Representative')->count(),
    ];
    
    // Chart Data - Messages Over Time (Last 7 days)
    $messagesChartLabels = [];
    $messagesChartData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = \Carbon\Carbon::now()->subDays($i);
        $messagesChartLabels[] = $date->format('M d');
        $messagesChartData[] = \App\Models\Chat::whereDate('created_at', $date->toDateString())->count();
    }
    
    // Chart Data - Announcements Status
    $announcementsStatus = [
        'published' => \App\Models\Announcement::where('status', 'published')->count(),
        'draft' => \App\Models\Announcement::where('status', 'draft')->count(),
    ];
    
    // Chart Data - Content Creation (Last 6 months)
    $contentChartLabels = [];
    $resolutionsData = [];
    $regulationsData = [];
    $announcementsData = [];
    $noticesData = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = \Carbon\Carbon::now()->subMonths($i);
        $contentChartLabels[] = $date->format('M Y');
        $resolutionsData[] = \App\Models\OfficialDocument::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $regulationsData[] = \App\Models\BoardRegulation::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $announcementsData[] = \App\Models\Announcement::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $noticesData[] = \App\Models\Notice::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
    }
@endphp

@section('content')
<div class="p-4 sm:p-6">
                <!-- Page Title -->
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard</h2>
                </div>
                
                <!-- Secondary Stats -->
                @if(Auth::user()->privilege === 'admin')
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
                @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <!-- Activity Over Time Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 dashboard-widget">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg" style="background-color: rgba(251, 209, 22, 0.1);">
                        <i class="fas fa-chart-line text-lg" style="color: #FBD116;"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Activity Over Time</h3>
                </div>
                <span class="text-xs text-gray-500">Last 30 Days</span>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- User Distribution Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 dashboard-widget">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg" style="background-color: rgba(5, 84, 152, 0.1);">
                        <i class="fas fa-chart-pie text-lg" style="color: #055498;"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">User Distribution</h3>
                </div>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="userDistributionChart"></canvas>
            </div>
        </div>

        <!-- Messages Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 dashboard-widget">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg" style="background-color: rgba(5, 84, 152, 0.1);">
                        <i class="fas fa-chart-bar text-lg" style="color: #055498;"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Messages Activity</h3>
                </div>
                <span class="text-xs text-gray-500">Last 7 Days</span>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="messagesChart"></canvas>
            </div>
        </div>

        <!-- Announcements Status Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 dashboard-widget">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg" style="background-color: rgba(251, 209, 22, 0.1);">
                        <i class="fas fa-chart-pie text-lg" style="color: #FBD116;"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Announcements Status</h3>
                </div>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="announcementsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Creation Chart (Full Width) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 dashboard-widget mb-4 sm:mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="p-2 rounded-lg" style="background-color: rgba(206, 32, 40, 0.1);">
                    <i class="fas fa-chart-area text-lg" style="color: #CE2028;"></i>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Content Creation Overview</h3>
            </div>
            <span class="text-xs text-gray-500">Last 6 Months</span>
        </div>
        <div class="relative" style="height: 300px;">
            <canvas id="contentChart"></canvas>
        </div>
    </div>
</div>
@endsection
    
@push('styles')
<style>
    /* Dashboard Widget Styles */
    .dashboard-widget {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .dashboard-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    /* Custom Scrollbar for Widget Lists */
    .widget-scroll::-webkit-scrollbar {
        width: 6px;
    }
    
    .widget-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .widget-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .widget-scroll::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    /* Clickable items */
    .widget-item-link {
        display: block;
        text-decoration: none;
        color: inherit;
        -webkit-tap-highlight-color: transparent;
    }
    
    .widget-item-link:active {
        background-color: #f3f4f6;
    }
    
    /* Touch-friendly targets */
    @media (max-width: 640px) {
        .widget-item-link {
            min-height: 48px;
            display: flex;
            align-items: center;
        }
    }
    
    /* Chart Container Styles */
    canvas {
        max-width: 100%;
    }
    
    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .dashboard-widget {
            min-height: auto;
        }
        
        .dashboard-widget canvas {
            max-height: 200px;
        }
    }
    
    @media (max-width: 640px) {
        .dashboard-widget {
            padding: 1rem !important;
        }
        
        .dashboard-widget h3 {
            font-size: 0.875rem !important;
        }
        
        .dashboard-widget .text-sm {
            font-size: 0.75rem !important;
        }
        
        .dashboard-widget .text-xs {
            font-size: 0.7rem !important;
        }
        
        .widget-scroll {
            max-height: 120px !important;
        }
        
        .dashboard-widget canvas {
            max-height: 180px;
        }
        
        /* Adjust chart container heights on mobile */
        .dashboard-widget .relative[style*="height: 250px"] {
            height: 200px !important;
        }
        
        .dashboard-widget .relative[style*="height: 300px"] {
            height: 220px !important;
        }
    }
    
    @media (max-width: 375px) {
        .dashboard-widget {
            padding: 0.75rem !important;
        }
        
        .widget-scroll {
            max-height: 100px !important;
        }
        
        .dashboard-widget canvas {
            max-height: 150px;
        }
        
        .dashboard-widget .relative[style*="height: 250px"] {
            height: 180px !important;
        }
        
        .dashboard-widget .relative[style*="height: 300px"] {
            height: 200px !important;
        }
    }
    
    /* Improve readability on small screens */
    @media (max-width: 640px) {
        .dashboard-widget p {
            line-height: 1.4;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        // Initialize Charts
        initializeCharts();
    });

    function initializeCharts() {
        // Chart.js default configuration
        Chart.defaults.font.family = "'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6B7280';
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Activity Over Time Chart (Line Chart)
        const activityCtx = document.getElementById('activityChart');
        if (activityCtx) {
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: @json($activityChartLabels),
                    datasets: [{
                        label: 'Activities',
                        data: @json($activityChartData),
                        borderColor: '#FBD116',
                        backgroundColor: 'rgba(251, 209, 22, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#FBD116',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // User Distribution Chart (Doughnut Chart)
        const userDistCtx = document.getElementById('userDistributionChart');
        if (userDistCtx) {
            new Chart(userDistCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Admin', 'CONSEC', 'Board Members', 'Authorized Reps'],
                    datasets: [{
                        data: [
                            {{ $userDistribution['admin'] }},
                            {{ $userDistribution['consec'] }},
                            {{ $userDistribution['board_members'] }},
                            {{ $userDistribution['authorized_reps'] }}
                        ],
                        backgroundColor: [
                            '#055498',
                            '#123a60',
                            '#FBD116',
                            '#CE2028'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 11 },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 6
                        }
                    }
                }
            });
        }

        // Messages Activity Chart (Bar Chart)
        const messagesCtx = document.getElementById('messagesChart');
        if (messagesCtx) {
            new Chart(messagesCtx, {
                type: 'bar',
                data: {
                    labels: @json($messagesChartLabels),
                    datasets: [{
                        label: 'Messages',
                        data: @json($messagesChartData),
                        backgroundColor: 'rgba(5, 84, 152, 0.8)',
                        borderColor: '#055498',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Announcements Status Chart (Doughnut Chart)
        const announcementsCtx = document.getElementById('announcementsChart');
        if (announcementsCtx) {
            new Chart(announcementsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Published', 'Draft'],
                    datasets: [{
                        data: [
                            {{ $announcementsStatus['published'] }},
                            {{ $announcementsStatus['draft'] }}
                        ],
                        backgroundColor: [
                            '#10B981',
                            '#6B7280'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 11 },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 6
                        }
                    }
                }
            });
        }

        // Content Creation Chart (Line Chart with Multiple Datasets)
        const contentCtx = document.getElementById('contentChart');
        if (contentCtx) {
            new Chart(contentCtx, {
                type: 'line',
                data: {
                    labels: @json($contentChartLabels),
                    datasets: [
                        {
                            label: 'Resolutions',
                            data: @json($resolutionsData),
                            borderColor: '#CE2028',
                            backgroundColor: 'rgba(206, 32, 40, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#CE2028',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Regulations',
                            data: @json($regulationsData),
                            borderColor: '#055498',
                            backgroundColor: 'rgba(5, 84, 152, 0.15)',
                            borderWidth: 3,
                            borderDash: [8, 4],
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#055498',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointStyle: 'circle'
                        },
                        {
                            label: 'Announcements',
                            data: @json($announcementsData),
                            borderColor: '#FBD116',
                            backgroundColor: 'rgba(251, 209, 22, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#FBD116',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Notices',
                            data: @json($noticesData),
                            borderColor: '#7C3AED',
                            backgroundColor: 'rgba(124, 58, 237, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#7C3AED',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointStyle: 'triangle'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            display: true,
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                usePointStyle: true,
                                boxWidth: 12,
                                boxHeight: 12
                            },
                            onClick: function(e, legendItem) {
                                // Prevent hiding datasets on click to ensure all are visible
                                const index = legendItem.datasetIndex;
                                const chart = this.chart;
                                const meta = chart.getDatasetMeta(index);
                                meta.hidden = false;
                                chart.update();
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 6,
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    }
</script>
@endpush

