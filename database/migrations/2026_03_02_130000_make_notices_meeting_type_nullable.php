<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Allow meeting_type (and related meeting fields) to be null for Notice of Postponement.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE notices MODIFY COLUMN meeting_type ENUM('online', 'onsite', 'hybrid') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE notices MODIFY COLUMN meeting_type ENUM('online', 'onsite', 'hybrid') NOT NULL DEFAULT 'onsite'");
    }
};
