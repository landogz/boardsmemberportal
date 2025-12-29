@php
    $replyerProfileMedia = $reply->user->profile_picture ? \App\Models\MediaLibrary::find($reply->user->profile_picture) : null;
    $replyerProfileUrl = $replyerProfileMedia ? asset('storage/' . $replyerProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->first_name . ' ' . $reply->user->last_name) . '&size=150&background=1877f2&color=fff';
    $isReplyerOnline = $reply->user->is_online ?? false;
@endphp
<div class="fb-comment-reply">
    <div class="fb-comment" id="comment-{{ $reply->id }}" data-comment-id="{{ $reply->id }}">
        <div class="profile-picture-container">
            <img src="{{ $replyerProfileUrl }}" alt="{{ $reply->user->first_name }} {{ $reply->user->last_name }}" class="fb-comment-avatar">
            <div class="{{ $isReplyerOnline ? 'online-indicator' : 'offline-indicator' }}"></div>
        </div>
        <div class="flex-1">
            <div class="fb-comment-content">
                <div class="fb-comment-author">{{ $reply->user->first_name }} {{ $reply->user->last_name }}</div>
                <div class="fb-comment-text-wrapper">
                    <div class="fb-comment-text" data-full-text="{{ htmlspecialchars($reply->content, ENT_QUOTES, 'UTF-8') }}">{{ $reply->content }}</div>
                    <button type="button" class="see-more-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See more</button>
                    <button type="button" class="see-less-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See less</button>
                </div>
            </div>
            <div class="fb-comment-actions">
                @if(!$referendum->isExpired())
                    <span class="fb-comment-action reply-btn" data-comment-id="{{ $reply->id }}">Reply</span>
                @endif
                @if(Auth::id() === $reply->user_id && !$referendum->isExpired())
                    <span class="fb-comment-action edit-comment-btn text-[#1877f2]" data-comment-id="{{ $reply->id }}">Edit</span>
                @endif
                @if(Auth::id() === $reply->user_id || Auth::user()->hasPermission('delete referendum'))
                    <span class="fb-comment-action delete-comment-btn text-red-500" data-comment-id="{{ $reply->id }}">Delete</span>
                @endif
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffInSeconds(now()) < 20 ? 'just now' : $reply->created_at->diffForHumans() }}</span>
            </div>
            
            <!-- Edit Reply Input (hidden by default) -->
            @if(Auth::id() === $reply->user_id && !$referendum->isExpired())
                <div class="edit-comment-container hidden mt-2">
                    <div class="flex items-start space-x-2">
                        @php
                            $currentUserProfileMedia = Auth::user()->profile_picture ? \App\Models\MediaLibrary::find(Auth::user()->profile_picture) : null;
                            $currentUserProfileUrl = $currentUserProfileMedia ? asset('storage/' . $currentUserProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&size=150&background=1877f2&color=fff';
                        @endphp
                        <img src="{{ $currentUserProfileUrl }}" alt="Your profile" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">
                        <div class="flex-1">
                            <textarea 
                                class="edit-comment-textarea w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white resize-none focus:outline-none focus:ring-2 focus:ring-[#1877f2] focus:border-[#1877f2] transition-all"
                                rows="2"
                                data-comment-id="{{ $reply->id }}"
                            >{{ $reply->content }}</textarea>
                            <div class="flex items-center justify-end gap-2 mt-2">
                                <button type="button" class="cancel-edit-btn text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-semibold">Cancel</button>
                                <button type="button" class="save-edit-btn text-xs bg-[#1877f2] text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-[#166fe5] transition-colors" data-comment-id="{{ $reply->id }}">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Nested Replies -->
            @if($reply->relationLoaded('replies') && $reply->replies->count() > 0)
                <div class="mt-2">
                    <!-- View Replies Button -->
                    <div class="mb-2">
                        <button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer" data-comment-id="{{ $reply->id }}">
                            <i class="fas fa-chevron-down mr-1"></i>
                            View all {{ $reply->replies->count() }} {{ $reply->replies->count() === 1 ? 'reply' : 'replies' }}
                        </button>
                        <button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden" data-comment-id="{{ $reply->id }}">
                            <i class="fas fa-chevron-up mr-1"></i>
                            Hide replies
                        </button>
                    </div>
                    
                    <!-- Replies Container (hidden by default) -->
                    <div class="replies-container hidden mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                        @foreach($reply->replies as $nestedReply)
                            @include('referendums.partials.reply', ['reply' => $nestedReply, 'referendum' => $referendum])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

