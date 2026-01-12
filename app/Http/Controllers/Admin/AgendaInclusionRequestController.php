<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaInclusionRequest;
use App\Models\Notice;
use App\Models\Notification;
use App\Mail\AgendaRequestRejectedEmail;
use App\Mail\AgendaRequestApprovedEmail;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AgendaInclusionRequestController extends Controller
{
    /**
     * Display a listing of agenda requests
     */
    public function index(Request $request)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('view agenda requests')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view agenda requests.');
        }

        // Handle notice filter parameter
        $noticeId = $request->query('notice');
        
        if ($noticeId) {
            // Get all agenda requests for this notice
            $noticeRequests = AgendaInclusionRequest::with(['notice', 'user', 'reviewer', 'attendanceConfirmation'])
                ->where('notice_id', $noticeId)
                ->whereHas('user', function($query) {
                    $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            // If there's only one request, redirect to its detail page
            if ($noticeRequests->count() === 1) {
                return redirect()->route('admin.agenda-inclusion-requests.show', $noticeRequests->first()->id);
            }
            
            // If there are multiple requests, filter the list
            $requests = AgendaInclusionRequest::with(['notice', 'user', 'reviewer', 'attendanceConfirmation'])
                ->where('notice_id', $noticeId)
                ->whereHas('user', function($query) {
                    $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // No filter, show all requests
            $requests = AgendaInclusionRequest::with(['notice', 'user', 'reviewer', 'attendanceConfirmation'])
                ->whereHas('user', function($query) {
                    $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.agenda-inclusion-requests.index', compact('requests', 'noticeId'));
    }

    /**
     * Display the specified agenda inclusion request
     */
    public function show($id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('view agenda requests')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view agenda requests.');
        }

        $request = AgendaInclusionRequest::with(['notice', 'user.governmentAgency', 'reviewer', 'attendanceConfirmation'])
            ->findOrFail($id);

        return view('admin.agenda-inclusion-requests.show', compact('request'));
    }

    /**
     * Approve agenda request
     */
    public function approve(Request $request, $id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('manage agenda requests')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage agenda requests.'
            ], 403);
        }

        $agendaRequest = AgendaInclusionRequest::with(['notice', 'user'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $agendaRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Reload to get updated data
            $agendaRequest->refresh();

            // Determine the correct URL based on user privilege
            $noticeUrl = in_array($agendaRequest->user->privilege, ['admin', 'consec']) 
                ? route('admin.notices.show', $agendaRequest->notice_id)
                : route('notices.show', $agendaRequest->notice_id);

            // Create in-app notification
            Notification::create([
                'user_id' => $agendaRequest->user_id,
                'type' => 'agenda_request_approved',
                'title' => 'Agenda Request Approved',
                'message' => 'Your agenda inclusion request for "' . $agendaRequest->notice->title . '" has been approved.',
                'url' => $noticeUrl,
                'data' => [
                    'agenda_request_id' => $agendaRequest->id,
                    'notice_id' => $agendaRequest->notice_id,
                    'notice_title' => $agendaRequest->notice->title,
                ],
            ]);

            // Send email to user
            try {
                Mail::to($agendaRequest->user->email)->send(new AgendaRequestApprovedEmail($agendaRequest, $agendaRequest->user));
            } catch (\Exception $e) {
                \Log::error('Failed to send agenda request approved email to user ' . $agendaRequest->user_id . ': ' . $e->getMessage());
            }

            AuditLogger::log(
                'agenda_inclusion_request.approved',
                'Approved agenda request for notice: ' . $agendaRequest->notice->title,
                $agendaRequest
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agenda request approved successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving agenda inclusion request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve agenda request. Please try again.',
            ], 500);
        }
    }

    /**
     * Reject agenda request
     */
    public function reject(Request $request, $id)
    {
        // Clear permission cache to ensure fresh permission check
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        if (!Auth::user()->hasPermission('manage agenda requests')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage agenda requests.'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $agendaRequest = AgendaInclusionRequest::with(['notice', 'user'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $agendaRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Reload to get updated data
            $agendaRequest->refresh();

            // Determine the correct URL based on user privilege
            $noticeUrl = in_array($agendaRequest->user->privilege, ['admin', 'consec']) 
                ? route('admin.notices.show', $agendaRequest->notice_id)
                : route('notices.show', $agendaRequest->notice_id);

            // Create in-app notification
            Notification::create([
                'user_id' => $agendaRequest->user_id,
                'type' => 'agenda_request_rejected',
                'title' => 'Agenda Request Rejected',
                'message' => 'Your agenda inclusion request for "' . $agendaRequest->notice->title . '" has been rejected.',
                'url' => $noticeUrl,
                'data' => [
                    'agenda_request_id' => $agendaRequest->id,
                    'notice_id' => $agendaRequest->notice_id,
                    'notice_title' => $agendaRequest->notice->title,
                    'rejection_reason' => $agendaRequest->rejection_reason,
                ],
            ]);

            // Send email to user
            try {
                Mail::to($agendaRequest->user->email)->send(new AgendaRequestRejectedEmail($agendaRequest, $agendaRequest->user));
            } catch (\Exception $e) {
                \Log::error('Failed to send agenda request rejected email to user ' . $agendaRequest->user_id . ': ' . $e->getMessage());
            }

            AuditLogger::log(
                'agenda_inclusion_request.rejected',
                'Rejected agenda request for notice: ' . $agendaRequest->notice->title,
                $agendaRequest
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agenda request rejected successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting agenda inclusion request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject agenda request. Please try again.',
            ], 500);
        }
    }
}
