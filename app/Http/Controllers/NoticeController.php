<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\AttendanceConfirmation;
use App\Models\AgendaInclusionRequest;
use App\Models\ReferenceMaterial;
use App\Models\MediaLibrary;
use App\Models\Notification;
use App\Mail\NoticeAcceptedEmail;
use App\Mail\NoticeDeclinedEmail;
use App\Mail\AgendaRequestSubmittedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices accessible to the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Only show notices where the user is in the allowedUsers list
        $notices = Notice::with(['creator', 'allowedUsers', 'attendanceConfirmations'])
            ->whereHas('allowedUsers', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);
        
        // Get attendance confirmations for current user
        $attendanceConfirmations = AttendanceConfirmation::where('user_id', $userId)
            ->pluck('status', 'notice_id')
            ->toArray();
        
        // Get agenda requests and reference materials for current user
        $agendaRequests = AgendaInclusionRequest::where('user_id', $userId)
            ->pluck('id', 'notice_id')
            ->toArray();
        
        $referenceMaterials = ReferenceMaterial::where('user_id', $userId)
            ->pluck('id', 'notice_id')
            ->toArray();
        
        return view('notices.index', compact('notices', 'attendanceConfirmations', 'agendaRequests', 'referenceMaterials'));
    }

    /**
     * Display the specified notice
     */
    public function show($id)
    {
        $userId = Auth::id();
        
        $notice = Notice::with(['creator', 'allowedUsers', 'relatedNotice'])
            ->findOrFail($id);
        
        // Check if user has access
        if (!$notice->allowedUsers()->where('users.id', $userId)->exists()) {
            abort(403, 'You do not have access to this notice.');
        }
        
        // Get current user's attendance confirmation status
        $attendanceConfirmation = AttendanceConfirmation::where('notice_id', $id)
            ->where('user_id', $userId)
            ->first();
        
        // Get current user's agenda inclusion request if exists
        $agendaRequest = null;
        if ($attendanceConfirmation && $attendanceConfirmation->status === 'accepted') {
            $agendaRequest = AgendaInclusionRequest::where('notice_id', $id)
                ->where('user_id', $userId)
                ->first();
        }
        
        // Get current user's reference material submission if exists
        $referenceMaterial = null;
        if ($attendanceConfirmation && $attendanceConfirmation->status === 'accepted') {
            $referenceMaterial = ReferenceMaterial::where('notice_id', $id)
                ->where('user_id', $userId)
                ->first();
        }
        
        // Check if meeting is done
        $isMeetingDone = $notice->isMeetingDone();
        
        // Handle action from email link (accept/decline)
        $action = request()->query('action');
        $autoAction = null;
        if ($action && in_array($action, ['accept', 'decline']) && (!$attendanceConfirmation || $attendanceConfirmation->status === 'pending')) {
            $autoAction = $action;
        }
        
        return view('notices.show', compact('notice', 'attendanceConfirmation', 'agendaRequest', 'referenceMaterial', 'isMeetingDone', 'autoAction'));
    }

    /**
     * Get pending notices for the authenticated user
     */
    public function getPendingNotices()
    {
        $userId = Auth::id();
        
        // Get all notices where user is allowed
        $allNotices = Notice::with(['creator', 'attendanceConfirmations'])
            ->whereHas('allowedUsers', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Filter to only include notices where user hasn't accepted or declined
        $pendingNotices = $allNotices->filter(function($notice) use ($userId) {
            $confirmation = $notice->attendanceConfirmations->where('user_id', $userId)->first();
            // Pending if no confirmation exists OR status is 'pending'
            return !$confirmation || $confirmation->status === 'pending';
        });
        
        $notices = $pendingNotices->map(function($notice) {
            return [
                'id' => $notice->id,
                'title' => $notice->title,
                'notice_type' => $notice->notice_type,
                'meeting_date' => $notice->meeting_date ? $notice->meeting_date->format('M d, Y') : null,
                'meeting_time' => $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') : null,
                'created_at' => $notice->created_at->format('M d, Y'),
            ];
        })->values();
        
        return response()->json([
            'success' => true,
            'notices' => $notices
        ]);
    }

    /**
     * Accept notice invitation
     */
    public function accept(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access
        if (!$notice->allowedUsers()->where('users.id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this notice.'
            ], 403);
        }
        
        // Check if already confirmed
        $existing = AttendanceConfirmation::where('notice_id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            if ($existing->status === 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already accepted this invitation.'
                ], 400);
            }
            // Update existing
            $existing->update([
                'status' => 'accepted',
                'declined_reason' => null,
            ]);
            $attendanceConfirmation = $existing;
        } else {
            // Create new
            $attendanceConfirmation = AttendanceConfirmation::create([
                'notice_id' => $id,
                'user_id' => $userId,
                'status' => 'accepted',
            ]);
        }
        
        // Reload notice with creator
        $notice->load('creator');
        $user = Auth::user();
        
        // Send email and notification to notice creator
        if ($notice->creator) {
            // Determine URL based on creator's privilege
            $noticeUrl = in_array($notice->creator->privilege, ['admin', 'consec']) 
                ? route('admin.notices.show', $notice->id)
                : route('notices.show', $notice->id);
            
            // Create in-app notification
            Notification::create([
                'user_id' => $notice->creator->id,
                'type' => 'notice_accepted',
                'title' => 'Notice Accepted',
                'message' => $user->first_name . ' ' . $user->last_name . ' has accepted the invitation to "' . $notice->title . '".',
                'url' => $noticeUrl,
                'data' => [
                    'notice_id' => $notice->id,
                    'notice_title' => $notice->title,
                    'user_id' => $user->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                ],
            ]);
            
            // Send email
            try {
                Mail::to($notice->creator->email)->send(new NoticeAcceptedEmail($notice, $user, $notice->creator));
            } catch (\Exception $e) {
                \Log::error('Failed to send notice accepted email to creator ' . $notice->creator->id . ': ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted successfully.',
            'attendance_confirmation_id' => $attendanceConfirmation->id,
        ]);
    }

    /**
     * Decline notice invitation
     */
    public function decline(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        
        $notice = Notice::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access
        if (!$notice->allowedUsers()->where('users.id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this notice.'
            ], 403);
        }
        
        // Check if already confirmed
        $existing = AttendanceConfirmation::where('notice_id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            if ($existing->status === 'declined') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already declined this invitation.'
                ], 400);
            }
            // Update existing
            $existing->update([
                'status' => 'declined',
                'declined_reason' => $request->reason,
            ]);
        } else {
            // Create new
            AttendanceConfirmation::create([
                'notice_id' => $id,
                'user_id' => $userId,
                'status' => 'declined',
                'declined_reason' => $request->reason,
            ]);
        }
        
        // Reload notice with creator
        $notice->load('creator');
        $user = Auth::user();
        
        // Send email and notification to notice creator
        if ($notice->creator) {
            // Determine URL based on creator's privilege
            $noticeUrl = in_array($notice->creator->privilege, ['admin', 'consec']) 
                ? route('admin.notices.show', $notice->id)
                : route('notices.show', $notice->id);
            
            // Create in-app notification
            Notification::create([
                'user_id' => $notice->creator->id,
                'type' => 'notice_declined',
                'title' => 'Notice Declined',
                'message' => $user->first_name . ' ' . $user->last_name . ' has declined the invitation to "' . $notice->title . '".',
                'url' => $noticeUrl,
                'data' => [
                    'notice_id' => $notice->id,
                    'notice_title' => $notice->title,
                    'user_id' => $user->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'declined_reason' => $request->reason,
                ],
            ]);
            
            // Send email
            try {
                Mail::to($notice->creator->email)->send(new NoticeDeclinedEmail($notice, $user, $notice->creator, $request->reason));
            } catch (\Exception $e) {
                \Log::error('Failed to send notice declined email to creator ' . $notice->creator->id . ': ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Invitation declined successfully.',
        ]);
    }

    /**
     * Upload attachment for agenda inclusion or reference materials (user-side, no admin permission required)
     */
    public function uploadAttachment(Request $request)
    {
        if (!$request->hasFile('files') && !$request->hasFile('files.*')) {
            return response()->json([
                'success' => false,
                'message' => 'No files provided.',
            ], 422);
        }

        $files = $request->file('files');
        if (!is_array($files)) {
            $files = [$files];
        }

        $maxSize = 30 * 1024 * 1024; // 30MB
        $uploadedFiles = [];
        $errors = [];

        $category = 'notice-attachments';
        if (!Storage::disk('public')->exists($category)) {
            Storage::disk('public')->makeDirectory($category);
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                $errors[] = ['file' => $file->getClientOriginalName(), 'error' => 'Invalid file.'];
                continue;
            }
            if ($file->getSize() > $maxSize) {
                $errors[] = ['file' => $file->getClientOriginalName(), 'error' => 'File exceeds 30MB limit.'];
                continue;
            }

            try {
                $fileType = $file->getMimeType();
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $category . '/' . $fileName;

                $uploaded = Storage::disk('public')->put($filePath, file_get_contents($file));
                if (!$uploaded) {
                    $errors[] = ['file' => $file->getClientOriginalName(), 'error' => 'Failed to save file.'];
                    continue;
                }

                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $fileType,
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);

                if ($media) {
                    $uploadedFiles[] = [
                        'id' => $media->id,
                        'name' => $media->file_name,
                        'url' => asset('storage/' . $media->file_path),
                        'type' => $fileType,
                        'size' => $file->getSize(),
                    ];
                } else {
                    Storage::disk('public')->delete($filePath);
                    $errors[] = ['file' => $file->getClientOriginalName(), 'error' => 'Failed to create record.'];
                }
            } catch (\Exception $e) {
                $errors[] = ['file' => $file->getClientOriginalName(), 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
            'files' => $uploadedFiles,
            'errors' => $errors,
        ]);
    }

    /**
     * Submit agenda inclusion request
     */
    public function submitAgendaInclusion(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'exists:media_library,id',
        ]);
        
        $notice = Notice::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access and has accepted
        if (!$notice->allowedUsers()->where('users.id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this notice.'
            ], 403);
        }
        
        $attendanceConfirmation = AttendanceConfirmation::where('notice_id', $id)
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->first();
        
        if (!$attendanceConfirmation) {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the invitation first before requesting agenda inclusion.'
            ], 400);
        }
        
        // Check if already submitted
        $existing = AgendaInclusionRequest::where('notice_id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an agenda inclusion request for this notice.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $agendaRequest = AgendaInclusionRequest::create([
                'notice_id' => $id,
                'user_id' => $userId,
                'attendance_confirmation_id' => $attendanceConfirmation->id,
                'description' => $request->description,
                'attachments' => $request->attachments ?? [],
                'status' => 'pending',
            ]);
            
            // Reload notice with creator
            $notice->load('creator');
            $user = Auth::user();
            
            // Send email and notification to notice creator
            if ($notice->creator) {
                // URL to the agenda request detail page for review
                $agendaRequestUrl = route('admin.agenda-inclusion-requests.show', $agendaRequest->id);
                
                // Create in-app notification
                Notification::create([
                    'user_id' => $notice->creator->id,
                    'type' => 'agenda_request_submitted',
                    'title' => 'New Agenda Request',
                    'message' => $user->first_name . ' ' . $user->last_name . ' has submitted an agenda inclusion request for "' . $notice->title . '".',
                    'url' => $agendaRequestUrl,
                    'data' => [
                        'agenda_request_id' => $agendaRequest->id,
                        'notice_id' => $notice->id,
                        'notice_title' => $notice->title,
                        'user_id' => $user->id,
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                    ],
                ]);
                
                // Send email
                try {
                    Mail::to($notice->creator->email)->send(new AgendaRequestSubmittedEmail($agendaRequest, $notice, $user, $notice->creator));
                } catch (\Exception $e) {
                    \Log::error('Failed to send agenda request submitted email to creator ' . $notice->creator->id . ': ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Agenda inclusion request submitted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting agenda inclusion request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit agenda inclusion request. Please try again.',
            ], 500);
        }
    }

    /**
     * Submit reference materials for a notice (after meeting is done)
     */
    public function submitReferenceMaterial(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'exists:media_library,id',
        ]);
        
        $notice = Notice::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access and has accepted
        if (!$notice->allowedUsers()->where('users.id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this notice.'
            ], 403);
        }
        
        // Check if meeting is done
        if (!$notice->isMeetingDone()) {
            return response()->json([
                'success' => false,
                'message' => 'Reference materials can only be submitted after the meeting date has passed.'
            ], 400);
        }
        
        $attendanceConfirmation = AttendanceConfirmation::where('notice_id', $id)
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->first();
        
        if (!$attendanceConfirmation) {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the invitation first before submitting reference materials.'
            ], 400);
        }
        
        // Check if already submitted
        $existing = ReferenceMaterial::where('notice_id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted reference materials for this notice.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $referenceMaterial = ReferenceMaterial::create([
                'notice_id' => $id,
                'user_id' => $userId,
                'attendance_confirmation_id' => $attendanceConfirmation->id,
                'description' => $request->description,
                'attachments' => $request->attachments ?? [],
                'status' => 'pending',
            ]);
            
            // Reload notice with creator
            $notice->load('creator');
            $user = Auth::user();
            
            // Send email and notification to notice creator
            if ($notice->creator) {
                // URL to the reference material detail page for review
                $referenceMaterialUrl = route('admin.reference-materials.show', $referenceMaterial->id);
                
                // Create in-app notification
                Notification::create([
                    'user_id' => $notice->creator->id,
                    'type' => 'reference_material_submitted',
                    'title' => 'New Reference Materials Submitted',
                    'message' => $user->first_name . ' ' . $user->last_name . ' has submitted reference materials for "' . $notice->title . '".',
                    'url' => $referenceMaterialUrl,
                    'data' => [
                        'reference_material_id' => $referenceMaterial->id,
                        'notice_id' => $notice->id,
                        'notice_title' => $notice->title,
                        'user_id' => $user->id,
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                    ],
                ]);
                
                // Send email (we'll create this email class later if needed)
                // Mail::to($notice->creator->email)->send(new ReferenceMaterialSubmittedEmail($referenceMaterial, $notice, $user, $notice->creator));
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Reference materials submitted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting reference materials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit reference materials. Please try again.',
            ], 500);
        }
    }
}
