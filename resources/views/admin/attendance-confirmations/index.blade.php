@extends('admin.layout')

@section('title', 'Attendance Confirmations')

@php
    $pageTitle = 'Attendance Confirmations';
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .stats-card {
        background: linear-gradient(135deg, #055498 0%, #123a60 100%);
        color: white;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .stat-item {
        text-align: center;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 12px;
        opacity: 0.9;
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
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Attendance Confirmations</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">View attendance confirmations and agenda requests for all notices</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            @if($notices->count() > 0)
            <div class="overflow-x-auto">
                <table id="attendanceTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Invited</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Declined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agenda Requests</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notices as $notice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ Str::limit($notice->title, 40) }}</div>
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
                                @if($notice->meeting_date)
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</div>
                                @endif
                                @if($notice->meeting_time)
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $notice->stats['total_invited'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-green-600">{{ $notice->stats['accepted'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-red-600">{{ $notice->stats['declined'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-yellow-600">{{ $notice->stats['pending'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-blue-600">{{ $notice->agendaInclusionRequests->count() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.attendance-confirmations.show', $notice->id) }}" class="text-[#055498] hover:text-[#123a60]">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No notices found</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            order: [[1, 'desc']],
            pageLength: 15,
        });
    });
</script>
@endpush
@endsection

