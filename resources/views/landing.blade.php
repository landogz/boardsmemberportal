<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Board Member Portal - Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(168, 85, 247, 0.5), 0 0 40px rgba(168, 85, 247, 0.3); }
            50% { box-shadow: 0 0 30px rgba(168, 85, 247, 0.8), 0 0 60px rgba(168, 85, 247, 0.5); }
        }
        @keyframes slide-in-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        .pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }
        .slide-in {
            animation: slide-in-up 0.6s ease-out;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #A855F7 0%, #3B82F6 50%, #10B981 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        .neon-glow {
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.5), 0 0 40px rgba(168, 85, 247, 0.3);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(168, 85, 247, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #A855F7, #3B82F6, #10B981);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 3s ease infinite;
        }
        /* Go to Top Button */
        #goToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #A855F7, #3B82F6);
            color: white;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(168, 85, 247, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
        #goToTop.show {
            display: flex;
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        #goToTop:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 30px rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, #3B82F6, #10B981);
        }
        #goToTop:active {
            transform: translateY(-2px) scale(1.05);
        }
        /* Responsive Typography */
        @media (max-width: 640px) {
            h1 { font-size: 2rem !important; line-height: 1.2; }
            h2 { font-size: 1.75rem !important; }
            h3 { font-size: 1.25rem !important; }
        }
        
        /* Touch-friendly targets */
        @media (hover: none) and (pointer: coarse) {
            a, button {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Cross-browser compatibility */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            width: 100%;
        }
        
        /* Responsive containers */
        .container {
            width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (min-width: 640px) {
            .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
        
        /* Responsive Go to Top Button */
        @media (max-width: 768px) {
            #goToTop {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
        
        @media (max-width: 480px) {
            #goToTop {
                bottom: 15px;
                right: 15px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }
        
        /* Fix for iOS Safari */
        @supports (-webkit-touch-callout: none) {
            body {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
        }
        
        /* Fix for older browsers */
        @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
            .gradient-text {
                background: #A855F7;
                -webkit-text-fill-color: #A855F7;
            }
        }
        
        /* Agency Footer - 1190 W, H varies - Mandatory, Customizable */
        .agency-footer {
            width: 100%;
            min-height: 200px;
            background-color: #f8f8f8;
            border-top: 2px solid #003366;
            padding: 20px 15px;
        }
        
        .dark .agency-footer {
            background-color: #1e293b;
            border-top-color: #3B82F6;
            color: #F1F5F9;
        }
        
        /* Standard Footer - 1190 W, H varies - Mandatory, Locked */
        .standard-footer {
            width: 100%;
            min-height: 150px;
            background-color: #222222;
            color: #ffffff;
            padding: 20px 15px;
            font-size: 12pt;
            font-family: Arial, sans-serif;
        }
        
        .standard-footer h4 {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .standard-footer p,
        .standard-footer li,
        .standard-footer a {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #ffffff;
        }
        
        .standard-footer a {
            color: #ffffff;
            text-decoration: none;
        }
        
        .standard-footer a:hover {
            color: #cccccc;
        }
        
        /* Republic Seal in footer - 36x36px with exact margins */
        .standard-footer .republic-seal-container {
            padding: 0;
            margin: 0;
        }
        
        .standard-footer .republic-seal {
            width: 200px;
            height: 200px;
            margin-left: 13px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-right: 0;
            object-fit: contain;
            display: block;
        }
        
        .dark .standard-footer {
            background-color: #0a0a0a;
        }
        
        /* Responsive adjustments for footers */
        @media (max-width: 768px) {
            .agency-footer,
            .standard-footer {
                padding: 15px 10px;
            }
            
            .standard-footer .republic-seal {
                width: 150px;
                height: 150px;
                margin: 10px auto;
            }
        }
        
        /* DICT/GWTD Layout Specifications */
        .gov-container {
            max-width: 1190px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Top Bar - 1190x45px - Mandatory, Locked */
        .top-bar {
            width: 100%;
            height: 45px;
            background-color: #003366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            font-size: 12px;
            font-family: Arial, Verdana, Tahoma, sans-serif;
        }
        
        .dark .top-bar {
            background-color: #1a1a1a;
        }
        
        /* Search bar styling */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .search-bar input {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 12px;
            width: 200px;
        }
        
        .search-bar button {
            padding: 5px 15px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            min-height: 32px;
        }
        
        .search-bar button:hover {
            background-color: #004488;
        }
        
        .dark .search-bar input {
            background-color: #1e293b;
            color: white;
            border-color: #374151;
        }
        
        .dark .search-bar button {
            background-color: #3B82F6;
        }
        
        .dark .search-bar button:hover {
            background-color: #2563EB;
        }
        
        /* Responsive adjustments for top-bar */
        @media (max-width: 768px) {
            .top-bar {
                height: auto;
                min-height: 45px;
                flex-direction: column;
                padding: 8px 15px;
                font-size: 11px;
            }
            
            .search-bar {
                width: 100%;
                margin-top: 8px;
            }
            
            .search-bar input {
                flex: 1;
                width: auto;
            }
        }
    </style>
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
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    <!-- Top Bar - 1190x45px - Mandatory, Locked -->
    <div class="top-bar">
        <div class="gov-container flex items-center justify-between w-full">
            <div class="flex items-center gap-4">
                <img src="https://ddb.gov.ph/wp-content/uploads/2021/08/republika-ng-pilipinas-1.png" 
                     alt="Republic of the Philippines" 
                     class="h-8 w-auto object-contain">
                <span class="hidden sm:inline">REPUBLIC OF THE PHILIPPINES</span>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search..." id="searchInput" class="dark:bg-gray-800 dark:text-white dark:border-gray-600">
                <button type="button" onclick="handleSearch()" class="dark:bg-blue-600">Search</button>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-[#0F172A]/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between w-full">
                <div class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-[#A855F7] to-[#3B82F6] bg-clip-text text-transparent truncate">
                    Board Portal
                </div>
                <div class="hidden lg:flex items-center space-x-4 xl:space-x-6 flex-shrink-0">
                    <a href="#announcements" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap">Announcements</a>
                    <a href="#meetings" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap">Meetings</a>
                    <a href="#about" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap">About</a>
                    <a href="#contact" class="text-sm xl:text-base hover:text-[#A855F7] transition whitespace-nowrap">Contact</a>
                    <!-- Dark Mode Toggle -->
                    <button id="themeToggle" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                        <span id="themeIcon" class="text-xl xl:text-2xl">🌙</span>
                    </button>
                    <a href="/login" class="px-3 xl:px-4 py-2 text-sm xl:text-base rounded-full border border-[#A855F7] hover:bg-[#A855F7] hover:text-white transition whitespace-nowrap">Login</a>
                    <a href="/register" class="px-3 xl:px-4 py-2 text-sm xl:text-base rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white hover:shadow-lg transition whitespace-nowrap">Register</a>
                </div>
                <div class="flex items-center space-x-2 lg:hidden flex-shrink-0">
                    <button id="themeToggleMobile" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                        <span id="themeIconMobile" class="text-xl">🌙</span>
                    </button>
                    <button id="mobileMenuBtn" class="text-2xl min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle menu" aria-expanded="false">☰</button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white dark:bg-[#0F172A] border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="#announcements" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center">Announcements</a>
                <a href="#meetings" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center">Meetings</a>
                <a href="#about" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center">About</a>
                <a href="#contact" class="block py-2 hover:text-[#A855F7] transition text-base min-h-[44px] flex items-center">Contact</a>
                <a href="/login" class="block px-4 py-3 rounded-full border border-[#A855F7] text-center min-h-[44px] flex items-center justify-center">Login</a>
                <a href="/register" class="block px-4 py-3 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white text-center min-h-[44px] flex items-center justify-center">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden gradient-bg text-white py-12 sm:py-16 md:py-20 lg:py-32">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 sm:top-20 sm:left-20 w-48 h-48 sm:w-72 sm:h-72 bg-white rounded-full blur-3xl float-animation"></div>
            <div class="absolute bottom-10 right-10 sm:bottom-20 sm:right-20 w-64 h-64 sm:w-96 sm:h-96 bg-white rounded-full blur-3xl float-animation" style="animation-delay: 2s;"></div>
        </div>
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black mb-4 sm:mb-6 leading-tight px-2">
                    Welcome to Board Member Portal
                </h1>
                <p class="text-base sm:text-lg md:text-xl lg:text-2xl mb-6 sm:mb-8 opacity-90 px-2">
                    Your gateway to seamless board management, meetings, and collaboration
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-2">
                    <a href="/login" class="px-6 sm:px-8 py-3 sm:py-4 rounded-full bg-white text-[#A855F7] font-bold hover:scale-105 transition transform shadow-xl text-sm sm:text-base min-h-[44px] flex items-center justify-center">
                        Get Started
                    </a>
                    <a href="#about" class="px-6 sm:px-8 py-3 sm:py-4 rounded-full border-2 border-white text-white font-bold hover:bg-white hover:text-[#A855F7] transition text-sm sm:text-base min-h-[44px] flex items-center justify-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Public Announcements Section -->
    <section id="announcements" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 sm:mb-12 gradient-text px-2">
                Public Announcements
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Announcement Card 1 -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#A855F7]/10 to-[#3B82F6]/10 border border-[#A855F7]/20 card-hover slide-in">
                    <div class="w-12 h-12 rounded-full bg-[#A855F7] flex items-center justify-center mb-4 pulse-glow">
                        <span class="text-2xl">📢</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Important Update</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Latest board meeting schedule and agenda items...</p>
                    <a href="#" class="text-[#A855F7] font-semibold hover:underline inline-flex items-center group">
                        Read More <span class="ml-1 group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
                <!-- Announcement Card 2 -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#10B981]/10 to-[#3B82F6]/10 border border-[#10B981]/20 card-hover slide-in" style="animation-delay: 0.1s;">
                    <div class="w-12 h-12 rounded-full bg-[#10B981] flex items-center justify-center mb-4 pulse-glow" style="animation-delay: 0.5s;">
                        <span class="text-2xl">📅</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Upcoming Events</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Mark your calendars for the quarterly review meeting...</p>
                    <a href="#" class="text-[#10B981] font-semibold hover:underline inline-flex items-center group">
                        Read More <span class="ml-1 group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
                <!-- Announcement Card 3 -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#3B82F6]/10 to-[#A855F7]/10 border border-[#3B82F6]/20 card-hover slide-in" style="animation-delay: 0.2s;">
                    <div class="w-12 h-12 rounded-full bg-[#3B82F6] flex items-center justify-center mb-4 pulse-glow" style="animation-delay: 1s;">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">New Features</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Enhanced portal features for better collaboration...</p>
                    <a href="#" class="text-[#3B82F6] font-semibold hover:underline inline-flex items-center group">
                        Read More <span class="ml-1 group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Public Meetings Section -->
    <section id="meetings" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-[#F9FAFB] to-gray-100 dark:from-[#0F172A] dark:to-[#1e293b]">
        <div class="container mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 sm:mb-12 gradient-text px-2">
                Public Meetings
            </h2>
            <div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">
                <!-- Meeting Card 1 -->
                <div class="rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full bg-[#A855F7] text-white text-xs sm:text-sm font-semibold whitespace-nowrap">Upcoming</span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">Dec 15, 2024</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold mb-2">Quarterly Board Meeting</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Review of Q4 performance and strategic planning for next quarter.</p>
                        </div>
                        <a href="#" class="w-full lg:w-auto px-5 sm:px-6 py-2.5 sm:py-3 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white font-semibold hover:scale-105 transition transform text-sm sm:text-base text-center min-h-[44px] flex items-center justify-center">
                            View Details
                        </a>
                    </div>
                </div>
                <!-- Meeting Card 2 -->
                <div class="rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full bg-[#10B981] text-white text-xs sm:text-sm font-semibold whitespace-nowrap">Scheduled</span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">Dec 20, 2024</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold mb-2">Annual General Meeting</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Annual review and election of board members.</p>
                        </div>
                        <a href="#" class="w-full lg:w-auto px-5 sm:px-6 py-2.5 sm:py-3 rounded-full bg-gradient-to-r from-[#10B981] to-[#3B82F6] text-white font-semibold hover:scale-105 transition transform text-sm sm:text-base text-center min-h-[44px] flex items-center justify-center">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section id="vision" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 lg:gap-12">
                <!-- Vision -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-[#A855F7]/20 to-[#3B82F6]/20 border border-[#A855F7]/30">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-[#A855F7] flex items-center justify-center mb-4 sm:mb-6 neon-glow mx-auto md:mx-0">
                        <span class="text-2xl sm:text-3xl">👁️</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center md:text-left">Our Vision</h2>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left">
                        To create a seamless, transparent, and efficient platform that empowers board members to collaborate effectively and make informed decisions for the betterment of our organization.
                    </p>
                </div>
                <!-- Mission -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-[#10B981]/20 to-[#3B82F6]/20 border border-[#10B981]/30">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-[#10B981] flex items-center justify-center mb-4 sm:mb-6 neon-glow mx-auto md:mx-0">
                        <span class="text-2xl sm:text-3xl">🎯</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center md:text-left">Our Mission</h2>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left">
                        To provide a modern, secure, and user-friendly portal that streamlines board operations, enhances communication, and ensures all members have access to the information they need when they need it.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-gray-50 to-white dark:from-[#1e293b] dark:to-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-6 sm:mb-8 gradient-text px-2">
                    About Us
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6 sm:mb-8 px-2">
                    The Board Member Portal is a comprehensive digital platform designed to facilitate seamless communication, 
                    collaboration, and management for board members. Our platform integrates modern technology with intuitive design 
                    to create an exceptional user experience.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mt-8 sm:mt-12">
                    <div class="rounded-2xl p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700">
                        <div class="text-4xl mb-4">🔒</div>
                        <h3 class="text-xl font-bold mb-2">Secure</h3>
                        <p class="text-gray-600 dark:text-gray-400">Enterprise-grade security for all your data</p>
                    </div>
                    <div class="rounded-2xl p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 card-hover">
                        <div class="text-4xl mb-4">⚡</div>
                        <h3 class="text-xl font-bold mb-2">Fast</h3>
                        <p class="text-gray-600 dark:text-gray-400">Lightning-fast performance and reliability</p>
                    </div>
                    <div class="rounded-2xl p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 card-hover">
                        <div class="text-4xl mb-4">🎨</div>
                        <h3 class="text-xl font-bold mb-2">Modern</h3>
                        <p class="text-gray-600 dark:text-gray-400">Beautiful, intuitive interface design</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 sm:mb-12 gradient-text px-2">
                    Contact Us
                </h2>
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 bg-gradient-to-br from-[#A855F7]/10 to-[#3B82F6]/10 border border-[#A855F7]/20">
                    <form id="contactForm" class="space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Name</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent text-base" placeholder="Your name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent text-base" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent text-base" placeholder="What's this about?" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Message</label>
                            <textarea rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent text-base resize-y" placeholder="Your message..." required></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white font-bold hover:scale-105 transition transform shadow-lg text-sm sm:text-base min-h-[44px]">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Agency Footer - 1190 W, H varies - Mandatory, Customizable -->
    <div class="agency-footer">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-bold mb-4 text-[#003366] dark:text-[#3B82F6]">Board Portal</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Modern board management platform for efficient collaboration and communication.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-[#003366] dark:text-[#3B82F6]">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="#announcements" class="hover:text-[#003366] dark:hover:text-[#3B82F6] transition">Announcements</a></li>
                        <li><a href="#meetings" class="hover:text-[#003366] dark:hover:text-[#3B82F6] transition">Meetings</a></li>
                        <li><a href="#about" class="hover:text-[#003366] dark:hover:text-[#3B82F6] transition">About</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-[#003366] dark:text-[#3B82F6]">Account</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="/login" class="hover:text-[#003366] dark:hover:text-[#3B82F6] transition">Login</a></li>
                        <li><a href="/register" class="hover:text-[#003366] dark:hover:text-[#3B82F6] transition">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-[#003366] dark:text-[#3B82F6]">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>Email: info@boardportal.gov.ph</li>
                        <li>Phone: +63 (2) 1234-5678</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Standard Footer - 1190 W, H varies - Mandatory, Locked -->
    <div class="standard-footer">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="republic-seal-container">
                    <img src="https://ddb.gov.ph/wp-content/uploads/2021/08/republika-ng-pilipinas-1.png" 
                         alt="Republic of the Philippines" 
                         class="republic-seal">
                </div>
                <div>
                    <h4 class="mb-2">REPUBLIC OF THE PHILIPPINES</h4>
                    <p>All content is in the public domain unless otherwise stated.</p>
                </div>
                <div>
                    <h4 class="mb-2">ABOUT PORTAL</h4>
                    <p class="mb-2">Learn more about the Board Member Portal, its features, and how it facilitates seamless board management.</p>
                    <ul class="space-y-1" style="list-style: none; padding: 0;">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#announcements">Announcements</a></li>
                        <li><a href="#meetings">Public Meetings</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-2">GOVERNMENT LINKS</h4>
                    <ul class="space-y-1" style="list-style: none; padding: 0;">
                        <li><a href="https://www.gov.ph" target="_blank" rel="noopener noreferrer">GOV.PH</a></li>
                        <li><a href="https://data.gov.ph" target="_blank" rel="noopener noreferrer">Open Data Portal</a></li>
                        <li><a href="https://www.officialgazette.gov.ph" target="_blank" rel="noopener noreferrer">Official Gazette</a></li>
                        <li><a href="https://www.president.gov.ph" target="_blank" rel="noopener noreferrer">Office of the President</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-600 pt-4 text-center">
                <p>&copy; 2024 Board Member Portal. All rights reserved. | Republic of the Philippines</p>
            </div>
        </div>
    </div>

    <!-- Go to Top Floating Button -->
    <button id="goToTop" type="button" aria-label="Go to top" title="Go to top" class="fixed bottom-8 right-8 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </button>

    <script>
        // Dark Mode Toggle with localStorage
        (function() {
            // Get theme from localStorage or default to light
            function getTheme() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    return savedTheme;
                }
                // Check system preference
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            }

            // Apply theme
            function applyTheme(theme) {
                const html = document.documentElement;
                
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                
                // Update icons
                updateThemeIcons(theme);
            }

            // Update theme icons
            function updateThemeIcons(theme) {
                const icon = theme === 'dark' ? '☀️' : '🌙';
                const themeIcon = document.getElementById('themeIcon');
                const themeIconMobile = document.getElementById('themeIconMobile');
                if (themeIcon) themeIcon.textContent = icon;
                if (themeIconMobile) themeIconMobile.textContent = icon;
            }

            // Toggle theme function
            window.toggleTheme = function() {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            };

            // Initialize theme on page load
            const theme = getTheme();
            applyTheme(theme);

            // Search functionality
            window.handleSearch = function() {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    const query = searchInput.value.trim();
                    if (query) {
                        // You can implement actual search functionality here
                        // For now, just show an alert or redirect to search results
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Search',
                                text: 'Searching for: ' + query,
                                confirmButtonColor: '#A855F7'
                            });
                        } else {
                            alert('Searching for: ' + query);
                        }
                    }
                }
            };

            // Wait for DOM
            function initApp() {
                // Theme toggle buttons - Use direct event listeners
                const themeToggle = document.getElementById('themeToggle');
                const themeToggleMobile = document.getElementById('themeToggleMobile');
                
                if (themeToggle) {
                    themeToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.toggleTheme();
                    });
                }
                if (themeToggleMobile) {
                    themeToggleMobile.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.toggleTheme();
                    });
                }

                // Wait for jQuery for other functionality
                if (typeof $ === 'undefined') {
                    setTimeout(initApp, 100);
                    return;
                }

                $(document).ready(function() {
                    // Also bind with jQuery as backup
                    $('#themeToggle, #themeToggleMobile').off('click').on('click', function(e) {
                        e.preventDefault();
                        window.toggleTheme();
                    });

                    // Mobile menu toggle
                    $('#mobileMenuBtn').on('click', function() {
                        const isExpanded = $(this).attr('aria-expanded') === 'true';
                        $('#mobileMenu').toggleClass('hidden');
                        $(this).attr('aria-expanded', !isExpanded);
                    });

                    // Smooth scroll
                    $('a[href^="#"]').on('click', function(e) {
                        e.preventDefault();
                        const target = $(this.getAttribute('href'));
                        if (target.length) {
                            $('html, body').animate({
                                scrollTop: target.offset().top - 80
                            }, 600);
                            $('#mobileMenu').addClass('hidden');
                        }
                    });

                    // Contact form
                    $('#contactForm').on('submit', function(e) {
                        e.preventDefault();
                        alert('Thank you for your message! We will get back to you soon.');
                    });

                    // Go to Top Button
                    const goToTopBtn = document.getElementById('goToTop');
                    
                    // Show/hide button based on scroll position
                    function toggleGoToTop() {
                        if (window.pageYOffset > 300) {
                            goToTopBtn.classList.add('show');
                        } else {
                            goToTopBtn.classList.remove('show');
                        }
                    }

                    // Scroll to top function
                    function scrollToTop() {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }

                    // Event listeners
                    window.addEventListener('scroll', toggleGoToTop);
                    if (goToTopBtn) {
                        goToTopBtn.addEventListener('click', scrollToTop);
                        
                        // Also use jQuery for smooth scroll
                        $(goToTopBtn).on('click', function() {
                            $('html, body').animate({
                                scrollTop: 0
                            }, 600);
                        });
                    }

                    // Initial check
                    toggleGoToTop();
                });
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initApp);
            } else {
                initApp();
            }
        })();
    </script>
</body>
</html>

