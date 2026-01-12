<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        // Check permission
        if (!Auth::user()->can('view users')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view users.');
        }

        $users = User::with(['roles', 'governmentAgency'])
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->orderBy('first_name')
            ->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing a user
     */
    public function edit($id)
    {
        // Check permission
        if (!Auth::user()->can('edit users')) {
            return redirect()->route('admin.users.index')->with('error', 'You do not have permission to edit users.');
        }

        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        // Check permission
        if (!Auth::user()->can('edit users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit users.'
            ], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ]);

        // Sync roles
        if (isset($validated['roles'])) {
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $user->syncRoles($roles);
        } else {
            $user->syncRoles([]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'redirect' => route('admin.users.index'),
        ]);
    }
}

