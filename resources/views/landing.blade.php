<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Board Member Portal - Welcome</title>
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(5, 84, 152, 0.5), 0 0 40px rgba(5, 84, 152, 0.3); }
            50% { box-shadow: 0 0 30px rgba(5, 84, 152, 0.8), 0 0 60px rgba(5, 84, 152, 0.5); }
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
            background: linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        .neon-glow {
            box-shadow: 0 0 20px rgba(5, 84, 152, 0.5), 0 0 40px rgba(5, 84, 152, 0.3);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(5, 84, 152, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #055498, #123a60, #055498);
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
            background: linear-gradient(135deg, #055498, #123a60);
            color: white;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(5, 84, 152, 0.4);
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
            box-shadow: 0 8px 30px rgba(5, 84, 152, 0.6);
            background: linear-gradient(135deg, #123a60, #055498);
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
        
        /* Ensure sticky navigation works */
        body {
            position: relative;
        }
        
        .top-bar.sticky,
        nav.sticky {
            position: -webkit-sticky;
            position: sticky;
        }
        
        /* Ensure parent containers don't interfere with sticky */
        html {
            overflow-x: hidden;
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
                background: #055498;
                -webkit-text-fill-color: #055498;
            }
        }
        
        /* Line clamp utilities for text truncation */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @include('components.header-footer-styles')
    <style>
        /* Banner - 1190x460px - Mandatory, Customizable */
        .banner {
            width: 100%;
            height: 460px;
            background-color: #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .dark .banner {
            background-color: #1e293b;
        }
        
        /* Banner slideshow */
        .banner-slideshow {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        .banner-slide {
            width: 100%;
            height: 100%;
            display: none;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            padding: 20px;
        }
        
        .banner-slide.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .banner-slide > div {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Banner navigation dots */
        .banner-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .banner-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 12px;
            min-height: 12px;
        }
        
        .banner-dot:hover {
            background-color: rgba(255, 255, 255, 0.8);
            transform: scale(1.2);
        }
        
        .banner-dot.active {
            background-color: white;
            border-color: white;
        }
        
        /* Responsive adjustments for banner */
        @media (max-width: 1190px) {
            .banner {
                width: 100%;
            }
        }
        
        @media (max-width: 1024px) {
            .banner {
                height: 400px;
            }
            
            .banner-slide {
                padding: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .banner {
                height: 350px;
                min-height: 350px;
            }
            
            .banner-slide {
                padding: 20px 15px;
            }
            
            .banner-slide h1 {
                font-size: 1.75rem !important;
                line-height: 1.2;
                margin-bottom: 1rem !important;
            }
            
            .banner-slide p {
                font-size: 1rem !important;
                margin-bottom: 1.5rem !important;
            }
            
            .banner-dots {
                bottom: 15px;
                gap: 8px;
            }
            
            .banner-dot {
                width: 10px;
                height: 10px;
                min-width: 10px;
                min-height: 10px;
                border-width: 1.5px;
            }
        }
        
        @media (max-width: 640px) {
            .banner {
                height: 300px;
                min-height: 300px;
            }
            
            .banner-slide {
                padding: 15px 10px;
            }
            
            .banner-slide h1 {
                font-size: 1.5rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            .banner-slide p {
                font-size: 0.875rem !important;
                margin-bottom: 1rem !important;
            }
            
            .banner-dots {
                bottom: 10px;
                gap: 6px;
            }
        }
        
        @media (max-width: 480px) {
            .banner {
                height: 280px;
                min-height: 280px;
            }
            
            .banner-slide {
                padding: 12px 8px;
            }
            
            .banner-slide h1 {
                font-size: 1.25rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .banner-slide p {
                font-size: 0.75rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            .banner-slide .flex {
                gap: 0.5rem !important;
            }
            
            .banner-slide a {
                padding: 0.5rem 1rem !important;
                font-size: 0.75rem !important;
            }
            
            .banner-dots {
                bottom: 8px;
                gap: 5px;
            }
            
            .banner-dot {
                width: 8px;
                height: 8px;
                min-width: 8px;
                min-height: 8px;
                border-width: 1px;
            }
        }
        
        @media (max-width: 360px) {
            .banner {
                height: 250px;
                min-height: 250px;
            }
            
            .banner-slide h1 {
                font-size: 1.125rem !important;
            }
            
            .banner-slide p {
                font-size: 0.7rem !important;
            }
        }
        
        /* Ensure banner content is always visible and properly centered */
        @media (orientation: landscape) and (max-height: 500px) {
            .banner {
                height: 100vh;
                min-height: 300px;
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
    @include('components.header')

    <!-- Banner - 1190x460px - Mandatory, Customizable -->
    <div class="banner">
        <div class="banner-slideshow">
            <!-- Slide 1 -->
            <div class="banner-slide active" style="background-image: linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Welcome to Board Member Portal</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Your gateway to seamless board management, meetings, and collaboration</p>
                    @guest
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="/login" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="color: #055498;">
                            Get Started
                        </a>
                        <a href="#about" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="hover:color: #055498;" onmouseover="this.style.backgroundColor='white'; this.style.color='#055498';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='white';">
                            Learn More
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="banner-slide" style="background-image: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Efficient Board Management</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Streamline your board operations with our comprehensive portal</p>
                    @guest
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="#announcements" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="color: #055498;">
                            View Announcements
                        </a>
                        <a href="#calendar-activities" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="hover:color: #055498;" onmouseover="this.style.backgroundColor='white'; this.style.color='#055498';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='white';">
                            Calendar Activities
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="banner-slide" style="background-image: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Secure & Modern Platform</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Enterprise-grade security with intuitive design for all board members</p>
                    @guest
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="/register" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="color: #055498;">
                            Register Now
                        </a>
                        <a href="#contact" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center" style="hover:color: #055498;" onmouseover="this.style.backgroundColor='white'; this.style.color='#055498';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='white';">
                            Contact Us
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
            <!-- Navigation Dots -->
            <div class="banner-dots">
                <span class="banner-dot active" data-slide="0"></span>
                <span class="banner-dot" data-slide="1"></span>
                <span class="banner-dot" data-slide="2"></span>
            </div>
        </div>
    </div>

    @auth
    <!-- Activities Calendar Section (Logged In Users Only) -->
    <section id="calendar-activities" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-4 sm:mb-6 gradient-text px-2">
                    Activities Calendar
            </h2>
                <p class="text-center text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 px-2">
                    View meetings, announcements, and scheduled events
                </p>
                <div class="bg-white dark:bg-[#1e293b] rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="mb-3 sm:mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
                                <i class="fas fa-calendar-alt mr-2" style="color: #055498;"></i>
                                Activities Calendar
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">View meetings, announcements, and scheduled events</p>
                    </div>
                        <button id="toggleFilterBtnLanding" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors inline-flex items-center" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <i class="fas fa-filter mr-2"></i>
                            <span>Filter</span>
                        </button>
                </div>

                    <!-- Advanced Filter Panel -->
                    <div id="filterPanelLanding" class="hidden mb-4 p-4 bg-gray-50 dark:bg-[#0F172A] rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Event Type Filter -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Type</label>
                                <select id="filterEventTypeLanding" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm bg-white dark:bg-[#1e293b] text-gray-900 dark:text-gray-100">
                                    <option value="all">All Types</option>
                                    <option value="meeting">Meetings</option>
                                    <option value="announcement">Announcements</option>
                                    <option value="resolution">Resolutions</option>
                                    <option value="other">Other</option>
                                </select>
                    </div>
                            
                            <!-- Date From Filter -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                                <input type="date" id="filterDateFromLanding" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm bg-white dark:bg-[#1e293b] text-gray-900 dark:text-gray-100">
                </div>

                            <!-- Date To Filter -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                                <input type="date" id="filterDateToLanding" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm bg-white dark:bg-[#1e293b] text-gray-900 dark:text-gray-100">
                    </div>
                            
                            <!-- Search Filter -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                                <input type="text" id="filterSearchLanding" placeholder="Search events..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm bg-white dark:bg-[#1e293b] text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500">
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="flex flex-wrap items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button id="clearFiltersBtnLanding" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-[#0F172A] transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Clear Filters
                            </button>
                            <button id="applyFiltersBtnLanding" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                <i class="fas fa-check mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                    
                    <div id="landingCalendar" class="calendar-container-landing"></div>
                </div>
            </div>
        </div>
    </section>
    @endauth

    <!-- Public Announcements Section (Logged-in users only) -->
    @auth
    <section id="announcements" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 sm:mb-12 gap-4">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center sm:text-left gradient-text px-2">
                    Public Announcements
                </h2>
                <a href="{{ route('announcements.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm sm:text-base">
                    <i class="fas fa-list mr-2"></i>
                    View All Announcements
                </a>
            </div>
            
            <!-- Loading State -->
            <div id="announcementsLoading" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-[#055498] mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Loading announcements...</p>
            </div>

            <!-- Empty State -->
            <div id="announcementsEmpty" class="hidden text-center py-12">
                <i class="fas fa-bullhorn text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No announcements available at this time.</p>
            </div>
            
            <!-- Logged-in User Design: News-style cards with images -->
            <div id="announcementsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 hidden">
                <!-- Announcements will be loaded here dynamically (max 3) -->
            </div>
        </div>
    </section>

    <!-- Professional Announcement Modal -->
    <div id="announcementModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 transition-opacity" onclick="closeAnnouncementModal()">
                <div class="absolute inset-0 bg-black opacity-60"></div>
            </div>

            <div class="relative bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl transform transition-all w-full max-w-4xl mx-auto" style="max-height: 90vh; display: flex; flex-direction: column;">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-[#055498] to-[#123a60] px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-bullhorn text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Announcement</h3>
                    </div>
                    <button onclick="closeAnnouncementModal()" class="w-9 h-9 rounded-full hover:bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all duration-200" aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="overflow-y-auto flex-1" style="max-height: calc(90vh - 80px);">
                    <div id="modalLoading" class="text-center py-16">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-[#055498] border-t-transparent mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">Loading announcement...</p>
                    </div>
                    <div id="modalContent" class="hidden">
                        <!-- Author Info -->
                        <div class="px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#055498] to-[#123a60] flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0" id="modalAuthorAvatar" style="font-size: 16px;">
                                    <!-- Initials or avatar -->
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-white text-lg mb-1" id="modalAuthorName"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-2" id="modalDate">
                                        <i class="far fa-calendar-alt text-xs"></i>
                                        <span id="modalDateText"></span>
                                        <span class="mx-1">·</span>
                                        <i class="fas fa-globe-americas text-xs"></i>
                                        <span>Public</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="px-6 pt-6 pb-4">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white leading-tight mb-2" id="modalAnnouncementTitle" style="color: #055498;"></h2>
                        </div>

                        <!-- Banner Image -->
                        <div id="modalBanner" class="mb-6 hidden">
                            <div class="relative overflow-hidden rounded-lg mx-6 shadow-lg">
                                <img src="" alt="Banner" class="w-full h-auto" id="modalBannerImg" style="max-height: 500px; object-fit: cover; display: block;">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="px-6 pb-8">
                            <div class="text-gray-700 dark:text-gray-300 text-base leading-relaxed prose prose-lg max-w-none prose-headings:text-gray-900 prose-headings:dark:text-white prose-p:text-gray-700 prose-p:dark:text-gray-300 prose-strong:text-gray-900 prose-strong:dark:text-white prose-a:text-[#055498] prose-a:no-underline hover:prose-a:underline prose-ul:text-gray-700 prose-ul:dark:text-gray-300 prose-ol:text-gray-700 prose-ol:dark:text-gray-300 prose-li:text-gray-700 prose-li:dark:text-gray-300" id="modalDescription" style="line-height: 1.8;"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#0F172A] rounded-b-2xl flex-shrink-0">
                    <div class="flex items-center justify-end">
                        <button onclick="closeAnnouncementModal()" class="px-6 py-2.5 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    @guest
    <!-- Vision, Mission & Mandate Section -->
    <section id="vision" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 lg:gap-12">
                <!-- Vision -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 mx-auto md:mx-0" style="background: linear-gradient(135deg, rgba(5, 84, 152, 0.2) 0%, rgba(18, 58, 96, 0.2) 100%); border: 1px solid rgba(5, 84, 152, 0.3);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mb-4 sm:mb-6 neon-glow mx-auto md:mx-0" style="background-color: #055498;">
                        <span class="text-2xl sm:text-3xl">👁️</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center md:text-left">Vision</h2>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left">
                        The DDB envisions “Drug-Free Communities”.
                    </p>
                </div>
                <!-- Mission -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 mx-auto md:mx-0" style="background: linear-gradient(135deg, rgba(5, 84, 152, 0.2) 0%, rgba(18, 58, 96, 0.2) 100%); border: 1px solid rgba(5, 84, 152, 0.3);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mb-4 sm:mb-6 neon-glow mx-auto md:mx-0" style="background-color: #055498;">
                        <span class="text-2xl sm:text-3xl">🎯</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center md:text-left">Mission</h2>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left">
                        The Dangerous Drugs Board is committed to stamping out the illicit supply of and demand for dangerous drugs
                        and precursor chemicals, and to promote regional and international cooperation in drug abuse prevention and control.
                    </p>
                </div>
                <!-- Mandate -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 mx-auto md:mx-0" style="background: linear-gradient(135deg, rgba(5, 84, 152, 0.2) 0%, rgba(18, 58, 96, 0.2) 100%); border: 1px solid rgba(5, 84, 152, 0.3);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mb-4 sm:mb-6 neon-glow mx-auto md:mx-0" style="background-color: #055498;">
                        <span class="text-2xl sm:text-3xl">📜</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center md:text-left">Mandate</h2>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left mb-3">
                        Republic Act No. 9165 or the Comprehensive Dangerous Drugs Act of 2002 mandates the DDB to be the
                        policy-making and strategy-formulating body on drug prevention and control.
                    </p>
                    <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed text-center md:text-left">
                        It shall develop and adopt a comprehensive, integrated, unified and balanced national drug abuse
                        prevention and control strategy.
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
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12" style="background: linear-gradient(135deg, rgba(5, 84, 152, 0.1) 0%, rgba(18, 58, 96, 0.1) 100%); border: 1px solid rgba(5, 84, 152, 0.2);">
                    <form id="contactForm" class="space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Name</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:border-transparent text-base" style="focus:ring-color: #055498;" placeholder="Your name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:border-transparent text-base" style="focus:ring-color: #055498;" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:border-transparent text-base" style="focus:ring-color: #055498;" placeholder="What's this about?" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Message</label>
                            <textarea rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:border-transparent text-base resize-y" style="focus:ring-color: #055498;" placeholder="Your message..." required></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 rounded-full text-white font-bold hover:scale-105 transition transform shadow-lg text-sm sm:text-base min-h-[44px]" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @endguest

    @include('components.footer')

    <!-- Go to Top Floating Button -->
    <button id="goToTop" type="button" aria-label="Go to top" title="Go to top" class="hidden fixed bottom-8 right-8 z-50" style="display: none !important;">
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
                // Default to light mode (ignore system preference)
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
                                confirmButtonColor: '#055498'
                            });
                        } else {
                            alert('Searching for: ' + query);
                        }
                    }
                }
            };

            // Banner Slideshow Functionality
            (function() {
                let currentSlide = 0;
                const slides = document.querySelectorAll('.banner-slide');
                const dots = document.querySelectorAll('.banner-dot');
                const totalSlides = slides.length;
                let slideInterval;

                function showSlide(index) {
                    // Remove active class from all slides and dots
                    slides.forEach(slide => slide.classList.remove('active'));
                    dots.forEach(dot => dot.classList.remove('active'));
                    
                    // Add active class to current slide and dot
                    if (slides[index]) {
                        slides[index].classList.add('active');
                    }
                    if (dots[index]) {
                        dots[index].classList.add('active');
                    }
                }

                function nextSlide() {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    showSlide(currentSlide);
                }

                function goToSlide(index) {
                    currentSlide = index;
                    showSlide(currentSlide);
                    resetInterval();
                }

                function resetInterval() {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
                }

                // Initialize slideshow
                function initSlideshow() {
                    if (slides.length === 0) return;
                    
                    showSlide(0);
                    resetInterval();

                    // Add click handlers to dots
                    dots.forEach((dot, index) => {
                        dot.addEventListener('click', () => goToSlide(index));
                    });

                    // Pause on hover
                    const banner = document.querySelector('.banner');
                    if (banner) {
                        banner.addEventListener('mouseenter', () => {
                            clearInterval(slideInterval);
                        });
                        banner.addEventListener('mouseleave', () => {
                            resetInterval();
                        });
                    }
                }

                // Wait for DOM to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSlideshow);
                } else {
                    initSlideshow();
                }
            })();

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
            
            // Handle hash navigation on page load
            function handleHashNavigation() {
                if (window.location.hash) {
                    const hash = window.location.hash.substring(1);
                    const target = document.getElementById(hash);
                    if (target) {
                        setTimeout(() => {
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initApp();
                    handleHashNavigation();
                });
            } else {
                initApp();
                handleHashNavigation();
            }
        })();
    </script>
    @auth
    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Handle logout
        document.getElementById('logoutFormNav')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleLogout();
        });

        document.getElementById('logoutFormMobile')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleLogout();
        });

        async function handleLogout() {
            try {
                const response = await axios.post('/logout');
                
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Logged Out',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.data.redirect;
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred during logout',
                });
            }
        }

        // Handle navigation links - ensure smooth scroll works on landing page
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                // Only prevent default if it's a hash link on the same page
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const target = document.getElementById(targetId);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Update URL without jumping
                        history.pushState(null, null, href);
                    }
                }
            });
        });
    </script>
    @endauth
    
    @auth
    <!-- FullCalendar Script -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
    <script>
        // Initialize Calendar for Landing Page
        (function() {
            // Store all events
            const allEventsLanding = [
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
            
            let calendarLanding = null;
            
            // Filter events function
            function filterEventsLanding() {
                const eventType = document.getElementById('filterEventTypeLanding').value;
                const dateFrom = document.getElementById('filterDateFromLanding').value;
                const dateTo = document.getElementById('filterDateToLanding').value;
                const searchTerm = document.getElementById('filterSearchLanding').value.toLowerCase();
                
                return allEventsLanding.filter(event => {
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
            function applyFiltersLanding() {
                if (calendarLanding) {
                    const filteredEvents = filterEventsLanding();
                    calendarLanding.removeAllEvents();
                    calendarLanding.addEventSource(filteredEvents);
                    
                    // Navigate calendar to date range if dates are set
                    const dateFrom = document.getElementById('filterDateFromLanding').value;
                    const dateTo = document.getElementById('filterDateToLanding').value;
                    
                    if (dateFrom || dateTo) {
                        // If both dates are set
                        if (dateFrom && dateTo) {
                            const fromDate = new Date(dateFrom);
                            const toDate = new Date(dateTo);
                            const daysDiff = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24));
                            
                            // Navigate to the start date
                            calendarLanding.gotoDate(dateFrom);
                            
                            // Adjust view based on date range
                            const isMobile = window.innerWidth < 768;
                            if (isMobile) {
                                // On mobile, use listWeek view
                                if (calendarLanding.view.type !== 'listWeek') {
                                    calendarLanding.changeView('listWeek');
                                }
                            } else {
                                // On desktop, adjust view based on range
                                if (daysDiff <= 7) {
                                    // For ranges up to a week, use week view
                                    if (calendarLanding.view.type !== 'timeGridWeek' && calendarLanding.view.type !== 'timeGridDay') {
                                        calendarLanding.changeView('timeGridWeek');
                                    }
                                } else if (daysDiff <= 31) {
                                    // For ranges up to a month, use month view
                                    if (calendarLanding.view.type !== 'dayGridMonth') {
                                        calendarLanding.changeView('dayGridMonth');
                                    }
                                } else {
                                    // For longer ranges, stay in month view
                                    if (calendarLanding.view.type !== 'dayGridMonth') {
                                        calendarLanding.changeView('dayGridMonth');
                                    }
                                }
                            }
                        } 
                        // If only From Date is set
                        else if (dateFrom) {
                            calendarLanding.gotoDate(dateFrom);
                        } 
                        // If only To Date is set
                        else if (dateTo) {
                            calendarLanding.gotoDate(dateTo);
                        }
                    }
                }
            }
            
            // Clear all filters
            function clearFiltersLanding() {
                document.getElementById('filterEventTypeLanding').value = 'all';
                document.getElementById('filterDateFromLanding').value = '';
                document.getElementById('filterDateToLanding').value = '';
                document.getElementById('filterSearchLanding').value = '';
                
                // Reset calendar to today's date
                if (calendarLanding) {
                    calendarLanding.gotoDate(new Date());
                    // Reset to default view based on screen size
                    const isMobile = window.innerWidth < 768;
                    if (isMobile && calendarLanding.view.type !== 'listWeek') {
                        calendarLanding.changeView('listWeek');
                    } else if (!isMobile && calendarLanding.view.type === 'listWeek') {
                        calendarLanding.changeView('dayGridMonth');
                    }
                }
                
                applyFiltersLanding();
            }
            
            function initLandingCalendar() {
                const calendarEl = document.getElementById('landingCalendar');
                
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
                    setTimeout(initLandingCalendar, 200);
                    return;
                }
                
                try {
                    const isMobile = window.innerWidth < 768;
                    
                    calendarLanding = new FC.Calendar(calendarEl, {
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
                        events: allEventsLanding,
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
                    
                    calendarLanding.render();
                    
                    // Filter panel toggle
                    const toggleFilterBtn = document.getElementById('toggleFilterBtnLanding');
                    const filterPanel = document.getElementById('filterPanelLanding');
                    
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
                    const applyFiltersBtn = document.getElementById('applyFiltersBtnLanding');
                    if (applyFiltersBtn) {
                        applyFiltersBtn.addEventListener('click', applyFiltersLanding);
                    }
                    
                    // Clear filters button
                    const clearFiltersBtn = document.getElementById('clearFiltersBtnLanding');
                    if (clearFiltersBtn) {
                        clearFiltersBtn.addEventListener('click', clearFiltersLanding);
                    }
                    
                    // Auto-apply filters on input change (debounced)
                    let filterTimeout;
                    const filterInputs = ['filterEventTypeLanding', 'filterDateFromLanding', 'filterDateToLanding', 'filterSearchLanding'];
                    filterInputs.forEach(inputId => {
                        const input = document.getElementById(inputId);
                        if (input) {
                            input.addEventListener('change', function() {
                                clearTimeout(filterTimeout);
                                filterTimeout = setTimeout(applyFiltersLanding, 300);
                            });
                            input.addEventListener('input', function() {
                                if (inputId === 'filterSearchLanding') {
                                    clearTimeout(filterTimeout);
                                    filterTimeout = setTimeout(applyFiltersLanding, 500);
                                }
                            });
                        }
                    });
                    
                    // Handle window resize
                    let resizeTimer;
                    window.addEventListener('resize', function() {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function() {
                            const isMobile = window.innerWidth < 768;
                            const currentView = calendarLanding.view.type;
                            
                            if (isMobile && currentView === 'dayGridMonth') {
                                calendarLanding.changeView('listWeek');
                            } else if (!isMobile && currentView === 'listWeek') {
                                calendarLanding.changeView('dayGridMonth');
                            }
                            
                            calendarLanding.setOption('headerToolbar', {
                                left: isMobile ? 'prev,next' : 'prev,next today',
                                center: 'title',
                                right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                            });
                        }, 250);
                    });
                } catch(error) {
                    console.error('Error initializing landing calendar:', error);
                }
            }
            
            // Load FullCalendar script dynamically if not already loaded
            if (typeof FullCalendar === 'undefined' && typeof window.FullCalendar === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js';
                script.onload = function() {
                    setTimeout(initLandingCalendar, 100);
                };
                script.onerror = function() {
                    const altScript = document.createElement('script');
                    altScript.src = 'https://unpkg.com/fullcalendar@6.1.10/index.global.min.js';
                    altScript.onload = function() {
                        setTimeout(initLandingCalendar, 100);
                    };
                    document.head.appendChild(altScript);
                };
                document.head.appendChild(script);
            } else {
                // FullCalendar already loaded, initialize immediately
                setTimeout(initLandingCalendar, 100);
            }
        })();
    </script>
    <style>
        /* Landing Page Calendar Styles */
        .calendar-container-landing {
            min-height: 500px;
            width: 100%;
            overflow-x: auto;
        }
        
        /* FullCalendar Custom Styling for Landing Page */
        #landingCalendar .fc {
            font-family: inherit;
        }
        
        #landingCalendar .fc-header-toolbar {
            margin-bottom: 1.5rem;
            padding: 0.5rem;
        }
        
        #landingCalendar .fc-button {
            background-color: #055498 !important;
            border-color: #055498 !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.2s !important;
        }
        
        #landingCalendar .fc-button:hover {
            background-color: #123a60 !important;
            border-color: #123a60 !important;
        }
        
        #landingCalendar .fc-button-active {
            background-color: #123a60 !important;
            border-color: #123a60 !important;
        }
        
        #landingCalendar .fc-today-button {
            background-color: #FBD116 !important;
            border-color: #FBD116 !important;
            color: #123a60 !important;
        }
        
        #landingCalendar .fc-today-button:hover {
            background-color: #facc15 !important;
            border-color: #facc15 !important;
        }
        
        #landingCalendar .fc-day-today {
            background-color: rgba(5, 84, 152, 0.1) !important;
        }
        
        #landingCalendar .fc-event {
            border-radius: 0.25rem !important;
            padding: 0.25rem 0.5rem !important;
            cursor: pointer !important;
        }
        
        #landingCalendar .fc-event:hover {
            opacity: 0.9 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }
        
        #landingCalendar .fc-daygrid-day-number {
            color: #374151 !important;
            font-weight: 500 !important;
        }
        
        #landingCalendar .fc-col-header-cell {
            background-color: #f9fafb !important;
            padding: 0.75rem 0 !important;
        }
        
        #landingCalendar .fc-col-header-cell-cushion {
            color: #374151 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
        }
        
        #landingCalendar .fc-daygrid-day {
            border-color: #e5e7eb !important;
        }
        
        #landingCalendar .fc-daygrid-day-frame {
            min-height: 100px !important;
        }
        
        @media (max-width: 640px) {
            .calendar-container-landing {
                min-height: 400px;
                padding: 0;
            }
            
            #landingCalendar .fc-header-toolbar {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.5rem 0;
            }
            
            #landingCalendar .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                width: 100%;
                flex-wrap: wrap;
            }
            
            #landingCalendar .fc-button {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
            }
            
            #landingCalendar .fc-toolbar-title {
                font-size: 1rem !important;
                margin: 0.5rem 0 !important;
            }
            
            #landingCalendar .fc-col-header-cell-cushion {
                font-size: 0.625rem !important;
                padding: 0.5rem 0.25rem !important;
            }
            
            #landingCalendar .fc-daygrid-day-number {
                font-size: 0.75rem !important;
                padding: 0.25rem !important;
            }
            
            #landingCalendar .fc-event {
                font-size: 0.75rem !important;
                padding: 0.125rem 0.375rem !important;
                margin: 0.125rem 0 !important;
            }
            
            #landingCalendar .fc-daygrid-day-frame {
                min-height: 60px !important;
            }
            
            #landingCalendar .fc-list-event {
                font-size: 0.875rem !important;
            }
            
            #landingCalendar .fc-list-event-title {
                font-size: 0.875rem !important;
            }
            
            #landingCalendar .fc-list-day-text {
                padding-left: 0.5rem !important;
            }
        }
        
        @media (min-width: 641px) and (max-width: 1024px) {
            .calendar-container-landing {
                min-height: 450px;
            }
            
            #landingCalendar .fc-header-toolbar {
                padding: 0.75rem 0;
            }
            
            #landingCalendar .fc-button {
                padding: 0.5rem 0.875rem !important;
                font-size: 0.875rem !important;
            }
            
            #landingCalendar .fc-toolbar-title {
                font-size: 1.125rem !important;
            }
            
            #landingCalendar .fc-daygrid-day-frame {
                min-height: 80px !important;
            }
            
            #landingCalendar .fc-event {
                font-size: 0.8125rem !important;
            }
        }
        
        @media (min-width: 1025px) {
            .calendar-container-landing {
                min-height: 600px;
            }
            
            #landingCalendar .fc-daygrid-day-frame {
                min-height: 120px !important;
            }
        }
        
        @media (max-width: 1024px) {
            #landingCalendar .fc-scroller {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            
            #landingCalendar .fc-daygrid-body {
                min-width: 100% !important;
            }
        }
        
        @media (hover: none) and (pointer: coarse) {
            #landingCalendar .fc-button {
                min-height: 44px !important;
                min-width: 44px !important;
            }
            
            #landingCalendar .fc-event {
                min-height: 32px !important;
            }
            
            #landingCalendar .fc-daygrid-day-number {
                min-height: 32px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        }
        
        @media (max-width: 640px) {
            #landingCalendar .fc-more-link {
                font-size: 0.75rem !important;
            }
            
            #landingCalendar .fc-popover {
                max-width: 90vw !important;
            }
        }
        
        /* Professional Announcement Modal Styles */
        #announcementModal {
            animation: fadeIn 0.3s ease-out;
        }
        
        #announcementModal > div > div {
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #announcementModal .prose {
            font-size: 1rem;
        }
        
        #announcementModal .prose p {
            margin-bottom: 1.25rem;
        }
        
        #announcementModal .prose ul,
        #announcementModal .prose ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        
        #announcementModal .prose li {
            margin-bottom: 0.5rem;
        }
        
        #announcementModal .prose h1,
        #announcementModal .prose h2,
        #announcementModal .prose h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        
        #announcementModal .prose img {
            border-radius: 0.5rem;
            margin: 1.5rem 0;
        }
        
        #announcementModal .prose a {
            color: #055498;
            font-weight: 500;
        }
        
        #announcementModal .prose a:hover {
            text-decoration: underline;
        }
        
        /* Mobile Responsive */
        @media (max-width: 640px) {
            #announcementModal > div > div {
                max-width: 95vw;
                margin: 1rem;
            }
            
            #announcementModal .prose {
                font-size: 0.9375rem;
            }
            
            #modalAnnouncementTitle {
                font-size: 1.5rem !important;
            }
        }
    </style>
    @endauth
    
    <script>
        // Handle navigation links for non-authenticated users too
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                // Only prevent default if it's a hash link on the same page
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const target = document.getElementById(targetId);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Update URL without jumping
                        history.pushState(null, null, href);
                    }
                }
            });
        });

        @auth
        // User Activity Tracking for Online Status (for authenticated users)
        (function() {
            let activityTimeout;
            let lastActivityTime = Date.now();
            const IDLE_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
            const PING_INTERVAL = 5 * 60 * 1000; // Ping server every 5 minutes

            // Track user activity
            function trackActivity() {
                lastActivityTime = Date.now();
                
                // Clear existing timeout
                clearTimeout(activityTimeout);
                
                // Set new timeout to check for idle
                activityTimeout = setTimeout(function() {
                    checkIdleStatus();
                }, IDLE_TIMEOUT);
            }

            // Check if user is idle
            function checkIdleStatus() {
                const timeSinceLastActivity = Date.now() - lastActivityTime;
                
                if (timeSinceLastActivity >= IDLE_TIMEOUT) {
                    // User has been idle for 30 minutes, show warning
                    Swal.fire({
                        title: 'Session Timeout',
                        text: 'You have been idle for 30 minutes. You will be logged out for security.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Logout user
                        axios.post('{{ route("logout") }}')
                            .then(() => {
                                window.location.href = '/';
                            })
                            .catch(() => {
                                window.location.href = '/';
                            });
                    });
                }
            }

            // Ping server to update activity
            function pingServer() {
                axios.post('{{ route("api.track-activity") }}')
                    .then(response => {
                        if (response.data.success) {
                            // Reset activity tracking when ping succeeds
                            trackActivity();
                        }
                    })
                    .catch(error => {
                        // If ping fails with 401, user might be logged out
                        if (error.response && error.response.status === 401) {
                            console.log('User session expired');
                            window.location.href = '/login';
                        } else {
                            console.log('Activity ping failed');
                        }
                    });
            }

            // Track various user activities
            ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, trackActivity, { passive: true });
            });

            // Initial activity tracking
            trackActivity();

            // Ping server every 5 minutes
            setInterval(pingServer, PING_INTERVAL);

            // Ping server on page visibility change
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    // User came back, immediately ping and track activity
                    pingServer();
                    trackActivity();
                }
            });

            // Ping server when window gains focus
            window.addEventListener('focus', function() {
                // Window gained focus, immediately ping and track activity
                pingServer();
                trackActivity();
            });
            
            // Also track activity on page load/refresh
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    trackActivity();
                    pingServer();
                });
            } else {
                trackActivity();
                pingServer();
            }
        })();
        @endauth
    </script>

    @auth
    <!-- Announcements Loading Script -->
    <script>
        (function() {
            // Set axios defaults
            if (typeof axios !== 'undefined') {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }

            // Load announcements for landing page
            function loadAnnouncements() {
                const loadingEl = document.getElementById('announcementsLoading');
                const emptyEl = document.getElementById('announcementsEmpty');
                const gridEl = document.getElementById('announcementsGrid');

                if (!loadingEl || !emptyEl || !gridEl) return;

                axios.get('{{ route("announcements.api.landing") }}', { params: { limit: 3 } })
                    .then(response => {
                        const announcements = response.data.announcements || [];
                        
                        loadingEl.classList.add('hidden');
                        
                        if (announcements.length === 0) {
                            emptyEl.classList.remove('hidden');
                            return;
                        }

                        emptyEl.classList.add('hidden');
                        gridEl.classList.remove('hidden');
                        
                        // Render announcements
                        gridEl.innerHTML = announcements.map(announcement => {
                            const bannerHtml = announcement.banner_url 
                                ? `<img src="${announcement.banner_url}" alt="${escapeHtml(announcement.title)}" class="w-full h-full object-cover">`
                                : `<div class="text-white text-center px-4">
                                    <svg class="w-16 h-16 mx-auto mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No Image</p>
                                </div>`;

                            return `
                                <div class="bg-white dark:bg-[#1e293b] rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                    <div class="w-full h-48 overflow-hidden flex items-center justify-center" style="background: ${announcement.banner_url ? 'transparent' : 'linear-gradient(135deg, #055498 0%, #123a60 100%)'};">
                                        ${bannerHtml}
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold mb-2 line-clamp-2" style="color: #055498;">
                                            ${escapeHtml(announcement.title)}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">${announcement.created_at}</p>
                                        <div class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3 prose prose-sm max-w-none">
                                            ${announcement.description_short || ''}
                                        </div>
                                        <button onclick="openAnnouncementModal(${announcement.id})" class="inline-block px-6 py-2 text-white font-semibold rounded transition-all duration-200 text-sm hover:shadow-md read-more-btn" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'" onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'">
                                            READ MORE
                                        </button>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    })
                    .catch(error => {
                        console.error('Error loading announcements:', error);
                        loadingEl.classList.add('hidden');
                        emptyEl.classList.remove('hidden');
                    });
            }

            // Escape HTML helper
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
            }

            // Open announcement modal
            window.openAnnouncementModal = function(announcementId) {
                const modal = document.getElementById('announcementModal');
                const modalLoading = document.getElementById('modalLoading');
                const modalContent = document.getElementById('modalContent');
                
                if (!modal) return;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Scroll to top of modal content
                const modalScrollContainer = modal.querySelector('.overflow-y-auto');
                if (modalScrollContainer) {
                    modalScrollContainer.scrollTop = 0;
                }
                
                modalLoading.classList.remove('hidden');
                modalContent.classList.add('hidden');

                axios.get(`{{ url('/announcements/api') }}/${announcementId}/modal`)
                    .then(response => {
                        const announcement = response.data.announcement;
                        
                        // Set author info
                        const authorName = announcement.author;
                        const authorInitials = authorName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                        document.getElementById('modalAuthorAvatar').textContent = authorInitials;
                        document.getElementById('modalAuthorName').textContent = authorName;
                        document.getElementById('modalDateText').textContent = announcement.created_at;

                        // Set banner
                        const bannerEl = document.getElementById('modalBanner');
                        if (announcement.banner_url) {
                            document.getElementById('modalBannerImg').src = announcement.banner_url;
                            bannerEl.classList.remove('hidden');
                        } else {
                            bannerEl.classList.add('hidden');
                        }

                        // Set title and description
                        document.getElementById('modalAnnouncementTitle').textContent = announcement.title;
                        // Render HTML description directly (from CKEditor, already sanitized)
                        const description = announcement.description || '';
                        document.getElementById('modalDescription').innerHTML = description;

                        modalLoading.classList.add('hidden');
                        modalContent.classList.remove('hidden');
                        
                        // Ensure scroll is at top after content loads
                        setTimeout(() => {
                            if (modalScrollContainer) {
                                modalScrollContainer.scrollTop = 0;
                            }
                        }, 100);
                    })
                    .catch(error => {
                        console.error('Error loading announcement:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load announcement details.',
                        });
                        closeAnnouncementModal();
                    });
            };

            // Close announcement modal
            window.closeAnnouncementModal = function() {
                const modal = document.getElementById('announcementModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };

            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAnnouncementModal();
                }
            });

            // Load announcements on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', loadAnnouncements);
            } else {
                loadAnnouncements();
            }
        })();
    </script>
    @endauth
    
    <!-- Global PDF Modal - Available on all pages -->
    @include('components.pdf-modal')
</body>
</html>


