<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserDeactivatedEmail;
use App\Mail\ConsecAccountCreatedEmail;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class CONSECController extends Controller
{
    /**
     * Display a listing of CONSEC accounts
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view consec accounts')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view CONSEC accounts.');
        }

        // CONSEC accounts are users with privilege = 'consec'
        $consecAccounts = User::where('privilege', 'consec')
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.consec.index', compact('consecAccounts'));
    }

    /**
     * Display the CONSEC account creation form
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create consec accounts')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create CONSEC accounts.');
        }

        return view('admin.consec.create');
    }

    /**
     * Store a newly created CONSEC account
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create CONSEC accounts.'
            ], 403);
        }

        $validated = $request->validate([
            'pre_nominal_title' => 'required|in:Mr.,Ms.,Dr.,Atty.,Engr.,Secretary,Undersecretary,Assistant Secretary,Director General,Executive Director,Attorney,Others',
            'pre_nominal_title_custom' => 'nullable|string|max:255|required_if:pre_nominal_title,Others',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'post_nominal_title' => 'nullable|in:CESO I,CESO II,CESO III,CESO IV,CESO V,CESO VI,Others',
            'post_nominal_title_custom' => 'nullable|string|max:255|required_if:post_nominal_title,Others',
            'designation' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:Lesbian,Gay,Bisexual,Transgender,Queer,Intersex,Non-binary,Cisgender,Prefer not to say',
            'birth_date' => 'required|date|before:today|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'office_region' => 'required|string|max:255',
            'office_province' => 'required|string|max:255',
            'office_city_municipality' => 'required|string|max:255',
            'office_barangay' => 'required|string|max:255',
            'office_building_no' => 'nullable|string|max:255',
            'office_house_no' => 'nullable|string|max:255',
            'office_street_name' => 'nullable|string|max:255',
            'office_purok' => 'nullable|string|max:255',
            'office_sitio' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:20|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:20',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('The password must contain at least one capital letter.');
                    }
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('The password must contain at least one small letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('The password must contain at least one number.');
                    }
                    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                        $fail('The password must contain at least one special character (e.g. ! @ # $ % & * ( ) - _ = + . , ).');
                    }
                },
            ],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'email.unique' => 'This email is already registered. Please use a different email address.',
            'birth_date.before_or_equal' => 'The person must be at least 18 years old.',
        ]);

        // Username format: firstname.lastname (lowercase, alphanumeric only; unique)
        $username = User::usernameFromName($validated['first_name'], $validated['last_name']);

        // Resolve final post nominal title (standard value or custom "Others")
        $postNominalTitle = $validated['post_nominal_title'] === 'Others'
            ? $validated['post_nominal_title_custom']
            : $validated['post_nominal_title'];

        $preNominalTitle = $validated['pre_nominal_title'] === 'Others'
            ? $validated['pre_nominal_title_custom']
            : $validated['pre_nominal_title'];

        $user = User::create([
            'id' => Str::uuid(),
            'government_agency_id' => null, // CONSEC accounts don't have government agency
            'pre_nominal_title' => $preNominalTitle,
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'extension_name' => $validated['extension_name'] ?? null,
            'post_nominal_title' => $postNominalTitle ?? null,
            'designation' => $validated['designation'],
            'sex' => $validated['sex'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'office_region' => $validated['office_region'],
            'office_province' => $validated['office_province'],
            'office_city_municipality' => $validated['office_city_municipality'],
            'office_barangay' => $validated['office_barangay'],
            'office_building_no' => $validated['office_building_no'] ?? null,
            'office_house_no' => $validated['office_house_no'] ?? null,
            'office_street_name' => $validated['office_street_name'] ?? null,
            'office_purok' => $validated['office_purok'] ?? null,
            'office_sitio' => $validated['office_sitio'] ?? null,
            'email' => $validated['email'],
            'username' => $username,
            'username_edited' => false,
            'password_hash' => Hash::make($validated['password']),
            'privilege' => 'consec',
            'is_active' => true,
            'mobile' => $validated['mobile'],
            'landline' => $validated['landline'] ?? null,
            'status' => 'approved', // CONSEC accounts are auto-approved
            'email_verified_at' => now(),
        ]);

        // Assign 'consec' role to CONSEC users
        $consecRole = \Spatie\Permission\Models\Role::where('name', 'consec')->first();
        if ($consecRole) {
            $user->assignRole('consec');
        } else {
            // Fallback to user role if consec role doesn't exist
            $user->assignRole('user');
        }

        // Grant Manage Agenda Requests to new CONSEC accounts
        $manageAgendaPermission = Permission::where('name', 'manage agenda requests')->first();
        if ($manageAgendaPermission) {
            $user->givePermissionTo('manage agenda requests');
        }

        AuditLogger::log(
            'consec.account_created',
            'CONSEC account created: ' . $user->email,
            $user,
            [
                'email' => $user->email,
                'username' => $user->username,
                'created_by' => Auth::user()->email,
            ]
        );

        // Notify the CONSEC user that their account has been created
        try {
            Mail::to($user->email)->send(new ConsecAccountCreatedEmail($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send CONSEC account created email to user ' . $user->id . ': ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'CONSEC account created successfully.',
            'redirect' => route('admin.consec.index'),
        ]);
    }

    /**
     * Show the form for editing a CONSEC account
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit consec accounts')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit CONSEC accounts.');
        }

        $user = User::where('privilege', 'consec')->findOrFail($id);
        return view('admin.consec.edit', compact('user'));
    }

    /**
     * Update a CONSEC account
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit CONSEC accounts.'
            ], 403);
        }

        $user = User::where('privilege', 'consec')->findOrFail($id);

        $validated = $request->validate([
            'pre_nominal_title' => 'required|in:Mr.,Ms.,Dr.,Atty.,Engr.,Secretary,Undersecretary,Assistant Secretary,Director General,Executive Director,Attorney,Others',
            'pre_nominal_title_custom' => 'nullable|string|max:255|required_if:pre_nominal_title,Others',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'post_nominal_title' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:Lesbian,Gay,Bisexual,Transgender,Queer,Intersex,Non-binary,Cisgender,Prefer not to say',
            'birth_date' => 'required|date|before:today|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'office_region' => 'required|string|max:255',
            'office_province' => 'required|string|max:255',
            'office_city_municipality' => 'required|string|max:255',
            'office_barangay' => 'required|string|max:255',
            'office_building_no' => 'nullable|string|max:255',
            'office_house_no' => 'nullable|string|max:255',
            'office_street_name' => 'nullable|string|max:255',
            'office_purok' => 'nullable|string|max:255',
            'office_sitio' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'mobile' => 'required|string|max:20|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ], [
            'birth_date.before_or_equal' => 'The person must be at least 18 years old.',
        ]);

        $preNominalTitle = $validated['pre_nominal_title'] === 'Others'
            ? $validated['pre_nominal_title_custom']
            : $validated['pre_nominal_title'];

        $updateData = [
            'pre_nominal_title' => $preNominalTitle,
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'extension_name' => $validated['extension_name'] ?? null,
            'post_nominal_title' => $validated['post_nominal_title'] ?? null,
            'designation' => $validated['designation'],
            'sex' => $validated['sex'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'office_region' => $validated['office_region'],
            'office_province' => $validated['office_province'],
            'office_city_municipality' => $validated['office_city_municipality'],
            'office_barangay' => $validated['office_barangay'],
            'office_building_no' => $validated['office_building_no'] ?? null,
            'office_house_no' => $validated['office_house_no'] ?? null,
            'office_street_name' => $validated['office_street_name'] ?? null,
            'office_purok' => $validated['office_purok'] ?? null,
            'office_sitio' => $validated['office_sitio'] ?? null,
            'email' => $validated['email'],
            'username' => User::usernameFromName($validated['first_name'], $validated['last_name'], $id),
            'username_edited' => false,
            'mobile' => $validated['mobile'],
            'landline' => $validated['landline'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $user->is_active,
        ];

        // Update password only if provided
        if (!empty($validated['password'])) {
            $updateData['password_hash'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLogger::log(
            'consec.account_updated',
            'CONSEC account updated: ' . $user->email,
            $user,
            [
                'updated_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'CONSEC account updated successfully.',
            'redirect' => route('admin.consec.index'),
        ]);
    }

    /**
     * Remove a CONSEC account
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete CONSEC accounts.'
            ], 403);
        }

        $user = User::where('privilege', 'consec')->findOrFail($id);
        $email = $user->email;
        
        $user->delete();

        AuditLogger::log(
            'consec.account_deleted',
            'CONSEC account deleted: ' . $email,
            null,
            [
                'deleted_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'CONSEC account deleted successfully.',
        ]);
    }

    /**
     * Toggle active status of a CONSEC account
     */
    public function toggleStatus($id)
    {
        if (!Auth::user()->hasPermission('edit consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit CONSEC accounts.'
            ], 403);
        }

        $user = User::where('privilege', 'consec')->findOrFail($id);
        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        $email = $user->email;

        if (!$user->is_active) {
            // When deactivating a CONSEC account, immediately terminate all active sessions
            $sessionsDestroyed = DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            $user->is_online = false;
            $user->current_session_id = null;
            $user->save();

            try {
                Mail::to($user->email)->send(new UserDeactivatedEmail($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send deactivation email to CONSEC user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        AuditLogger::log(
            'consec.account_status_toggled',
            "CONSEC account {$status}: {$email}",
            $user,
            [
                'changed_by' => Auth::user()->email,
                'old_status' => $oldStatus ? 'active' : 'inactive',
                'new_status' => $user->is_active ? 'active' : 'inactive',
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "CONSEC account {$status} successfully.",
            'is_active' => $user->is_active ? 1 : 0,
        ]);
    }

    /**
     * Get user permissions for setup
     */
    public function getPermissions($id)
    {
        if (!Auth::user()->hasPermission('edit consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit CONSEC accounts.'
            ], 403);
        }

        // Clear permission cache to ensure we get fresh data from database
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get fresh user data
        $user = User::where('privilege', 'consec')->findOrFail($id);
        
        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $this->groupPermissionsByCategory($allPermissions);

        // Get user's permission IDs for display
        $revokedPermissionNames = $user->revoked_permissions ?? [];
        
        // Get role permissions directly from database (bypass cache)
        // Query role permissions fresh from the database
        $roleIds = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->pluck('role_id')
            ->toArray();
        
        $rolePermissionIds = [];
        if (!empty($roleIds)) {
            $rolePermissionIds = DB::table('role_has_permissions')
                ->whereIn('role_id', $roleIds)
                ->pluck('permission_id')
                ->toArray();
        }
        
        // Get direct permissions directly from database (bypass cache)
        $directPermissionIds = DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->pluck('permission_id')
            ->toArray();
        
        // Combine role and direct permissions (union, not all permissions)
        $allUserPermissionIds = array_unique(array_merge($rolePermissionIds, $directPermissionIds));
        
        // Filter out revoked permissions from the combined list
        $userPermissionIds = $allPermissions->filter(function($permission) use ($revokedPermissionNames, $allUserPermissionIds) {
            // Include if: (in role OR direct permissions) AND not revoked
            return in_array($permission->id, $allUserPermissionIds) && !in_array($permission->name, $revokedPermissionNames);
        })->pluck('id')->toArray();

        // Convert grouped permissions to array format for JSON
        $permissionsArray = [];
        foreach ($groupedPermissions as $category => $group) {
            $permissionsArray[$category] = [
                'icon' => $group['icon'],
                'permissions' => array_map(function($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }, $group['permissions'])
            ];
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
            ],
            'permissions' => $permissionsArray,
            'user_permissions' => array_values($userPermissionIds), // All permissions (roles + direct) for display, excluding revoked
            'direct_permissions' => $directPermissionIds, // Direct permissions only
            'revoked_permissions' => $revokedPermissionNames, // Revoked permissions (names)
        ]);
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit consec accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit CONSEC accounts.'
            ], 403);
        }

        $user = User::where('privilege', 'consec')->with('roles')->findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Get the permission IDs from request (convert to integers)
        $requestedPermissionIds = isset($validated['permissions']) ? array_map('intval', $validated['permissions']) : [];
        
        // Get all available permissions with their IDs and names
        $allPermissions = Permission::all()->keyBy('id');
        $allPermissionIds = $allPermissions->pluck('id')->toArray();
        
        // Get currently revoked permissions (stored as permission names)
        $currentRevokedPermissionNames = $user->revoked_permissions ?? [];
        
        // Get names of requested permissions
        $requestedPermissionNames = $allPermissions->whereIn('id', $requestedPermissionIds)->pluck('name')->toArray();
        
        // Build new revoked permissions list:
        // - If permission is NOT in requested list, add to revoked (if user has consec role)
        // - If permission IS in requested list but was previously revoked, remove from revoked
        $newRevokedPermissionNames = [];
        
        foreach ($allPermissions as $permission) {
            $permissionName = $permission->name;
            $isRequested = in_array($permission->id, $requestedPermissionIds);
            $wasRevoked = in_array($permissionName, $currentRevokedPermissionNames);
            
            if (!$isRequested) {
                // Permission is not checked - add to revoked list
                $newRevokedPermissionNames[] = $permissionName;
            }
            // If permission is checked, don't add to revoked (even if it was previously revoked, we're unrevoking it)
        }
        
        // Update revoked permissions: store names of permissions that are NOT checked
        $user->revoked_permissions = !empty($newRevokedPermissionNames) ? array_values(array_unique($newRevokedPermissionNames)) : null;
        $user->save();

        // Sync direct permissions to exactly match the checked permissions in the UI.
        // This ensures that checking a box really grants the permission (even if the role didn't have it),
        // and unchecking revokes it (combined with revoked_permissions overriding role-based grants).
        $user->syncPermissions($requestedPermissionIds);

        // Clear permission cache to ensure changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            'consec.permissions_updated',
            'Updated permissions for CONSEC account: ' . $user->email,
            $user,
            [
                'revoked_permissions' => $newRevokedPermissionNames ?? [],
                'requested_permissions_count' => count($requestedPermissionIds),
                'updated_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.',
        ]);
    }

    /**
     * Group permissions by category (same logic as RoleController)
     */
    /**
     * Group permissions by category based on their names
     */
    private function groupPermissionsByCategory($permissions)
    {
        $groups = [
            'User Management' => ['icon' => 'fas fa-users', 'permissions' => []],
            'Board Member Management' => ['icon' => 'fas fa-user-tie', 'permissions' => []],
            'Pending Registrations' => ['icon' => 'fas fa-clock', 'permissions' => []],
            'Role Management' => ['icon' => 'fas fa-shield-alt', 'permissions' => []],
            'Permission Management' => ['icon' => 'fas fa-key', 'permissions' => []],
            'Board Resolutions' => ['icon' => 'fas fa-gavel', 'permissions' => []],
            'Board Regulations' => ['icon' => 'fas fa-balance-scale', 'permissions' => []],
            'Referendum' => ['icon' => 'fas fa-vote-yea', 'permissions' => []],
            'Government Agencies' => ['icon' => 'fas fa-building', 'permissions' => []],
            'Media Library' => ['icon' => 'fas fa-photo-video', 'permissions' => []],
            'Announcements' => ['icon' => 'fas fa-bullhorn', 'permissions' => []],
            'Notices' => ['icon' => 'fas fa-sticky-note', 'permissions' => []],
            'Calendar Events' => ['icon' => 'fas fa-calendar-alt', 'permissions' => []],
            'Audit Logs' => ['icon' => 'fas fa-history', 'permissions' => []],
            'CONSEC Account Management' => ['icon' => 'fas fa-users-cog', 'permissions' => []],
            'Attendance Confirmation' => ['icon' => 'fas fa-check-circle', 'permissions' => []],
            'Reference Materials' => ['icon' => 'fas fa-book', 'permissions' => []],
            'Request for Inclusion in the Agenda' => ['icon' => 'fas fa-clipboard-list', 'permissions' => []],
            'Report Generation' => ['icon' => 'fas fa-chart-bar', 'permissions' => []],
        ];

        foreach ($permissions as $permission) {
            $name = strtolower($permission->name);
            
            // Check in order of specificity (most specific first)
            // Check for board member FIRST (before user check, since "board member" contains "user")
            if (strpos($name, 'board member') !== false) {
                $groups['Board Member Management']['permissions'][] = $permission;
            } elseif (strpos($name, 'government') !== false) {
                // All government agencies permissions in one category
                $groups['Government Agencies']['permissions'][] = $permission;
            } elseif (strpos($name, 'consec') !== false) {
                $groups['CONSEC Account Management']['permissions'][] = $permission;
            } elseif (strpos($name, 'board resolution') !== false) {
                $groups['Board Resolutions']['permissions'][] = $permission;
            } elseif (strpos($name, 'board regulation') !== false) {
                $groups['Board Regulations']['permissions'][] = $permission;
            } elseif (strpos($name, 'referendum') !== false) {
                $groups['Referendum']['permissions'][] = $permission;
            } elseif (strpos($name, 'role') !== false) {
                $groups['Role Management']['permissions'][] = $permission;
            } elseif (strpos($name, 'permission') !== false) {
                $groups['Permission Management']['permissions'][] = $permission;
            } elseif (strpos($name, 'media') !== false) {
                $groups['Media Library']['permissions'][] = $permission;
            } elseif (strpos($name, 'announcement') !== false) {
                $groups['Announcements']['permissions'][] = $permission;
            } elseif (strpos($name, 'notice') !== false) {
                $groups['Notices']['permissions'][] = $permission;
            } elseif (strpos($name, 'calendar') !== false) {
                $groups['Calendar Events']['permissions'][] = $permission;
            } elseif (strpos($name, 'audit') !== false) {
                $groups['Audit Logs']['permissions'][] = $permission;
            } elseif (strpos($name, 'pending registration') !== false) {
                $groups['Pending Registrations']['permissions'][] = $permission;
            } elseif (strpos($name, 'attendance') !== false) {
                $groups['Attendance Confirmation']['permissions'][] = $permission;
            } elseif (strpos($name, 'reference') !== false) {
                $groups['Reference Materials']['permissions'][] = $permission;
            } elseif (strpos($name, 'agenda') !== false) {
                $groups['Request for Inclusion in the Agenda']['permissions'][] = $permission;
            } elseif (strpos($name, 'report') !== false) {
                $groups['Report Generation']['permissions'][] = $permission;
            } elseif (strpos($name, 'user') !== false) {
                $groups['User Management']['permissions'][] = $permission;
            } else {
                // Default to User Management if no match
                $groups['User Management']['permissions'][] = $permission;
            }
        }

        // Sort permissions within each group (View, Create, Edit, Delete, Manage)
        foreach ($groups as $category => &$group) {
            if (!empty($group['permissions'])) {
                usort($group['permissions'], function($a, $b) {
                    $order = ['view' => 1, 'create' => 2, 'edit' => 3, 'delete' => 4, 'manage' => 5];
                    $nameA = strtolower($a->name);
                    $nameB = strtolower($b->name);
                    
                    $priorityA = 99;
                    $priorityB = 99;
                    
                    foreach ($order as $key => $value) {
                        if (strpos($nameA, $key) !== false) {
                            $priorityA = $value;
                            break;
                        }
                    }
                    
                    foreach ($order as $key => $value) {
                        if (strpos($nameB, $key) !== false) {
                            $priorityB = $value;
                            break;
                        }
                    }
                    
                    if ($priorityA !== $priorityB) {
                        return $priorityA - $priorityB;
                    }
                    
                    return strcmp($nameA, $nameB);
                });
            }
        }
        unset($group);

        // Remove empty groups
        return array_filter($groups, function($group) {
            return !empty($group['permissions']);
        });
    }
}

