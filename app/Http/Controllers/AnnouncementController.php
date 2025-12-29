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
        
        // Base query
        if ($user->hasRole('admin')) {
            $query = Announcement::published()
                ->with(['creator.profilePictureMedia', 'bannerImage']);
        } else {
            $query = Announcement::published()
                ->whereHas('allowedUsers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['creator.profilePictureMedia', 'bannerImage']);
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

        return view('announcements.index', compact('announcements', 'search'));
    }

    /**
     * Display the specified announcement
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $announcement = Announcement::with(['creator', 'bannerImage', 'allowedUsers'])
            ->findOrFail($id);

        // Check if user has access or is admin
        if (!$user->hasRole('admin') && !$announcement->hasUserAccess($user->id)) {
            abort(403, 'You do not have permission to view this announcement.');
        }

        // Check if announcement is published
        if (!$user->hasRole('admin') && !$announcement->isPublished()) {
            abort(404, 'This announcement is not available.');
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

        $user = Auth::user();
        $limit = $request->input('limit', 6);

        // Admins can see all published announcements
        if ($user->hasRole('admin')) {
            $announcements = Announcement::published()
                ->with(['creator', 'bannerImage'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } else {
            // Regular users can only see announcements they have access to
            $announcements = Announcement::published()
                ->whereHas('allowedUsers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['creator', 'bannerImage'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        // Format announcements for response
        $formatted = $announcements->map(function($announcement) {
            $bannerUrl = null;
            if ($announcement->bannerImage) {
                $bannerUrl = asset('storage/' . $announcement->bannerImage->file_path);
            }

            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'description' => $announcement->description,
                'description_short' => \Str::limit(strip_tags($announcement->description), 150),
                'banner_url' => $bannerUrl,
                'author' => $announcement->creator->first_name . ' ' . $announcement->creator->last_name,
                'created_at' => $announcement->created_at->format('M d, Y'),
                'created_at_human' => $announcement->created_at->diffForHumans(),
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

        $user = Auth::user();
        
        $announcement = Announcement::with(['creator.profilePictureMedia', 'bannerImage'])
            ->findOrFail($id);

        // Check if user has access or is admin
        if (!$user->hasRole('admin') && !$announcement->hasUserAccess($user->id)) {
            return response()->json(['error' => 'You do not have permission to view this announcement.'], 403);
        }

        // Check if announcement is published
        if (!$user->hasRole('admin') && !$announcement->isPublished()) {
            return response()->json(['error' => 'This announcement is not available.'], 404);
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
            'description' => $announcement->description,
            'banner_url' => $bannerUrl,
            'author' => $announcement->creator->first_name . ' ' . $announcement->creator->last_name,
            'author_profile_url' => $authorProfileUrl,
            'created_at' => $announcement->created_at->format('F d, Y'),
            'created_at_human' => $announcement->created_at->diffForHumans(),
        ];

        return response()->json(['announcement' => $formatted]);
    }
}
