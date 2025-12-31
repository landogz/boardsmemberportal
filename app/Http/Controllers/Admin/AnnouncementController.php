<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Notification;
use App\Models\MediaLibrary;
use App\Mail\AnnouncementEmail;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view announcements.');
        }

        $announcements = Announcement::with(['creator', 'bannerImage', 'allowedUsers'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create announcements.');
        }

        $users = User::where('privilege', '!=', 'admin')
            ->orderBy('privilege')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.announcements.create', compact('users'));
    }

    /**
     * Store a newly created announcement
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
            'status' => 'nullable|in:draft,published',
            'scheduled_at' => 'nullable|date',
        ], [
            'title.required' => 'The title field is required.',
            'description.required' => 'The description field is required.',
            'allowed_users.required' => 'Please select at least one allowed user.',
            'allowed_users.min' => 'Please select at least one allowed user.',
        ]);

        DB::beginTransaction();
        try {
            $bannerImageId = null;
            
            // Handle banner image upload
            if ($request->hasFile('banner_image')) {
                $file = $request->file('banner_image');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'announcements/' . $fileName;
                
                // Store file
                Storage::disk('public')->put($filePath, file_get_contents($file));
                
                // Create media library entry
                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);
                
                $bannerImageId = $media->id;
            }

            // Determine status: if scheduled_at is in the future, set to draft
            $scheduledAt = $validated['scheduled_at'] ? new \DateTime($validated['scheduled_at']) : null;
            $requestedStatus = $validated['status'] ?? 'published';
            
            // If scheduled_at is in the future, force status to draft (will be auto-published by scheduler)
            if ($scheduledAt && $scheduledAt > now()) {
                $finalStatus = 'draft';
            } else {
                $finalStatus = $requestedStatus;
            }

            $announcement = Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['description'], // Map description to content
                'banner_image_id' => $bannerImageId,
                'created_by' => Auth::id(),
                'status' => $finalStatus,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
            ]);

            // Attach allowed users
            $announcement->allowedUsers()->attach($validated['allowed_users']);

            // Send notifications and emails to all allowed users (only if published)
            if ($announcement->status === 'published') {
                // Check if scheduled for future
                $shouldNotifyNow = $announcement->scheduled_at === null || $announcement->scheduled_at <= now();
                
                if ($shouldNotifyNow) {
                    // Reload announcement with allowed users
                    $announcement->load('allowedUsers');
                    
                    foreach ($announcement->allowedUsers as $user) {
                        // Check if notification already exists to avoid duplicates
                        $existingNotification = Notification::where('user_id', $user->id)
                            ->where('type', 'announcement')
                            ->where('data->announcement_id', $announcement->id)
                            ->first();
                        
                        if (!$existingNotification) {
                            Notification::create([
                                'user_id' => $user->id,
                                'type' => 'announcement',
                                'title' => 'New Announcement',
                                'message' => 'A new announcement "' . $announcement->title . '" has been published.',
                                'url' => route('announcements.show', $announcement->id),
                                'data' => [
                                    'announcement_id' => $announcement->id,
                                    'announcement_title' => $announcement->title,
                                ],
                            ]);
                        }
                        
                        // Send email to user
                        try {
                            Mail::to($user->email)->send(new AnnouncementEmail($announcement, $user));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send announcement email to user ' . $user->id . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            AuditLogger::log(
                'announcement.created',
                'Created announcement: ' . $announcement->title,
                $announcement,
                ['announcement_id' => $announcement->id]
            );

            DB::commit();

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating announcement: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Failed to create announcement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified announcement
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view announcements.');
        }

        $announcement = Announcement::with(['creator', 'bannerImage', 'allowedUsers'])
            ->findOrFail($id);

        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to edit announcements.');
        }

        $announcement = Announcement::with('allowedUsers')->findOrFail($id);
        $users = User::where('privilege', '!=', 'admin')
            ->orderBy('privilege')
            ->orderByRaw("CASE WHEN privilege = 'user' THEN representative_type ELSE '' END")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $selectedUsers = $announcement->allowedUsers->pluck('id')->toArray();

        return view('admin.announcements.edit', compact('announcement', 'users', 'selectedUsers'));
    }

    /**
     * Update the specified announcement
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit announcements')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to edit announcements.');
        }

        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'allowed_users' => 'required|array|min:1',
            'allowed_users.*' => 'exists:users,id',
            'status' => 'nullable|in:draft,published',
            'scheduled_at' => 'nullable|date',
        ], [
            'title.required' => 'The title field is required.',
            'description.required' => 'The description field is required.',
            'allowed_users.required' => 'Please select at least one allowed user.',
            'allowed_users.min' => 'Please select at least one allowed user.',
        ]);

        DB::beginTransaction();
        try {
            $bannerImageId = $announcement->banner_image_id;
            
            // Handle banner image upload if new image provided
            if ($request->hasFile('banner_image')) {
                // Delete old banner image if exists
                if ($bannerImageId) {
                    $oldMedia = MediaLibrary::find($bannerImageId);
                    if ($oldMedia && Storage::disk('public')->exists($oldMedia->file_path)) {
                        Storage::disk('public')->delete($oldMedia->file_path);
                    }
                    $oldMedia?->delete();
                }
                
                $file = $request->file('banner_image');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'announcements/' . $fileName;
                
                // Store file
                Storage::disk('public')->put($filePath, file_get_contents($file));
                
                // Create media library entry
                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);
                
                $bannerImageId = $media->id;
            }

            $oldStatus = $announcement->status;
            $oldScheduledAt = $announcement->scheduled_at;
            
            // Determine status: if scheduled_at is in the future, set to draft
            $scheduledAt = isset($validated['scheduled_at']) && $validated['scheduled_at'] 
                ? new \DateTime($validated['scheduled_at']) 
                : ($announcement->scheduled_at ? new \DateTime($announcement->scheduled_at) : null);
            $requestedStatus = $validated['status'] ?? $announcement->status;
            
            // If scheduled_at is in the future, force status to draft (will be auto-published by scheduler)
            if ($scheduledAt && $scheduledAt > now()) {
                $finalStatus = 'draft';
            } else {
                $finalStatus = $requestedStatus;
            }

            $announcement->update([
                'title' => $validated['title'],
                'content' => $validated['description'], // Map description to content
                'banner_image_id' => $bannerImageId,
                'status' => $finalStatus,
                'scheduled_at' => $validated['scheduled_at'] ?? $announcement->scheduled_at,
            ]);

            // Get previous allowed users
            $previousAllowedUsers = $announcement->allowedUsers->pluck('id')->toArray();

            // Sync allowed users
            $announcement->allowedUsers()->sync($validated['allowed_users']);
            
            // Get newly added users
            $newlyAddedUsers = array_diff($validated['allowed_users'], $previousAllowedUsers);
            
            // Send notifications and emails to newly added users if published
            if ($announcement->status === 'published') {
                $shouldNotifyNow = $announcement->scheduled_at === null || $announcement->scheduled_at <= now();
                
                if ($shouldNotifyNow && !empty($newlyAddedUsers)) {
                    // Reload announcement with allowed users
                    $announcement->load('allowedUsers');
                    
                    foreach ($announcement->allowedUsers->whereIn('id', $newlyAddedUsers) as $user) {
                        // Check if notification already exists to avoid duplicates
                        $existingNotification = Notification::where('user_id', $user->id)
                            ->where('type', 'announcement')
                            ->where('data->announcement_id', $announcement->id)
                            ->first();
                        
                        if (!$existingNotification) {
                            Notification::create([
                                'user_id' => $user->id,
                                'type' => 'announcement',
                                'title' => 'New Announcement',
                                'message' => 'A new announcement "' . $announcement->title . '" has been published.',
                                'url' => route('announcements.show', $announcement->id),
                                'data' => [
                                    'announcement_id' => $announcement->id,
                                    'announcement_title' => $announcement->title,
                                ],
                            ]);
                        }
                        
                        // Send email to newly added user
                        try {
                            Mail::to($user->email)->send(new AnnouncementEmail($announcement, $user));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send announcement email to user ' . $user->id . ': ' . $e->getMessage());
                        }
                    }
                }
    }

            // If status changed from draft to published, notify all allowed users
            if ($oldStatus === 'draft' && $announcement->status === 'published') {
                $shouldNotifyNow = $announcement->scheduled_at === null || $announcement->scheduled_at <= now();
                
                if ($shouldNotifyNow) {
                    // Reload announcement with allowed users
                    $announcement->load('allowedUsers');
                    
                    foreach ($announcement->allowedUsers as $user) {
                        // Check if notification already exists to avoid duplicates
                        $existingNotification = Notification::where('user_id', $user->id)
                            ->where('type', 'announcement')
                            ->where('data->announcement_id', $announcement->id)
                            ->first();
                        
                        if (!$existingNotification) {
                            Notification::create([
                                'user_id' => $user->id,
                                'type' => 'announcement',
                                'title' => 'New Announcement',
                                'message' => 'A new announcement "' . $announcement->title . '" has been published.',
                                'url' => route('announcements.show', $announcement->id),
                                'data' => [
                                    'announcement_id' => $announcement->id,
                                    'announcement_title' => $announcement->title,
                                ],
                            ]);
                        }
                        
                        // Send email to user
                        try {
                            Mail::to($user->email)->send(new AnnouncementEmail($announcement, $user));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send announcement email to user ' . $user->id . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            AuditLogger::log(
                'announcement.updated',
                'Updated announcement: ' . $announcement->title,
                $announcement,
                ['announcement_id' => $announcement->id]
            );

            DB::commit();

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating announcement: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified announcement from storage
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete announcements')) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete announcements.'
                ], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete announcements.');
        }

        $announcement = Announcement::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete banner image if exists
            if ($announcement->banner_image_id) {
                $media = MediaLibrary::find($announcement->banner_image_id);
                if ($media && Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media?->delete();
            }

            AuditLogger::log(
                'announcement.deleted',
                'Deleted announcement: ' . $announcement->title,
                $announcement,
                ['announcement_id' => $announcement->id]
            );

            $announcement->delete();

            DB::commit();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement deleted successfully.'
                ]);
            }

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting announcement: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete announcement: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete announcement: ' . $e->getMessage());
        }
    }
}
