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
                <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Announcements</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalAnnouncements">0</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Meetings</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalMeetings">0</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Resolutions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalResolutions">0</p>
                </div>
                <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-text text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" onclick="window.location.href='#'">
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
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" onclick="window.location.href='#'">
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
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" onclick="window.location.href='#'">
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
        
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg shadow-sm p-6 text-white cursor-pointer hover:shadow-md transition-shadow" onclick="window.location.href='#'">
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
    
    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Chart</h3>
        <div class="h-64">
            <canvas id="canvas"></canvas>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tasks -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>
                    {{ date('d F Y') }}
                </h3>
                <button class="text-green-600 hover:text-green-700">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Today Tasks for {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
            <ul class="space-y-3">
                <li class="task-item border-l-4 border-blue-500 pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded">
                    <a href="#" class="text-sm font-medium text-gray-800">Review pending attendance confirmations</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>10:00 AM</strong></p>
                </li>
                <li class="task-item border-l-4 border-green-500 pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded">
                    <a href="#" class="text-sm font-medium text-gray-800">Approve new board resolution</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>11:00 AM</strong></p>
                </li>
                <li class="task-item border-l-4 border-purple-500 pl-4 py-2 cursor-pointer hover:bg-gray-50 rounded">
                    <a href="#" class="text-sm font-medium text-gray-800">Schedule next board meeting</a>
                    <p class="text-xs text-gray-500 mt-1"><strong>02:00 PM</strong></p>
                </li>
            </ul>
        </div>
        
        <!-- Updates -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-comments text-blue-600 mr-2"></i>
                    Updates
                </h3>
                <button class="text-green-600 hover:text-green-700">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">User confirmation</p>
            <ul class="space-y-3">
                <li class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">System</p>
                        <p class="text-xs text-gray-600 mt-1">New board member registration pending approval.</p>
                        <p class="text-xs text-gray-400 mt-1">12 min ago</p>
                    </div>
                </li>
                <li class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Chart
        try {
            const ctx = document.getElementById('canvas');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Activity',
                            data: [12, 19, 3, 5, 2, 3],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        } catch(e) {
            console.log('Chart initialization skipped:', e.message);
        }

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

