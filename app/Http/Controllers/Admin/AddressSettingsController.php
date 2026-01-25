<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admin can access
        if (Auth::user()->privilege !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $type = $request->get('type', 'regions'); // regions, provinces, cities, barangays
        $search = $request->get('search', '');

        $data = [];
        $regions = [];
        $provinces = [];
        $cities = [];

        switch ($type) {
            case 'regions':
                $query = Region::query();
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('region_name', 'like', '%' . $search . '%')
                          ->orWhere('region_code', 'like', '%' . $search . '%')
                          ->orWhere('psgc_code', 'like', '%' . $search . '%');
                    });
                }
                $data = $query->orderBy('region_name')->paginate(20)->appends($request->query());
                break;
            case 'provinces':
                $query = Province::with('region');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('province_name', 'like', '%' . $search . '%')
                          ->orWhere('province_code', 'like', '%' . $search . '%')
                          ->orWhere('psgc_code', 'like', '%' . $search . '%')
                          ->orWhereHas('region', function($r) use ($search) {
                              $r->where('region_name', 'like', '%' . $search . '%');
                          });
                    });
                }
                $data = $query->orderBy('province_name')->paginate(20)->appends($request->query());
                $regions = Region::orderBy('region_name')->get();
                break;
            case 'cities':
                $query = City::with(['province', 'region']);
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('city_name', 'like', '%' . $search . '%')
                          ->orWhere('city_code', 'like', '%' . $search . '%')
                          ->orWhere('psgc_code', 'like', '%' . $search . '%')
                          ->orWhereHas('province', function($p) use ($search) {
                              $p->where('province_name', 'like', '%' . $search . '%');
                          })
                          ->orWhereHas('region', function($r) use ($search) {
                              $r->where('region_name', 'like', '%' . $search . '%');
                          });
                    });
                }
                $data = $query->orderBy('city_name')->paginate(20)->appends($request->query());
                $provinces = Province::orderBy('province_name')->get();
                $regions = Region::orderBy('region_name')->get();
                break;
            case 'barangays':
                $query = Barangay::with(['city', 'province', 'region']);
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('brgy_name', 'like', '%' . $search . '%')
                          ->orWhere('brgy_code', 'like', '%' . $search . '%')
                          ->orWhereHas('city', function($c) use ($search) {
                              $c->where('city_name', 'like', '%' . $search . '%');
                          })
                          ->orWhereHas('province', function($p) use ($search) {
                              $p->where('province_name', 'like', '%' . $search . '%');
                          })
                          ->orWhereHas('region', function($r) use ($search) {
                              $r->where('region_name', 'like', '%' . $search . '%');
                          });
                    });
                }
                $data = $query->orderBy('brgy_name')->paginate(20)->appends($request->query());
                $cities = City::orderBy('city_name')->get();
                $provinces = Province::orderBy('province_name')->get();
                $regions = Region::orderBy('region_name')->get();
                break;
        }

        return view('admin.address-settings.index', compact('data', 'type', 'regions', 'provinces', 'cities', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can access
        if (Auth::user()->privilege !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');

        try {
            switch ($type) {
                case 'region':
                    $validated = $request->validate([
                        'psgc_code' => 'required|string|max:20|unique:regions,psgc_code',
                        'region_name' => 'required|string|max:255',
                        'region_code' => 'required|string|max:10|unique:regions,region_code',
                    ]);
                    $item = Region::create($validated);
                    break;

                case 'province':
                    $validated = $request->validate([
                        'province_code' => 'required|string|max:20|unique:provinces,province_code',
                        'province_name' => 'required|string|max:255',
                        'psgc_code' => 'required|string|max:20',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item = Province::create($validated);
                    break;

                case 'city':
                    $validated = $request->validate([
                        'city_code' => 'required|string|max:20|unique:cities,city_code',
                        'city_name' => 'required|string|max:255',
                        'psgc_code' => 'required|string|max:20',
                        'province_code' => 'required|string|max:20|exists:provinces,province_code',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item = City::create($validated);
                    break;

                case 'barangay':
                    $validated = $request->validate([
                        'brgy_code' => 'required|string|max:20|unique:barangays,brgy_code',
                        'brgy_name' => 'required|string|max:255',
                        'city_code' => 'required|string|max:20|exists:cities,city_code',
                        'province_code' => 'required|string|max:20|exists:provinces,province_code',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item = Barangay::create($validated);
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            AuditLogger::log(
                'address.created',
                ucfirst($type) . ' created: ' . ($item->region_name ?? $item->province_name ?? $item->city_name ?? $item->brgy_name),
                Auth::user(),
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' created successfully.',
                'data' => $item
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ' . $type . '. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only admin can access
        if (Auth::user()->privilege !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');

        try {
            switch ($type) {
                case 'region':
                    $item = Region::findOrFail($id);
                    $validated = $request->validate([
                        'psgc_code' => 'required|string|max:20|unique:regions,psgc_code,' . $id,
                        'region_name' => 'required|string|max:255',
                        'region_code' => 'required|string|max:10|unique:regions,region_code,' . $id,
                    ]);
                    $item->update($validated);
                    break;

                case 'province':
                    $item = Province::findOrFail($id);
                    $validated = $request->validate([
                        'province_code' => 'required|string|max:20|unique:provinces,province_code,' . $id,
                        'province_name' => 'required|string|max:255',
                        'psgc_code' => 'required|string|max:20',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item->update($validated);
                    break;

                case 'city':
                    $item = City::findOrFail($id);
                    $validated = $request->validate([
                        'city_code' => 'required|string|max:20|unique:cities,city_code,' . $id,
                        'city_name' => 'required|string|max:255',
                        'psgc_code' => 'required|string|max:20',
                        'province_code' => 'required|string|max:20|exists:provinces,province_code',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item->update($validated);
                    break;

                case 'barangay':
                    $item = Barangay::findOrFail($id);
                    $validated = $request->validate([
                        'brgy_code' => 'required|string|max:20|unique:barangays,brgy_code,' . $id,
                        'brgy_name' => 'required|string|max:255',
                        'city_code' => 'required|string|max:20|exists:cities,city_code',
                        'province_code' => 'required|string|max:20|exists:provinces,province_code',
                        'region_code' => 'required|string|max:10|exists:regions,region_code',
                    ]);
                    $item->update($validated);
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            AuditLogger::log(
                'address.updated',
                ucfirst($type) . ' updated: ' . ($item->region_name ?? $item->province_name ?? $item->city_name ?? $item->brgy_name),
                Auth::user(),
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully.',
                'data' => $item->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ' . $type . '. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Only admin can access
        if (Auth::user()->privilege !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');

        try {
            switch ($type) {
                case 'region':
                    $item = Region::findOrFail($id);
                    $name = $item->region_name;
                    // Check if region has provinces
                    if (Province::where('region_code', $item->region_code)->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete region. It has associated provinces.'
                        ], 400);
                    }
                    $item->delete();
                    break;

                case 'province':
                    $item = Province::findOrFail($id);
                    $name = $item->province_name;
                    // Check if province has cities
                    if (City::where('province_code', $item->province_code)->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete province. It has associated cities.'
                        ], 400);
                    }
                    $item->delete();
                    break;

                case 'city':
                    $item = City::findOrFail($id);
                    $name = $item->city_name;
                    // Check if city has barangays
                    if (Barangay::where('city_code', $item->city_code)->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete city. It has associated barangays.'
                        ], 400);
                    }
                    $item->delete();
                    break;

                case 'barangay':
                    $item = Barangay::findOrFail($id);
                    $name = $item->brgy_name;
                    $item->delete();
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            AuditLogger::log(
                'address.deleted',
                ucfirst($type) . ' deleted: ' . $name,
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ' . $type . '. Please try again.'
            ], 500);
        }
    }
}
