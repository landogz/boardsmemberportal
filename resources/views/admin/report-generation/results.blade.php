@if($reportType === 'notices')
    @foreach($results as $notice)
        <div class="result-item">
            <h4 class="result-title">{{ $notice->title }}</h4>
            <div class="result-meta">
                @php
                    $typeClass = 'bg-blue-100 text-blue-700';
                    if ($notice->notice_type === 'Agenda') {
                        $typeClass = 'bg-red-100 text-red-700';
                    } elseif ($notice->notice_type === 'Board Issuances') {
                        $typeClass = 'bg-purple-100 text-purple-700';
                    }
                @endphp
                <span class="result-badge {{ $typeClass }}">{{ $notice->notice_type }}</span>
                @if($notice->meeting_type)
                    <span class="result-badge" style="background-color: #f3f4f6; color: #374151;">{{ ucfirst($notice->meeting_type) }}</span>
                @endif
                @if($notice->meeting_date)
                    <span class="result-footer-item">
                        <i class="fas fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</span>
                    </span>
                @endif
            </div>
            @if($notice->description)
                <p class="result-description">{{ Str::limit(strip_tags($notice->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Created by:</strong> {{ $notice->creator->first_name ?? 'N/A' }} {{ $notice->creator->last_name ?? '' }}</span>
                </span>
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $notice->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'announcements')
    @foreach($results as $announcement)
        <div class="result-item">
            <h4 class="result-title">{{ $announcement->title }}</h4>
            @if($announcement->description)
                <p class="result-description">{{ Str::limit(strip_tags($announcement->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Created by:</strong> {{ $announcement->creator->first_name ?? 'N/A' }} {{ $announcement->creator->last_name ?? '' }}</span>
                </span>
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'board_regulations')
    @foreach($results as $regulation)
        <div class="result-item">
            <h4 class="result-title">{{ $regulation->title }}</h4>
            <div class="result-meta">
                <span class="result-badge" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fas fa-tag mr-1.5" style="font-size: 0.75rem;"></i>
                    Version {{ $regulation->version }}
                </span>
                @if($regulation->effective_date)
                    <span class="result-footer-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Effective: {{ \Carbon\Carbon::parse($regulation->effective_date)->format('M d, Y') }}</span>
                    </span>
                @endif
            </div>
            @if($regulation->description)
                <p class="result-description">{{ Str::limit(strip_tags($regulation->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Uploaded by:</strong> {{ $regulation->uploader->first_name ?? 'N/A' }} {{ $regulation->uploader->last_name ?? '' }}</span>
                </span>
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $regulation->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'board_resolutions')
    @foreach($results as $resolution)
        <div class="result-item">
            <h4 class="result-title">{{ $resolution->title }}</h4>
            <div class="result-meta">
                <span class="result-badge" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fas fa-tag mr-1.5" style="font-size: 0.75rem;"></i>
                    Version {{ $resolution->version }}
                </span>
                @if($resolution->effective_date)
                    <span class="result-footer-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Effective: {{ \Carbon\Carbon::parse($resolution->effective_date)->format('M d, Y') }}</span>
                    </span>
                @endif
            </div>
            @if($resolution->description)
                <p class="result-description">{{ Str::limit(strip_tags($resolution->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Uploaded by:</strong> {{ $resolution->uploader->first_name ?? 'N/A' }} {{ $resolution->uploader->last_name ?? '' }}</span>
                </span>
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $resolution->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'referendums')
    @foreach($results as $referendum)
        <div class="result-item">
            <h4 class="result-title">{{ $referendum->title }}</h4>
            <div class="result-meta">
                <span class="result-badge {{ $referendum->status === 'published' ? 'bg-green-100 text-green-700' : ($referendum->status === 'closed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($referendum->status) }}</span>
            </div>
            @if($referendum->description)
                <p class="result-description">{{ Str::limit(strip_tags($referendum->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Created by:</strong> {{ $referendum->creator->first_name ?? 'N/A' }} {{ $referendum->creator->last_name ?? '' }}</span>
                </span>
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $referendum->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'agenda_requests')
    @foreach($results as $request)
        <div class="result-item">
            <h4 class="result-title">Agenda Request for: {{ $request->notice->title ?? 'N/A' }}</h4>
            <div class="result-meta">
                <span class="result-badge {{ $request->status === 'approved' ? 'bg-green-100 text-green-700' : ($request->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($request->status) }}</span>
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Requested by:</strong> {{ $request->user->first_name ?? 'N/A' }} {{ $request->user->last_name ?? '' }}</span>
                </span>
            </div>
            @if($request->description)
                <p class="result-description">{{ Str::limit(strip_tags($request->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>Submitted: {{ $request->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'reference_materials')
    @foreach($results as $material)
        <div class="result-item">
            <h4 class="result-title">Reference Material for: {{ $material->notice->title ?? 'N/A' }}</h4>
            <div class="result-meta">
                <span class="result-badge {{ $material->status === 'approved' ? 'bg-green-100 text-green-700' : ($material->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($material->status) }}</span>
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Submitted by:</strong> {{ $material->user->first_name ?? 'N/A' }} {{ $material->user->last_name ?? '' }}</span>
                </span>
            </div>
            @if($material->description)
                <p class="result-description">{{ Str::limit(strip_tags($material->description), 200) }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>Submitted: {{ $material->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'attendance_confirmations')
    @foreach($results as $confirmation)
        <div class="result-item">
            <h4 class="result-title">Attendance for: {{ $confirmation->notice->title ?? 'N/A' }}</h4>
            <div class="result-meta">
                <span class="result-badge {{ $confirmation->status === 'accepted' ? 'bg-green-100 text-green-700' : ($confirmation->status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($confirmation->status) }}</span>
                <span class="result-footer-item">
                    <i class="fas fa-user"></i>
                    <span><strong>User:</strong> {{ $confirmation->user->first_name ?? 'N/A' }} {{ $confirmation->user->last_name ?? '' }}</span>
                </span>
            </div>
            @if($confirmation->declined_reason)
                <p class="result-description"><strong>Reason:</strong> {{ $confirmation->declined_reason }}</p>
            @endif
            <div class="result-footer">
                <span class="result-footer-item">
                    <i class="fas fa-clock"></i>
                    <span>Confirmed: {{ $confirmation->created_at->format('M d, Y h:i A') }}</span>
                </span>
            </div>
        </div>
    @endforeach

@elseif($reportType === 'quorum_guide')
    @if($results->count() > 0)
        @php
            $quorumData = $results->first();
            $nomNotice = $quorumData['nom_notice'];
            $agendaNotice = $quorumData['agenda_notice'] ?? null;
            $attendeesByAgency = $quorumData['attendees_by_agency'] ?? [];
            
            // Extract meeting number from title (e.g., "207th" from "207th REGULAR BOARD MEETING")
            $meetingNumber = null;
            $previousMeetingNumber = null;
            if (preg_match('/(\d+)(?:st|nd|rd|th)/i', $nomNotice->title, $matches)) {
                $meetingNumber = (int)$matches[1];
                $previousMeetingNumber = $meetingNumber - 1;
                // Convert to ordinal (e.g., 206 -> "206th")
                $suffix = 'th';
                if ($previousMeetingNumber % 100 < 10 || $previousMeetingNumber % 100 > 20) {
                    $lastDigit = $previousMeetingNumber % 10;
                    if ($lastDigit == 1) $suffix = 'st';
                    elseif ($lastDigit == 2) $suffix = 'nd';
                    elseif ($lastDigit == 3) $suffix = 'rd';
                }
                $previousMeetingNumber = $previousMeetingNumber . $suffix;
            }
        @endphp
        
        <div class="quorum-guide-report">
            <!-- Header -->
            <div class="mb-3 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ strtoupper($nomNotice->title) }}</h2>
                @if($nomNotice->meeting_date)
                    <p class="text-lg text-gray-700">{{ $nomNotice->meeting_date->format('d F Y') }}</p>
                @endif
            </div>

            <!-- QUORUM GUIDE Section -->
            <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">QUORUM GUIDE</h3>
                
                <!-- Notice of Meeting Info -->
                <div class="mb-2">
                    <p class="text-base font-semibold text-gray-800 mb-1">
                        Sending of the Notice of Meeting and Minutes of the Previous
                        @if($previousMeetingNumber)
                            ({{ $previousMeetingNumber }})
                        @endif
                        Meeting
                    </p>
                    @if($nomNotice->meeting_date)
                        <p class="text-sm text-gray-600 ml-4 mb-0">• {{ $nomNotice->meeting_date->format('d F Y') }}</p>
                    @endif
                </div>

                <!-- Agenda Info -->
                @if($agendaNotice)
                    <div class="mb-2">
                        <p class="text-base font-semibold text-gray-800 mb-1">
                            Sending of Provisional Agenda
                        </p>
                        @if($agendaNotice->meeting_date)
                            <p class="text-sm text-gray-600 ml-4 mb-0">• {{ $agendaNotice->meeting_date->format('d F Y') }}</p>
                        @elseif($agendaNotice->created_at)
                            <p class="text-sm text-gray-600 ml-4 mb-0">• {{ $agendaNotice->created_at->format('d F Y') }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Attendees Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 20%;">AGENCY</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 30%;">ATTENDEES WHO ARE MEMBERS OF THE BOARD</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 35%;">Other Attendees</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 15%;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNumber = 1;
                        @endphp
                        @foreach($attendeesByAgency as $agencyData)
                            @php
                                $boardMembers = $agencyData['board_members'] ?? [];
                                $otherAttendees = $agencyData['other_attendees'] ?? [];
                                $agencyName = $agencyData['agency_name'] ?? 'Unknown Agency';
                                $remarks = $agencyData['remarks'] ?? '';
                            @endphp
                            
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 align-top">
                                    <div class="font-semibold">{{ $rowNumber }} - {{ strtoupper($agencyName) }}</div>
                                </td>
                                
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(count($boardMembers) > 0)
                                        @foreach($boardMembers as $member)
                                            @php
                                                $title = $member->pre_nominal_title ?? '';
                                                $firstName = $member->first_name ?? '';
                                                $lastName = $member->last_name ?? '';
                                                $middleInitial = $member->middle_initial ?? '';
                                                $postNominal = $member->post_nominal_title ?? '';
                                                $designation = $member->designation ?? '';
                                                
                                                $fullName = trim(($title ? $title . ' ' : '') . strtoupper($firstName) . ($middleInitial ? ' ' . strtoupper($middleInitial) . '.' : '') . ' ' . strtoupper($lastName) . ($postNominal ? ' ' . $postNominal : ''));
                                            @endphp
                                            <div class="font-semibold">{{ $fullName }}</div>
                                            @if($designation)
                                                <div class="text-sm text-gray-600">{{ $designation }}</div>
                                            @endif
                                            @if(!$loop->last)
                                                <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(count($otherAttendees) > 0)
                                        @foreach($otherAttendees as $attendee)
                                            @php
                                                // Check if it's a CC email (non-registered user) or registered user
                                                if (is_array($attendee) && isset($attendee['type']) && $attendee['type'] === 'cc_email') {
                                                    // CC email user
                                                    $fullName = strtoupper($attendee['name'] ?? '');
                                                    $position = $attendee['position'] ?? '';
                                                } else {
                                                    // Registered user
                                                    $title = $attendee->pre_nominal_title ?? '';
                                                    $firstName = $attendee->first_name ?? '';
                                                    $lastName = $attendee->last_name ?? '';
                                                    $middleInitial = $attendee->middle_initial ?? '';
                                                    $postNominal = $attendee->post_nominal_title ?? '';
                                                    $designation = $attendee->designation ?? '';
                                                    
                                                    $fullName = trim(($title ? $title . ' ' : '') . strtoupper($firstName) . ($middleInitial ? ' ' . strtoupper($middleInitial) . '.' : '') . ' ' . strtoupper($lastName) . ($postNominal ? ' ' . $postNominal : ''));
                                                    $position = $designation;
                                                }
                                            @endphp
                                            <div class="font-semibold">{{ $fullName }}</div>
                                            @if($position)
                                                <div class="text-sm text-gray-600">{{ $position }}</div>
                                            @endif
                                            @if(!$loop->last)
                                                <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                
                                <td class="border border-gray-300 px-4 py-2 align-top">
                                    {{ $remarks }}
                                </td>
                            </tr>
                            
                            @php $rowNumber++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Please select a Notice of Meeting to generate the Quorum Guide</p>
        </div>
    @endif

@elseif($reportType === 'summary_regular_meeting')
    @if($results->count() > 0)
        @php
            $summaryData = $results->first();
            $year = $summaryData['year'] ?? date('Y');
            $notices = $summaryData['notices'] ?? [];
            $totalMeetings = $summaryData['total_meetings'] ?? 0;
            $totalRegulations = $summaryData['total_regulations'] ?? 0;
            $totalResolutions = $summaryData['total_resolutions'] ?? 0;
        @endphp
        
        <div class="summary-regular-meeting-report">
            <!-- Header -->
                   <div class="mb-6 text-center">
                       <img src="{{ asset('images/ddbheader.png') }}" alt="DDB Header" class="mx-auto mb-4" style="max-width: 250px; height: auto;">
                       <h2 class="text-xl font-bold text-gray-900 mb-2">SUMMARY OF REGULAR MEETING OF</h2>
                       <h2 class="text-xl font-bold text-gray-900 mb-2">THE DANGEROUS DRUGS BOARD</h2>
                       <p class="text-base text-gray-700 font-semibold">YEAR: {{ $year }}</p>
                   </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 5%;">NO.</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 25%;">MEETING TITLE</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 15%;">DATE OF MEETING</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 10%;">NO. OF ATTENDEES</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 22.5%;">BOARD REGULATIONS</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 22.5%;">BOARD RESOLUTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNumber = 1;
                        @endphp
                        @foreach($notices as $item)
                            @php
                                $notice = $item['notice'];
                                $regulations = $item['regulations'] ?? [];
                                $resolutions = $item['resolutions'] ?? [];
                                $maxRows = max(count($regulations), count($resolutions), 1);
                            @endphp
                            
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    <div class="font-semibold">{{ $rowNumber }}</div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    <div>{{ $notice->title }}</div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if($notice->meeting_date)
                                        {{ $notice->meeting_date->format('F d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    {{ $notice->no_of_attendees ?? '—' }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-top">
                                    @if(count($regulations) > 0)
                                        @foreach($regulations as $index => $reg)
                                            <div class="mb-3">
                                                @if(!empty($reg['title']) && trim($reg['title']) !== '')
                                                    <div class="font-semibold mb-1">{{ $reg['title'] }}</div>
                                                @endif
                                                @if(!empty($reg['description']) && trim($reg['description']) !== '')
                                                    <div class="text-sm text-gray-600 leading-relaxed">{{ $reg['description'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-top">
                                    @if(count($resolutions) > 0)
                                        @foreach($resolutions as $index => $res)
                                            <div class="mb-3">
                                                @php
                                                    $resolutionLabel = 'Board Resolution No. ' . ($index + 1);
                                                    if (!empty($res['title']) && preg_match('/Board Resolution No\./i', $res['title'])) {
                                                        $resolutionLabel = $res['title'];
                                                    } elseif (!empty($res['resolution_number']) && trim($res['resolution_number']) !== '') {
                                                        $resolutionLabel = $res['resolution_number'];
                                                    }
                                                @endphp
                                                <div class="font-semibold mb-1">{{ $resolutionLabel }}</div>
                                                @if(!empty($res['description']) && trim($res['description']) !== '')
                                                    <div class="text-sm text-gray-600 leading-relaxed">{{ $res['description'] }}</div>
                                                @elseif(!empty($res['title']) && trim($res['title']) !== '' && !preg_match('/Board Resolution No\./i', $res['title']))
                                                    <div class="text-sm text-gray-600 leading-relaxed">{{ $res['title'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            
                            @php $rowNumber++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Totals Below Table -->
            <div class="mt-4 text-left space-y-2">
                <div class="font-semibold text-gray-900">
                    Total no. of Meetings: {{ $totalMeetings }}
                </div>
                <div class="font-semibold text-gray-900">
                    Total no. of Approved Board Regulations: {{ $totalRegulations }}
                </div>
                <div class="font-semibold text-gray-900">
                    Total no. of Approved Resolutions: {{ $totalResolutions }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Please select a year to generate the Summary of Regular Meeting report</p>
        </div>
    @endif

@elseif($reportType === 'summary_regular_meeting_by_title')
    @if($results->count() > 0)
        @php
            $summaryData = $results->first();
            $notice = $summaryData['notice'] ?? [];
            $rows = $summaryData['rows'] ?? [];
            $totalRegulations = $summaryData['total_regulations'] ?? 0;
            $totalResolutions = $summaryData['total_resolutions'] ?? 0;
        @endphp
        
        <div class="summary-regular-meeting-by-title-report">
            <!-- Header -->
            <div class="mb-6 text-center">
                <img src="{{ asset('images/ddbheader.png') }}" alt="DDB Header" class="mx-auto mb-4" style="max-width: 250px; height: auto;">
                <h2 class="text-xl font-bold text-gray-900 mb-2">SUMMARY OF {{ strtoupper($notice['title'] ?? '') }}</h2>
                @if(!empty($notice['meeting_date']))
                    <p class="text-base text-gray-700 font-semibold">{{ \Carbon\Carbon::parse($notice['meeting_date'])->format('F d, Y') }}</p>
                @endif
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left" style="width: 10%;">NO.</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-center" colspan="4" style="width: 90%;">APPROVED ISSUANCES</th>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left"></th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-center" colspan="2" style="width: 45%;">BOARD REGULATIONS</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-center" colspan="2" style="width: 45%;">BOARD RESOLUTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $maxRows = max(count($rows), 1);
                        @endphp
                        @for($i = 0; $i < $maxRows; $i++)
                            @php
                                $row = $rows[$i] ?? null;
                            @endphp
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    <div class="font-semibold">{{ $i + 1 }}.</div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(!empty($row['regulation']) && !empty($row['regulation']['title']))
                                        <div>{{ $row['regulation']['title'] }}</div>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(!empty($row['regulation']) && !empty($row['regulation']['description']))
                                        <div>{{ $row['regulation']['description'] }}</div>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(!empty($row['resolution']) && !empty($row['resolution']['title']))
                                        <div>{{ $row['resolution']['title'] }}</div>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 align-middle">
                                    @if(!empty($row['resolution']) && !empty($row['resolution']['description']))
                                        <div>{{ $row['resolution']['description'] }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            
            <!-- Totals Below Table -->
            <div class="mt-4 text-left space-y-2">
                <div class="font-semibold text-gray-900">
                    Total no. of Approved Board Regulations: {{ $totalRegulations }}
                </div>
                <div class="font-semibold text-gray-900">
                    Total no. of Approved Resolutions: {{ $totalResolutions }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Please select a notice title to generate the Summary of Regular Meeting by Title report</p>
        </div>
    @endif
@endif

