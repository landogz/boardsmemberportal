<?php

namespace App\Http\Controllers;

use App\Models\GovernmentAgency;
use App\Models\MediaLibrary;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GovernmentAgencyController extends Controller
{
    /**
     * Display a listing of government agencies
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view government agencies')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view government agencies.');
        }

        $agencies = GovernmentAgency::with('logo')->orderBy('name')->get();
        return view('admin.government-agencies.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new agency
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('view government agencies')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view government agencies.');
        }

        return view('admin.government-agencies.create');
    }

    /**
     * Store a newly created agency
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create government agencies.'
            ], 403);
        }

        // Get require_agency_code setting
        $settings = cache()->get('agency_settings', []);
        $requireCode = $settings['require_agency_code'] ?? false;

        $validationRules = [
            'name' => 'required|string|max:255|unique:government_agencies,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        // Make code required if setting is enabled
        if ($requireCode) {
            $validationRules['code'] = 'required|string|max:255|unique:government_agencies,code';
        } else {
            $validationRules['code'] = 'nullable|string|max:255|unique:government_agencies,code';
        }

        $request->validate($validationRules);

        // Get auto-activate setting
        $settings = cache()->get('agency_settings', []);
        $autoActivate = $settings['auto_activate_agencies'] ?? false;

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : ($autoActivate ? true : false),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                
                // Validate file size (2MB = 2048 KB)
                if ($file->getSize() > 2048 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: File size exceeds 2MB limit.',
                        'errors' => ['logo' => ['The logo file size must not exceed 2MB.']]
                    ], 422);
                }

                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Invalid file type.',
                        'errors' => ['logo' => ['The logo must be a valid image file (JPEG, PNG, JPG, GIF, or SVG).']]
                    ], 422);
                }

                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'agency-logos/' . $fileName;
                
                // Check if directory exists, create if not
                if (!Storage::disk('public')->exists('agency-logos')) {
                    Storage::disk('public')->makeDirectory('agency-logos');
                }
                
                $uploaded = Storage::disk('public')->put($filePath, file_get_contents($file));
                
                if (!$uploaded) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Unable to save file to storage.',
                        'errors' => ['logo' => ['Failed to save the logo file. Please try again.']]
                    ], 422);
                }

                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);

                if (!$media) {
                    // Clean up uploaded file if media creation fails
                    Storage::disk('public')->delete($filePath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Unable to create media record.',
                        'errors' => ['logo' => ['Failed to create media record. Please try again.']]
                    ], 422);
                }

                $data['logo_id'] = $media->id;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logo upload failed: ' . $e->getMessage(),
                    'errors' => ['logo' => ['An error occurred while uploading the logo: ' . $e->getMessage()]]
                ], 422);
            }
        }

        $agency = GovernmentAgency::create($data);

        // Audit log
        AuditLogger::log(
            'government_agency.created',
            'Created government agency: ' . $agency->name,
            $agency,
            [
                'code' => $agency->code,
                'is_active' => $agency->is_active,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Government agency created successfully',
            'agency' => $agency
        ]);
    }

    /**
     * Show the form for editing an agency
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('view government agencies')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view government agencies.');
        }

        $agency = GovernmentAgency::with('logo')->findOrFail($id);
        return view('admin.government-agencies.edit', compact('agency'));
    }

    /**
     * Update an agency
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit government agencies.'
            ], 403);
        }

        $agency = GovernmentAgency::findOrFail($id);

        // Get require_agency_code setting
        $settings = cache()->get('agency_settings', []);
        $requireCode = $settings['require_agency_code'] ?? false;

        $validationRules = [
            'name' => 'required|string|max:255|unique:government_agencies,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        // Make code required if setting is enabled
        if ($requireCode) {
            $validationRules['code'] = 'required|string|max:255|unique:government_agencies,code,' . $id;
        } else {
            $validationRules['code'] = 'nullable|string|max:255|unique:government_agencies,code,' . $id;
        }

        $request->validate($validationRules);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : $agency->is_active,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if exists
                if ($agency->logo_id) {
                    $oldMedia = MediaLibrary::find($agency->logo_id);
                    if ($oldMedia && Storage::disk('public')->exists($oldMedia->file_path)) {
                        Storage::disk('public')->delete($oldMedia->file_path);
                    }
                    if ($oldMedia) {
                        $oldMedia->delete();
                    }
                }

                $file = $request->file('logo');
                
                // Validate file size (2MB = 2048 KB)
                if ($file->getSize() > 2048 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: File size exceeds 2MB limit.',
                        'errors' => ['logo' => ['The logo file size must not exceed 2MB.']]
                    ], 422);
                }

                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Invalid file type.',
                        'errors' => ['logo' => ['The logo must be a valid image file (JPEG, PNG, JPG, GIF, or SVG).']]
                    ], 422);
                }

                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'agency-logos/' . $fileName;
                
                // Check if directory exists, create if not
                if (!Storage::disk('public')->exists('agency-logos')) {
                    Storage::disk('public')->makeDirectory('agency-logos');
                }
                
                $uploaded = Storage::disk('public')->put($filePath, file_get_contents($file));
                
                if (!$uploaded) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Unable to save file to storage.',
                        'errors' => ['logo' => ['Failed to save the logo file. Please try again.']]
                    ], 422);
                }

                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);

                if (!$media) {
                    // Clean up uploaded file if media creation fails
                    Storage::disk('public')->delete($filePath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Logo upload failed: Unable to create media record.',
                        'errors' => ['logo' => ['Failed to create media record. Please try again.']]
                    ], 422);
                }

                $data['logo_id'] = $media->id;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logo upload failed: ' . $e->getMessage(),
                    'errors' => ['logo' => ['An error occurred while uploading the logo: ' . $e->getMessage()]]
                ], 422);
            }
        }

        $agency->update($data);

        // Audit log
        AuditLogger::log(
            'government_agency.updated',
            'Updated government agency: ' . $agency->name,
            $agency,
            [
                'code' => $agency->code,
                'is_active' => $agency->is_active,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Government agency updated successfully',
            'agency' => $agency
        ]);
    }

    /**
     * Delete an agency
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete government agencies.'
            ], 403);
        }

        $agency = GovernmentAgency::findOrFail($id);

        // Check if agency has users
        if ($agency->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete agency. There are users associated with this agency.'
            ], 422);
        }

        // Delete logo if exists
        if ($agency->logo_id) {
            $logo = MediaLibrary::find($agency->logo_id);
            if ($logo && Storage::disk('public')->exists($logo->file_path)) {
                Storage::disk('public')->delete($logo->file_path);
            }
            if ($logo) {
                $logo->delete();
            }
        }

        $agency->delete();

        AuditLogger::log(
            'government_agency.deleted',
            'Deleted government agency: ' . $agency->name,
            $agency,
            [
                'code' => $agency->code,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Government agency deleted successfully'
        ]);
    }

    /**
     * Toggle agency active status
     */
    public function toggleStatus($id)
    {
        if (!Auth::user()->hasPermission('edit government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit government agencies.'
            ], 403);
        }

        $agency = GovernmentAgency::findOrFail($id);
        $agency->is_active = !$agency->is_active;
        $agency->save();

        AuditLogger::log(
            'government_agency.toggled_status',
            'Toggled agency status: ' . $agency->name,
            $agency,
            [
                'is_active' => $agency->is_active,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Agency status updated successfully',
            'is_active' => $agency->is_active
        ]);
    }

    /**
     * Show agency settings page
     */
    public function settings()
    {
        if (!Auth::user()->hasPermission('view government agencies')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view government agencies.');
        }

        // Get current settings from cache or default values
        $cachedSettings = cache()->get('agency_settings', []);
        $settings = [
            'auto_activate_agencies' => $cachedSettings['auto_activate_agencies'] ?? false,
            'require_agency_code' => $cachedSettings['require_agency_code'] ?? false,
        ];

        return view('admin.government-agencies.settings', compact('settings'));
    }

    /**
     * Save agency settings
     */
    public function saveSettings(Request $request)
    {
        if (!Auth::user()->hasPermission('edit government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit government agencies.'
            ], 403);
        }

        $request->validate([
            'auto_activate_agencies' => 'boolean',
            'require_agency_code' => 'boolean',
        ]);

        // Store settings in config file or cache
        // For now, we'll use cache (you can later move to a settings table)
        cache()->forever('agency_settings', [
            'auto_activate_agencies' => $request->has('auto_activate_agencies') ? $request->auto_activate_agencies : false,
            'require_agency_code' => $request->has('require_agency_code') ? $request->require_agency_code : false,
        ]);

        AuditLogger::log(
            'government_agency.settings_saved',
            'Updated government agency global settings',
            null,
            [
                'auto_activate_agencies' => (bool) ($request->auto_activate_agencies ?? false),
                'require_agency_code' => (bool) ($request->require_agency_code ?? false),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully'
        ]);
    }

    /**
     * Bulk activate all agencies
     */
    public function bulkActivate()
    {
        if (!Auth::user()->hasPermission('edit government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit government agencies.'
            ], 403);
        }

        $count = GovernmentAgency::where('is_active', false)->update(['is_active' => true]);

        AuditLogger::log(
            'government_agency.bulk_activated',
            "Bulk activated {$count} agencies",
            null,
            ['count' => $count]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully activated {$count} agencies",
            'count' => $count
        ]);
    }

    /**
     * Bulk deactivate all agencies
     */
    public function bulkDeactivate()
    {
        if (!Auth::user()->hasPermission('edit government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit government agencies.'
            ], 403);
        }

        $count = GovernmentAgency::where('is_active', true)->update(['is_active' => false]);

        AuditLogger::log(
            'government_agency.bulk_deactivated',
            "Bulk deactivated {$count} agencies",
            null,
            ['count' => $count]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully deactivated {$count} agencies",
            'count' => $count
        ]);
    }

    /**
     * Bulk delete selected agencies
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->hasPermission('delete government agencies')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete government agencies.'
            ], 403);
        }

        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No agencies selected.'
            ], 422);
        }

        $agencies = GovernmentAgency::whereIn('id', $ids)->get();

        // Check for agencies with users
        $blocked = [];
        foreach ($agencies as $agency) {
            if ($agency->users()->count() > 0) {
                $blocked[] = $agency->name;
            }
        }

        if (!empty($blocked)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the following agencies because they have users: ' . implode(', ', $blocked)
            ], 422);
        }

        // Delete logos and agencies
        foreach ($agencies as $agency) {
            if ($agency->logo_id) {
                $logo = MediaLibrary::find($agency->logo_id);
                if ($logo && Storage::disk('public')->exists($logo->file_path)) {
                    Storage::disk('public')->delete($logo->file_path);
                }
                if ($logo) {
                    $logo->delete();
                }
            }

            AuditLogger::log(
                'government_agency.deleted',
                'Deleted government agency (bulk): ' . $agency->name,
                $agency,
                [
                    'code' => $agency->code,
                ]
            );

            $agency->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected agencies deleted successfully'
        ]);
    }
}

