<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\AttendanceConfirmation;
use App\Models\AgendaInclusionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Filter out landogzwebsolutions@landogzwebsolutions.com
        $invitedUsers = $notice->allowedUsers
            ->filter(function($user) {
                return $user->email !== 'landogzwebsolutions@landogzwebsolutions.com';
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
            });

        return view('admin.attendance-confirmations.show', compact('notice', 'invitedUsers'));
    }
}
