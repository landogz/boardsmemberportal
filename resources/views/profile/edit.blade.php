<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Edit Profile - Board Member Portal</title>
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
        .profile-picture-container {
            position: relative;
            display: inline-block;
        }
        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            border-radius: 50%;
            cursor: pointer;
        }
        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
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
            color: #055498;
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

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 md:p-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">Edit Profile</h1>
                <p class="text-gray-600 dark:text-gray-400">Update your profile information</p>
            </div>

            <!-- Profile Picture Section -->
            <div class="mb-8 text-center">
                <div class="profile-picture-container inline-block">
                    @php
                        $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=200&background=055498&color=fff';
                        if ($user->profile_picture) {
                            $media = \App\Models\MediaLibrary::find($user->profile_picture);
                            if ($media) {
                                $profilePic = asset('storage/' . $media->file_path);
                            }
                        }
                    @endphp
                    <img id="profilePicturePreview" src="{{ $profilePic }}" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover shadow-lg" style="border: 4px solid #055498;">
                    <div class="profile-picture-overlay">
                        <span class="text-white font-semibold">Change</span>
                    </div>
                </div>
                <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">Click on the image to change your profile picture</p>
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

            <form id="profileForm" class="space-y-4">
                <!-- Step 1: Government Agency & Personal Information -->
                <div class="step active" id="step1">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        @if(Auth::user()->privilege === 'admin')
                            Personal Information
                        @else
                            Government Agency & Personal Information
                        @endif
                    </h2>
                    
                    <!-- Government Agency -->
                    @if(Auth::user()->privilege !== 'admin')
                    <div class="mb-4">
                        <label for="government_agency_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Government Agency</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <select 
                                    id="government_agency_id" 
                                    name="government_agency_id" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                    @endif

                    <!-- Representative Type -->
                    <div class="mb-4">
                        <label for="representative_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Representative Type</label>
                        <select 
                            id="representative_type" 
                            name="representative_type" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">Select Type</option>
                            <option value="Board Member" {{ Auth::user()->representative_type === 'Board Member' ? 'selected' : '' }}>Board Member</option>
                            <option value="Authorized Representative" {{ Auth::user()->representative_type === 'Authorized Representative' ? 'selected' : '' }}>Authorized Representative</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="representative_type-error"></span>
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="pre_nominal_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pre Nominal Title</label>
                            <select 
                                id="pre_nominal_title" 
                                name="pre_nominal_title" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                                <option value="">Select Title</option>
                                <option value="Mr." {{ $user->pre_nominal_title == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                <option value="Ms." {{ $user->pre_nominal_title == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="pre_nominal_title-error"></span>
                        </div>

                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                value="{{ $user->first_name }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                            <span class="text-red-500 text-sm hidden" id="first_name-error"></span>
                        </div>

                        <div>
                            <label for="middle_initial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Initial</label>
                            <input 
                                type="text" 
                                id="middle_initial" 
                                name="middle_initial" 
                                value="{{ $user->middle_initial }}"
                                maxlength="10"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="M.I."
                            >
                            <span class="text-red-500 text-sm hidden" id="middle_initial-error"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                value="{{ $user->last_name }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                            <span class="text-red-500 text-sm hidden" id="last_name-error"></span>
                        </div>

                        <div>
                            <label for="post_nominal_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Post Nominal Title</label>
                            <select 
                                id="post_nominal_title" 
                                name="post_nominal_title" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                                <option value="">Select Title</option>
                                <option value="Sr." {{ $user->post_nominal_title == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                <option value="Jr." {{ $user->post_nominal_title == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                <option value="I" {{ $user->post_nominal_title == 'I' ? 'selected' : '' }}>I</option>
                                <option value="II" {{ $user->post_nominal_title == 'II' ? 'selected' : '' }}>II</option>
                                <option value="III" {{ $user->post_nominal_title == 'III' ? 'selected' : '' }}>III</option>
                                <option value="Others" {{ !in_array($user->post_nominal_title, ['Sr.', 'Jr.', 'I', 'II', 'III', null, '']) ? 'selected' : '' }}>Others</option>
                            </select>
                            <input 
                                type="text" 
                                id="post_nominal_title_custom" 
                                name="post_nominal_title_custom" 
                                value="{{ !in_array($user->post_nominal_title, ['Sr.', 'Jr.', 'I', 'II', 'III', null, '']) ? $user->post_nominal_title : '' }}"
                                placeholder="Specify other title"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 mt-2 {{ !in_array($user->post_nominal_title, ['Sr.', 'Jr.', 'I', 'II', 'III', null, '']) ? '' : 'hidden' }}"
                            >
                            <span class="text-red-500 text-sm hidden" id="post_nominal_title-error"></span>
                        </div>
                    </div>

                    <!-- Designation -->
                    <div class="mb-4">
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Designation</label>
                        <input 
                            type="text" 
                            id="designation" 
                            name="designation" 
                            value="{{ $user->designation }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Your designation"
                        >
                        <span class="text-red-500 text-sm hidden" id="designation-error"></span>
                    </div>

                    <!-- Sex and Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sex</label>
                            <select 
                                id="sex" 
                                name="sex" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                                <option value="">Select Sex</option>
                                <option value="Male" {{ $user->sex == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $user->sex == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="sex-error"></span>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                            <select 
                                id="gender" 
                                name="gender" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                                <option value="">Select Gender</option>
                                <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Non-Binary" {{ $user->gender == 'Non-Binary' ? 'selected' : '' }}>Non-Binary</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="gender-error"></span>
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div class="mb-4">
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birth Date</label>
                        <input 
                            type="date" 
                            id="birth_date" 
                            name="birth_date" 
                            value="{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : '' }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                        <span class="text-red-500 text-sm hidden" id="birth_date-error"></span>
                    </div>
                </div>

                <!-- Office Address Section -->
                <!-- Step 2: Office Address (PSGC) -->
                <div class="step" id="step2">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Complete Office Address (PSGC)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="office_building_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Building No.</label>
                            <input 
                                type="text" 
                                id="office_building_no" 
                                name="office_building_no" 
                                value="{{ $user->office_building_no }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Building No."
                            >
                        </div>

                        <div>
                            <label for="office_house_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">House No.</label>
                            <input 
                                type="text" 
                                id="office_house_no" 
                                name="office_house_no" 
                                value="{{ $user->office_house_no }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="House No."
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="office_street_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Name</label>
                        <input 
                            type="text" 
                            id="office_street_name" 
                            name="office_street_name" 
                            value="{{ $user->office_street_name }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Street Name"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="office_purok" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purok</label>
                            <input 
                                type="text" 
                                id="office_purok" 
                                name="office_purok" 
                                value="{{ $user->office_purok }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Purok"
                            >
                        </div>

                        <div>
                            <label for="office_sitio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sitio</label>
                            <input 
                                type="text" 
                                id="office_sitio" 
                                name="office_sitio" 
                                value="{{ $user->office_sitio }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Sitio"
                            >
                        </div>
                    </div>

                    <!-- PSGC Dropdowns -->
                    <div class="mb-4">
                        <label for="office_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Region</label>
                        <select 
                            id="office_region" 
                            name="office_region" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">Select Region</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_region-error"></span>
                    </div>

                    <div class="mb-4">
                        <label for="office_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province</label>
                        <select 
                            id="office_province" 
                            name="office_province" 
                            disabled
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">Select Province</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_province-error"></span>
                    </div>

                    <div class="mb-4">
                        <label for="office_city_municipality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City/Municipality</label>
                        <select 
                            id="office_city_municipality" 
                            name="office_city_municipality" 
                            disabled
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">Select City/Municipality</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_city_municipality-error"></span>
                    </div>

                    <div class="mb-4">
                        <label for="office_barangay" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barangay</label>
                        <select 
                            id="office_barangay" 
                            name="office_barangay" 
                            disabled
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">Select Barangay</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_barangay-error"></span>
                    </div>
                </div>

                <!-- Step 3: Contact Information -->
                <div class="step" id="step3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Contact Information</h2>
                    
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ $user->username }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Username"
                        >
                        <span class="text-red-500 text-sm hidden" id="username-error"></span>
                        <span class="text-green-500 text-sm hidden" id="username-success"></span>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ $user->email }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                        <span class="text-red-500 text-sm hidden" id="email-error"></span>
                    </div>

                    <div class="mb-4">
                        <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number *</label>
                        <input 
                            type="text" 
                            id="mobile" 
                            name="mobile" 
                            value="{{ $user->mobile }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="+63 912 345 6789"
                        >
                        <span class="text-red-500 text-sm hidden" id="mobile-error"></span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: +63</p>
                    </div>

                    <div class="mb-4">
                        <label for="landline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Landline / Office Number</label>
                        <input 
                            type="text" 
                            id="landline" 
                            name="landline" 
                            value="{{ $user->landline }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="(02) 8912-12345"
                        >
                        <span class="text-red-500 text-sm hidden" id="landline-error"></span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: (02) 8912-12345 (Optional)</p>
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-4">
                        <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company</label>
                        <input 
                            type="text" 
                            id="company" 
                            name="company" 
                            value="{{ $user->company }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                        <span class="text-red-500 text-sm hidden" id="company-error"></span>
                    </div>
                </div>

                <!-- Step 4: Account Security -->
                <div class="step" id="step4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Account Security</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            >
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Enter new password"
                            >
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
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Confirm new password"
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
                    <div class="ml-auto flex space-x-4">
                        <a href="{{ route('landing') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancel
                        </a>
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
                            id="saveBtn"
                            class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hidden"
                            style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                        >
                            <span id="saveBtnText">Save Changes</span>
                            <span id="saveBtnLoader" class="hidden">Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Store PSGC data
        let regionsData = [];
        let provincesData = [];
        let citiesData = [];
        let barangaysData = [];

        // Profile picture preview and auto-upload
        const profilePictureInput = document.getElementById('profilePictureInput');
        const profilePicturePreview = document.getElementById('profilePicturePreview');
        const profilePictureContainer = document.querySelector('.profile-picture-container');

        profilePictureContainer.addEventListener('click', () => {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please select an image file.',
                });
                return;
            }

            // Validate file size (2MB = 2048KB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Profile picture must be less than 2MB.',
                });
                return;
            }

            // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePicturePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Show loading state
            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait while we upload your profile picture.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Upload the file
            try {
                const formData = new FormData();
                formData.append('profile_picture', file);

                const response = await axios.post('{{ route("profile.upload-picture") }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                if (response.data.success) {
                    // Update preview with the uploaded image URL
                    profilePicturePreview.src = response.data.profile_picture_url;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message || 'Profile picture uploaded successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(response.data.message || 'Upload failed');
                }
            } catch (error) {
                // Revert to original image on error
                const originalSrc = profilePicturePreview.getAttribute('data-original-src') || profilePicturePreview.src;
                profilePicturePreview.src = originalSrc;

                let errorMessage = 'Failed to upload profile picture.';
                if (error.response && error.response.data) {
                    if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.response.data.errors && error.response.data.errors.profile_picture) {
                        errorMessage = error.response.data.errors.profile_picture[0];
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: errorMessage,
                });
            }
        });

        // Store original image source
        profilePicturePreview.setAttribute('data-original-src', profilePicturePreview.src);

        // Load Government Agencies and PSGC data
        // Store agencies data with logos for profile edit
        let agenciesDataMap = {};

        $(document).ready(function() {
            // Load Government Agencies (only if not admin)
            @if(Auth::user()->privilege !== 'admin')
            const userAgencyId = '{{ $user->government_agency_id }}';
            $.ajax({
                url: '/api/government-agencies',
                method: 'GET',
                success: function(agencies) {
                    const agencySelect = $('#government_agency_id');
                    agencySelect.html('<option value="">Select Agency</option>');
                    agencies.forEach(agency => {
                        const selected = userAgencyId == agency.id ? 'selected' : '';
                        // Store logo URL in map
                        agenciesDataMap[agency.id] = agency.logo_url || null;
                        agencySelect.append(`<option value="${agency.id}" ${selected}>${agency.name}${agency.code ? ' (' + agency.code + ')' : ''}</option>`);
                    });

                    // Trigger change once to show current logo if any
                    agencySelect.trigger('change');
                },
                error: function() {
                    $('#government_agency_id').html('<option value="">Error loading agencies</option>');
                }
            });
            @endif

            // Show agency logo when agency is selected (only if not admin)
            @if(Auth::user()->privilege !== 'admin')
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
            @endif

            // Load PSGC data
            const userOfficeRegion = '{{ $user->office_region }}';
            const userOfficeProvince = '{{ $user->office_province }}';
            const userOfficeCity = '{{ $user->office_city_municipality }}';
            const userOfficeBarangay = '{{ $user->office_barangay }}';
            
            $.getJSON('/address/region.json', function(regions) {
                regionsData = regions;
                const regionSelect = $('#office_region');
                regionSelect.html('<option value="">Select Region</option>');
                regions.forEach(region => {
                    const selected = userOfficeRegion == region.region_code ? 'selected' : '';
                    regionSelect.append(`<option value="${region.region_code}" data-id="${region.id}" ${selected}>${region.region_name}</option>`);
                });
                
                // If user has a region selected, trigger change to load provinces
                if (userOfficeRegion) {
                    setTimeout(() => {
                        regionSelect.trigger('change');
                    }, 100);
                }
            }).fail(function() {
                $('#office_region').html('<option value="">Error loading regions</option>');
            });

            $.getJSON('/address/province.json', function(provinces) {
                provincesData = provinces;
            }).fail(function() {
                console.error('Failed to load provinces');
            });

            $.getJSON('/address/city.json', function(cities) {
                citiesData = cities;
            }).fail(function() {
                console.error('Failed to load cities');
            });

            $.getJSON('/address/barangay.json', function(barangays) {
                barangaysData = barangays;
            }).fail(function() {
                console.error('Failed to load barangays');
            });

            // Handle post nominal title "Others" option
            $('#post_nominal_title').on('change', function() {
                if ($(this).val() === 'Others') {
                    $('#post_nominal_title_custom').removeClass('hidden').prop('required', true);
                } else {
                    $('#post_nominal_title_custom').addClass('hidden').prop('required', false).val('');
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

            // Username availability check
            let usernameCheckTimeout;
            const originalUsername = '{{ $user->username }}';
            
            $('#username').on('input', function() {
                const username = $(this).val().trim();
                const usernameInput = $(this);
                const usernameError = $('#username-error');
                const usernameSuccess = $('#username-success');
                
                // Clear previous timeout
                clearTimeout(usernameCheckTimeout);
                
                // Hide previous messages
                usernameError.addClass('hidden');
                usernameSuccess.addClass('hidden');
                
                // Reset border color
                usernameInput.removeClass('border-red-500 border-green-500');
                
                // Only check if username has changed
                if (!username || username === originalUsername) {
                    return;
                }
                
                // Debounce the check (wait 500ms after user stops typing)
                usernameCheckTimeout = setTimeout(function() {
                    if (username.length < 3) {
                        usernameError.text('Username must be at least 3 characters').removeClass('hidden');
                        usernameInput.addClass('border-red-500');
                        return;
                    }
                    
                    // Check username availability
                    axios.post('/profile/check-username', {
                        username: username
                    })
                    .then(function(response) {
                        if (response.data.available) {
                            usernameSuccess.text(response.data.message).removeClass('hidden');
                            usernameError.addClass('hidden');
                            usernameInput.removeClass('border-red-500').addClass('border-green-500');
                        } else {
                            usernameError.text(response.data.message).removeClass('hidden');
                            usernameSuccess.addClass('hidden');
                            usernameInput.removeClass('border-green-500').addClass('border-red-500');
                        }
                    })
                    .catch(function(error) {
                        if (error.response && error.response.data && error.response.data.message) {
                            usernameError.text(error.response.data.message).removeClass('hidden');
                            usernameInput.addClass('border-red-500');
                        } else {
                            usernameError.text('Error checking username availability').removeClass('hidden');
                            usernameInput.addClass('border-red-500');
                        }
                    });
                }, 500);
            });

            // Password validation
            $('#password').on('input', function() {
                const password = $(this).val();
                
                // Check requirements
                $('#req-length').toggleClass('valid invalid', password.length >= 6);
                $('#req-uppercase').toggleClass('valid invalid', /[A-Z]/.test(password));
                $('#req-lowercase').toggleClass('valid invalid', /[a-z]/.test(password));
                $('#req-number').toggleClass('valid invalid', /[0-9]/.test(password));
                $('#req-special').toggleClass('valid invalid', /[~!@#$%^&*|]/.test(password));
            });

            // PSGC Cascading Dropdowns
            $('#office_region').on('change', function() {
                const regionCode = $(this).val();
                const provinceSelect = $('#office_province');
                const citySelect = $('#office_city_municipality');
                const barangaySelect = $('#office_barangay');
                
                if (regionCode) {
                    const filteredProvinces = provincesData.filter(p => p.region_code === regionCode);
                    provinceSelect.prop('disabled', false).html('<option value="">Select Province</option>');
                    filteredProvinces.forEach(province => {
                        const selected = userOfficeProvince == province.province_code ? 'selected' : '';
                        provinceSelect.append(`<option value="${province.province_code}" ${selected}>${province.province_name}</option>`);
                    });
                    
                    citySelect.prop('disabled', true).html('<option value="">Select City/Municipality</option>');
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                    
                    // If user has a province selected, trigger change to load cities
                    if (userOfficeProvince) {
                        setTimeout(() => {
                            provinceSelect.trigger('change');
                        }, 100);
                    }
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
                    const filteredCities = citiesData.filter(c => c.province_code === provinceCode);
                    citySelect.prop('disabled', false).html('<option value="">Select City/Municipality</option>');
                    filteredCities.forEach(city => {
                        const selected = userOfficeCity == city.city_code ? 'selected' : '';
                        citySelect.append(`<option value="${city.city_code}" ${selected}>${city.city_name}</option>`);
                    });
                    
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                    
                    // If user has a city selected, trigger change to load barangays
                    if (userOfficeCity) {
                        setTimeout(() => {
                            citySelect.trigger('change');
                        }, 100);
                    }
                } else {
                    citySelect.prop('disabled', true).html('<option value="">Select City/Municipality</option>');
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                }
            });

            $('#office_city_municipality').on('change', function() {
                const cityCode = $(this).val();
                const barangaySelect = $('#office_barangay');
                
                if (cityCode) {
                    const filteredBarangays = barangaysData.filter(b => b.city_code === cityCode);
                    barangaySelect.prop('disabled', false).html('<option value="">Select Barangay</option>');
                    filteredBarangays.forEach(barangay => {
                        const selected = userOfficeBarangay == barangay.brgy_code ? 'selected' : '';
                        barangaySelect.append(`<option value="${barangay.brgy_code}" ${selected}>${barangay.brgy_name}</option>`);
                    });
                } else {
                    barangaySelect.prop('disabled', true).html('<option value="">Select Barangay</option>');
                }
            });
        });

        // Tab navigation
        let currentStep = 1;
        const totalSteps = 4;

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
            $('#saveBtn').toggleClass('hidden', step !== totalSteps);
            
            updateStepIndicator();
        }

        // Initialize step indicator
        updateStepIndicator();

        // Next button
        $('#nextBtn').on('click', function() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        // Previous button
        $('#prevBtn').on('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Password validation function
        function validatePassword(password) {
            return password.length >= 6 &&
                   /[A-Z]/.test(password) &&
                   /[a-z]/.test(password) &&
                   /[0-9]/.test(password) &&
                   /[~!@#$%^&*|]/.test(password);
        }

        // Form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            
            // Government Agency & Personal Information
            @if(Auth::user()->privilege !== 'admin')
            formData.append('government_agency_id', document.getElementById('government_agency_id').value);
            @endif
            formData.append('representative_type', document.getElementById('representative_type').value);
            formData.append('pre_nominal_title', document.getElementById('pre_nominal_title').value);
            formData.append('first_name', document.getElementById('first_name').value);
            formData.append('middle_initial', document.getElementById('middle_initial').value);
            formData.append('last_name', document.getElementById('last_name').value);
            const postNominalTitle = document.getElementById('post_nominal_title').value;
            const finalPostNominal = postNominalTitle === 'Others' ? document.getElementById('post_nominal_title_custom').value : postNominalTitle;
            formData.append('post_nominal_title', finalPostNominal);
            formData.append('designation', document.getElementById('designation').value);
            formData.append('sex', document.getElementById('sex').value);
            formData.append('gender', document.getElementById('gender').value);
            formData.append('birth_date', document.getElementById('birth_date').value);
            
            // Office Address
            formData.append('office_building_no', document.getElementById('office_building_no').value);
            formData.append('office_house_no', document.getElementById('office_house_no').value);
            formData.append('office_street_name', document.getElementById('office_street_name').value);
            formData.append('office_purok', document.getElementById('office_purok').value);
            formData.append('office_sitio', document.getElementById('office_sitio').value);
            formData.append('office_region', document.getElementById('office_region').value);
            formData.append('office_province', document.getElementById('office_province').value);
            formData.append('office_city_municipality', document.getElementById('office_city_municipality').value);
            formData.append('office_barangay', document.getElementById('office_barangay').value);
            
            // Contact Information
            formData.append('email', document.getElementById('email').value);
            formData.append('username', document.getElementById('username').value);
            formData.append('mobile', document.getElementById('mobile').value);
            formData.append('landline', document.getElementById('landline').value);
            
            // Additional Information
            formData.append('company', document.getElementById('company').value);
            
            if (profilePictureInput.files[0]) {
                formData.append('profile_picture', profilePictureInput.files[0]);
            }

            const currentPassword = document.getElementById('current_password').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            // Validate password if provided
            if (password || passwordConfirmation || currentPassword) {
                if (!currentPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Current password is required to change password',
                    });
                    saveBtn.disabled = false;
                    saveBtnText.classList.remove('hidden');
                    saveBtnLoader.classList.add('hidden');
                    return;
                }
                
                if (!password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'New password is required',
                    });
                    saveBtn.disabled = false;
                    saveBtnText.classList.remove('hidden');
                    saveBtnLoader.classList.add('hidden');
                    return;
                }
                
                if (!validatePassword(password)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Password does not meet requirements. Please check all requirements are met.',
                    });
                    saveBtn.disabled = false;
                    saveBtnText.classList.remove('hidden');
                    saveBtnLoader.classList.add('hidden');
                    return;
                }
                
                if (password !== passwordConfirmation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Passwords do not match',
                    });
                    saveBtn.disabled = false;
                    saveBtnText.classList.remove('hidden');
                    saveBtnLoader.classList.add('hidden');
                    return;
                }
            }

            const saveBtn = document.getElementById('saveBtn');
            const saveBtnText = document.getElementById('saveBtnText');
            const saveBtnLoader = document.getElementById('saveBtnLoader');

            // Clear previous errors
            document.querySelectorAll('.text-red-500').forEach(el => {
                el.classList.add('hidden');
            });

            // Disable button
            saveBtn.disabled = true;
            saveBtnText.classList.add('hidden');
            saveBtnLoader.classList.remove('hidden');

            try {
                // Update profile
                const response = await axios.post('/profile/update', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Update password if provided
                if (currentPassword && password && passwordConfirmation) {
                    await axios.post('/profile/password', {
                        current_password: currentPassword,
                        password: password,
                        password_confirmation: passwordConfirmation
                    });
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Profile updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    if (response.data.user.profile_picture_url) {
                        profilePicturePreview.src = response.data.user.profile_picture_url;
                    }
                    window.location.reload();
                });
            } catch (error) {
                saveBtn.disabled = false;
                saveBtnText.classList.remove('hidden');
                saveBtnLoader.classList.add('hidden');

                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    
                    Object.keys(errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        if (errorElement) {
                            errorElement.textContent = errors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });

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

