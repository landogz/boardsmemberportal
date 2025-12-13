<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Register - Board Member Portal</title>
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
    <style>
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #A855F7 0%, #3B82F6 50%, #10B981 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }
        .step-indicator .step-item {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .step-indicator .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #9ca3af;
            transition: all 0.3s;
        }
        .step-indicator .step-item.active .step-number {
            background: linear-gradient(135deg, #A855F7, #3B82F6);
            border-color: #A855F7;
            color: white;
        }
        .step-indicator .step-item.completed .step-number {
            background: #10B981;
            border-color: #10B981;
            color: white;
        }
        .step-indicator .step-label {
            margin-top: 8px;
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }
        .step-indicator .step-item.active .step-label {
            color: #A855F7;
            font-weight: 600;
        }
    </style>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <div class="min-h-[50vh] flex items-center justify-center p-4 py-16">
        <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Create Account</h1>
                <p class="text-gray-600 dark:text-gray-400">Register as a board member</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Contact & Company</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Additional Info</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Security</div>
                </div>
            </div>

            <form id="registerForm" class="space-y-4">
                <!-- Step 1: Personal Information -->
                <div class="step active" id="step1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Personal Information</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="First name"
                                >
                                <span class="text-red-500 text-sm hidden" id="first_name-error"></span>
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name *</label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Last name"
                                >
                                <span class="text-red-500 text-sm hidden" id="last_name-error"></span>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Enter your email"
                            >
                            <span class="text-red-500 text-sm hidden" id="email-error"></span>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Contact & Company -->
                <div class="step" id="step2">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Contact & Company Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mobile</label>
                            <input 
                                type="text" 
                                id="mobile" 
                                name="mobile"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Mobile number"
                            >
                            <span class="text-red-500 text-sm hidden" id="mobile-error"></span>
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                            <input 
                                type="text" 
                                id="company" 
                                name="company"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Company name"
                            >
                            <span class="text-red-500 text-sm hidden" id="company-error"></span>
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                            <input 
                                type="text" 
                                id="position" 
                                name="position"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Your position/title"
                            >
                            <span class="text-red-500 text-sm hidden" id="position-error"></span>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Additional Information -->
                <div class="step" id="step3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Additional Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="representative_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Representative Name</label>
                            <input 
                                type="text" 
                                id="representative_name" 
                                name="representative_name"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Representative name"
                            >
                            <span class="text-red-500 text-sm hidden" id="representative_name-error"></span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">You can skip this step if it doesn't apply to you.</p>
                    </div>
                </div>

                <!-- Step 4: Security -->
                <div class="step" id="step4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Create Password</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Enter password (min 8 characters)"
                            >
                            <span class="text-red-500 text-sm hidden" id="password-error"></span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Password must be at least 8 characters long</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password *</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Confirm password"
                            >
                            <span class="text-red-500 text-sm hidden" id="password_confirmation-error"></span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        type="button" 
                        id="prevBtn"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition hidden"
                    >
                        Previous
                    </button>
                    <div class="ml-auto">
                        <button 
                            type="button" 
                            id="nextBtn"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg"
                        >
                            Next
                        </button>
                        <button 
                            type="submit" 
                            id="registerBtn"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg hidden"
                        >
                            <span id="registerBtnText">Create Account</span>
                            <span id="registerBtnLoader" class="hidden">Loading...</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold">Login here</a>
                </p>
                <a href="{{ route('landing') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mt-2 inline-block">Back to Home</a>
            </div>
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        let currentStep = 1;
        const totalSteps = 4;

        // Step validation functions
        function validateStep(step) {
            let isValid = true;
            
            if (step === 1) {
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const email = document.getElementById('email').value.trim();
                
                if (!firstName) {
                    showError('first_name', 'First name is required');
                    isValid = false;
                }
                if (!lastName) {
                    showError('last_name', 'Last name is required');
                    isValid = false;
                }
                if (!email) {
                    showError('email', 'Email is required');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Please enter a valid email address');
                    isValid = false;
                }
            } else if (step === 4) {
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;
                
                if (!password) {
                    showError('password', 'Password is required');
                    isValid = false;
                } else if (password.length < 8) {
                    showError('password', 'Password must be at least 8 characters');
                    isValid = false;
                }
                
                if (!passwordConfirmation) {
                    showError('password_confirmation', 'Please confirm your password');
                    isValid = false;
                } else if (password !== passwordConfirmation) {
                    showError('password_confirmation', 'Passwords do not match');
                    isValid = false;
                }
            }
            
            return isValid;
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + '-error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
            }
        }

        function clearErrors() {
            document.querySelectorAll('.text-red-500').forEach(el => {
                el.classList.add('hidden');
            });
        }

        function updateStepIndicator() {
            document.querySelectorAll('.step-item').forEach((item, index) => {
                const stepNum = index + 1;
                item.classList.remove('active', 'completed');
                
                if (stepNum < currentStep) {
                    item.classList.add('completed');
                } else if (stepNum === currentStep) {
                    item.classList.add('active');
                }
            });
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step').forEach(s => {
                s.classList.remove('active');
            });
            
            // Show current step
            document.getElementById('step' + step).classList.add('active');
            
            // Update buttons
            document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
            document.getElementById('nextBtn').classList.toggle('hidden', step === totalSteps);
            document.getElementById('registerBtn').classList.toggle('hidden', step !== totalSteps);
            
            updateStepIndicator();
        }

        // Next button
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateStep(currentStep)) {
                clearErrors();
                currentStep++;
                showStep(currentStep);
            }
        });

        // Previous button
        document.getElementById('prevBtn').addEventListener('click', function() {
            clearErrors();
            currentStep--;
            showStep(currentStep);
        });

        // Form submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate final step
            if (!validateStep(4)) {
                return;
            }
            
            const formData = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                mobile: document.getElementById('mobile').value,
                company: document.getElementById('company').value,
                position: document.getElementById('position').value,
                representative_name: document.getElementById('representative_name').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            const registerBtn = document.getElementById('registerBtn');
            const registerBtnText = document.getElementById('registerBtnText');
            const registerBtnLoader = document.getElementById('registerBtnLoader');

            // Clear previous errors
            clearErrors();

            // Disable button
            registerBtn.disabled = true;
            registerBtnText.classList.add('hidden');
            registerBtnLoader.classList.remove('hidden');

            try {
                const response = await axios.post('/register', formData);

                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: response.data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '/login';
                    });
                }
            } catch (error) {
                registerBtn.disabled = false;
                registerBtnText.classList.remove('hidden');
                registerBtnLoader.classList.add('hidden');

                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    
                    // Find which step has errors and navigate to it
                    let errorStep = 1;
                    Object.keys(errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        if (errorElement) {
                            errorElement.textContent = errors[field][0];
                            errorElement.classList.remove('hidden');
                            
                            // Determine which step this field belongs to
                            if (['first_name', 'last_name', 'email'].includes(field)) {
                                errorStep = 1;
                            } else if (['mobile', 'company', 'position'].includes(field)) {
                                errorStep = 2;
                            } else if (['representative_name'].includes(field)) {
                                errorStep = 3;
                            } else if (['password', 'password_confirmation'].includes(field)) {
                                errorStep = 4;
                            }
                        }
                    });
                    
                    // Navigate to step with error
                    currentStep = errorStep;
                    showStep(currentStep);

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.',
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
