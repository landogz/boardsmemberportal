<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Board Member Portal - Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* DICT/GWTD Compliance Styles */
        body {
            font-family: Verdana, Arial, Tahoma, sans-serif;
            font-size: 12pt; /* 10-12pt as per DICT guidelines */
            line-height: 1.6;
            color: #000000;
            background-color: #FFFFFF;
        }
        
        .content-area {
            background-color: #FFFFFF;
            color: #000000;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: Verdana, Arial, Tahoma, sans-serif;
            font-weight: bold;
        }
        
        h1 {
            font-size: 24pt;
        }
        
        h2 {
            font-size: 20pt;
        }
        
        h3 {
            font-size: 16pt;
        }
        
        p, li, td, th {
            font-size: 12pt; /* 10-12pt for content */
        }
        
        a {
            color: #0066CC;
            text-decoration: underline;
        }
        
        a:hover, a:focus {
            color: #004499;
        }
        
        .btn-primary {
            background-color: #0066CC;
            color: #FFFFFF;
            border: 1px solid #0066CC;
            padding: 8px 16px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #004499;
            border-color: #004499;
        }
        
        .btn-secondary {
            background-color: #FFFFFF;
            color: #0066CC;
            border: 1px solid #0066CC;
            padding: 8px 16px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #F0F0F0;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        table th, table td {
            border: 1px solid #CCCCCC;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            background-color: #F0F0F0;
            font-weight: bold;
        }
        
        .card {
            background-color: #FFFFFF;
            border: 1px solid #CCCCCC;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .header-bg {
            background-color: #003366;
            color: #FFFFFF;
        }
        
        .footer-bg {
            background-color: #333333;
            color: #FFFFFF;
        }
        
        /* Accessibility: Skip to content link */
        .skip-link {
            position: absolute;
            left: -9999px;
            z-index: 999;
        }
        
        .skip-link:focus {
            left: 6px;
            top: 6px;
            background-color: #FFFFFF;
            color: #000000;
            padding: 8px;
            border: 2px solid #000000;
        }
        
        /* Focus indicators for accessibility */
        *:focus {
            outline: 2px solid #0066CC;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Skip to content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="header-bg" role="navigation" aria-label="Main navigation">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between flex-wrap">
                <div class="text-xl font-bold">
                    Board Member Portal
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#announcements" class="text-white hover:underline">Announcements</a>
                    <a href="#meetings" class="text-white hover:underline">Meetings</a>
                    <a href="#vision" class="text-white hover:underline">Vision & Mission</a>
                    <a href="#about" class="text-white hover:underline">About</a>
                    <a href="#contact" class="text-white hover:underline">Contact</a>
                    <a href="/login" class="btn-secondary">Login</a>
                    <a href="/register" class="btn-primary">Register</a>
                </div>
                <button id="mobileMenuBtn" class="md:hidden text-white text-xl" aria-label="Toggle mobile menu" aria-expanded="false">
                    ☰ Menu
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-gray-600">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="#announcements" class="block text-white hover:underline">Announcements</a>
                <a href="#meetings" class="block text-white hover:underline">Meetings</a>
                <a href="#vision" class="block text-white hover:underline">Vision & Mission</a>
                <a href="#about" class="block text-white hover:underline">About</a>
                <a href="#contact" class="block text-white hover:underline">Contact</a>
                <a href="/login" class="block btn-secondary text-center">Login</a>
                <a href="/register" class="block btn-primary text-center">Register</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="content-area">
        <!-- Hero Section -->
        <section class="header-bg py-12 md:py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="mb-4">Welcome to Board Member Portal</h1>
                    <p class="text-lg mb-8">Your gateway to seamless board management, meetings, and collaboration</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/login" class="btn-primary">Get Started</a>
                        <a href="#about" class="btn-secondary">Learn More</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Public Announcements Section -->
        <section id="announcements" class="content-area py-12 md:py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-center mb-8">Public Announcements</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Announcement Card 1 -->
                    <div class="card">
                        <h3 class="mb-3">Important Update</h3>
                        <p class="mb-4">Latest board meeting schedule and agenda items for the upcoming quarter. Please review the documents and prepare your questions.</p>
                        <a href="#" class="font-bold">Read More →</a>
                    </div>
                    <!-- Announcement Card 2 -->
                    <div class="card">
                        <h3 class="mb-3">Upcoming Events</h3>
                        <p class="mb-4">Mark your calendars for the quarterly review meeting scheduled for next month. All board members are expected to attend.</p>
                        <a href="#" class="font-bold">Read More →</a>
                    </div>
                    <!-- Announcement Card 3 -->
                    <div class="card">
                        <h3 class="mb-3">New Features</h3>
                        <p class="mb-4">Enhanced portal features for better collaboration and communication. Check out the latest updates and improvements.</p>
                        <a href="#" class="font-bold">Read More →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Public Meetings Section -->
        <section id="meetings" class="content-area py-12 md:py-16" style="background-color: #F9F9F9;">
            <div class="container mx-auto px-4">
                <h2 class="text-center mb-8">Public Meetings</h2>
                <div class="max-w-4xl mx-auto space-y-4">
                    <!-- Meeting Card 1 -->
                    <div class="card">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div>
                                <div class="mb-2">
                                    <span class="font-bold">Status:</span> <span class="italic">Upcoming</span> | 
                                    <span class="font-bold">Date:</span> <span>December 15, 2024</span>
                                </div>
                                <h3 class="mb-2">Quarterly Board Meeting</h3>
                                <p>Review of Q4 performance and strategic planning for next quarter. All board members are required to attend.</p>
                            </div>
                            <a href="#" class="btn-primary">View Details</a>
                        </div>
                    </div>
                    <!-- Meeting Card 2 -->
                    <div class="card">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div>
                                <div class="mb-2">
                                    <span class="font-bold">Status:</span> <span class="italic">Scheduled</span> | 
                                    <span class="font-bold">Date:</span> <span>December 20, 2024</span>
                                </div>
                                <h3 class="mb-2">Annual General Meeting</h3>
                                <p>Annual review and election of board members. This is a mandatory meeting for all members.</p>
                            </div>
                            <a href="#" class="btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section id="vision" class="content-area py-12 md:py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8">
                    <!-- Vision -->
                    <div class="card">
                        <h2 class="mb-4">Our Vision</h2>
                        <p class="leading-relaxed">
                            To create a seamless, transparent, and efficient platform that empowers board members to collaborate effectively and make informed decisions for the betterment of our organization.
                        </p>
                    </div>
                    <!-- Mission -->
                    <div class="card">
                        <h2 class="mb-4">Our Mission</h2>
                        <p class="leading-relaxed">
                            To provide a modern, secure, and user-friendly portal that streamlines board operations, enhances communication, and ensures all members have access to the information they need when they need it.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="content-area py-12 md:py-16" style="background-color: #F9F9F9;">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-center mb-8">About Us</h2>
                    <p class="mb-8 leading-relaxed">
                        The Board Member Portal is a comprehensive digital platform designed to facilitate seamless communication, 
                        collaboration, and management for board members. Our platform integrates modern technology with intuitive design 
                        to create an exceptional user experience while maintaining compliance with government standards and accessibility requirements.
                    </p>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="card text-center">
                            <h3 class="mb-3">Secure</h3>
                            <p>Enterprise-grade security for all your data and communications.</p>
                        </div>
                        <div class="card text-center">
                            <h3 class="mb-3">Efficient</h3>
                            <p>Streamlined processes for faster decision-making and collaboration.</p>
                        </div>
                        <div class="card text-center">
                            <h3 class="mb-3">Accessible</h3>
                            <p>Compliant with WCAG 2.0 accessibility standards for all users.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="content-area py-12 md:py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-center mb-8">Contact Us</h2>
                    <div class="card">
                        <form id="contactForm" class="space-y-6" aria-label="Contact form">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contactName" class="block font-bold mb-2">Name <span class="text-red-600">*</span></label>
                                    <input type="text" id="contactName" name="name" required 
                                           class="w-full px-4 py-2 border border-gray-400 bg-white focus:outline-2 focus:outline-blue-600" 
                                           placeholder="Your name" aria-required="true">
                                </div>
                                <div>
                                    <label for="contactEmail" class="block font-bold mb-2">Email <span class="text-red-600">*</span></label>
                                    <input type="email" id="contactEmail" name="email" required 
                                           class="w-full px-4 py-2 border border-gray-400 bg-white focus:outline-2 focus:outline-blue-600" 
                                           placeholder="your@email.com" aria-required="true">
                                </div>
                            </div>
                            <div>
                                <label for="contactSubject" class="block font-bold mb-2">Subject <span class="text-red-600">*</span></label>
                                <input type="text" id="contactSubject" name="subject" required 
                                       class="w-full px-4 py-2 border border-gray-400 bg-white focus:outline-2 focus:outline-blue-600" 
                                       placeholder="What's this about?" aria-required="true">
                            </div>
                            <div>
                                <label for="contactMessage" class="block font-bold mb-2">Message <span class="text-red-600">*</span></label>
                                <textarea id="contactMessage" name="message" rows="5" required 
                                          class="w-full px-4 py-2 border border-gray-400 bg-white focus:outline-2 focus:outline-blue-600" 
                                          placeholder="Your message..." aria-required="true"></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer-bg py-8" role="contentinfo">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="font-bold mb-4">Board Portal</h3>
                    <p>Modern board management platform</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#announcements" class="text-white hover:underline">Announcements</a></li>
                        <li><a href="#meetings" class="text-white hover:underline">Meetings</a></li>
                        <li><a href="#about" class="text-white hover:underline">About</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Account</h4>
                    <ul class="space-y-2">
                        <li><a href="/login" class="text-white hover:underline">Login</a></li>
                        <li><a href="/register" class="text-white hover:underline">Register</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li>Email: info@boardportal.gov.ph</li>
                        <li>Phone: +63 (2) 1234-5678</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-600 pt-4 text-center">
                <p>&copy; 2024 Board Member Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle with accessibility
        (function() {
            function initApp() {
                if (typeof $ === 'undefined') {
                    setTimeout(initApp, 100);
                    return;
                }

                $(document).ready(function() {
                    // Mobile menu toggle
                    $('#mobileMenuBtn').on('click', function() {
                        const isExpanded = $(this).attr('aria-expanded') === 'true';
                        $('#mobileMenu').toggleClass('hidden');
                        $(this).attr('aria-expanded', !isExpanded);
                    });

                    // Smooth scroll for anchor links
                    $('a[href^="#"]').on('click', function(e) {
                        e.preventDefault();
                        const target = $(this.getAttribute('href'));
                        if (target.length) {
                            $('html, body').animate({
                                scrollTop: target.offset().top - 80
                            }, 600);
                            $('#mobileMenu').addClass('hidden');
                            $('#mobileMenuBtn').attr('aria-expanded', 'false');
                            // Focus management for accessibility
                            target.attr('tabindex', '-1').focus();
                        }
                    });

                    // Contact form submission
                    $('#contactForm').on('submit', function(e) {
                        e.preventDefault();
                        // Form validation and submission logic here
                        alert('Thank you for your message! We will get back to you soon.');
                        this.reset();
                    });

                    // Keyboard navigation support
                    $(document).on('keydown', function(e) {
                        // ESC key closes mobile menu
                        if (e.key === 'Escape' && !$('#mobileMenu').hasClass('hidden')) {
                            $('#mobileMenu').addClass('hidden');
                            $('#mobileMenuBtn').attr('aria-expanded', 'false').focus();
                        }
                    });
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
