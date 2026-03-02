@if($comments->count() > 0)
    <div class="space-y-4">
        @foreach($comments as $comment)
            @php
                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->first_name . ' ' . $comment->user->last_name) . '&size=64&background=055498&color=fff';
                if ($comment->user->profile_picture) {
                    $media = \App\Models\MediaLibrary::find($comment->user->profile_picture);
                    if ($media) {
                        $profilePic = asset('storage/' . $media->file_path);
                    }
                }
                $isOnline = $comment->user->is_online ?? false;
            @endphp
            <div class="comment-item border-b border-gray-200 pb-4 last:border-b-0 px-3 py-2 -mx-3 rounded-lg">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 relative">
                        <img src="{{ $profilePic }}"
                             alt="{{ $comment->user->first_name }} {{ $comment->user->last_name }}"
                             class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                        @if($isOnline)
                            <span class="online-indicator"></span>
                        @endif
                    </div>
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
                        @if($comment->replies->count() > 0)
                            <div class="mt-3 comment-replies-wrapper">
                                <button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer flex items-center gap-1 mb-2" data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                    <span>View all {{ $comment->replies->count() }} {{ $comment->replies->count() === 1 ? 'reply' : 'replies' }}</span>
                                </button>
                                <button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer flex items-center gap-1 hidden mb-2" data-comment-id="{{ $comment->id }}">
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
