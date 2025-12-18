<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the privilege enum to include 'consec'
        DB::statement("ALTER TABLE users MODIFY COLUMN privilege ENUM('admin', 'user', 'consec') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First, update any 'consec' users to 'user'
        DB::table('users')->where('privilege', 'consec')->update(['privilege' => 'user']);
        
        // Then modify the enum back
        DB::statement("ALTER TABLE users MODIFY COLUMN privilege ENUM('admin', 'user') DEFAULT 'user'");
    }
};
