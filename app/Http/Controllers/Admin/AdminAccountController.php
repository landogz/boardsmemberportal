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

class AdminAccountController extends Controller
{
    private function ensureAdminAccess()
    {
        if (!Auth::check() || Auth::user()->privilege !== 'admin') {
            abort(403, 'Only admin users can manage admin accounts.');
        }
    }

    public function index()
    {
        $this->ensureAdminAccess();

        $adminAccounts = User::where('privilege', 'admin')
            ->where('id', '!=', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.admin-accounts.index', compact('adminAccounts'));
    }

    public function create()
    {
        $this->ensureAdminAccess();
        return view('admin.admin-accounts.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'designation' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => 'nullable|string|max:20|regex:/^\+63[0-9]{10}$/',
            'password' => [
                'required',
                'string',
                'min:8',
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
                    if (!preg_match('/[!@#$%&*()\-_=+.,]/', $value)) {
                        $fail('The password must contain at least one special character (! @ # $ % & * ( ) - _ = + . ,).');
                    }
                },
            ],
        ]);

        $user = User::create([
            'id' => Str::uuid(),
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'extension_name' => $validated['extension_name'] ?? null,
            'designation' => $validated['designation'],
            'email' => $validated['email'],
            'username' => User::usernameFromName($validated['first_name'], $validated['last_name']),
            'username_edited' => false,
            'password_hash' => Hash::make($validated['password']),
            'privilege' => 'admin',
            'is_active' => true,
            'mobile' => $validated['mobile'] ?? null,
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        AuditLogger::log(
            'admin.account_created',
            'Admin account created: ' . $user->email,
            $user,
            [
                'created_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Admin account created successfully.',
            'redirect' => route('admin.admin-accounts.index'),
        ]);
    }

    public function edit($id)
    {
        $this->ensureAdminAccess();

        $user = User::where('privilege', 'admin')->findOrFail($id);
        return view('admin.admin-accounts.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdminAccess();

        $user = User::where('privilege', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'designation' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20|regex:/^\+63[0-9]{10}$/',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('The password must contain at least one capital letter.');
                    }
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('The password must contain at least one small letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('The password must contain at least one number.');
                    }
                    if (!preg_match('/[!@#$%&*()\-_=+.,]/', $value)) {
                        $fail('The password must contain at least one special character (! @ # $ % & * ( ) - _ = + . ,).');
                    }
                },
            ],
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'extension_name' => $validated['extension_name'] ?? null,
            'designation' => $validated['designation'],
            'email' => $validated['email'],
            'username' => User::usernameFromName($validated['first_name'], $validated['last_name'], $user->id),
            'username_edited' => false,
            'mobile' => $validated['mobile'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $user->is_active,
        ];

        if (!empty($validated['password'])) {
            $updateData['password_hash'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLogger::log(
            'admin.account_updated',
            'Admin account updated: ' . $user->email,
            $user,
            [
                'updated_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Admin account updated successfully.',
            'redirect' => route('admin.admin-accounts.index'),
        ]);
    }

    public function destroy($id)
    {
        $this->ensureAdminAccess();

        if ((string) Auth::id() === (string) $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own admin account.',
            ], 422);
        }

        $user = User::where('privilege', 'admin')->findOrFail($id);
        $email = $user->email;
        $user->delete();

        AuditLogger::log(
            'admin.account_deleted',
            'Admin account deleted: ' . $email,
            null,
            [
                'deleted_by' => Auth::user()->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Admin account deleted successfully.',
        ]);
    }

    public function toggleStatus($id)
    {
        $this->ensureAdminAccess();

        if ((string) Auth::id() === (string) $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own admin account.',
            ], 422);
        }

        $user = User::where('privilege', 'admin')->findOrFail($id);
        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        if (!$user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            $user->is_online = false;
            $user->current_session_id = null;
            $user->save();
        }

        $status = $user->is_active ? 'activated' : 'deactivated';

        AuditLogger::log(
            'admin.account_status_toggled',
            "Admin account {$status}: {$user->email}",
            $user,
            [
                'changed_by' => Auth::user()->email,
                'old_status' => $oldStatus ? 'active' : 'inactive',
                'new_status' => $user->is_active ? 'active' : 'inactive',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Admin account {$status} successfully.",
            'is_active' => $user->is_active ? 1 : 0,
        ]);
    }
}

