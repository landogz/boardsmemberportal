<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferenceMaterial;
use App\Models\Notice;
use App\Models\Notification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReferenceMaterialController extends Controller
{
    /**
     * Display a listing of reference materials
     */
    public function index(Request $request)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('view reference materials')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reference materials.');
        }

        // Handle notice filter parameter
        $noticeId = $request->query('notice');
        
        if ($noticeId) {
            $materials = ReferenceMaterial::with(['notice', 'user', 'reviewer', 'attendanceConfirmation'])
                ->where('notice_id', $noticeId)
                ->whereHas('user', function($query) {
                    $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $materials = ReferenceMaterial::with(['notice', 'user', 'reviewer', 'attendanceConfirmation'])
                ->whereHas('user', function($query) {
                    $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.reference-materials.index', compact('materials', 'noticeId'));
    }

    /**
     * Display the specified reference material
     */
    public function show($id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('view reference materials')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reference materials.');
        }

        $material = ReferenceMaterial::with(['notice', 'user.governmentAgency', 'reviewer', 'attendanceConfirmation'])
            ->findOrFail($id);

        return view('admin.reference-materials.show', compact('material'));
    }

    /**
     * Approve reference material
     */
    public function approve(Request $request, $id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('manage reference materials')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage reference materials.'
            ], 403);
        }

        $material = ReferenceMaterial::with(['notice', 'user'])->findOrFail($id);

        // Check if the current user is the creator of the notice
        if ($material->notice->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the meeting creator can approve reference materials.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $material->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for the submitter
            Notification::create([
                'user_id' => $material->user_id,
                'type' => 'reference_material_approved',
                'title' => 'Reference Materials Approved',
                'message' => 'Your reference materials for "' . $material->notice->title . '" have been approved.',
                'url' => route('notices.show', $material->notice_id),
                'data' => [
                    'reference_material_id' => $material->id,
                    'notice_id' => $material->notice_id,
                ],
            ]);

            // Log audit
            AuditLogger::log(
                'reference_material.approved',
                'Reference material approved for notice: ' . $material->notice->title,
                $material
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reference materials approved successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving reference material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve reference materials. Please try again.',
            ], 500);
        }
    }

    /**
     * Reject reference material
     */
    public function reject(Request $request, $id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('manage reference materials')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage reference materials.'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $material = ReferenceMaterial::with(['notice', 'user'])->findOrFail($id);

        // Check if the current user is the creator of the notice
        if ($material->notice->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the meeting creator can reject reference materials.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $material->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create notification for the submitter
            Notification::create([
                'user_id' => $material->user_id,
                'type' => 'reference_material_rejected',
                'title' => 'Reference Materials Rejected',
                'message' => 'Your reference materials for "' . $material->notice->title . '" have been rejected.',
                'url' => route('notices.show', $material->notice_id),
                'data' => [
                    'reference_material_id' => $material->id,
                    'notice_id' => $material->notice_id,
                    'rejection_reason' => $request->rejection_reason,
                ],
            ]);

            // Log audit
            AuditLogger::log(
                'reference_material.rejected',
                'Reference material rejected for notice: ' . $material->notice->title,
                $material
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reference materials rejected successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting reference material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject reference materials. Please try again.',
            ], 500);
        }
    }
}
