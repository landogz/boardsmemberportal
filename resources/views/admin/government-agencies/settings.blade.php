@extends('admin.layout')

@section('title', 'Agency Settings')

@php
    $pageTitle = 'Agency Settings';
    $headerActions = [];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Agency Settings</h2>
        <p class="text-gray-600 mt-1">Configure general settings for government agencies</p>
    </div>

    <!-- Settings Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Default Agency Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Default Settings</h3>
            <form id="settingsForm" class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="auto_activate_agencies" 
                            name="auto_activate_agencies"
                            {{ ($settings['auto_activate_agencies'] ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-[#055498] accent-[#055498]"
                        >
                        <span class="ml-2 text-sm text-gray-700">Auto-activate new agencies</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-6">New agencies will be automatically activated when created</p>
                </div>
                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="require_agency_code" 
                            name="require_agency_code"
                            {{ ($settings['require_agency_code'] ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-[#055498] accent-[#055498]"
                        >
                        <span class="ml-2 text-sm text-gray-700">Require agency code</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-6">Agency code will be mandatory when creating new agencies</p>
                </div>
                <div class="pt-4 border-t">
                    <button 
                        type="submit" 
                        id="saveSettingsBtn"
                        class="px-6 py-2 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                        style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                    >
                        <span id="saveSettingsBtnText">Save Settings</span>
                        <span id="saveSettingsBtnLoader" class="hidden">Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Agency Statistics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Agencies</span>
                    <span class="text-lg font-bold text-gray-900">{{ \App\Models\GovernmentAgency::count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Agencies</span>
                    <span class="text-lg font-bold" style="color: #10B981;">{{ \App\Models\GovernmentAgency::where('is_active', true)->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Inactive Agencies</span>
                    <span class="text-lg font-bold" style="color: #EF4444;">{{ \App\Models\GovernmentAgency::where('is_active', false)->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Users</span>
                    <span class="text-lg font-bold text-gray-900">{{ \App\Models\User::whereNotNull('government_agency_id')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Bulk Actions</h3>
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <button 
                    onclick="activateAllAgencies()" 
                    class="px-4 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors"
                >
                    <i class="fas fa-check-circle mr-2"></i>Activate All Agencies
                </button>
                <button 
                    onclick="deactivateAllAgencies()" 
                    class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors"
                >
                    <i class="fas fa-times-circle mr-2"></i>Deactivate All Agencies
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Set up axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Save settings form
    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveSettingsBtn');
        const saveBtnText = document.getElementById('saveSettingsBtnText');
        const saveBtnLoader = document.getElementById('saveSettingsBtnLoader');

        saveBtn.disabled = true;
        saveBtnText.classList.add('hidden');
        saveBtnLoader.classList.remove('hidden');

        const formData = {
            auto_activate_agencies: document.getElementById('auto_activate_agencies').checked,
            require_agency_code: document.getElementById('require_agency_code').checked
        };

        try {
            const response = await axios.post('{{ route("admin.government-agencies.settings.save") }}', formData);
            
            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'An error occurred. Please try again.'
            });
        } finally {
            saveBtn.disabled = false;
            saveBtnText.classList.remove('hidden');
            saveBtnLoader.classList.add('hidden');
        }
    });

    function activateAllAgencies() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will activate all inactive agencies.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, activate all!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post('{{ route("admin.government-agencies.bulk.activate") }}');
                    
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.'
                    });
                }
            }
        });
    }

    function deactivateAllAgencies() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will deactivate all active agencies.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, deactivate all!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post('{{ route("admin.government-agencies.bulk.deactivate") }}');
                    
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.'
                    });
                }
            }
        });
    }
</script>
@endpush

