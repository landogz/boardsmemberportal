@extends('admin.layout')

@section('title', 'Agenda Requests')

@php
    $pageTitle = 'Agenda Requests';
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending {
        background-color: rgba(251, 191, 36, 0.1);
        color: #F59E0B;
    }
    .status-approved {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    .status-rejected {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }
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
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Agenda Inclusion Requests</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Review and manage agenda requests from board members</p>
        @if(isset($noticeId) && $noticeId)
            @php
                $notice = \App\Models\Notice::find($noticeId);
            @endphp
            @if($notice)
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-filter mr-2"></i>
                        Showing agenda requests for notice: <strong>{{ $notice->title }}</strong>
                        <a href="{{ route('admin.agenda-inclusion-requests.index') }}" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                            Clear filter
                        </a>
                    </p>
                </div>
            @endif
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table id="agendaRequestsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ Str::limit($request->notice->title, 40) }}</div>
                                @php
                                    $typeClass = 'type-other';
                                    if ($request->notice->notice_type === 'Notice of Meeting') {
                                        $typeClass = 'type-meeting';
                                    } elseif ($request->notice->notice_type === 'Agenda') {
                                        $typeClass = 'type-agenda';
                                    } elseif ($request->notice->notice_type === 'Board Issuances') {
                                        $typeClass = 'type-board-issuances';
                                    }
                                @endphp
                                <span class="notice-type-badge {{ $typeClass }}">
                                    {{ $request->notice->notice_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $profileMedia = $request->user->profile_picture ? \App\Models\MediaLibrary::find($request->user->profile_picture) : null;
                                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($request->user->first_name . ' ' . $request->user->last_name) . '&size=40&background=055498&color=fff';
                                        @endphp
                                        <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->user->first_name }} {{ $request->user->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($request->description, 100) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->attachment_media->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($request->attachment_media as $media)
                                            @php
                                                $isPdf = $media->file_type === 'application/pdf' || str_ends_with(strtolower($media->file_name ?? ''), '.pdf');
                                            @endphp
                                            @if($isPdf)
                                                <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $media->file_path) }}', '{{ addslashes($media->file_name) }}'); return false;" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-800 transition-colors border border-gray-200 hover:border-red-300" title="{{ $media->file_name }}">
                                                    <i class="fas fa-file-pdf text-red-600"></i>
                                                    <span>View PDF</span>
                                                    <i class="fas fa-eye text-[10px] opacity-70"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.media-library.download', $media->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-800 transition-colors border border-gray-200 hover:border-blue-300" title="{{ $media->file_name }}">
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                    <span class="max-w-[120px] truncate">{{ $media->file_name }}</span>
                                                    <i class="fas fa-external-link-alt text-[10px] opacity-70"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-{{ $request->status }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('M d, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.agenda-inclusion-requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($request->status === 'pending')
                                        <button onclick="approveRequest({{ $request->id }})" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button onclick="rejectRequest({{ $request->id }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @else
                                        <span class="text-gray-400">Reviewed</span>
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
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No agenda requests found</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">Reject Agenda Request</h3>
        <form id="rejectForm">
            <input type="hidden" id="rejectRequestId" name="request_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rejection Reason <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="rejectReason" 
                    name="reason" 
                    rows="4" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                    placeholder="Please provide a reason for rejecting this request..."
                ></textarea>
            </div>
            <div class="flex gap-3">
                <button 
                    type="button" 
                    onclick="closeRejectModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    $(document).ready(function() {
        $('#agendaRequestsTable').DataTable({
            order: [[4, 'desc']],
            pageLength: 15,
        });
    });

    function approveRequest(id) {
        Swal.fire({
            title: 'Approve Request?',
            text: 'Are you sure you want to approve this agenda request?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/admin/agenda-inclusion-requests/${id}/approve`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.response?.data?.message || 'Failed to approve request.',
                        });
                    });
            }
        });
    }

    function rejectRequest(id) {
        document.getElementById('rejectRequestId').value = id;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const requestId = document.getElementById('rejectRequestId').value;
        const reason = document.getElementById('rejectReason').value;
        
        axios.post(`/admin/agenda-inclusion-requests/${requestId}/reject`, { reason })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeRejectModal();
                        location.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.response?.data?.message || 'Failed to reject request.',
                });
            });
    });
</script>
@endpush
@endsection

