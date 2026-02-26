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

        // Years and meetings for Summary of Regular Meeting: use all Notice of Meeting (so dropdowns always have options)
        $meetingNoticesForSummary = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('title')
            ->get();
        $availableYears = $meetingNoticesForSummary
            ->map(function ($n) { return $n->meeting_date ? (int) $n->meeting_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->sortDesc()
            ->values()
            ->toArray();

        // Distinct years from approved_date in Board Regulations and Board Resolutions (PHP so it works with any DB driver)
        $regulationYears = BoardRegulation::whereNotNull('approved_date')
            ->get()
            ->map(function ($r) { return $r->approved_date ? (int) $r->approved_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $resolutionYears = OfficialDocument::whereNotNull('approved_date')
            ->get()
            ->map(function ($r) { return $r->approved_date ? (int) $r->approved_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $regulationResolutionYears = array_values(array_unique(array_merge($regulationYears, $resolutionYears)));
        rsort($regulationResolutionYears);
        
        return view('admin.report-generation.index', compact('users', 'notices', 'nomNotices', 'availableYears', 'regulationResolutionYears', 'meetingNoticesForSummary'));
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
                
                // Date range (use notice_date_from / notice_date_to when set, else date_from / date_to)
                $noticeDateFrom = $request->input('notice_date_from') ? Carbon::parse($request->input('notice_date_from'))->startOfDay() : $dateFrom;
                $noticeDateTo = $request->input('notice_date_to') ? Carbon::parse($request->input('notice_date_to'))->endOfDay() : $dateTo;
                if ($noticeDateFrom) {
                    $query->where('created_at', '>=', $noticeDateFrom);
                }
                if ($noticeDateTo) {
                    $query->where('created_at', '<=', $noticeDateTo);
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
                if ($request->filled('year')) {
                    $year = (int) $request->input('year');
                    $query->whereNotNull('approved_date')
                        ->whereBetween('approved_date', [
                            Carbon::create($year, 1, 1)->startOfDay()->toDateString(),
                            Carbon::create($year, 12, 31)->endOfDay()->toDateString(),
                        ]);
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
                if ($request->filled('year')) {
                    $year = (int) $request->input('year');
                    $query->whereNotNull('approved_date')
                        ->whereBetween('approved_date', [
                            Carbon::create($year, 1, 1)->startOfDay()->toDateString(),
                            Carbon::create($year, 12, 31)->endOfDay()->toDateString(),
                        ]);
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
                // All registered users (system) → ATTENDEES WHO ARE MEMBERS OF THE BOARD; CC emails only → Other Attendees
                $agenciesData = [];
                $userIdsWithConfirmation = $acceptedConfirmations->pluck('user_id')->all();

                // Process registered users from attendance confirmations (all go to board_members, with attendance_mode for hybrid)
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

                    $attendanceMode = $nomNotice->meeting_type === 'hybrid' ? ($confirmation->attendance_mode ?? null) : null;
                    $agenciesData[$agencyId]['board_members'][] = [
                        'user' => $user,
                        'attendance_mode' => $attendanceMode,
                    ];
                }

                // Include allowedUsers who have no attendance confirmation yet (all go to board_members)
                foreach ($nomNotice->allowedUsers as $user) {
                    if (in_array($user->id, $userIdsWithConfirmation)) {
                        continue;
                    }
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

                    $agenciesData[$agencyId]['board_members'][] = [
                        'user' => $user,
                        'attendance_mode' => null,
                    ];
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
                $year = $request->input('year');
                if (!$year) {
                    $results = collect();
                    break;
                }
                // All Notice of Meeting in the selected year; resolutions are those with notice_id = each notice
                $notices = Notice::where('notice_type', 'Notice of Meeting')
                    ->whereNotNull('meeting_date')
                    ->whereYear('meeting_date', $year)
                    ->orderBy('meeting_date', 'asc')
                    ->get();
                $summaryData = [];
                $totalRegulations = 0;
                $totalResolutions = 0;
                foreach ($notices as $notice) {
                    $regulations = [];
                    $resolutions = [];
                    // Regulations: from notice board_regulations array and from regulations linked via notice_id
                    $regulationIds = [];
                    $boardRegulations = $notice->board_regulations;
                    if (is_string($boardRegulations)) {
                        $boardRegulations = json_decode($boardRegulations, true);
                    }
                    if (!empty($boardRegulations) && is_array($boardRegulations)) {
                        $regulationIds = array_filter(array_map(function($id) { return is_numeric($id) ? (int)$id : $id; }, $boardRegulations));
                    }
                    foreach (BoardRegulation::where('notice_id', $notice->id)->get() as $reg) {
                        if (!in_array($reg->id, $regulationIds, true)) {
                            $regulationIds[] = $reg->id;
                        }
                    }
                    if (!empty($regulationIds)) {
                        foreach (BoardRegulation::whereIn('id', $regulationIds)->orderBy('approved_date')->get() as $reg) {
                            $regulations[] = ['title' => $reg->title, 'description' => $reg->description ?? '', 'version' => $reg->version];
                            $totalRegulations++;
                        }
                    }
                    // Resolutions linked to this meeting via notice_id
                    foreach (OfficialDocument::where('notice_id', $notice->id)->orderBy('approved_date')->get() as $res) {
                        $resolutions[] = [
                            'title' => $res->title,
                            'description' => $res->description ?? '',
                            'resolution_number' => $res->resolution_number ?? '',
                            'version' => $res->version,
                        ];
                        $totalResolutions++;
                    }
                    $summaryData[] = ['notice' => $notice, 'regulations' => $regulations, 'resolutions' => $resolutions];
                }
                $results = collect([[
                    'year' => (int)$year,
                    'notices' => $summaryData,
                    'total_meetings' => count($summaryData),
                    'total_regulations' => $totalRegulations,
                    'total_resolutions' => $totalResolutions,
                ]]);
                break;

            case 'summary_regular_meeting_by_title':
                $noticeId = $request->input('notice_title_id');
                if (!$noticeId) {
                    $results = collect();
                    break;
                }
                $notice = Notice::find($noticeId);
                if (!$notice) {
                    $results = collect();
                    break;
                }
                $regulations = [];
                $resolutions = [];
                $totalRegulations = 0;
                $totalResolutions = 0;
                // Regulations: from notice board_regulations array and from regulations linked via notice_id
                $regulationIds = [];
                $boardRegulations = $notice->board_regulations;
                if (is_string($boardRegulations)) {
                    $boardRegulations = json_decode($boardRegulations, true);
                }
                if (!empty($boardRegulations) && is_array($boardRegulations)) {
                    $regulationIds = array_filter(array_map(function($id) { return is_numeric($id) ? (int)$id : $id; }, $boardRegulations));
                }
                foreach (BoardRegulation::where('notice_id', $notice->id)->get() as $reg) {
                    if (!in_array($reg->id, $regulationIds, true)) {
                        $regulationIds[] = $reg->id;
                    }
                }
                if (!empty($regulationIds)) {
                    foreach (BoardRegulation::whereIn('id', $regulationIds)->orderBy('approved_date')->get() as $reg) {
                        $regulations[] = ['title' => $reg->title, 'description' => $reg->description ?? '', 'version' => $reg->version];
                        $totalRegulations++;
                    }
                }
                // Resolutions linked to this meeting via notice_id
                foreach (OfficialDocument::where('notice_id', $notice->id)->orderBy('approved_date')->get() as $res) {
                    $resolutions[] = [
                        'title' => $res->title,
                        'description' => $res->description ?? '',
                        'resolution_number' => $res->resolution_number ?? '',
                        'version' => $res->version,
                    ];
                    $totalResolutions++;
                }
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
        
        // Years and meetings for Summary reports: use all Notice of Meeting
        $meetingNoticesForSummary = Notice::where('notice_type', 'Notice of Meeting')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('title')
            ->get();
        $availableYears = $meetingNoticesForSummary
            ->map(function ($n) { return $n->meeting_date ? (int) $n->meeting_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->sortDesc()
            ->values()
            ->toArray();

        // Distinct years for Board Regulations/Resolutions year dropdown (so it stays populated and selected after search)
        $regulationYears = BoardRegulation::whereNotNull('approved_date')
            ->get()
            ->map(function ($r) { return $r->approved_date ? (int) $r->approved_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $resolutionYears = OfficialDocument::whereNotNull('approved_date')
            ->get()
            ->map(function ($r) { return $r->approved_date ? (int) $r->approved_date->format('Y') : null; })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $regulationResolutionYears = array_values(array_unique(array_merge($regulationYears, $resolutionYears)));
        rsort($regulationResolutionYears);

        return view('admin.report-generation.index', compact('results', 'reportType', 'filters', 'users', 'notices', 'nomNotices', 'availableYears', 'regulationResolutionYears', 'meetingNoticesForSummary'));
    }
}

