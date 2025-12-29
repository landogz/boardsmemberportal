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

@section('content')
<div class="p-4 lg:p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Banner Image -->
        @if($announcement->bannerImage)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $announcement->bannerImage->file_path) }}" alt="Banner" class="w-full h-64 object-cover rounded-lg">
            </div>
        @endif

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>

        <!-- Meta Information -->
        <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-600">
            <div class="flex items-center">
                <i class="fas fa-user mr-2"></i>
                <span>By {{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-calendar mr-2"></i>
                <span>{{ $announcement->created_at->format('F d, Y') }}</span>
            </div>
            <div class="flex items-center">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $announcement->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($announcement->status) }}
                </span>
            </div>
            @if($announcement->scheduled_at)
                <div class="flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Scheduled: {{ $announcement->scheduled_at->format('F d, Y H:i') }}</span>
                </div>
            @endif
        </div>

        <!-- Description -->
        <div class="prose max-w-none mb-6">
            <div class="text-gray-700">{!! $announcement->description !!}</div>
        </div>

        <!-- Allowed Users -->
        <div class="border-t pt-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Allowed Users ({{ $announcement->allowedUsers->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($announcement->allowedUsers as $user)
                    @php
                        $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=64&background=055498&color=fff';
                        if ($user->profile_picture) {
                            $media = \App\Models\MediaLibrary::find($user->profile_picture);
                            if ($media) {
                                $profilePic = asset('storage/' . $media->file_path);
                            }
                        }
                    @endphp
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color: #055498;">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

