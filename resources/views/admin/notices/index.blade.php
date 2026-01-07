@extends('admin.layout')

@section('title', 'Notices')

@php
    $pageTitle = 'Notices';
    $headerActions = [];
    if (Auth::user()->hasPermission('create notices')) {
        $headerActions[] = [
            'url' => route('admin.notices.create'),
            'text' => 'Create Notice',
            'icon' => 'fas fa-plus',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .notice-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .type-meeting {
        background-color: rgba(5, 84, 152, 0.1);
        color: #055498;
    }
    .type-agenda {
        background-color: rgba(206, 32, 40, 0.1);
        color: #CE2028;
    }
    .type-board-issuances {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }
    .type-other {
        background-color: rgba(156, 163, 175, 0.1);
        color: #6B7280;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800">All Notices</h2>
            <p class="text-sm text-gray-600 mt-1">Manage notices and their visibility</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if($notices->count() > 0)
        <div class="overflow-x-auto">
            <table id="noticesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowed Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notices as $notice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeClass = 'type-other';
                                if ($notice->notice_type === 'Notice of Meeting') {
                                    $typeClass = 'type-meeting';
                                } elseif ($notice->notice_type === 'Agenda') {
                                    $typeClass = 'type-agenda';
                                } elseif ($notice->notice_type === 'Board Issuances') {
                                    $typeClass = 'type-board-issuances';
                                }
                            @endphp
                            <span class="notice-type-badge {{ $typeClass }}">
                                {{ $notice->notice_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($notice->title, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 capitalize">{{ $notice->meeting_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $notice->allowedUsers->count() }} user(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $notice->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.notices.show', $notice->id) }}" class="text-[#055498] hover:text-[#123a60]">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->hasPermission('edit notices'))
                                <a href="{{ route('admin.notices.edit', $notice->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if(Auth::user()->hasPermission('delete notices'))
                                <button onclick="deleteNotice({{ $notice->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No notices found</p>
            @if(Auth::user()->hasPermission('create notices'))
            <a href="{{ route('admin.notices.create') }}" class="mt-4 inline-block px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors">
                Create Your First Notice
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#noticesTable').DataTable({
            order: [[5, 'desc']], // Sort by created date descending
            pageLength: 15,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ notices per page",
                info: "Showing _START_ to _END_ of _TOTAL_ notices",
                infoEmpty: "No notices found",
                infoFiltered: "(filtered from _MAX_ total notices)",
            }
        });
    });

    function deleteNotice(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/notices/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete notice.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Failed to delete notice.',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection

