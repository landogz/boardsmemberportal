<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        // Permission check will be done manually in each method
        // $this->middleware('permission:manage permissions');
    }

    /**
     * Display a listing of permissions
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view permissions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view permissions.');
        }

        $permissions = Permission::orderBy('name')->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('view permissions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view permissions.');
        }

        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create permissions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create permissions.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        AuditLogger::log(
            'permission.created',
            'Created permission: ' . $permission->name,
            $permission
        );

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'redirect' => route('admin.permissions.index'),
        ]);
    }

    /**
     * Show the form for editing a permission
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('view permissions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view permissions.');
        }

        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update a permission
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit permissions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit permissions.'
            ], 403);
        }

        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        AuditLogger::log(
            'permission.updated',
            'Updated permission: ' . $permission->name,
            $permission
        );

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'redirect' => route('admin.permissions.index'),
        ]);
    }

    /**
     * Remove a permission
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete permissions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete permissions.'
            ], 403);
        }

        $permission = Permission::findOrFail($id);

        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete permission. It is assigned to one or more roles.'
            ], 422);
        }

        $permissionName = $permission->name;
        $permission->delete();

        AuditLogger::log(
            'permission.deleted',
            'Deleted permission: ' . $permissionName
        );

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully.',
        ]);
    }
}

