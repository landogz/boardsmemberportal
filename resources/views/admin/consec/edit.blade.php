@extends('admin.layout')

@section('title', 'Edit CONSEC Account')

@php
    $pageTitle = 'Edit CONSEC Account';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.consec.index'),
        'text' => 'Back to CONSEC',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
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
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit CONSEC Account</h2>
        <p class="text-gray-600 mt-1">Update CONSEC account information</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
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

        <form id="editCONSECForm" class="space-y-4">
            <!-- Step 1: Personal Information -->
            <div class="step active" id="step1">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="pre_nominal_title" class="block text-sm font-medium text-gray-700 mb-1">Pre Nominal Title *</label>
                            <select id="pre_nominal_title" name="pre_nominal_title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
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
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">Designation *</label>
                        <input type="text" id="designation" name="designation" required value="{{ $user->designation }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Your designation">
                        <span class="text-red-500 text-sm hidden" id="designation-error"></span>
                    </div>

                    <!-- Sex and Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 mb-1">Sex *</label>
                            <select id="sex" name="sex" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                                <option value="">Select Sex</option>
                                <option value="Male" {{ $user->sex === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $user->sex === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="sex-error"></span>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select id="gender" name="gender" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
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
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Birth Date *</label>
                        <input type="date" id="birth_date" name="birth_date" required value="{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : '' }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
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
                        <label for="office_region" class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                        <select id="office_region" name="office_region" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select Region</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_region-error"></span>
                    </div>

                    <div>
                        <label for="office_province" class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                        <select id="office_province" name="office_province" required disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select Province</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_province-error"></span>
                    </div>

                    <div>
                        <label for="office_city_municipality" class="block text-sm font-medium text-gray-700 mb-1">City/Municipality *</label>
                        <select id="office_city_municipality" name="office_city_municipality" required disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
                            <option value="">Select City/Municipality</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="office_city_municipality-error"></span>
                    </div>

                    <div>
                        <label for="office_barangay" class="block text-sm font-medium text-gray-700 mb-1">Barangay *</label>
                        <select id="office_barangay" name="office_barangay" required disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition">
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
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" id="username" name="username" required value="{{ $user->username }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Username">
                        <span class="text-red-500 text-sm hidden" id="username-error"></span>
                    </div>

                    <!-- Mobile Number -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number *</label>
                        <input type="text" id="mobile" name="mobile" required value="{{ $user->mobile }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="+63 912 345 6789">
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
                </div>
            </div>

            <!-- Step 4: Account Security -->
            <div class="step" id="step4">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Security</h3>
                <div class="space-y-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Leave blank to keep current password">
                        <span class="text-red-500 text-sm hidden" id="password-error"></span>
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password. If changing, minimum 6 alphanumeric characters with at least 1 capital, 1 small letter, 1 number, and 1 special character.</p>
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
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition" placeholder="Confirm password">
                        <span class="text-red-500 text-sm hidden" id="password_confirmation-error"></span>
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="h-4 w-4 text-[#055498] border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Account is Active</span>
                        </label>
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
                        <span id="submitBtnText">Update CONSEC Account</span>
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
                
                // If user has a region selected, trigger change to load provinces
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

        // Password validation (only if password is being changed)
        $('#password').on('input', function() {
            const password = $(this).val();
            
            if (password.length > 0) {
                // Check requirements
                $('#req-length').toggleClass('valid invalid', password.length >= 6);
                $('#req-uppercase').toggleClass('valid invalid', /[A-Z]/.test(password));
                $('#req-lowercase').toggleClass('valid invalid', /[a-z]/.test(password));
                $('#req-number').toggleClass('valid invalid', /[0-9]/.test(password));
                $('#req-special').toggleClass('valid invalid', /[~!@#$%^&*|]/.test(password));
            } else {
                // Reset all requirements if password is empty
                $('#req-length, #req-uppercase, #req-lowercase, #req-number, #req-special').removeClass('valid').addClass('invalid');
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
                            const selected = userOfficeProvince == province.province_code ? 'selected' : '';
                            provinceSelect.append(`<option value="${province.province_code}" ${selected}>${province.province_name}</option>`);
                        });
                        
                        // If user has a province selected, trigger change to load cities
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
                        
                        // If user has a city selected, trigger change to load barangays
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

    // Step validation functions
    function validateStep(step) {
        let isValid = true;
        let firstInvalidField = null;
        
        if (step === 1) {
            const preNominalTitle = $('#pre_nominal_title').val();
            const firstName = $('#first_name').val().trim();
            const lastName = $('#last_name').val().trim();
            const designation = $('#designation').val().trim();
            const sex = $('#sex').val();
            const gender = $('#gender').val();
            const birthDate = $('#birth_date').val();
            
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
            
            // Password is optional (only validate if provided)
            if (password) {
                if (!validatePassword(password)) {
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
        $('.step').removeClass('active');
        $(`#step${step}`).addClass('active');
        
        $('#prevBtn').toggleClass('hidden', step === 1);
        $('#nextBtn').toggleClass('hidden', step === totalSteps);
        $('#submitBtn').toggleClass('hidden', step !== totalSteps);
        
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
    $('#editCONSECForm').on('submit', async function(e) {
        e.preventDefault();
        
        if (!validateStep(4)) {
            return;
        }
        
        // Prepare post nominal title (handle "Others" option)
        const postNominalTitle = $('#post_nominal_title').val();
        const finalPostNominal = postNominalTitle === 'Others' ? $('#post_nominal_title_custom').val() : postNominalTitle;

        const formData = {
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
            password: $('#password').val() || null,
            password_confirmation: $('#password_confirmation').val() || null,
            is_active: $('#is_active').is(':checked') ? 1 : 0,
        };

        const submitBtn = $('#submitBtn');
        const submitBtnText = $('#submitBtnText');
        const submitBtnLoader = $('#submitBtnLoader');

        clearErrors();
        submitBtn.prop('disabled', true);
        submitBtnText.addClass('hidden');
        submitBtnLoader.removeClass('hidden');

        try {
            const response = await axios.put(`/admin/consec/{{ $user->id }}`, formData);

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.data.redirect;
                });
            }
        } catch (error) {
            submitBtn.prop('disabled', false);
            submitBtnText.removeClass('hidden');
            submitBtnLoader.addClass('hidden');

            if (error.response?.status === 422) {
                const errors = error.response.data.errors;
                let errorStep = 1;
                
                Object.keys(errors).forEach(field => {
                    const errorElement = $(`#${field.replace(/\./g, '_')}-error`);
                    if (errorElement.length) {
                        errorElement.text(errors[field][0]).removeClass('hidden');
                        
                        if (['pre_nominal_title', 'first_name', 'last_name', 'middle_initial', 'post_nominal_title', 'designation', 'sex', 'gender', 'birth_date'].includes(field)) {
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
@endpush
