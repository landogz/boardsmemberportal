<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {first_name? : First name}
                            {last_name? : Last name}
                            {email? : Email (unique)}
                            {password? : Password}
                            {--username= : Optional username (default: derived from name)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user and assign the admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $firstName = $this->argument('first_name') ?? $this->ask('First name');
        $lastName  = $this->argument('last_name') ?? $this->ask('Last name');
        $email     = $this->argument('email') ?? $this->ask('Email');
        $password  = $this->argument('password') ?? $this->secret('Password');

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email [{$email}] already exists.");
            return self::FAILURE;
        }

        $username = $this->option('username');
        if ($username === null) {
            $username = User::usernameFromName($firstName, $lastName);
        }

        $user = User::create([
            'id'                => Str::uuid(),
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'email'             => $email,
            'username'          => $username,
            'password_hash'     => Hash::make($password),
            'privilege'         => 'admin',
            'is_active'         => true,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        $this->info('Admin user created successfully.');
        $this->table(
            ['ID', 'Name', 'Email', 'Username'],
            [[$user->id, "{$user->first_name} {$user->last_name}", $user->email, $user->username]]
        );

        return self::SUCCESS;
    }
}
