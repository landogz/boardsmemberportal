<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=5.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Forgot Password - Board Member Portal</title>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        body, p, span, div, li, td, th, label, input, textarea, select, button {
            font-family: 'Gotham Rounded', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        h1, .title, .headline {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 700;
            font-size: 34px;
            line-height: 1.3;
        }
        h2, h3, h4, h5, h6, .header, .subheader {
            font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600;
            font-size: 24px;
            line-height: 1.3;
        }
        h2 { font-size: 28px; }
        h3 { font-size: 24px; }
        h4 { font-size: 22px; }
        h5, h6 { font-size: 20px; }
        small, .text-sm, .text-xs { font-size: 12px; line-height: 1.5; }
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

    <div class="min-h-[50vh] flex items-center justify-center p-4 py-16">
        <div class="max-w-md w-full bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Forgot Password</h1>
                <p class="text-gray-600">Enter your email or username to request a reset.</p>
            </div>

            <form id="forgotPasswordForm" class="space-y-4">
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

                <button
                    type="submit"
                    id="resetBtn"
                    class="w-full text-white py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                    style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                >
                    <span id="resetBtnText">Send Reset Link</span>
                    <span id="resetBtnLoader" class="hidden">Sending...</span>
                </button>
            </form>

            <div class="text-center space-y-2">
                <a href="{{ route('login') }}" class="text-sm font-semibold" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                    Back to Login
                </a>
                <div>
                    <a href="{{ route('landing') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-block">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#forgotPasswordForm').on('submit', function(e) {
            e.preventDefault();

            const login = $('#email').val();
            const resetBtn = $('#resetBtn');
            const resetBtnText = $('#resetBtnText');
            const resetBtnLoader = $('#resetBtnLoader');

            $('#email-error').addClass('hidden').text('');
            resetBtn.prop('disabled', true);
            resetBtnText.addClass('hidden');
            resetBtnLoader.removeClass('hidden');

            $.ajax({
                url: '{{ route("password.email") }}',
                method: 'POST',
                data: { email: login },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Request Sent',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            $('#email-error').removeClass('hidden').text(errors.email[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred. Please try again.'
                        });
                    }
                },
                complete: function() {
                    resetBtn.prop('disabled', false);
                    resetBtnText.removeClass('hidden');
                    resetBtnLoader.addClass('hidden');
                }
            });
        });
    </script>

    @include('components.footer')
</body>
</html>

