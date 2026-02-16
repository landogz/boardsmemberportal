<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\BirthdayGreetingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSampleBirthdayEmail extends Command
{
    protected $signature = 'birthdays:send-sample {email=rolan.benavidez@gmail.com : Email address to send the sample to}';

    protected $description = 'Send a sample birthday greeting email to the given address';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email: {$email}");
            return Command::FAILURE;
        }

        $user = new User([
            'first_name' => 'Rolan',
            'last_name' => 'Benavidez',
            'email' => $email,
        ]);

        try {
            Mail::to($email)->send(new BirthdayGreetingEmail($user));
            $this->info("Sample birthday email sent to: {$email}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send: " . $e->getMessage());
            \Log::error('Sample birthday email failed', ['email' => $email, 'error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
