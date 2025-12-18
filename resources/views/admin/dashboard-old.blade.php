<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Board Member Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/png" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        /* Custom styles for sidebar toggle and animations */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        #sidebar.hidden {
            transform: translateX(-100%);
        }
        
        @media (min-width: 1024px) {
            #sidebar {
                transform: translateX(0);
            }
        }
        
        /* Dropdown styles */
        .dropdown-menu {
            display: none;
        }
        
        .dropdown.show .dropdown-menu {
            display: block;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Online status indicator */
        .online_animation {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- User Info -->
                <div class="p-4 border-b border-gray-800">
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
                            <p class="text-xs text-gray-400 flex items-center">
                                <span class="online_animation mr-2"></span> Online
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="p-4">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Admin Panel</h4>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-gray-800 text-white">
                                    <i class="fas fa-tachometer-alt w-5 text-yellow-400"></i>
                                    <span class="ml-3">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-users w-5 text-pink-400"></i>
                                    <span class="ml-3 flex-1">User Management</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </a>
                                <ul class="hidden mt-2 ml-4 space-y-1 border-l-2 border-gray-700 pl-4">
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-user-tie w-4 text-gray-500 group-hover:text-pink-400 transition-colors"></i>
                                            <span class="ml-3">Board Members</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-user-shield w-4 text-gray-500 group-hover:text-pink-400 transition-colors"></i>
                                            <span class="ml-3">Authorized Representatives</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-clock w-4 text-gray-500 group-hover:text-pink-400 transition-colors"></i>
                                            <span class="ml-3">Pending Registrations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-user-cog w-4 text-gray-500 group-hover:text-pink-400 transition-colors"></i>
                                            <span class="ml-3">User Roles</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('admin.portal-manager') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-cog w-5 text-blue-400"></i>
                                    <span class="ml-3">Portal Manager</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-edit w-5 text-green-400"></i>
                                    <span class="ml-3 flex-1">Content Management</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </a>
                                <ul class="hidden mt-2 ml-4 space-y-1 border-l-2 border-gray-700 pl-4">
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-bullhorn w-4 text-gray-500 group-hover:text-green-400 transition-colors"></i>
                                            <span class="ml-3">Announcements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-bell w-4 text-gray-500 group-hover:text-green-400 transition-colors"></i>
                                            <span class="ml-3">Notices</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-file-alt w-4 text-gray-500 group-hover:text-green-400 transition-colors"></i>
                                            <span class="ml-3">Templates</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-folder-open w-4 text-gray-500 group-hover:text-green-400 transition-colors"></i>
                                            <span class="ml-3">Media Library</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-check-circle w-5 text-red-400"></i>
                                    <span class="ml-3">Attendance Confirmation</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-file-alt w-5 text-purple-400"></i>
                                    <span class="ml-3 flex-1">Board Issuances</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </a>
                                <ul class="hidden mt-2 ml-4 space-y-1 border-l-2 border-gray-700 pl-4">
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-file-contract w-4 text-gray-500 group-hover:text-purple-400 transition-colors"></i>
                                            <span class="ml-3">Board Resolutions</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-file-signature w-4 text-gray-500 group-hover:text-purple-400 transition-colors"></i>
                                            <span class="ml-3">Official Documents</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-book w-5 text-indigo-400"></i>
                                    <span class="ml-3">Reference Materials</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-clipboard-list w-5 text-teal-400"></i>
                                    <span class="ml-3">Request for Inclusion in the Agenda</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-chart-bar w-5 text-orange-400"></i>
                                    <span class="ml-3">Report Generation</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="menu-toggle flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                    <i class="fas fa-building w-5 text-cyan-400"></i>
                                    <span class="ml-3 flex-1">Government Agency</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </a>
                                <ul class="hidden mt-2 ml-4 space-y-1 border-l-2 border-gray-700 pl-4">
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-building w-4 text-gray-500 group-hover:text-cyan-400 transition-colors"></i>
                                            <span class="ml-3">Manage Agencies</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white rounded-md transition-all duration-200 group">
                                            <i class="fas fa-tools w-4 text-gray-500 group-hover:text-cyan-400 transition-colors"></i>
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
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Topbar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-4">
                        <button id="sidebarCollapse" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800 hidden sm:block">Dashboard</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications Dropdown -->
                        <div class="relative dropdown">
                            <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dropdown-toggle" data-toggle="dropdown">
                                <i class="far fa-bell text-xl"></i>
                                <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">2</span>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto custom-scrollbar">
                                <div class="p-3 border-b border-gray-200 bg-gray-50">
                                    <h6 class="text-sm font-semibold text-gray-800">Notifications</h6>
                                </div>
                                <div class="py-1">
                                    <a href="#" class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-bullhorn text-purple-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800">New Announcement</p>
                                            <p class="text-xs text-gray-600 mt-1 truncate">Quarterly Board Meeting Scheduled</p>
                                            <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                        </div>
                                    </a>
                                    <a href="#" class="notification-item flex items-start px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-file-text text-green-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800">Meeting Notice</p>
                                            <p class="text-xs text-gray-600 mt-1 truncate">Q1 2024 Board Meeting - Please confirm attendance</p>
                                            <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="border-t border-gray-200">
                                    <a href="{{ route('notifications') }}" class="block px-4 py-3 text-center text-sm font-semibold text-purple-600 hover:bg-gray-50">
                                        See All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Messages Dropdown -->
                        <div class="relative dropdown">
                            <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dropdown-toggle" data-toggle="dropdown">
                                <i class="far fa-envelope text-xl"></i>
                                <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">3</span>
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
                                    <a href="{{ route('messages') }}" class="block px-4 py-3 text-center text-sm font-semibold text-blue-600 hover:bg-gray-50">
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
                                <a href="#" id="logoutBtn" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar bg-gray-50 p-4 lg:p-6">
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
            </main>
        </div>
    </div>
    
    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
    
    <script>
        // Sidebar Toggle
        $(document).ready(function() {
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
                $(this).parent().toggleClass('show');
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown').removeClass('show');
                }
            });
            
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
        });
        
        // Logout Function
        window.handleLogoutClick = function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logging out...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect || '/';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'An error occurred while logging out.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Logout error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while logging out.',
                        });
                    });
                }
            });
            
            return false;
        };

        $(document).ready(function() {
            // Set CSRF token for AJAX
            if (typeof $.ajaxSetup === 'function') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            }

            // Check for messages popup
            function checkMessagesPopup() {
                if (typeof window.openMessagesPopup === 'function') {
                    console.log('Messages popup loaded successfully');
                } else {
                    setTimeout(checkMessagesPopup, 100);
                }
            }
            checkMessagesPopup();

            // Logout handler
            $('#logoutBtn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.handleLogoutClick === 'function') {
                    window.handleLogoutClick(e);
                }
                return false;
            });

            $(document).on('click', '#logoutBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.handleLogoutClick === 'function') {
                    window.handleLogoutClick(e);
                }
                return false;
            });

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
    
</body>
</html>
