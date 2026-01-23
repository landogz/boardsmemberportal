@extends('admin.layout')

@section('title', 'Attendance Confirmation Details')

@php
    $pageTitle = 'Attendance Confirmation Details';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.attendance-confirmations.index'),
        'text' => 'Back to List',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6 space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        @php
                            $typeColors = [
                                'Notice of Meeting' => 'bg-blue-500 text-white',
                                'Agenda' => 'bg-purple-500 text-white',
                                'Other Matters' => 'bg-gray-500 text-white'
                            ];
                            $typeColor = $typeColors[$notice->notice_type] ?? 'bg-gray-500 text-white';
                        @endphp
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide {{ $typeColor }}">
                            {{ $notice->notice_type }}
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-2">{{ $notice->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        @if($notice->meeting_date)
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                <span>{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</span>
                            </div>
                        @endif
                        @if($notice->meeting_time)
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-clock text-xs"></i>
                                <span>{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $accepted = $invitedUsers->where('status', 'accepted')->count();
        $declined = $invitedUsers->where('status', 'declined')->count();
        $pending = $invitedUsers->where('status', 'pending')->count();
        $total = count($invitedUsers);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-gray-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-700">{{ $total }}</div>
                    <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Invited</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-green-700">{{ $accepted }}</div>
                    <div class="text-xs font-medium text-green-600 uppercase tracking-wide">Accepted</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl p-5 border border-red-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-red-700">{{ $declined }}</div>
                    <div class="text-xs font-medium text-red-600 uppercase tracking-wide">Declined</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-yellow-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-yellow-700">{{ $pending }}</div>
                    <div class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invited Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Invited Users</span>
                <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $total }}</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Agency</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Declined Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Agenda Request</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invitedUsers as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @php
                                    $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($item['user']->first_name . ' ' . $item['user']->last_name) . '&size=40&background=055498&color=fff&bold=true';
                                    if ($item['user']->profile_picture) {
                                        $media = \App\Models\MediaLibrary::find($item['user']->profile_picture);
                                        if ($media) {
                                            $profilePic = asset('storage/' . $media->file_path);
                                        }
                                    }
                                @endphp
                                <img src="{{ $profilePic }}" alt="{{ $item['user']->first_name }} {{ $item['user']->last_name }}" class="w-10 h-10 rounded-lg object-cover border-2 border-blue-200 shadow-sm">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $item['user']->first_name }} {{ $item['user']->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['user']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $item['user']->governmentAgency ? $item['user']->governmentAgency->name : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                @php
                                    $statusColors = [
                                        'accepted' => 'bg-green-100 text-green-700 border-green-200',
                                        'declined' => 'bg-red-100 text-red-700 border-red-200',
                                        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200'
                                    ];
                                    $statusColor = $statusColors[$item['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusColor }} whitespace-nowrap">
                                    <i class="fas fa-{{ $item['status'] === 'accepted' ? 'check-circle' : ($item['status'] === 'declined' ? 'times-circle' : 'clock') }} mr-1.5"></i>
                                    {{ ucfirst($item['status']) }}
                                </span>
                                @if($item['status'] === 'declined' && isset($item['user']) && $item['user'] && isset($item['user']->id) && isset($item['user']->email))
                                    <button 
                                        data-notice-id="{{ $notice->id }}"
                                        data-user-id="{{ $item['user']->id }}"
                                        class="re-invite-btn px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap min-h-[32px]"
                                        title="Re-invite this user"
                                    >
                                        <i class="fas fa-paper-plane text-xs"></i>
                                        <span>Re-invite</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item['declined_reason'])
                                <div class="text-sm text-gray-700 max-w-xs">{{ Str::limit($item['declined_reason'], 100) }}</div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item['agenda_request'])
                                @php
                                    $agendaStatusColors = [
                                        'approved' => 'bg-green-100 text-green-700 border-green-200',
                                        'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200'
                                    ];
                                    $agendaStatusColor = $agendaStatusColors[$item['agenda_request']->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $agendaStatusColor }}">
                                    <i class="fas fa-{{ $item['agenda_request']->status === 'approved' ? 'check' : ($item['agenda_request']->status === 'rejected' ? 'times' : 'clock') }} mr-1.5"></i>
                                    {{ ucfirst($item['agenda_request']->status) }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        // Set up axios defaults
        if (typeof axios !== 'undefined') {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }

        function reInviteUser(noticeId, userId) {
            if (typeof Swal === 'undefined') {
                alert('SweetAlert2 is not loaded. Please refresh the page.');
                return;
            }

            Swal.fire({
                title: 'Re-invite User?',
                text: 'This will send the notice again to the user and reset their status to pending.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Re-invite',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#055498',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Sending invitation...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    // Build the URL dynamically
                    var url = '/admin/attendance-confirmations/' + encodeURIComponent(noticeId) + '/re-invite';
                    
                    // Ensure user_id is sent as a string (UUID)
                    axios.post(url, {
                        user_id: String(userId)
                    })
                    .then(function(response) {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.data.message || 'User has been re-invited successfully.',
                                confirmButtonColor: '#055498',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(function() {
                                // Reload the page to show updated status
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.data.message || 'Failed to re-invite user. Please try again.',
                                confirmButtonColor: '#055498'
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Error re-inviting user:', error);
                        var errorMessage = 'Failed to re-invite user. Please try again.';
                        
                        if (error.response) {
                            // Server responded with error
                            if (error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            } else if (error.response.status === 404) {
                                errorMessage = 'User or notice not found. The user may have been deleted.';
                            } else if (error.response.status === 403) {
                                errorMessage = 'You do not have permission to perform this action.';
                            }
                        } else if (error.request) {
                            // Request was made but no response received
                            errorMessage = 'No response from server. Please check your connection.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#055498'
                        });
                    });
                }
            });
        }

        // Attach event listeners to all re-invite buttons
        document.addEventListener('DOMContentLoaded', function() {
            const reInviteButtons = document.querySelectorAll('.re-invite-btn');
            reInviteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-notice-id');
                    const userId = this.getAttribute('data-user-id');
                    if (noticeId && userId) {
                        // Convert to appropriate types - noticeId is integer, userId is UUID string
                        reInviteUser(parseInt(noticeId), String(userId));
                    }
                });
            });
        });
    })();
</script>
@endpush
@endsection
