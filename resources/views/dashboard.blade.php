<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Dashboard - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Gotham Font -->
    <link href="https://cdn.jsdelivr.net/npm/gotham-fonts@1.0.3/css/gotham-rounded.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize theme immediately before page renders to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>
        /* Typography Standards */
        
        /* Body Text - Gotham or Montserrat, 14-16px, 1-1.5 line height */
        body, p, span, div, li, td, th, label, input, textarea, select, button {
            font-family: 'Gotham Rounded', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px; /* 14px digital (default) */
            line-height: 1.5; /* 1.5 line height for readability */
        }
        
        /* Titles/Headlines - Montserrat Bold (or Gotham Bold fallback), 28-32px, 1.2-1.3 line height */
        h1, .title, .headline {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 700; /* Bold */
            font-size: 30px; /* 30px digital (middle of 28-32px range) */
            line-height: 1.25; /* 1.25 (middle of 1.2-1.3 range) */
        }
        
        /* Headers/Subheaders - Montserrat Semi-Bold, 20-24px, 1.3 line height */
        h2, h3, h4, h5, h6, .header, .subheader {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600; /* Semi-Bold */
            font-size: 22px; /* 22px digital (middle of 20-24px range) */
            line-height: 1.3;
        }
        
        /* Specific heading sizes */
        h2 {
            font-size: 24px;
        }
        
        h3 {
            font-size: 22px;
        }
        
        h4 {
            font-size: 20px;
        }
        
        h5, h6 {
            font-size: 18px;
        }
        
        /* Small text adjustments */
        small, .text-sm, .text-xs {
            font-size: 12px;
            line-height: 1.5;
        }
    </style>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Dashboard Content -->
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          

            <!-- Welcome Section -->
            <div class="mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-2">
                    Welcome back, {{ Auth::user()->first_name }}!
                </h2>
                <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your board activities</p>
            </div>

            <!-- Quick Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium opacity-90">Announcements</h4>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">12</p>
                    <p class="text-sm opacity-75 mt-1">Active announcements</p>
                </div>

                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium opacity-90">Upcoming Events</h4>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">5</p>
                    <p class="text-sm opacity-75 mt-1">This month</p>
                </div>

                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium opacity-90">Pending Notices</h4>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">2</p>
                    <p class="text-sm opacity-75 mt-1">Require attention</p>
                </div>

                <div class="rounded-xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium opacity-90">Resolutions</h4>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">24</p>
                    <p class="text-sm opacity-75 mt-1">Total resolutions</p>
                </div>
            </div>

            <!-- Dashboard Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Announcements Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Announcements</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">View latest announcements and updates</p>
                    
                    <!-- Sample Announcements -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-2 h-2 rounded-full mt-2" style="background-color: #055498;"></div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Quarterly Board Meeting Scheduled</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-2 h-2 rounded-full mt-2" style="background-color: #055498;"></div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">New Policy Update Available</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-flex items-center font-semibold transition" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                        View All 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Calendar Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Calendar</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Check upcoming meetings and events</p>
                    
                    <!-- Sample Calendar Events -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-12 text-center">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">JAN</div>
                                <div class="text-lg font-bold" style="color: #055498;">15</div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Board Meeting</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">10:00 AM - 12:00 PM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-12 text-center">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">JAN</div>
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">22</div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Committee Review</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">2:00 PM - 4:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-flex items-center font-semibold transition" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                        View Calendar 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Meeting Notices Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Meeting Notices</h3>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full" style="background-color: rgba(5, 84, 152, 0.2); color: #055498;">2 New</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">View meeting notices and confirm attendance</p>
                    
                    <!-- Sample Meeting Notices -->
                    <div class="space-y-3 mb-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg" style="border-left: 4px solid #055498;">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Q1 2024 Board Meeting</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Scheduled for January 15, 2024</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(251, 209, 22, 0.2); color: #123a60;">Pending Response</span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg" style="border-left: 4px solid #055498;">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Policy Review Session</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Scheduled for January 22, 2024</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(5, 84, 152, 0.2); color: #055498;">Confirmed</span>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-flex items-center font-semibold transition" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                        View Notices 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Messages Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Messages</h3>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-full">3</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Chat with board members</p>
                    
                    <!-- Sample Messages -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                JD
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">John Doe</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Can we discuss the agenda for next week?</p>
                            </div>
                            <span class="text-xs text-gray-400">2m</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                JS
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Jane Smith</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Thanks for the update on the resolution</p>
                            </div>
                            <span class="text-xs text-gray-400">1h</span>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-flex items-center font-semibold transition" style="color: #FBD116;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#FBD116'">
                        Open Chat 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Resolutions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Resolutions</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Browse board resolution library</p>
                    
                    <!-- Sample Resolutions -->
                    <div class="space-y-3 mb-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Resolution #2024-001</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Jan 10</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Policy Amendment on Board Procedures</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Resolution #2024-002</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Jan 5</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Budget Approval for Q1 2024</p>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold transition">
                        View Library 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Handle navigation links to landing page sections
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.includes('{{ route("landing") }}')) {
                    e.preventDefault();
                    window.location.href = href;
                }
            });
        });

    </script>
    
    @include('components.footer')
    
    <!-- Global PDF Modal - Available on all pages -->
    @include('components.pdf-modal')
</body>
</html>
