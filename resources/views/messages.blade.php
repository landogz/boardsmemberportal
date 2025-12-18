<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Messages - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
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
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Messages Content -->
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-2">
                            Messages
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">Chat with board members</p>
                    </div>
                    <button class="px-4 py-2 text-sm font-semibold bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition">
                        New Message
                    </button>
                </div>
            </div>

            <!-- Messages Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Conversations List -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" placeholder="Search conversations..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <div class="overflow-y-auto max-h-[calc(100vh-300px)]">
                        <!-- Conversation Item -->
                        <a href="#" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    JD
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">John Doe</p>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">2m</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Can we discuss the agenda for next week?</p>
                                </div>
                                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                            </div>
                        </a>
                        <!-- Conversation Item -->
                        <a href="#" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
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
                        <!-- Conversation Item -->
                        <a href="#" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
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
                        <!-- Conversation Item -->
                        <a href="#" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold">
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
                        <!-- Conversation Item -->
                        <a href="#" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold">
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
                </div>

                <!-- Chat Area -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                JD
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">John Doe</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Active now</p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900/30">
                        <!-- Received Message -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                JD
                            </div>
                            <div class="flex-1">
                                <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">Hi! Can we discuss the agenda for next week's board meeting?</p>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-1">2 minutes ago</p>
                            </div>
                        </div>

                        <!-- Sent Message -->
                        <div class="flex items-start space-x-3 justify-end">
                            <div class="flex-1 flex justify-end">
                                <div class="max-w-[70%]">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-3 shadow-sm">
                                        <p class="text-sm">Sure! I've prepared a draft agenda. Let me share it with you.</p>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mr-1 text-right">1 minute ago</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Received Message -->
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                JD
                            </div>
                            <div class="flex-1">
                                <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">That would be great! I'll review it and get back to you with my feedback.</p>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-1">Just now</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <form class="flex items-center space-x-2">
                            <input type="text" placeholder="Type a message..." class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition">
                                Send
                            </button>
                        </form>
                    </div>
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

