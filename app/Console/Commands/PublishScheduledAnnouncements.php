<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Notification;
use App\Mail\AnnouncementEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PublishScheduledAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically publish scheduled announcements when their scheduled time arrives';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Find announcements that are scheduled to be published now or in the past
        // and are still in draft status
        $scheduledAnnouncements = Announcement::where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->with(['allowedUsers'])
            ->get();

        $publishedCount = 0;

        foreach ($scheduledAnnouncements as $announcement) {
            DB::beginTransaction();
            try {
                // Update status to published
                $announcement->status = 'published';
                $announcement->save();

                // Send notifications and emails to all allowed users
                foreach ($announcement->allowedUsers as $user) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'announcement',
                        'title' => 'New Announcement',
                        'message' => 'A new announcement "' . $announcement->title . '" has been published.',
                        'data' => [
                            'announcement_id' => $announcement->id,
                            'announcement_title' => $announcement->title,
                        ],
                        'url' => route('announcements.show', $announcement->id),
                        'is_read' => false,
                    ]);
                    
                    // Send email to user
                    try {
                        Mail::to($user->email)->send(new AnnouncementEmail($announcement, $user));
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to user {$user->id}: " . $e->getMessage());
                        \Log::error('Failed to send announcement email to user ' . $user->id . ': ' . $e->getMessage());
                    }
                }

                DB::commit();
                $publishedCount++;
                
                $this->info("Published announcement: {$announcement->title} (ID: {$announcement->id})");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to publish announcement ID {$announcement->id}: " . $e->getMessage());
            }
        }

        if ($publishedCount > 0) {
            $this->info("Successfully published {$publishedCount} announcement(s).");
        } else {
            $this->info("No scheduled announcements to publish at this time.");
        }

        return Command::SUCCESS;
    }
}

