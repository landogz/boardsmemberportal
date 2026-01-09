@extends('admin.layout')

@section('title', 'View Announcement')

@php
    $pageTitle = 'View Announcement';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.announcements.index'),
        'text' => 'Back to Announcements',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    if (Auth::user()->hasPermission('edit announcements')) {
        $headerActions[] = [
            'url' => route('admin.announcements.edit', $announcement->id),
            'text' => 'Edit',
            'icon' => 'fas fa-edit',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-published {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .status-draft {
        background-color: rgba(156, 163, 175, 0.1);
        color: #6B7280;
        border: 1px solid rgba(156, 163, 175, 0.2);
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6 space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Banner Image -->
        @if($announcement->bannerImage)
            <div class="relative w-full h-64 lg:h-80 overflow-hidden bg-gradient-to-br from-yellow-50 to-yellow-100">
                <img src="{{ asset('storage/' . $announcement->bannerImage->file_path) }}" alt="Banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>
        @endif

        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="status-badge {{ $announcement->status === 'published' ? 'status-published' : 'status-draft' }}">
                            <i class="fas fa-{{ $announcement->status === 'published' ? 'check-circle' : 'file-alt' }} mr-1.5"></i>
                            {{ ucfirst($announcement->status) }}
                        </span>
                        @if($announcement->scheduled_at)
                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white text-gray-600 border border-gray-200">
                                <i class="fas fa-clock mr-1.5"></i>
                                Scheduled: {{ $announcement->scheduled_at->format('M d, Y H:i') }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-3">{{ $announcement->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            @php
                                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($announcement->creator->first_name . ' ' . $announcement->creator->last_name) . '&size=32&background=055498&color=fff&bold=true';
                                if ($announcement->creator->profile_picture) {
                                    $media = \App\Models\MediaLibrary::find($announcement->creator->profile_picture);
                                    if ($media) {
                                        $profilePic = asset('storage/' . $media->file_path);
                                    }
                                }
                            @endphp
                            <img src="{{ $profilePic }}" alt="{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}" class="w-8 h-8 rounded-full object-cover border-2 border-yellow-200 shadow-sm">
                            <span class="font-medium text-gray-700">{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-calendar text-xs"></i>
                            <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-users text-xs"></i>
                            <span>{{ $announcement->allowedUsers->count() }} Allowed User(s)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Content -->
        <div class="px-6 py-6">
            <div class="prose prose-lg max-w-none">
                <div class="text-gray-700 leading-relaxed">{!! $announcement->description !!}</div>
            </div>
        </div>
    </div>

    <!-- Allowed Users Card -->
    @if($announcement->allowedUsers->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-yellow-400 to-amber-500 rounded-full"></div>
                <span>Allowed Users</span>
            </h3>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                {{ $announcement->allowedUsers->count() }} User(s)
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($announcement->allowedUsers as $user)
                @php
                    $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=48&background=055498&color=fff&bold=true';
                    if ($user->profile_picture) {
                        $media = \App\Models\MediaLibrary::find($user->profile_picture);
                        if ($media) {
                            $profilePic = asset('storage/' . $media->file_path);
                        }
                    }
                @endphp
                <div class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
                    <div class="flex-shrink-0">
                        <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-12 h-12 rounded-full object-cover border-2 border-yellow-200 shadow-sm">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

