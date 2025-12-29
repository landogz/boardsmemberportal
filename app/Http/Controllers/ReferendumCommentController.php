<?php

namespace App\Http\Controllers;

use App\Models\Referendum;
use App\Models\ReferendumComment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferendumCommentController extends Controller
{
    /**
     * Store a comment on a referendum
     */
    public function store(Request $request, $referendumId)
    {
        $referendum = Referendum::findOrFail($referendumId);
        $userId = Auth::id();

        // Check if user has access
        if (!$referendum->allowedUsers()->where('users.id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this referendum.'
            ], 403);
        }

        // Check if referendum is expired
        if ($referendum->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'This referendum has expired. Commenting is no longer allowed.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:referendum_comments,id',
        ]);

        try {
            // Get parent_id from validated data (may be null)
            $parentId = isset($validated['parent_id']) && $validated['parent_id'] ? $validated['parent_id'] : null;
            
            // If parent_id is provided, verify it belongs to this referendum
            if ($parentId) {
                $parentComment = ReferendumComment::find($parentId);
                if (!$parentComment || $parentComment->referendum_id !== $referendum->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent comment.'
                    ], 400);
                }
            }
            
            $comment = ReferendumComment::create([
                'referendum_id' => $referendum->id,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'content' => $validated['content'],
            ]);

            // If this is a reply, notify the parent comment author
            if ($comment->parent_id) {
                $parentComment = ReferendumComment::with('user')->find($comment->parent_id);
                if ($parentComment && $parentComment->user_id !== $userId) {
                    Notification::create([
                        'user_id' => $parentComment->user_id,
                        'type' => 'referendum_comment_reply',
                        'title' => 'New Reply to Your Comment',
                        'message' => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' replied to your comment on "' . $referendum->title . '"',
                        'url' => route('referendums.show', $referendum->id) . '#comment-' . $comment->id,
                        'data' => [
                            'referendum_id' => $referendum->id,
                            'comment_id' => $comment->id,
                            'parent_comment_id' => $parentComment->id,
                            'replier_id' => $userId,
                            'replier_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        ],
                    ]);
                }
            }

            // Load relationships for response
            $comment->load(['user', 'parent.user']);

            // Get profile picture URL
            $userProfileMedia = $comment->user->profile_picture ? \App\Models\MediaLibrary::find($comment->user->profile_picture) : null;
            $userProfileUrl = $userProfileMedia ? asset('storage/' . $userProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->first_name . ' ' . $comment->user->last_name) . '&size=150&background=1877f2&color=fff';

            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->first_name . ' ' . $comment->user->last_name,
                        'email' => $comment->user->email,
                        'profile_picture' => $userProfileUrl,
                        'is_online' => $comment->user->is_online ?? false,
                    ],
                    'parent_id' => $comment->parent_id,
                    'parent_user' => $comment->parent ? [
                        'id' => $comment->parent->user->id,
                        'name' => $comment->parent->user->first_name . ' ' . $comment->parent->user->last_name,
                    ] : null,
                    'created_at' => $comment->created_at->toDateTimeString(),
                    'created_at_human' => $comment->created_at->diffInSeconds(now()) < 20 ? 'Just Now' : $comment->created_at->diffForHumans(),
                    'replies' => [], // New comments have no replies
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to post comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a comment
     */
    public function update(Request $request, $referendumId, $commentId)
    {
        $referendum = Referendum::findOrFail($referendumId);
        $comment = ReferendumComment::where('referendum_id', $referendum->id)
            ->findOrFail($commentId);

        // Only the comment author can update
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own comments.'
            ], 403);
        }

        // Check if referendum is expired
        if ($referendum->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'This referendum has expired. Comments cannot be edited.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        try {
            $comment->update([
                'content' => $validated['content'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroy($referendumId, $commentId)
    {
        $referendum = Referendum::findOrFail($referendumId);
        $comment = ReferendumComment::where('referendum_id', $referendum->id)
            ->findOrFail($commentId);

        // Only the comment author or admin can delete
        $isAdmin = Auth::user()->hasPermission('delete referendum');
        if ($comment->user_id !== Auth::id() && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this comment.'
            ], 403);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
