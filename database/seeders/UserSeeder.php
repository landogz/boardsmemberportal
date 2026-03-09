<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        User::create([
            'id' => Str::uuid(),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'password_hash' => Hash::make('password'),
            'privilege' => 'admin',
            'is_active' => true,
            'mobile' => '1234567890',
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => Str::uuid(),
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'misadministrator@yopmail.com',
            'password_hash' => Hash::make('password'),
            'privilege' => 'admin',
            'is_active' => true,
            'mobile' => null,
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);
    }
}

