@extends('admin.layout')

@section('title', 'Agenda Request Details')

@php
    $pageTitle = 'Agenda Request Details';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.agenda-inclusion-requests.index'),
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
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-500 text-white',
                                'approved' => 'bg-green-500 text-white',
                                'rejected' => 'bg-red-500 text-white'
                            ];
                            $statusColor = $statusColors[$request->status] ?? 'bg-gray-500 text-white';
                        @endphp
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide {{ $statusColor }}">
                            <i class="fas fa-{{ $request->status === 'approved' ? 'check-circle' : ($request->status === 'rejected' ? 'times-circle' : 'clock') }} mr-1.5"></i>
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-2">Agenda Request</h1>
                    <p class="text-gray-600">Review request details and attachments</p>
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
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $request->notice->title }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Notice Type</label>
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
                <span class="notice-type-badge {{ $typeClass }} mt-1">
                    {{ $request->notice->notice_type }}
                </span>
            </div>
            @if($request->notice->meeting_date)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Meeting Date</label>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($request->notice->meeting_date)->format('F d, Y') }}</p>
                </div>
            @endif
            @if($request->notice->meeting_time)
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Meeting Time</label>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($request->notice->meeting_time)->format('g:i A') }}</p>
                </div>
            @endif
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.notices.show', $request->notice->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium text-sm transition-all duration-200 border border-blue-200">
                <i class="fas fa-external-link-alt"></i>
                <span>View Notice</span>
            </a>
        </div>
    </div>

    <!-- Requester Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-purple-500 to-indigo-600 rounded-full"></div>
            <span>Requested By</span>
        </h3>
        <div class="flex items-center gap-4">
            @php
                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($request->user->first_name . ' ' . $request->user->last_name) . '&size=64&background=7C3AED&color=fff&bold=true';
                if ($request->user->profile_picture) {
                    $media = \App\Models\MediaLibrary::find($request->user->profile_picture);
                    if ($media) {
                        $profilePic = asset('storage/' . $media->file_path);
                    }
                }
            @endphp
            <img src="{{ $profilePic }}" alt="{{ $request->user->first_name }} {{ $request->user->last_name }}" class="w-16 h-16 rounded-xl object-cover border-2 border-purple-200 shadow-lg">
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $request->user->first_name }} {{ $request->user->last_name }}</p>
                <p class="text-sm text-gray-600 mt-0.5">{{ $request->user->email }}</p>
                @if($request->user->governmentAgency)
                    <p class="text-sm text-gray-500 mt-1">{{ $request->user->governmentAgency->name }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-gray-500 to-gray-600 rounded-full"></div>
            <span>Description</span>
        </h3>
        <div class="prose max-w-none">
            <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $request->description }}</p>
        </div>
    </div>

    <!-- Attachments -->
    @if($request->attachments && count($request->attachments) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Attachments</span>
                <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ count($request->attachments) }}</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($request->attachment_media as $attachment)
                    @php
                        $isImage = in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
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
                                    {{ number_format($attachment->file_size / 1024, 2) }} KB
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
    @if($request->status !== 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full"></div>
                <span>Review Information</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Reviewed By</label>
                    <p class="text-sm font-semibold text-gray-900 mt-1">
                        {{ $request->reviewer ? $request->reviewer->first_name . ' ' . $request->reviewer->last_name : 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Reviewed At</label>
                    <p class="text-sm font-semibold text-gray-900 mt-1">
                        {{ $request->reviewed_at ? $request->reviewed_at->format('F d, Y g:i A') : 'N/A' }}
                    </p>
                </div>
                @if($request->status === 'rejected' && $request->rejection_reason)
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Rejection Reason</label>
                        <div class="mt-2 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-medium text-red-900">{{ $request->rejection_reason }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    @if($request->status === 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-end gap-3">
                <button onclick="rejectRequest({{ $request->id }})" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-times"></i>
                    <span>Reject</span>
                </button>
                <button onclick="approveRequest({{ $request->id }})" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-check"></i>
                    <span>Approve</span>
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
            <span>Reject Agenda Request</span>
        </h3>
        <form id="rejectForm">
            <input type="hidden" id="rejectRequestId" name="request_id">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Rejection Reason <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="rejectReason" 
                    name="reason" 
                    rows="4" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                    placeholder="Please provide a reason for rejecting this request..."
                ></textarea>
            </div>
            <div class="flex gap-3">
                <button 
                    type="button" 
                    onclick="closeRejectModal()" 
                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors"
                >
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    function approveRequest(id) {
        Swal.fire({
            title: 'Approve Request?',
            text: 'Are you sure you want to approve this agenda request?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, approve',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                axios.post(`/admin/agenda-inclusion-requests/${id}/approve`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.data.message,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-xl'
                                }
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
                            customClass: {
                                popup: 'rounded-xl'
                            }
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
        
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            customClass: {
                popup: 'rounded-xl'
            }
        });
        
        axios.post(`/admin/agenda-inclusion-requests/${requestId}/reject`, { reason })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
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
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            });
    });
</script>
@endpush
@endsection
