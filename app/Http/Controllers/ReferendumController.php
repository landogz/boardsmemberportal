<?php

namespace App\Http\Controllers;

use App\Models\Referendum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferendumController extends Controller
{
    /**
     * Display a listing of referendums accessible to the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Only show referendums where the user is in the allowedUsers list
        // This applies to all users, regardless of permissions
        $referendums = Referendum::with(['creator', 'votes', 'allowedUsers'])
            ->whereHas('allowedUsers', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);
        
        return view('referendums.index', compact('referendums'));
    }

    /**
     * Display the specified referendum
     */
    public function show($id)
    {
        $referendum = Referendum::with(['creator', 'votes.user', 'allowedUsers'])->findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access
        if (!$referendum->allowedUsers()->where('users.id', $userId)->exists() && !Auth::user()->hasPermission('view referendum')) {
            abort(403, 'You do not have access to this referendum.');
        }
        
        $userVote = $referendum->getUserVote($userId);
        
        // Load all comments with users (we'll organize them hierarchically)
        // Don't eager load replies to avoid duplicates - we'll organize them manually
        $allComments = $referendum->allComments()->with('user')->orderBy('created_at', 'asc')->get();
        
        // Organize comments hierarchically
        $commentsById = $allComments->keyBy('id');
        $mainComments = collect();
        
        foreach ($allComments as $comment) {
            if ($comment->parent_id === null) {
                // This is a main comment
                $mainComments->push($comment);
            } else {
                // This is a reply - add it to its parent's replies
                if (isset($commentsById[$comment->parent_id])) {
                    $parent = $commentsById[$comment->parent_id];
                    if (!$parent->relationLoaded('replies')) {
                        $parent->setRelation('replies', collect());
                    }
                    // Check if reply is already in the collection to avoid duplicates
                    if (!$parent->replies->contains('id', $comment->id)) {
                        $parent->replies->push($comment);
                    }
                }
            }
        }
        
        // Recursively organize nested replies
        $organizeReplies = function($comment) use (&$organizeReplies, $allComments) {
            if ($comment->relationLoaded('replies') && $comment->replies->count() > 0) {
                // Remove duplicates and sort replies by created_at ascending (oldest first)
                $comment->replies = $comment->replies->unique('id')->sortBy('created_at')->values();
                
                foreach ($comment->replies as $reply) {
                    // Find all replies to this reply
                    $nestedReplies = $allComments->filter(function($c) use ($reply) {
                        return $c->parent_id === $reply->id;
                    });
                    
                    if ($nestedReplies->count() > 0) {
                        if (!$reply->relationLoaded('replies')) {
                            $reply->setRelation('replies', collect());
                        }
                        // Sort nested replies by created_at ascending
                        $nestedReplies = $nestedReplies->sortBy('created_at');
                        foreach ($nestedReplies as $nestedReply) {
                            // Check if nested reply is already in the collection to avoid duplicates
                            if (!$reply->replies->contains('id', $nestedReply->id)) {
                                $reply->replies->push($nestedReply);
                            }
                        }
                        // Remove duplicates and sort the replies collection
                        $reply->replies = $reply->replies->unique('id')->sortBy('created_at')->values();
                        // Recursively organize nested replies
                        $organizeReplies($reply);
                    }
                }
            }
        };
        
        foreach ($mainComments as $comment) {
            $organizeReplies($comment);
        }
        
        // Sort main comments by created_at asc (oldest first, newest last)
        $mainComments = $mainComments->sortBy('created_at')->values();
        
        // For initial display, show the last 5 (most recent) but they'll be displayed in order (oldest of those 5 first)
        // If user wants to see oldest first, we can change this to take(5) instead
        $totalMainComments = $allComments->where('parent_id', null)->count();
        $mainComments = $mainComments->take(5)->values();
        $totalMainComments = $allComments->where('parent_id', null)->count();
        
        // Get vote statistics
        $acceptCount = $referendum->acceptVotes()->count();
        $declineCount = $referendum->declineVotes()->count();
        $totalVotes = $referendum->votes()->count();
        
        return view('referendums.show', compact('referendum', 'userVote', 'mainComments', 'totalMainComments', 'acceptCount', 'declineCount', 'totalVotes'));
    }

    /**
     * Get comments for a referendum (for pagination)
     */
    public function getComments($id, Request $request)
    {
        $referendum = Referendum::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access
        if (!$referendum->allowedUsers()->where('users.id', $userId)->exists() && !Auth::user()->hasPermission('view referendum')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this referendum.'
            ], 403);
        }
        
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);
        
        // Load all comments with users (we'll organize them hierarchically)
        $allComments = $referendum->allComments()->with('user')->orderBy('created_at', 'asc')->get();
        
        // Organize comments hierarchically
        $commentsById = $allComments->keyBy('id');
        $mainComments = collect();
        
        foreach ($allComments as $comment) {
            if ($comment->parent_id === null) {
                // This is a main comment
                $mainComments->push($comment);
            } else {
                // This is a reply - add it to its parent's replies
                if (isset($commentsById[$comment->parent_id])) {
                    $parent = $commentsById[$comment->parent_id];
                    if (!$parent->relationLoaded('replies')) {
                        $parent->setRelation('replies', collect());
                    }
                    // Check if reply is already in the collection to avoid duplicates
                    if (!$parent->replies->contains('id', $comment->id)) {
                        $parent->replies->push($comment);
                    }
                }
            }
        }
        
        // Recursively organize nested replies
        $organizeReplies = function($comment) use (&$organizeReplies, $allComments) {
            if ($comment->relationLoaded('replies') && $comment->replies->count() > 0) {
                // Remove duplicates and sort replies by created_at ascending (oldest first)
                $comment->replies = $comment->replies->unique('id')->sortBy('created_at')->values();
                
                foreach ($comment->replies as $reply) {
                    // Find all replies to this reply
                    $nestedReplies = $allComments->filter(function($c) use ($reply) {
                        return $c->parent_id === $reply->id;
                    });
                    
                    if ($nestedReplies->count() > 0) {
                        if (!$reply->relationLoaded('replies')) {
                            $reply->setRelation('replies', collect());
                        }
                        // Sort nested replies by created_at ascending
                        $nestedReplies = $nestedReplies->sortBy('created_at');
                        foreach ($nestedReplies as $nestedReply) {
                            // Check if nested reply is already in the collection to avoid duplicates
                            if (!$reply->replies->contains('id', $nestedReply->id)) {
                                $reply->replies->push($nestedReply);
                            }
                        }
                        // Remove duplicates and sort the replies collection
                        $reply->replies = $reply->replies->unique('id')->sortBy('created_at')->values();
                        // Recursively organize nested replies
                        $organizeReplies($reply);
                    }
                }
            }
        };
        
        foreach ($mainComments as $comment) {
            $organizeReplies($comment);
        }
        
        // Sort main comments by created_at asc (oldest first, newest last)
        $mainComments = $mainComments->sortBy('created_at')->values();
        
        // Skip and take for pagination
        $comments = $mainComments->skip($offset)->take($limit)->values();
        $totalMainComments = $allComments->where('parent_id', null)->count();
        
        // Format comments for response (recursively format replies)
        $formatComment = function($comment) use (&$formatComment) {
            $commenterProfileMedia = $comment->user->profile_picture ? \App\Models\MediaLibrary::find($comment->user->profile_picture) : null;
            $commenterProfileUrl = $commenterProfileMedia ? asset('storage/' . $commenterProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->first_name . ' ' . $comment->user->last_name) . '&size=150&background=1877f2&color=fff';
            
            $formatted = [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->first_name . ' ' . $comment->user->last_name,
                    'profile_picture' => $commenterProfileUrl,
                    'is_online' => $comment->user->is_online ?? false,
                ],
                'created_at' => $comment->created_at->diffInSeconds(now()) < 20 ? 'just now' : $comment->created_at->diffForHumans(),
                'replies' => [],
            ];
            
            // Recursively format nested replies
            if ($comment->relationLoaded('replies') && $comment->replies->count() > 0) {
                $formatted['replies'] = $comment->replies->map(function($reply) use (&$formatComment) {
                    return $formatComment($reply);
                })->toArray();
            }
            
            return $formatted;
        };
        
        $formattedComments = $comments->map(function($comment) use ($formatComment) {
            return $formatComment($comment);
        });
        
        return response()->json([
            'success' => true,
            'comments' => $formattedComments,
            'total' => $totalMainComments,
            'loaded' => $offset + $comments->count(),
        ]);
    }

    /**
     * Get new comments for a referendum (for real-time updates)
     */
    public function getNewComments($id, Request $request)
    {
        $referendum = Referendum::findOrFail($id);
        $userId = Auth::id();
        
        // Check if user has access
        if (!$referendum->allowedUsers()->where('users.id', $userId)->exists() && !Auth::user()->hasPermission('view referendum')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this referendum.'
            ], 403);
        }
        
        $lastCommentId = $request->input('last_comment_id', 0);
        $lastTimestamp = $request->input('last_timestamp');
        
        // Get all comments created after the last known comment ID or timestamp
        $query = $referendum->allComments()->with('user')->orderBy('created_at', 'asc');
        
        if ($lastCommentId > 0) {
            $query->where('id', '>', $lastCommentId);
        } elseif ($lastTimestamp) {
            $query->where('created_at', '>', $lastTimestamp);
        }
        
        $newComments = $query->get();
        
        if ($newComments->isEmpty()) {
            return response()->json([
                'success' => true,
                'comments' => [],
                'main_comments' => [],
                'replies' => []
            ]);
        }
        
        // Separate main comments and replies
        $mainComments = $newComments->where('parent_id', null);
        $replies = $newComments->where('parent_id', '!=', null);
        
        // Format comments for response
        $formatComment = function($comment) {
            $commenterProfileMedia = $comment->user->profile_picture ? \App\Models\MediaLibrary::find($comment->user->profile_picture) : null;
            $commenterProfileUrl = $commenterProfileMedia ? asset('storage/' . $commenterProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->first_name . ' ' . $comment->user->last_name) . '&size=150&background=1877f2&color=fff';
            
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->first_name . ' ' . $comment->user->last_name,
                    'email' => $comment->user->email,
                    'profile_picture' => $commenterProfileUrl,
                    'is_online' => $comment->user->is_online ?? false,
                ],
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at->toDateTimeString(),
                'created_at_human' => $comment->created_at->diffInSeconds(now()) < 20 ? 'Just Now' : $comment->created_at->diffForHumans(),
                'replies' => [],
            ];
        };
        
        $formattedMainComments = $mainComments->map($formatComment)->values();
        $formattedReplies = $replies->map($formatComment)->values();
        
        // Get the latest comment ID and timestamp
        $latestComment = $newComments->sortByDesc('id')->first();
        
        return response()->json([
            'success' => true,
            'main_comments' => $formattedMainComments,
            'replies' => $formattedReplies,
            'last_comment_id' => $latestComment->id,
            'last_timestamp' => $latestComment->created_at->toDateTimeString(),
        ]);
    }
}

