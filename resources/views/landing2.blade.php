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
        
        /* MastHead - 1190x140px - Mandatory, Customizable */
        .masthead {
            width: 100%;
            height: 140px;
            background-color: #ffffff;
            border-bottom: 2px solid #003366;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
        }
        
        .masthead img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        /* Banner - 1190x460px - Mandatory, Customizable */
        .banner {
            width: 100%;
            height: 460px;
            background-color: #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        /* Auxiliary Menu - 1190x45px - Optional */
        .auxiliary-menu {
            width: 100%;
            height: 45px;
            background-color: #f8f8f8;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }
        
        /* Content Area - 1190 W, H varies - Min 1 column, Max 3 columns */
        .content-area {
            width: 100%;
            min-height: 400px;
            padding: 20px 15px;
            background-color: #ffffff;
        }
        
        /* Agency Footer - 1190 W, H varies - Mandatory, Customizable */
        .agency-footer {
            width: 100%;
            min-height: 200px;
            background-color: #f8f8f8;
            border-top: 2px solid #003366;
            padding: 20px 15px;
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
        
        /* Responsive adjustments */
        @media (max-width: 1190px) {
            .gov-container {
                max-width: 100%;
            }
            .top-bar, .masthead, .banner, .auxiliary-menu, .content-area, .agency-footer, .standard-footer {
                width: 100%;
            }
            .banner {
                height: auto;
                min-height: 300px;
            }
            .masthead {
                height: auto;
                min-height: 100px;
                flex-direction: column;
                padding: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .top-bar {
                height: auto;
                min-height: 45px;
                flex-direction: column;
                padding: 8px 15px;
                font-size: 11px;
            }
            .banner {
                min-height: 250px;
            }
            .masthead {
                min-height: 80px;
            }
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
        }
        
        /* Menu styling */
        .main-menu {
            display: flex;
            gap: 15px;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .main-menu a,
        .main-menu button {
            color: #003366;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 12px;
            transition: all 0.3s;
            white-space: nowrap;
            min-height: 44px;
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        
        .main-menu a:hover,
        .main-menu button:hover {
            color: #0066cc;
        }
        
        .main-menu .btn-login {
            background-color: #003366;
            color: white;
            border-radius: 6px;
            padding: 8px 16px;
        }
        
        .main-menu .btn-login:hover {
            background-color: #004488;
            color: white;
        }
        
        .main-menu .btn-register {
            background-color: transparent;
            border: 2px solid #003366;
            color: #003366;
            border-radius: 6px;
            padding: 6px 16px;
        }
        
        .main-menu .btn-register:hover {
            background-color: #003366;
            color: white;
        }
        
        .dark .main-menu a,
        .dark .main-menu button {
            color: #3B82F6;
        }
        
        .dark .main-menu a:hover,
        .dark .main-menu button:hover {
            color: #60A5FA;
        }
        
        .dark .main-menu .btn-login {
            background-color: #3B82F6;
        }
        
        .dark .main-menu .btn-login:hover {
            background-color: #2563EB;
        }
        
        .dark .main-menu .btn-register {
            border-color: #3B82F6;
            color: #3B82F6;
        }
        
        .dark .main-menu .btn-register:hover {
            background-color: #3B82F6;
            color: white;
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #003366;
            padding: 8px;
            min-width: 44px;
            min-height: 44px;
            align-items: center;
            justify-content: center;
        }
        
        .dark .mobile-menu-btn {
            color: #3B82F6;
        }
        
        /* Mobile menu */
        .mobile-menu {
            display: none;
            width: 100%;
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 15px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 50;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-menu.active {
            display: block;
        }
        
        .mobile-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .mobile-menu li {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .mobile-menu li:last-child {
            border-bottom: none;
        }
        
        .mobile-menu a,
        .mobile-menu button {
            display: block;
            padding: 12px 0;
            color: #003366;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            min-height: 44px;
            align-items: center;
            display: flex;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
        }
        
        .mobile-menu a:hover,
        .mobile-menu button:hover {
            color: #0066cc;
        }
        
        .mobile-menu .btn-login {
            background-color: #003366;
            color: white;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            justify-content: center;
            margin-top: 5px;
        }
        
        .mobile-menu .btn-login:hover {
            background-color: #004488;
            color: white;
        }
        
        .mobile-menu .btn-register {
            background-color: transparent;
            border: 2px solid #003366;
            color: #003366;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            justify-content: center;
            margin-top: 5px;
        }
        
        .mobile-menu .btn-register:hover {
            background-color: #003366;
            color: white;
        }
        
        .dark .mobile-menu {
            background-color: #0F172A;
            border-top-color: #374151;
        }
        
        .dark .mobile-menu li {
            border-bottom-color: #374151;
        }
        
        .dark .mobile-menu a,
        .dark .mobile-menu button {
            color: #3B82F6;
        }
        
        .dark .mobile-menu a:hover,
        .dark .mobile-menu button:hover {
            color: #60A5FA;
        }
        
        .dark .mobile-menu .btn-login {
            background-color: #3B82F6;
        }
        
        .dark .mobile-menu .btn-login:hover {
            background-color: #2563EB;
        }
        
        .dark .mobile-menu .btn-register {
            border-color: #3B82F6;
            color: #3B82F6;
        }
        
        .dark .mobile-menu .btn-register:hover {
            background-color: #3B82F6;
            color: white;
        }
        
        
        /* Responsive menu */
        @media (max-width: 1024px) {
            .main-menu {
                gap: 10px;
            }
            
            .main-menu a {
                font-size: 13px;
            }
        }
        
        @media (max-width: 768px) {
            .main-menu {
                display: none;
            }
            
            .mobile-menu-btn {
                display: flex;
            }
            
            .masthead {
                position: relative;
            }
        }
        
        @media (max-width: 640px) {
            .masthead .flex.items-center.gap-4.flex-1 {
                flex-wrap: wrap;
            }
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
        }
        
        .banner-slide.active {
            display: block;
        }
        
        /* Content columns */
        .content-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        @media (max-width: 992px) {
            .content-columns {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .content-columns {
                grid-template-columns: 1fr;
            }
        }
        
        /* Dark mode support */
        .dark .top-bar {
            background-color: #1a1a1a;
        }
        
        .dark .masthead {
            background-color: #0F172A;
            border-bottom-color: #3B82F6;
        }
        
        .dark .banner {
            background-color: #1e293b;
        }
        
        .dark .auxiliary-menu {
            background-color: #1e293b;
            border-bottom-color: #374151;
        }
        
        .dark .content-area {
            background-color: #0F172A;
            color: #F1F5F9;
        }
        
        .dark .agency-footer {
            background-color: #1e293b;
            border-top-color: #3B82F6;
            color: #F1F5F9;
        }
        
        .dark .standard-footer {
            background-color: #0a0a0a;
        }
        
        /* Touch-friendly targets */
        @media (hover: none) and (pointer: coarse) {
            a, button {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            width: 100%;
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
        }
        
        @media (max-width: 768px) {
            #goToTop {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 20px;
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
<body class="bg-white dark:bg-[#0F172A] text-gray-900 dark:text-[#F1F5F9] transition-colors duration-300">
    
    <!-- 1. Top Bar - 1190x45px - Mandatory, Locked -->
    <div class="top-bar">
        <div class="gov-container flex items-center justify-between w-full">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/republica.png') }}" 
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

    <!-- 2. MastHead - 1190x140px - Mandatory, Customizable -->
    <div class="masthead" style="position: relative;">
        <div class="gov-container flex items-center justify-between w-full flex-wrap gap-4">
            <div class="flex items-center gap-4 flex-1">
                <img src="https://ddb.gov.ph/wp-content/uploads/2021/08/DDB_Website_Header1.png" 
                     alt="Agency Logo" 
                     class="h-12 sm:h-14 md:h-16 w-auto object-contain max-h-[70px]">
                <!-- Main Menu - Desktop -->
                <ul class="main-menu hidden md:flex">
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#meetings">Meetings</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#vision">Vision & Mission</a></li>
                    <li>
                        <button id="themeToggle" type="button" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                            <span id="themeIcon">🌙</span>
                        </button>
                    </li>
                    <li><a href="/login" class="btn-login">Login</a></li>
                    <li><a href="/register" class="btn-register">Register</a></li>
                </ul>
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="mobile-menu-btn md:hidden" aria-label="Toggle menu" aria-expanded="false">
                    <span id="menuIcon">☰</span>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu">
            <ul>
                <li><a href="#announcements">Announcements</a></li>
                <li><a href="#meetings">Meetings</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#vision">Vision & Mission</a></li>
                <li>
                    <button id="themeToggleMobile" type="button" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                        <span id="themeIconMobile">🌙</span>
                    </button>
                </li>
                <li><a href="/login" class="btn-login">Login</a></li>
                <li><a href="/register" class="btn-register">Register</a></li>
            </ul>
        </div>
    </div>

    <!-- 3. Banner - 1190x460px - Mandatory, Customizable -->
    <div class="banner">
        <div class="banner-slideshow">
            <div class="banner-slide active" style="background-image: linear-gradient(135deg, #003366 0%, #0066cc 100%); display: flex; align-items: center; justify-content: center; color: white;">
                <div class="text-center px-4">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4">Welcome to Board Member Portal</h1>
                    <p class="text-lg sm:text-xl mb-6">Your gateway to seamless board management and collaboration</p>
                    <a href="#content" class="px-6 py-3 bg-white text-[#003366] rounded-lg font-semibold hover:opacity-90 transition inline-block">Get Started</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Auxiliary Menu - 1190x45px - Optional (Hidden - Menu moved to MastHead) -->
    <!-- Menu is now in MastHead section -->

    <!-- 5. Content Area - 1190 W, H varies - Min 1 column, Max 3 columns -->
    <div class="content-area" id="content">
        <div class="gov-container">
            <!-- Public Announcements Section -->
            <section id="announcements" class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-[#003366] dark:text-[#3B82F6]">Public Announcements</h2>
                <div class="content-columns">
                    <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="text-3xl mb-3">📢</div>
                        <h3 class="text-xl font-bold mb-2">Important Update</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Latest board meeting schedule and agenda items...</p>
                        <a href="#" class="text-[#003366] dark:text-[#3B82F6] font-semibold hover:underline">Read More →</a>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="text-3xl mb-3">📅</div>
                        <h3 class="text-xl font-bold mb-2">Upcoming Events</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Mark your calendars for the quarterly review meeting...</p>
                        <a href="#" class="text-[#003366] dark:text-[#3B82F6] font-semibold hover:underline">Read More →</a>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="text-3xl mb-3">🎯</div>
                        <h3 class="text-xl font-bold mb-2">New Features</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Enhanced portal features for better collaboration...</p>
                        <a href="#" class="text-[#003366] dark:text-[#3B82F6] font-semibold hover:underline">Read More →</a>
                    </div>
                </div>
            </section>

            <!-- Public Meetings Section -->
            <section id="meetings" class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-[#003366] dark:text-[#3B82F6]">Public Meetings</h2>
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="px-3 py-1 bg-[#003366] dark:bg-[#3B82F6] text-white text-sm font-semibold rounded-full inline-block mb-2">Upcoming</span>
                                <h3 class="text-xl font-bold mb-2">Quarterly Board Meeting</h3>
                                <p class="text-gray-600 dark:text-gray-400">Dec 15, 2024 - Review of Q4 performance</p>
                            </div>
                            <a href="#" class="px-6 py-2 bg-[#003366] dark:bg-[#3B82F6] text-white rounded hover:opacity-90 transition text-center">View Details</a>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded-full inline-block mb-2">Scheduled</span>
                                <h3 class="text-xl font-bold mb-2">Annual General Meeting</h3>
                                <p class="text-gray-600 dark:text-gray-400">Dec 20, 2024 - Annual review and elections</p>
                            </div>
                            <a href="#" class="px-6 py-2 bg-[#003366] dark:bg-[#3B82F6] text-white rounded hover:opacity-90 transition text-center">View Details</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vision & Mission Section -->
            <section id="vision" class="mb-12">
                <div class="content-columns">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-3xl mb-3">👁️</div>
                        <h2 class="text-2xl font-bold mb-4 text-[#003366] dark:text-[#3B82F6]">Our Vision</h2>
                        <p class="text-gray-700 dark:text-gray-300">To create a seamless, transparent, and efficient platform that empowers board members to collaborate effectively.</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-3xl mb-3">🎯</div>
                        <h2 class="text-2xl font-bold mb-4 text-[#003366] dark:text-[#3B82F6]">Our Mission</h2>
                        <p class="text-gray-700 dark:text-gray-300">To provide a modern, secure, and user-friendly portal that streamlines board operations and enhances communication.</p>
                    </div>
                </div>
            </section>

            <!-- About Us Section -->
            <section id="about" class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-[#003366] dark:text-[#3B82F6]">About Us</h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-8 leading-relaxed">
                    The Board Member Portal is a comprehensive digital platform designed to facilitate seamless communication, 
                    collaboration, and management for board members. Our platform integrates modern technology with intuitive design 
                    to create an exceptional user experience.
                </p>
                <div class="content-columns">
                    <div class="text-center p-6">
                        <div class="text-4xl mb-3">🔒</div>
                        <h3 class="text-xl font-bold mb-2">Secure</h3>
                        <p class="text-gray-600 dark:text-gray-400">Enterprise-grade security</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="text-4xl mb-3">⚡</div>
                        <h3 class="text-xl font-bold mb-2">Fast</h3>
                        <p class="text-gray-600 dark:text-gray-400">Lightning-fast performance</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="text-4xl mb-3">🎨</div>
                        <h3 class="text-xl font-bold mb-2">Modern</h3>
                        <p class="text-gray-600 dark:text-gray-400">Beautiful, intuitive design</p>
                    </div>
                </div>
            </section>

            <!-- Contact Us Section -->
            <section id="contact" class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-[#003366] dark:text-[#3B82F6]">Contact Us</h2>
                <div class="bg-gray-50 dark:bg-[#1e293b] p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                    <form id="contactForm" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Name</label>
                                <input type="text" class="w-full px-4 py-3 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0F172A] focus:ring-2 focus:ring-[#003366] dark:focus:ring-[#3B82F6] focus:border-transparent" placeholder="Your name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0F172A] focus:ring-2 focus:ring-[#003366] dark:focus:ring-[#3B82F6] focus:border-transparent" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-3 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0F172A] focus:ring-2 focus:ring-[#003366] dark:focus:ring-[#3B82F6] focus:border-transparent" placeholder="What's this about?" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Message</label>
                            <textarea rows="5" class="w-full px-4 py-3 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0F172A] focus:ring-2 focus:ring-[#003366] dark:focus:ring-[#3B82F6] focus:border-transparent resize-y" placeholder="Your message..." required></textarea>
                        </div>
                        <button type="submit" class="px-8 py-3 bg-[#003366] dark:bg-[#3B82F6] text-white rounded hover:opacity-90 transition font-semibold min-h-[44px]">
                            Send Message
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <!-- 6. Agency Footer - 1190 W, H varies - Mandatory, Customizable -->
    <div class="agency-footer">
        <div class="gov-container">
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

    <!-- 7. Standard Footer - 1190 W, H varies - Mandatory, Locked -->
    <div class="standard-footer">
        <div class="gov-container">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="republic-seal-container">
                    <img src="{{ asset('images/republica.png') }}" 
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
                        <li><a href="https://www.gov.ph" target="_blank">GOV.PH</a></li>
                        <li><a href="https://data.gov.ph" target="_blank">Open Data Portal</a></li>
                        <li><a href="https://www.officialgazette.gov.ph" target="_blank">Official Gazette</a></li>
                        <li><a href="https://www.president.gov.ph" target="_blank">Office of the President</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-600 pt-4 text-center">
                <p>&copy; 2024 Board Member Portal. All rights reserved. | Republic of the Philippines</p>
            </div>
        </div>
    </div>

    <!-- Go to Top Floating Button -->
    <button id="goToTop" type="button" aria-label="Go to top" title="Go to top">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </button>

    <script>
        // Dark Mode Toggle with localStorage
        (function() {
            function getTheme() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    return savedTheme;
                }
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            }

            function applyTheme(theme) {
                const html = document.documentElement;
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                updateThemeIcons(theme);
            }

            function updateThemeIcons(theme) {
                const icon = theme === 'dark' ? '☀️' : '🌙';
                const themeIcon = document.getElementById('themeIcon');
                const themeIconMobile = document.getElementById('themeIconMobile');
                if (themeIcon) themeIcon.textContent = icon;
                if (themeIconMobile) themeIconMobile.textContent = icon;
            }

            window.toggleTheme = function() {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            };

            const theme = getTheme();
            applyTheme(theme);
        })();

        // Search functionality
        function handleSearch() {
            const searchInput = document.getElementById('searchInput');
            const query = searchInput.value.trim();
            if (query) {
                alert('Searching for: ' + query);
                // Implement actual search functionality here
            }
        }

        // Wait for jQuery
        function initApp() {
            if (typeof $ === 'undefined') {
                setTimeout(initApp, 100);
                return;
            }

            $(document).ready(function() {
                // Mobile menu toggle
                $('#mobileMenuBtn').on('click', function() {
                    const menu = $('#mobileMenu');
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    menu.toggleClass('active');
                    $(this).attr('aria-expanded', !isExpanded);
                    $('#menuIcon').text(menu.hasClass('active') ? '✕' : '☰');
                });
                
                // Close mobile menu when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.masthead').length) {
                        $('#mobileMenu').removeClass('active');
                        $('#mobileMenuBtn').attr('aria-expanded', 'false');
                        $('#menuIcon').text('☰');
                    }
                });
                
                // Smooth scroll
                $('a[href^="#"]').on('click', function(e) {
                    e.preventDefault();
                    const target = $(this.getAttribute('href'));
                    if (target.length) {
                        $('html, body').animate({
                            scrollTop: target.offset().top - 100
                        }, 600);
                        // Close mobile menu after clicking
                        $('#mobileMenu').removeClass('active');
                        $('#mobileMenuBtn').attr('aria-expanded', 'false');
                        $('#menuIcon').text('☰');
                    }
                });

                // Contact form
                $('#contactForm').on('submit', function(e) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thank you!',
                            text: 'Your message has been sent. We will get back to you soon.',
                            confirmButtonColor: '#003366'
                        });
                    } else {
                        alert('Thank you for your message! We will get back to you soon.');
                    }
                });

                // Go to Top Button
                const goToTopBtn = document.getElementById('goToTop');
                function toggleGoToTop() {
                    if (window.pageYOffset > 300) {
                        goToTopBtn.classList.add('show');
                    } else {
                        goToTopBtn.classList.remove('show');
                    }
                }

                function scrollToTop() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }

                window.addEventListener('scroll', toggleGoToTop);
                if (goToTopBtn) {
                    goToTopBtn.addEventListener('click', scrollToTop);
                    $(goToTopBtn).on('click', function() {
                        $('html, body').animate({ scrollTop: 0 }, 600);
                    });
                }
                toggleGoToTop();
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initApp);
        } else {
            initApp();
        }
    </script>
</body>
</html>
