@extends('admin.layout')

@section('title', 'Version History')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endpush

@php
    $pageTitle = 'Version History';
    $headerActions = [
        [
            'url' => route('admin.board-resolutions.index'),
            'text' => 'Back to Board Resolutions',
            'icon' => 'fas fa-arrow-left',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ]
    ];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Version History</h2>
        <p class="text-gray-600 mt-1">Document: <span class="font-semibold">{{ $document->title }}</span></p>
    </div>

    <!-- Current Version Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Current Version</h3>
            <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Active</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Title</p>
                <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Version</p>
                <p class="text-sm font-medium text-gray-900">{{ $document->version ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Effective Date</p>
                <p class="text-sm font-medium text-gray-900">{{ $document->effective_date ? $document->effective_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Uploaded By</p>
                <p class="text-sm font-medium text-gray-900">
                    @if($document->uploader)
                        {{ $document->uploader->first_name }} {{ $document->uploader->last_name }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
            @if($document->description)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600">Description</p>
                <p class="text-sm text-gray-900">{{ $document->description }}</p>
            </div>
            @endif
            @if($document->pdf)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 mb-2">PDF File</p>
                <button 
                    onclick="openGlobalPdfModal('{{ route('admin.board-resolutions.pdf', $document->id) }}', '{{ addslashes($document->title) }}')"
                    class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors cursor-pointer"
                >
                    <i class="fas fa-file-pdf mr-2"></i>{{ $document->pdf->file_name }}
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Version History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Previous Versions</h3>
        
        @if($versions->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No version history available</p>
                <p class="text-sm text-gray-400 mt-2">Version history will appear here when the document is updated.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table id="versionsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PDF</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($versions as $version)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">#{{ $loop->iteration }}</span>
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded">Archived</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $version->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $version->version ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $version->effective_date ? $version->effective_date->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">{{ Str::limit($version->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($version->change_notes)
                                    <div class="text-sm text-gray-900 max-w-xs bg-yellow-50 p-2 rounded border border-yellow-200">{{ Str::limit($version->change_notes, 50) }}</div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($version->uploader)
                                    <div class="text-sm text-gray-900">{{ $version->uploader->first_name }} {{ $version->uploader->last_name }}</div>
                                @else
                                    <span class="text-sm text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $version->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $version->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($version->pdf)
                                    <button 
                                        onclick="openGlobalPdfModal('{{ asset('storage/' . $version->pdf->file_path) }}', '{{ addslashes($version->title) }}')"
                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition-colors"
                                        title="View PDF"
                                    >
                                        <i class="fas fa-file-pdf mr-1"></i>View
                                    </button>
                                @else
                                    <span class="text-sm text-gray-400">No PDF</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Using global PDF modal from components/pdf-modal.blade.php -->
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    // Initialize DataTable
    $(document).ready(function() {
        @if($versions->isNotEmpty())
        $('#versionsTable').DataTable({
            order: [[7, 'desc']], // Sort by updated date descending (newest first)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                search: "Search versions:",
                lengthMenu: "Show _MENU_ versions per page",
                info: "Showing _START_ to _END_ of _TOTAL_ versions",
                infoEmpty: "No versions found",
                infoFiltered: "(filtered from _MAX_ total versions)"
            },
            columnDefs: [
                { orderable: false, targets: [8] } // Disable sorting on PDF column
            ]
        });
        @endif
    });

    // Using global PDF modal functions from components/pdf-modal.blade.php
</script>
@endpush

