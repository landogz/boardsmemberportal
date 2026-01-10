@extends('admin.layout')

@section('title', 'Report Generation')

@php
    $pageTitle = 'Report Generation';
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .filter-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    .filter-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .results-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .result-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .result-item:hover {
        background: #f9fafb;
    }
    .result-item:last-child {
        border-bottom: none;
    }
    .result-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }
    .result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        align-items: center;
    }
    .result-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .result-description {
        font-size: 0.9375rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 0.75rem;
    }
    .result-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        font-size: 0.8125rem;
        color: #6b7280;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
    }
    .result-footer-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    .result-footer-item i {
        font-size: 0.75rem;
        color: #9ca3af;
    }
    .quorum-guide-report {
        font-family: Arial, sans-serif;
    }
    .quorum-guide-report table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    .quorum-guide-report table th,
    .quorum-guide-report table td {
        border: 1px solid #333;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }
    .quorum-guide-report table th {
        background-color: #f3f4f6;
        font-weight: bold;
    }
    .quorum-guide-report table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Report Generation</h2>
        <p class="text-sm text-gray-600 mt-1">Generate comprehensive reports with advanced search and filtering options</p>
    </div>

    <!-- Search Form -->
    <div class="filter-section">
        <form id="reportForm" method="GET" action="{{ route('admin.report-generation.search') }}">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Report Type</label>
                <select name="report_type" id="report_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                    {{-- Hidden for now --}}
                    {{-- <option value="notices" {{ (request('report_type') == 'notices') ? 'selected' : '' }}>Notices</option> --}}
                    {{-- <option value="announcements" {{ (request('report_type') == 'announcements') ? 'selected' : '' }}>Announcements</option> --}}
                    {{-- <option value="board_regulations" {{ (request('report_type') == 'board_regulations') ? 'selected' : '' }}>Board Regulations</option> --}}
                    {{-- <option value="board_resolutions" {{ (request('report_type') == 'board_resolutions') ? 'selected' : '' }}>Board Resolutions</option> --}}
                    {{-- <option value="referendums" {{ (request('report_type') == 'referendums') ? 'selected' : '' }}>Referendums</option> --}}
                    {{-- <option value="agenda_requests" {{ (request('report_type') == 'agenda_requests') ? 'selected' : '' }}>Agenda Requests</option> --}}
                    {{-- <option value="reference_materials" {{ (request('report_type') == 'reference_materials') ? 'selected' : '' }}>Reference Materials</option> --}}
                    {{-- <option value="attendance_confirmations" {{ (request('report_type') == 'attendance_confirmations') ? 'selected' : '' }}>Attendance Confirmations</option> --}}
                    <option value="quorum_guide" {{ (request('report_type') == 'quorum_guide') ? 'selected' : '' }}>Quorum Guide</option>
                    <option value="summary_regular_meeting" {{ (request('report_type') == 'summary_regular_meeting') ? 'selected' : '' }}>Summary of Regular Meeting</option>
                    <option value="summary_regular_meeting_by_title" {{ (request('report_type') == 'summary_regular_meeting_by_title') ? 'selected' : '' }}>Summary of Regular Meeting by Title</option>
                </select>
            </div>

            <div class="filter-row">
                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search in title/description..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                </div>
            </div>

            <!-- Dynamic filters based on report type -->
            <div id="dynamicFilters">
                <!-- Notices filters -->
                <div class="filter-row notices-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice Type</label>
                        <select name="notice_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Types</option>
                            <option value="Notice of Meeting" {{ request('notice_type') == 'Notice of Meeting' ? 'selected' : '' }}>Notice of Meeting</option>
                            <option value="Agenda" {{ request('notice_type') == 'Agenda' ? 'selected' : '' }}>Agenda</option>
                            <option value="Board Issuances" {{ request('notice_type') == 'Board Issuances' ? 'selected' : '' }}>Board Issuances</option>
                            <option value="Other Matters" {{ request('notice_type') == 'Other Matters' ? 'selected' : '' }}>Other Matters</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Meeting Type</label>
                        <select name="meeting_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Types</option>
                            <option value="onsite" {{ request('meeting_type') == 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="online" {{ request('meeting_type') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ request('meeting_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                </div>

                <!-- Announcements filters -->
                <div class="filter-row announcements-filters" style="display: none;">
                    <!-- No additional filters for announcements -->
                </div>

                <!-- Board Regulations filters -->
                <div class="filter-row board_regulations-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Uploaded By</label>
                        <select name="uploaded_by" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('uploaded_by') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Board Resolutions filters -->
                <div class="filter-row board_resolutions-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Uploaded By</label>
                        <select name="uploaded_by" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('uploaded_by') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Referendums filters -->
                <div class="filter-row referendums-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                <!-- Agenda Requests filters -->
                <div class="filter-row agenda_requests-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice</label>
                        <select name="notice_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Notices</option>
                            @foreach($notices as $notice)
                                <option value="{{ $notice->id }}" {{ request('notice_id') == $notice->id ? 'selected' : '' }}>{{ Str::limit($notice->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>User</label>
                        <select name="user_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Reference Materials filters -->
                <div class="filter-row reference_materials-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice</label>
                        <select name="notice_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Notices</option>
                            @foreach($notices as $notice)
                                <option value="{{ $notice->id }}" {{ request('notice_id') == $notice->id ? 'selected' : '' }}>{{ Str::limit($notice->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>User</label>
                        <select name="user_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Attendance Confirmations filters -->
                <div class="filter-row attendance_confirmations-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice</label>
                        <select name="notice_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Notices</option>
                            @foreach($notices as $notice)
                                <option value="{{ $notice->id }}" {{ (string)request('notice_id') === (string)$notice->id ? 'selected' : '' }}>{{ Str::limit($notice->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>User</label>
                        <select name="user_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string)request('user_id') === (string)$user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">All Statuses</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>

                <!-- Quorum Guide filters -->
                <div class="filter-row quorum_guide-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice of Meeting</label>
                        <select name="notice_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">Select Notice of Meeting</option>
                            @foreach($nomNotices as $notice)
                                <option value="{{ $notice->id }}" {{ request('notice_id') == $notice->id ? 'selected' : '' }}>
                                    {{ $notice->title }} 
                                    @if($notice->meeting_date)
                                        ({{ $notice->meeting_date->format('M d, Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Summary of Regular Meeting filters -->
                <div class="filter-row summary_regular_meeting-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Year</label>
                        <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" required>
                            <option value="">Select Year</option>
                            @if(isset($availableYears) && count($availableYears) > 0)
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Summary of Regular Meeting by Title filters -->
                <div class="filter-row summary_regular_meeting_by_title-filters" style="display: none;">
                    <div class="filter-group">
                        <label>Notice Title</label>
                        <select name="notice_title_id" id="notice_title_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                            <option value="">Select Notice Title</option>
                            @php
                                $boardIssuancesNotices = \App\Models\Notice::where('notice_type', 'Board Issuances')
                                    ->orderBy('meeting_date', 'desc')
                                    ->get();
                            @endphp
                            @foreach($boardIssuancesNotices as $notice)
                                <option value="{{ $notice->id }}" {{ request('notice_title_id') == $notice->id ? 'selected' : '' }}>
                                    {{ $notice->title }}
                                    @if($notice->meeting_date)
                                        ({{ $notice->meeting_date->format('M d, Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="submit" class="px-6 py-2.5 bg-[#055498] text-white rounded-lg font-medium hover:bg-[#123a60] transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <button type="button" onclick="resetForm()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
                @php
                    $canPrint = false;
                    if (isset($results) && $results->count() > 0) {
                        $canPrint = true;
                    } elseif (isset($reportType) && in_array($reportType, ['quorum_guide', 'summary_regular_meeting', 'summary_regular_meeting_by_title'])) {
                        $canPrint = true;
                    }
                @endphp
                @if($canPrint)
                    <button type="button" onclick="printReport()" class="px-6 py-2.5 text-white rounded-lg font-medium hover:opacity-90 transition-colors" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-print mr-2"></i>Print Report
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @php
        $showResults = false;
        if (isset($results)) {
            $showResults = true;
        } elseif (isset($reportType) && ($reportType === 'quorum_guide' || $reportType === 'summary_regular_meeting')) {
            $showResults = true;
        }
    @endphp
    @if($showResults)
        <div class="results-section">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">
                    @if(isset($reportType) && $reportType === 'quorum_guide')
                        Quorum Guide Report
                    @elseif(isset($reportType) && $reportType === 'summary_regular_meeting')
                        Summary of Regular Meeting Report
                    @elseif(isset($reportType) && $reportType === 'summary_regular_meeting_by_title')
                        Summary of Regular Meeting by Title Report
                    @else
                        Results ({{ isset($results) ? $results->count() : 0 }} {{ Str::plural('record', isset($results) ? $results->count() : 0) }})
                    @endif
                </h3>
            </div>

            @php
                $hasResults = false;
                if (isset($results) && $results->count() > 0) {
                    $hasResults = true;
                } elseif (isset($reportType) && in_array($reportType, ['quorum_guide', 'summary_regular_meeting', 'summary_regular_meeting_by_title'])) {
                    $hasResults = true;
                }
            @endphp
            @if($hasResults)
                <div id="reportResults">
                    @include('admin.report-generation.results', ['results' => $results ?? collect(), 'reportType' => $reportType ?? ''])
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No records found matching your search criteria</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Show/hide filters based on report type
    document.getElementById('report_type').addEventListener('change', function() {
        const reportType = this.value;
        // Hide all filter groups and remove required attributes
        document.querySelectorAll('.filter-row').forEach(row => {
            row.style.display = 'none';
            row.querySelectorAll('select[required], input[required]').forEach(field => {
                field.removeAttribute('required');
            });
        });
        // Show relevant filter group
        // Note: We don't add required attributes to filter fields - they should all be optional
        const relevantFilter = document.querySelector('.' + reportType + '-filters');
        if (relevantFilter) {
            relevantFilter.style.display = 'grid';
        }
    });
    
    // Remove required from hidden fields before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        // Remove required from all hidden filter fields
        document.querySelectorAll('.filter-row[style*="display: none"] select[required], .filter-row[style*="display: none"] input[required]').forEach(field => {
            field.removeAttribute('required');
        });
        // Also ensure all visible filter fields are optional (except for special report types)
        const reportType = document.getElementById('report_type').value;
        if (!['quorum_guide', 'summary_regular_meeting', 'summary_regular_meeting_by_title'].includes(reportType)) {
            document.querySelectorAll('.filter-row:not([style*="display: none"]) select[required], .filter-row:not([style*="display: none"]) input[required]').forEach(field => {
                field.removeAttribute('required');
            });
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const reportType = document.getElementById('report_type').value;
        // Hide all filter groups first
        document.querySelectorAll('.filter-row').forEach(row => {
            row.style.display = 'none';
            row.querySelectorAll('select[required], input[required]').forEach(field => {
                field.removeAttribute('required');
            });
        });
        // Show relevant filter group
        const relevantFilter = document.querySelector('.' + reportType + '-filters');
        if (relevantFilter) {
            relevantFilter.style.display = 'grid';
        }
        
        // Preserve selected values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const noticeId = urlParams.get('notice_id');
        const userId = urlParams.get('user_id');
        
        if (noticeId) {
            const noticeSelect = document.querySelector('select[name="notice_id"]');
            if (noticeSelect) {
                noticeSelect.value = noticeId;
            }
        }
        
        if (userId) {
            const userSelect = document.querySelector('select[name="user_id"]');
            if (userSelect) {
                userSelect.value = userId;
            }
        }
    });

    function resetForm() {
        document.getElementById('reportForm').reset();
        document.querySelectorAll('.filter-row').forEach(row => {
            row.style.display = 'none';
        });
    }

    function printReport() {
        const reportType = document.getElementById('report_type').value;
        @if(isset($results) && $results->count() > 0)
            @if(isset($reportType) && $reportType === 'quorum_guide')
                @php
                    $quorumData = $results->first();
                    $printData = [
                        'nom_notice' => [
                            'title' => $quorumData['nom_notice']->title ?? null,
                            'meeting_date' => $quorumData['nom_notice']->meeting_date ? $quorumData['nom_notice']->meeting_date->toDateString() : null,
                            'meeting_type' => $quorumData['nom_notice']->meeting_type ?? null,
                            'description' => $quorumData['nom_notice']->description ?? null,
                        ],
                        'agenda_notice' => $quorumData['agenda_notice'] ? [
                            'meeting_date' => $quorumData['agenda_notice']->meeting_date ? $quorumData['agenda_notice']->meeting_date->toDateString() : null,
                            'created_at' => $quorumData['agenda_notice']->created_at ? $quorumData['agenda_notice']->created_at->toDateTimeString() : null,
                        ] : null,
                        'attendees_by_agency' => array_map(function($agency) {
                            return [
                                'agency_name' => $agency['agency_name'] ?? 'Unknown Agency',
                                'board_members' => array_map(function($member) {
                                    return [
                                        'pre_nominal_title' => $member->pre_nominal_title ?? '',
                                        'first_name' => $member->first_name ?? '',
                                        'last_name' => $member->last_name ?? '',
                                        'middle_initial' => $member->middle_initial ?? '',
                                        'post_nominal_title' => $member->post_nominal_title ?? '',
                                        'designation' => $member->designation ?? '',
                                    ];
                                }, $agency['board_members'] ?? []),
                                'other_attendees' => array_map(function($attendee) {
                                    // Check if it's a CC email (array) or registered user (object)
                                    if (is_array($attendee) && isset($attendee['type']) && $attendee['type'] === 'cc_email') {
                                        return [
                                            'type' => 'cc_email',
                                            'name' => $attendee['name'] ?? '',
                                            'position' => $attendee['position'] ?? '',
                                        ];
                                    } else {
                                        return [
                                            'type' => 'user',
                                            'pre_nominal_title' => $attendee->pre_nominal_title ?? '',
                                            'first_name' => $attendee->first_name ?? '',
                                            'last_name' => $attendee->last_name ?? '',
                                            'middle_initial' => $attendee->middle_initial ?? '',
                                            'post_nominal_title' => $attendee->post_nominal_title ?? '',
                                            'designation' => $attendee->designation ?? '',
                                        ];
                                    }
                                }, $agency['other_attendees'] ?? []),
                                'remarks' => $agency['remarks'] ?? '',
                            ];
                        }, $quorumData['attendees_by_agency'] ?? [])
                    ];
                @endphp
                const results = [@json($printData)];
            @elseif(isset($reportType) && $reportType === 'summary_regular_meeting')
                @php
                    $summaryData = $results->first();
                    $printData = [
                        'year' => $summaryData['year'] ?? date('Y'),
                        'notices' => array_map(function($item) {
                            return [
                                'notice' => [
                                    'title' => $item['notice']->title ?? '',
                                    'meeting_date' => $item['notice']->meeting_date ? $item['notice']->meeting_date->toDateString() : null,
                                    'no_of_attendees' => $item['notice']->no_of_attendees ?? null,
                                ],
                                'regulations' => array_map(function($reg) {
                                    return [
                                        'title' => $reg['title'] ?? '',
                                        'description' => $reg['description'] ?? '',
                                        'version' => $reg['version'] ?? '',
                                    ];
                                }, $item['regulations'] ?? []),
                                'resolutions' => array_map(function($res) {
                                    return [
                                        'title' => $res['title'] ?? '',
                                        'description' => $res['description'] ?? '',
                                        'resolution_number' => $res['resolution_number'] ?? '',
                                        'version' => $res['version'] ?? '',
                                    ];
                                }, $item['resolutions'] ?? []),
                            ];
                        }, $summaryData['notices'] ?? []),
                        'total_meetings' => $summaryData['total_meetings'] ?? 0,
                        'total_regulations' => $summaryData['total_regulations'] ?? 0,
                        'total_resolutions' => $summaryData['total_resolutions'] ?? 0,
                    ];
                @endphp
                const results = [@json($printData)];
            @elseif(isset($reportType) && $reportType === 'summary_regular_meeting_by_title')
                @php
                    $summaryData = $results->first();
                    $printData = [
                        'notice' => [
                            'title' => $summaryData['notice']['title'] ?? '',
                            'meeting_date' => $summaryData['notice']['meeting_date'] ? \Carbon\Carbon::parse($summaryData['notice']['meeting_date'])->toDateString() : null,
                        ],
                        'rows' => array_map(function($row) {
                            return [
                                'regulation' => $row['regulation'] ? [
                                    'title' => $row['regulation']['title'] ?? '',
                                    'description' => $row['regulation']['description'] ?? '',
                                ] : null,
                                'resolution' => $row['resolution'] ? [
                                    'title' => $row['resolution']['title'] ?? '',
                                    'description' => $row['resolution']['description'] ?? '',
                                    'resolution_number' => $row['resolution']['resolution_number'] ?? '',
                                ] : null,
                            ];
                        }, $summaryData['rows'] ?? []),
                        'total_regulations' => $summaryData['total_regulations'] ?? 0,
                        'total_resolutions' => $summaryData['total_resolutions'] ?? 0,
                    ];
                @endphp
                const results = [@json($printData)];
            @else
                @php
                    $printData = $results->map(function($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title ?? null,
                            'description' => $item->description ?? null,
                            'notice_type' => $item->notice_type ?? null,
                            'meeting_type' => $item->meeting_type ?? null,
                            'meeting_date' => $item->meeting_date ?? null,
                            'status' => $item->status ?? null,
                            'version' => $item->version ?? null,
                            'effective_date' => $item->effective_date ?? null,
                            'created_at' => $item->created_at ? $item->created_at->toDateTimeString() : null,
                            'creator' => $item->creator ? ['first_name' => $item->creator->first_name, 'last_name' => $item->creator->last_name] : null,
                            'uploader' => $item->uploader ? ['first_name' => $item->uploader->first_name, 'last_name' => $item->uploader->last_name] : null,
                            'user' => $item->user ? ['first_name' => $item->user->first_name, 'last_name' => $item->user->last_name] : null,
                            'notice' => $item->notice ? ['title' => $item->notice->title] : null,
                            'declined_reason' => $item->declined_reason ?? null,
                        ];
                    });
                @endphp
                const results = @json($printData);
            @endif
        @else
            const results = [];
        @endif
        const filters = @json(isset($filters) ? $filters : []);
        
        const printContent = generatePrintContent(results, reportType, filters);
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 250);
        };
    }

    function generatePrintContent(results, reportType, filters) {
        const currentDate = new Date().toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const currentTime = new Date().toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        const reportTypeNames = {
            'notices': 'Notices',
            'announcements': 'Announcements',
            'board_regulations': 'Board Regulations',
            'board_resolutions': 'Board Resolutions',
            'referendums': 'Referendums',
            'agenda_requests': 'Agenda Requests',
            'reference_materials': 'Reference Materials',
            'attendance_confirmations': 'Attendance Confirmations',
            'quorum_guide': 'Quorum Guide'
        };

        let filterInfo = '';
        // Hide filter info for Quorum Guide
        if (reportType !== 'quorum_guide') {
            filterInfo = '<div class="info-section">';
            filterInfo += '<div class="info-row"><span class="info-label">Generated On:</span><span class="info-value">' + currentDate + ' at ' + currentTime + '</span></div>';
            filterInfo += '<div class="info-row"><span class="info-label">Generated By:</span><span class="info-value">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }} ({{ Auth::user()->email }})</span></div>';
            filterInfo += '<div class="info-row"><span class="info-label">Report Type:</span><span class="info-value">' + reportTypeNames[reportType] + '</span></div>';
            filterInfo += '<div class="info-row"><span class="info-label">Total Records:</span><span class="info-value">' + results.length + '</span></div>';
            
            if (filters.date_from || filters.date_to || filters.search || Object.keys(filters).some(k => k !== 'report_type' && filters[k])) {
                filterInfo += '<div class="info-row" style="margin-top: 10px;"><span class="info-label" style="font-weight: bold; color: #055498;">Applied Filters:</span><span class="info-value"></span></div>';
                if (filters.date_from) {
                    filterInfo += '<div class="info-row"><span class="info-label">From Date:</span><span class="info-value">' + new Date(filters.date_from).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</span></div>';
                }
                if (filters.date_to) {
                    filterInfo += '<div class="info-row"><span class="info-label">To Date:</span><span class="info-value">' + new Date(filters.date_to).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</span></div>';
                }
                if (filters.search) {
                    filterInfo += '<div class="info-row"><span class="info-label">Search Term:</span><span class="info-value">' + escapeHtml(filters.search) + '</span></div>';
                }
            }
            filterInfo += '</div>';
        }

        let tableContent = generateTableContent(results, reportType);

        return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${reportTypeNames[reportType]} Report</title>
    <style>
            @page {
                size: ${reportType === 'quorum_guide' || reportType === 'summary_regular_meeting' || reportType === 'summary_regular_meeting_by_title' ? 'A4 portrait' : 'landscape'};
                margin: 15mm;
                margin-header: 0;
                margin-footer: 0;
            }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin-left: 30px;
            margin-right: 30px;
        }
        
        .header {
            color: #000000 !important;
            padding: 0 20px 20px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .header img {
            max-width: 400px;
            height: auto;
            margin-top: 0;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            margin-top: 5px;
            font-weight: bold;
            color: #000000 !important;
        }
        
        .header p {
            font-size: 11px;
            opacity: 1;
            margin-bottom: 5px;
            color: #000000 !important;
        }
        
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #055498;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        thead th {
            background-color: #055498 !important;
            color: white !important;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #033d6b;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #055498;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        @media print {
            body {
                margin: 0;
            }
            
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            thead th {
                background-color: #055498 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    ${reportType === 'quorum_guide' ? `<div class="header">
        <img src="${window.location.origin}/images/ddbheader.png" alt="DDB Header" onerror="this.style.display='none';" style="max-width: 250px; height: auto;">
    </div>` : reportType === 'summary_regular_meeting' ? `<div class="header" style="text-align: center; margin-bottom: 20px;">
        <img src="${window.location.origin}/images/ddbheader.png" alt="DDB Header" onerror="this.style.display='none';" style="max-width: 250px; height: auto; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
        <h1 style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">SUMMARY OF REGULAR MEETING OF</h1>
        <h1 style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">THE DANGEROUS DRUGS BOARD</h1>
        <p style="font-size: 12px; font-weight: bold;">YEAR: ${filters.year || new Date().getFullYear()}</p>
    </div>` : reportType === 'summary_regular_meeting_by_title' && results.length > 0 ? `<div class="header" style="text-align: center; margin-bottom: 20px;">
        <img src="${window.location.origin}/images/ddbheader.png" alt="DDB Header" onerror="this.style.display='none';" style="max-width: 250px; height: auto; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
        <h1 style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">SUMMARY OF ${results[0].notice ? results[0].notice.title.toUpperCase() : ''}</h1>
        ${results[0].notice && results[0].notice.meeting_date ? `<p style="font-size: 12px; font-weight: bold; margin-bottom: 0;">${new Date(results[0].notice.meeting_date).toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' })}</p>` : ''}
    </div>` : `<div class="header">
        <img src="${window.location.origin}/images/ddbheader.png" alt="DDB Header" onerror="this.style.display='none';">
        <h1>${reportTypeNames[reportType]} Report</h1>
        <p>Board Member Portal - Report Generation</p>
    </div>`}
    
    ${reportType !== 'summary_regular_meeting' && reportType !== 'summary_regular_meeting_by_title' ? filterInfo : ''}
    
    ${tableContent}
    
    <div class="footer">
        <p>This report was generated on ${currentDate} at ${currentTime} from the Board Member Portal System</p>
        ${reportType === 'summary_regular_meeting' && results.length > 0 ? 
            `<p style="margin-top: 5px;">Report contains ${results[0].total_meetings || 0} meeting(s) based on search criteria</p>` :
            `<p style="margin-top: 5px;">Report contains ${results.length} record(s) based on search criteria</p>`
        }
    </div>
</body>
</html>`;
    }

    function generateTableContent(results, reportType) {
        if (results.length === 0) {
            return '<div class="no-data"><p>No records found.</p></div>';
        }

        // Special handling for Quorum Guide
        if (reportType === 'quorum_guide') {
            return generateQuorumGuideContent(results[0]);
        }
        
        // Special handling for Summary of Regular Meeting
        if (reportType === 'summary_regular_meeting') {
            return generateSummaryRegularMeetingContent(results[0]);
        }
        
        // Special handling for Summary of Regular Meeting by Title
        if (reportType === 'summary_regular_meeting_by_title') {
            if (results.length > 0 && results[0]) {
                return generateSummaryRegularMeetingByTitleContent(results[0]);
            }
            return '<div class="no-data"><p>No records found.</p></div>';
        }

        let table = '<table><thead><tr>';
        let headers = getTableHeaders(reportType);
        headers.forEach(header => {
            table += '<th>' + header + '</th>';
        });
        table += '</tr></thead><tbody>';

        results.forEach(item => {
            table += '<tr>';
            let cells = getTableCells(item, reportType);
            cells.forEach(cell => {
                table += '<td>' + escapeHtml(cell) + '</td>';
            });
            table += '</tr>';
        });

        table += '</tbody></table>';
        return table;
    }

    function generateQuorumGuideContent(quorumData) {
        const nomNotice = quorumData.nom_notice || {};
        const agendaNotice = quorumData.agenda_notice || null;
        const attendeesByAgency = quorumData.attendees_by_agency || [];
        
        let content = '<div style="text-align: center; margin-bottom: 15px;">';
        content += '<h2 style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">' + escapeHtml(nomNotice.title ? nomNotice.title.toUpperCase() : '') + '</h2>';
        if (nomNotice.meeting_date) {
            const meetingDate = new Date(nomNotice.meeting_date);
            const formattedDate = meetingDate.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
            content += '<p style="font-size: 16px; margin-bottom: 10px;">' + formattedDate + '</p>';
        }
        content += '</div>';

        content += '<div style="margin-bottom: 15px;">';
        content += '<h3 style="font-size: 18px; font-weight: bold; margin-bottom: 8px; text-align: center;">QUORUM GUIDE</h3>';
        
        // Extract meeting number from title
        let previousMeetingNumber = null;
        if (nomNotice.title) {
            const match = nomNotice.title.match(/(\d+)(?:st|nd|rd|th)/i);
            if (match) {
                const currentNumber = parseInt(match[1]);
                const previousNumber = currentNumber - 1;
                let suffix = 'th';
                if (previousNumber % 100 < 10 || previousNumber % 100 > 20) {
                    const lastDigit = previousNumber % 10;
                    if (lastDigit === 1) suffix = 'st';
                    else if (lastDigit === 2) suffix = 'nd';
                    else if (lastDigit === 3) suffix = 'rd';
                }
                previousMeetingNumber = previousNumber + suffix;
            }
        }
        
        // Notice of Meeting info
        content += '<div style="margin-bottom: 8px;">';
        content += '<p style="font-weight: bold; margin-bottom: 3px;">Sending of the Notice of Meeting and Minutes of the Previous' + 
                   (previousMeetingNumber ? ' (' + previousMeetingNumber + ')' : '') + ' Meeting</p>';
        // Date second
        if (nomNotice.meeting_date) {
            const meetingDate = new Date(nomNotice.meeting_date);
            const formattedDate = meetingDate.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
            content += '<p style="margin-left: 20px; margin-bottom: 0;">• ' + escapeHtml(formattedDate) + '</p>';
        }
        content += '</div>';

        // Agenda info
        if (agendaNotice) {
            content += '<div style="margin-bottom: 8px;">';
            content += '<p style="font-weight: bold; margin-bottom: 3px;">Sending of Provisional Agenda</p>';
            if (agendaNotice.meeting_date) {
                const agendaDate = new Date(agendaNotice.meeting_date);
                const formattedDate = agendaDate.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
                content += '<p style="margin-left: 20px; margin-bottom: 0;">• ' + escapeHtml(formattedDate) + '</p>';
            } else if (agendaNotice.created_at) {
                const agendaDate = new Date(agendaNotice.created_at);
                const formattedDate = agendaDate.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
                content += '<p style="margin-left: 20px; margin-bottom: 0;">• ' + escapeHtml(formattedDate) + '</p>';
            }
            content += '</div>';
        }
        content += '</div>';

        // Table
        let table = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        table += '<thead><tr>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 20%;">AGENCY</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 30%;">ATTENDEES WHO ARE MEMBERS OF THE BOARD</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 35%;">Other Attendees</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 15%;">Remarks</th>';
        table += '</tr></thead><tbody>';

        let rowNumber = 1;
        attendeesByAgency.forEach(agencyData => {
            const boardMembers = agencyData.board_members || [];
            const otherAttendees = agencyData.other_attendees || [];
            const agencyName = agencyData.agency_name || 'Unknown Agency';
            const remarks = agencyData.remarks || '';

            table += '<tr>';
            
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
            table += '<div style="font-weight: bold;">' + rowNumber + ' - ' + escapeHtml(agencyName.toUpperCase()) + '</div>';
            table += '</td>';

            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            if (boardMembers.length > 0) {
                boardMembers.forEach((member, index) => {
                    const title = member.pre_nominal_title || '';
                    const firstName = member.first_name || '';
                    const lastName = member.last_name || '';
                    const middleInitial = member.middle_initial || '';
                    const postNominal = member.post_nominal_title || '';
                    const designation = member.designation || '';
                    
                    let fullName = (title ? title + ' ' : '') + firstName.toUpperCase() + 
                                   (middleInitial ? ' ' + middleInitial.toUpperCase() + '.' : '') + 
                                   ' ' + lastName.toUpperCase() + 
                                   (postNominal ? ' ' + postNominal : '');
                    table += '<div style="font-weight: bold;">' + escapeHtml(fullName.trim()) + '</div>';
                    if (designation) {
                        table += '<div style="font-size: 8px; color: #666;">' + escapeHtml(designation) + '</div>';
                    }
                    if (index < boardMembers.length - 1) {
                        table += '<br>';
                    }
                });
            }
            table += '</td>';

            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            if (otherAttendees.length > 0) {
                otherAttendees.forEach((attendee, index) => {
                    // Check if it's a CC email or registered user
                    if (attendee.type === 'cc_email') {
                        // CC email user
                        const fullName = (attendee.name || '').toUpperCase();
                        const position = attendee.position || '';
                        table += '<div style="font-weight: bold;">' + escapeHtml(fullName) + '</div>';
                        if (position) {
                            table += '<div style="font-size: 8px; color: #666;">' + escapeHtml(position) + '</div>';
                        }
                    } else {
                        // Registered user
                        const title = attendee.pre_nominal_title || '';
                        const firstName = attendee.first_name || '';
                        const lastName = attendee.last_name || '';
                        const middleInitial = attendee.middle_initial || '';
                        const postNominal = attendee.post_nominal_title || '';
                        const designation = attendee.designation || '';
                        
                        let fullName = (title ? title + ' ' : '') + firstName.toUpperCase() + 
                                       (middleInitial ? ' ' + middleInitial.toUpperCase() + '.' : '') + 
                                       ' ' + lastName.toUpperCase() + 
                                       (postNominal ? ' ' + postNominal : '');
                        table += '<div style="font-weight: bold;">' + escapeHtml(fullName.trim()) + '</div>';
                        if (designation) {
                            table += '<div style="font-size: 8px; color: #666;">' + escapeHtml(designation) + '</div>';
                        }
                    }
                    if (index < otherAttendees.length - 1) {
                        table += '<br>';
                    }
                });
            }
            table += '</td>';

            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
            table += escapeHtml(remarks);
            table += '</td>';

            table += '</tr>';
            
            rowNumber++;
        });

        table += '</tbody></table>';
        return content + table;
    }

    function generateSummaryRegularMeetingContent(summaryData) {
        const notices = summaryData.notices || [];
        const totalMeetings = summaryData.total_meetings || 0;
        const totalRegulations = summaryData.total_regulations || 0;
        const totalResolutions = summaryData.total_resolutions || 0;
        
        let table = '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
        table += '<thead><tr>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 5%;">NO.</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 25%;">MEETING TITLE</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 15%;">DATE OF MEETING</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 10%;">NO. OF ATTENDEES</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 22.5%;">BOARD REGULATIONS</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 22.5%;">BOARD RESOLUTIONS</th>';
        table += '</tr></thead><tbody>';
        
        let rowNumber = 1;
        notices.forEach(item => {
            const notice = item.notice || {};
            const regulations = item.regulations || [];
            const resolutions = item.resolutions || [];
            
            table += '<tr>';
            
            // NO. column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            table += '<div style="font-weight: bold;">' + rowNumber + '</div>';
            table += '</td>';
            
            // MEETING TITLE column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            table += escapeHtml(notice.title || '');
            table += '</td>';
            
            // DATE OF MEETING column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            if (notice.meeting_date) {
                const meetingDate = new Date(notice.meeting_date);
                const formattedDate = meetingDate.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
                table += escapeHtml(formattedDate);
            } else {
                table += '—';
            }
            table += '</td>';
            
            // NO. OF ATTENDEES column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: middle;">';
            table += (notice.no_of_attendees || '—');
            table += '</td>';
            
            // BOARD REGULATIONS column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
            if (regulations.length > 0) {
                regulations.forEach((reg, index) => {
                    table += '<div style="margin-bottom: 12px;">';
                    if (reg.title && reg.title.trim() !== '') {
                        table += '<div style="font-weight: bold; margin-bottom: 4px;">' + escapeHtml(reg.title) + '</div>';
                    }
                    if (reg.description && reg.description.trim() !== '') {
                        table += '<div style="font-size: 11px; line-height: 1.4; margin-top: 2px;">' + escapeHtml(reg.description) + '</div>';
                    }
                    table += '</div>';
                });
            } else {
                table += '';
            }
            table += '</td>';
            
            // BOARD RESOLUTIONS column
            table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
            if (resolutions.length > 0) {
                resolutions.forEach((res, index) => {
                    table += '<div style="margin-bottom: 12px;">';
                    // Check if title contains resolution number format, otherwise use index
                    let resolutionLabel = 'Board Resolution No. ' + (index + 1);
                    if (res.title && res.title.match(/Board Resolution No\./i)) {
                        resolutionLabel = res.title;
                    } else if (res.resolution_number && res.resolution_number.trim() !== '') {
                        resolutionLabel = res.resolution_number;
                    }
                    table += '<div style="font-weight: bold; margin-bottom: 4px;">' + escapeHtml(resolutionLabel) + '</div>';
                    // Always show description if available
                    if (res.description && res.description.trim() !== '') {
                        table += '<div style="font-size: 11px; line-height: 1.4; margin-top: 2px;">' + escapeHtml(res.description) + '</div>';
                    } else if (res.title && res.title.trim() !== '' && !res.title.match(/Board Resolution No\./i)) {
                        table += '<div style="font-size: 11px; line-height: 1.4; margin-top: 2px;">' + escapeHtml(res.title) + '</div>';
                    }
                    table += '</div>';
                });
            } else {
                table += '';
            }
            table += '</td>';
            
            table += '</tr>';
            
            rowNumber++;
        });
        
        table += '</tbody></table>';
        
        // Totals below table
        let totals = '<div style="margin-top: 20px; text-align: left;">';
        totals += '<div style="font-weight: bold; margin-bottom: 8px;">Total no. of Meetings: ' + totalMeetings + '</div>';
        totals += '<div style="font-weight: bold; margin-bottom: 8px;">Total no. of Approved Board Regulations: ' + totalRegulations + '</div>';
        totals += '<div style="font-weight: bold;">Total no. of Approved Resolutions: ' + totalResolutions + '</div>';
        totals += '</div>';
        
        return table + totals;
    }

    function generateSummaryRegularMeetingByTitleContent(summaryData) {
        if (!summaryData) {
            return '<p>No data available</p>';
        }
        const notice = summaryData.notice || {};
        const rows = summaryData.rows || [];
        const totalRegulations = summaryData.total_regulations || 0;
        const totalResolutions = summaryData.total_resolutions || 0;
        
        // Table
        let table = '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
        table += '<thead>';
        table += '<tr>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left; width: 10%;">NO.</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: center; width: 90%;" colspan="4">APPROVED ISSUANCES</th>';
        table += '</tr>';
        table += '<tr>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: left;"></th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: center; width: 22.5%;" colspan="2">BOARD REGULATIONS</th>';
        table += '<th style="border: 1px solid #333; padding: 8px; background-color: #055498; color: white; text-align: center; width: 22.5%;" colspan="2">BOARD RESOLUTIONS</th>';
        table += '</tr>';
        table += '</thead><tbody>';
        
        if (!rows || rows.length === 0) {
            table += '<tr><td colspan="5" style="border: 1px solid #333; padding: 8px; text-align: center;">No data available</td></tr>';
        } else {
            rows.forEach((row, index) => {
                if (!row) return;
                
                table += '<tr>';
                
                // NO. column
                table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
                table += '<div style="font-weight: bold;">' + (index + 1) + '.</div>';
                table += '</td>';
                
                // BOARD REGULATIONS - Title column (left)
                table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
                if (row.regulation && row.regulation.title) {
                    table += '<div style="font-weight: bold; margin-bottom: 4px;">' + escapeHtml(row.regulation.title) + '</div>';
                }
                table += '</td>';
                
                // BOARD REGULATIONS - Description column (right)
                table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
                if (row.regulation && row.regulation.description) {
                    table += '<div style="font-size: 11px; line-height: 1.4;">' + escapeHtml(row.regulation.description) + '</div>';
                }
                table += '</td>';
                
                // BOARD RESOLUTIONS - Title column (left)
                table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
                if (row.resolution && row.resolution.title) {
                    table += '<div style="font-weight: bold; margin-bottom: 4px;">' + escapeHtml(row.resolution.title) + '</div>';
                }
                table += '</td>';
                
                // BOARD RESOLUTIONS - Description column (right)
                table += '<td style="border: 1px solid #333; padding: 8px; vertical-align: top;">';
                if (row.resolution && row.resolution.description) {
                    table += '<div style="font-size: 11px; line-height: 1.4;">' + escapeHtml(row.resolution.description) + '</div>';
                }
                table += '</td>';
                
                table += '</tr>';
            });
        }
        
        table += '</tbody></table>';
        
        // Totals below table
        let totals = '<div style="margin-top: 20px; text-align: left;">';
        totals += '<div style="font-weight: bold; margin-bottom: 8px;">Total no. of Approved Board Regulations: ' + totalRegulations + '</div>';
        totals += '<div style="font-weight: bold;">Total no. of Approved Resolutions: ' + totalResolutions + '</div>';
        totals += '</div>';
        
        return table + totals;
    }

    function getTableHeaders(reportType) {
        const headers = {
            'notices': ['Title', 'Type', 'Meeting Type', 'Meeting Date', 'Created By', 'Created At'],
            'announcements': ['Title', 'Description', 'Created By', 'Created At'],
            'board_regulations': ['Title', 'Version', 'Effective Date', 'Uploaded By', 'Created At'],
            'board_resolutions': ['Title', 'Version', 'Effective Date', 'Uploaded By', 'Created At'],
            'referendums': ['Title', 'Status', 'Created By', 'Created At'],
            'agenda_requests': ['Notice', 'User', 'Description', 'Status', 'Submitted At'],
            'reference_materials': ['Notice', 'User', 'Description', 'Status', 'Submitted At'],
            'attendance_confirmations': ['Notice', 'User', 'Status', 'Confirmed At']
        };
        return headers[reportType] || [];
    }

    function getTableCells(item, reportType) {
        const cells = {
            'notices': [
                item.title || '—',
                item.notice_type || '—',
                item.meeting_type || '—',
                item.meeting_date ? new Date(item.meeting_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—',
                item.creator ? (item.creator.first_name + ' ' + item.creator.last_name) : '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'announcements': [
                item.title || '—',
                (item.description ? stripHtml(item.description) : '—'),
                item.creator ? (item.creator.first_name + ' ' + item.creator.last_name) : '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'board_regulations': [
                item.title || '—',
                item.version || '—',
                item.effective_date ? new Date(item.effective_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—',
                item.uploader ? (item.uploader.first_name + ' ' + item.uploader.last_name) : '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'board_resolutions': [
                item.title || '—',
                item.version || '—',
                item.effective_date ? new Date(item.effective_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—',
                item.uploader ? (item.uploader.first_name + ' ' + item.uploader.last_name) : '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'referendums': [
                item.title || '—',
                item.status || '—',
                item.creator ? (item.creator.first_name + ' ' + item.creator.last_name) : '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'agenda_requests': [
                item.notice ? item.notice.title : '—',
                item.user ? (item.user.first_name + ' ' + item.user.last_name) : '—',
                (item.description ? item.description.substring(0, 80) + (item.description.length > 80 ? '...' : '') : '—'),
                item.status || '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'reference_materials': [
                item.notice ? item.notice.title : '—',
                item.user ? (item.user.first_name + ' ' + item.user.last_name) : '—',
                (item.description ? item.description.substring(0, 80) + (item.description.length > 80 ? '...' : '') : '—'),
                item.status || '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ],
            'attendance_confirmations': [
                item.notice ? item.notice.title : '—',
                item.user ? (item.user.first_name + ' ' + item.user.last_name) : '—',
                item.status || '—',
                item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            ]
        };
        return cells[reportType] || [];
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    function stripHtml(html) {
        if (!html) return '';
        let text = String(html);
        // Remove HTML tags
        text = text.replace(/<[^>]*>/g, '');
        // Decode common HTML entities
        const entityMap = {
            '&nbsp;': ' ',
            '&rsquo;': "'",
            '&lsquo;': "'",
            '&rdquo;': '"',
            '&ldquo;': '"',
            '&mdash;': '—',
            '&ndash;': '–',
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#039;': "'",
            '&apos;': "'"
        };
        // Replace HTML entities
        for (const entity in entityMap) {
            text = text.replace(new RegExp(entity, 'g'), entityMap[entity]);
        }
        // Replace numeric entities
        text = text.replace(/&#(\d+);/g, function(match, dec) {
            return String.fromCharCode(dec);
        });
        text = text.replace(/&#x([a-f\d]+);/gi, function(match, hex) {
            return String.fromCharCode(parseInt(hex, 16));
        });
        // Clean up extra whitespace
        text = text.replace(/\s+/g, ' ').trim();
        return text;
    }
</script>
@endpush

