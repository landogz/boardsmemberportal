<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

class AddressController extends Controller
{
    /**
     * Get all regions
     */
    public function regions()
    {
        $regions = Region::orderBy('region_name')->get();
        return response()->json($regions);
    }

    /**
     * Get provinces by region code
     */
    public function provinces(Request $request)
    {
        $validated = $request->validate([
            'region_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
        ]);
        $query = Province::query();
        if (!empty($validated['region_code'] ?? null)) {
            $query->where('region_code', $validated['region_code']);
        }
        $provinces = $query->orderBy('province_name')->get();
        return response()->json($provinces);
    }

    /**
     * Get cities by province code
     */
    public function cities(Request $request)
    {
        $validated = $request->validate([
            'province_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
            'region_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
        ]);
        $query = City::query();
        if (!empty($validated['province_code'] ?? null)) {
            $query->where('province_code', $validated['province_code']);
        }
        if (!empty($validated['region_code'] ?? null)) {
            $query->where('region_code', $validated['region_code']);
        }
        $cities = $query->orderBy('city_name')->get();
        return response()->json($cities);
    }

    /**
     * Get barangays by city code
     */
    public function barangays(Request $request)
    {
        $validated = $request->validate([
            'city_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
            'province_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
            'region_code' => 'nullable|string|max:32|regex:/^[a-zA-Z0-9\-_]+$/',
        ]);
        $query = Barangay::query();
        if (!empty($validated['city_code'] ?? null)) {
            $query->where('city_code', $validated['city_code']);
        }
        if (!empty($validated['province_code'] ?? null)) {
            $query->where('province_code', $validated['province_code']);
        }
        if (!empty($validated['region_code'] ?? null)) {
            $query->where('region_code', $validated['region_code']);
        }
        $barangays = $query->orderBy('brgy_name')->get();
        return response()->json($barangays);
    }
}
