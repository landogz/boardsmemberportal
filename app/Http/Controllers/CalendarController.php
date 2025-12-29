<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use App\Models\BoardRegulation;
use App\Models\Announcement;
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

        // Get Board Resolutions (Official Documents)
        $resolutions = OfficialDocument::with(['pdf', 'uploader'])
            ->whereNotNull('effective_date')
            ->get();

        foreach ($resolutions as $resolution) {
            $year = $resolution->effective_date ? $resolution->effective_date->format('Y') : null;
            $url = route('board-issuances', [
                'type' => 'resolution',
                'year' => $year,
                'keyword' => urlencode($resolution->title),
            ]) . '#resolution-' . $resolution->id;
            
            $events[] = [
                'id' => 'resolution_' . $resolution->id,
                'title' => $resolution->title,
                'start' => $resolution->effective_date->format('Y-m-d'),
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
                ],
            ];
        }

        // Get Board Regulations
        $regulations = BoardRegulation::with(['pdf', 'uploader'])
            ->whereNotNull('effective_date')
            ->get();

        foreach ($regulations as $regulation) {
            $year = $regulation->effective_date ? $regulation->effective_date->format('Y') : null;
            $url = route('board-issuances', [
                'type' => 'regulation',
                'year' => $year,
                'keyword' => urlencode($regulation->title),
            ]) . '#regulation-' . $regulation->id;
            
            $events[] = [
                'id' => 'regulation_' . $regulation->id,
                'title' => $regulation->title,
                'start' => $regulation->effective_date->format('Y-m-d'),
                'backgroundColor' => '#CE2028',
                'borderColor' => '#CE2028',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'regulation',
                    'description' => $regulation->description ?? 'Board Regulation',
                    'id' => $regulation->id,
                    'year' => $year,
                    'effective_date' => $regulation->effective_date ? $regulation->effective_date->format('F d, Y') : null,
                    'approved_date' => $regulation->approved_date ? $regulation->approved_date->format('F d, Y') : null,
                    'url' => $url,
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

        return response()->json(['events' => $events]);
    }
}

