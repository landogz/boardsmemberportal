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
        $query = Province::query();
        
        if ($request->has('region_code')) {
            $query->where('region_code', $request->region_code);
        }
        
        $provinces = $query->orderBy('province_name')->get();
        return response()->json($provinces);
    }

    /**
     * Get cities by province code
     */
    public function cities(Request $request)
    {
        $query = City::query();
        
        if ($request->has('province_code')) {
            $query->where('province_code', $request->province_code);
        }
        
        if ($request->has('region_code')) {
            $query->where('region_code', $request->region_code);
        }
        
        $cities = $query->orderBy('city_name')->get();
        return response()->json($cities);
    }

    /**
     * Get barangays by city code
     */
    public function barangays(Request $request)
    {
        $query = Barangay::query();
        
        if ($request->has('city_code')) {
            $query->where('city_code', $request->city_code);
        }
        
        if ($request->has('province_code')) {
            $query->where('province_code', $request->province_code);
        }
        
        if ($request->has('region_code')) {
            $query->where('region_code', $request->region_code);
        }
        
        $barangays = $query->orderBy('brgy_name')->get();
        return response()->json($barangays);
    }
}
