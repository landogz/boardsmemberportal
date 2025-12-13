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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                background: #A855F7;
                -webkit-text-fill-color: #A855F7;
            }
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
            <div class="banner-slide active" style="background-image: linear-gradient(135deg, #A855F7 0%, #3B82F6 50%, #10B981 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Welcome to Board Member Portal</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Your gateway to seamless board management, meetings, and collaboration</p>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="/login" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white text-[#A855F7] rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            Get Started
                        </a>
                        <a href="#about" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white hover:text-[#A855F7] transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="banner-slide" style="background-image: linear-gradient(135deg, #003366 0%, #0066cc 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Efficient Board Management</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Streamline your board operations with our comprehensive portal</p>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="#announcements" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white text-[#003366] rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            View Announcements
                        </a>
                        <a href="#meetings" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white hover:text-[#003366] transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            Upcoming Meetings
                        </a>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="banner-slide" style="background-image: linear-gradient(135deg, #10B981 0%, #3B82F6 100%);">
                <div class="text-center px-2 sm:px-4 text-white relative z-10">
                    <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-2 sm:mb-4 leading-tight">Secure & Modern Platform</h1>
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-6 opacity-90 px-2">Enterprise-grade security with intuitive design for all board members</p>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                        <a href="/register" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 bg-white text-[#10B981] rounded-full font-bold hover:scale-105 transition transform shadow-xl text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            Register Now
                        </a>
                        <a href="#contact" class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-2 sm:py-3 md:py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white hover:text-[#10B981] transition text-xs sm:text-sm md:text-base min-h-[44px] flex items-center justify-center">
                            Contact Us
                        </a>
                    </div>
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

    @include('components.footer')

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
    </script>
</body>
</html>

