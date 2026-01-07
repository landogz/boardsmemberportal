<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Login - Board Member Portal</title>
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
        
        /* Titles/Headlines - Montserrat Bold (or Gotham Bold fallback), bumped sizes for clarity */
        h1, .title, .headline {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 700; /* Bold */
            font-size: 34px; /* Larger headline */
            line-height: 1.3; /* Slightly taller for readability */
        }
        
        /* Headers/Subheaders - Montserrat Semi-Bold, bumped sizes for clarity */
        h2, h3, h4, h5, h6, .header, .subheader {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600; /* Semi-Bold */
            font-size: 24px; /* Larger default for headers/subheaders */
            line-height: 1.3;
        }
        
        /* Specific heading sizes */
        h2 {
            font-size: 28px;
        }
        
        h3 {
            font-size: 24px;
        }
        
        h4 {
            font-size: 22px;
        }
        
        h5, h6 {
            font-size: 20px;
        }
        
        /* Small text adjustments */
        small, .text-sm, .text-xs {
            font-size: 12px;
            line-height: 1.5;
        }
        
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
    </style>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <div class="min-h-[50vh] flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl p-8 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
            <p class="text-gray-600">Sign in to your account</p>
        </div>

        <form id="loginForm" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email or Username</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    placeholder="Enter your email or username"
                >
                <span class="text-red-500 text-sm hidden" id="email-error"></span>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    placeholder="Enter your password"
                >
                <span class="text-red-500 text-sm hidden" id="password-error"></span>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#055498] accent-[#055498]">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">Forgot password?</a>
            </div>

            <button 
                type="submit" 
                id="loginBtn"
                class="w-full text-white py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
            >
                <span id="loginBtnText">Sign In</span>
                <span id="loginBtnLoader" class="hidden">Loading...</span>
            </button>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">Register here</a>
            </p>
            <a href="{{ route('landing') }}" class="text-sm text-gray-500 hover:text-gray-700 mt-2 inline-block">Back to Home</a>
        </div>
    </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginBtnLoader = document.getElementById('loginBtnLoader');

            // Clear previous errors
            document.getElementById('email-error').classList.add('hidden');
            document.getElementById('password-error').classList.add('hidden');

            // Disable button
            loginBtn.disabled = true;
            loginBtnText.classList.add('hidden');
            loginBtnLoader.classList.remove('hidden');

            try {
                // Get redirect parameter from URL if present
                const urlParams = new URLSearchParams(window.location.search);
                const redirectParam = urlParams.get('redirect');
                
                const response = await axios.post('/login' + (redirectParam ? '?redirect=' + encodeURIComponent(redirectParam) : ''), {
                    email: email,
                    password: password,
                    remember: remember
                });

                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.data.redirect;
                    });
                }
            } catch (error) {
                loginBtn.disabled = false;
                loginBtnText.classList.remove('hidden');
                loginBtnLoader.classList.add('hidden');

                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    
                    if (errors.email) {
                        document.getElementById('email-error').textContent = errors.email[0];
                        document.getElementById('email-error').classList.remove('hidden');
                    }
                    if (errors.password) {
                        document.getElementById('password-error').textContent = errors.password[0];
                        document.getElementById('password-error').classList.remove('hidden');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: errors.email ? errors.email[0] : 'Please check your credentials',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                }
            }
        });

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
</body>
</html>

