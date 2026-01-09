@extends('admin.layout')

@section('title', 'Reference Material Details')

@php
    $pageTitle = 'Reference Material Details';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.reference-materials.index'),
        'text' => 'Back to List',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@push('styles')
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
<div class="p-4 lg:p-6 space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-500 text-white',
                                'approved' => 'bg-green-500 text-white',
                                'rejected' => 'bg-red-500 text-white'
                            ];
                            $statusColor = $statusColors[$material->status] ?? 'bg-gray-500 text-white';
                        @endphp
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide {{ $statusColor }}">
                            <i class="fas fa-{{ $material->status === 'approved' ? 'check-circle' : ($material->status === 'rejected' ? 'times-circle' : 'clock') }} mr-1.5"></i>
                            {{ ucfirst($material->status) }}
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-2">Reference Materials</h1>
                    <p class="text-gray-600">Review submission details and attachments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notice Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
            <span>Related Notice</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Notice Title</label>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $material->notice->title }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Notice Type</label>
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
                <span class="notice-type-badge {{ $typeClass }} mt-1">
                    {{ $material->notice->notice_type }}
                </span>
            </div>
        </div>
    </div>

    <!-- Submitter Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-purple-500 to-indigo-600 rounded-full"></div>
            <span>Submitted By</span>
        </h3>
        <div class="flex items-center space-x-4">
            @php
                $profileMedia = $material->user->profile_picture ? \App\Models\MediaLibrary::find($material->user->profile_picture) : null;
                $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($material->user->first_name . ' ' . $material->user->last_name) . '&size=64&background=055498&color=fff';
            @endphp
            <img src="{{ $profileUrl }}" alt="Profile" class="h-16 w-16 rounded-full object-cover border-2" style="border-color: #055498;">
            <div>
                <p class="text-lg font-semibold text-gray-900">{{ $material->user->first_name }} {{ $material->user->last_name }}</p>
                <p class="text-sm text-gray-600">{{ $material->user->email }}</p>
                @if($material->user->governmentAgency)
                    <p class="text-sm text-gray-500">{{ $material->user->governmentAgency->name }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full"></div>
            <span>Description</span>
        </h3>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $material->description }}</p>
    </div>

    <!-- Attachments -->
    @if($material->attachments && count($material->attachments) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Attachments</span>
                <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ count($material->attachments) }}</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($material->attachment_media as $attachment)
                    @php
                        $isImage = in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
                        
                        // Get file size from storage if not in database
                        $fileSize = $attachment->file_size;
                        if (!$fileSize || $fileSize == 0) {
                            try {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($attachment->file_path)) {
                                    $fileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($attachment->file_path);
                                }
                            } catch (\Exception $e) {
                                $fileSize = 0;
                            }
                        }
                        
                        // Format file size
                        if ($fileSize >= 1048576) {
                            $fileSizeFormatted = number_format($fileSize / 1048576, 2) . ' MB';
                        } elseif ($fileSize >= 1024) {
                            $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';
                        } else {
                            $fileSizeFormatted = $fileSize . ' bytes';
                        }
                    @endphp
                    <div class="group border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-blue-300 transition-all duration-200 bg-gray-50">
                        @if($isImage)
                            <div class="w-full h-40 rounded-lg overflow-hidden mb-3 bg-gray-100">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </div>
                        @elseif($isPdf)
                            <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="w-full h-40 rounded-lg bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center mb-3 border border-red-200 cursor-pointer hover:bg-red-100 transition-colors">
                                <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                            </a>
                        @else
                            <div class="w-full h-40 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-3 border border-gray-300">
                                <i class="fas fa-file text-5xl text-gray-400"></i>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $attachment->file_name }}">
                                {{ $attachment->file_name }}
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500">
                                    {{ $fileSizeFormatted }}
                                </p>
                                @if($isPdf)
                                    <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                        <span>View PDF</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.media-library.download', $attachment->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download text-xs"></i>
                                        <span>Download</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Review Information -->
    @if($material->status !== 'pending')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-gray-500 to-gray-600 rounded-full"></div>
            <span>Review Information</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Reviewed By</label>
                <p class="text-sm font-semibold text-gray-900 mt-1">
                    {{ $material->reviewer ? $material->reviewer->first_name . ' ' . $material->reviewer->last_name : 'N/A' }}
                </p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Reviewed At</label>
                <p class="text-sm font-semibold text-gray-900 mt-1">
                    {{ $material->reviewed_at ? $material->reviewed_at->format('M d, Y g:i A') : 'N/A' }}
                </p>
            </div>
            @if($material->status === 'rejected' && $material->rejection_reason)
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Rejection Reason</label>
                <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $material->rejection_reason }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($material->status === 'pending' && $material->notice->created_by === Auth::id())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
        <div class="flex gap-3">
            <button onclick="approveMaterial({{ $material->id }})" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                <i class="fas fa-check mr-2"></i>Approve
            </button>
            <button onclick="rejectMaterial({{ $material->id }})" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                <i class="fas fa-times mr-2"></i>Reject
            </button>
        </div>
    </div>
    @endif
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

