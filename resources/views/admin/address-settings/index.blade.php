@extends('admin.layout')

@section('title', 'Address Settings')

@php
    $pageTitle = 'Address Settings';
    $headerActions = [];
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .tab-button {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .tab-button.active {
        background: linear-gradient(135deg, #055498 0%, #123a60 100%);
        color: white;
        border-color: #055498;
    }
    .tab-button:not(.active):hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    /* Pagination Styles */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .pagination > * {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        min-width: 2.5rem;
        height: 2.5rem;
    }
    .pagination a,
    .pagination span:not(.active span):not(.disabled span) {
        color: #374151;
        background: white;
        border: 1px solid #e5e7eb;
    }
    .pagination a:hover {
        background: linear-gradient(135deg, #055498 0%, #123a60 100%);
        color: white;
        border-color: #055498;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(5, 84, 152, 0.2);
    }
    .pagination .active span,
    .pagination span.active {
        background: linear-gradient(135deg, #055498 0%, #123a60 100%);
        color: white;
        border-color: #055498;
        font-weight: 600;
    }
    .pagination .disabled span,
    .pagination span.disabled {
        color: #9ca3af;
        background: #f3f4f6;
        border-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .action-btn {
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Address Settings</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Manage regions, provinces, cities, and barangays</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap gap-2">
                <button onclick="switchTab('regions')" class="tab-button px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-semibold text-sm sm:text-base {{ $type === 'regions' ? 'active' : 'bg-white text-gray-700' }}">
                    <i class="fas fa-map mr-2"></i> Regions
                </button>
                <button onclick="switchTab('provinces')" class="tab-button px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-semibold text-sm sm:text-base {{ $type === 'provinces' ? 'active' : 'bg-white text-gray-700' }}">
                    <i class="fas fa-map-marked-alt mr-2"></i> Provinces
                </button>
                <button onclick="switchTab('cities')" class="tab-button px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-semibold text-sm sm:text-base {{ $type === 'cities' ? 'active' : 'bg-white text-gray-700' }}">
                    <i class="fas fa-city mr-2"></i> Cities
                </button>
                <button onclick="switchTab('barangays')" class="tab-button px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-semibold text-sm sm:text-base {{ $type === 'barangays' ? 'active' : 'bg-white text-gray-700' }}">
                    <i class="fas fa-home mr-2"></i> Barangays
                </button>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            <!-- Header with Search and Add Button -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Manage {{ ucfirst($type) }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Total: {{ $data->total() }} {{ strtolower($type) }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('admin.address-settings.index') }}" class="flex-1 md:flex-initial">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="Search {{ ucfirst($type) }}..." 
                                class="w-full md:w-64 px-4 py-2 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            @if($search)
                            <a href="{{ route('admin.address-settings.index', ['type' => $type]) }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                    <button onclick="openCreateModal()" class="px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300 hover:opacity-90 whitespace-nowrap text-sm sm:text-base" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-plus mr-2"></i> Add New
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if($type === 'regions')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PSGC Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region Name</th>
                            @elseif($type === 'provinces')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PSGC Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            @elseif($type === 'cities')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PSGC Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            @elseif($type === 'barangays')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barangay Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barangay Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            @if($type === 'regions')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->psgc_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->region_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->region_name }}</td>
                            @elseif($type === 'provinces')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->province_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->province_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->psgc_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->region->region_name ?? $item->region_code }}</td>
                            @elseif($type === 'cities')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->city_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->city_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->psgc_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->province->province_name ?? $item->province_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->region->region_name ?? $item->region_code }}</td>
                            @elseif($type === 'barangays')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->brgy_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->brgy_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->city->city_name ?? $item->city_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->province->province_name ?? $item->province_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->region->region_name ?? $item->region_code }}</td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <button onclick="openEditModal({{ $item->id }})" class="action-btn text-blue-600 hover:text-blue-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteItem({{ $item->id }})" class="action-btn text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No {{ $type }} found</p>
                                    <p class="text-sm mt-2">
                                        @if($search)
                                            No results match your search criteria.
                                        @else
                                            Get started by adding your first {{ strtolower($type) }}.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($data->hasPages())
            <div class="pagination">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add New {{ ucfirst($type) }}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="addressForm" class="p-6">
            <input type="hidden" id="itemId" name="id">
            <input type="hidden" id="itemType" name="type" value="{{ $type === 'regions' ? 'region' : ($type === 'provinces' ? 'province' : ($type === 'cities' ? 'city' : 'barangay')) }}">

            @if($type === 'regions')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PSGC Code <span class="text-red-500">*</span></label>
                    <input type="text" id="psgc_code" name="psgc_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region Code <span class="text-red-500">*</span></label>
                    <input type="text" id="region_code" name="region_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region Name <span class="text-red-500">*</span></label>
                    <input type="text" id="region_name" name="region_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
            @elseif($type === 'provinces')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                    <select id="region_code" name="region_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->region_code }}">{{ $region->region_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province Code <span class="text-red-500">*</span></label>
                    <input type="text" id="province_code" name="province_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province Name <span class="text-red-500">*</span></label>
                    <input type="text" id="province_name" name="province_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PSGC Code <span class="text-red-500">*</span></label>
                    <input type="text" id="psgc_code" name="psgc_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
            @elseif($type === 'cities')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                    <select id="region_code" name="region_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->region_code }}">{{ $region->region_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->province_code }}" data-region="{{ $province->region_code }}">{{ $province->province_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">City Code <span class="text-red-500">*</span></label>
                    <input type="text" id="city_code" name="city_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">City Name <span class="text-red-500">*</span></label>
                    <input type="text" id="city_name" name="city_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PSGC Code <span class="text-red-500">*</span></label>
                    <input type="text" id="psgc_code" name="psgc_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
            @elseif($type === 'barangays')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                    <select id="region_code" name="region_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->region_code }}">{{ $region->region_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required disabled>
                        <option value="">Select Province</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">City/Municipality <span class="text-red-500">*</span></label>
                    <select id="city_code" name="city_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required disabled>
                        <option value="">Select City/Municipality</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay Code <span class="text-red-500">*</span></label>
                    <input type="text" id="brgy_code" name="brgy_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay Name <span class="text-red-500">*</span></label>
                    <input type="text" id="brgy_name" name="brgy_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                </div>
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300 hover:opacity-90" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <span id="submitText">Save</span>
                    <span id="submitLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Saving...
                    </span>
                </button>
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

    function switchTab(type) {
        const search = new URLSearchParams(window.location.search).get('search') || '';
        let url = '{{ route("admin.address-settings.index") }}?type=' + type;
        if (search) {
            url += '&search=' + encodeURIComponent(search);
        }
        window.location.href = url;
    }

    // Auto-submit search on Enter key
    document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.closest('form').submit();
        }
    });

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New {{ ucfirst($type) }}';
        document.getElementById('addressForm').reset();
        document.getElementById('itemId').value = '';
        @if($type === 'barangays')
        resetBarangayCascades();
        @endif
        document.getElementById('addressModal').classList.remove('hidden');
    }

    function openEditModal(id) {
        // Fetch item data and populate form
        const type = '{{ $type === "regions" ? "region" : ($type === "provinces" ? "province" : ($type === "cities" ? "city" : "barangay")) }}';
        const item = @json($data->items());
        const itemData = item.find(i => i.id == id);
        
        if (!itemData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Item not found',
                confirmButtonColor: '#055498'
            });
            return;
        }

        document.getElementById('modalTitle').textContent = 'Edit {{ ucfirst($type) }}';
        document.getElementById('itemId').value = id;

        @if($type === 'regions')
            document.getElementById('psgc_code').value = itemData.psgc_code || '';
            document.getElementById('region_code').value = itemData.region_code || '';
            document.getElementById('region_name').value = itemData.region_name || '';
        @elseif($type === 'provinces')
            document.getElementById('region_code').value = itemData.region_code || '';
            document.getElementById('province_code').value = itemData.province_code || '';
            document.getElementById('province_name').value = itemData.province_name || '';
            document.getElementById('psgc_code').value = itemData.psgc_code || '';
        @elseif($type === 'cities')
            document.getElementById('region_code').value = itemData.region_code || '';
            document.getElementById('province_code').value = itemData.province_code || '';
            document.getElementById('city_code').value = itemData.city_code || '';
            document.getElementById('city_name').value = itemData.city_name || '';
            document.getElementById('psgc_code').value = itemData.psgc_code || '';
        @elseif($type === 'barangays')
            document.getElementById('region_code').value = itemData.region_code || '';
            document.getElementById('brgy_code').value = itemData.brgy_code || '';
            document.getElementById('brgy_name').value = itemData.brgy_name || '';
            // Load provinces for region, then cities for province, then set selections
            const regionCode = itemData.region_code;
            const provinceCode = itemData.province_code;
            const cityCode = itemData.city_code;
            const provinceSelect = document.getElementById('province_code');
            const citySelect = document.getElementById('city_code');
            provinceSelect.innerHTML = '<option value="">Loading...</option>';
            provinceSelect.disabled = true;
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            citySelect.disabled = true;
            axios.get('/api/address/provinces', { params: { region_code: regionCode } })
                .then(function(r) {
                    provinceSelect.innerHTML = '<option value="">Select Province</option>';
                    r.data.forEach(function(p) {
                        const opt = document.createElement('option');
                        opt.value = p.province_code;
                        opt.textContent = p.province_name;
                        provinceSelect.appendChild(opt);
                    });
                    provinceSelect.value = provinceCode || '';
                    provinceSelect.disabled = false;
                    return axios.get('/api/address/cities', { params: { province_code: provinceCode } });
                })
                .then(function(r) {
                    citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                    r.data.forEach(function(c) {
                        const opt = document.createElement('option');
                        opt.value = c.city_code;
                        opt.textContent = c.city_name;
                        citySelect.appendChild(opt);
                    });
                    citySelect.value = cityCode || '';
                    citySelect.disabled = false;
                })
                .catch(function() {
                    provinceSelect.innerHTML = '<option value="">Error loading</option>';
                    provinceSelect.disabled = false;
                });
        @endif

        document.getElementById('addressModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('addressModal').classList.add('hidden');
        document.getElementById('addressForm').reset();
    }

    // Form submission
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const submitText = document.getElementById('submitText');
        const submitLoading = document.getElementById('submitLoading');
        const itemId = document.getElementById('itemId').value;
        const formData = new FormData(this);
        const type = formData.get('type');

        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        submitBtn.disabled = true;

        const url = itemId 
            ? `/admin/address-settings/${itemId}`
            : '/admin/address-settings/store';
        const method = itemId ? 'PUT' : 'POST';

        axios({
            method: method,
            url: url,
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(function(response) {
            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.data.message,
                    confirmButtonColor: '#055498'
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(function(error) {
            let errorMessage = 'An error occurred. Please try again.';
            if (error.response && error.response.data) {
                if (error.response.data.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response.data.errors) {
                    const errors = error.response.data.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: errorMessage,
                confirmButtonColor: '#055498'
            });
        })
        .finally(function() {
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
            submitBtn.disabled = false;
        });
    });

    function deleteItem(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const type = '{{ $type === "regions" ? "region" : ($type === "provinces" ? "province" : ($type === "cities" ? "city" : "barangay")) }}';
                
                axios.delete(`/admin/address-settings/${id}`, {
                    data: { type: type }
                })
                .then(function(response) {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.data.message,
                            confirmButtonColor: '#055498'
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete item.',
                        confirmButtonColor: '#055498'
                    });
                });
            }
        });
    }

    // Cascading dropdowns: Cities tab (region filters province by data-region)
    @if($type === 'cities')
    document.getElementById('region_code')?.addEventListener('change', function() {
        const regionCode = this.value;
        const provinceSelect = document.getElementById('province_code');
        if (provinceSelect) {
            Array.from(provinceSelect.options).forEach(option => {
                if (option.value && option.dataset.region !== regionCode) {
                    option.style.display = 'none';
                } else {
                    option.style.display = 'block';
                }
            });
            provinceSelect.value = '';
        }
    });
    @endif

    // Barangays: populate Province when Region selected, City when Province selected (via API)
    @if($type === 'barangays')
    function resetBarangayCascades() {
        const provinceSelect = document.getElementById('province_code');
        const citySelect = document.getElementById('city_code');
        if (provinceSelect) {
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            provinceSelect.disabled = true;
        }
        if (citySelect) {
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            citySelect.disabled = true;
        }
    }

    document.getElementById('region_code')?.addEventListener('change', function() {
        const regionCode = this.value;
        const provinceSelect = document.getElementById('province_code');
        const citySelect = document.getElementById('city_code');
        if (!provinceSelect || !citySelect) return;
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        provinceSelect.disabled = true;
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        citySelect.disabled = true;
        if (!regionCode) return;
        provinceSelect.disabled = true;
        provinceSelect.innerHTML = '<option value="">Loading...</option>';
        axios.get('/api/address/provinces', { params: { region_code: regionCode } })
            .then(function(response) {
                provinceSelect.innerHTML = '<option value="">Select Province</option>';
                response.data.forEach(function(p) {
                    const opt = document.createElement('option');
                    opt.value = p.province_code;
                    opt.textContent = p.province_name;
                    provinceSelect.appendChild(opt);
                });
                provinceSelect.disabled = false;
            })
            .catch(function() {
                provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
                provinceSelect.disabled = false;
            });
    });

    document.getElementById('province_code')?.addEventListener('change', function() {
        const provinceCode = this.value;
        const citySelect = document.getElementById('city_code');
        if (!citySelect) return;
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        citySelect.disabled = true;
        if (!provinceCode) return;
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Loading...</option>';
        axios.get('/api/address/cities', { params: { province_code: provinceCode } })
            .then(function(response) {
                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                response.data.forEach(function(c) {
                    const opt = document.createElement('option');
                    opt.value = c.city_code;
                    opt.textContent = c.city_name;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            })
            .catch(function() {
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
                citySelect.disabled = false;
            });
    });
    @endif
</script>
@endpush
