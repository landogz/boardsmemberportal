@extends('admin.layout')

@section('title', 'Audit Logs')

@php
    $headerTitle = 'Audit Logs';
    $headerSubtitle = 'System-wide user activity trail';
    $headerActions = [];
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    .dt-buttons {
        margin-bottom: 1rem;
    }
    .dt-buttons .dt-button {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
    <div class="space-y-4 sm:space-y-6 p-4 sm:p-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="mb-3 sm:mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800">Audit Trail</h2>
                <p class="text-xs sm:text-sm text-gray-600">View all recorded actions performed by users across the system.</p>
            </div>

            <div class="overflow-x-auto">
                <table id="auditLogsTable" class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="display: none;">ID</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date / Time</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route / Method</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap" style="display: none;">{{ $log->id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap" data-order="{{ $log->created_at?->timestamp ?? 0 }}">
                                    <div class="text-[11px] text-gray-900">
                                        {{ $log->created_at?->format('M d, Y h:i A') }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-shrink-0">
                                                @php
                                                    $profileMedia = $log->user->profile_picture ? \App\Models\MediaLibrary::find($log->user->profile_picture) : null;
                                                    $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($log->user->first_name . ' ' . $log->user->last_name) . '&size=32&background=055498&color=fff';
                                                @endphp
                                                <img src="{{ $profileUrl }}" alt="Profile" class="h-8 w-8 rounded-full object-cover border-2" style="border-color: #055498;">
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-semibold text-gray-900">
                                                    {{ $log->user->first_name }} {{ $log->user->last_name }}
                                                </div>
                                                <div class="text-[10px] text-gray-500">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=System&size=32&background=6B7280&color=fff" alt="System" class="h-8 w-8 rounded-full object-cover border-2" style="border-color: #6B7280;">
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-semibold text-gray-500">System / Guest</div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-semibold" style="background-color: rgba(5,84,152,0.08); color:#055498;">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-[11px] text-gray-800 line-clamp-3 max-w-xs">
                                        {{ $log->description ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-[11px] text-gray-800">{{ $log->ip_address ?? '—' }}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-[10px] font-mono text-gray-700">
                                        {{ $log->method ?? '—' }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 truncate max-w-xs" title="{{ $log->url }}">
                                        @php
                                            $url = $log->url ?? '';
                                            // Remove base URL (http://127.0.0.1:8000 or any domain)
                                            $path = parse_url($url, PHP_URL_PATH);
                                            $query = parse_url($url, PHP_URL_QUERY);
                                            $displayUrl = $path ?? $url;
                                            if ($query) {
                                                $displayUrl .= '?' . $query;
                                            }
                                        @endphp
                                        {{ $displayUrl }}
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-[10px] text-gray-700">
                                        {{ $log->model_type ? class_basename($log->model_type) : '—' }}
                                    </div>
                                    @if($log->model_id)
                                        <div class="text-[10px] text-gray-500">ID: {{ $log->model_id }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500">
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
    var table;
    $(document).ready(function() {
        table = $('#auditLogsTable').DataTable({
            order: [[0, 'desc']], // Sort by ID descending (newest first)
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { targets: 0, visible: false }, // Hide ID column
                { targets: 1, type: 'num' } // Treat date column as numeric for sorting
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv mr-2"></i>Export CSV',
                    className: 'px-4 py-2 text-white rounded-lg font-semibold hover:opacity-90 transition',
                    style: 'background-color: #055498;',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Get all text content from the cell, replacing multiple spaces with single space
                                var text = $(node).text().trim().replace(/\s+/g, ' ');
                                return text || '—';
                            }
                        }
                    },
                    filename: 'audit-logs-' + new Date().toISOString().split('T')[0]
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel mr-2"></i>Export Excel',
                    className: 'px-4 py-2 text-white rounded-lg font-semibold hover:opacity-90 transition',
                    style: 'background-color: #055498;',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Get all text content from the cell, replacing multiple spaces with single space
                                var text = $(node).text().trim().replace(/\s+/g, ' ');
                                return text || '—';
                            }
                        }
                    },
                    filename: 'audit-logs-' + new Date().toISOString().split('T')[0]
                },
                {
                    text: '<i class="fas fa-file-pdf mr-2"></i>Export PDF',
                    className: 'px-4 py-2 text-white rounded-lg font-semibold hover:opacity-90 transition',
                    style: 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);',
                    action: function (e, dt, button, config) {
                        exportFilteredPdf();
                    }
                }
            ],
            language: {
                search: "Search logs:",
                lengthMenu: "Show _MENU_ logs per page",
                info: "Showing _START_ to _END_ of _TOTAL_ logs",
                infoEmpty: "No logs found",
                infoFiltered: "(filtered from _MAX_ total logs)"
            }
        });
    });

    // Export filtered PDF
    function exportFilteredPdf() {
        // Get the current search value from DataTables
        // Use the global table variable that was initialized
        var searchValue = table.search();
        
        // Also try to get it from the search input field directly
        var searchInput = $('.dataTables_filter input').val();
        if (searchInput && searchInput.trim() !== '') {
            searchValue = searchInput.trim();
        }
        
        // Build the export URL with search parameter
        var exportUrl = '{{ route("admin.audit-logs.export-pdf") }}';
        
        // Only add search parameter if it's not empty
        if (searchValue && searchValue.trim() !== '') {
            exportUrl += '?search=' + encodeURIComponent(searchValue.trim());
        }
        
        // Debug: log the values
        console.log('DataTable search():', table.search());
        console.log('Search input value:', searchInput);
        console.log('Final search value:', searchValue);
        console.log('Export URL:', exportUrl);
        
        // Show loading message
        Swal.fire({
            title: 'Generating PDF',
            text: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Create a form to submit the request (more reliable than window.open for downloads)
        var form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("admin.audit-logs.export-pdf") }}';
        form.target = '_blank';
        form.style.display = 'none';
        
        // Add search parameter as hidden input if it exists
        if (searchValue && searchValue.trim() !== '') {
            var searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'search';
            searchInput.value = searchValue.trim();
            form.appendChild(searchInput);
        }
        
        document.body.appendChild(form);
        form.submit();
        
        // Remove form after submission
        setTimeout(function() {
            document.body.removeChild(form);
            Swal.close();
        }, 500);
    }
</script>
@endpush


