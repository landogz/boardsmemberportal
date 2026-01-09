<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class BoardMemberController extends Controller
{
    /**
     * Display a listing of Board Member accounts
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view board members')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board members.');
        }

        // Board members are users with privilege = 'user'
        $boardMembers = User::where('privilege', 'user')
            ->with(['roles', 'governmentAgency.logo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.board-members.index', compact('boardMembers'));
    }

    /**
     * Display the Board Member account creation form
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create board members')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create board members.');
        }

        return view('admin.board-members.create');
    }

    /**
     * Store a newly created Board Member account
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create board members.'
            ], 403);
        }

        $validated = $request->validate([
            'government_agency_id' => 'required|exists:government_agencies,id',
            'representative_type' => 'required|in:Board Member,Authorized Representative',
            'pre_nominal_title' => 'required|in:Mr.,Ms.',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'post_nominal_title' => 'nullable|in:Sr.,Jr.,I,II,III,Others',
            'post_nominal_title_custom' => 'nullable|string|max:255|required_if:post_nominal_title,Others',
            'designation' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:Male,Female,Non-Binary',
            'birth_date' => 'required|date|before:today',
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
            'username' => 'required|string|max:255|unique:users',
            'mobile' => 'required|string|max:20|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate username if not provided or if it already exists
        $username = $validated['username'];
        $originalUsername = $username;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        // Handle post nominal title
        $postNominalTitle = $validated['post_nominal_title'] === 'Others' 
            ? $validated['post_nominal_title_custom'] 
            : $validated['post_nominal_title'];

        $user = User::create([
            'id' => Str::uuid(),
            'government_agency_id' => $validated['government_agency_id'],
            'representative_type' => $validated['representative_type'],
            'pre_nominal_title' => $validated['pre_nominal_title'],
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'post_nominal_title' => $postNominalTitle,
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
            'privilege' => 'user',
            'is_active' => true,
            'mobile' => $validated['mobile'],
            'landline' => $validated['landline'] ?? null,
            'status' => 'approved', // Board members are auto-approved
            'email_verified_at' => now(),
        ]);

        // Assign 'user' role to board members
        $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
        if ($userRole) {
            $user->assignRole('user');
        }

        AuditLogger::log(
            'board_member.account_created',
            'Board member account created: ' . $user->email,
            $user,
            [
                'email' => $user->email,
                'username' => $user->username,
                'created_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Board member account created successfully.',
            'redirect' => route('admin.board-members.index'),
        ]);
    }

    /**
     * Show the form for editing a Board Member account
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit board members')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit board members.');
        }

        $user = User::where('privilege', 'user')->findOrFail($id);
        return view('admin.board-members.edit', compact('user'));
    }

    /**
     * Update a Board Member account
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit board members.'
            ], 403);
        }

        $user = User::where('privilege', 'user')->findOrFail($id);

        $validated = $request->validate([
            'government_agency_id' => 'required|exists:government_agencies,id',
            'representative_type' => 'required|in:Board Member,Authorized Representative',
            'pre_nominal_title' => 'required|in:Mr.,Ms.',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'post_nominal_title' => 'nullable|in:Sr.,Jr.,I,II,III,Others',
            'post_nominal_title_custom' => 'nullable|string|max:255|required_if:post_nominal_title,Others',
            'designation' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:Male,Female,Non-Binary',
            'birth_date' => 'required|date|before:today',
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
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'mobile' => 'required|string|max:20|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        // Handle post nominal title
        $postNominalTitle = isset($validated['post_nominal_title']) && $validated['post_nominal_title'] === 'Others' 
            ? $validated['post_nominal_title_custom'] 
            : ($validated['post_nominal_title'] ?? $user->post_nominal_title);

        $updateData = [
            'government_agency_id' => $validated['government_agency_id'],
            'representative_type' => $validated['representative_type'],
            'pre_nominal_title' => $validated['pre_nominal_title'],
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'post_nominal_title' => $postNominalTitle,
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
            'username' => $validated['username'],
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
            'board_member.account_updated',
            'Board member account updated: ' . $user->email,
            $user,
            [
                'updated_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Board member account updated successfully.',
            'redirect' => route('admin.board-members.index'),
        ]);
    }

    /**
     * Remove a Board Member account
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete board members.'
            ], 403);
        }

        $user = User::where('privilege', 'user')->findOrFail($id);
        $email = $user->email;
        
        $user->delete();

        AuditLogger::log(
            'board_member.account_deleted',
            'Board member account deleted: ' . $email,
            null,
            [
                'deleted_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Board member account deleted successfully.',
        ]);
    }

    /**
     * Toggle active status of a Board Member account
     */
    public function toggleStatus($id)
    {
        if (!Auth::user()->hasPermission('edit board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit board members.'
            ], 403);
        }

        $user = User::where('privilege', 'user')->findOrFail($id);
        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        $email = $user->email;

        AuditLogger::log(
            'board_member.account_status_toggled',
            "Board member account {$status}: {$email}",
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
            'message' => "Board member account {$status} successfully.",
            'is_active' => $user->is_active ? 1 : 0,
        ]);
    }

    /**
     * Get user permissions for setup
     */
    public function getPermissions($id)
    {
        if (!Auth::user()->hasPermission('edit board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit board members.'
            ], 403);
        }

        // Clear permission cache to ensure we get fresh data from database
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get fresh user data
        $user = User::where('privilege', 'user')->findOrFail($id);
        
        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $this->groupPermissionsByCategory($allPermissions);

        // Get user's permission IDs for display
        $revokedPermissionNames = $user->revoked_permissions ?? [];
        
        // Get role permissions directly from database (bypass cache)
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
        
        // Combine role and direct permissions
        $allUserPermissionIds = array_unique(array_merge($rolePermissionIds, $directPermissionIds));
        
        // Filter out revoked permissions from the combined list
        $userPermissionIds = $allPermissions->filter(function($permission) use ($revokedPermissionNames, $allUserPermissionIds) {
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
                'name' => $user->pre_nominal_title . ' ' . $user->first_name . ' ' . ($user->middle_initial ? $user->middle_initial . '. ' : '') . $user->last_name . ($user->post_nominal_title ? ', ' . $user->post_nominal_title : ''),
                'email' => $user->email,
            ],
            'permissions' => $permissionsArray,
            'user_permissions' => array_values($userPermissionIds),
            'direct_permissions' => $directPermissionIds,
            'revoked_permissions' => $revokedPermissionNames,
        ]);
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit board members')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit board members.'
            ], 403);
        }

        $user = User::where('privilege', 'user')->with('roles')->findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Get the permission IDs from request
        $requestedPermissionIds = isset($validated['permissions']) ? array_map('intval', $validated['permissions']) : [];
        
        // Get all available permissions with their IDs and names
        $allPermissions = Permission::all()->keyBy('id');
        $allPermissionIds = $allPermissions->pluck('id')->toArray();
        
        // Get currently revoked permissions
        $currentRevokedPermissionNames = $user->revoked_permissions ?? [];
        
        // Build new revoked permissions list
        $newRevokedPermissionNames = [];
        
        foreach ($allPermissions as $permission) {
            $permissionName = $permission->name;
            $isRequested = in_array($permission->id, $requestedPermissionIds);
            
            if (!$isRequested) {
                $newRevokedPermissionNames[] = $permissionName;
            }
        }
        
        // Update revoked permissions
        $user->revoked_permissions = !empty($newRevokedPermissionNames) ? array_values(array_unique($newRevokedPermissionNames)) : null;
        $user->save();
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            'board_member.permissions_updated',
            'Updated permissions for board member account: ' . $user->email,
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
