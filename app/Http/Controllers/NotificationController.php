<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Events\NotificationUnreadCountUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Get recent notifications
     */
    public function getRecent(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'url' => $notification->url,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_at_full' => $notification->created_at->format('M d, Y h:i A'),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        // Broadcast unread count update
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        broadcast(new NotificationUnreadCountUpdated(Auth::id(), $count));

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        // Broadcast unread count update
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        broadcast(new NotificationUnreadCountUpdated(Auth::id(), $count));

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Broadcast unread count update (should be 0)
        broadcast(new NotificationUnreadCountUpdated(Auth::id(), 0));

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Display all notifications for public users
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read

        $query = Notification::where('user_id', Auth::id());

        // Apply filters
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications', compact('notifications', 'filter'));
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $wasUnread = !$notification->is_read;
        $notification->delete();

        // Broadcast unread count update if deleted notification was unread
        if ($wasUnread) {
            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
            broadcast(new NotificationUnreadCountUpdated(Auth::id(), $count));
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Display all notifications for admin panel
     */
    public function adminIndex(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read

        $query = Notification::where('user_id', Auth::id());

        // Apply filters
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications', 'filter'));
    }
}
