@extends('admin.layout')

@section('title', 'Announcements')

@php
    $pageTitle = 'Announcements';
    $headerActions = [];
    if (Auth::user()->hasPermission('create announcements')) {
        $headerActions[] = [
            'url' => route('admin.announcements.create'),
            'text' => 'Create Announcement',
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
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-published {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    .status-draft {
        background-color: rgba(156, 163, 175, 0.1);
        color: #6B7280;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800">All Announcements</h2>
            <p class="text-sm text-gray-600 mt-1">Manage announcements and their visibility</p>
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

        @if($announcements->count() > 0)
        <div class="overflow-x-auto">
            <table id="announcementsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowed Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($announcements as $announcement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($announcement->title, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge {{ $announcement->status === 'published' ? 'status-published' : 'status-draft' }}">
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $announcement->allowedUsers->count() }} user(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $announcement->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.announcements.show', $announcement->id) }}" class="text-[#055498] hover:text-[#123a60]">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->hasPermission('edit announcements'))
                                <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="text-[#055498] hover:text-[#123a60]">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if(Auth::user()->hasPermission('delete announcements'))
                                <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-bullhorn text-6xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">No announcements found.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        @if($announcements->count() > 0)
        $('#announcementsTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
        });
        @endif
    });
</script>
@endpush

