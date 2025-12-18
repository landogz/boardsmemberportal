@extends('admin.layout')

@section('title', 'User Profile')

@php
    $headerTitle = 'User Profile';
    $headerSubtitle = 'View user details';
    $headerActions = [];
@endphp

@section('content')
    <div class="space-y-4 sm:space-y-6 p-4 sm:p-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden border-2 flex-shrink-0" style="border-color:#055498;">
                        @php
                            $profileMedia = $user->profile_picture ? \App\Models\MediaLibrary::find($user->profile_picture) : null;
                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://via.placeholder.com/150?text=User';
                        @endphp
                        <img src="{{ $profileUrl }}" alt="Profile Picture" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 break-words">
                            {{ trim(($user->pre_nominal_title ?? '') . ' ' . $user->first_name . ' ' . ($user->middle_initial ? $user->middle_initial . '.' : '') . ' ' . $user->last_name . ' ' . ($user->post_nominal_title ?? '')) }}
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-600">
                            {{ $user->designation ?? 'Board Member' }}
                        </p>
                        <p class="mt-1 text-xs inline-flex items-center px-2 py-1 rounded-full" style="background-color: rgba(5,84,152,0.08); color:#055498;">
                            <i class="fas fa-id-badge mr-1"></i> {{ $user->username ?? 'No username' }}
                        </p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Privilege</p>
                    <p class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        {{ $user->privilege === 'admin' ? 'text-white' : 'text-gray-800' }}"
                        style="background-color: {{ $user->privilege === 'admin' ? '#CE2028' : '#FBD116' }};">
                        <i class="fas fa-user-shield mr-1"></i>
                        {{ ucfirst($user->privilege ?? 'member') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 p-4 sm:p-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
                            <i class="fas fa-user text-[#055498] mr-2"></i>
                            <h2 class="text-sm font-semibold text-gray-800">Personal Information</h2>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Full Name</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->first_name }} {{ $user->middle_initial }} {{ $user->last_name }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Email</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Mobile</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->mobile ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Landline</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->landline ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Sex</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->sex ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Gender</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->gender ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Birth Date</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('F d, Y') : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
                            <i class="fas fa-building text-[#055498] mr-2"></i>
                            <h2 class="text-sm font-semibold text-gray-800">Office Address</h2>
                        </div>
                        @php
                            // Map PSGC codes to human-readable names using the JSON files at public/address
                            $regionName = $user->office_region;
                            $provinceName = $user->office_province;
                            $cityName = $user->office_city_municipality;
                            $barangayName = $user->office_barangay;

                            try {
                                $regionsJson = @file_get_contents(public_path('address/region.json'));
                                $provincesJson = @file_get_contents(public_path('address/province.json'));
                                $citiesJson = @file_get_contents(public_path('address/city.json'));
                                $barangaysJson = @file_get_contents(public_path('address/barangay.json'));

                                $regionsData = $regionsJson ? json_decode($regionsJson, true) : [];
                                $provincesData = $provincesJson ? json_decode($provincesJson, true) : [];
                                $citiesData = $citiesJson ? json_decode($citiesJson, true) : [];
                                $barangaysData = $barangaysJson ? json_decode($barangaysJson, true) : [];

                                if ($user->office_region && is_array($regionsData)) {
                                    $match = collect($regionsData)->firstWhere('region_code', $user->office_region);
                                    if ($match) {
                                        $regionName = $match['region_name'] ?? $user->office_region;
                                    }
                                }

                                if ($user->office_province && is_array($provincesData)) {
                                    $match = collect($provincesData)->firstWhere('province_code', $user->office_province);
                                    if ($match) {
                                        $provinceName = $match['province_name'] ?? $user->office_province;
                                    }
                                }

                                if ($user->office_city_municipality && is_array($citiesData)) {
                                    $match = collect($citiesData)->firstWhere('city_code', $user->office_city_municipality);
                                    if ($match) {
                                        $cityName = $match['city_name'] ?? $user->office_city_municipality;
                                    }
                                }

                                if ($user->office_barangay && is_array($barangaysData)) {
                                    $match = collect($barangaysData)->firstWhere('brgy_code', $user->office_barangay);
                                    if ($match) {
                                        $barangayName = $match['brgy_name'] ?? $user->office_barangay;
                                    }
                                }
                            } catch (\Throwable $e) {
                                // Fail silently and fall back to raw codes
                            }
                        @endphp
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="md:col-span-2">
                                <p class="text-gray-500 text-xs uppercase">Address Line</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->office_building_no }} {{ $user->office_house_no }} {{ $user->office_street_name }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Purok / Sitio</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->office_purok }} {{ $user->office_sitio }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Region</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $regionName ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Province</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $provinceName ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">City / Municipality</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $cityName ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Barangay</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $barangayName ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
                            <i class="fas fa-building-columns text-[#055498] mr-2"></i>
                            <h2 class="text-sm font-semibold text-gray-800">Government Agency</h2>
                        </div>
                        <div class="p-4 text-sm space-y-3">
                            @php
                                $agency = $user->government_agency_id
                                    ? \App\Models\GovernmentAgency::with('logo')->find($user->government_agency_id)
                                    : null;
                            @endphp

                            @if($agency)
                                <div class="flex items-center space-x-3">
                                    @if($agency->logo)
                                        <img src="{{ asset('storage/' . $agency->logo->file_path) }}"
                                             alt="{{ $agency->name }}"
                                             class="w-10 h-10 object-contain rounded bg-white border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 flex items-center justify-center rounded bg-gray-100 border border-gray-200 text-xs font-semibold text-gray-600">
                                            {{ \Illuminate\Support\Str::limit($agency->name, 2, '') }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-gray-900 font-semibold">{{ $agency->name }}</p>
                                        <p class="text-xs text-gray-500">Code: {{ $agency->code ?? '—' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500">No agency assigned.</p>
                            @endif
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
                            <i class="fas fa-info-circle text-[#055498] mr-2"></i>
                            <h2 class="text-sm font-semibold text-gray-800">Account Information</h2>
                        </div>
                        <div class="p-4 text-sm space-y-3">
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Username</p>
                                <p class="mt-1 text-gray-900 font-medium">{{ $user->username ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Status</p>
                                <p class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $user->status === 'active' ? 'text-white' : 'text-gray-800' }}"
                                   style="background-color: {{ $user->status === 'active' ? '#055498' : '#FBD116' }};">
                                    <i class="fas fa-circle mr-1 text-[8px]"></i>
                                    {{ ucfirst($user->status ?? 'inactive') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Registered At</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->created_at ? $user->created_at->format('F d, Y h:i A') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase">Last Updated</p>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $user->updated_at ? $user->updated_at->format('F d, Y h:i A') : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


