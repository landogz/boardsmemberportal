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
        // Create 1 Admin User
        User::create([
            'id' => Str::uuid(),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'privilege' => 'admin',
            'is_active' => true,
            'mobile' => '1234567890',
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Create 4 Regular Users
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'mobile' => '1234567891',
                'company' => 'ABC Corporation',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'mobile' => '1234567892',
                'company' => 'XYZ Industries',
                'representative_name' => 'John Representative',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'mobile' => '1234567893',
                'company' => 'Tech Solutions Inc',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@example.com',
                'mobile' => '1234567894',
                'company' => 'Global Enterprises',
                'representative_name' => 'Mike Representative',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'id' => Str::uuid(),
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password_hash' => Hash::make('password'),
                'privilege' => 'user',
                'is_active' => true,
                'mobile' => $userData['mobile'],
                'company' => $userData['company'] ?? null,
                'representative_name' => $userData['representative_name'] ?? null,
                'status' => 'approved',
                'email_verified_at' => now(),
            ]);
        }
    }
}

