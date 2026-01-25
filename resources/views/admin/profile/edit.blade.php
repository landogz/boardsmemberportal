@extends('admin.layout')

@section('title', 'Edit Profile')

@php
    $pageTitle = 'Edit Profile';
    // Determine if post_nominal_title is in the standard list or custom
    $standardPostNominals = ['Sr.', 'Jr.', 'I', 'II', 'III'];
    $postNominalValue = $user->post_nominal_title ?? '';
    $isCustomPostNominal = $postNominalValue && !in_array($postNominalValue, $standardPostNominals);
@endphp

@push('styles')
<style>
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
    .password-input-valid {
        border-color: #10B981 !important;
        background-color: rgba(16, 185, 129, 0.05) !important;
    }
    .password-input-invalid {
        border-color: #ef4444 !important;
        background-color: rgba(239, 68, 68, 0.05) !important;
    }
    .profile-picture-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
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
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .profile-picture-container:hover .profile-picture-overlay {
        opacity: 1;
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
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Profile</h2>
        <p class="text-gray-600 mt-1">Update your profile information</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
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
            <p class="text-sm text-gray-600 mt-4">Click on the image to change your profile picture</p>
            @if($user->profile_picture)
            <button type="button" id="removeProfilePictureBtn" class="mt-2 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-trash-alt mr-2"></i>
                Remove Profile Picture
            </button>
            @endif
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-item active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Personal Info</div>
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
            <!-- Step 1: Personal Information -->
            <div class="step active" id="step1">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="pre_nominal_title" class="block text-sm font-medium text-gray-700 mb-1">Pre Nominal Title</label>
                            <select id="pre_nominal_title" name="pre_nominal_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                                <option value="">Select Title</option>
                                <option value="Mr." {{ $user->pre_nominal_title === 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                <option value="Ms." {{ $user->pre_nominal_title === 'Ms.' ? 'selected' : '' }}>Ms.</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="pre_nominal_title-error"></span>
                        </div>

                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required value="{{ $user->first_name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="First name">
                            <span class="text-red-500 text-sm hidden" id="first_name-error"></span>
                        </div>

                        <div>
                            <label for="middle_initial" class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                            <input type="text" id="middle_initial" name="middle_initial" maxlength="10" value="{{ $user->middle_initial }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="M.I.">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required value="{{ $user->last_name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Last name">
                            <span class="text-red-500 text-sm hidden" id="last_name-error"></span>
                        </div>

                        <div>
                            <label for="post_nominal_title" class="block text-sm font-medium text-gray-700 mb-1">Post Nominal Title</label>
                            <select id="post_nominal_title" name="post_nominal_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                                <option value="">Select Title</option>
                                <option value="Sr." {{ $user->post_nominal_title === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                <option value="Jr." {{ $user->post_nominal_title === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                <option value="I" {{ $user->post_nominal_title === 'I' ? 'selected' : '' }}>I</option>
                                <option value="II" {{ $user->post_nominal_title === 'II' ? 'selected' : '' }}>II</option>
                                <option value="III" {{ $user->post_nominal_title === 'III' ? 'selected' : '' }}>III</option>
                                <option value="Others" {{ $isCustomPostNominal ? 'selected' : '' }}>Others</option>
                            </select>
                            <div id="post_nominal_title_custom_wrapper" class="mt-2 {{ $isCustomPostNominal ? '' : 'hidden' }}">
                                <label for="post_nominal_title_custom" class="block text-xs font-medium text-gray-600 mb-1">Others:</label>
                                <input type="text" id="post_nominal_title_custom" name="post_nominal_title_custom" value="{{ $isCustomPostNominal ? $user->post_nominal_title : '' }}" placeholder="Specify other title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            </div>
                            <span class="text-red-500 text-sm hidden" id="post_nominal_title-error"></span>
                        </div>
                    </div>

                    <!-- Designation -->
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                        <input type="text" id="designation" name="designation" value="{{ $user->designation }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Your designation">
                        <span class="text-red-500 text-sm hidden" id="designation-error"></span>
                    </div>

                    <!-- Sex and Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                            <select id="sex" name="sex" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                                <option value="">Select Sex</option>
                                <option value="Male" {{ $user->sex === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $user->sex === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="sex-error"></span>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ $user->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $user->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Non-Binary" {{ $user->gender === 'Non-Binary' ? 'selected' : '' }}>Non-Binary</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="gender-error"></span>
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : '' }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                        <span class="text-red-500 text-sm hidden" id="birth_date-error"></span>
                    </div>
                </div>
            </div>

            <!-- Step 2: Office Address (PSGC) -->
            <div class="step" id="step2">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Complete Office Address (PSGC)</h3>
                <div class="space-y-4">
                    <!-- Building/House/Street Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="office_building_no" class="block text-sm font-medium text-gray-700 mb-1">Building No.</label>
                            <input type="text" id="office_building_no" name="office_building_no" value="{{ $user->office_building_no }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Building No.">
                        </div>

                        <div>
                            <label for="office_house_no" class="block text-sm font-medium text-gray-700 mb-1">House No.</label>
                            <input type="text" id="office_house_no" name="office_house_no" value="{{ $user->office_house_no }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="House No.">
                        </div>
                    </div>

                    <div>
                        <label for="office_street_name" class="block text-sm font-medium text-gray-700 mb-1">Street Name</label>
                        <input type="text" id="office_street_name" name="office_street_name" value="{{ $user->office_street_name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Street Name">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="office_purok" class="block text-sm font-medium text-gray-700 mb-1">Purok</label>
                            <input type="text" id="office_purok" name="office_purok" value="{{ $user->office_purok }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Purok">
                        </div>

                        <div>
                            <label for="office_sitio" class="block text-sm font-medium text-gray-700 mb-1">Sitio</label>
                            <input type="text" id="office_sitio" name="office_sitio" value="{{ $user->office_sitio }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Sitio">
                        </div>
                    </div>

                    <!-- PSGC Dropdowns -->
                    <div>
                        <label for="office_region" class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                        <select id="office_region" name="office_region" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select Region</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_region-error"></span>
                    </div>

                    <div>
                        <label for="office_province" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                        <select id="office_province" name="office_province" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select Province</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_province-error"></span>
                    </div>

                    <div>
                        <label for="office_city_municipality" class="block text-sm font-medium text-gray-700 mb-1">City/Municipality</label>
                        <select id="office_city_municipality" name="office_city_municipality" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select City/Municipality</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_city_municipality-error"></span>
                    </div>

                    <div>
                        <label for="office_barangay" class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                        <select id="office_barangay" name="office_barangay" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select Barangay</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_barangay-error"></span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Contact Information -->
            <div class="step" id="step3">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h3>
                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required value="{{ $user->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Enter your email">
                        <span class="text-red-500 text-sm hidden" id="email-error"></span>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="username" name="username" value="{{ $user->username }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Username">
                        <span class="text-red-500 text-sm hidden" id="username-error"></span>
                        <span class="text-green-500 text-sm hidden" id="username-success"></span>
                    </div>

                    <!-- Mobile Number -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <input type="text" id="mobile" name="mobile" value="{{ $user->mobile }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="+63 912 345 6789">
                        <span class="text-red-500 text-sm hidden" id="mobile-error"></span>
                        <p class="text-xs text-gray-500 mt-1">Format: +63</p>
                    </div>

                    <!-- Landline -->
                    <div>
                        <label for="landline" class="block text-sm font-medium text-gray-700 mb-1">Company Landline / Office Number</label>
                        <input type="text" id="landline" name="landline" value="{{ $user->landline }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="(02) 8912-12345">
                        <span class="text-red-500 text-sm hidden" id="landline-error"></span>
                        <p class="text-xs text-gray-500 mt-1">Format: (02) 8912-12345 (Optional)</p>
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                        <input type="text" id="company" name="company" value="{{ $user->company }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Company name">
                        <span class="text-red-500 text-sm hidden" id="company-error"></span>
                    </div>
                </div>
            </div>

            <!-- Step 4: Account Security -->
            <div class="step" id="step4">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Security</h3>
                <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change the password</p>
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Enter current password to change password">
                            <button 
                                type="button" 
                                id="toggleCurrentPassword" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors p-2 z-10"
                                aria-label="Toggle password visibility"
                            >
                                <i class="fas fa-eye-slash text-lg" id="currentPasswordEyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Enter new password">
                            <button 
                                type="button" 
                                id="togglePassword" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors p-2 z-10"
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
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Confirm new password">
                            <button 
                                type="button" 
                                id="togglePasswordConfirmation" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors p-2 z-10"
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
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <button type="button" id="prevBtn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition hidden">
                    Previous
                </button>
                <div class="ml-auto">
                    <button type="button" id="nextBtn" class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        Next
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hidden" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <span id="submitBtnText">Update Profile</span>
                        <span id="submitBtnLoader" class="hidden">Updating...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    let currentStep = 1;
    const totalSteps = 4;

    // Store PSGC data
    let regionsData = [];
    let provincesData = [];
    let citiesData = [];
    let barangaysData = [];

    // User's existing address values
    const userOfficeRegion = '{{ $user->office_region }}';
    const userOfficeProvince = '{{ $user->office_province }}';
    const userOfficeCity = '{{ $user->office_city_municipality }}';
    const userOfficeBarangay = '{{ $user->office_barangay }}';

    // Profile picture upload
    $('.profile-picture-container').on('click', function() {
        $('#profilePictureInput').click();
    });

    $('#profilePictureInput').on('change', async function(e) {
        const file = e.target.files[0];
        if (!file) {
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please select a valid image file (JPEG, PNG, JPG, or GIF)',
            });
            $(this).val(''); // Clear the input
            return;
        }

        // Validate file size (2MB = 2048 KB)
        if (file.size > 2048 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Image size must not exceed 2MB',
            });
            $(this).val(''); // Clear the input
            return;
        }

        // Store original image source
        const originalSrc = $('#profilePicturePreview').attr('src');
        
        // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePicturePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);

        // Show loading overlay
        const $container = $('.profile-picture-container');
        const loadingOverlay = $('<div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full z-10" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 50%;"><i class="fas fa-spinner fa-spin text-white text-2xl"></i></div>');
        $container.append(loadingOverlay);

        // Upload the file immediately
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
                $('#profilePicturePreview').attr('src', response.data.profile_picture_url);
                loadingOverlay.remove();
                
                // Update header and sidebar profile pictures
                const newProfilePicUrl = response.data.profile_picture_url;
                $('#headerProfilePicture').attr('src', newProfilePicUrl);
                $('#sidebarProfilePicture').attr('src', newProfilePicUrl);
                
                // Show remove button if it doesn't exist
                if ($('#removeProfilePictureBtn').length === 0) {
                    const removeBtn = $('<button>', {
                        type: 'button',
                        id: 'removeProfilePictureBtn',
                        class: 'mt-2 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors inline-flex items-center',
                        html: '<i class="fas fa-trash-alt mr-2"></i>Remove Profile Picture'
                    });
                    $('#profilePictureInput').after(removeBtn);
                    
                    // Attach event handler
                    $('#removeProfilePictureBtn').on('click', function() {
                        Swal.fire({
                            title: 'Remove Profile Picture?',
                            text: 'Are you sure you want to remove your profile picture? This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Yes, Remove It',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Removing...',
                                    text: 'Please wait while we remove your profile picture.',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                axios.post('{{ route("profile.remove-picture") }}')
                                    .then(function(response) {
                                        if (response.data.success) {
                                            $('#profilePicturePreview').attr('src', response.data.profile_picture_url);
                                            $('#headerProfilePicture').attr('src', response.data.profile_picture_url);
                                            $('#sidebarProfilePicture').attr('src', response.data.profile_picture_url);
                                            $(document).trigger('profilePictureUpdated', [response.data.profile_picture_url]);
                                            $('#removeProfilePictureBtn').fadeOut(300, function() {
                                                $(this).remove();
                                            });

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.data.message || 'Profile picture removed successfully.',
                                                timer: 2000,
                                                showConfirmButton: false,
                                                toast: true,
                                                position: 'top-end'
                                            });
                                        } else {
                                            throw new Error(response.data.message || 'Remove failed');
                                        }
                                    })
                                    .catch(function(error) {
                                        let errorMessage = 'Failed to remove profile picture.';
                                        if (error.response && error.response.data && error.response.data.message) {
                                            errorMessage = error.response.data.message;
                                        }
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Remove Failed',
                                            text: errorMessage,
                                        });
                                    });
                            }
                        });
                    });
                }
                
                // Trigger custom event for other components that might need to update
                $(document).trigger('profilePictureUpdated', [newProfilePicUrl]);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message || 'Profile picture uploaded successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                throw new Error(response.data.message || 'Upload failed');
            }
        } catch (error) {
            // Revert to original image on error
            $('#profilePicturePreview').attr('src', originalSrc);
            loadingOverlay.remove();
            $(this).val(''); // Clear the input

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

    // Remove profile picture
    $('#removeProfilePictureBtn').on('click', function() {
        Swal.fire({
            title: 'Remove Profile Picture?',
            text: 'Are you sure you want to remove your profile picture? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Remove It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing...',
                    text: 'Please wait while we remove your profile picture.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route("profile.remove-picture") }}')
                    .then(function(response) {
                        if (response.data.success) {
                            // Update preview with default avatar
                            $('#profilePicturePreview').attr('src', response.data.profile_picture_url);
                            
                            // Update header and sidebar profile pictures
                            $('#headerProfilePicture').attr('src', response.data.profile_picture_url);
                            $('#sidebarProfilePicture').attr('src', response.data.profile_picture_url);
                            
                            // Trigger custom event for other components
                            $(document).trigger('profilePictureUpdated', [response.data.profile_picture_url]);
                            
                            // Hide remove button
                            $('#removeProfilePictureBtn').fadeOut(300, function() {
                                $(this).remove();
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.data.message || 'Profile picture removed successfully.',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            throw new Error(response.data.message || 'Remove failed');
                        }
                    })
                    .catch(function(error) {
                        let errorMessage = 'Failed to remove profile picture.';
                        if (error.response && error.response.data) {
                            if (error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Remove Failed',
                            text: errorMessage,
                        });
                    });
            }
        });
    });

    // Post nominal title custom input
    $('#post_nominal_title').on('change', function() {
        if ($(this).val() === 'Others') {
            $('#post_nominal_title_custom_wrapper').removeClass('hidden');
        } else {
            $('#post_nominal_title_custom_wrapper').addClass('hidden');
        }
    });

    // Password validation
    function validatePassword(password) {
        return password.length >= 6 &&
               /[A-Z]/.test(password) &&
               /[a-z]/.test(password) &&
               /[0-9]/.test(password) &&
               /[~!@#$%^&*|]/.test(password);
    }

    // Toggle password visibility
    $('#toggleCurrentPassword').on('click', function() {
        const passwordInput = $('#current_password');
        const eyeIcon = $('#currentPasswordEyeIcon');
        
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

    // Username availability check
    let usernameCheckTimeout;
    $('#username').on('input', function() {
        const username = $(this).val();
        clearTimeout(usernameCheckTimeout);
        
        if (username.length < 3) {
            $('#username-success').addClass('hidden');
            $('#username-error').addClass('hidden');
            return;
        }

        usernameCheckTimeout = setTimeout(function() {
            axios.post('{{ route("profile.check-username") }}', { username: username })
                .then(response => {
                    if (response.data.available) {
                        $('#username-success').removeClass('hidden').text('Username is available');
                        $('#username-error').addClass('hidden');
                    } else {
                        $('#username-error').removeClass('hidden').text(response.data.message);
                        $('#username-success').addClass('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error checking username:', error);
                });
        }, 500);
    });

    $(document).ready(function() {
        // Load PSGC data from API
        axios.get('/api/address/regions')
            .then(function(response) {
                regionsData = response.data;
                const regionSelect = $('#office_region');
                regionSelect.html('<option value="">Select Region</option>');
                response.data.forEach(region => {
                    const selected = userOfficeRegion == region.region_code ? 'selected' : '';
                    regionSelect.append(`<option value="${region.region_code}" data-id="${region.id}" ${selected}>${region.region_name}</option>`);
                });
                
                if (userOfficeRegion) {
                    setTimeout(() => {
                        regionSelect.trigger('change');
                    }, 100);
                }
            })
            .catch(function(error) {
                console.error('Failed to load regions:', error);
                $('#office_region').html('<option value="">Error loading regions</option>');
            });

        // Provinces, cities, and barangays are now loaded on-demand via API when dropdowns change

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
                            const selected = userOfficeProvince == province.province_code ? 'selected' : '';
                            provinceSelect.append(`<option value="${province.province_code}" ${selected}>${province.province_name}</option>`);
                        });
                        
                        if (userOfficeProvince) {
                            setTimeout(() => {
                                provinceSelect.trigger('change');
                            }, 100);
                        }
                    })
                    .catch(function(error) {
                        console.error('Failed to load provinces:', error);
                        provinceSelect.html('<option value="">Error loading provinces</option>');
                    });
                
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
                            const selected = userOfficeCity == city.city_code ? 'selected' : '';
                            citySelect.append(`<option value="${city.city_code}" ${selected}>${city.city_name}</option>`);
                        });
                        
                        if (userOfficeCity) {
                            setTimeout(() => {
                                citySelect.trigger('change');
                            }, 100);
                        }
                    })
                    .catch(function(error) {
                        console.error('Failed to load cities:', error);
                        citySelect.html('<option value="">Error loading cities</option>');
                    });
                
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
                            const selected = userOfficeBarangay == barangay.brgy_code ? 'selected' : '';
                            barangaySelect.append(`<option value="${barangay.brgy_code}" ${selected}>${barangay.brgy_name}</option>`);
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
        $('.step').removeClass('active');
        $(`#step${step}`).addClass('active');
        
        $('#prevBtn').toggleClass('hidden', step === 1);
        $('#nextBtn').toggleClass('hidden', step === totalSteps);
        $('#submitBtn').toggleClass('hidden', step !== totalSteps);
        
        updateStepIndicator();
    }

    // Next button
    $('#nextBtn').on('click', function() {
        clearErrors();
        currentStep++;
        showStep(currentStep);
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Previous button
    $('#prevBtn').on('click', function() {
        clearErrors();
        currentStep--;
        showStep(currentStep);
    });

    // Form submission
    $('#profileForm').on('submit', async function(e) {
        e.preventDefault();
        
        const postNominalTitle = $('#post_nominal_title').val();
        const finalPostNominal = postNominalTitle === 'Others' ? $('#post_nominal_title_custom').val() : postNominalTitle;

        const formData = new FormData();
        formData.append('pre_nominal_title', $('#pre_nominal_title').val());
        formData.append('first_name', $('#first_name').val());
        formData.append('middle_initial', $('#middle_initial').val());
        formData.append('last_name', $('#last_name').val());
        formData.append('post_nominal_title', finalPostNominal);
        formData.append('designation', $('#designation').val());
        formData.append('sex', $('#sex').val());
        formData.append('gender', $('#gender').val());
        formData.append('birth_date', $('#birth_date').val());
        formData.append('office_building_no', $('#office_building_no').val());
        formData.append('office_house_no', $('#office_house_no').val());
        formData.append('office_street_name', $('#office_street_name').val());
        formData.append('office_purok', $('#office_purok').val());
        formData.append('office_sitio', $('#office_sitio').val());
        formData.append('office_region', $('#office_region').val());
        formData.append('office_province', $('#office_province').val());
        formData.append('office_city_municipality', $('#office_city_municipality').val());
        formData.append('office_barangay', $('#office_barangay').val());
        formData.append('email', $('#email').val());
        formData.append('username', $('#username').val());
        formData.append('mobile', $('#mobile').val());
        formData.append('landline', $('#landline').val());
        formData.append('company', $('#company').val());

        if ($('#profilePictureInput')[0].files[0]) {
            formData.append('profile_picture', $('#profilePictureInput')[0].files[0]);
        }

        const currentPassword = $('#current_password').val();
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();

        // Validate password if provided
        if (password || passwordConfirmation || currentPassword) {
            if (!currentPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Current password is required to change password',
                });
                return;
            }
            
            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'New password is required',
                });
                return;
            }
            
            if (!validatePassword(password)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Password does not meet requirements. Please check all requirements are met.',
                });
                return;
            }
            
            if (password !== passwordConfirmation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Passwords do not match',
                });
                return;
            }
        }

        const submitBtn = $('#submitBtn');
        const submitBtnText = $('#submitBtnText');
        const submitBtnLoader = $('#submitBtnLoader');

        clearErrors();
        submitBtn.prop('disabled', true);
        submitBtnText.addClass('hidden');
        submitBtnLoader.removeClass('hidden');

        try {
            // Update profile
            const response = await axios.post('{{ route("profile.update") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            // Update password if provided
            if (currentPassword && password && passwordConfirmation) {
                await axios.post('{{ route("profile.password") }}', {
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
                if (response.data.user && response.data.user.profile_picture_url) {
                    $('#profilePicturePreview').attr('src', response.data.user.profile_picture_url);
                }
                window.location.reload();
            });
        } catch (error) {
            submitBtn.prop('disabled', false);
            submitBtnText.removeClass('hidden');
            submitBtnLoader.addClass('hidden');

            if (error.response && error.response.data && error.response.data.errors) {
                const errors = error.response.data.errors;
                Object.keys(errors).forEach(key => {
                    const errorElement = $(`#${key}-error`);
                    if (errorElement.length) {
                        errorElement.removeClass('hidden').text(errors[key][0]);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.response?.data?.message || 'An error occurred while updating your profile',
                });
            }
        }
    });
</script>
@endpush

