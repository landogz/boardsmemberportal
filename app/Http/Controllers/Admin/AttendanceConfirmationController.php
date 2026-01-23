<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\AttendanceConfirmation;
use App\Models\AgendaInclusionRequest;
use App\Models\User;
use App\Models\Notification;
use App\Mail\NoticeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AttendanceConfirmationController extends Controller
{
    /**
     * Display attendance confirmations for all notices
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermission('view attendance confirmation')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view attendance confirmation.');
        }

        // Handle notice filter parameter
        $noticeId = $request->query('notice');
        
        // Get all notices with their attendance confirmations and agenda inclusion requests
        $query = Notice::with([
            'attendanceConfirmations.user.governmentAgency',
            'agendaInclusionRequests.user',
            'allowedUsers'
        ]);
        
        if ($noticeId) {
            $query->where('id', $noticeId);
        }
        
        $notices = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics for each notice
        foreach ($notices as $notice) {
            // Filter out landogzwebsolutions@landogzwebsolutions.com from allowed users
            $filteredAllowedUsers = $notice->allowedUsers->filter(function($user) {
                return $user->email !== 'landogzwebsolutions@landogzwebsolutions.com';
            });
            $totalInvited = $filteredAllowedUsers->count();
            // Filter attendance confirmations for the excluded user
            $filteredConfirmations = $notice->attendanceConfirmations->filter(function($confirmation) {
                return $confirmation->user && $confirmation->user->email !== 'landogzwebsolutions@landogzwebsolutions.com';
            });
            $accepted = $filteredConfirmations->where('status', 'accepted')->count();
            $declined = $filteredConfirmations->where('status', 'declined')->count();
            $pending = $totalInvited - $accepted - $declined;
            
            $notice->stats = [
                'total_invited' => $totalInvited,
                'accepted' => $accepted,
                'declined' => $declined,
                'pending' => $pending,
            ];
        }

        return view('admin.attendance-confirmations.index', compact('notices', 'noticeId'));
    }

    /**
     * Show details for a specific notice's attendance confirmations
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view attendance confirmation')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view attendance confirmation.');
        }

        $notice = Notice::with([
            'attendanceConfirmations.user.governmentAgency',
            'agendaInclusionRequests.user',
            'allowedUsers.governmentAgency'
        ])->findOrFail($id);

        // Get all invited users and their confirmation status
        // Filter out landogzwebsolutions@landogzwebsolutions.com and any deleted users
        // Use a query to ensure we only get users that actually exist in the database
        $invitedUsers = $notice->allowedUsers()
            ->where('users.email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->whereNotNull('users.id')
            ->whereNotNull('users.email')
            ->get()
            ->filter(function($user) {
                // Additional check: verify user actually exists by checking if we can access its properties
                try {
                    return $user && $user->exists && $user->id && $user->email;
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->map(function($user) use ($notice) {
                $confirmation = $notice->attendanceConfirmations->where('user_id', $user->id)->first();
                $agendaRequest = $notice->agendaInclusionRequests->where('user_id', $user->id)->first();
                
                return [
                    'user' => $user,
                    'status' => $confirmation ? $confirmation->status : 'pending',
                    'declined_reason' => $confirmation && $confirmation->status === 'declined' ? $confirmation->declined_reason : null,
                    'agenda_request' => $agendaRequest,
                ];
            })
            ->filter(function($item) {
                // Final safety check to ensure user still exists and has required fields
                return $item !== null && 
                       isset($item['user']) && 
                       $item['user'] !== null && 
                       isset($item['user']->id) && 
                       isset($item['user']->email);
            });

        return view('admin.attendance-confirmations.show', compact('notice', 'invitedUsers'));
    }

    /**
     * Re-invite a user to a notice (reset declined status to pending and resend email)
     */
    public function reInvite(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('view attendance confirmation')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required'
        ], [
            'user_id.required' => 'User ID is required.'
        ]);

        $notice = Notice::with('allowedUsers')->find($id);
        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found.'
            ], 404);
        }

        $userId = trim($request->user_id);
        
        // First, try to get user from the notice's allowed users list (this ensures they're actually invited)
        $user = $notice->allowedUsers->first(function($u) use ($userId) {
            return $u && $u->id === $userId;
        });
        
        // If not found in allowed users, try to find directly from database
        if (!$user) {
            $user = User::where('id', $userId)->first();
            
            if (!$user) {
                \Log::warning('Re-invite: User not found', [
                    'user_id' => $userId,
                    'notice_id' => $id,
                    'user_id_type' => gettype($userId),
                    'user_id_length' => strlen($userId)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. The user may have been deleted or is not invited to this notice.'
                ], 404);
            }
            
            // If user exists but not in allowed users, return error
            if (!$notice->allowedUsers->contains($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is not invited to this notice.'
                ], 400);
            }
        }
        
        // Final verification that user is valid
        if (!$user || !$user->id || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user data. Please refresh the page and try again.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Find or create attendance confirmation
            $confirmation = AttendanceConfirmation::where('notice_id', $notice->id)
                ->where('user_id', $user->id)
                ->first();

            if ($confirmation) {
                // Update existing confirmation to pending and clear declined reason
                $confirmation->update([
                    'status' => 'pending',
                    'declined_reason' => null,
                ]);
            } else {
                // Create new confirmation with pending status
                AttendanceConfirmation::create([
                    'notice_id' => $notice->id,
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'declined_reason' => null,
                ]);
            }

            // Determine the correct URL based on user privilege
            $noticeUrl = in_array($user->privilege, ['admin', 'consec']) 
                ? route('admin.notices.show', $notice->id)
                : route('notices.show', $notice->id);

            // Create in-app notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'notice',
                'title' => 'Notice Re-invitation',
                'message' => 'You have been re-invited to the notice "' . $notice->title . '". Please review and respond.',
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
                \Log::error('Failed to send notice re-invitation email to user ' . $user->id . ': ' . $e->getMessage());
                // Don't fail the request if email fails, just log it
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User has been re-invited successfully. The notice has been sent again and status updated to pending.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error re-inviting user to notice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to re-invite user. Please try again.'
            ], 500);
        }
    }
}
