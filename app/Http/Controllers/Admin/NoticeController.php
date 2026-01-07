<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\User;
use App\Models\MediaLibrary;
use App\Models\Notification;
use App\Mail\NoticeEmail;
use App\Mail\NoticeCcEmail;
use App\Mail\NoticeEditedEmail;
use App\Mail\NoticeCcEditedEmail;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view notices')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view notices.');
        }

        $notices = Notice::with(['creator', 'allowedUsers', 'relatedNotice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new notice
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create notices')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create notices.');
        }

        $users = User::where('privilege', 'user')
            ->with('governmentAgency')
            ->leftJoin('government_agencies', 'users.government_agency_id', '=', 'government_agencies.id')
            ->select('users.*')
            ->orderBy('government_agencies.name')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get all Board Regulations and Board Resolutions for Board Issuances
        $boardRegulations = \App\Models\BoardRegulation::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();
        
        $boardResolutions = \App\Models\OfficialDocument::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        // Get all Notice of Meeting notices for Agenda dropdown
        $noticeOfMeetingNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notices.create', compact('users', 'noticeOfMeetingNotices', 'boardRegulations', 'boardResolutions'));
    }

    /**
     * Store a newly created notice
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create notices')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create notices.'
            ], 403);
        }

        $validated = $request->validate([
            'notice_type' => 'required|in:Notice of Meeting,Agenda,Other Matters,Board Issuances',
            'title' => 'required|string|max:255',
            'title_dropdown' => 'nullable|exists:notices,id|required_if:notice_type,Agenda',
            'related_notice_id' => 'nullable|exists:notices,id',
            'meeting_type' => 'required|in:online,onsite,hybrid',
            'meeting_link' => 'nullable|string|max:500|required_if:meeting_type,online|required_if:meeting_type,hybrid',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable|date_format:H:i',
            'no_of_attendees' => 'nullable|integer|min:1',
            'board_regulations' => 'nullable|array',
            'board_regulations.*' => 'exists:board_regulations,id',
            'board_resolutions' => 'nullable|array',
            'board_resolutions.*' => 'exists:official_documents,id',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'exists:media_library,id',
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
            'cc_emails' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'The title field is required.',
            'title_dropdown.required_if' => 'Please select a notice from the dropdown for Agenda type.',
            'meeting_link.required_if' => 'Meeting link is required for online or hybrid meetings.',
            'allowed_users.required' => 'Please select at least one allowed user.',
            'allowed_users.min' => 'Please select at least one allowed user.',
        ]);

        DB::beginTransaction();
        try {

            // For Agenda type, get title from related notice if title_dropdown is provided
            $title = $validated['title'];
            $relatedNoticeId = null;
            if ($validated['notice_type'] === 'Agenda' && isset($validated['title_dropdown'])) {
                $relatedNotice = Notice::find($validated['title_dropdown']);
                if ($relatedNotice) {
                    $title = $relatedNotice->title; // Use the related notice's title
                    $relatedNoticeId = $relatedNotice->id;
                }
            } elseif (isset($validated['related_notice_id'])) {
                $relatedNoticeId = $validated['related_notice_id'];
            }

            $notice = Notice::create([
                'notice_type' => $validated['notice_type'],
                'title' => $title,
                'related_notice_id' => $relatedNoticeId,
                'meeting_type' => $validated['meeting_type'],
                'meeting_link' => in_array($validated['meeting_type'], ['online', 'hybrid']) ? $validated['meeting_link'] : null,
                'meeting_date' => $validated['meeting_date'] ?? null,
                'meeting_time' => $validated['meeting_time'] ?? null,
                'no_of_attendees' => ($validated['notice_type'] === 'Board Issuances' && isset($validated['no_of_attendees'])) ? $validated['no_of_attendees'] : null,
                'board_regulations' => ($validated['notice_type'] === 'Board Issuances' && !empty($validated['board_regulations'])) ? json_encode($validated['board_regulations']) : null,
                'board_resolutions' => ($validated['notice_type'] === 'Board Issuances' && !empty($validated['board_resolutions'])) ? json_encode($validated['board_resolutions']) : null,
                'description' => $validated['description'] ?? null,
                'attachments' => $validated['attachments'] ?? [],
                'cc_emails' => !empty($validated['cc_emails']) ? json_encode(array_values(array_filter($validated['cc_emails'], function($item) {
                    return !empty($item['email']);
                }))) : null,
                'created_by' => Auth::id(),
            ]);

            // Attach allowed users
            $notice->allowedUsers()->attach($validated['allowed_users']);

            // Reload notice with allowed users
            $notice->load('allowedUsers');

            // Send notifications and emails to all allowed users
            foreach ($notice->allowedUsers as $user) {
                // Determine the correct URL based on user privilege
                $noticeUrl = in_array($user->privilege, ['admin', 'consec']) 
                    ? route('admin.notices.show', $notice->id)
                    : route('notices.show', $notice->id);
                
                // Create in-app notification
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'notice',
                    'title' => 'New Notice',
                    'message' => 'A new notice "' . $notice->title . '" has been created and is now available for your review.',
                    'url' => $noticeUrl,
                    'data' => [
                        'notice_id' => $notice->id,
                        'notice_title' => $notice->title,
                    ],
                ]);
                
                // Send email to user
                try {
                    Mail::to($user->email)->send(new NoticeEmail($notice, $user));
                } catch (\Exception $e) {
                    \Log::error('Failed to send notice email to user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            // Send emails to CC users (non-registered users)
            if (!empty($validated['cc_emails'])) {
                $ccEmails = array_map('trim', explode(',', $validated['cc_emails']));
                foreach ($ccEmails as $ccEmail) {
                    if (filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($ccEmail)->send(new NoticeCcEmail($notice, $ccEmail));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send notice CC email to ' . $ccEmail . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            AuditLogger::log(
                'notice.create',
                'Created notice: ' . $notice->title,
                $notice
            );

            return response()->json([
                'success' => true,
                'message' => 'Notice created successfully.',
                'redirect' => route('admin.notices.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating notice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notice. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified notice
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view notices')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view notices.');
        }

        $notice = Notice::with(['creator', 'allowedUsers.governmentAgency', 'relatedNotice'])
            ->findOrFail($id);

        // Get all approved agenda inclusion requests for this notice
        $approvedAgendaRequests = \App\Models\AgendaInclusionRequest::with(['user.governmentAgency'])
            ->where('notice_id', $id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Load attachment media for each agenda request
        foreach ($approvedAgendaRequests as $agendaRequest) {
            if (!empty($agendaRequest->attachments)) {
                $agendaRequest->setRelation('attachmentMedia', \App\Models\MediaLibrary::whereIn('id', $agendaRequest->attachments)->get());
            } else {
                $agendaRequest->setRelation('attachmentMedia', collect([]));
            }
        }

        return view('admin.notices.show', compact('notice', 'approvedAgendaRequests'));
    }

    /**
     * Show the form for editing the specified notice
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit notices')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to edit notices.');
        }

        $notice = Notice::with(['allowedUsers'])->findOrFail($id);

        $users = User::where('privilege', 'user')
            ->with('governmentAgency')
            ->leftJoin('government_agencies', 'users.government_agency_id', '=', 'government_agencies.id')
            ->select('users.*')
            ->orderBy('government_agencies.name')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get all Board Regulations and Board Resolutions for Board Issuances
        $boardRegulations = \App\Models\BoardRegulation::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();
        
        $boardResolutions = \App\Models\OfficialDocument::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        // Get all Notice of Meeting notices for Agenda dropdown
        $noticeOfMeetingNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notices.edit', compact('notice', 'users', 'noticeOfMeetingNotices', 'boardRegulations', 'boardResolutions'));
    }

    /**
     * Update the specified notice
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit notices')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit notices.'
            ], 403);
        }

        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'notice_type' => 'required|in:Notice of Meeting,Agenda,Other Matters,Board Issuances',
            'title' => 'required|string|max:255',
            'title_dropdown' => 'nullable|exists:notices,id|required_if:notice_type,Agenda',
            'related_notice_id' => 'nullable|exists:notices,id',
            'meeting_type' => 'required|in:online,onsite,hybrid',
            'meeting_link' => 'nullable|string|max:500|required_if:meeting_type,online|required_if:meeting_type,hybrid',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable|date_format:H:i',
            'no_of_attendees' => 'nullable|integer|min:1',
            'board_regulations' => 'nullable|array',
            'board_regulations.*' => 'exists:board_regulations,id',
            'board_resolutions' => 'nullable|array',
            'board_resolutions.*' => 'exists:official_documents,id',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'exists:media_library,id',
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
            'cc_emails' => 'nullable|array',
            'cc_emails.*.name' => 'required_with:cc_emails.*|string|max:255',
            'cc_emails.*.email' => 'required_with:cc_emails.*|email|max:255',
            'cc_emails.*.position' => 'nullable|string|max:255',
            'cc_emails.*.agency' => 'nullable|string|max:255',
        ], [
            'title.required' => 'The title field is required.',
            'title_dropdown.required_if' => 'Please select a notice from the dropdown for Agenda type.',
            'meeting_link.required_if' => 'Meeting link is required for online or hybrid meetings.',
            'allowed_users.required' => 'Please select at least one allowed user.',
            'allowed_users.min' => 'Please select at least one allowed user.',
        ]);

        DB::beginTransaction();
        try {

            // For Agenda type, get title from related notice if title_dropdown is provided
            $title = $validated['title'];
            $relatedNoticeId = $notice->related_notice_id;
            if ($validated['notice_type'] === 'Agenda' && isset($validated['title_dropdown'])) {
                $relatedNotice = Notice::find($validated['title_dropdown']);
                if ($relatedNotice) {
                    $title = $relatedNotice->title; // Use the related notice's title
                    $relatedNoticeId = $relatedNotice->id;
                }
            } elseif (isset($validated['related_notice_id'])) {
                $relatedNoticeId = $validated['related_notice_id'];
            } elseif ($validated['notice_type'] !== 'Agenda') {
                $relatedNoticeId = null;
            }

            $notice->update([
                'notice_type' => $validated['notice_type'],
                'title' => $title,
                'related_notice_id' => $relatedNoticeId,
                'meeting_type' => $validated['meeting_type'],
                'meeting_link' => in_array($validated['meeting_type'], ['online', 'hybrid']) ? $validated['meeting_link'] : null,
                'meeting_date' => $validated['meeting_date'] ?? null,
                'meeting_time' => $validated['meeting_time'] ?? null,
                'no_of_attendees' => ($validated['notice_type'] === 'Board Issuances' && isset($validated['no_of_attendees'])) ? $validated['no_of_attendees'] : null,
                'board_regulations' => ($validated['notice_type'] === 'Board Issuances' && !empty($validated['board_regulations'])) ? json_encode($validated['board_regulations']) : null,
                'board_resolutions' => ($validated['notice_type'] === 'Board Issuances' && !empty($validated['board_resolutions'])) ? json_encode($validated['board_resolutions']) : null,
                'description' => $validated['description'] ?? null,
                'attachments' => $validated['attachments'] ?? [],
                'cc_emails' => !empty($validated['cc_emails']) ? json_encode(array_values(array_filter($validated['cc_emails'], function($item) {
                    return !empty($item['email']);
                }))) : null,
            ]);

            // Sync allowed users
            $notice->allowedUsers()->sync($validated['allowed_users']);

            // Reload notice with allowed users
            $notice->load('allowedUsers');

            // Send notifications and emails to ALL allowed users (notice was edited)
            foreach ($notice->allowedUsers as $user) {
                // Determine the correct URL based on user privilege
                $noticeUrl = in_array($user->privilege, ['admin', 'consec']) 
                    ? route('admin.notices.show', $notice->id)
                    : route('notices.show', $notice->id);
                
                // Create in-app notification
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'notice',
                    'title' => 'Notice Updated',
                    'message' => 'A notice "' . $notice->title . '" has been updated. Please review the changes.',
                    'url' => $noticeUrl,
                    'data' => [
                        'notice_id' => $notice->id,
                        'notice_title' => $notice->title,
                    ],
                ]);
                
                // Send email to user
                try {
                    Mail::to($user->email)->send(new NoticeEditedEmail($notice, $user));
                } catch (\Exception $e) {
                    \Log::error('Failed to send notice edited email to user ' . $user->id . ': ' . $e->getMessage());
                }
            }

            // Send emails to ALL CC users (notice was edited)
            if (!empty($validated['cc_emails'])) {
                foreach ($validated['cc_emails'] as $ccData) {
                    if (!empty($ccData['email']) && filter_var($ccData['email'], FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($ccData['email'])->send(new NoticeCcEditedEmail($notice, $ccData['email']));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send notice CC edited email to ' . $ccData['email'] . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            AuditLogger::log(
                'notice.update',
                'Updated notice: ' . $notice->title,
                $notice
            );

            return response()->json([
                'success' => true,
                'message' => 'Notice updated successfully.',
                'redirect' => route('admin.notices.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating notice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notice. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified notice
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete notices')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete notices.'
            ], 403);
        }

        $notice = Notice::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete attachments if exist
            if ($notice->attachments && count($notice->attachments) > 0) {
                foreach ($notice->attachments as $attachmentId) {
                    $media = MediaLibrary::find($attachmentId);
                    if ($media) {
                        Storage::disk('public')->delete($media->file_path);
                        $media->delete();
                    }
                }
            }

            // Detach all users
            $notice->allowedUsers()->detach();

            $title = $notice->title;
            $notice->delete();

            DB::commit();

            AuditLogger::log(
                'notice.delete',
                'Deleted notice: ' . $title,
                null
            );

            return response()->json([
                'success' => true,
                'message' => 'Notice deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting notice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notice. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
