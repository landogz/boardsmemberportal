@extends('admin.layout')

@section('title', 'Reference Materials')

@php
    $pageTitle = 'Reference Materials';
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
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Reference Materials</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Review and manage reference materials submitted by board members</p>
        @if(isset($noticeId) && $noticeId)
            @php
                $notice = \App\Models\Notice::find($noticeId);
            @endphp
            @if($notice)
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-filter mr-2"></i>
                        Showing reference materials for notice: <strong>{{ $notice->title }}</strong>
                        <a href="{{ route('admin.reference-materials.index') }}" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                            Clear filter
                        </a>
                    </p>
                </div>
            @endif
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            @if($materials->count() > 0)
            <div class="overflow-x-auto">
                <table id="referenceMaterialsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($materials as $material)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ Str::limit($material->notice->title, 40) }}</div>
                                @php
                                    $typeClass = 'type-other';
                                    if ($material->notice->notice_type === 'Notice of Meeting') {
                                        $typeClass = 'type-meeting';
                                    } elseif ($material->notice->notice_type === 'Agenda') {
                                        $typeClass = 'type-agenda';
                                    } elseif ($material->notice->notice_type === 'Board Issuances') {
                                        $typeClass = 'type-board-issuances';
                                    }
                                @endphp
                                <span class="notice-type-badge {{ $typeClass }}">
                                    {{ $material->notice->notice_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $profileMedia = $material->user->profile_picture ? \App\Models\MediaLibrary::find($material->user->profile_picture) : null;
                                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($material->user->first_name . ' ' . $material->user->last_name) . '&size=40&background=055498&color=fff';
                                        @endphp
                                        <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $material->user->first_name }} {{ $material->user->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $material->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($material->description, 100) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-{{ $material->status }}">
                                    {{ ucfirst($material->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $material->created_at->format('M d, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.reference-materials.show', $material->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($material->status === 'pending' && $material->notice->created_by === Auth::id())
                                        <button onclick="approveMaterial({{ $material->id }})" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button onclick="rejectMaterial({{ $material->id }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @elseif($material->status !== 'pending')
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
                <p class="text-gray-500 text-lg">No reference materials found</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">Reject Reference Materials</h3>
        <form id="rejectForm">
            <input type="hidden" id="rejectMaterialId" name="material_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rejection Reason <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="rejectReason" 
                    name="rejection_reason" 
                    rows="4" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                    placeholder="Please provide a reason for rejecting this submission..."
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
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    @if($materials->count() > 0)
    $(document).ready(function() {
        $('#referenceMaterialsTable').DataTable({
            order: [[4, 'desc']],
            pageLength: 25,
        });
    });
    @endif

    function approveMaterial(id) {
        Swal.fire({
            title: 'Approve Reference Materials?',
            text: 'Are you sure you want to approve this submission?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, approve'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/admin/reference-materials/${id}/approve`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Approved!',
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
                            title: 'Error',
                            text: error.response?.data?.message || 'Failed to approve reference materials.'
                        });
                    });
            }
        });
    }

    function rejectMaterial(id) {
        document.getElementById('rejectMaterialId').value = id;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const materialId = document.getElementById('rejectMaterialId').value;
        const reason = document.getElementById('rejectReason').value;
        
        axios.post(`/admin/reference-materials/${materialId}/reject`, { rejection_reason: reason })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejected!',
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
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to reject reference materials.'
                });
            });
    });
</script>
@endpush

