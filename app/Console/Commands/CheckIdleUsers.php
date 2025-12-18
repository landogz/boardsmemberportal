<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckIdleUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-idle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for idle users and log them out after 30 minutes of inactivity';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $idleMinutes = 30;
        $idleThreshold = now()->subMinutes($idleMinutes);

        // Find users who are online but haven't been active for 30 minutes
        $idleUsers = User::where('is_online', true)
            ->where('last_activity', '<', $idleThreshold)
            ->get();

        $loggedOutCount = 0;

        foreach ($idleUsers as $user) {
            // Store session ID before clearing
            $sessionId = $user->current_session_id;
            
            // Set user as offline
            $user->is_online = false;
            $user->current_session_id = null;
            $user->save();

            // Destroy all sessions for this user
            $sessionsDestroyed = $this->destroyUserSessions($user);

            // Log the auto-logout
            AuditLogger::log(
                'auth.auto_logout',
                'User automatically logged out due to inactivity (30 minutes)',
                $user,
                [
                    'last_activity' => $user->last_activity ? $user->last_activity->toDateTimeString() : null,
                    'idle_minutes' => $idleMinutes,
                    'session_id' => $sessionId,
                    'sessions_destroyed' => $sessionsDestroyed,
                    'triggered_by' => 'system_scheduler',
                ]
            );

            $loggedOutCount++;
            
            $this->info("Logged out idle user: {$user->email} (Last activity: {$user->last_activity})");
        }

        if ($loggedOutCount > 0) {
            $this->info("Successfully logged out {$loggedOutCount} idle user(s).");
        } else {
            $this->info("No idle users found.");
        }

        return Command::SUCCESS;
    }

    /**
     * Destroy all sessions for a user
     */
    private function destroyUserSessions($user): int
    {
        // Delete all sessions for this user from the sessions table
        return \DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
    }
}
