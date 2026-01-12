<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Announcement;
use App\Models\BoardRegulation;
use App\Models\OfficialDocument;
use App\Models\Referendum;
use App\Models\AgendaInclusionRequest;
use App\Models\ReferenceMaterial;
use App\Models\AttendanceConfirmation;
use App\Models\User;
use App\Models\GovernmentAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportGenerationController extends Controller
{
    /**
     * Display the report generation page
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view reports')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reports.');
        }

        // Get filter options - only consec and admin users for "Uploaded By" dropdown
        $users = User::whereIn('privilege', ['consec', 'admin'])
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $notices = Notice::orderBy('title')->get();
        
        // Get Notice of Meeting notices for Quorum Guide
        $nomNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('title')
            ->get();
        
        return view('admin.report-generation.index', compact('users', 'notices', 'nomNotices'));
    }

    /**
     * Generate and display report results
     */
    public function search(Request $request)
    {
        if (!Auth::user()->hasPermission('view reports')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reports.');
        }

        $reportType = $request->input('report_type', 'notices');
        $results = collect();
        $filters = $request->except(['_token']);

        // Date range
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from'))->startOfDay() : null;
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to'))->endOfDay() : null;

        switch ($reportType) {
            case 'notices':
                $query = Notice::with(['creator', 'allowedUsers']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('notice_type')) {
                    $query->where('notice_type', $request->input('notice_type'));
                }
                if ($request->input('meeting_type')) {
                    $query->where('meeting_type', $request->input('meeting_type'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'announcements':
                $query = Announcement::with(['creator', 'allowedUsers']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'board_regulations':
                $query = BoardRegulation::with(['uploader']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('uploaded_by') && $request->input('uploaded_by') !== '') {
                    $query->where('uploaded_by', $request->input('uploaded_by'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('version', 'like', "%{$search}%");
                    });
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'board_resolutions':
                $query = OfficialDocument::with(['uploader']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('uploaded_by') && $request->input('uploaded_by') !== '') {
                    $query->where('uploaded_by', $request->input('uploaded_by'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('version', 'like', "%{$search}%");
                    });
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'referendums':
                $query = Referendum::with(['creator']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('status')) {
                    $query->where('status', $request->input('status'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'agenda_requests':
                $query = AgendaInclusionRequest::with(['notice', 'user', 'reviewer']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('notice_id')) {
                    $query->where('notice_id', $request->input('notice_id'));
                }
                if ($request->input('user_id') && $request->input('user_id') !== '') {
                    $query->where('user_id', $request->input('user_id'));
                }
                if ($request->input('status')) {
                    $query->where('status', $request->input('status'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where('description', 'like', "%{$search}%");
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'reference_materials':
                $query = ReferenceMaterial::with(['notice', 'user', 'reviewer']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('notice_id')) {
                    $query->where('notice_id', $request->input('notice_id'));
                }
                if ($request->input('user_id') && $request->input('user_id') !== '') {
                    $query->where('user_id', $request->input('user_id'));
                }
                if ($request->input('status')) {
                    $query->where('status', $request->input('status'));
                }
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $query->where('description', 'like', "%{$search}%");
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'attendance_confirmations':
                $query = AttendanceConfirmation::with(['notice', 'user']);
                
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
                if ($request->input('notice_id')) {
                    $query->where('notice_id', $request->input('notice_id'));
                }
                if ($request->input('user_id') && $request->input('user_id') !== '') {
                    $query->where('user_id', $request->input('user_id'));
                }
                if ($request->input('status')) {
                    $query->where('status', $request->input('status'));
                }
                
                $results = $query->orderBy('created_at', 'desc')->get();
                break;

            case 'quorum_guide':
                // Get the selected Notice of Meeting
                $nomId = $request->input('notice_id');
                if (!$nomId) {
                    $results = collect();
                    break;
                }
                
                $nomNotice = Notice::with(['relatedNotice', 'attendanceConfirmations.user.governmentAgency', 'allowedUsers.governmentAgency'])
                    ->where('id', $nomId)
                    ->where('notice_type', 'Notice of Meeting')
                    ->first();
                
                if (!$nomNotice) {
                    $results = collect();
                    break;
                }
                
                // Get related Agenda notice
                $agendaNotice = Notice::where('related_notice_id', $nomNotice->id)
                    ->where('notice_type', 'Agenda')
                    ->first();
                
                // Get accepted attendance confirmations
                $acceptedConfirmations = AttendanceConfirmation::with(['user.governmentAgency'])
                    ->where('notice_id', $nomNotice->id)
                    ->where('status', 'accepted')
                    ->get();
                
                // Group by agency and separate Board Members from Other Attendees
                $quorumData = [
                    'nom_notice' => $nomNotice,
                    'agenda_notice' => $agendaNotice,
                    'attendees_by_agency' => []
                ];
                
                // Group attendees by agency
                $agenciesData = [];
                
                // Process registered users (from attendance confirmations)
                foreach ($acceptedConfirmations as $confirmation) {
                    $user = $confirmation->user;
                    $agencyId = $user->government_agency_id ?? 0;
                    $agencyName = $user->governmentAgency->name ?? 'Unknown Agency';
                    
                    if (!isset($agenciesData[$agencyId])) {
                        $agenciesData[$agencyId] = [
                            'agency_id' => $agencyId,
                            'agency_name' => $agencyName,
                            'board_members' => [],
                            'other_attendees' => [],
                            'remarks' => $nomNotice->meeting_type === 'onsite' ? 'Face to face' : ucfirst($nomNotice->meeting_type)
                        ];
                    }
                    
                    // Only Board Members go to board_members, others go to other_attendees
                    if ($user->representative_type === 'Board Member') {
                        $agenciesData[$agencyId]['board_members'][] = $user;
                    } else {
                        $agenciesData[$agencyId]['other_attendees'][] = $user;
                    }
                }
                
                // Also include users who are in allowedUsers but don't have attendance confirmation yet
                foreach ($nomNotice->allowedUsers as $user) {
                    $hasConfirmation = $acceptedConfirmations->where('user_id', $user->id)->first();
                    if (!$hasConfirmation) {
                        $agencyId = $user->government_agency_id ?? 0;
                        $agencyName = $user->governmentAgency->name ?? 'Unknown Agency';
                        
                        if (!isset($agenciesData[$agencyId])) {
                            $agenciesData[$agencyId] = [
                                'agency_id' => $agencyId,
                                'agency_name' => $agencyName,
                                'board_members' => [],
                                'other_attendees' => [],
                                'remarks' => $nomNotice->meeting_type === 'onsite' ? 'Face to face' : ucfirst($nomNotice->meeting_type)
                            ];
                        }
                        
                        if ($user->representative_type === 'Board Member') {
                            $agenciesData[$agencyId]['board_members'][] = $user;
                        } else {
                            $agenciesData[$agencyId]['other_attendees'][] = $user;
                        }
                    }
                }
                
                // Process CC emails (non-registered users)
                $ccEmails = $nomNotice->cc_emails;
                if (!empty($ccEmails) && is_array($ccEmails)) {
                    foreach ($ccEmails as $ccEmail) {
                        if (isset($ccEmail['agency']) && !empty($ccEmail['agency'])) {
                            $agencyId = (int)$ccEmail['agency']; // Convert to integer
                            $agency = GovernmentAgency::find($agencyId);
                            $agencyName = $agency ? $agency->name : 'Unknown Agency';
                            
                            if (!isset($agenciesData[$agencyId])) {
                                $agenciesData[$agencyId] = [
                                    'agency_id' => $agencyId,
                                    'agency_name' => $agencyName,
                                    'board_members' => [],
                                    'other_attendees' => [],
                                    'remarks' => $nomNotice->meeting_type === 'onsite' ? 'Face to face' : ucfirst($nomNotice->meeting_type)
                                ];
                            }
                            
                            // CC emails always go to other_attendees
                            $agenciesData[$agencyId]['other_attendees'][] = [
                                'type' => 'cc_email',
                                'name' => $ccEmail['name'] ?? '',
                                'email' => $ccEmail['email'] ?? '',
                                'position' => $ccEmail['position'] ?? '',
                                'agency_id' => $agencyId,
                            ];
                        }
                    }
                }
                
                $quorumData['attendees_by_agency'] = array_values($agenciesData);
                $results = collect([$quorumData]);
                break;

            case 'summary_regular_meeting':
                // Get year filter
                $year = $request->input('year');
                
                if (!$year) {
                    $results = collect();
                    break;
                }
                
                // Get Board Issuances notices for the selected year
                $query = Notice::where('notice_type', 'Board Issuances')
                    ->whereYear('meeting_date', $year)
                    ->orderBy('meeting_date', 'asc');
                
                $notices = $query->get();
                
                // Process each notice to get board regulations and resolutions
                $summaryData = [];
                $totalRegulations = 0;
                $totalResolutions = 0;
                
                foreach ($notices as $index => $notice) {
                    $regulations = [];
                    $resolutions = [];
                    
                    // Get board regulations - handle both array and JSON string
                    $boardRegulations = $notice->board_regulations;
                    if (is_string($boardRegulations)) {
                        $boardRegulations = json_decode($boardRegulations, true);
                    }
                    if (!empty($boardRegulations) && is_array($boardRegulations)) {
                        $regulationIds = array_filter($boardRegulations, function($id) {
                            return !empty($id);
                        });
                        if (!empty($regulationIds)) {
                            // Convert string IDs to integers if needed
                            $regulationIds = array_map(function($id) {
                                return is_numeric($id) ? (int)$id : $id;
                            }, $regulationIds);
                            $regulationModels = BoardRegulation::whereIn('id', $regulationIds)->get();
                            foreach ($regulationModels as $reg) {
                                $regulations[] = [
                                    'title' => $reg->title,
                                    'description' => $reg->description ?? '',
                                    'version' => $reg->version,
                                ];
                                $totalRegulations++;
                            }
                        }
                    }
                    
                    // Get board resolutions - handle both array and JSON string
                    $boardResolutions = $notice->board_resolutions;
                    if (is_string($boardResolutions)) {
                        $boardResolutions = json_decode($boardResolutions, true);
                    }
                    if (!empty($boardResolutions) && is_array($boardResolutions)) {
                        $resolutionIds = array_filter($boardResolutions, function($id) {
                            return !empty($id);
                        });
                        if (!empty($resolutionIds)) {
                            // Convert string IDs to integers if needed
                            $resolutionIds = array_map(function($id) {
                                return is_numeric($id) ? (int)$id : $id;
                            }, $resolutionIds);
                            $resolutionModels = OfficialDocument::whereIn('id', $resolutionIds)->get();
                            foreach ($resolutionModels as $res) {
                                $resolutions[] = [
                                    'title' => $res->title,
                                    'description' => $res->description ?? '',
                                    'resolution_number' => $res->resolution_number ?? '',
                                    'version' => $res->version,
                                ];
                                $totalResolutions++;
                            }
                        }
                    }
                    
                    // Always add the notice, even if it has no regulations or resolutions
                    $summaryData[] = [
                        'notice' => $notice,
                        'regulations' => $regulations,
                        'resolutions' => $resolutions,
                    ];
                }
                
                // Return as a collection with a single item for consistency with other report types
                $results = collect([
                    [
                        'year' => (int)$year,
                        'notices' => $summaryData,
                        'total_meetings' => count($summaryData),
                        'total_regulations' => $totalRegulations,
                        'total_resolutions' => $totalResolutions,
                    ]
                ]);
                break;

            case 'summary_regular_meeting_by_title':
                // Get notice title filter
                $noticeId = $request->input('notice_title_id');
                
                if (!$noticeId) {
                    $results = collect();
                    break;
                }
                
                // Get the specific Board Issuances notice
                $notice = Notice::where('notice_type', 'Board Issuances')
                    ->where('id', $noticeId)
                    ->first();
                
                if (!$notice) {
                    $results = collect();
                    break;
                }
                
                // Process regulations and resolutions
                $regulations = [];
                $resolutions = [];
                $totalRegulations = 0;
                $totalResolutions = 0;
                
                // Get board regulations
                $boardRegulations = $notice->board_regulations;
                if (is_string($boardRegulations)) {
                    $boardRegulations = json_decode($boardRegulations, true);
                }
                if (!empty($boardRegulations) && is_array($boardRegulations)) {
                    $regulationIds = array_filter($boardRegulations, function($id) {
                        return !empty($id);
                    });
                    if (!empty($regulationIds)) {
                        $regulationIds = array_map(function($id) {
                            return is_numeric($id) ? (int)$id : $id;
                        }, $regulationIds);
                        $regulationModels = BoardRegulation::whereIn('id', $regulationIds)->get();
                        foreach ($regulationModels as $reg) {
                            $regulations[] = [
                                'title' => $reg->title,
                                'description' => $reg->description ?? '',
                                'version' => $reg->version,
                            ];
                            $totalRegulations++;
                        }
                    }
                }
                
                // Get board resolutions
                $boardResolutions = $notice->board_resolutions;
                if (is_string($boardResolutions)) {
                    $boardResolutions = json_decode($boardResolutions, true);
                }
                if (!empty($boardResolutions) && is_array($boardResolutions)) {
                    $resolutionIds = array_filter($boardResolutions, function($id) {
                        return !empty($id);
                    });
                    if (!empty($resolutionIds)) {
                        $resolutionIds = array_map(function($id) {
                            return is_numeric($id) ? (int)$id : $id;
                        }, $resolutionIds);
                        $resolutionModels = OfficialDocument::whereIn('id', $resolutionIds)->get();
                        foreach ($resolutionModels as $res) {
                            $resolutions[] = [
                                'title' => $res->title,
                                'description' => $res->description ?? '',
                                'resolution_number' => $res->resolution_number ?? '',
                                'version' => $res->version,
                            ];
                            $totalResolutions++;
                        }
                    }
                }
                
                // Get max count for rows
                $maxCount = max(count($regulations), count($resolutions));
                
                // Build rows data
                $rows = [];
                for ($i = 0; $i < $maxCount; $i++) {
                    $rows[] = [
                        'regulation' => $regulations[$i] ?? null,
                        'resolution' => $resolutions[$i] ?? null,
                    ];
                }
                
                $results = collect([
                    [
                        'notice' => [
                            'title' => $notice->title,
                            'meeting_date' => $notice->meeting_date,
                        ],
                        'rows' => $rows,
                        'total_regulations' => $totalRegulations,
                        'total_resolutions' => $totalResolutions,
                    ]
                ]);
                break;
        }

        // Get filter options for the view - only consec and admin users for "Uploaded By" dropdown
        $users = User::whereIn('privilege', ['consec', 'admin'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $notices = Notice::orderBy('title')->get();
        
        // Get Notice of Meeting notices for Quorum Guide
        $nomNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('title')
            ->get();
        
        // Get distinct years from Board Issuances notices for Summary of Regular Meeting
        $availableYears = Notice::where('notice_type', 'Board Issuances')
            ->whereNotNull('meeting_date')
            ->selectRaw('YEAR(meeting_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return view('admin.report-generation.index', compact('results', 'reportType', 'filters', 'users', 'notices', 'nomNotices', 'availableYears'));
    }
}

