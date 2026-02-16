<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of published announcements for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search', '');
        $requestedCategory = $request->input('category');
        $category = in_array($requestedCategory, ['public', 'board_member_activities'], true) ? $requestedCategory : null;
        $isAdminOrConsec = $user->hasRole('admin') || (($user->privilege ?? null) === 'consec');

        // Auto-publish any draft announcements whose scheduled_at is now or in the past
        \App\Models\Announcement::autoPublishScheduled();
        
        // Base query: include effectively published announcements plus
        // scheduled drafts (upcoming) so users can see future-dated items.
        if ($isAdminOrConsec) {
            $query = Announcement::query()
                ->with(['creator.profilePictureMedia', 'bannerImage']);
        } else {
            $query = Announcement::query()
                ->whereHas('allowedUsers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['creator.profilePictureMedia', 'bannerImage']);
        }

        // Visibility rules for everyone on this listing:
        // - status = published (any scheduled_at, including future)
        // - OR status = draft with a scheduled_at date (upcoming announcement)
        $query->where(function ($q) {
            $q->where('status', 'published')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'draft')
                      ->whereNotNull('scheduled_at');
              });
        });

        // Apply category filter if provided
        if ($category !== null) {
            $query->where('category', $category);
        }
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $announcements = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('announcements.index', compact('announcements', 'search', 'category'));
    }

    /**
     * Display the specified announcement
     */
    public function show($id)
    {
        $user = Auth::user();
        $isAdminOrConsec = $user->hasRole('admin') || (($user->privilege ?? null) === 'consec');
        
        $announcement = Announcement::with(['creator', 'bannerImage', 'allowedUsers'])
            ->findOrFail($id);

        // Check if user has access or is admin/consec
        if (!$isAdminOrConsec && !$announcement->hasUserAccess($user->id)) {
            abort(403, 'You do not have permission to view this announcement.');
        }

        // Check if announcement is visible for this user.
        // Non-admin/Non-consec can see:
        // - any "published" announcement (regardless of scheduled_at), OR
        // - drafts that have a scheduled_at date (upcoming announcements)
        if (!$isAdminOrConsec) {
            $isVisible = ($announcement->status === 'published')
                || ($announcement->status === 'draft' && $announcement->scheduled_at !== null);

            if (!$isVisible) {
                abort(404, 'This announcement is not available.');
            }
        }

        return view('announcements.show', compact('announcement'));
    }

    /**
     * Get announcements for landing page (AJAX)
     */
    public function getForLanding(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['announcements' => []]);
        }

        // Ensure any due scheduled announcements are marked as published before fetching
        \App\Models\Announcement::autoPublishScheduled();

        $user = Auth::user();
        $isAdminOrConsec = $user->hasRole('admin') || (($user->privilege ?? null) === 'consec');
        $limit = $request->input('limit', 6);
        $requestedCategory = $request->input('category');
        $category = in_array($requestedCategory, ['public', 'board_member_activities'], true) ? $requestedCategory : null;

        // Base query for landing:
        // - status = published (any scheduled_at, including future)
        // - OR status = draft with a scheduled_at date (upcoming announcement)
        // Admins and CONSEC can see all matching announcements
        if ($isAdminOrConsec) {
            $query = Announcement::query()
                ->with(['creator', 'bannerImage']);
        } else {
            // Regular users can only see announcements they have access to
            $query = Announcement::query()
                ->whereHas('allowedUsers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['creator', 'bannerImage']);
        }

        $query->where(function ($q) {
            $q->where('status', 'published')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'draft')
                      ->whereNotNull('scheduled_at');
              });
        });

        if ($category) {
            $query->where('category', $category);
        }

        $announcements = $query
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Format announcements for response
        $formatted = $announcements->map(function($announcement) {
            $bannerUrl = null;
            if ($announcement->bannerImage) {
                $bannerUrl = asset('storage/' . $announcement->bannerImage->file_path);
            }

            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                // Use description_with_links so any plain URLs become clickable in the modal
                'description' => $announcement->description_with_links,
                'description_short' => \Str::limit(strip_tags($announcement->description), 150),
                'banner_url' => $bannerUrl,
                'author' => $announcement->creator->first_name . ' ' . $announcement->creator->last_name,
                'created_at' => $announcement->created_at->format('M d, Y'),
                'created_at_human' => $announcement->created_at->diffForHumans(),
                'category' => $announcement->category,
                'category_label' => $announcement->category_label,
            ];
        });

        return response()->json(['announcements' => $formatted]);
    }

    /**
     * Get single announcement for modal (AJAX)
     */
    public function getForModal($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Ensure any due scheduled announcements are marked as published before fetching
        \App\Models\Announcement::autoPublishScheduled();

        $user = Auth::user();
        $isAdminOrConsec = $user->hasRole('admin') || (($user->privilege ?? null) === 'consec');
        
        $announcement = Announcement::with(['creator.profilePictureMedia', 'bannerImage'])
            ->findOrFail($id);

        // Check if user has access or is admin/consec
        if (!$isAdminOrConsec && !$announcement->hasUserAccess($user->id)) {
            return response()->json(['error' => 'You do not have permission to view this announcement.'], 403);
        }

        // For non-admin / non-consec users, allow:
        // - any "published" announcement, OR
        // - drafts that have a scheduled_at date (so users can open upcoming announcements from calendar)
        if (!$isAdminOrConsec) {
            $isVisible = ($announcement->status === 'published')
                || ($announcement->status === 'draft' && $announcement->scheduled_at !== null);

            if (!$isVisible) {
                return response()->json(['error' => 'This announcement is not available.'], 404);
            }
        }

        $bannerUrl = null;
        if ($announcement->bannerImage) {
            $bannerUrl = asset('storage/' . $announcement->bannerImage->file_path);
        }

        // Get author profile picture URL
        $authorProfileUrl = null;
        if ($announcement->creator->profilePictureMedia) {
            $authorProfileUrl = asset('storage/' . $announcement->creator->profilePictureMedia->file_path);
        }

        $formatted = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            // Use description_with_links so any plain URLs become clickable in the modal
            'description' => $announcement->description_with_links,
            'banner_url' => $bannerUrl,
            'author' => $announcement->creator->first_name . ' ' . $announcement->creator->last_name,
            'author_profile_url' => $authorProfileUrl,
            'created_at' => $announcement->created_at->format('F d, Y'),
            'created_at_human' => $announcement->created_at->diffForHumans(),
            'category' => $announcement->category,
            'category_label' => $announcement->category_label,
        ];

        return response()->json(['announcement' => $formatted]);
    }
}
