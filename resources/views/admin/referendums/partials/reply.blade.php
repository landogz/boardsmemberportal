@php
    // Get reply user profile picture
    $replyProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->first_name . ' ' . $reply->user->last_name) . '&size=64&background=055498&color=fff';
    if ($reply->user->profile_picture) {
        $replyMedia = \App\Models\MediaLibrary::find($reply->user->profile_picture);
        if ($replyMedia) {
            $replyProfilePic = asset('storage/' . $replyMedia->file_path);
        }
    }
    
    // Check if reply user is online
    $replyIsOnline = $reply->user->is_online ?? false;
@endphp
<div class="comment-reply">
    <div class="flex items-start space-x-3">
        <!-- Reply Profile Picture with Online Indicator -->
        <div class="flex-shrink-0 relative">
            <img src="{{ $replyProfilePic }}" 
                 alt="{{ $reply->user->first_name }} {{ $reply->user->last_name }}" 
                 class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
            @if($replyIsOnline)
                <span class="online-indicator" style="width: 10px; height: 10px; border-width: 1.5px;"></span>
            @endif
        </div>
        
        <!-- Reply Content -->
        <div class="flex-1 min-w-0">
            <div class="bg-gray-50 rounded-2xl px-3 py-2 inline-block max-w-full">
                <div class="flex items-baseline space-x-2 mb-1">
                    <span class="font-semibold text-xs text-gray-900 hover:underline cursor-pointer">
                        {{ $reply->user->first_name }} {{ $reply->user->last_name }}
                    </span>
                    @if($replyIsOnline)
                        <span class="text-xs text-green-600 font-medium">● Online</span>
                    @endif
                </div>
                <div class="fb-comment-text-wrapper">
                    <div class="fb-comment-text text-xs text-gray-800" data-full-text="{{ htmlspecialchars($reply->content, ENT_QUOTES, 'UTF-8') }}">{{ $reply->content }}</div>
                    <button type="button" class="see-more-btn hidden text-xs">See more</button>
                    <button type="button" class="see-less-btn hidden text-xs">See less</button>
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-1.5 ml-1">
                <span class="text-xs text-gray-500 hover:underline cursor-pointer">
                    {{ $reply->created_at->diffForHumans() }}
                </span>
            </div>
            
            <!-- Nested Replies -->
            @if($reply->relationLoaded('replies') && $reply->replies->count() > 0)
                <div class="mt-2 comment-replies-wrapper">
                    <!-- View/Hide replies toggle -->
                    <button 
                        type="button" 
                        class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer flex items-center gap-1 mb-1"
                        data-comment-id="{{ $reply->id }}"
                    >
                        <i class="fas fa-chevron-down text-[10px]"></i>
                        <span>
                            View all {{ $reply->replies->count() }} {{ $reply->replies->count() === 1 ? 'reply' : 'replies' }}
                        </span>
                    </button>
                    <button 
                        type="button" 
                        class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden flex items-center gap-1 mb-1"
                        data-comment-id="{{ $reply->id }}"
                    >
                        <i class="fas fa-chevron-up text-[10px]"></i>
                        <span>Hide replies</span>
                    </button>
                    
                    <!-- Replies Container (hidden by default) -->
                    <div class="replies-container hidden mt-1">
                    @foreach($reply->replies as $nestedReply)
                        @include('admin.referendums.partials.reply', ['reply' => $nestedReply])
                    @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

