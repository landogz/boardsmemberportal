@extends('admin.layout')

@section('title', 'Edit Admin Account')

@php
    $pageTitle = 'Edit Admin Account';
    $headerActions = [[
        'url' => route('admin.admin-accounts.index'),
        'text' => 'Back to Admin Accounts',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ]];
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .password-requirements li {
        transition: color 0.2s ease;
    }
    .password-requirements li.valid { color: #059669; }
    .password-requirements li.invalid { color: #DC2626; }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Admin Account</h2>
        <p class="text-gray-600 mt-1">Update admin user details</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="editAdminAccountForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ $user->first_name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ $user->last_name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                    <input type="text" name="middle_initial" value="{{ $user->middle_initial }}" maxlength="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Extension Name</label>
                    <input type="text" name="extension_name" value="{{ $user->extension_name }}" maxlength="50" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation *</label>
                    <input type="text" name="designation" value="{{ $user->designation }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile (+63...)</label>
                    <input type="text" name="mobile" value="{{ $user->mobile }}" placeholder="+639123456789" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors p-2 z-10" aria-label="Toggle password visibility">
                            <i class="fas fa-eye-slash" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                    <ul class="password-requirements text-xs mt-2 space-y-1">
                        <li id="req-length" class="invalid">Minimum of 8 characters</li>
                        <li id="req-uppercase" class="invalid">At least 1 capital letter</li>
                        <li id="req-lowercase" class="invalid">At least 1 small letter</li>
                        <li id="req-number" class="invalid">At least 1 number</li>
                        <li id="req-special" class="invalid">At least 1 special character (e.g. ! @ # $ % & * ( ) - _ = + . , )</li>
                    </ul>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                        <button type="button" id="togglePasswordConfirmation" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors p-2 z-10" aria-label="Toggle password visibility">
                            <i class="fas fa-eye-slash" id="passwordConfirmationEyeIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                <button type="submit" id="submitBtn" class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    Update Admin Account
                </button>
                <a href="{{ route('admin.admin-accounts.index') }}" class="px-6 py-3 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    function validatePassword(password) {
        return password.length >= 8 &&
            /[A-Z]/.test(password) &&
            /[a-z]/.test(password) &&
            /[0-9]/.test(password) &&
            /[^A-Za-z0-9]/.test(password);
    }

    function updatePasswordRequirements(password) {
        if (!password) {
            $('#req-length, #req-uppercase, #req-lowercase, #req-number, #req-special').removeClass('valid').addClass('invalid');
            return;
        }
        $('#req-length').toggleClass('valid invalid', password.length >= 8);
        $('#req-uppercase').toggleClass('valid invalid', /[A-Z]/.test(password));
        $('#req-lowercase').toggleClass('valid invalid', /[a-z]/.test(password));
        $('#req-number').toggleClass('valid invalid', /[0-9]/.test(password));
        $('#req-special').toggleClass('valid invalid', /[^A-Za-z0-9]/.test(password));
    }

    $('#password').on('input', function() {
        updatePasswordRequirements($(this).val() || '');
    });

    $('#togglePassword').on('click', function() {
        const $input = $('#password');
        const $icon = $('#passwordEyeIcon');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    $('#togglePasswordConfirmation').on('click', function() {
        const $input = $('#password_confirmation');
        const $icon = $('#passwordConfirmationEyeIcon');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    $('#editAdminAccountForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true);

        const password = $('#password').val() || '';
        const passwordConfirmation = $('#password_confirmation').val() || '';
        if (password) {
            if (!validatePassword(password)) {
                Swal.fire({ icon: 'error', title: 'Invalid Password', text: 'Password does not meet the required format.' });
                submitBtn.prop('disabled', false);
                return;
            }
            if (!passwordConfirmation) {
                Swal.fire({ icon: 'error', title: 'Missing Confirmation', text: 'Please confirm the new password.' });
                submitBtn.prop('disabled', false);
                return;
            }
            if (password !== passwordConfirmation) {
                Swal.fire({ icon: 'error', title: 'Password Mismatch', text: 'Password confirmation does not match.' });
                submitBtn.prop('disabled', false);
                return;
            }
        }

        const payload = {
            first_name: $('[name="first_name"]').val(),
            middle_initial: $('[name="middle_initial"]').val(),
            last_name: $('[name="last_name"]').val(),
            extension_name: $('[name="extension_name"]').val(),
            designation: $('[name="designation"]').val(),
            email: $('[name="email"]').val(),
            mobile: $('[name="mobile"]').val(),
            is_active: $('[name="is_active"]').val(),
            password: password,
            password_confirmation: passwordConfirmation,
            _method: 'PUT'
        };

        axios.post('{{ route("admin.admin-accounts.update", $user->id) }}', payload)
            .then((response) => {
                Swal.fire({ icon: 'success', title: 'Success', text: response.data.message })
                    .then(() => window.location.href = response.data.redirect);
            })
            .catch((error) => {
                Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.message || 'Failed to update admin account.' });
            })
            .finally(() => submitBtn.prop('disabled', false));
    });
</script>
@endpush

