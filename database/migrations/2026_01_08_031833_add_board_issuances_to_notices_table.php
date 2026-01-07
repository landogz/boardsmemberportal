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
        // Modify notice_type enum to include 'Board Issuances'
        DB::statement("ALTER TABLE notices MODIFY COLUMN notice_type ENUM('Notice of Meeting', 'Agenda', 'Other Matters', 'Board Issuances') DEFAULT 'Notice of Meeting'");
        
        // Add fields for Board Regulations and Board Resolutions selection
        Schema::table('notices', function (Blueprint $table) {
            $table->json('board_regulations')->nullable()->after('no_of_attendees');
            $table->json('board_resolutions')->nullable()->after('board_regulations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the new fields
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['board_regulations', 'board_resolutions']);
        });
        
        // Revert notice_type enum
        DB::statement("ALTER TABLE notices MODIFY COLUMN notice_type ENUM('Notice of Meeting', 'Agenda', 'Other Matters') DEFAULT 'Notice of Meeting'");
    }
};
