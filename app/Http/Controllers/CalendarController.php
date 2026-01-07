<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use App\Models\BoardRegulation;
use App\Models\Announcement;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    /**
     * Get calendar events (resolutions, regulations, announcements)
     */
    public function getEvents(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['events' => []]);
        }

        $user = Auth::user();
        $events = [];

        // Get Board Resolutions (Official Documents) - Display all resolutions
        $resolutions = OfficialDocument::with(['pdf', 'uploader'])->get();

        foreach ($resolutions as $resolution) {
            // Use effective_date if available, otherwise use approved_date, otherwise use created_at
            $eventDate = $resolution->effective_date 
                ?? $resolution->approved_date 
                ?? $resolution->created_at;
            
            // Skip if no date is available
            if (!$eventDate) {
                continue;
            }
            
            $year = $eventDate ? $eventDate->format('Y') : null;
            $url = route('board-issuances', [
                'type' => 'resolution',
                'year' => $year,
                'keyword' => urlencode($resolution->title),
            ]) . '#resolution-' . $resolution->id;
            
            // Get PDF URL if available
            $pdfUrl = null;
            if ($resolution->pdf && $resolution->pdf->file_path) {
                $pdfUrl = asset('storage/' . $resolution->pdf->file_path);
            }
            
            $events[] = [
                'id' => 'resolution_' . $resolution->id,
                'title' => $resolution->title,
                'start' => $eventDate->format('Y-m-d'),
                'backgroundColor' => '#CE2028',
                'borderColor' => '#CE2028',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'resolution',
                    'description' => $resolution->description ?? 'Board Resolution',
                    'id' => $resolution->id,
                    'year' => $year,
                    'effective_date' => $resolution->effective_date ? $resolution->effective_date->format('F d, Y') : null,
                    'approved_date' => $resolution->approved_date ? $resolution->approved_date->format('F d, Y') : null,
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ],
            ];
        }

        // Get Board Regulations - Display all regulations
        $regulations = BoardRegulation::with(['pdf', 'uploader'])->get();

        foreach ($regulations as $regulation) {
            // Use effective_date if available, otherwise use approved_date, otherwise use created_at
            $eventDate = $regulation->effective_date 
                ?? $regulation->approved_date 
                ?? $regulation->created_at;
            
            // Skip if no date is available
            if (!$eventDate) {
                continue;
            }
            
            $year = $eventDate ? $eventDate->format('Y') : null;
            $url = route('board-issuances', [
                'type' => 'regulation',
                'year' => $year,
                'keyword' => urlencode($regulation->title),
            ]) . '#regulation-' . $regulation->id;
            
            // Get PDF URL if available
            $pdfUrl = null;
            if ($regulation->pdf && $regulation->pdf->file_path) {
                $pdfUrl = asset('storage/' . $regulation->pdf->file_path);
            }
            
            $events[] = [
                'id' => 'regulation_' . $regulation->id,
                'title' => $regulation->title,
                'start' => $eventDate->format('Y-m-d'),
                'backgroundColor' => '#055498',
                'borderColor' => '#055498',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'regulation',
                    'description' => $regulation->description ?? 'Board Regulation',
                    'id' => $regulation->id,
                    'year' => $year,
                    'effective_date' => $regulation->effective_date ? $regulation->effective_date->format('F d, Y') : null,
                    'approved_date' => $regulation->approved_date ? $regulation->approved_date->format('F d, Y') : null,
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ],
            ];
        }

        // Get Announcements (only if user has access)
        if ($user->hasRole('admin')) {
            $announcements = Announcement::published()
                ->with(['creator', 'bannerImage'])
                ->get();
        } else {
            $announcements = Announcement::published()
                ->whereHas('allowedUsers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['creator', 'bannerImage'])
                ->get();
        }

        foreach ($announcements as $announcement) {
            // Use scheduled_at if available, otherwise use created_at
            $eventDate = $announcement->scheduled_at 
                ? $announcement->scheduled_at->format('Y-m-d')
                : $announcement->created_at->format('Y-m-d');

            $events[] = [
                'id' => 'announcement_' . $announcement->id,
                'title' => $announcement->title,
                'start' => $eventDate,
                'backgroundColor' => '#FBD116',
                'borderColor' => '#FBD116',
                'textColor' => '#123a60',
                'extendedProps' => [
                    'type' => 'announcement',
                    'description' => Str::limit(strip_tags($announcement->description), 150),
                    'id' => $announcement->id,
                    'url' => route('announcements.show', $announcement->id),
                ],
            ];
        }

        // Get Notices
        // For admin/consec: show all notices
        // For regular users: only show notices where they are invited/allowed
        if ($user->privilege === 'admin' || $user->privilege === 'consec') {
            $notices = Notice::with(['creator', 'allowedUsers'])->get();
        } else {
            $notices = Notice::whereHas('allowedUsers', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['creator', 'allowedUsers'])
            ->get();
        }

        foreach ($notices as $notice) {
            // Use meeting_date if available, otherwise use created_at
            // Parse meeting_date if it's a string
            if ($notice->meeting_date) {
                $eventDate = is_string($notice->meeting_date) 
                    ? \Carbon\Carbon::parse($notice->meeting_date)
                    : $notice->meeting_date;
            } else {
                $eventDate = $notice->created_at;
            }
            
            $eventDateFormatted = $eventDate->format('Y-m-d');

            // Determine URL based on user privilege
            $url = ($user->privilege === 'admin' || $user->privilege === 'consec') 
                ? route('admin.notices.show', $notice->id)
                : route('notices.show', $notice->id);

            $events[] = [
                'id' => 'notice_' . $notice->id,
                'title' => $notice->title,
                'start' => $eventDateFormatted,
                'backgroundColor' => '#7C3AED',
                'borderColor' => '#7C3AED',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'notice',
                    'description' => Str::limit(strip_tags($notice->description ?? ''), 150),
                    'id' => $notice->id,
                    'notice_type' => $notice->notice_type,
                    'meeting_type' => $notice->meeting_type,
                    'meeting_date' => $notice->meeting_date ? (is_string($notice->meeting_date) ? \Carbon\Carbon::parse($notice->meeting_date)->format('F d, Y') : $notice->meeting_date->format('F d, Y')) : null,
                    'meeting_time' => $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') : null,
                    'meeting_link' => $notice->meeting_link,
                    'url' => $url,
                ],
            ];
        }

        return response()->json(['events' => $events]);
    }
}

