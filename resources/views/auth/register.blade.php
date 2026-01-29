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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
            background: linear-gradient(135deg, #055498, #123a60);
            border-color: #055498;
            color: white;
        }
        .step-indicator .step-item.completed .step-number {
            background: #055498;
            border-color: #055498;
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
        
        /* Fix Safari select height mismatch with inputs */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            box-sizing: border-box;
            height: auto;
            min-height: 48px; /* Match input height (py-3 = 12px top + 12px bottom = 24px padding + 1px border top + 1px border bottom + ~22px content = ~48px) */
        }
        
        /* Ensure inputs and selects have same height calculation */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"],
        textarea,
        select {
            box-sizing: border-box;
            line-height: 1.5;
        }
        
        /* Safari specific fix for select dropdown arrow */
        select::-webkit-inner-spin-button,
        select::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Custom dropdown arrow for Safari */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23374151' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 40px !important;
        }
        
        .dark select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%9ca3af' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        }
    </style>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <div class="min-h-[50vh] flex items-center justify-center p-4">
        <div class="max-w-4xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Create Account</h1>
                <p class="text-gray-600 dark:text-gray-400">Register as a Board Member / Authorized Representative</p>
            </div>

            <!-- Pending Approval Notice -->
            <div class="flex items-start gap-3 p-4 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"></i>
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <p class="font-medium mb-1">Registration requires CONSEC approval</p>
                    <p class="text-blue-700 dark:text-blue-300">Your registration will be reviewed by CONSEC. You will receive an email once your account has been approved. You can then sign in with your credentials.</p>
                </div>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Agency & Personal</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Office Address</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Contact Info</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Account Security</div>
                </div>
            </div>

            <form id="registerForm" class="space-y-4">
                <!-- Step 1: Government Agency & Personal Information -->
                <div class="step active" id="step1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Government Agency & Personal Information</h2>
                    <div class="space-y-4">
                        <!-- Government Agency -->
                        <div>
                            <label for="government_agency_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Government Agency *</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <select 
                                        id="government_agency_id" 
                                        name="government_agency_id" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    >
                                        <option value="">Loading agencies...</option>
                                    </select>
                                    <span class="text-red-500 text-sm hidden" id="government_agency_id-error"></span>
                                </div>
                                <div id="agencyLogoPreview" class="hidden">
                                    <img id="agencyLogoImg" src="" alt="Agency Logo" class="h-16 w-16 object-contain border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 p-2">
                                </div>
                            </div>
                        </div>

                        <!-- Representative Type -->
                        <div>
                            <label for="representative_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Representative Type *</label>
                            <select 
                                id="representative_type" 
                                name="representative_type" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                                <option value="">Select Type</option>
                                <option value="Board Member">Board Member</option>
                                <option value="Authorized Representative">Authorized Representative</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="representative_type-error"></span>
                        </div>

                        <!-- Name Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="pre_nominal_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pre Nominal Title *</label>
                                <select 
                                    id="pre_nominal_title" 
                                    name="pre_nominal_title" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                >
                                    <option value="">Select Title</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                </select>
                                <span class="text-red-500 text-sm hidden" id="pre_nominal_title-error"></span>
                            </div>

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
                                <label for="middle_initial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Initial</label>
                                <input 
                                    type="text" 
                                    id="middle_initial" 
                                    name="middle_initial" 
                                    maxlength="10"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="M.I."
                                >
                                <span class="text-red-500 text-sm hidden" id="middle_initial-error"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                            <div>
                                <label for="post_nominal_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Post Nominal Title</label>
                                <select 
                                    id="post_nominal_title" 
                                    name="post_nominal_title" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                >
                                    <option value="">Select Title</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="Others">Others</option>
                                </select>
                                <div id="post_nominal_title_custom_wrapper" class="mt-2 hidden">
                                    <label for="post_nominal_title_custom" class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Others:</label>
                                    <input 
                                        type="text" 
                                        id="post_nominal_title_custom" 
                                        name="post_nominal_title_custom" 
                                        placeholder="Specify other title"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    >
                                </div>
                                <span class="text-red-500 text-sm hidden" id="post_nominal_title-error"></span>
                            </div>
                        </div>

                        <!-- Designation -->
                        <div>
                            <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Designation *</label>
                            <input 
                                type="text" 
                                id="designation" 
                                name="designation" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Your designation"
                            >
                            <span class="text-red-500 text-sm hidden" id="designation-error"></span>
                        </div>

                        <!-- Sex and Gender -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sex *</label>
                                <select 
                                    id="sex" 
                                    name="sex" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                >
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <span class="text-red-500 text-sm hidden" id="sex-error"></span>
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender *</label>
                                <select 
                                    id="gender" 
                                    name="gender" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                >
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Non-Binary">Non-Binary</option>
                                </select>
                                <span class="text-red-500 text-sm hidden" id="gender-error"></span>
                            </div>
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Date *</label>
                            <input 
                                type="date" 
                                id="birth_date" 
                                name="birth_date" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                            <span class="text-red-500 text-sm hidden" id="birth_date-error"></span>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Office Address (PSGC) -->
                <div class="step" id="step2">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Complete Office Address (PSGC)</h2>
                    <div class="space-y-4">
                        <!-- Building/House/Street Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="office_building_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Building No.</label>
                                <input 
                                    type="text" 
                                    id="office_building_no" 
                                    name="office_building_no" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Building No."
                                >
                            </div>

                            <div>
                                <label for="office_house_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">House No.</label>
                                <input 
                                    type="text" 
                                    id="office_house_no" 
                                    name="office_house_no" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="House No."
                                >
                            </div>
                        </div>

                        <div>
                            <label for="office_street_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street Name</label>
                            <input 
                                type="text" 
                                id="office_street_name" 
                                name="office_street_name" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Street Name"
                            >
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="office_purok" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purok</label>
                                <input 
                                    type="text" 
                                    id="office_purok" 
                                    name="office_purok" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Purok"
                                >
                            </div>

                            <div>
                                <label for="office_sitio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sitio</label>
                                <input 
                                    type="text" 
                                    id="office_sitio" 
                                    name="office_sitio" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Sitio"
                                >
                            </div>
                        </div>

                        <!-- PSGC Dropdowns -->
                        <div>
                            <label for="office_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Region *</label>
                            <select 
                                id="office_region" 
                                name="office_region" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                                <option value="">Select Region</option>
                                <!-- Regions will be populated via JavaScript -->
                            </select>
                            <span class="text-red-500 text-sm hidden" id="office_region-error"></span>
                        </div>

                        <div>
                            <label for="office_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province *</label>
                            <select 
                                id="office_province" 
                                name="office_province" 
                                required
                                disabled
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                                <option value="">Select Province</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="office_province-error"></span>
                        </div>

                        <div>
                            <label for="office_city_municipality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City/Municipality *</label>
                            <select 
                                id="office_city_municipality" 
                                name="office_city_municipality" 
                                required
                                disabled
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                                <option value="">Select City/Municipality</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="office_city_municipality-error"></span>
                        </div>

                        <div>
                            <label for="office_barangay" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barangay *</label>
                            <select 
                                id="office_barangay" 
                                name="office_barangay" 
                                required
                                disabled
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            >
                                <option value="">Select Barangay</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="office_barangay-error"></span>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Contact Information -->
                <div class="step" id="step3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="Enter your email"
                            >
                            <span class="text-red-500 text-sm hidden" id="email-error"></span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Single device login for authorized users. 30 minutes idle time.</p>
                        </div>

                        <!-- Username (System Generated) -->
                        <div style="display: none;">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username *</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                required
                                readonly
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition bg-gray-100 dark:bg-gray-600"
                                placeholder="System generated username"
                            >
                            <span class="text-red-500 text-sm hidden" id="username-error"></span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">System generated. Can be edited once registration has been approved.</p>
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mobile Number *</label>
                            <input 
                                type="text" 
                                id="mobile" 
                                name="mobile" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="+63 912 345 6789"
                            >
                            <span class="text-red-500 text-sm hidden" id="mobile-error"></span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: +63</p>
                        </div>

                        <!-- Landline -->
                        <div>
                            <label for="landline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Landline / Office Number</label>
                            <input 
                                type="text" 
                                id="landline" 
                                name="landline" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                placeholder="(02) 8912-12345"
                            >
                            <span class="text-red-500 text-sm hidden" id="landline-error"></span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: (02) 8912-12345 (Optional)</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Account Security -->
                <div class="step" id="step4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Account Security</h2>
                    <div class="space-y-4">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Enter password"
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
                                    <li id="req-special" class="invalid">At least 1 special character (~, !, #, $, %, ^, &, *, |, etc.)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password *</label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    placeholder="Confirm password"
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
                            class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                            style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                        >
                            Next
                        </button>
                        <button 
                            type="submit" 
                            id="registerBtn"
                            class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hidden"
                            style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                        >
                            <span id="registerBtnText">Submit Registration</span>
                            <span id="registerBtnLoader" class="hidden">Loading...</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-semibold" style="color: #055498;" onmouseover="this.style.color='#123a60'" onmouseout="this.style.color='#055498'">Login here</a>
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

        // Store PSGC data
        let regionsData = [];
        let provincesData = [];
        let citiesData = [];
        let barangaysData = [];

        // Populate Regions and Government Agencies
        // Store agencies data with logos
        let agenciesDataMap = {};

        $(document).ready(function() {
            // Load Government Agencies from API
            $.ajax({
                url: '/api/government-agencies',
                method: 'GET',
                success: function(agencies) {
                    const agencySelect = $('#government_agency_id');
                    agencySelect.html('<option value="">Select Agency</option>');
                    agencies.forEach(agency => {
                        // Store logo URL in map
                        agenciesDataMap[agency.id] = agency.logo_url || null;
                        agencySelect.append(`<option value="${agency.id}">${agency.name}${agency.code ? ' (' + agency.code + ')' : ''}</option>`);
                    });
                },
                error: function() {
                    $('#government_agency_id').html('<option value="">Error loading agencies</option>');
                }
            });

            // Show agency logo when agency is selected
            $('#government_agency_id').on('change', function() {
                const selectedAgencyId = $(this).val();
                const logoUrl = agenciesDataMap[selectedAgencyId];
                const logoPreview = $('#agencyLogoPreview');
                const logoImg = $('#agencyLogoImg');
                
                if (logoUrl && selectedAgencyId) {
                    logoImg.attr('src', logoUrl);
                    logoImg.on('error', function() {
                        $(this).attr('src', '');
                        logoPreview.addClass('hidden');
                    });
                    logoPreview.removeClass('hidden');
                } else {
                    logoPreview.addClass('hidden');
                }
            });

            // Load PSGC data from API
            axios.get('/api/address/regions')
                .then(function(response) {
                    regionsData = response.data;
                    const regionSelect = $('#office_region');
                    regionSelect.html('<option value="">Select Region</option>');
                    response.data.forEach(region => {
                        regionSelect.append(`<option value="${region.region_code}" data-id="${region.id}">${region.region_name}</option>`);
                    });
                })
                .catch(function(error) {
                    console.error('Failed to load regions:', error);
                    $('#office_region').html('<option value="">Error loading regions</option>');
                });

            // Provinces, cities, and barangays are now loaded on-demand via API when dropdowns change

            // Handle post nominal title "Others" option
            $('#post_nominal_title').on('change', function() {
                if ($(this).val() === 'Others') {
                    $('#post_nominal_title_custom_wrapper').removeClass('hidden');
                    $('#post_nominal_title_custom').prop('required', true);
                } else {
                    $('#post_nominal_title_custom_wrapper').addClass('hidden');
                    $('#post_nominal_title_custom').prop('required', false).val('');
                }
            });

            // Generate username based on name and email
            function generateUsername() {
                const firstName = $('#first_name').val().trim();
                const lastName = $('#last_name').val().trim();
                const email = $('#email').val().trim();
                
                if (firstName && lastName && email) {
                    const firstInitial = firstName.charAt(0).toLowerCase();
                    const lastPart = lastName.toLowerCase().substring(0, 5);
                    const emailPart = email.split('@')[0].substring(0, 3).toLowerCase();
                    const randomNum = Math.floor(Math.random() * 1000);
                    const username = `${firstInitial}${lastPart}${emailPart}${randomNum}`.substring(0, 20);
                    $('#username').val(username);
                }
            }

            // Auto-generate username when name or email changes
            $('#first_name, #last_name, #email').on('blur', function() {
                if ($('#first_name').val() && $('#last_name').val() && $('#email').val()) {
                    generateUsername();
                }
            });

            // Phone number formatting
            $('#mobile').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.startsWith('63')) {
                    value = '+' + value;
                } else if (value.startsWith('0')) {
                    value = '+63' + value.substring(1);
                } else if (value && !value.startsWith('+63')) {
                    value = '+63' + value;
                }
                $(this).val(value);
            });

            $('#landline').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.startsWith('02')) {
                        value = '(02) ' + value.substring(2, 6) + '-' + value.substring(6);
                    } else if (value.length >= 7) {
                        value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 6) + '-' + value.substring(6);
                    }
                }
                $(this).val(value);
            });

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#passwordEyeIcon');
                
                if (passwordInput.attr('type') === 'password') {
                    // Show password - change icon to eye (password is now visible)
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    // Hide password - change icon to eye-slash (password is now hidden)
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });

            $('#togglePasswordConfirmation').on('click', function() {
                const passwordInput = $('#password_confirmation');
                const eyeIcon = $('#passwordConfirmationEyeIcon');
                
                if (passwordInput.attr('type') === 'password') {
                    // Show password - change icon to eye (password is now visible)
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    // Hide password - change icon to eye-slash (password is now hidden)
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });

            // Password validation
            $('#password').on('input', function() {
                const password = $(this).val();
                const $passwordInput = $(this);
                
                // Check individual requirements
                const hasLength = password.length >= 6;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSpecial = /[~!@#$%^&*|]/.test(password);
                
                // Update requirement indicators
                if (hasLength) {
                    $('#req-length').removeClass('invalid').addClass('valid');
                } else {
                    $('#req-length').removeClass('valid').addClass('invalid');
                }
                
                if (hasUppercase) {
                    $('#req-uppercase').removeClass('invalid').addClass('valid');
                } else {
                    $('#req-uppercase').removeClass('valid').addClass('invalid');
                }
                
                if (hasLowercase) {
                    $('#req-lowercase').removeClass('invalid').addClass('valid');
                } else {
                    $('#req-lowercase').removeClass('valid').addClass('invalid');
                }
                
                if (hasNumber) {
                    $('#req-number').removeClass('invalid').addClass('valid');
                } else {
                    $('#req-number').removeClass('valid').addClass('invalid');
                }
                
                if (hasSpecial) {
                    $('#req-special').removeClass('invalid').addClass('valid');
                } else {
                    $('#req-special').removeClass('valid').addClass('invalid');
                }
                
                // Check if all requirements are met
                const allValid = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
                
                // Update password input styling
                if (password.length > 0) {
                    if (allValid) {
                        $passwordInput.removeClass('password-input-invalid').addClass('password-input-valid');
                    } else {
                        $passwordInput.removeClass('password-input-valid').addClass('password-input-invalid');
                    }
                } else {
                    $passwordInput.removeClass('password-input-valid password-input-invalid');
                }
            });

            // PSGC Cascading Dropdowns using API
            $('#office_region').on('change', function() {
                const regionCode = $(this).val();
                const provinceSelect = $('#office_province');
                const citySelect = $('#office_city_municipality');
                const barangaySelect = $('#office_barangay');
                
                if (regionCode) {
                    // Fetch provinces by region_code from API
                    provinceSelect.prop('disabled', true).html('<option value="">Loading...</option>');
                    axios.get('/api/address/provinces', { params: { region_code: regionCode } })
                        .then(function(response) {
                            provinceSelect.prop('disabled', false).html('<option value="">Select Province</option>');
                            response.data.forEach(province => {
                                provinceSelect.append(`<option value="${province.province_code}">${province.province_name}</option>`);
                            });
                        })
                        .catch(function(error) {
                            console.error('Failed to load provinces:', error);
                            provinceSelect.html('<option value="">Error loading provinces</option>');
                        });
                    
                    // Reset city and barangay
                    citySelect.prop('disabled', true).html('<option value="">Select City/Municipality</option>');
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                } else {
                    provinceSelect.prop('disabled', true).html('<option value="">Select Province</option>');
                    citySelect.prop('disabled', true).html('<option value="">Select City/Municipality</option>');
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                }
            });

            $('#office_province').on('change', function() {
                const provinceCode = $(this).val();
                const citySelect = $('#office_city_municipality');
                const barangaySelect = $('#office_barangay');
                
                if (provinceCode) {
                    // Fetch cities by province_code from API
                    citySelect.prop('disabled', true).html('<option value="">Loading...</option>');
                    axios.get('/api/address/cities', { params: { province_code: provinceCode } })
                        .then(function(response) {
                            citySelect.prop('disabled', false).html('<option value="">Select City/Municipality</option>');
                            response.data.forEach(city => {
                                citySelect.append(`<option value="${city.city_code}">${city.city_name}</option>`);
                            });
                        })
                        .catch(function(error) {
                            console.error('Failed to load cities:', error);
                            citySelect.html('<option value="">Error loading cities</option>');
                        });
                    
                    // Reset barangay
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                } else {
                    citySelect.prop('disabled', true).html('<option value="">Select City/Municipality</option>');
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                }
            });

            $('#office_city_municipality').on('change', function() {
                const cityCode = $(this).val();
                const barangaySelect = $('#office_barangay');
                
                if (cityCode) {
                    // Fetch barangays by city_code from API
                    barangaySelect.prop('disabled', true).html('<option value="">Loading...</option>');
                    axios.get('/api/address/barangays', { params: { city_code: cityCode } })
                        .then(function(response) {
                            barangaySelect.prop('disabled', false).html('<option value="">Select Barangay</option>');
                            response.data.forEach(barangay => {
                                barangaySelect.append(`<option value="${barangay.brgy_code}">${barangay.brgy_name}</option>`);
                            });
                        })
                        .catch(function(error) {
                            console.error('Failed to load barangays:', error);
                            barangaySelect.html('<option value="">Error loading barangays</option>');
                        });
                } else {
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                }
            });
        });

        // Step validation functions
        function validateStep(step) {
            let isValid = true;
            let firstInvalidField = null;
            
            if (step === 1) {
                const governmentAgencyId = $('#government_agency_id').val();
                const representativeType = $('#representative_type').val();
                const preNominalTitle = $('#pre_nominal_title').val();
                const firstName = $('#first_name').val().trim();
                const lastName = $('#last_name').val().trim();
                const designation = $('#designation').val().trim();
                const sex = $('#sex').val();
                const gender = $('#gender').val();
                const birthDate = $('#birth_date').val();
                
                if (!governmentAgencyId) {
                    showError('government_agency_id', 'Government agency is required');
                    if (!firstInvalidField) firstInvalidField = '#government_agency_id';
                    isValid = false;
                }
                if (!representativeType) {
                    showError('representative_type', 'Representative type is required');
                    if (!firstInvalidField) firstInvalidField = '#representative_type';
                    isValid = false;
                }
                if (!preNominalTitle) {
                    showError('pre_nominal_title', 'Pre nominal title is required');
                    if (!firstInvalidField) firstInvalidField = '#pre_nominal_title';
                    isValid = false;
                }
                if (!firstName) {
                    showError('first_name', 'First name is required');
                    if (!firstInvalidField) firstInvalidField = '#first_name';
                    isValid = false;
                }
                if (!lastName) {
                    showError('last_name', 'Last name is required');
                    if (!firstInvalidField) firstInvalidField = '#last_name';
                    isValid = false;
                }
                if (!designation) {
                    showError('designation', 'Designation is required');
                    if (!firstInvalidField) firstInvalidField = '#designation';
                    isValid = false;
                }
                if (!sex) {
                    showError('sex', 'Sex is required');
                    if (!firstInvalidField) firstInvalidField = '#sex';
                    isValid = false;
                }
                if (!gender) {
                    showError('gender', 'Gender is required');
                    if (!firstInvalidField) firstInvalidField = '#gender';
                    isValid = false;
                }
                if (!birthDate) {
                    showError('birth_date', 'Birth date is required');
                    if (!firstInvalidField) firstInvalidField = '#birth_date';
                    isValid = false;
                }
            } else if (step === 2) {
                const region = $('#office_region').val();
                const province = $('#office_province').val();
                const city = $('#office_city_municipality').val();
                const barangay = $('#office_barangay').val();
                
                if (!region) {
                    showError('office_region', 'Region is required');
                    if (!firstInvalidField) firstInvalidField = '#office_region';
                    isValid = false;
                }
                if (!province) {
                    showError('office_province', 'Province is required');
                    if (!firstInvalidField) firstInvalidField = '#office_province';
                    isValid = false;
                }
                if (!city) {
                    showError('office_city_municipality', 'City/Municipality is required');
                    if (!firstInvalidField) firstInvalidField = '#office_city_municipality';
                    isValid = false;
                }
                if (!barangay) {
                    showError('office_barangay', 'Barangay is required');
                    if (!firstInvalidField) firstInvalidField = '#office_barangay';
                    isValid = false;
                }
            } else if (step === 3) {
                const email = $('#email').val().trim();
                const username = $('#username').val().trim();
                const mobile = $('#mobile').val().trim();
                
                if (!email) {
                    showError('email', 'Email is required');
                    if (!firstInvalidField) firstInvalidField = '#email';
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Please enter a valid email address');
                    if (!firstInvalidField) firstInvalidField = '#email';
                    isValid = false;
                }
                if (!username) {
                    showError('username', 'Username is required');
                    if (!firstInvalidField) firstInvalidField = '#username';
                    isValid = false;
                }
                if (!mobile) {
                    showError('mobile', 'Mobile number is required');
                    if (!firstInvalidField) firstInvalidField = '#mobile';
                    isValid = false;
                } else if (!mobile.startsWith('+63')) {
                    showError('mobile', 'Mobile number must start with +63');
                    if (!firstInvalidField) firstInvalidField = '#mobile';
                    isValid = false;
                }
            } else if (step === 4) {
                const password = $('#password').val();
                const passwordConfirmation = $('#password_confirmation').val();
                
                if (!password) {
                    showError('password', 'Password is required');
                    if (!firstInvalidField) firstInvalidField = '#password';
                    isValid = false;
                } else if (!validatePassword(password)) {
                    showError('password', 'Password does not meet requirements');
                    if (!firstInvalidField) firstInvalidField = '#password';
                    isValid = false;
                }
                
                if (!passwordConfirmation) {
                    showError('password_confirmation', 'Please confirm your password');
                    if (!firstInvalidField) firstInvalidField = '#password_confirmation';
                    isValid = false;
                } else if (password !== passwordConfirmation) {
                    showError('password_confirmation', 'Passwords do not match');
                    if (!firstInvalidField) firstInvalidField = '#password_confirmation';
                    isValid = false;
                }
            }
            
            // Focus on first invalid field
            if (!isValid && firstInvalidField) {
                setTimeout(() => {
                    $(firstInvalidField).focus();
                    $('html, body').animate({
                        scrollTop: $(firstInvalidField).offset().top - 100
                    }, 500);
                }, 100);
            }
            
            return isValid;
        }

        function validatePassword(password) {
            return password.length >= 6 &&
                   /[A-Z]/.test(password) &&
                   /[a-z]/.test(password) &&
                   /[0-9]/.test(password) &&
                   /[~!@#$%^&*|]/.test(password);
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function showError(fieldId, message) {
            const errorElement = $(`#${fieldId}-error`);
            if (errorElement.length) {
                errorElement.text(message);
                errorElement.removeClass('hidden');
            }
        }

        function clearErrors() {
            $('.text-red-500').addClass('hidden');
        }

        function updateStepIndicator() {
            $('.step-item').each(function(index) {
                const stepNum = index + 1;
                $(this).removeClass('active completed');
                
                if (stepNum < currentStep) {
                    $(this).addClass('completed');
                } else if (stepNum === currentStep) {
                    $(this).addClass('active');
                }
            });
        }

        function showStep(step) {
            // Hide all steps
            $('.step').removeClass('active');
            
            // Show current step
            $(`#step${step}`).addClass('active');
            
            // Update buttons
            $('#prevBtn').toggleClass('hidden', step === 1);
            $('#nextBtn').toggleClass('hidden', step === totalSteps);
            $('#registerBtn').toggleClass('hidden', step !== totalSteps);
            
            updateStepIndicator();
        }

        // Next button
        $('#nextBtn').on('click', function() {
            if (validateStep(currentStep)) {
                clearErrors();
                currentStep++;
                showStep(currentStep);
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Scroll to top to see error messages
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Previous button
        $('#prevBtn').on('click', function() {
            clearErrors();
            currentStep--;
            showStep(currentStep);
        });

        // Form submission
        $('#registerForm').on('submit', async function(e) {
            e.preventDefault();
            
            // Validate final step
            if (!validateStep(4)) {
                return;
            }
            
            // Show confirmation dialog
            const result = await Swal.fire({
                title: 'Registration Review',
                text: 'Please review your information carefully. Click “Confirm” to submit your registration or “Cancel” to make changes.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });

            if (!result.isConfirmed) {
                return;
            }
            
            // Prepare form data
            const postNominalTitle = $('#post_nominal_title').val();
            const finalPostNominal = postNominalTitle === 'Others' ? $('#post_nominal_title_custom').val() : postNominalTitle;
            
            const formData = {
                government_agency_id: $('#government_agency_id').val(),
                representative_type: $('#representative_type').val(),
                pre_nominal_title: $('#pre_nominal_title').val(),
                first_name: $('#first_name').val(),
                middle_initial: $('#middle_initial').val(),
                last_name: $('#last_name').val(),
                post_nominal_title: finalPostNominal,
                designation: $('#designation').val(),
                sex: $('#sex').val(),
                gender: $('#gender').val(),
                birth_date: $('#birth_date').val(),
                office_building_no: $('#office_building_no').val(),
                office_house_no: $('#office_house_no').val(),
                office_street_name: $('#office_street_name').val(),
                office_purok: $('#office_purok').val(),
                office_sitio: $('#office_sitio').val(),
                office_region: $('#office_region').val(),
                office_province: $('#office_province').val(),
                office_city_municipality: $('#office_city_municipality').val(),
                office_barangay: $('#office_barangay').val(),
                email: $('#email').val(),
                username: $('#username').val(),
                mobile: $('#mobile').val(),
                landline: $('#landline').val(),
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val(),
            };

            const registerBtn = $('#registerBtn');
            const registerBtnText = $('#registerBtnText');
            const registerBtnLoader = $('#registerBtnLoader');

            // Clear previous errors
            clearErrors();

            // Disable button
            registerBtn.prop('disabled', true);
            registerBtnText.addClass('hidden');
            registerBtnLoader.removeClass('hidden');

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
                registerBtn.prop('disabled', false);
                registerBtnText.removeClass('hidden');
                registerBtnLoader.addClass('hidden');

                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    
                    // Find which step has errors and navigate to it
                    let errorStep = 1;
                    Object.keys(errors).forEach(field => {
                        const errorElement = $(`#${field}-error`);
                        if (errorElement.length) {
                            errorElement.text(errors[field][0]);
                            errorElement.removeClass('hidden');
                            
                            // Determine which step this field belongs to
                            if (['government_agency_id', 'representative_type', 'pre_nominal_title', 'first_name', 'last_name', 'middle_initial', 'post_nominal_title', 'designation', 'sex', 'gender', 'birth_date'].includes(field)) {
                                errorStep = 1;
                            } else if (field.startsWith('office_')) {
                                errorStep = 2;
                            } else if (['email', 'username', 'mobile', 'landline'].includes(field)) {
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
    </script>
    
    @include('components.footer')
</body>
</html>

