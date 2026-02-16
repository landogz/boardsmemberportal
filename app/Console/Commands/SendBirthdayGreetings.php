<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\BirthdayGreetingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdays:send-greetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday greeting emails to consec and user (board member) accounts whose birth date is today';

    /**
     * Cache key prefix for "already sent today" (avoids duplicate sends if command runs twice).
     */
    private function sentCacheKey(int $userId): string
    {
        return 'birthday_greeting_sent_' . now()->toDateString() . '_' . $userId;
    }

    /**
     * Execute the console command.
     * Automatically detects users (consec + user) with birth_date = today and sends one email per user per day.
     */
    public function handle(): int
    {
        $today = now();

        $users = User::query()
            ->whereIn('privilege', ['consec', 'user'])
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->whereNotNull('email')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $key = $this->sentCacheKey($user->id);
            if (Cache::get($key)) {
                $this->line("Already sent today, skipping: {$user->email}");
                continue;
            }

            try {
                Mail::to($user->email)->send(new BirthdayGreetingEmail($user));
                Cache::put($key, true, now()->endOfDay());
                $sent++;
                $this->info("Sent birthday email to: {$user->email} ({$user->first_name} {$user->last_name})");
            } catch (\Exception $e) {
                $this->error("Failed to send birthday email to {$user->email}: " . $e->getMessage());
                \Log::error('Birthday greeting send failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} birthday greeting(s).");
        } else {
            $this->info('No birthday greetings to send today.');
        }

        return Command::SUCCESS;
    }
}
