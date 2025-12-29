<?php

namespace App\Http\Controllers;

use App\Models\Referendum;
use App\Models\ReferendumVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferendumVoteController extends Controller
{
    /**
     * Store a vote for a referendum
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
                'message' => 'This referendum has expired. Voting is no longer allowed.'
            ], 403);
        }

        // Check if user has already voted
        if ($referendum->hasUserVoted($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already voted on this referendum.'
            ], 422);
        }

        $validated = $request->validate([
            'vote' => 'required|in:accept,decline',
        ]);

        try {
            ReferendumVote::create([
                'referendum_id' => $referendum->id,
                'user_id' => $userId,
                'vote' => $validated['vote'],
            ]);

            // Get updated vote counts
            $acceptCount = $referendum->acceptVotes()->count();
            $declineCount = $referendum->declineVotes()->count();

            return response()->json([
                'success' => true,
                'message' => 'Vote recorded successfully.',
                'accept_count' => $acceptCount,
                'decline_count' => $declineCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record vote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vote statistics for a referendum
     */
    public function statistics($referendumId)
    {
        $referendum = Referendum::findOrFail($referendumId);

        // Only admin can view statistics
        if (!Auth::user()->hasPermission('view referendum')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view statistics.'
            ], 403);
        }

        $acceptVotes = $referendum->acceptVotes()->with('user')->get();
        $declineVotes = $referendum->declineVotes()->with('user')->get();

        return response()->json([
            'success' => true,
            'accept_count' => $acceptVotes->count(),
            'decline_count' => $declineVotes->count(),
            'total_votes' => $referendum->votes()->count(),
            'accept_votes' => $acceptVotes->map(function ($vote) {
                return [
                    'user_id' => $vote->user_id,
                    'user_name' => $vote->user->first_name . ' ' . $vote->user->last_name,
                    'user_email' => $vote->user->email,
                    'voted_at' => $vote->created_at->toDateTimeString(),
                ];
            }),
            'decline_votes' => $declineVotes->map(function ($vote) {
                return [
                    'user_id' => $vote->user_id,
                    'user_name' => $vote->user->first_name . ' ' . $vote->user->last_name,
                    'user_email' => $vote->user->email,
                    'voted_at' => $vote->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }
}
