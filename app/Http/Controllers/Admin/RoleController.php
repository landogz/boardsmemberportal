<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        // Permission check will be done manually in each method
        // $this->middleware('permission:manage roles');
    }

    /**
     * Display a listing of roles
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view roles')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view roles.');
        }

        // Get all roles except admin (admin has automatic access to all permissions)
        $roles = Role::with(['permissions', 'users' => function($query) {
            $query->with('profilePictureMedia');
        }])
            ->where('name', '!=', 'admin')
            ->orderBy('name')
            ->get();

        // Get all permissions and group them by category
        // Note: "manage" permissions are filtered in the view, except for "Request for Inclusion in the Agenda" and "Media Library" categories
        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $this->groupPermissionsByCategory($allPermissions);

        return view('admin.roles.index', compact('roles', 'groupedPermissions', 'allPermissions'));
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

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('view roles')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view roles.');
        }

        $permissions = Permission::orderBy('name')->get()->filter(function($permission) {
            return !str_contains(strtolower($permission->name), 'manage ');
        });
        $groupedPermissions = $this->groupPermissionsByCategory($permissions);
        return view('admin.roles.create', compact('permissions', 'groupedPermissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create roles.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        // Clear permission cache to ensure changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            'role.created',
            'Created role: ' . $role->name,
            $role,
            ['permissions_count' => $role->permissions->count()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'redirect' => route('admin.roles.index'),
        ]);
    }

    /**
     * Show the form for editing a role
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('view roles')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view roles.');
        }

        $role = Role::with('permissions')->findOrFail($id);
        
        // Prevent editing admin role
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The admin role cannot be edited.');
        }

        $permissions = Permission::orderBy('name')->get()->filter(function($permission) {
            return !str_contains(strtolower($permission->name), 'manage ');
        });
        $groupedPermissions = $this->groupPermissionsByCategory($permissions);
        return view('admin.roles.edit', compact('role', 'permissions', 'groupedPermissions'));
    }

    /**
     * Update a role
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit roles.'
            ], 403);
        }

        $role = Role::findOrFail($id);
        
        // Prevent updating admin role
        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'The admin role cannot be modified.'
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        // Get current permissions before sync (reload to get fresh data)
        $role->load('permissions');
        $currentPermissionIds = $role->permissions->pluck('id')->toArray();
        
        if (isset($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $newPermissionIds = $permissions->pluck('id')->toArray();
            $role->syncPermissions($permissions);
        } else {
            $newPermissionIds = [];
            $role->syncPermissions([]);
        }

        // Update users' revoked_permissions based on permission changes
        $removedPermissionIds = array_diff($currentPermissionIds, $newPermissionIds);
        $addedPermissionIds = array_diff($newPermissionIds, $currentPermissionIds);
        
        foreach ($removedPermissionIds as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission) {
                $this->updateUsersRevokedPermissions($role, $permission, true);
            }
        }
        
        foreach ($addedPermissionIds as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission) {
                $this->updateUsersRevokedPermissions($role, $permission, false);
            }
        }

        // Clear permission cache to ensure changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            'role.updated',
            'Updated role: ' . $role->name,
            $role,
            ['permissions_count' => $role->permissions->count()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'redirect' => route('admin.roles.index'),
        ]);
    }

    /**
     * Remove a role
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete roles.'
            ], 403);
        }

        $role = Role::findOrFail($id);

        // Prevent deletion of admin role (it has automatic access to all)
        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'The admin role cannot be deleted.'
            ], 422);
        }

        // Prevent deletion of user role
        if ($role->name === 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default user role.'
            ], 422);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role. There are users assigned to this role.'
            ], 422);
        }

        $roleName = $role->name;
        $role->delete();

        AuditLogger::log(
            'role.deleted',
            'Deleted role: ' . $roleName
        );

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }

    /**
     * Update role permissions via AJAX
     */
    public function updatePermissions(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit roles.'
            ], 403);
        }

        $role = Role::findOrFail($id);
        
        // Prevent updating admin role
        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'The admin role cannot be modified.'
            ], 422);
        }

        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'granted' => 'required|boolean',
        ]);

        $permission = Permission::findOrFail($validated['permission_id']);

        if ($validated['granted']) {
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
                
                // If permission was granted to role, remove it from revoked_permissions for all users with this role
                $this->updateUsersRevokedPermissions($role, $permission, false);
            }
        } else {
            if ($role->hasPermissionTo($permission)) {
                $role->revokePermissionTo($permission);
                
                // If permission was revoked from role, add it to revoked_permissions for all users with this role
                $this->updateUsersRevokedPermissions($role, $permission, true);
            }
        }

        // Clear permission cache to ensure changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            'role.permission_updated',
            'Updated permission for role: ' . $role->name . ' - ' . $permission->name,
            $role,
            ['permission' => $permission->name, 'granted' => $validated['granted']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
        ]);
    }

    /**
     * Update revoked_permissions for all users with a specific role
     */
    private function updateUsersRevokedPermissions(Role $role, Permission $permission, bool $revoke)
    {
        // Get all user IDs with this role
        $userIds = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('role_id', $role->id)
            ->pluck('model_id')
            ->toArray();

        if (empty($userIds)) {
            return;
        }

        // Get all users with this role
        $users = User::whereIn('id', $userIds)
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->get();

        foreach ($users as $user) {
            $revokedPermissions = $user->revoked_permissions ?? [];
            
            if ($revoke) {
                // Add permission to revoked list if not already there
                if (!in_array($permission->name, $revokedPermissions)) {
                    $revokedPermissions[] = $permission->name;
                    $user->revoked_permissions = array_values(array_unique($revokedPermissions));
                    $user->save();
                }
            } else {
                // Remove permission from revoked list if it's there
                $revokedPermissions = array_values(array_diff($revokedPermissions, [$permission->name]));
                $user->revoked_permissions = !empty($revokedPermissions) ? $revokedPermissions : null;
                $user->save();
            }
        }
    }
}

