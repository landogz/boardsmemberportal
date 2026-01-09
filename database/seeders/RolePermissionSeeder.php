<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'manage roles',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission Management
            'manage permissions',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Board Resolutions
            'manage board resolutions',
            'view board resolutions',
            'create board resolutions',
            'edit board resolutions',
            'delete board resolutions',
            
            // Board Regulations
            'manage board regulations',
            'view board regulations',
            'create board regulations',
            'edit board regulations',
            'delete board regulations',
            
            // Referendums
            'manage referendum',
            'view referendum',
            'create referendum',
            'edit referendum',
            'delete referendum',
            
            // Government Agencies
            'manage government agencies',
            'view government agencies',
            'create government agencies',
            'edit government agencies',
            'delete government agencies',
            
            // Media Library
            'manage media library',
            'view media library',
            'upload media',
            'edit media',
            'delete media',
            
            // Announcements
            'manage announcements',
            'view announcements',
            'create announcements',
            'edit announcements',
            'delete announcements',
            
            // Notices
            'manage notices',
            'view notices',
            'create notices',
            'edit notices',
            'delete notices',
            
            // Calendar Events
            'manage calendar events',
            'view calendar events',
            'create calendar events',
            'edit calendar events',
            'delete calendar events',
            
            // Audit Logs
            'view audit logs',
            
            // CONSEC Account Management
            'manage consec accounts',
            'view consec accounts',
            'create consec accounts',
            'edit consec accounts',
            'delete consec accounts',
            
            // Board Member Management
            'manage board members',
            'view board members',
            'create board members',
            'edit board members',
            'delete board members',
            
            // Pending Registrations
            'manage pending registrations',
            'view pending registrations',
            'approve pending registrations',
            'disapprove pending registrations',
            
            // Attendance Confirmation
            'manage attendance confirmation',
            'view attendance confirmation',
            'create attendance confirmation',
            'edit attendance confirmation',
            'delete attendance confirmation',
            
            // Reference Materials
            'manage reference materials',
            'view reference materials',
            'create reference materials',
            'edit reference materials',
            'delete reference materials',
            
            // Request for Inclusion in the Agenda
            'manage agenda requests',
            'view agenda requests',
            'create agenda requests',
            'edit agenda requests',
            'delete agenda requests',
            
            // Report Generation
            'manage reports',
            'view reports',
            'generate reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $consecRole = Role::firstOrCreate(['name' => 'consec', 'guard_name' => 'web']);

        // Assign all permissions to admin role
        $adminRole->syncPermissions(Permission::all());

        // Assign all permissions to CONSEC role (same as admin)
        $consecRole->syncPermissions(Permission::all());

        // Assign basic permissions to user role
        $userRole->syncPermissions([
            'view board resolutions',
            'view board regulations',
            'view referendum',
            'view announcements',
            'view notices',
            'view calendar events',
            'upload media',
            'delete media',
        ]);

        // Assign admin role to existing admin users
        $adminUsers = User::where('privilege', 'admin')->get();
        foreach ($adminUsers as $user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }

        // Assign CONSEC role to existing CONSEC users
        $consecUsers = User::where('privilege', 'consec')->get();
        foreach ($consecUsers as $user) {
            if (!$user->hasRole('consec')) {
                $user->assignRole('consec');
            }
        }

        // Assign user role to non-admin, non-consec users
        $regularUsers = User::where(function($query) {
            $query->where('privilege', '!=', 'admin')
                  ->where('privilege', '!=', 'consec')
                  ->orWhereNull('privilege');
        })->get();
        foreach ($regularUsers as $user) {
            if (!$user->hasAnyRole(['admin', 'consec', 'user'])) {
                $user->assignRole('user');
            }
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}

