<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Models\User;
use App\Models\Notification;
use App\Mail\UnreadMessageReminderEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendUnreadMessageReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send-unread-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email and notification reminders for messages that have been unread for 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Find messages that are unread and older than 24 hours
        $twentyFourHoursAgo = now()->subHours(24);
        
        $remindersSent = 0;
        $remindersFailed = 0;

        // Handle individual messages
        $individualMessages = Chat::where('is_read', false)
            ->whereNull('group_id')
            ->whereNotNull('receiver_id')
            ->where('timestamp', '<=', $twentyFourHoursAgo)
            ->with(['sender', 'receiver'])
            ->get();

        foreach ($individualMessages as $message) {
            if (!$message->sender || !$message->receiver) {
                continue;
            }

            // Check if reminder already sent in last 24 hours
            $existingNotification = Notification::where('user_id', $message->receiver_id)
                ->where('type', 'unread_message_reminder')
                ->where('data->message_id', $message->id)
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if ($existingNotification) {
                continue;
            }

            try {
                // Determine messages URL based on user privilege
                $messagesUrl = ($message->receiver->privilege === 'admin' || $message->receiver->privilege === 'consec') 
                    ? url('/admin/messages') 
                    : url('/messages');

                // Create in-app notification
                Notification::create([
                    'user_id' => $message->receiver_id,
                    'type' => 'unread_message_reminder',
                    'title' => 'Unread Message Reminder',
                    'message' => 'You have an unread message from ' . $message->sender->first_name . ' ' . $message->sender->last_name . ' that has been waiting for over 24 hours.',
                    'url' => $messagesUrl,
                    'data' => [
                        'message_id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $message->sender->first_name . ' ' . $message->sender->last_name,
                    ],
                    'is_read' => false,
                ]);

                // Send email reminder
                Mail::to($message->receiver->email)->send(
                    new UnreadMessageReminderEmail($message->receiver, $message, $message->sender)
                );

                $remindersSent++;
                $this->info("Sent reminder for individual message ID {$message->id} to user {$message->receiver->email}");
            } catch (\Exception $e) {
                $remindersFailed++;
                $this->error("Failed to send reminder for message ID {$message->id}: " . $e->getMessage());
                \Log::error('Failed to send unread message reminder for message ' . $message->id . ': ' . $e->getMessage());
            }
        }

        // Handle group messages
        $groupMessages = Chat::where('is_read', false)
            ->whereNotNull('group_id')
            ->where('timestamp', '<=', $twentyFourHoursAgo)
            ->with(['sender', 'group'])
            ->get();

        foreach ($groupMessages as $message) {
            if (!$message->sender || !$message->group) {
                continue;
            }

            // Get all group members except the sender
            $groupMembers = \App\Models\GroupMember::where('group_id', $message->group_id)
                ->where('user_id', '!=', $message->sender_id)
                ->with('user')
                ->get();

            foreach ($groupMembers as $member) {
                if (!$member->user) {
                    continue;
                }

                // Check if reminder already sent in last 24 hours
                $existingNotification = Notification::where('user_id', $member->user_id)
                    ->where('type', 'unread_message_reminder')
                    ->where('data->message_id', $message->id)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->first();

                if ($existingNotification) {
                    continue;
                }

                try {
                    // Determine messages URL based on user privilege
                    $messagesUrl = ($member->user->privilege === 'admin' || $member->user->privilege === 'consec') 
                        ? url('/admin/messages') 
                        : url('/messages');

                    // Create in-app notification
                    Notification::create([
                        'user_id' => $member->user_id,
                        'type' => 'unread_message_reminder',
                        'title' => 'Unread Group Message Reminder',
                        'message' => 'You have an unread message in "' . $message->group->name . '" from ' . $message->sender->first_name . ' ' . $message->sender->last_name . ' that has been waiting for over 24 hours.',
                        'url' => $messagesUrl,
                        'data' => [
                            'message_id' => $message->id,
                            'group_id' => $message->group_id,
                            'group_name' => $message->group->name,
                            'sender_id' => $message->sender_id,
                            'sender_name' => $message->sender->first_name . ' ' . $message->sender->last_name,
                        ],
                        'is_read' => false,
                    ]);

                    // Send email reminder
                    Mail::to($member->user->email)->send(
                        new UnreadMessageReminderEmail($member->user, $message, $message->sender)
                    );

                    $remindersSent++;
                    $this->info("Sent reminder for group message ID {$message->id} to user {$member->user->email}");
                } catch (\Exception $e) {
                    $remindersFailed++;
                    $this->error("Failed to send reminder for group message ID {$message->id} to user {$member->user_id}: " . $e->getMessage());
                    \Log::error('Failed to send unread group message reminder for message ' . $message->id . ' to user ' . $member->user_id . ': ' . $e->getMessage());
                }
            }
        }

        if ($remindersSent > 0) {
            $this->info("Successfully sent {$remindersSent} reminder(s).");
        } else {
            $this->info("No unread messages found that require reminders.");
        }

        if ($remindersFailed > 0) {
            $this->warn("Failed to send {$remindersFailed} reminder(s). Check logs for details.");
        }

        return Command::SUCCESS;
    }
}

