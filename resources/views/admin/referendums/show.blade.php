@extends('admin.layout')

@section('title', 'Referendum Details')

@php
    $pageTitle = 'Referendum Details';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.referendums.index'),
        'text' => 'Back to Referendums',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    if (Auth::user()->hasPermission('edit referendum')) {
        $headerActions[] = [
            'url' => route('admin.referendums.edit', $referendum->id),
            'text' => 'Edit Referendum',
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
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    .status-expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }
    .comment-reply {
        margin-left: 2rem;
        padding-left: 0.75rem;
        border-left: 2px solid #e5e7eb;
        margin-top: 0.75rem;
    }
    
    /* Limit maximum indentation for deeply nested comments */
    .comment-reply .comment-reply {
        margin-left: 1.5rem;
        padding-left: 0.5rem;
    }
    
    .comment-reply .comment-reply .comment-reply {
        margin-left: 1.25rem;
        padding-left: 0.5rem;
    }
    
    /* For deeper nesting, use minimal indentation */
    .comment-reply .comment-reply .comment-reply .comment-reply {
        margin-left: 1rem;
        padding-left: 0.5rem;
        border-left-color: #d1d5db;
    }
    
    /* Even deeper - no more indentation, just subtle border */
    .comment-reply .comment-reply .comment-reply .comment-reply .comment-reply {
        margin-left: 0.75rem;
        padding-left: 0.5rem;
        border-left-color: #e5e7eb;
        border-left-width: 1px;
    }
    
    /* Maximum depth - no indentation, just spacing */
    .comment-reply .comment-reply .comment-reply .comment-reply .comment-reply .comment-reply {
        margin-left: 0;
        padding-left: 0.5rem;
        border-left: none;
    }
    .comment-item {
        transition: background-color 0.2s ease;
    }
    .comment-item:hover {
        background-color: #f9fafb;
    }
    .online-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background-color: #10B981;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
    }
    .fb-comment-text {
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.3333;
    }
    .fb-comment-text.truncated {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .see-more-btn, .see-less-btn {
        color: #65676b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
        padding: 0;
        background: none;
        border: none;
        display: inline-block;
    }
    .see-more-btn:hover, .see-less-btn:hover {
        text-decoration: underline;
    }
    .see-more-btn.hidden, .see-less-btn.hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $referendum->title }}</h2>
        <div class="flex items-center space-x-4 mt-2">
            <span class="status-badge {{ $referendum->isExpired() ? 'status-expired' : 'status-active' }}">
                {{ $referendum->isExpired() ? 'Expired' : 'Active' }}
            </span>
            <span class="text-sm text-gray-600">
                Created by: {{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}
            </span>
            <span class="text-sm text-gray-600">
                Expires: {{ $referendum->expires_at->format('M d, Y h:i A') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Content</h3>
                <div class="prose max-w-none text-gray-700 whitespace-pre-wrap">
                    {{ $referendum->content }}
                </div>
            </div>

            <!-- Attachments -->
            @if($referendum->attachments && count($referendum->attachments) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-paperclip mr-2" style="color: #055498;"></i>
                    Attachments
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($referendum->attachments as $index => $attachmentId)
                        @php
                            $media = \App\Models\MediaLibrary::find($attachmentId);
                        @endphp
                        @if($media)
                            @php
                                $isImage = str_starts_with($media->file_type, 'image/');
                                $isPdf = str_ends_with(strtolower($media->file_name), '.pdf') || $media->file_type === 'application/pdf';
                            @endphp
                            <div class="border rounded-lg p-2 hover:shadow-md transition-shadow">
                                @if($isImage)
                                    <img 
                                        src="{{ asset('storage/' . $media->file_path) }}" 
                                        alt="{{ $media->file_name }}" 
                                        class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-90 transition-opacity"
                                        onclick="openAttachmentGallery({{ $index }})"
                                    >
                                @elseif($isPdf)
                                    <div 
                                        class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded cursor-pointer hover:bg-gray-200 transition"
                                        onclick="openGlobalPdfModal('{{ asset('storage/' . $media->file_path) }}', '{{ $media->file_name }}')"
                                    >
                                        <i class="fas fa-file-pdf text-3xl text-red-500 mb-1"></i>
                                        <p class="text-xs text-gray-600 text-center px-1 truncate w-full">{{ $media->file_name }}</p>
                                    </div>
                                @else
                                    <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                        <i class="fas fa-file text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-600 mt-1 truncate" title="{{ $media->file_name }}">{{ $media->file_name }}</p>
                                @if($isPdf)
                                    <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $media->file_path) }}', '{{ $media->file_name }}')" class="text-xs text-blue-600 hover:underline mt-1 block">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $media->file_path) }}" download="{{ $media->file_name }}" class="text-xs text-blue-600 hover:underline mt-1 block">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Comments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-comments mr-2" style="color: #055498;"></i>
                        Comments 
                        <span class="text-sm font-normal text-gray-500">({{ $totalComments }})</span>
                    </h3>
                    <button type="button" id="toggleCommentsBtn" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-eye" id="toggleCommentsIcon"></i>
                        <span id="toggleCommentsText">Hide</span>
                    </button>
                </div>
                <div id="commentsContent">
                @if($comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            @php
                                // Get user profile picture
                                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->first_name . ' ' . $comment->user->last_name) . '&size=64&background=055498&color=fff';
                                if ($comment->user->profile_picture) {
                                    $media = \App\Models\MediaLibrary::find($comment->user->profile_picture);
                                    if ($media) {
                                        $profilePic = asset('storage/' . $media->file_path);
                                    }
                                }
                                
                                // Check if user is online
                                $isOnline = $comment->user->is_online ?? false;
                            @endphp
                            <div class="comment-item border-b border-gray-200 pb-4 last:border-b-0 px-3 py-2 -mx-3 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <!-- Profile Picture with Online Indicator -->
                                    <div class="flex-shrink-0 relative">
                                        <img src="{{ $profilePic }}" 
                                             alt="{{ $comment->user->first_name }} {{ $comment->user->last_name }}" 
                                             class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                                        @if($isOnline)
                                            <span class="online-indicator"></span>
                                        @endif
                                    </div>
                                    
                                    <!-- Comment Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-gray-50 rounded-2xl px-4 py-2.5 inline-block max-w-full">
                                            <div class="flex items-baseline space-x-2 mb-1">
                                                <span class="font-semibold text-sm text-gray-900 hover:underline cursor-pointer">
                                                    {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                                                </span>
                                                @if($isOnline)
                                                    <span class="text-xs text-green-600 font-medium">● Online</span>
                                                @endif
                                            </div>
                                            <div class="fb-comment-text-wrapper">
                                                <div class="fb-comment-text text-sm text-gray-800" data-full-text="{{ htmlspecialchars($comment->content, ENT_QUOTES, 'UTF-8') }}">{{ $comment->content }}</div>
                                                <button type="button" class="see-more-btn hidden">See more</button>
                                                <button type="button" class="see-less-btn hidden">See less</button>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4 mt-2 ml-1">
                                            <span class="text-xs text-gray-500 hover:underline cursor-pointer">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <!-- Replies -->
                                        @if($comment->replies->count() > 0)
                                            <div class="mt-3 comment-replies-wrapper">
                                                <!-- View/Hide replies toggle -->
                                                <button 
                                                    type="button" 
                                                    class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer flex items-center gap-1 mb-2"
                                                    data-comment-id="{{ $comment->id }}"
                                                >
                                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                                    <span>
                                                        View all {{ $comment->replies->count() }} {{ $comment->replies->count() === 1 ? 'reply' : 'replies' }}
                                                    </span>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer flex items-center gap-1 hidden mb-2"
                                                    data-comment-id="{{ $comment->id }}"
                                                >
                                                    <i class="fas fa-chevron-up text-[10px]"></i>
                                                    <span>Hide replies</span>
                                                </button>
                                                
                                                <div class="replies-container hidden mt-1">
                                                    @foreach($comment->replies as $reply)
                                                        @include('admin.referendums.partials.reply', ['reply' => $reply])
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No comments yet. Be the first to comment!</p>
                    </div>
                @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Analytics -->
        <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-4 lg:self-start lg:max-h-[calc(100vh-2rem)] lg:overflow-y-auto">
            <!-- Vote Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vote Statistics</h3>
                <div class="space-y-4">
                    <button type="button" onclick="showVotersModal('accept')" class="w-full flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <span class="font-medium text-gray-700">Accept</span>
                        </div>
                        <span class="text-xl font-bold text-green-600">{{ $acceptVotes->count() }}</span>
                    </button>
                    <button type="button" onclick="showVotersModal('decline')" class="w-full flex items-center justify-between p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-times-circle text-red-600"></i>
                            <span class="font-medium text-gray-700">Decline</span>
                        </div>
                        <span class="text-xl font-bold text-red-600">{{ $declineVotes->count() }}</span>
                    </button>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Total Votes</span>
                        <span class="text-xl font-bold text-gray-800">{{ $totalVotes }}</span>
                    </div>
                </div>
            </div>

            <!-- Accept Voters -->
            @if($acceptVotes->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Accept Votes 
                    <span class="text-sm font-normal text-gray-500">({{ $acceptVotes->count() }})</span>
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($acceptVotes as $vote)
                        @php
                            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($vote->user->first_name . ' ' . $vote->user->last_name) . '&size=64&background=10B981&color=fff';
                            if ($vote->user->profile_picture) {
                                $media = \App\Models\MediaLibrary::find($vote->user->profile_picture);
                                if ($media) {
                                    $profilePic = asset('storage/' . $media->file_path);
                                }
                            }
                        @endphp
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <img src="{{ $profilePic }}" alt="{{ $vote->user->first_name }} {{ $vote->user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2 border-green-600">
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-700 block truncate">
                                    {{ $vote->user->first_name }} {{ $vote->user->last_name }}
                                </span>
                                <span class="text-xs text-gray-500 truncate block">{{ $vote->user->email }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Decline Voters -->
            @if($declineVotes->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Decline Votes 
                    <span class="text-sm font-normal text-gray-500">({{ $declineVotes->count() }})</span>
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($declineVotes as $vote)
                        @php
                            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($vote->user->first_name . ' ' . $vote->user->last_name) . '&size=64&background=EF4444&color=fff';
                            if ($vote->user->profile_picture) {
                                $media = \App\Models\MediaLibrary::find($vote->user->profile_picture);
                                if ($media) {
                                    $profilePic = asset('storage/' . $media->file_path);
                                }
                            }
                        @endphp
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <img src="{{ $profilePic }}" alt="{{ $vote->user->first_name }} {{ $vote->user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2 border-red-600">
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-700 block truncate">
                                    {{ $vote->user->first_name }} {{ $vote->user->last_name }}
                                </span>
                                <span class="text-xs text-gray-500 truncate block">{{ $vote->user->email }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Allowed Users -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Allowed Users 
                    <span class="text-sm font-normal text-gray-500">({{ $referendum->allowedUsers->count() }})</span>
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($referendum->allowedUsers as $user)
                        @php
                            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=64&background=055498&color=fff';
                            if ($user->profile_picture) {
                                $media = \App\Models\MediaLibrary::find($user->profile_picture);
                                if ($media) {
                                    $profilePic = asset('storage/' . $media->file_path);
                                }
                            }
                        @endphp
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color: #055498;">
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-700 block truncate">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </span>
                                <span class="text-xs text-gray-500 truncate block">{{ $user->email }}</span>
                            </div>
                            @if($user->privilege === 'consec')
                                <span class="ml-auto px-2 py-0.5 text-xs rounded font-medium flex-shrink-0" style="background-color: #055498; color: #ffffff;">CONSEC</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Voters Modal -->
<div id="votersModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800"></h3>
            <button type="button" onclick="closeVotersModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="px-6 py-4 overflow-y-auto flex-1">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Image Gallery Modal -->
<div id="imageGalleryModal" class="fixed inset-0 bg-black bg-opacity-90 z-[100] hidden flex items-center justify-center">
    <div class="relative w-full h-full flex items-center justify-center">
        <!-- Close Button -->
        <button 
            onclick="closeImageGallery()" 
            class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 p-2 transition-colors"
            style="background: rgba(0,0,0,0.5); border-radius: 50%;"
        >
            <i class="fas fa-times text-2xl"></i>
        </button>

        <!-- Previous Button -->
        <button 
            onclick="previousImage()" 
            id="prevImageBtn"
            class="absolute left-4 text-white hover:text-gray-300 z-10 p-3 rounded-full transition hidden"
            style="background: rgba(0,0,0,0.5);"
        >
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>

        <!-- Next Button -->
        <button 
            onclick="nextImage()" 
            id="nextImageBtn"
            class="absolute right-4 text-white hover:text-gray-300 z-10 p-3 rounded-full transition hidden"
            style="background: rgba(0,0,0,0.5);"
        >
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>

        <!-- Image Container -->
        <div class="flex items-center justify-center w-full h-full p-4">
            <img 
                id="galleryImage" 
                src="" 
                alt="Gallery Image" 
                class="max-w-full max-h-full object-contain"
            >
        </div>

        <!-- Image Counter -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm px-4 py-2 rounded-full" style="background: rgba(0,0,0,0.5);">
            <span id="imageCounter">1 / 1</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    const acceptVoters = @json($acceptVotersData);
    const declineVoters = @json($declineVotersData);

    function showVotersModal(type) {
        const modal = document.getElementById('votersModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        let voters, title, icon, iconColor;
        
        if (type === 'accept') {
            voters = acceptVoters;
            title = 'Accept Votes';
            icon = 'fa-check-circle';
            iconColor = 'text-green-600';
        } else {
            voters = declineVoters;
            title = 'Decline Votes';
            icon = 'fa-times-circle';
            iconColor = 'text-red-600';
        }
        
        modalTitle.innerHTML = `<i class="fas ${icon} ${iconColor} mr-2"></i>${title} (${voters.length})`;
        
        if (voters.length === 0) {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas ${icon} ${iconColor} text-4xl mb-3"></i>
                    <p class="text-gray-500">No ${type} votes yet.</p>
                </div>
            `;
        } else {
            let html = '<div class="space-y-2">';
            voters.forEach((voter, index) => {
                const profilePic = voter.profile_picture || `https://ui-avatars.com/api/?name=${encodeURIComponent(voter.name)}&size=64&background=${type === 'accept' ? '10B981' : 'EF4444'}&color=fff`;
                html += `
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <img src="${profilePic}" alt="${voter.name}" class="w-10 h-10 rounded-full object-cover border-2 ${type === 'accept' ? 'border-green-600' : 'border-red-600'}">
                            <div>
                                <p class="font-medium text-gray-800">${voter.name}</p>
                                <p class="text-sm text-gray-500">${voter.email}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Voted on</p>
                            <p class="text-sm text-gray-700">${voter.voted_at}</p>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            modalContent.innerHTML = html;
        }
        
        modal.classList.remove('hidden');
    }
    
    function closeVotersModal() {
        document.getElementById('votersModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('votersModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVotersModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVotersModal();
            closeImageGallery();
        }
    });

    // Attachment Gallery Data
    @php
        $attachmentGalleryData = [];
        if($referendum->attachments && count($referendum->attachments) > 0) {
            foreach($referendum->attachments as $attachmentId) {
                $media = \App\Models\MediaLibrary::find($attachmentId);
                if($media) {
                    $isImage = str_starts_with($media->file_type, 'image/');
                    $isPdf = str_ends_with(strtolower($media->file_name), '.pdf') || $media->file_type === 'application/pdf';
                    $attachmentGalleryData[] = [
                        'url' => asset('storage/' . $media->file_path),
                        'name' => $media->file_name,
                        'isImage' => $isImage,
                        'isPdf' => $isPdf
                    ];
                }
            }
        }
    @endphp
    const attachmentGalleryData = @json($attachmentGalleryData);
    
    let currentImageIndex = 0;

    // Open Attachment Gallery
    function openAttachmentGallery(index) {
        if (attachmentGalleryData.length === 0) return;
        
        currentImageIndex = index;
        const item = attachmentGalleryData[currentImageIndex];
        
        // If it's a PDF, open in PDF modal
        if (item.isPdf) {
            if (typeof openGlobalPdfModal === 'function') {
                openGlobalPdfModal(item.url, item.name);
            } else {
                window.open(item.url, '_blank');
            }
            return;
        }
        
        // If it's not an image, just download
        if (!item.isImage) {
            window.open(item.url, '_blank');
            return;
        }
        
        // Otherwise, open in image gallery
        const modal = document.getElementById('imageGalleryModal');
        const galleryImage = document.getElementById('galleryImage');
        const imageCounter = document.getElementById('imageCounter');
        const prevBtn = document.getElementById('prevImageBtn');
        const nextBtn = document.getElementById('nextImageBtn');

        // Filter only images for navigation
        const imageItems = attachmentGalleryData.filter(item => item.isImage);
        const imageIndex = imageItems.findIndex(img => img.url === item.url);
        
        // Show/hide navigation buttons
        if (imageItems.length > 1) {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        }

        updateGalleryImage();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Update Gallery Image
    function updateGalleryImage() {
        const galleryImage = document.getElementById('galleryImage');
        const imageCounter = document.getElementById('imageCounter');
        
        // Find current image index in filtered image list
        const imageItems = attachmentGalleryData.filter(item => item.isImage);
        let imageIndex = 0;
        for (let i = 0; i <= currentImageIndex; i++) {
            if (attachmentGalleryData[i].isImage) {
                imageIndex = imageItems.findIndex(item => item.url === attachmentGalleryData[i].url);
            }
        }
        
        if (imageItems[imageIndex]) {
            galleryImage.src = imageItems[imageIndex].url;
            galleryImage.alt = imageItems[imageIndex].name;
            imageCounter.textContent = `${imageIndex + 1} / ${imageItems.length}`;
        }
    }

    // Next Image (skip non-images)
    function nextImage() {
        const imageItems = attachmentGalleryData.filter(item => item.isImage);
        if (imageItems.length <= 1) return;
        
        do {
            if (currentImageIndex < attachmentGalleryData.length - 1) {
                currentImageIndex++;
            } else {
                currentImageIndex = 0; // Loop to first
            }
        } while (!attachmentGalleryData[currentImageIndex].isImage && attachmentGalleryData.length > 1);
        
        if (attachmentGalleryData[currentImageIndex].isImage) {
            updateGalleryImage();
        }
    }

    // Previous Image (skip non-images)
    function previousImage() {
        const imageItems = attachmentGalleryData.filter(item => item.isImage);
        if (imageItems.length <= 1) return;
        
        do {
            if (currentImageIndex > 0) {
                currentImageIndex--;
            } else {
                currentImageIndex = attachmentGalleryData.length - 1; // Loop to last
            }
        } while (!attachmentGalleryData[currentImageIndex].isImage && attachmentGalleryData.length > 1);
        
        if (attachmentGalleryData[currentImageIndex].isImage) {
            updateGalleryImage();
        }
    }

    // Close Image Gallery
    function closeImageGallery() {
        const modal = document.getElementById('imageGalleryModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Keyboard navigation for image gallery
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('imageGalleryModal');
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeImageGallery();
            }
        }
    });

    // Close on outside click
    document.getElementById('imageGalleryModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageGallery();
        }
    });

    // Check and truncate long comments (more than 4 lines) - including replies
    function checkAndTruncateComments() {
        $('.fb-comment-text').each(function() {
            const $text = $(this);
            const $wrapper = $text.closest('.fb-comment-text-wrapper');
            const $seeMoreBtn = $wrapper.find('.see-more-btn');
            const $seeLessBtn = $wrapper.find('.see-less-btn');
            
            // Reset to check actual height - temporarily remove truncated class
            $text.removeClass('truncated');
            
            // Get the line height (approximately)
            const lineHeight = parseFloat($text.css('line-height')) || 20;
            const maxHeight = lineHeight * 4; // 4 lines
            
            // Check if content exceeds 4 lines
            if ($text[0].scrollHeight > maxHeight) {
                // Truncate - content exceeds 4 lines
                $text.addClass('truncated');
                $seeMoreBtn.removeClass('hidden');
                $seeLessBtn.addClass('hidden');
            } else {
                // No truncation needed
                $text.removeClass('truncated');
                $seeMoreBtn.addClass('hidden');
                $seeLessBtn.addClass('hidden');
            }
        });
    }

    // See More / See Less functionality
    $(document).on('click', '.see-more-btn', function() {
        const $wrapper = $(this).closest('.fb-comment-text-wrapper');
        const $text = $wrapper.find('.fb-comment-text');
        const $seeMoreBtn = $wrapper.find('.see-more-btn');
        const $seeLessBtn = $wrapper.find('.see-less-btn');
        
        $text.removeClass('truncated');
        $seeMoreBtn.addClass('hidden');
        $seeLessBtn.removeClass('hidden');
    });

    $(document).on('click', '.see-less-btn', function() {
        const $wrapper = $(this).closest('.fb-comment-text-wrapper');
        const $text = $wrapper.find('.fb-comment-text');
        const $seeMoreBtn = $wrapper.find('.see-more-btn');
        const $seeLessBtn = $wrapper.find('.see-less-btn');
        
        $text.addClass('truncated');
        $seeMoreBtn.removeClass('hidden');
        $seeLessBtn.addClass('hidden');
    });

    // Check truncation on page load and after dynamic content changes
    $(document).ready(function() {
        // Wait a bit for content to render
        setTimeout(function() {
            checkAndTruncateComments();
        }, 100);
        
        // Also check after window resize
        $(window).on('resize', function() {
            setTimeout(checkAndTruncateComments, 100);
        });
    });

    // View / Hide replies for admin comments (similar behaviour to public referendums page)
    $(document).on('click', '.view-replies-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const $wrapper = $btn.closest('.comment-replies-wrapper');
        const $repliesContainer = $wrapper.find('.replies-container').first();
        const $hideBtn = $wrapper.find('.hide-replies-btn').first();

        // Show replies
        $repliesContainer.stop(true, true).slideDown(200);
        $btn.addClass('hidden');
        $hideBtn.removeClass('hidden');
    });

    $(document).on('click', '.hide-replies-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const $wrapper = $btn.closest('.comment-replies-wrapper');
        const $repliesContainer = $wrapper.find('.replies-container').first();
        const $viewBtn = $wrapper.find('.view-replies-btn').first();

        // Hide replies
        $repliesContainer.stop(true, true).slideUp(200);
        $btn.addClass('hidden');
        $viewBtn.removeClass('hidden');
    });

    // Toggle Comments Section
    $(document).ready(function() {
        const commentsContent = $('#commentsContent');
        const toggleBtn = $('#toggleCommentsBtn');
        const toggleIcon = $('#toggleCommentsIcon');
        const toggleText = $('#toggleCommentsText');
        
        // Check localStorage for saved state
        const savedState = localStorage.getItem('referendumCommentsVisible');
        const isVisible = savedState === null || savedState === 'true'; // Default to visible
        
        // Set initial state
        if (!isVisible) {
            commentsContent.hide();
            toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            toggleText.text('View');
        } else {
            commentsContent.show();
            toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            toggleText.text('Hide');
        }
        
        // Toggle on button click
        toggleBtn.on('click', function() {
            if (commentsContent.is(':visible')) {
                commentsContent.slideUp(300);
                toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                toggleText.text('View');
                localStorage.setItem('referendumCommentsVisible', 'false');
            } else {
                commentsContent.slideDown(300);
                toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                toggleText.text('Hide');
                localStorage.setItem('referendumCommentsVisible', 'true');
            }
        });
    });
</script>
@endpush

