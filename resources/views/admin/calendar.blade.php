@extends('admin.layout')

@section('title', 'Calendar of Activities')

@php
    $pageTitle = 'Calendar of Activities';
@endphp

@section('content')
<div class="p-3 sm:p-4 md:p-6">
    <!-- Page Title -->
    <div class="mb-3 sm:mb-4 md:mb-6">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Calendar of Activities</h2>
        <p class="text-xs sm:text-sm text-gray-600 mt-1">View meetings, announcements, and scheduled events</p>
    </div>
    
    <!-- Activities Calendar Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 md:p-6 mb-4 sm:mb-6">
        <div class="mb-3 sm:mb-4 flex flex-col gap-3 sm:gap-4">
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm sm:text-base md:text-lg font-semibold text-gray-800 mb-2 sm:mb-0">
                        <i class="fas fa-calendar-alt mr-1.5 sm:mr-2" style="color: #055498;"></i>
                        Activities Calendar
                    </h3>
                    <!-- Color Legend - Mobile Optimized -->
                    <div class="flex items-center gap-2 sm:gap-3 flex-wrap mt-2 sm:mt-0">
                        <div class="flex items-center gap-1 sm:gap-1.5">
                            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full flex-shrink-0" style="background-color: #FBD116; border: 1px solid #d4a017;"></span>
                            <span class="text-[10px] xs:text-xs sm:text-sm text-gray-600 whitespace-nowrap">Announcements</span>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-1.5">
                            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full flex-shrink-0" style="background-color: #CE2028; border: 1px solid #a01a1f;"></span>
                            <span class="text-[10px] xs:text-xs sm:text-sm text-gray-600 whitespace-nowrap">Resolutions</span>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-1.5">
                            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full flex-shrink-0" style="background-color: #055498; border: 1px solid #044080;"></span>
                            <span class="text-[10px] xs:text-xs sm:text-sm text-gray-600 whitespace-nowrap">Regulations</span>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex gap-2 sm:gap-2 flex-shrink-0">
                    <button id="printCalendarBtn" class="px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white rounded-lg transition-colors inline-flex items-center justify-center min-h-[44px] sm:min-h-0 touch-manipulation" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-print mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">Print</span>
                        <span class="xs:hidden">Print</span>
                    </button>
                    <button id="toggleFilterBtn" class="px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white rounded-lg transition-colors inline-flex items-center justify-center min-h-[44px] sm:min-h-0 touch-manipulation" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-filter mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">Filter</span>
                        <span class="xs:hidden">Filter</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Filter Panel -->
        <div id="filterPanel" class="hidden mb-3 sm:mb-4 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Event Type Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Event Type</label>
                    <select id="filterEventType" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm min-h-[44px] sm:min-h-0 touch-manipulation">
                        <option value="all">All Types</option>
                        <option value="announcement">Announcements</option>
                        <option value="resolution">Resolutions</option>
                        <option value="regulation">Regulations</option>
                        <option value="notice">Notices</option>
                    </select>
                </div>
                
                <!-- Date From Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">From Date</label>
                    <input type="date" id="filterDateFrom" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm min-h-[44px] sm:min-h-0 touch-manipulation">
                </div>

                <!-- Date To Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">To Date</label>
                    <input type="date" id="filterDateTo" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm min-h-[44px] sm:min-h-0 touch-manipulation">
                </div>
                
                <!-- Search Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Search</label>
                    <input type="text" id="filterSearch" placeholder="Search events..." class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm min-h-[44px] sm:min-h-0 touch-manipulation">
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-end gap-2 sm:gap-2 mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                <button id="clearFiltersBtn" class="px-4 py-2.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center min-h-[44px] sm:min-h-0 touch-manipulation">
                    <i class="fas fa-times mr-1.5 sm:mr-2"></i>
                    Clear Filters
                </button>
                <button id="applyFiltersBtn" class="px-4 py-2.5 sm:py-2 text-xs sm:text-sm font-medium text-white rounded-lg transition-colors inline-flex items-center justify-center min-h-[44px] sm:min-h-0 touch-manipulation" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-check mr-1.5 sm:mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </div>
        
        <div id="calendar" class="calendar-container"></div>
    </div>
</div>
@endsection
    
@push('scripts')
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script>
    // Initialize Calendar for Admin Calendar Page
    (function() {
        // Get current user information for print report
        const currentUserInfo = {
            firstName: @json(Auth::user()->first_name ?? ''),
            lastName: @json(Auth::user()->last_name ?? ''),
            email: @json(Auth::user()->email ?? '')
        };
        
        // Store all events (will be loaded from API)
        let allEvents = [];
        let calendar = null;
        
        // Load events from API
        function loadCalendarEvents() {
            if (typeof axios === 'undefined') {
                console.error('Axios is not loaded');
                return;
            }
            
            axios.get('{{ route("api.calendar.events") }}')
                .then(response => {
                    allEvents = response.data.events || [];
                    
                    // Update calendar if already initialized
                    if (calendar) {
                        calendar.removeAllEvents();
                        calendar.addEventSource(allEvents);
                    } else {
                        // Initialize calendar after events are loaded
                        initCalendar();
                    }
                })
                .catch(error => {
                    console.error('Error loading calendar events:', error);
                    // Initialize calendar with empty events if API fails
                    if (!calendar) {
                        initCalendar();
                    }
                });
        }
        
        // Filter events function
        function filterEvents() {
            const eventType = document.getElementById('filterEventType').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const searchTerm = document.getElementById('filterSearch').value.toLowerCase();
            
            return allEvents.filter(event => {
                // Filter by event type
                if (eventType !== 'all' && event.extendedProps.type !== eventType) {
                    return false;
                }
                
                // Filter by date range
                const eventDate = new Date(event.start);
                if (dateFrom && eventDate < new Date(dateFrom)) {
                    return false;
                }
                if (dateTo && eventDate > new Date(dateTo + 'T23:59:59')) {
                    return false;
                }
                
                // Filter by search term
                if (searchTerm && !event.title.toLowerCase().includes(searchTerm) && 
                    !(event.extendedProps.description && event.extendedProps.description.toLowerCase().includes(searchTerm))) {
                    return false;
                }
                
                return true;
            });
        }
        
        // Apply filters to calendar
        function applyFilters() {
            if (calendar) {
                const filteredEvents = filterEvents();
                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
                
                // Navigate calendar to date range if dates are set
                const dateFrom = document.getElementById('filterDateFrom').value;
                const dateTo = document.getElementById('filterDateTo').value;
                
                if (dateFrom || dateTo) {
                    // If both dates are set
                    if (dateFrom && dateTo) {
                        const fromDate = new Date(dateFrom);
                        const toDate = new Date(dateTo);
                        const daysDiff = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24));
                        
                        // Navigate to the start date
                        calendar.gotoDate(dateFrom);
                        
                        // Adjust view based on date range
                        const isMobile = window.innerWidth < 768;
                        if (isMobile) {
                            // On mobile, use listWeek view
                            if (calendar.view.type !== 'listWeek') {
                                calendar.changeView('listWeek');
                            }
                        } else {
                            // On desktop, adjust view based on range
                            if (daysDiff <= 7) {
                                // For ranges up to a week, use week view
                                if (calendar.view.type !== 'timeGridWeek' && calendar.view.type !== 'timeGridDay') {
                                    calendar.changeView('timeGridWeek');
                                }
                            } else if (daysDiff <= 31) {
                                // For ranges up to a month, use month view
                                if (calendar.view.type !== 'dayGridMonth') {
                                    calendar.changeView('dayGridMonth');
                                }
                            } else {
                                // For longer ranges, stay in month view
                                if (calendar.view.type !== 'dayGridMonth') {
                                    calendar.changeView('dayGridMonth');
                                }
                            }
                        }
                    } 
                    // If only From Date is set
                    else if (dateFrom) {
                        calendar.gotoDate(dateFrom);
                    } 
                    // If only To Date is set
                    else if (dateTo) {
                        calendar.gotoDate(dateTo);
                    }
                }
            }
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('filterEventType').value = 'all';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('filterSearch').value = '';
            
            // Reset calendar to today's date
            if (calendar) {
                calendar.gotoDate(new Date());
                // Reset to default view based on screen size
                const isMobile = window.innerWidth < 768;
                if (isMobile && calendar.view.type !== 'listWeek') {
                    calendar.changeView('listWeek');
                } else if (!isMobile && calendar.view.type === 'listWeek') {
                    calendar.changeView('dayGridMonth');
                }
            }
            
            applyFilters();
        }
        
        // Print calendar events function
        function printCalendarEvents() {
            if (!calendar) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Calendar Not Ready',
                    text: 'Please wait for the calendar to load.',
                    confirmButtonColor: '#055498'
                });
                return;
            }
            
            // Get all currently visible events from the calendar
            const visibleEvents = calendar.getEvents();
            
            if (visibleEvents.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Events',
                    text: 'There are no events to print. Please adjust your filters.',
                    confirmButtonColor: '#055498'
                });
                return;
            }
            
            // Get current filter values for the report header
            const eventType = document.getElementById('filterEventType').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const searchTerm = document.getElementById('filterSearch').value;
            
            // Format events for printing
            const eventsData = visibleEvents.map(event => {
                const eventType = event.extendedProps.type || 'event';
                const startDate = event.start instanceof Date ? event.start : new Date(event.start);
                const formattedDate = startDate.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                return {
                    title: event.title,
                    type: eventType.charAt(0).toUpperCase() + eventType.slice(1),
                    date: formattedDate,
                    startDate: startDate.toISOString().split('T')[0],
                    description: event.extendedProps.description || 'No description available.',
                    effectiveDate: event.extendedProps.effective_date || null,
                    approvedDate: event.extendedProps.approved_date || null,
                    url: event.extendedProps.url || null,
                    id: event.extendedProps.id || null
                };
            });
            
            // Sort events by date (descending - newest first)
            eventsData.sort((a, b) => new Date(b.startDate) - new Date(a.startDate));
            
            // Create print window
            const printWindow = window.open('', '_blank');
            const printContent = generatePrintContent(eventsData, eventType, dateFrom, dateTo, searchTerm);
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Wait for content to load, then print
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();
                }, 250);
            };
        }
        
        // Generate print content HTML
        function generatePrintContent(events, eventTypeFilter, dateFrom, dateTo, searchTerm) {
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
            
            let filterInfo = '<div class="info-section">';
            filterInfo += '<div class="info-row"><span class="info-label">Generated On:</span><span class="info-value">' + currentDate + ' at ' + currentTime + '</span></div>';
            filterInfo += '<div class="info-row"><span class="info-label">Generated By:</span><span class="info-value">' + (currentUserInfo.firstName + ' ' + currentUserInfo.lastName).trim() + ' (' + currentUserInfo.email + ')</span></div>';
            filterInfo += '<div class="info-row"><span class="info-label">Total Events:</span><span class="info-value">' + events.length + '</span></div>';
            if (eventTypeFilter !== 'all' || dateFrom || dateTo || searchTerm) {
                filterInfo += '<div class="info-row" style="margin-top: 10px;"><span class="info-label" style="font-weight: bold; color: #055498;">Applied Filters:</span><span class="info-value"></span></div>';
                filterInfo += '<div class="info-row"><span class="info-label">Event Type:</span><span class="info-value">' + (eventTypeFilter === 'all' ? 'All Types' : eventTypeFilter.charAt(0).toUpperCase() + eventTypeFilter.slice(1)) + '</span></div>';
                if (dateFrom) {
                    filterInfo += '<div class="info-row"><span class="info-label">From Date:</span><span class="info-value">' + new Date(dateFrom).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</span></div>';
                }
                if (dateTo) {
                    filterInfo += '<div class="info-row"><span class="info-label">To Date:</span><span class="info-value">' + new Date(dateTo).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</span></div>';
                }
                if (searchTerm) {
                    filterInfo += '<div class="info-row"><span class="info-label">Search Term:</span><span class="info-value">' + escapeHtml(searchTerm) + '</span></div>';
                }
            }
            filterInfo += '</div>';
            
            let eventsTable = '';
            if (events.length > 0) {
                eventsTable = '<table><thead><tr>';
                eventsTable += '<th style="width: 15%;">Date</th>';
                eventsTable += '<th style="width: 12%;">Type</th>';
                eventsTable += '<th style="width: 25%;">Title</th>';
                eventsTable += '<th style="width: 38%;">Description</th>';
                eventsTable += '<th style="width: 10%;">Details</th>';
                eventsTable += '</tr></thead><tbody>';
                
                events.forEach(event => {
                    eventsTable += '<tr>';
                    eventsTable += '<td>' + event.date + '</td>';
                    eventsTable += '<td><span class="badge">' + event.type + '</span></td>';
                    eventsTable += '<td style="font-weight: bold; color: #333;">' + escapeHtml(event.title) + '</td>';
                    
                    // Clean description - remove HTML tags but preserve text content
                    let cleanDescription = event.description || 'No description available.';
                    cleanDescription = cleanDescription.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                    if (!cleanDescription || cleanDescription === '') {
                        cleanDescription = 'No description available.';
                    }
                    eventsTable += '<td style="font-size: 9px; color: #666; line-height: 1.6; text-align: justify; white-space: normal; word-wrap: break-word;">' + escapeHtml(cleanDescription) + '</td>';
                    
                    // Details column
                    let detailsHtml = '';
                    if (event.effectiveDate) {
                        detailsHtml += '<div style="margin-bottom: 4px;"><strong style="font-size: 8px; color: #055498;">Effective:</strong><br><span style="font-size: 8px;">' + event.effectiveDate + '</span></div>';
                    }
                    if (event.approvedDate) {
                        detailsHtml += '<div><strong style="font-size: 8px; color: #055498;">Approved:</strong><br><span style="font-size: 8px;">' + event.approvedDate + '</span></div>';
                    }
                    if (!detailsHtml) {
                        detailsHtml = '<span style="color: #999; font-size: 8px;">—</span>';
                    }
                    eventsTable += '<td>' + detailsHtml + '</td>';
                    eventsTable += '</tr>';
                });
                
                eventsTable += '</tbody></table>';
            } else {
                eventsTable = '<div class="no-data"><p>No events found.</p></div>';
            }
            
            return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Events Report</title>
    <style>
        @page {
            size: landscape;
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
            background-color: white;
        }
        
        thead {
            background-color: #055498;
            color: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #044080;
            background-color: #055498 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        tbody td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            font-size: 9px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        
        tbody td:nth-child(4) {
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e8f4f8;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            background-color: rgba(5, 84, 152, 0.1);
            color: #055498;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 9px;
        }
        
        .no-data {
            padding: 30px;
            text-align: center;
            color: #999;
            font-style: italic;
        }
        
        @media print {
            @page {
                size: landscape;
                margin: 15mm;
                margin-header: 0;
                margin-footer: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .header {
                page-break-after: avoid;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .header img {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            tbody td {
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: break-word;
            }
            
            tbody td:nth-child(4) {
                white-space: normal !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
            }
            
            thead {
                display: table-header-group;
                background-color: #055498 !important;
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
    <div class="header">
        <img src="${window.location.origin}/images/ddbheader.png" alt="DDB Header" onerror="this.style.display='none';">
        <h1>Activities Calendar Report</h1>
        <p>Board Member Portal - Calendar Events</p>
    </div>
    
    ${filterInfo}
    
    ${eventsTable}
    
    <div class="footer">
        <p>This report was generated on ${currentDate} at ${currentTime} from the Board Member Portal System</p>
        <p style="margin-top: 5px;">Report contains ${events.length} event(s) based on current calendar filters</p>
    </div>
</body>
</html>`;
        }
        
        // Escape HTML helper
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                return;
            }
            
            // Check for FullCalendar availability
            let FC = null;
            if (typeof FullCalendar !== 'undefined' && FullCalendar.Calendar) {
                FC = FullCalendar;
            } else if (typeof window.FullCalendar !== 'undefined' && window.FullCalendar.Calendar) {
                FC = window.FullCalendar;
            } else if (typeof window.FC !== 'undefined' && window.FC.Calendar) {
                FC = window.FC;
            }
            
            if (!FC || typeof FC.Calendar === 'undefined') {
                setTimeout(initCalendar, 200);
                return;
            }
            
            try {
                const isMobile = window.innerWidth < 768;
                
                calendar = new FC.Calendar(calendarEl, {
                    initialView: isMobile ? 'listWeek' : 'dayGridMonth',
                    headerToolbar: {
                        left: isMobile ? 'prev,next' : 'prev,next today',
                        center: 'title',
                        right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    editable: false,
                    selectable: false,
                    views: {
                        listWeek: {
                            listDayFormat: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' },
                            listDaySideFormat: false
                        }
                    },
                    events: allEvents,
                    eventClick: function(info) {
                        const eventType = info.event.extendedProps.type || 'event';
                        const description = info.event.extendedProps.description || 'No description available.';
                        const eventDate = info.event.start.toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        const eventUrl = info.event.extendedProps.url || null;
                        const pdfUrl = info.event.extendedProps.pdf_url || null;
                        const effectiveDate = info.event.extendedProps.effective_date || null;
                        const approvedDate = info.event.extendedProps.approved_date || null;
                        
                        // Notice-specific details
                        const noticeType = info.event.extendedProps.notice_type || null;
                        const meetingType = info.event.extendedProps.meeting_type || null;
                        const meetingDate = info.event.extendedProps.meeting_date || null;
                        const meetingTime = info.event.extendedProps.meeting_time || null;
                        const meetingLink = info.event.extendedProps.meeting_link || null;
                        
                        // Build action buttons based on event type
                        let actionButton = '';
                        if (eventType === 'announcement' && eventUrl) {
                            actionButton = `<button onclick="window.openAnnouncementModal(${info.event.extendedProps.id}); Swal.close();" class="mt-3 px-4 py-2 bg-gradient-to-r from-[#055498] to-[#123a60] text-white rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all">
                                <i class="fas fa-eye mr-2"></i>View Announcement
                            </button>`;
                        } else if ((eventType === 'resolution' || eventType === 'regulation') && pdfUrl) {
                            actionButton = `<button onclick="window.open('${pdfUrl}', '_blank'); Swal.close();" class="mt-3 px-4 py-2 bg-gradient-to-r from-[#055498] to-[#123a60] text-white rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all">
                                <i class="fas fa-file-pdf mr-2"></i>View PDF
                            </button>`;
                        } else if (eventUrl) {
                            actionButton = `<a href="${eventUrl}" class="mt-3 inline-block px-4 py-2 bg-gradient-to-r from-[#055498] to-[#123a60] text-white rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all">
                                <i class="fas fa-external-link-alt mr-2"></i>View Details
                            </a>`;
                        }
                        
                        // Build date fields for resolutions and regulations
                        let dateFields = '';
                        let showEventDate = true;
                        if (eventType === 'resolution' || eventType === 'regulation') {
                            showEventDate = false; // Don't show the generic "Date" field for resolutions/regulations
                            if (effectiveDate) {
                                dateFields += `<p class="mb-2"><strong>Effective Date:</strong> ${effectiveDate}</p>`;
                            }
                            if (approvedDate) {
                                dateFields += `<p class="mb-2"><strong>Approved Date:</strong> ${approvedDate}</p>`;
                            }
                        }
                        
                        // Build notice-specific fields
                        let noticeFields = '';
                        if (eventType === 'notice') {
                            noticeFields = '<div class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200">';
                            noticeFields += '<p class="text-sm font-semibold text-purple-800 mb-2"><i class="fas fa-info-circle mr-1"></i>Notice Details</p>';
                            if (noticeType) {
                                noticeFields += `<p class="mb-2 text-sm"><strong>Notice Type:</strong> <span class="text-purple-700">${noticeType}</span></p>`;
                            }
                            if (meetingType) {
                                const meetingTypeLabel = meetingType.charAt(0).toUpperCase() + meetingType.slice(1);
                                noticeFields += `<p class="mb-2 text-sm"><strong>Meeting Type:</strong> <span class="text-purple-700">${meetingTypeLabel}</span></p>`;
                            }
                            if (meetingDate) {
                                noticeFields += `<p class="mb-2 text-sm"><strong>Meeting Date:</strong> <span class="text-purple-700">${meetingDate}</span></p>`;
                            }
                            if (meetingTime) {
                                noticeFields += `<p class="mb-2 text-sm"><strong>Meeting Time:</strong> <span class="text-purple-700">${meetingTime}</span></p>`;
                            }
                            if (meetingLink && (meetingType === 'online' || meetingType === 'hybrid')) {
                                noticeFields += `<p class="mb-2 text-sm"><strong>Meeting Link:</strong> <a href="${meetingLink}" target="_blank" class="text-purple-700 hover:text-purple-900 underline break-all"><i class="fas fa-link mr-1"></i>${meetingLink}</a></p>`;
                            }
                            noticeFields += '</div>';
                        }
                        
                        Swal.fire({
                            title: info.event.title,
                            html: `
                                <div class="text-left">
                                    <p class="mb-2"><strong>Type:</strong> <span class="capitalize">${eventType}</span></p>
                                    ${showEventDate ? `<p class="mb-2"><strong>Date:</strong> ${eventDate}</p>` : ''}
                                    ${dateFields}
                                    ${noticeFields}
                                    <p class="mb-2 mt-3"><strong>Description:</strong></p>
                                    <p class="text-sm text-gray-600 mb-3">${description}</p>
                                    ${actionButton}
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#055498',
                            showCloseButton: true,
                            width: '700px',
                            customClass: {
                                popup: 'swal-wide-popup'
                            }
                        });
                    },
                    eventDisplay: 'block',
                    dayMaxEvents: true,
                    moreLinkClick: 'popover'
                });
                
                calendar.render();
                
                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        const isMobile = window.innerWidth < 768;
                        const currentView = calendar.view.type;
                        
                        if (isMobile && currentView === 'dayGridMonth') {
                            calendar.changeView('listWeek');
                        } else if (!isMobile && currentView === 'listWeek') {
                            calendar.changeView('dayGridMonth');
                        }
                        
                        calendar.setOption('headerToolbar', {
                            left: isMobile ? 'prev,next' : 'prev,next today',
                            center: 'title',
                            right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                        });
                    }, 250);
                });
                
                // Filter panel toggle
                const toggleFilterBtn = document.getElementById('toggleFilterBtn');
                const filterPanel = document.getElementById('filterPanel');
                
                if (toggleFilterBtn && filterPanel) {
                    toggleFilterBtn.addEventListener('click', function() {
                        filterPanel.classList.toggle('hidden');
                        const icon = toggleFilterBtn.querySelector('i');
                        if (filterPanel.classList.contains('hidden')) {
                            icon.className = 'fas fa-filter mr-2';
                        } else {
                            icon.className = 'fas fa-filter mr-2';
                        }
                    });
                }
                
                // Apply filters button
                const applyFiltersBtn = document.getElementById('applyFiltersBtn');
                if (applyFiltersBtn) {
                    applyFiltersBtn.addEventListener('click', applyFilters);
                }
                
                // Clear filters button
                const clearFiltersBtn = document.getElementById('clearFiltersBtn');
                if (clearFiltersBtn) {
                    clearFiltersBtn.addEventListener('click', clearFilters);
                }
                
                // Print calendar button - use arrow function to preserve scope
                const printCalendarBtn = document.getElementById('printCalendarBtn');
                if (printCalendarBtn) {
                    printCalendarBtn.addEventListener('click', () => printCalendarEvents());
                }
                
                // Auto-apply filters on input change (debounced)
                let filterTimeout;
                const filterInputs = ['filterEventType', 'filterDateFrom', 'filterDateTo', 'filterSearch'];
                filterInputs.forEach(inputId => {
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.addEventListener('change', function() {
                            clearTimeout(filterTimeout);
                            filterTimeout = setTimeout(applyFilters, 300);
                        });
                        input.addEventListener('input', function() {
                            if (inputId === 'filterSearch') {
                                clearTimeout(filterTimeout);
                                filterTimeout = setTimeout(applyFilters, 500);
                            }
                        });
                    }
                });
            } catch(error) {
                console.error('Error initializing calendar:', error);
            }
        }
        
        // Load calendar events first, then initialize calendar
        if (typeof axios !== 'undefined') {
            loadCalendarEvents();
        } else {
            // Wait for axios to load
            const checkAxios = setInterval(function() {
                if (typeof axios !== 'undefined') {
                    clearInterval(checkAxios);
                    loadCalendarEvents();
                }
            }, 100);
        }
        
        // Load FullCalendar script dynamically if not already loaded
        if (typeof FullCalendar === 'undefined' && typeof window.FullCalendar === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js';
            script.onload = function() {
                // Calendar will be initialized after events are loaded
            };
            script.onerror = function() {
                const altScript = document.createElement('script');
                altScript.src = 'https://unpkg.com/fullcalendar@6.1.10/index.global.min.js';
                altScript.onload = function() {
                    // Calendar will be initialized after events are loaded
                };
                document.head.appendChild(altScript);
            };
            document.head.appendChild(script);
        }
    })();
</script>

<style>
    /* Touch-friendly interactions */
    * {
        -webkit-tap-highlight-color: transparent;
    }
    
    button, a {
        touch-action: manipulation;
    }
    
    /* Extra small breakpoint (xs) - 475px and up */
    @media (min-width: 475px) {
        .xs\:inline {
            display: inline !important;
        }
        
        .xs\:hidden {
            display: none !important;
        }
    }
    
    /* Fix Safari select dropdown height to match input height */
    #filterEventType {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        box-sizing: border-box;
        line-height: 1.5;
        padding-right: 36px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23374151' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 12px;
    }
    
    @media (max-width: 640px) {
        #filterEventType {
            min-height: 44px;
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        #filterDateFrom, #filterDateTo, #filterSearch {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
    
    @media (min-width: 641px) {
        #filterEventType {
            min-height: 38px;
        }
    }
    
    /* FullCalendar Custom Styling */
    .calendar-container {
        min-height: 400px;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #calendar .fc {
        font-family: inherit;
    }
    
    #calendar .fc-header-toolbar {
        margin-bottom: 1.5rem;
        padding: 0.5rem;
    }
    
    #calendar .fc-button {
        background-color: #055498 !important;
        border-color: #055498 !important;
        color: white !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.375rem !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
    }
    
    #calendar .fc-button:hover {
        background-color: #123a60 !important;
        border-color: #123a60 !important;
    }
    
    #calendar .fc-button-active {
        background-color: #123a60 !important;
        border-color: #123a60 !important;
    }
    
    #calendar .fc-today-button {
        background-color: #FBD116 !important;
        border-color: #FBD116 !important;
        color: #123a60 !important;
    }
    
    #calendar .fc-today-button:hover {
        background-color: #facc15 !important;
        border-color: #facc15 !important;
    }
    
    #calendar .fc-day-today {
        background-color: rgba(5, 84, 152, 0.1) !important;
    }
    
    #calendar .fc-event {
        border-radius: 0.25rem !important;
        padding: 0.25rem 0.5rem !important;
        cursor: pointer !important;
    }
    
    #calendar .fc-event:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    #calendar .fc-daygrid-day-number {
        color: #374151 !important;
        font-weight: 500 !important;
    }
    
    #calendar .fc-col-header-cell {
        background-color: #f9fafb !important;
        padding: 0.75rem 0 !important;
    }
    
    #calendar .fc-col-header-cell-cushion {
        color: #374151 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
    }
    
    #calendar .fc-daygrid-day {
        border-color: #e5e7eb !important;
    }
    
    #calendar .fc-daygrid-day-frame {
        min-height: 100px !important;
    }
    
    /* Extra small devices (phones, 320px and up) */
    @media (max-width: 374px) {
        .calendar-container {
            min-height: 350px;
            padding: 0;
        }
        
        #calendar .fc-header-toolbar {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }
        
        #calendar .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
            flex-wrap: wrap;
        }
        
        #calendar .fc-button {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.7rem !important;
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        #calendar .fc-toolbar-title {
            font-size: 0.875rem !important;
            margin: 0.5rem 0 !important;
        }
        
        #calendar .fc-col-header-cell-cushion {
            font-size: 0.6rem !important;
            padding: 0.4rem 0.2rem !important;
        }
        
        #calendar .fc-daygrid-day-number {
            font-size: 0.7rem !important;
            padding: 0.2rem !important;
        }
        
        #calendar .fc-event {
            font-size: 0.7rem !important;
            padding: 0.1rem 0.3rem !important;
            margin: 0.1rem 0 !important;
            min-height: 28px !important;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 55px !important;
        }
        
        #calendar .fc-list-event {
            font-size: 0.8rem !important;
        }
        
        #calendar .fc-list-event-title {
            font-size: 0.8rem !important;
        }
        
        #calendar .fc-list-day-text {
            padding-left: 0.4rem !important;
            font-size: 0.75rem !important;
        }
    }
    
    /* Small devices (phones, 375px and up) */
    @media (min-width: 375px) and (max-width: 640px) {
        .calendar-container {
            min-height: 400px;
            padding: 0;
        }
        
        #calendar .fc-header-toolbar {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }
        
        #calendar .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
            flex-wrap: wrap;
        }
        
        #calendar .fc-button {
            padding: 0.5rem 0.875rem !important;
            font-size: 0.75rem !important;
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        #calendar .fc-toolbar-title {
            font-size: 1rem !important;
            margin: 0.5rem 0 !important;
        }
        
        #calendar .fc-col-header-cell-cushion {
            font-size: 0.625rem !important;
            padding: 0.5rem 0.25rem !important;
        }
        
        #calendar .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            padding: 0.25rem !important;
        }
        
        #calendar .fc-event {
            font-size: 0.75rem !important;
            padding: 0.125rem 0.375rem !important;
            margin: 0.125rem 0 !important;
            min-height: 32px !important;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 60px !important;
        }
        
        #calendar .fc-list-event {
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-list-event-title {
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-list-day-text {
            padding-left: 0.5rem !important;
        }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
        .calendar-container {
            min-height: 450px;
        }
        
        #calendar .fc-header-toolbar {
            padding: 0.75rem 0;
        }
        
        #calendar .fc-button {
            padding: 0.5rem 0.875rem !important;
            font-size: 0.875rem !important;
        }
        
        #calendar .fc-toolbar-title {
            font-size: 1.125rem !important;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 80px !important;
        }
        
        #calendar .fc-event {
            font-size: 0.8125rem !important;
        }
    }
    
    @media (min-width: 1025px) {
        .calendar-container {
            min-height: 600px;
        }
        
        #calendar .fc-daygrid-day-frame {
            min-height: 120px !important;
        }
    }
    
    @media (max-width: 1024px) {
        #calendar .fc-scroller {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        #calendar .fc-daygrid-body {
            min-width: 100% !important;
        }
    }
    
    @media (hover: none) and (pointer: coarse) {
        #calendar .fc-button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        #calendar .fc-event {
            min-height: 32px !important;
        }
        
        #calendar .fc-daygrid-day-number {
            min-height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }
    
    @media (max-width: 640px) {
        #calendar .fc-more-link {
            font-size: 0.75rem !important;
        }
        
        #calendar .fc-popover {
            max-width: 90vw !important;
        }
    }
    
    /* SweetAlert Wide Popup for Calendar Events */
    .swal-wide-popup {
        width: 600px !important;
        max-width: 90vw !important;
    }
    
    @media (max-width: 640px) {
        .swal-wide-popup {
            width: 95vw !important;
            margin: 1rem !important;
        }
    }
    
    /* Improve text readability on small screens */
    @media (max-width: 640px) {
        body {
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
    }
    
    /* Better spacing for mobile */
    @media (max-width: 640px) {
        .calendar-container {
            margin: 0 -0.75rem;
            padding: 0 0.75rem;
        }
    }
</style>
@endpush

