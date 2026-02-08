<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=5.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Reset Password - Board Member Portal</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        .password-requirements {
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .password-requirements li {
            padding: 0.25rem 0;
        }
        .password-requirements li.valid {
            color: #10B981;
        }
        .password-requirements li.invalid {
            color: #ef4444;
        }
        .password-input-valid {
            border-color: #10B981 !important;
            background-color: rgba(16, 185, 129, 0.05) !important;
        }
        .password-input-invalid {
            border-color: #ef4444 !important;
            background-color: rgba(239, 68, 68, 0.05) !important;
        }
        .dark .password-input-valid {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }
        .dark .password-input-invalid {
            background-color: rgba(239, 68, 68, 0.1) !important;
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
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Reset Password</h1>
                <p class="text-gray-600 dark:text-gray-400">Enter your new password below.</p>
            </div>

            <form id="resetPasswordForm" class="space-y-4">
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            minlength="6"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Enter new password"
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-colors p-2 z-10"
                            aria-label="Toggle password visibility"
                        >
                            <i class="fas fa-eye-slash text-lg" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                    <span class="text-red-500 text-sm hidden" id="password-error"></span>
                    <div class="password-requirements mt-2">
                        <ul>
                            <li id="req-length" class="invalid">Minimum of 6 alphanumeric characters</li>
                            <li id="req-uppercase" class="invalid">At least 1 capital letter</li>
                            <li id="req-lowercase" class="invalid">At least 1 small letter</li>
                            <li id="req-number" class="invalid">At least 1 number</li>
                            <li id="req-special" class="invalid">At least 1 special character (~, !, #, $, %, ^, &, *, |)</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            minlength="6"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Confirm new password"
                        >
                        <button 
                            type="button" 
                            id="togglePasswordConfirmation" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-colors p-2 z-10"
                            aria-label="Toggle password visibility"
                        >
                            <i class="fas fa-eye-slash text-lg" id="passwordConfirmationEyeIcon"></i>
                        </button>
                    </div>
                    <span class="text-red-500 text-sm hidden" id="password_confirmation-error"></span>
                </div>

                <button
                    type="submit"
                    id="resetBtn"
                    class="w-full text-white py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                    style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                >
                    <span id="resetBtnText">Reset Password</span>
                    <span id="resetBtnLoader" class="hidden">Resetting...</span>
                </button>
            </form>

            <div class="text-center space-y-2">
                <a href="{{ route('login') }}" class="text-sm font-semibold" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            const token = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            // Password validation
            function filterPasswordInput(el) {
                const allowed = /[A-Za-z0-9~!#$%^&*|]/g;
                const val = el.value;
                const filtered = (val.match(allowed) || []).join('');
                if (val !== filtered) el.value = filtered;
            }
            $('#password').on('input', function() {
                filterPasswordInput(this);
                const password = $(this).val();
                const $passwordInput = $(this);
                
                const hasLength = password.length >= 6;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSpecial = /[~!#$%^&*|]/.test(password);
                
                // Update individual requirement indicators
                $('#req-length').removeClass('valid invalid').addClass(hasLength ? 'valid' : 'invalid');
                $('#req-uppercase').removeClass('valid invalid').addClass(hasUppercase ? 'valid' : 'invalid');
                $('#req-lowercase').removeClass('valid invalid').addClass(hasLowercase ? 'valid' : 'invalid');
                $('#req-number').removeClass('valid invalid').addClass(hasNumber ? 'valid' : 'invalid');
                $('#req-special').removeClass('valid invalid').addClass(hasSpecial ? 'valid' : 'invalid');
                
                // Check if all requirements are met
                const allValid = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
                
                // Update password input styling
                if (password.length > 0) {
                    $passwordInput.removeClass('password-input-valid password-input-invalid').addClass(allValid ? 'password-input-valid' : 'password-input-invalid');
                } else {
                    $passwordInput.removeClass('password-input-valid password-input-invalid');
                }
            });
            $('#password_confirmation').on('input', function() { filterPasswordInput(this); });

            // Password toggle functionality
            function setupPasswordToggle(inputId, toggleId, iconId) {
                const passwordInput = document.getElementById(inputId);
                const toggleButton = document.getElementById(toggleId);
                const eyeIcon = document.getElementById(iconId);

                if (toggleButton && passwordInput && eyeIcon) {
                    toggleButton.addEventListener('click', function() {
                        if (passwordInput.type === 'password') {
                            // Show password - change icon to eye (password is now visible)
                            passwordInput.type = 'text';
                            eyeIcon.classList.remove('fa-eye-slash');
                            eyeIcon.classList.add('fa-eye');
                        } else {
                            // Hide password - change icon to eye-slash (password is now hidden)
                            passwordInput.type = 'password';
                            eyeIcon.classList.remove('fa-eye');
                            eyeIcon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }
            
            // Setup password toggles
            setupPasswordToggle('password', 'togglePassword', 'passwordEyeIcon');
            setupPasswordToggle('password_confirmation', 'togglePasswordConfirmation', 'passwordConfirmationEyeIcon');

            function validatePassword(password) {
                return password.length >= 6 &&
                       /[A-Z]/.test(password) &&
                       /[a-z]/.test(password) &&
                       /[0-9]/.test(password) &&
                       /[~!#$%^&*|]/.test(password);
            }

            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();

                const password = $('#password').val();
                const passwordConfirmation = $('#password_confirmation').val();
                const resetBtn = $('#resetBtn');
                const resetBtnText = $('#resetBtnText');
                const resetBtnLoader = $('#resetBtnLoader');

                // Clear previous errors
                $('#password-error, #password_confirmation-error').addClass('hidden').text('');

                // Validate passwords match
                if (password !== passwordConfirmation) {
                    $('#password_confirmation-error').removeClass('hidden').text('Passwords do not match.');
                    return;
                }

            // Validate password requirements
            if (!validatePassword(password)) {
                $('#password-error').removeClass('hidden').text('Password does not meet all requirements.');
                return;
            }

                resetBtn.prop('disabled', true);
                resetBtnText.addClass('hidden');
                resetBtnLoader.removeClass('hidden');

                // Show loading
                Swal.fire({
                    title: 'Resetting Password...',
                    text: 'Please wait while we reset your password.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("password.update") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        token: $('input[name="token"]').val(),
                        email: $('input[name="email"]').val(),
                        password: password,
                        password_confirmation: passwordConfirmation,
                        _token: token
                    },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset!',
                        text: response.message,
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        window.location.href = '{{ route("login") }}';
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.password) {
                            $('#password-error').removeClass('hidden').text(errors.password[0]);
                        }
                        if (errors.password_confirmation) {
                            $('#password_confirmation-error').removeClass('hidden').text(errors.password_confirmation[0]);
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
        });
    </script>

    @include('components.footer')
</body>
</html>

