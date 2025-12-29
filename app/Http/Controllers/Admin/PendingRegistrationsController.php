<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingRegistrationsController extends Controller
{
    /**
     * Display a listing of pending registrations
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view pending registrations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view pending registrations.');
        }

        // Get pending registrations (users with status = 'pending')
        $pendingRegistrations = User::where('status', 'pending')
            ->with(['governmentAgency', 'roles'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pending-registrations.index', compact('pendingRegistrations'));
    }

    /**
     * Show a pending registration details
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view pending registrations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view pending registrations.');
        }

        $user = User::where('status', 'pending')
            ->with(['governmentAgency', 'roles'])
            ->findOrFail($id);

        return view('admin.pending-registrations.show', compact('user'));
    }

    /**
     * Approve a pending registration
     */
    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('approve pending registrations')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to approve pending registrations.'
            ], 403);
        }

        $user = User::where('status', 'pending')->findOrFail($id);

        // Update user status to approved and activate account
        $user->update([
            'status' => 'approved',
            'is_active' => true,
        ]);

        // Ensure user has 'user' role
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        AuditLogger::log(
            'pending_registration.approved',
            'Pending registration approved: ' . $user->email,
            $user,
            [
                'approved_by' => Auth::user()->email,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Registration approved successfully. User can now login.',
        ]);
    }

    /**
     * Disapprove a pending registration
     */
    public function disapprove(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('disapprove pending registrations')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to disapprove pending registrations.'
            ], 403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user = User::where('status', 'pending')->findOrFail($id);

        // Store user data for audit log before deletion
        $userEmail = $user->email;
        $userName = trim(($user->pre_nominal_title ?? '') . ' ' . $user->first_name . ' ' . ($user->middle_initial ? $user->middle_initial . '.' : '') . ' ' . $user->last_name . ' ' . ($user->post_nominal_title ?? ''));
        $rejectionReason = $validated['rejection_reason'] ?? 'No reason provided';

        // Log the rejection before deletion
        AuditLogger::log(
            'pending_registration.rejected',
            'Pending registration rejected and deleted: ' . $userEmail,
            null, // User is being deleted, so pass null
            [
                'rejected_by' => Auth::user()->email,
                'user_id' => $user->id,
                'user_email' => $userEmail,
                'user_name' => $userName,
                'rejection_reason' => $rejectionReason,
            ]
        );

        // Delete the user record
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registration rejected and deleted successfully.',
        ]);
    }
}

