<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referendum;
use App\Models\User;
use App\Models\Notification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReferendumController extends Controller
{
    /**
     * Display a listing of referendums
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view referendum.');
        }

        $referendums = Referendum::with(['creator', 'votes', 'allComments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($referendum) {
                $referendum->status = $referendum->isExpired() ? 'expired' : 'active';
                $referendum->accept_count = $referendum->acceptVotes()->count();
                $referendum->decline_count = $referendum->declineVotes()->count();
                $referendum->total_votes = $referendum->votes()->count();
                $referendum->total_comments = $referendum->allComments()->count();
                return $referendum;
            });

        return view('admin.referendums.index', compact('referendums'));
    }

    /**
     * Show the form for creating a new referendum
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create referendum.');
        }

        $users = User::where('privilege', '!=', 'admin')
            ->with('governmentAgency')
            ->leftJoin('government_agencies', 'users.government_agency_id', '=', 'government_agencies.id')
            ->select('users.*')
            ->orderBy('privilege')
            ->orderBy('government_agencies.name')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.referendums.create', compact('users'));
    }

    /**
     * Store a newly created referendum
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create referendum.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'exists:media_library,id',
            'expires_at' => 'required|date|after:now',
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $referendum = Referendum::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'attachments' => $validated['attachments'] ?? [],
                'expires_at' => $validated['expires_at'],
                'created_by' => Auth::id(),
                'is_active' => true,
            ]);

            // Attach allowed users
            $referendum->allowedUsers()->attach($validated['allowed_users']);

            // Send notifications and emails to all allowed users
            foreach ($validated['allowed_users'] as $userId) {
                $user = User::find($userId);
                if ($user) {
                    // Create in-app notification
                    Notification::create([
                        'user_id' => $userId,
                        'type' => 'announcement',
                        'title' => 'New Referendum Available',
                        'message' => 'A new referendum "' . $referendum->title . '" has been created and is now available for your review and vote.',
                        'url' => route('referendums.show', $referendum->id),
                        'data' => [
                            'referendum_id' => $referendum->id,
                            'referendum_title' => $referendum->title,
                        ],
                    ]);
                    
                    // Send email
                    try {
                        Mail::to($user->email)->send(new \App\Mail\ReferendumEmail($user, $referendum));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send referendum email to user ' . $userId . ': ' . $e->getMessage());
                    }
                }
            }

            AuditLogger::log(
                'referendum.created',
                'Created referendum: ' . $referendum->title,
                $referendum,
                ['referendum_id' => $referendum->id]
            );

            DB::commit();

            return redirect()->route('admin.referendums.index')
                ->with('success', 'Referendum created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create referendum: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified referendum with analytics
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view referendum.');
        }

        $referendum = Referendum::with([
            'creator',
            'votes.user',
            'allowedUsers'
        ])->findOrFail($id);

        // Get vote statistics
        $acceptVotes = $referendum->acceptVotes()->with('user')->get();
        $declineVotes = $referendum->declineVotes()->with('user')->get();
        $totalVotes = $referendum->votes()->count();
        $totalComments = $referendum->allComments()->count();

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
        $comments = $mainComments->sortBy('created_at')->values();

        // Prepare voter data for JavaScript
        $acceptVotersData = $acceptVotes->map(function($vote) {
            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($vote->user->first_name . ' ' . $vote->user->last_name) . '&size=64&background=10B981&color=fff';
            if ($vote->user->profile_picture) {
                $media = \App\Models\MediaLibrary::find($vote->user->profile_picture);
                if ($media) {
                    $profilePic = asset('storage/' . $media->file_path);
                }
            }
            return [
                'id' => $vote->user->id,
                'name' => $vote->user->first_name . ' ' . $vote->user->last_name,
                'email' => $vote->user->email,
                'voted_at' => $vote->created_at->format('M d, Y h:i A'),
                'profile_picture' => $profilePic
            ];
        })->values()->all();

        $declineVotersData = $declineVotes->map(function($vote) {
            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($vote->user->first_name . ' ' . $vote->user->last_name) . '&size=64&background=EF4444&color=fff';
            if ($vote->user->profile_picture) {
                $media = \App\Models\MediaLibrary::find($vote->user->profile_picture);
                if ($media) {
                    $profilePic = asset('storage/' . $media->file_path);
                }
            }
            return [
                'id' => $vote->user->id,
                'name' => $vote->user->first_name . ' ' . $vote->user->last_name,
                'email' => $vote->user->email,
                'voted_at' => $vote->created_at->format('M d, Y h:i A'),
                'profile_picture' => $profilePic
            ];
        })->values()->all();

        return view('admin.referendums.show', compact(
            'referendum',
            'acceptVotes',
            'declineVotes',
            'totalVotes',
            'totalComments',
            'comments',
            'acceptVotersData',
            'declineVotersData'
        ));
    }

    /**
     * Show the form for editing the specified referendum
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to edit referendum.');
        }

        $referendum = Referendum::with('allowedUsers')->findOrFail($id);
        $users = User::where('privilege', '!=', 'admin')
            ->with('governmentAgency')
            ->leftJoin('government_agencies', 'users.government_agency_id', '=', 'government_agencies.id')
            ->select('users.*')
            ->orderBy('privilege')
            ->orderBy('government_agencies.name')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $selectedUsers = $referendum->allowedUsers->pluck('id')->toArray();

        return view('admin.referendums.edit', compact('referendum', 'users', 'selectedUsers'));
    }

    /**
     * Update the specified referendum
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit referendum')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to edit referendum.');
        }

        $referendum = Referendum::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'exists:media_library,id',
            'expires_at' => 'required|date',
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $referendum->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'attachments' => $validated['attachments'] ?? [],
                'expires_at' => $validated['expires_at'],
            ]);

            // Get previous allowed users
            $previousAllowedUsers = $referendum->allowedUsers->pluck('id')->toArray();

            // Sync allowed users
            $referendum->allowedUsers()->sync($validated['allowed_users']);
            
            // Get newly added users (users who weren't previously allowed but are now)
            $newlyAddedUsers = array_diff($validated['allowed_users'], $previousAllowedUsers);
            
            // Send notifications to newly added users
            foreach ($newlyAddedUsers as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'announcement',
                    'title' => 'Referendum Updated - You Now Have Access',
                    'message' => 'The referendum "' . $referendum->title . '" has been updated and you now have access to view, vote, and comment on it.',
                    'url' => route('referendums.show', $referendum->id),
                    'data' => [
                        'referendum_id' => $referendum->id,
                        'referendum_title' => $referendum->title,
                    ],
                ]);
            }

            AuditLogger::log(
                'referendum.updated',
                'Updated referendum: ' . $referendum->title,
                $referendum,
                ['referendum_id' => $referendum->id]
            );

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Referendum updated successfully.'
                ]);
            }

            return redirect()->route('admin.referendums.index')
                ->with('success', 'Referendum updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update referendum: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update referendum: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified referendum
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete referendum')) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete referendum.'
                ], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete referendum.');
        }

        $referendum = Referendum::findOrFail($id);

        DB::beginTransaction();
        try {
            $title = $referendum->title;
            $referendum->delete();

            AuditLogger::log(
                'referendum.deleted',
                'Deleted referendum: ' . $title,
                null,
                ['referendum_id' => $id]
            );

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Referendum deleted successfully.'
                ]);
            }

            return redirect()->route('admin.referendums.index')
                ->with('success', 'Referendum deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete referendum: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete referendum: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete referendums
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->hasPermission('delete referendum')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete referendums.'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:referendums,id',
        ]);

        $ids = $request->input('ids');
        $deletedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                try {
                    $referendum = Referendum::findOrFail($id);
                    $title = $referendum->title;
                    $referendum->delete();

                    AuditLogger::log(
                        'referendum.deleted',
                        'Deleted referendum: ' . $title,
                        null,
                        ['referendum_id' => $id]
                    );

                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete referendum ID {$id}: " . $e->getMessage();
                }
            }

            DB::commit();

            if ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} referendum(s) deleted successfully." . (!empty($errors) ? ' Some errors occurred: ' . implode(', ', $errors) : '')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No referendums were deleted. Errors: ' . implode(', ', $errors)
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete referendums: ' . $e->getMessage()
            ], 500);
        }
    }
}
