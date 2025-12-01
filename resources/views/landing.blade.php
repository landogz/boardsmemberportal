<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-[#0F172A]/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="text-2xl font-bold bg-gradient-to-r from-[#A855F7] to-[#3B82F6] bg-clip-text text-transparent">
                    Board Portal
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#announcements" class="hover:text-[#A855F7] transition">Announcements</a>
                    <a href="#meetings" class="hover:text-[#A855F7] transition">Meetings</a>
                    <a href="#about" class="hover:text-[#A855F7] transition">About</a>
                    <a href="#contact" class="hover:text-[#A855F7] transition">Contact</a>
                    <!-- Dark Mode Toggle -->
                    <button id="themeToggle" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                        <span id="themeIcon" class="text-2xl">🌙</span>
                    </button>
                    <a href="/login" class="px-4 py-2 rounded-full border border-[#A855F7] hover:bg-[#A855F7] hover:text-white transition">Login</a>
                    <a href="/register" class="px-4 py-2 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white hover:shadow-lg transition">Register</a>
                </div>
                <div class="flex items-center space-x-2 md:hidden">
                    <button id="themeToggleMobile" type="button" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer" aria-label="Toggle dark mode" onclick="window.toggleTheme && window.toggleTheme()">
                        <span id="themeIconMobile" class="text-2xl">🌙</span>
                    </button>
                    <button id="mobileMenuBtn" class="text-2xl">☰</button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white dark:bg-[#0F172A] border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-4 py-4 space-y-4">
                <a href="#announcements" class="block hover:text-[#A855F7] transition">Announcements</a>
                <a href="#meetings" class="block hover:text-[#A855F7] transition">Meetings</a>
                <a href="#about" class="block hover:text-[#A855F7] transition">About</a>
                <a href="#contact" class="block hover:text-[#A855F7] transition">Contact</a>
                <a href="/login" class="block px-4 py-2 rounded-full border border-[#A855F7] text-center">Login</a>
                <a href="/register" class="block px-4 py-2 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white text-center">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden gradient-bg text-white py-20 md:py-32">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl float-animation"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl float-animation" style="animation-delay: 2s;"></div>
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight">
                    Welcome to Board Member Portal
                </h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">
                    Your gateway to seamless board management, meetings, and collaboration
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/login" class="px-8 py-4 rounded-full bg-white text-[#A855F7] font-bold hover:scale-105 transition transform shadow-xl">
                        Get Started
                    </a>
                    <a href="#about" class="px-8 py-4 rounded-full border-2 border-white text-white font-bold hover:bg-white hover:text-[#A855F7] transition">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Public Announcements Section -->
    <section id="announcements" class="py-16 md:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-center mb-12 gradient-text">
                Public Announcements
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
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
    <section id="meetings" class="py-16 md:py-24 bg-gradient-to-br from-[#F9FAFB] to-gray-100 dark:from-[#0F172A] dark:to-[#1e293b]">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-center mb-12 gradient-text">
                Public Meetings
            </h2>
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Meeting Card 1 -->
                <div class="rounded-3xl p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-[#A855F7] text-white text-sm font-semibold">Upcoming</span>
                                <span class="text-gray-500 dark:text-gray-400">Dec 15, 2024</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Quarterly Board Meeting</h3>
                            <p class="text-gray-600 dark:text-gray-400">Review of Q4 performance and strategic planning for next quarter.</p>
                        </div>
                        <a href="#" class="px-6 py-3 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white font-semibold hover:scale-105 transition transform">
                            View Details
                        </a>
                    </div>
                </div>
                <!-- Meeting Card 2 -->
                <div class="rounded-3xl p-6 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-[#10B981] text-white text-sm font-semibold">Scheduled</span>
                                <span class="text-gray-500 dark:text-gray-400">Dec 20, 2024</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Annual General Meeting</h3>
                            <p class="text-gray-600 dark:text-gray-400">Annual review and election of board members.</p>
                        </div>
                        <a href="#" class="px-6 py-3 rounded-full bg-gradient-to-r from-[#10B981] to-[#3B82F6] text-white font-semibold hover:scale-105 transition transform">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section id="vision" class="py-16 md:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12">
                <!-- Vision -->
                <div class="rounded-3xl p-8 bg-gradient-to-br from-[#A855F7]/20 to-[#3B82F6]/20 border border-[#A855F7]/30">
                    <div class="w-16 h-16 rounded-full bg-[#A855F7] flex items-center justify-center mb-6 neon-glow">
                        <span class="text-3xl">👁️</span>
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Our Vision</h2>
                    <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                        To create a seamless, transparent, and efficient platform that empowers board members to collaborate effectively and make informed decisions for the betterment of our organization.
                    </p>
                </div>
                <!-- Mission -->
                <div class="rounded-3xl p-8 bg-gradient-to-br from-[#10B981]/20 to-[#3B82F6]/20 border border-[#10B981]/30">
                    <div class="w-16 h-16 rounded-full bg-[#10B981] flex items-center justify-center mb-6 neon-glow">
                        <span class="text-3xl">🎯</span>
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Our Mission</h2>
                    <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                        To provide a modern, secure, and user-friendly portal that streamlines board operations, enhances communication, and ensures all members have access to the information they need when they need it.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-16 md:py-24 bg-gradient-to-br from-gray-50 to-white dark:from-[#1e293b] dark:to-[#0F172A]">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-8 gradient-text">
                    About Us
                </h2>
                <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed mb-8">
                    The Board Member Portal is a comprehensive digital platform designed to facilitate seamless communication, 
                    collaboration, and management for board members. Our platform integrates modern technology with intuitive design 
                    to create an exceptional user experience.
                </p>
                <div class="grid md:grid-cols-3 gap-8 mt-12">
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
    <section id="contact" class="py-16 md:py-24 bg-white dark:bg-[#0F172A]">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-12 gradient-text">
                    Contact Us
                </h2>
                <div class="rounded-3xl p-8 md:p-12 bg-gradient-to-br from-[#A855F7]/10 to-[#3B82F6]/10 border border-[#A855F7]/20">
                    <form id="contactForm" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Name</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent" placeholder="Your name">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent" placeholder="your@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent" placeholder="What's this about?">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Message</label>
                            <textarea rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e293b] focus:ring-2 focus:ring-[#A855F7] focus:border-transparent" placeholder="Your message..."></textarea>
                        </div>
                        <button type="submit" class="w-full md:w-auto px-8 py-4 rounded-full bg-gradient-to-r from-[#A855F7] to-[#3B82F6] text-white font-bold hover:scale-105 transition transform shadow-lg">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-[#1a1a1a] text-gray-300 dark:text-gray-400 py-12 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Left Section: Republic of the Philippines Emblem -->
                <div class="md:col-span-1">
                    <div class="mb-4">
                        <img src="https://ddb.gov.ph/wp-content/uploads/2021/08/republika-ng-pilipinas-1.png" 
                             alt="Republic of the Philippines" 
                             class="w-32 h-auto">
                    </div>
                </div>

                <!-- Second Section: Public Domain Content -->
                <div class="md:col-span-1">
                    <h4 class="text-blue-400 dark:text-blue-500 font-bold mb-4 text-sm uppercase tracking-wide">REPUBLIC OF THE PHILIPPINES</h4>
                    <p class="text-gray-300 dark:text-gray-400 text-sm leading-relaxed">
                        All content is in the public domain unless otherwise stated.
                    </p>
                </div>

                <!-- Third Section: About Portal -->
                <div class="md:col-span-1">
                    <h4 class="text-blue-400 dark:text-blue-500 font-bold mb-4 text-sm uppercase tracking-wide">ABOUT PORTAL</h4>
                    <p class="text-gray-300 dark:text-gray-400 text-sm leading-relaxed mb-4">
                        Learn more about the Board Member Portal, its features, and how it facilitates seamless board management and collaboration.
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="#about" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="#announcements" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Announcements
                            </a>
                        </li>
                        <li>
                            <a href="#meetings" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Public Meetings
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Fourth Section: Quick Links -->
                <div class="md:col-span-1">
                    <h4 class="text-blue-400 dark:text-blue-500 font-bold mb-4 text-sm uppercase tracking-wide">QUICK LINKS</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="/login" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/register" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Register
                            </a>
                        </li>
                        <li>
                            <a href="#contact" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Contact Us
                            </a>
                        </li>
                        <li>
                            <a href="#vision" class="text-gray-300 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 transition flex items-center">
                                <span class="w-2 h-2 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></span>
                                Vision & Mission
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 dark:border-gray-600 pt-6 mt-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-400 dark:text-gray-500">
                    <p>&copy; 2024 Board Member Portal. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Republic of the Philippines</p>
                </div>
            </div>
        </div>
    </footer>

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
                        $('#mobileMenu').toggleClass('hidden');
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

